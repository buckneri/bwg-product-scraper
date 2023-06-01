// /admin/js/admin-script.js
jQuery(document).ready(function($) {
    // Image uploader variables
    var mediaUploader;
    var removeButton = $('#remove_placeholder_image_button');
    var imageContainer = $('#placeholder_image_container');
    var imagePreview = $('#placeholder_image_preview');
    var mediaIdInput = $('#media_image_id');
    var saveSettingsButton = $('[name="save_plugin_settings"]');

    // Handle the click event of the "Choose Image" button
    $('#upload_placeholder_image_button').on('click', function(e) {
        e.preventDefault();

        // If the media uploader exists, open it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create the media uploader
        mediaUploader = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Select'
            },
            multiple: false
        });

        // Handle the selection of an image
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();

            // Update the image preview
            imagePreview.attr('src', attachment.url);

            // Update the media ID input
            mediaIdInput.val(attachment.id);

            // Show the remove button
            removeButton.show();

            // Submit the form
            saveSettingsButton.trigger('click');
        });

        // Open the media uploader
        mediaUploader.open();
    });

    // Handle the click event of the "Remove Image" button
    removeButton.on('click', function(e) {
        e.preventDefault();

        // Remove the image preview
        imagePreview.attr('src', '');

        // Reset the media ID input
        mediaIdInput.val('');

        // Hide the remove button
        removeButton.hide();

        // Submit the form
        saveSettingsButton.trigger('click');
    });

    // Handle the click event of the "Test" button
$('[name="test_website"]').on('click', function(e) {
    e.preventDefault();
    // Get the website data from the form
    var websiteData = {
        image_css: $('#image_css').val(),
        description_css: $('#description_css').val(),
        price_css: $('#price_css').val(),
        sku_css: $('#sku_css').val(),
        short_desc_css: $('#short_desc_css').val(),
        title_css: $('#title_css').val(),
        example_product_url: $('#example_product_url').val(),
    };

    // Send an AJAX request to test the website
    $.ajax({
        url: ajaxurl,
        method: 'POST',
        data: {
            action: 'test_website',
            website_data: websiteData
        },
        beforeSend: function() {
            console.log('Sending AJAX request...');
        },
        success: function(response) {
            // Log the response for debugging
            console.log('AJAX response:', response);
            console.log('Type of AJAX response:', typeof response);
            console.log('Response properties:', response.data.title, response.data.description, response.data.short_description, response.data.price, response.data.images);

            // Create a new modal element
            var modal = $('<div class="modal" tabindex="-1" role="dialog"></div>');

            // Load modal content from template file
            modal.load(bwgps.pluginsUrl + '/modal-content.php', function() {
                // Append the modal to the body
                $('body').append(modal);

                var imagesHtml = '';
                for (var i = 0; i < response.data.images.length; i++) {
                    imagesHtml += '<img src="' + response.data.images[i] + '" style="width:100px;height:100px;">';
                }
                modal.find('.modal-body').html(
                    "<p><strong>Title:</strong> " + response.data.title + "</p>" +
                    "<p><strong>Description:</strong> " + response.data.description + "</p>" +
                    "<p><strong>Short Description:</strong> " + response.data.short_description + "</p>" +
                    "<p><strong>Price:</strong> " + response.data.price + "</p>" +
                    "<p><strong>SKU:</strong> " + response.data.sku + "</p>" +
                    imagesHtml
                );

                // Show the modal popup
                modal.modal('show');

                // Clean up the modal after it's closed
                modal.on('hidden.bs.modal', function() {
                    modal.remove();
                });

                console.log('Modal loaded and displayed successfully.');
            });
        },

        error: function(xhr, status, error) {
            // Log the AJAX error for debugging
            console.log('AJAX error:', error);

            // Show an error message
            var errorMessage = '<div class="alert alert-danger">An error occurred: ' + error + '</div>';
            $('body').append(errorMessage);
        },
        complete: function() {
            console.log('AJAX request completed.');
        }
    });

});




});
