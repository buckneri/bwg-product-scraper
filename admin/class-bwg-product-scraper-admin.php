<?php
// /admin/class-bwg-product-scraper-admin.php
class BWG_Product_Scraper_Admin {

    private $version;

    public function __construct($version) {
        $this->version = $version;
        add_action('admin_menu', array($this, 'add_admin_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'handle_form_submission'));
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_media();
        wp_enqueue_script('bwgps-admin-script', plugin_dir_url(__FILE__) . 'js/admin-script.js', array('jquery'), $this->version, true);
        wp_localize_script('bwgps-admin-script', 'bwgps', array(
            'mediaUploaderTitle' => __('Choose Image', 'bwgps'),
            'mediaUploaderButton' => __('Select', 'bwgps')
        ));
    }

    public function add_admin_pages() {
        add_options_page(
            'BWG Product Scraper',
            'BWG Product Scraper',
            'manage_options',
            'bwg-product-scraper',
            array($this, 'display_settings_page')
        );
    }

    public function display_settings_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'import_products';

        echo '<div class="wrap">';
        echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';

        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=bwg-product-scraper&tab=import_products" class="nav-tab ' . ($active_tab == 'import_products' ? 'nav-tab-active' : '') . '">Import Products</a>';
        echo '<a href="?page=bwg-product-scraper&tab=manage_websites" class="nav-tab ' . ($active_tab == 'manage_websites' ? 'nav-tab-active' : '') . '">Manage Websites</a>';
        echo '<a href="?page=bwg-product-scraper&tab=plugin_settings" class="nav-tab ' . ($active_tab == 'plugin_settings' ? 'nav-tab-active' : '') . '">Plugin Settings</a>';
        echo '</h2>';

        switch ($active_tab) {
            case 'import_products':
                $this->display_import_products_tab();
                break;
            case 'manage_websites':
                $this->display_manage_websites_tab();
                break;
            case 'plugin_settings':
                $this->display_plugin_settings_tab();
                break;
        }

        echo '</div>';
    }

    public function display_import_products_tab() {
        $websites = array(
            'example_site' => 'Example Site'
        );

        echo '<form action="" method="post">';
        echo '<label for="website">Website:</label><br/>';
        echo '<select id="website" name="website">';
        foreach ($websites as $slug => $name) {
            echo "<option value='{$slug}'>{$name}</option>";
        }
        echo '</select>';

        echo '<br/><br/><label for="product_urls">Product URLs (one per line):</label><br/>';
        echo '<textarea id="product_urls" name="product_urls" rows="10" cols="50"></textarea>';

        echo '<br/><br/><input type="submit" name="import_products" value="Import Products"/>';
        echo '</form>';
    }

    public function display_manage_websites_tab() {
	    echo '<form action="" method="post" enctype="multipart/form-data">';

	    $website_draft = get_option('bwgps_website_draft', array());

	    $display_name = isset($website_draft['display_name']) ? $website_draft['display_name'] : '';
	    echo '<label for="display_name">Display Name:</label><br/>';
	    echo '<input type="text" id="display_name" name="display_name" value="' . esc_attr($display_name) . '" required/>';

	    $home_url = isset($website_draft['home_url']) ? $website_draft['home_url'] : '';
	    echo '<br/><br/><label for="home_url">Site Home Page URL:</label><br/>';
	    echo '<input type="url" id="home_url" name="home_url" value="' . esc_attr($home_url) . '" required/>';
		
		$title_css = isset($website_draft['title_css']) ? $website_draft['title_css'] : '';
		echo '<br/><br/><label for="title_css">Product Title CSS Identifier:</label><br/>';
		echo '<input type="text" id="title_css" name="title_css" value="' . esc_attr($title_css) . '"/>';

	    $image_css = isset($website_draft['image_css']) ? $website_draft['image_css'] : '';
	    echo '<br/><br/><label for="image_css">Product Image CSS Identifier:</label><br/>';
	    echo '<input type="text" id="image_css" name="image_css" value="' . esc_attr($image_css) . '"/>';

	    $description_css = isset($website_draft['description_css']) ? $website_draft['description_css'] : '';
	    echo '<br/><br/><label for="description_css">Product Description CSS Identifier:</label><br/>';
	    echo '<input type="text" id="description_css" name="description_css" value="' . esc_attr($description_css) . '"/>';

	    $short_desc_css = isset($website_draft['short_desc_css']) ? $website_draft['short_desc_css'] : '';
	    echo '<br/><br/><label for="short_desc_css">Product Short Description CSS Identifier:</label><br/>';
	    echo '<input type="text" id="short_desc_css" name="short_desc_css" value="' . esc_attr($short_desc_css) . '"/>';

	    $price_css = isset($website_draft['price_css']) ? $website_draft['price_css'] : '';
	    echo '<br/><br/><label for="price_css">Product Price CSS Identifier:</label><br/>';
	    echo '<input type="text" id="price_css" name="price_css" value="' . esc_attr($price_css) . '"/>';

	    $example_product_url = isset($website_draft['example_product_url']) ? $website_draft['example_product_url'] : '';
    	echo '<br/><br/><label for="example_product_url">Example Product URL:</label><br/>';
    	echo '<input type="url" id="example_product_url" name="example_product_url" value="' . esc_attr($example_product_url) . '"/>';

	    echo '<br/><br/><input type="submit" name="save_website_draft" value="Save Draft"/>';
	    echo '<input type="submit" name="test_website" value="Test"/>';
	    echo '<input type="submit" name="add_website" value="Add Website"/>';

	    echo '</form>';

	    echo '<h2>Saved Websites</h2>';

	    $websites = get_option('bwgps_websites', array());

	    echo '<table>';
	    echo '<tr><th>Display Name</th><th>Home URL</th><th>Image CSS</th><th>Description CSS</th><th>Price CSS</th><th>Short Desc CSS</th><th>Placeholder Image</th></tr>';

	    foreach ($websites as $index => $website) {
	        echo '<tr>';
	        echo '<td>' . esc_html($website['display_name']) . '</td>';
	        echo '<td>' . esc_html($website['title_css']) . '</td>';
	        echo '<td>' . esc_html($website['home_url']) . '</td>';
	        echo '<td>' . esc_html($website['image_css']) . '</td>';
	        echo '<td>' . esc_html($website['description_css']) . '</td>';
	        echo '<td>' . esc_html($website['price_css']) . '</td>';
	        echo '<td>' . esc_html($website['short_desc_css']) . '</td>';
	        echo '<td><form action="" method="post"><input type="hidden" name="website_index" value="' . $index . '"/><input type="submit" name="delete_website" value="Delete"/></form></td>';
	        echo '</tr>';
	    }
	    echo '</table>';
	}



    public function display_plugin_settings_tab() {
	    // Get the saved image URL and media ID from the options table
	    $placeholder_image_url = get_option('bwgps_placeholder_image', '');
	    $media_image_id = get_option('bwgps_media_image_id', '');

	    echo '<form action="" method="post" enctype="multipart/form-data">';
	    echo '<br/><br/><label for="placeholder_image">Default Placeholder Image:</label><br/>';
	    echo '<input type="hidden" id="media_image_id" name="media_image_id" value="' . esc_attr($media_image_id) . '" />';
	    echo '<div id="placeholder_image_container">';
	    if ($placeholder_image_url) {
	        echo '<img id="placeholder_image_preview" src="' . esc_url($placeholder_image_url) . '" alt="Placeholder image" width="100" /><br/>';
	        echo '<button type="button" id="remove_placeholder_image_button">Remove Image</button><br/>';
	    } else {
	        echo '<button type="button" id="upload_placeholder_image_button">Choose Image</button><br/>';
	    }
	    echo '</div>';
	    echo '<br/><br/><input type="submit" name="save_plugin_settings" value="Save Settings"/>';
	    echo '</form>';

	    // Include the necessary JavaScript code
	    $this->enqueue_admin_scripts();

	    // Log the variables for debugging
	    error_log('Placeholder Image: ' . $placeholder_image_url);
	    error_log('Media Image ID: ' . $media_image_id);
	}

    public function handle_form_submission() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['add_website'])) {
            // Handle website form submission
            $display_name = sanitize_text_field($_POST['display_name']);
            $home_url = esc_url_raw($_POST['home_url']);
            $root_url = esc_url_raw($_POST['root_url']);
            $image_css = sanitize_text_field($_POST['image_css']);
            $description_css = sanitize_text_field($_POST['description_css']);
            $price_css = sanitize_text_field($_POST['price_css']);
            $short_desc_css = sanitize_text_field($_POST['short_desc_css']);
            $title_css = sanitize_text_field($_POST['title_css']);
            
            // TODO: Handle the file upload and store the image URL
            $placeholder_image = '';  // Placeholder until we implement file uploads

            $website_data = array(
                'display_name' => $display_name,
                'home_url' => $home_url,
                'image_css' => $image_css,
                'description_css' => $description_css,
                'price_css' => $price_css,
                'short_desc_css' => $short_desc_css,
                'title_css' => $title_css
            );

            // Get the current list of websites
            $websites = get_option('bwgps_websites', array());

            // Add the new website to the list
            $websites[] = $website_data;

            // Update the websites list in the database
            update_option('bwgps_websites', $websites);

            // Add a success message to be displayed
            add_settings_error('bwgps_websites', esc_attr('settings_updated'), 'Website added successfully.', 'updated');

            // Clear the website draft
            delete_option('bwgps_website_draft');

        } elseif (isset($_POST['save_website_draft'])) {
            // Handle website draft form submission
            $website_draft = array(
                'display_name' => sanitize_text_field($_POST['display_name']),
                'home_url' => esc_url_raw($_POST['home_url']),
                'root_url' => esc_url_raw($_POST['root_url']),
                'image_css' => sanitize_text_field($_POST['image_css']),
                'description_css' => sanitize_text_field($_POST['description_css']),
                'price_css' => sanitize_text_field($_POST['price_css']),
                'short_desc_css' => sanitize_text_field($_POST['short_desc_css']),
                'title_css' => sanitize_text_field($_POST['title_css']),
                'example_product_url' => sanitize_text_field($_POST['example_product_url'])
            );

            update_option('bwgps_website_draft', $website_draft);

        } elseif (isset($_POST['delete_website'])) {
            // Handle website deletion
            $index_to_delete = intval($_POST['website_index']);
            $websites = get_option('bwgps_websites', array());
            if (isset($websites[$index_to_delete])) {
                unset($websites[$index_to_delete]);
                // Re-index the array
                $websites = array_values($websites);
                update_option('bwgps_websites', $websites);
                add_settings_error('bwgps_websites', esc_attr('settings_updated'), 'Website deleted successfully.', 'updated');
            }

        } elseif (isset($_POST['save_plugin_settings'])) {
            // Handle the placeholder image selection from media library
            $media_image_id = isset($_POST['media_image_id']) ? intval($_POST['media_image_id']) : 0;
            $placeholder_image_url = '';

            if ($media_image_id) {
                $attachment_url = wp_get_attachment_url($media_image_id);
                if ($attachment_url) {
                    $placeholder_image_url = esc_url_raw($attachment_url);
                }
            }

            update_option('bwgps_placeholder_image', $placeholder_image_url);
            update_option('bwgps_media_image_id', $media_image_id);
        }

        // Log the variables for debugging
        error_log('Placeholder Image: ' . $placeholder_image_url);
        error_log('Media Image ID: ' . $media_image_id);
    }

    public function run() {
        $bwgps_admin = new BWG_Product_Scraper_Admin($this->version);
        $bwgps_admin->handle_form_submission();
    }
}
