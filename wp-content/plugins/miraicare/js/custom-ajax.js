jQuery(document).ready(function($) {
    $('.edit-button').on('click', function() {
        var sessionId = $(this).data('id');
        var sessionDay = $(this).data('day');
        var sessionTime = $(this).data('time');

        // Show a simple popup/modal (you can use a library like jQuery UI or Bootstrap for a nicer modal)
        var popupContent = '<div>' +
                           '<label for="session_day">Session Day:</label>' +
                           '<input type="text" id="session_day" value="' + sessionDay + '"/><br>' +
                           '<label for="session_time">Session Time:</label>' +
                           '<input type="text" id="session_time" value="' + sessionTime + '"/><br>' +
                           '<button id="save-button" data-id="' + sessionId + '">Save</button>' +
                           '</div>';

        // Display the popup (using alert as an example, replace with a modal for better UX)
        alert(popupContent);

        // Save the updated data when the Save button is clicked
        $('#save-button').on('click', function() {
            var updatedDay = $('#session_day').val();
            var updatedTime = $('#session_time').val();
            var id = $(this).data('id');

            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'update_session',
                    session_id: id,
                    session_day: updatedDay,
                    session_time: updatedTime
                },
                success: function(response) {
                    alert('Session updated successfully!');
                    location.reload(); // Refresh the page to see updated data
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        });
    });
});