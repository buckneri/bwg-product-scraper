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
});
