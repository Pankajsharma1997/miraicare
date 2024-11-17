<?php 
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
// Add a Doctor
function miraicare_add_doctor_() {
    // Check if the form has been submitted
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

        // Check the last doctor_identity number in the doctors  table
            $last_identity_number = $wpdb->get_var("SELECT d_identity_number FROM $table_name ORDER BY id DESC LIMIT 1");
            // If no doctor identity number  exist in table, set a default
if (!$last_identity_number) {
    $new_identity_number = 131101; // Default starting ID
} else {
    $new_identity_number = intval($last_identity_number) + 1; // Increment the highest found registration ID
}
// Format as a 6-digit string
$doctor_identity_number = str_pad($new_identity_number, 6, '0', STR_PAD_LEFT);


        // Insert the doctor into the database
        $wpdb->insert($table_name, array(
            'd_name'          => $doctor_name,
            'd_identity_number'=>$doctor_identity_number,
            'd_specialty'     => $doctor_specialty,
            'd_email'         => $doctor_email,
            'd_phone'         => $doctor_phone,
            'd_image'         => $doctor_image_url,
            'department_icon' => $department_icon_url,
        ));

      echo'<div class="updated"> <p> Doctor Updated Sucessfully </p></div>';
    }
    ?>

    <div class="wrap"> 
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
                <input type="submit" name="submit" class="submit" value="Add Doctor" />
            </p>
        </form>
    </div>
    <?php
}