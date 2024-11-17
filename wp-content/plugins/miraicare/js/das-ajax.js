jQuery(document).ready(function($) {
    $('input[name="appointment_date"]').change(function() {
         var selectedDate = $(this).val();
        var doctorId = $('input[name="doctor_id"]').val(); // Assuming you have a hidden input for doctor_id
          var selectedweekDate = new Date($(this).val());
        var options = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var dayName = options[selectedweekDate.getUTCDay()];


        $.ajax({
            type: 'POST',
            url: das_ajax_obj.ajax_url,
            data: {
                action: 'get_booked_slots',
                date: selectedDate,
                doctor_id: doctorId
            },
            success: function(response) {
                $('select[name="appointment_time"]').find('option:not(:first)').remove();
                $.each(response, function(index, slot) {
                    $('select[name="appointment_time"]').append('<option value="' + slot + '">' + slot + '</option>');
                });
                $('#appointment_day').val(dayName);
            }
        });
        
    });

    $('#appointment-form').submit(function(e) {
        e.preventDefault(); // Prevent default form submission
        var formData = $(this).serialize(); // Serialize the form data

        $.ajax({
            type: 'POST',
            url: das_ajax_obj.ajax_url,
            data: {
                action: 'book_appointment',
                form_data: formData
            },
            success: function(response) {
                $('#response-message').html(response.message); // Display response message
            }
        });
    });
});
