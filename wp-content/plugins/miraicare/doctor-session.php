<?php
if (!defined('ABSPATH')) {
    exit;
}

function enqueue_custom_scripts() {
    wp_enqueue_script('jquery');
    //wp_enqueue_script('custom-ajax-script', get_template_directory_uri() . '/js/custom-ajax.js', array('jquery'), null, true);
    wp_enqueue_script('custom-ajax-script', plugins_url('/js/custom-ajax.js', __FILE__), array('jquery'), null, true);

    // Localize script to use AJAX URL
    wp_localize_script('custom-ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
add_action('admin_enqueue_scripts', 'enqueue_custom_scripts'); // This line ensures it runs in admin too


// Function to set doctor time slots
function set_doctor_time_slots() {
    global $wpdb;

    // Check for form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctor_id'])) {
        $doctor_id = intval($_POST['doctor_id']);
        $session_days = isset($_POST['session_days']) ? $_POST['session_days'] : [];
        $session_times = isset($_POST['session_times']) ? $_POST['session_times'] : [];

        // Validate input
        if ($doctor_id && !empty($session_days) && !empty($session_times)) {
            // Prepare to insert sessions
            $doctors_session_table = $wpdb->prefix . 'doctors_sessions';
            $session_times_combined = implode(',', array_map('sanitize_text_field', $session_times));

            foreach ($session_days as $day) {
                // Insert a single entry for each day with the combined session times
                $wpdb->insert(
                    $doctors_session_table,
                    [
                        'doctor_id' => $doctor_id,
                        'session_day' => sanitize_text_field($day),
                        'session_time' => $session_times_combined,
                    ]
                );
            }

            // Feedback message
            echo '<div class="updated"><p>Sessions saved successfully!</p></div>';
        } else {
            echo '<div class="error"><p>Please select at least one day and one time slot.</p></div>';
        }
    }
}


// Main function to render the doctor session page
function doctor_session_page() {
    global $wpdb;

    // Set the doctor time slots
    set_doctor_time_slots();

    // Get doctor ID from the query parameter
    $doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;

    if ($doctor_id) {
        $doctor_table = $wpdb->prefix . 'doctors';
        $doctor = $wpdb->get_row($wpdb->prepare("SELECT * FROM $doctor_table WHERE d_id = %d", $doctor_id));

        if ($doctor):
            ?>
            <h2>Set Session for Dr. <?php echo esc_html($doctor->d_name); ?></h2>
            <form method="post" action="">
                <label for="session_days">Select Days:</label><br>
                <input type="checkbox" name="session_days[]" value="Monday"> Monday<br>
                <input type="checkbox" name="session_days[]" value="Tuesday"> Tuesday<br>
                <input type="checkbox" name="session_days[]" value="Wednesday"> Wednesday<br>
                <input type="checkbox" name="session_days[]" value="Thursday"> Thursday<br>
                <input type="checkbox" name="session_days[]" value="Friday"> Friday<br>
                <input type="checkbox" name="session_days[]" value="Saturday"> Saturday<br>
                <input type="checkbox" name="session_days[]" value="Sunday"> Sunday<br>

                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required><br>

                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required><br>

                <label for="session_times">Select Time Slots:</label><br>
                <select id="time_slots" name="session_times[]" multiple required>
                    <!-- Time slots will be populated here -->
                </select><br>

                <input type="hidden" name="doctor_id" value="<?php echo esc_attr($doctor->d_id); ?>">
                <input type="submit" value="Save Session" class="button button-primary">
            </form>

            <script>
                document.getElementById('start_time').addEventListener('change', generateTimeSlots);
                document.getElementById('end_time').addEventListener('change', generateTimeSlots);

                function generateTimeSlots() {
                    const startTime = document.getElementById('start_time').value;
                    const endTime = document.getElementById('end_time').value;

                    if (startTime && endTime) {
                        const start = new Date(`1970-01-01T${startTime}:00`);
                        const end = new Date(`1970-01-01T${endTime}:00`);
                        const timeSlotsSelect = document.getElementById('time_slots');
                        timeSlotsSelect.innerHTML = ''; // Clear existing options

                        // Generate 30-minute intervals
                        for (let time = start; time <= end; time.setMinutes(time.getMinutes() + 30)) {
                            const option = document.createElement('option');
                            let hours = time.getHours();
                            const minutes = time.getMinutes().toString().padStart(2, '0');
                            const period = hours >= 12 ? 'PM' : 'AM';
                            hours = hours % 12 || 12; // Convert to 12-hour format
                            option.value = `${hours}:${minutes}`;
                            option.text = `${hours}:${minutes} ${period}`;
                            timeSlotsSelect.add(option);
                        }
                    }
                }
            </script>
            <?php
            // Show the sessions for the doctor
            show_doctor_sessions($doctor_id);
        else:
            echo '<p>Doctor not found.</p>';
        endif;
    } else {
        echo '<p>No doctor selected.</p>';
    }
}



// Function to show doctor sessions in a table
function show_doctor_sessions($doctor_id) {
    global $wpdb;

    $doctor_table = $wpdb->prefix . 'doctors';
    $doctors_session_table = $wpdb->prefix . 'doctors_sessions';

    // Query to get the session details for the specific doctor
    $sessions = $wpdb->get_results($wpdb->prepare(
        "SELECT d.d_name, ds.id, ds.doctor_id, ds.session_day, ds.session_time 
         FROM $doctor_table AS d 
         JOIN $doctors_session_table AS ds 
         ON d.d_id = ds.doctor_id 
         WHERE ds.doctor_id = %d",
        $doctor_id
    ));

    // Check if sessions exist
    if ($sessions) {
        echo '<h2>Sessions for Doctor ID: ' . esc_html($doctor_id) . '</h2>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Doctor ID</th>';
        echo '<th>Doctor Name</th>';
        echo '<th>Session Day</th>';
        echo '<th>Session Time</th>';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // Loop through each session and create a row
        foreach ($sessions as $session) {
            echo '<tr>';
            echo '<td>' . esc_html($session->doctor_id) . '</td>';
            echo '<td>' . esc_html($session->d_name) . '</td>';
            echo '<td>' . esc_html($session->session_day) . '</td>';
            echo '<td>' . esc_html($session->session_time) . '</td>';
            echo '<td><button class="edit-button" data-id="' . esc_html($session->id) . '" data-day="' . esc_html($session->session_day) . '" data-time="' . esc_html($session->session_time) . '">Edit</button></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No sessions found for this doctor.</p>';
    }

// Ajax function for save the data 
function update_session() {
    global $wpdb;

    // Get the posted data
    $session_id = intval($_POST['session_id']);
    $session_day = sanitize_text_field($_POST['session_day']);
    $session_time = sanitize_text_field($_POST['session_time']);

    // Update the session in the database
    $updated = $wpdb->update(
        $wpdb->prefix . 'doctors_sessions',
        array(
            'session_day' => $session_day,
            'session_time' => $session_time
        ),
        array('id' => $session_id)
    );

    // Return a response
    if ($updated !== false) {
        wp_send_json_success('Session updated successfully!');
    } else {
        wp_send_json_error('Failed to update session.');
    }

    wp_die(); // Terminate the script
}
add_action('wp_ajax_update_session', 'update_session');
add_action('wp_ajax_nopriv_update_session', 'update_session');

}

// Call the main function to render the page
// doctor_session_page();