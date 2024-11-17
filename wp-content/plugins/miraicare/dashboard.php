<?php
// Exist if Accessed directly 
if (!defined('ABSPATH'))
{
    exit;
}



// Show the clinic details like doctors list , Appointments List etc....... 
                                                            // Show Doctors List 
                                                            function miraicare_clinic_setup() {
                                                                echo '<h1 style="text-align:center">Admin Dashboard</h1>';
                                                                global $wpdb;
                                                            
                                                                // Total Number of Patients:
                                                                $patients_table  = $wpdb->prefix . 'patients';
                                                                $total_patients  = $wpdb->get_results("SELECT * FROM $patients_table"); 
                                                            
                                                                // Total Numbers of Doctors:
                                                                $doctors_table  = $wpdb->prefix . 'doctors';
                                                                $total_doctors  = $wpdb->get_results("SELECT * FROM $doctors_table");
                                                                
                                                                // Total Appointments: 
                                                                $appointments_table = $wpdb->prefix . 'appointments';
                                                                $total_appointments = $wpdb->get_results("SELECT * FROM $appointments_table");
                                                            
                                                                // Display the counts in cards
                                                                echo '<div class="dashboard-cards">';
                                                                echo '<div class="card"><h2>Total Patients</h2><p>' . count($total_patients) . '</p></div>';
                                                                echo '<div class="card"><h2>Total Doctors</h2><p>' . count($total_doctors) . '</p></div>';
                                                                echo '<div class="card"><h2>Total Appointments</h2><p>' . count($total_appointments) . '</p></div>';
                                                                echo '</div>';


                                                                  // Display today's appointments
    echo '<h2 style="text-align:center">Today\'s Appointments</h2>';
    echo daily_appointments_display(); // Call the daily appointments display function
                                                            }
                                                            


            // Function for showing the current Appointments in the page
            function daily_appointments_display() {
                global $wpdb;
                $appointment_table = $wpdb->prefix . 'appointments'; 
                $today = date('Y-m-d'); 
                $appointments = $wpdb->get_results("SELECT * FROM $appointment_table WHERE appointment_date = '$today' ORDER BY doctor_specialty");
                ob_start();
                ?> 
            
                <div class="daily_appointment_table"> 
                    <table class="da_table"> 
                        <thead> 
                            <tr> 
                                <th> Department Name    </th>
                                <th> Doctor Name        </th> 
                                <th> Patient Name       </th>
                                <th> Appointment Date   </th>
                                <th> Appointment Time   </th>
                                <th> Appointment_status </th>
                            </tr>
                        </thead>
                        <tbody> 
                            <?php if ($appointments): // Check if there are appointments ?>
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr> 
                                        <td><?php echo esc_html ($appointment->doctor_specialty);?> </td>
                                        <td><?php echo esc_html ($appointment->doctor_name);?>      </td>
                                        <td><?php echo esc_html ($appointment->patient_name);?>     </td>
                                        <td><?php echo esc_html ($appointment->appointment_date);?> </td>
                                        <td><?php echo esc_html ($appointment->appointment_time);?> </td>
                                        <td><?php echo esc_html ($appointment->status);?>           </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: // If no appointments ?>
                                <tr>
                                    <td colspan="5">No appointments for today.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php
                return ob_get_clean(); // Return the buffered content
            }
            
            // Register the shortcode
            add_shortcode('daily_appointments', 'daily_appointments_display');
            

        
