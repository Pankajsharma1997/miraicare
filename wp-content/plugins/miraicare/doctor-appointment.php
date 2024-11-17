<?php
// Exist when Accessed Directly
if (!defined('ABSPATH')){
    exit;
}

// Add the Custom CSS to the admin panel and User interfacce 
function miraicare_enqueue_scripts() {
    // Use the correct path without './'
    wp_enqueue_style('mirai-styles', plugin_dir_url(__FILE__) . '/css/style.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('das-ajax-script', plugins_url('/js/das-ajax.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('das-ajax-script', 'das_ajax_obj', array('ajax_url' => admin_url('admin-ajax.php')));
  
  
  }
  
  // Enqueue scripts and styles for both admin and frontend
  add_action('wp_enqueue_scripts', 'miraicare_enqueue_scripts');
  add_action('admin_enqueue_scripts', 'miraicare_enqueue_scripts'); // This line ensures it runs in admin too

  function appointment_booking_form() {
    global $wpdb;

    // Fetch doctor details from query parameters
    $doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : null;
    $doctor_info = $wpdb->get_row($wpdb->prepare("SELECT d_name, d_specialty, d_image, d_email,d_identity_number FROM {$wpdb->prefix}doctors WHERE d_id = %d", $doctor_id));
 
    
  
    ob_start();
    ?>
    <form method="post">
    <?php if ($doctor_info): ?>
            <img src="<?php echo esc_url($doctor_info->d_image);?>" alt="<?php echo esc_attr($doctor_info->d_name); ?>" style="width: 50%; height: auto;" />
            <table> 
                <thead> 
                <th>  <h3> Doctor  Details  </h3> </th>
                </thead>
                <tbody> 
                    <tr> 
                         <td> <h3> Doctor Name: </h3> </td> 
                         <td>  <strong> Dr. <?php echo esc_html($doctor_info->d_name); ?> </strong> </td>
                         <td> <h3> Specialty:  </h3>          
                         <td> <strong> <?php echo esc_html($doctor_info->d_specialty); ?></strong> </td>     
                    </tr>
                </tbody>                
            </table>
            
            <input type="hidden" name="doctor_id" value="<?php echo esc_attr($doctor_id); ?>">
            <input type="hidden" name="doctor_name" value="<?php echo esc_attr($doctor_info->d_name); ?>">
            <input type="hidden" name="doctor_email" value="<?php echo esc_attr($doctor_info->d_email);?>">
            <input type="hidden" name="doctor_specialty" value="<?php echo esc_attr($doctor_info->d_specialty); ?>">
            <input type="hidden" name="doctor_identity_number" value="<?php echo esc_attr($doctor_info->d_identity_number); ?>">
       
    <?php endif; ?>

   
        <label for="patient_name">Patient Name:</label>
        <input type="text" id="patient_name" name="patient_name" required>
        

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="mobile">Mobile:</label>
        <input type="text" id="mobile" name="mobile" required>
        
        
        <label for="appointment_date">Choose Date:</label>
    <input type="date" id="appointment_date" name="appointment_date" required>

    <label for="appointment_day">Choose Day:</label>
<select id="appointment_day" name="appointment_day" required>
    <option value="">Select a day</option>
    <option value="Sunday">Sunday</option>
    <option value="Monday">Monday</option>
    <option value="Tuesday">Tuesday</option>
    <option value="Wednesday">Wednesday</option>
    <option value="Thursday">Thursday</option>
    <option value="Friday">Friday</option>
    <option value="Saturday">Saturday</option>
</select>

    <label for="appointment_time">Select Time:</label>
        <select name="appointment_time" >
            <option value="">Select a time slot</option>
            <!-- Options will be populated by AJAX -->
        </select>
        
    
        <input type="submit" name="submit_appointment" class="appointment" value="Book Appointment">
    </form>
    
    
    
    <?php

 // Handle form submission 
if (isset($_POST['submit_appointment'])) {
    
    // Sanitize input fields
    $patient_name        = sanitize_text_field($_POST['patient_name']);
    $email               = sanitize_email($_POST['email']);
    $mobile              = sanitize_text_field($_POST['mobile']);
    $doctor_id           = intval($_POST['doctor_id']);
    $doctor_name         = sanitize_text_field($_POST['doctor_name']);
    $doctor_email        = sanitize_text_field($_POST['doctor_email']);
    $doctor_specialty    = sanitize_text_field($_POST['doctor_specialty']);
    $doctor_identity_number    = sanitize_text_field($_POST['doctor_identity_number']);
    $appointment_date    = sanitize_text_field($_POST['appointment_date']);
    $appointment_time    = sanitize_text_field($_POST['appointment_time']);
    $appointment_day = isset($_POST['appointment_day']) ? sanitize_text_field($_POST['appointment_day']) : '';
   
    // Generate registration ID
    $appointment_table = $wpdb->prefix . 'appointments';
    $patient_table = $wpdb->prefix . 'patients';          // Make sure this is defined
   
         // Check the last registration ID from the appointments table
        $last_registration_appointments = $wpdb->get_var("SELECT registration_id FROM $appointment_table ORDER BY id DESC LIMIT 1");

        // Check the last registration ID from the patients table
        $last_registration_patients = $wpdb->get_var("SELECT registration_id FROM $patient_table ORDER BY id DESC LIMIT 1");

        // Determine the highest registration ID
        $last_registration = max($last_registration_appointments, $last_registration_patients);

        // If no registrations exist in both tables, set a default
          if (!$last_registration) {
               $new_registration_id = 131601; // Default starting ID
                } else {
               $new_registration_id = intval($last_registration) + 1; // Increment the highest found registration ID
                } 
    
   

    // Format as a 6-digit string
    $registration_id = str_pad($new_registration_id, 6, '0', STR_PAD_LEFT);
   
    
    // Insert new appointment
    $appointment_inserted = $wpdb->insert(
        $appointment_table,
        [
            'registration_id' => $registration_id,
            'patient_name' => $patient_name,
            'patient_email' => $email,
            'patient_mobile' => $mobile,
            'doctor_id' => $doctor_id,
            'd_identity_number'=>$doctor_identity_number,
            'doctor_name' => $doctor_name,
            'doctor_email' => $doctor_email,
            'doctor_specialty' => $doctor_specialty,
            'appointment_date' => $appointment_date, 
            'appointment_time' => $appointment_time,
            'appointment_day'=> $appointment_day,
            'status' => 'Pending' // Default status
        ]
    );
   
   
    // Insert patient details into the patients table
    $patient_inserted = $wpdb->insert(
        $patient_table,
        [
            'registration_id' => $registration_id,
            'p_name' => $patient_name,
            'p_email' => $email,
            'p_mobile' => $mobile,
            'registered_date' => current_time('mysql')
        ]
    );
              
    // Check if both inserts were successful
    if ($appointment_inserted && $patient_inserted) {
        echo "<p>Appointment booked successfully! Your registration ID is: <strong>$registration_id</strong></p>";
        echo "<p>Appointment booked successfully! Your registration ID is: <strong>$appointment_day</strong></p>";
        echo "<p><strong>Note:</strong> Please note the Registration ID. It will help you check your status.</p>";
    } else {
        echo "<p>Error: " . $wpdb->last_error . "</p>";
    }
}

return ob_get_clean();
}
add_shortcode('appointment_booking', 'appointment_booking_form');

function das_get_booked_slots() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'appointments';
    $doctor_session_table = $wpdb->prefix.'doctors_sessions';

    // Get and sanitize the input data
    $date = sanitize_text_field($_POST['date']);
    $day = date('l', strtotime($date)); // Get the day of the week from the date input
    // Try to get doctor_id from the AJAX request
    $doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : null;

    // If not provided in the AJAX request, check the URL
    if (is_null($doctor_id)) {
        $doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : null;
    }

    // Check if doctor_id is provided
    if (is_null($doctor_id)) {
        wp_send_json_error('Doctor ID not provided.');
        return;
    }

    // Fetch booked slots for the selected date and specific doctor
    $booked_slots = $wpdb->get_col($wpdb->prepare(
        "SELECT appointment_time FROM $table_name WHERE DATE(appointment_date) = %s AND doctor_id = %d",
        $date,
        $doctor_id
    ));



      // Fetch booked slots for the selected date and specific doctor
      $Fetched_time_slots = $wpdb->get_col($wpdb->prepare(
        "SELECT session_time FROM $doctor_session_table WHERE session_day = %s AND doctor_id = %d",
        $day,
        $doctor_id
    ));
    
    // Initialize an array to hold the booked time slots
$time_slots = [];

// Iterate over the fetched time slots and explode them into arrays
foreach ($Fetched_time_slots as $slots) {
    $time_slots = array_merge($time_slots, explode(',', $slots));
}

    // Time slots array
    //$time_slots = ['10:00 AM', '10:30 AM', '11:00 AM', '11:30 AM', '12:00 PM', '12:30 PM', '1:00 PM', '2:00 PM', '2:30 PM', '3:00 PM', '3:30 PM', '4:00 PM', '4:30 PM', '5:00 PM'];

    // Calculate available slots
    $available_slots = array_diff($time_slots, $booked_slots);

    // Return available slots as a JSON array
    wp_send_json($available_slots);
}

add_action('wp_ajax_get_booked_slots', 'das_get_booked_slots');
add_action('wp_ajax_nopriv_get_booked_slots', 'das_get_booked_slots');