<?php 
 /**
        * Plugin Name: Miraicare
        * Plugin URI: https://example.com/miraicare-plugin 
        * Description: This Plugin is user for create a complete  Easy Healthcare clinic setup in wordpress .
        * Version: 1.0
        * Author: Miraidevelopers  
        * License: GPLv2 or later 
          */
         // Exit if accessed directly 
         if ( !defined('ABSPATH')) {
            exit;
                }

// Add the Custom CSS to the admin panel and User interfacce 
function mh_enqueue_scripts() {
    wp_enqueue_style('mirai-styles', plugin_dir_url(__FILE__) . '/css/style.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('das-ajax-script', plugins_url('/js/das-ajax.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('das-ajax-script', 'das_ajax_obj', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_localize_script('my-script', 'ajaxurl', admin_url('admin-ajax.php')); // Localize AJAX URL
  
  
  }
  
  // Enqueue scripts and styles for both admin and frontend
  add_action('wp_enqueue_scripts', 'mh_enqueue_scripts');
  add_action('admin_enqueue_scripts', 'mh_enqueue_scripts'); // This line ensures it runs in admin too


  

  function miraicare_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Create table structure for storing the information of the patients
    $patients_table = $wpdb->prefix . 'patients';
    $patients_sql = "CREATE TABLE IF NOT EXISTS $patients_table (
        p_id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username varchar(255),
        password varchar(200),
        p_name text NOT NULL,
        p_email varchar(255) NOT NULL
    ) $charset_collate;";

    // Create table structure for storing the information of the doctors
    $doctors_table = $wpdb->prefix . 'doctors';
    $doctors_sql = "CREATE TABLE IF NOT EXISTS $doctors_table (
        d_id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        d_name text NOT NULL,
        d_specialty varchar(255) NOT NULL,
        d_phone varchar(255),
        d_email varchar(255),
        d_image varchar(255)
    ) $charset_collate;";


    // Create table structure for storing the information of the doctors
    $doctors_session_table = $wpdb->prefix . 'doctors_sessions';
    $doctors_session_sql = "CREATE TABLE IF NOT EXISTS $doctors_session_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        doctor_id mediumint(9)  NULL,
        session_day varchar(255) NOT NULL,
        session_time time

    ) $charset_collate;";

    // Create table structure for storing the information of the appointments
    $appointments_table = $wpdb->prefix . 'appointments';
    $appointments_sql = "CREATE TABLE IF NOT EXISTS $appointments_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        registration_id mediumint(9) NOT NULL,
        patient_name varchar(200) NOT NULL,
        patient_email varchar(120) NOT NULL,
        doctor_id mediumint(9) NOT NULL,
        doctor_name varchar(120),
        doctor_specialty varchar(120),
        appointment_date date NOT NULL,
       appointment_time varchar(200),
        status varchar(20) DEFAULT 'pending',
        FOREIGN KEY (doctor_id) REFERENCES {$doctors_table}(d_id) ON DELETE CASCADE
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($patients_sql);
    dbDelta($doctors_sql);
    dbDelta($appointments_sql);
    dbDelta($doctors_session_sql);

    // Debugging: Check for errors
    if ($wpdb->last_error) {
        error_log($wpdb->last_error);
    }
}

register_activation_hook(__FILE__, 'miraicare_create_tables');

// Include the new files
require_once plugin_dir_path(__FILE__) . 'add-doctor.php';
require_once plugin_dir_path(__FILE__) . 'dashboard.php';
require_once plugin_dir_path(__FILE__) . 'doctor-appointment.php';
require_once plugin_dir_path(__FILE__) . 'doctor-session.php';


      
// Create a menu with Miraicare_health and sub menus
function mh_menu_pages() {
    // Add main menu page
    add_menu_page(
        'Miraicare',                           // Page Title 
        'Dashboard',                           // Menu Title 
        'manage_options',                      // Capability 
        'Dashboard',                           // Menu Slug 
        'miraicare_clinic_setup',              // Function to display the page 
        'dashicons-plus',                      // Icon
        6                                      // Position 
    );
  
    // Add submenu for Doctor Dashboard  
    add_submenu_page(
        'Dashboard',                                 // Parent Menu Slug 
        'Doctor-Dashboard',                          // Page Title
        'Doctor-Dashboard',                          // Menu Title
        'manage_options',                            // Capability
        'Doctor-Dashboard',                          // Menu Slug
        'doctor_dashboard'                           // Function to display the Page 
    );

    // Add Submenu for Patient Dashboard 
    add_submenu_page(
        'Dashboard',                         // Parent Menu Slug 
        'Patient-Dashboard',                        // Page Title 
        'Patient-Dashboard',                        // Menu Title
        'manage_options',                           // Capability
        'patient-dashboard',                        // Menu Slug 
        'patient_dashboard'                         // Function to display the Page 
    );
    // Add submenu for Appointments Page 
    add_submenu_page(
        'Dashboard',                         // Parent Menu Slug 
        'Appointments-Page',                       // Page Title 
        'Appointments-Page',                       // Menu Title
        'manage_options',                           // Capability
        'check_appointments',                       // Menu Slug 
        'miraicare_check_appointments'              // Function to display the Page 
    );
    add_submenu_page(
        'Dashboard',                         // Parent Menu Slug 
        'Doctor-session',                       // Page Title 
        'Doctor-session',                       // Menu Title
        'manage_options',                           // Capability
        'Doctor-session',                         // Menu Slug 
        'doctor_session_page'              // Function to display the Page 
    );
  }
  add_action('admin_menu', 'mh_menu_pages');
                  



                                                                                   // Admin Area  Start Here 

                                                                 // Doctor Dashboard 
function doctor_dashboard() {


    global $wpdb;
      // Query to get doctor details from database 
      $doctor_table = $wpdb-> prefix.'doctors';
      $doctors = $wpdb-> get_results("SELECT * FROM  $doctor_table");
      ob_start(); // Start output buffering
  
  
      if (isset($_POST['submit'])) {
          global $wpdb;
      // Sanitize and get form input values
      $doctor_name = sanitize_text_field($_POST['doctor_name']);
      $doctor_specialty = sanitize_text_field($_POST['doctor_specialty']);
      $doctor_email = sanitize_email($_POST['doctor_email']);
      $doctor_phone = sanitize_text_field($_POST['doctor_phone']);
      
      // Handle image upload
      $doctor_image_url = '';
      if (!empty($_FILES['doctor_image']['name'])) {
          $uploaded_file = $_FILES['doctor_image'];
          $upload_overrides = array('test_form' => false);
          $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
          if ($movefile && !isset($movefile['error'])) {
              $doctor_image_url = $movefile['url'];
          } else {
              echo '<div class="error"><p>Image upload error: ' . $movefile['error'] . '</p></div>';
          }
      }
  
       // Handle image upload
       $department_icon_url = '';
       if (!empty($_FILES['department_icon']['name'])) {
           $uploaded_file = $_FILES['department_icon'];
           $upload_overrides = array('test_form' => false);
           $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
           if ($movefile && !isset($movefile['error'])) {
               $department_icon_url = $movefile['url'];
           } else {
               echo '<div class="error"><p>Image upload error: ' . $movefile['error'] . '</p></div>';
           }
       }
   // Prepare data to insert
   $table_name = $wpdb->prefix . 'doctors'; 
  
   // Insert the doctor into the database
   $wpdb->insert($table_name, array(
       'd_name'          => $doctor_name,
       'd_specialty'     => $doctor_specialty,
       'd_email'         => $doctor_email,
       'd_phone'         => $doctor_phone,
       'd_image'         => $doctor_image_url,
       'department_icon' => $department_icon_url,
   ));
  
  echo'<div class="updated"> <p> Doctor Updated Sucessfully </p></div>';
   // Refresh the page to show the new entry
   echo "<meta http-equiv='refresh' content='0'>";
  }
    
      ?>
      <div class="wrap"> 
             <h1> Doctor Dashboard  </h1>
             <!-- Button to toggle the form -->
          <button id="toggleForm" style="margin-bottom: 20px;">Add Doctor</button>
             <div id="doctorForm" style="display: none;">
             <h1>Add Doctor</h1>
          <form method="POST" action="" enctype="multipart/form-data">
              <table class="form-table">
                  <tr>
                      <th><label for="doctor_name">Name</label></th>
                      <td><input type="text" name="doctor_name" id="doctor_name" required /></td>
                  </tr>
                  <tr>
                      <th><label for="doctor_specialty">Specialty</label></th>
                      <td><input type="text" name="doctor_specialty" id="doctor_specialty" required /></td>
                  </tr>
                  <tr>
                      <th><label for="doctor_email">Email</label></th>
                      <td><input type="email" name="doctor_email" id="doctor_email" required /></td>
                  </tr>
                  <tr>
                      <th><label for="doctor_phone">Phone</label></th>
                      <td><input type="text" name="doctor_phone" id="doctor_phone" required /></td>
                  </tr>
                  <tr>
                      <th><label for ="doctor_image">Doctor Image</label></th>
                      <td><input type="file" name="doctor_image" id="doctor_image" accept="image/*" /> </td>
                  </tr>
                  <tr> 
                      <th> <label for ="department_icon"> Department Icon  </label></th>
                      <td> <input type="file" name="department_icon" id="department_icon" accept="image/*"/> </td>
                  </tr>

              </table>
              <p>
                  <input type="submit" name="submit" class="submit" value="submit" />
              </p>
          </form>
          </div>

             <?php if ($doctors): ?> 
                 <table class ="widefat fixed"> 
                  <thead> 
                      <tr> 
                          <th> Doctor Name      </th>
                          <th> Doctor Specialty </th>
                          <th> Doctor Email     </th>
                          <th> Doctor Mobile    </th>
                          <th> Doctor Image     </th>
                          <th> Add Session      </th>
                         
                      </tr>
                  </thead>
                  <tbody> 
                    <?php foreach($doctors as $doctor):?>
                  <tr>
          
                      <td> <strong>Dr.</strong> <?php echo esc_html($doctor->d_name);?>    </td> 
                      <td>  <?php echo esc_html($doctor->d_specialty);?>                   </td> 
                      <td>  <?php echo esc_html($doctor->d_email);?>                       </td> 
                      <td>  <?php echo esc_html($doctor->d_phone);?>                       </td>
                      <td>  <img src ="<?php echo esc_url($doctor->d_image)?>" alt =" <?php $doctor->d_name?>" style= "width:50px; , height= auto;"/> </td> 
                     
                      <td>
    <button class= "appointment" onclick = "window.location.href ='<?php echo admin_url('admin.php?page=Doctor-session&doctor_id='  . $doctor->d_id); ?>';" >  Doctor Session </button>
</td>
                  </tr>
                  <?php endforeach; ?>
                  </tbody>
                 </table>
                 <?php endif;?>   
      </div>
     
      
      <script>
    // JavaScript to toggle the form visibility and button text
    document.getElementById('toggleForm').onclick = function() {
        var form = document.getElementById('doctorForm');
        var button = document.getElementById('toggleForm');
        
        if (form.style.display === "none") {
            form.style.display = "block";
            button.textContent = "Close"; // Change button text
        } else {
            form.style.display = "none";
            button.textContent = "Add Doctor"; // Revert button text
        }
    };
</script>
      <?php 
       ob_end_flush(); // Flush the output buffer
}

       
    
                                                                                          // Patient Dashboard  
function patient_dashboard(){
    global $wpdb;
    $patient_table = $wpdb->prefix. 'patients';
    $patients = $wpdb -> get_results("SELECT * FROM $patient_table");
    
    ob_start(); // Start output buffering

    // output table for patient data 
    if ($patients):?>
    <div class="patient-wrap"> 
       <h1> Patient Dashboard </h1>
    <table class="common-table">  
        <thead> 
            <tr> 
                <th> Registration id </th>
                <th> Patient Name    </th>
                <th> Patient Email   </th>
                <th> Patient Mobile  </th>
                <th> Registration Date </th>
            </tr>
        </thead>
        <tbody> 
            <?php foreach($patients as $patient):?>
                <tr> 
                    <td> <?php echo esc_html($patient->registration_id);?> </td> 
                    <td> <?php echo esc_html($patient->p_name);?> </td>
                    <td> <?php echo esc_html($patient->p_email);?></td>
                    <td> <?php echo esc_html($patient->p_mobile);?></td>
                    <td> <?php echo esc_html($patient->registered_date);?></td>
                </tr>
                <?php endforeach;?>
        </tbody>
    </table>
    </div>
    <?php endif;?>

    <?php
     ob_end_flush(); // Flush the output buffer
}
  


   
 


                                                                                            // Show the Patients Appointments List 
    function miraicare_check_appointments() {
    global $wpdb;
    $appointment_table = $wpdb->prefix . 'appointments';
    $doctor_table = $wpdb->prefix . 'doctors';
    
    // Handle accept/reject actions
    if (isset($_POST['action_type']) && isset($_POST['appointment_id'])) {
        $appointment_id = intval($_POST['appointment_id']);
        $action_type = sanitize_text_field($_POST['action_type']);
        
        // Get appointment details for email
        $appointment = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $appointment_table WHERE id = %d", $appointment_id
        ));

        if ($action_type === 'accept') {
            $status = 'Accepted';

            // Prepare email
            $to = sanitize_email($appointment->patient_email);
            $subject = "Welcome to Miraicare Health Your Appointment has been Accepted";
            $message = "Dear " . esc_html($appointment->patient_name) . ",\n\n";
            $message .= "Your appointment has been accepted.\n";
            $message .= "Details:\n";
            $message .= "Patient Name: " . esc_html($appointment->patient_name) . "\n";
            $message .= "Doctor Name: " . esc_html($appointment->doctor_name) . "\n";
            $message .= "Doctor Specialty: " . esc_html($appointment->doctor_specialty) . "\n";
            $message .= "Appointment Date: " . esc_html($appointment->appointment_date) . "\n";
            $message .= "Time Slot: " . esc_html($appointment->appointment_time) . "\n";
            $message .= "Status: " . esc_html($status) . "\n\n";
            $message .= "Thank you for choosing our services!";
            
            // Send email
            wp_mail($to, $subject, $message);

        } elseif ($action_type === 'reject') {
            $status = 'Rejected';
        }

        // Update the status in the database
        $wpdb->update(
            $appointment_table,
            ['status' => $status],
            ['id' => $appointment_id]
        );
    }

    $query = "
        SELECT a.*, d.d_name, d.d_specialty 
        FROM $appointment_table a 
        LEFT JOIN $doctor_table d ON a.doctor_id = d.d_id 
    ";

    $appointments = $wpdb->get_results($query);
    $serial_number = 1;
    ?>
    <div class="wrap"> 
        <h1>Check Appointments</h1>
        <?php if ($appointments): ?>
            <p>This is the content for checking appointments.</p>  
            <table class="widefat fixed">
                <thead>
                    <tr> 
                        <th>Sr.No.</th>
                        <th>Patient Name</th>
                        <th>Patient Email</th>
                        <th>Doctor Name</th>
                        <th>Doctor Specialty</th> 
                        <th>Appointment Date</th>
                        <th>Appointment Time Slot</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody> 
                    <?php foreach ($appointments as $appointment): ?>
                        <tr> 
                            <td><?php echo esc_attr($serial_number) ?></td>
                            <td><?php echo esc_html($appointment->patient_name) ?></td>
                            <td><?php echo esc_html($appointment->patient_email) ?></td>
                            <td><?php echo esc_html($appointment->d_name) ?></td>
                            <td><?php echo esc_html($appointment->d_specialty) ?></td>
                            <td><?php echo esc_html($appointment->appointment_date) ?></td>
                            <td><?php echo esc_html($appointment->appointment_time) ?></td>
                            <td><?php echo esc_html($appointment->status) ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo esc_attr($appointment->id); ?>">
                                    <input type="hidden" name="action_type" value="accept">
                                    <input type="submit" value="Accept" class="button button-primary">
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo esc_attr($appointment->id); ?>">
                                    <input type="hidden" name="action_type" value="reject">
                                    <input type="submit" value="Reject" class="button button-secondary">
                                </form>
                            </td>
                        </tr>
                        <?php $serial_number++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No appointments found.</p>
        <?php endif; ?>
    </div> 
    <?php 
}



 


                                                                                  // Admin Area End Here 



                        // Users Area

                                                                                                   // Shortcode for Display the Doctors Page 
function mh_doctor_cards_shortcode() {
    global $wpdb;
    $doctors_table = $wpdb->prefix . 'doctors';
    
    // Query to get doctor details
    $doctors = $wpdb->get_results("SELECT d_id, d_name, d_specialty, d_image FROM $doctors_table");
    
    // Start output buffering
    ob_start();
  
  
    if ($doctors): ?>
        <div class="doctor-cards">
            <?php foreach ($doctors as $doctor): ?>
                <div class="doctor-card">
                    <img src="<?php echo esc_url($doctor->d_image); ?>" alt="<?php echo esc_attr($doctor->d_name); ?>" style="width: 100%; height: auto;" />
                    <h3> Dr. <?php echo esc_html($doctor->d_name); ?></h3>
                    <p><strong>Specialty:</strong> <?php echo esc_html($doctor->d_specialty); ?></p>
                  
                    <button class= "appointment" onclick = "window.location.href ='<?php echo esc_url(home_url('/book-appointment/?doctor_id=' . $doctor->d_id)); ?>';" >  Book an Appointment</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No doctors found.</p>
    <?php endif;
  
    // Return the output buffer content
    return ob_get_clean();
  }
  
  // Register the shortcode
  add_shortcode('doctor_cards', 'mh_doctor_cards_shortcode');

                                                                                                 // Patient  Appointment Page 
 
                                                                   // Check Status of the patient using Registration id 

// Check Application Status 
function check_appointment() {
    global $wpdb;
    $table_name = $wpdb->prefix .'appointments';

  // Start output buffering
    ob_start();  

    // Output the form
    ?>
    <form id="check-appointment-form">
        <label for="registration_id">Enter Registration ID:</label>
        <input type="text" id="registration_id" name="registration_id" required>
        <button type="submit">Check Status</button>
    </form>
    <div id="appointment-result" style="display:none;"></div>

    <script>
        jQuery(document).ready(function($) {
            $('#check-appointment-form').on('submit', function(e) {
                e.preventDefault();
                
                var registration_id = $('#registration_id').val();
                
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'check_appointment_status',
                        registration_id: registration_id
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status) {
                            $('#appointment-result').html('Status: ' + data.status + '<br>Appointment Date: ' + data.Appointment_date).show();
                        } else {
                            $('#appointment-result').html(data).show();
                        }
                    },
                    error: function() {
                        $('#appointment-result').html('An error occurred. Please try again.').show();
                    }
                });
            });
        });
    </script>
    <?php

    return ob_get_clean();
}
add_shortcode('check_appointment_form', 'check_appointment');

// Handle AJAX request
function check_appointment_status() {
    global $wpdb;
    $table_name = $wpdb->prefix .'appointments';
    
    // Sanitize input
    $registration_id = sanitize_text_field($_POST['registration_id']);

    // Fetch appointment details
    $appointment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE registration_id = %s", $registration_id));

    // Return appointment status
    if ($appointment) {
        echo json_encode(['status' => $appointment->status, 'Appointment_date' => $appointment->appointment_date]);
    } else {
        echo json_encode(['status' => 'Invalid Registration ID']);
    }
    wp_die(); // Required to properly terminate AJAX requests
}
add_action('wp_ajax_check_appointment_status', 'check_appointment_status');
add_action('wp_ajax_nopriv_check_appointment_status', 'check_appointment_status');



                                                // Feature Services and Book Appointments Services 
function feature_services(){
global $wpdb;
$doctor_table  = $wpdb->prefix.'doctors';

// fetch the doctor details from the doctor table 
$doctors = $wpdb->get_results("SELECT d_id, d_name, d_specialty, d_image, department_icon FROM $doctor_table");

ob_start();

if($doctors) :?>
        <div class="service-cards">
            <?php foreach ($doctors as $doctor): ?>
                <div class="service-card">
                    <img src="<?php echo esc_url($doctor->department_icon); ?>" alt="<?php echo esc_attr($doctor->d_specialty); ?>"  />
                    <h3>  <?php echo esc_html($doctor->d_specialty); ?></h3>
                  
                    <button class= "appointment" onclick = "window.location.href ='<?php echo esc_url(home_url('/book-appointment/?doctor_id=' . $doctor->d_id)); ?>';">  View Details </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No doctors found.</p>
    <?php endif;
    // Return the output buffer content
    return ob_get_clean();

}

add_shortcode('our_feature_services','feature_services');


                                                                                            // Function for check  doctor daily appointment Status  
function doctor_daily_appointments(){
    global $wpdb;
    $table_name = $wpdb->prefix.'appointments';

    // Start output buffering
    ob_start();
    
    // Output the form
    ?>
    <form id="daily-appointment-form">

        <label for="d_identity_number">Enter Doctor  ID:</label>
        <input type="text" id="d_identity_number" name="d_identity_number" required/>
          
         <label for="doctor_email"> Enter Doctor Email </label>
        <input type="email" id="doctor_email" name = "doctor_email" required/>  

        <button type="submit" class = "daily-appointment-check-btn">Check Daily Appointments </button>
    </form>

    <div id="appointment-result" style="display:none;"></div>



    <script> 
    jQuery(document).ready(function($) {
        $('#daily-appointment-form').on('submit', function(e) {
            e.preventDefault();

            var d_identity_number = $('#d_identity_number').val();
             var doctor_email = $('#doctor_email').val();

            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'check_daily_appointments',
                    d_identity_number: d_identity_number, 
                     doctor_email: doctor_email
                },
                success: function(response) {
                    // Handle the response
                    var data = response; // No need to parse if using wp_send_json
                    if (data.success) {
                        var doctor = data.data.doctor; // Access doctor info
                        var appointments = data.data.appointments; // Access appointment data
                    
                        // Create a section to display doctor information
                        var doctorHtml = '<div class="doctor-info" style="display: flex; align-items: center;">';
doctorHtml += '<h3 style="margin-right: 20px;">Doctor Information</h3>';
doctorHtml += '<div style="margin-right: 20px;">';
doctorHtml += '<p> <strong> Name: </strong> ' + 'Dr. ' + doctor.name + '</p>';
doctorHtml += '<p> <strong> Department: </strong> ' + doctor.specialty + '</p>';
doctorHtml += '</div>';
doctorHtml += '<img src="' + doctor.image + '" alt="Doctor Image" style="width:100px; height:auto; margin: 20px;" />';
doctorHtml += '</div>';

                        
                        // Create a table to display the results
                        var resultHtml = '<table border="1" style="width:100%; border-collapse: collapse;">';
                        resultHtml += '<tr> <th> Patient_Name </th><th>Status</th><th>Appointment Date</th> <th>Appointment Time </th> </tr>'; // Table headers

                        appointments.forEach(function(appointment) {
                            resultHtml += '<tr>';
                            resultHtml += '<td>' + appointment.patient_name + '</td>';
                            resultHtml += '<td>' + appointment.status + '</td>';
                            resultHtml += '<td>' + appointment.appointment_date + '</td>';
                            resultHtml += '<td>' + appointment.appointment_time + '</td>';
                          
                            resultHtml += '</tr>';
                        });

                        resultHtml += '</table>';
                        $('#appointment-result').html(doctorHtml + resultHtml).show();
                    } 
                    else {
                        $('#appointment-result').html(data.data.message).show();
                    }
                },
                error: function() {
                    $('#appointment-result').html('An error occurred. Please try again.').show();
                }
            });
        });
    });
</script>
    <?php
    // Return the output buffer content 
    return ob_get_clean();
}
add_shortcode('mirai_doc_appointmnet','doctor_daily_appointments');



                                                                                             // Handle AJAX request to check appointment status
function check_daily_appointments() {
    global $wpdb;
    $table_name = $wpdb->prefix .'appointments';

    // Start output buffering 
    ob_start();
    
    // Sanitize input
    $d_identity_number = intval($_POST['d_identity_number']);
    $doctor_email = sanitize_email($_POST['doctor_email']);       
   
    
    // Fetch appointments for the given doctor
   $query = "  SELECT 
    a.*, 
    d_name AS doctor_name, 
    d_specialty AS doctor_specialty, 
    d_image AS doctor_image 
FROM 
   $table_name AS a
JOIN 
    wp_doctors AS d ON a.doctor_email = d_email
WHERE 
    a.d_identity_number = %d  
    AND a.doctor_email = %s 
    AND DATE(a.appointment_date) BETWEEN CURDATE() AND CURDATE() + INTERVAL 1 DAY";

    $sql = $wpdb->prepare($query, $d_identity_number, $doctor_email);
    $appointments = $wpdb->get_results($sql);


    if ($appointments) {

         // Extract doctor information from the first appointment
         $doctor_info = [
            'name' => $appointments[0]->doctor_name,
            'specialty' => $appointments[0]->doctor_specialty,
            'image' => $appointments[0]->doctor_image
        ];
        $appointment_data = array();
        foreach ($appointments as $appointment) {
            $appointment_data[] = [
                'status' => $appointment->status,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'patient_name'=> $appointment->patient_name,

                // Add any other relevant fields here
            ];
        }
 
        // Send response
        wp_send_json_success(['doctor' => $doctor_info, 'appointments' => $appointment_data]);
    } else {
        wp_send_json_error(['message' => 'No appointments Found For Today.']);
    }
}

add_action('wp_ajax_check_daily_appointments', 'check_daily_appointments');
add_action('wp_ajax_nopriv_check_daily_appointments', 'check_daily_appointments');