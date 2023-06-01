<?php
// /admin/class-bwg-product-scraper-admin.php
// Import the required libraries
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
use GuzzleHttp\Client;

class BWG_Product_Scraper_Admin {

    private $version;

    public function __construct($version) {
        $this->version = $version;
        add_action('admin_menu', array($this, 'add_admin_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'handle_form_submission'));
        add_action('wp_ajax_test_website', array($this, 'test_website_ajax_handler'));
    }

    public function enqueue_admin_scripts() {
	    wp_enqueue_media();
	    wp_enqueue_script('bwgps-admin-script', plugin_dir_url(__FILE__) . 'js/admin-script.js', array('jquery'), $this->version, true);
	    wp_enqueue_style('bwgps-bootstrap-style', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
	    wp_enqueue_script('bwgps-bootstrap-script', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true);
	    wp_enqueue_style('bwgps-custom-style', plugin_dir_url(__FILE__) . 'css/custom-style.css'); 
	    wp_localize_script('bwgps-admin-script', 'bwgps', array(
	        'mediaUploaderTitle' => __('Choose Image', 'bwgps'),
	        'mediaUploaderButton' => __('Select', 'bwgps'),
	        'pluginsUrl' => plugin_dir_url(__FILE__),
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
        $websites = get_option('bwgps_websites', array());

        echo '<form action="" method="post">';
        echo '<label for="website">Website:</label><br/>';
        echo '<select id="website" name="website">';
        
        foreach ($websites as $website) {
            $slug = sanitize_title($website['display_name']);
            $name = esc_html($website['display_name']);
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

        $sku_css = isset($website_draft['sku_css']) ? $website_draft['sku_css'] : '';
        echo '<br/><br/><label for="sku_css">Product SKU CSS Identifier:</label><br/>';
        echo '<input type="text" id="sku_css" name="sku_css" value="' . esc_attr($sku_css) . '"/>';

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
	    echo '<tr><th>Display Name</th><th>Home URL</th></tr>';

	    foreach ($websites as $index => $website) {
	        echo '<tr>';
	        echo '<td>' . esc_html($website['display_name']) . '</td>';
	        echo '<td>' . esc_html($website['home_url']) . '</td>';
	        echo '<td><form action="" method="post"><input type="hidden" name="website_index" value="' . $index . '"/>';
            echo '<input type="submit" name="delete_website" value="Delete"/>';
            echo '<input type="submit" name="edit_website" value="Edit"/></form></td>';
            echo '</tr>';

	        echo '</tr>';
	    }
	    echo '</table>';
		 // Include the necessary JavaScript code for handling the test button and modal popup
	    $this->enqueue_admin_scripts();
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
            $image_css = sanitize_text_field($_POST['image_css']);
            $description_css = sanitize_text_field($_POST['description_css']);
            $price_css = sanitize_text_field($_POST['price_css']);
            $short_desc_css = sanitize_text_field($_POST['short_desc_css']);
            $title_css = sanitize_text_field($_POST['title_css']);
            $example_product_url = sanitize_text_field($_POST['example_product_url']);
            $sku_css = sanitize_text_field($_POST['sku_css']);
            
            $placeholder_image = '';  // Placeholder until we implement file uploads

            $website_data = array(
                'display_name' => $display_name,
                'home_url' => $home_url,
                'image_css' => $image_css,
                'description_css' => $description_css,
                'price_css' => $price_css,
                'short_desc_css' => $short_desc_css,
                'title_css' => $title_css,
                'sku_css' => $sku_css,
                'example_product_url' => $example_product_url
            );

            // Get the current list of websites
            $websites = get_option('bwgps_websites', array());

            // Check if the website already exists in the list
            $existing_index = -1;

            foreach ($websites as $index => $website) {
                if ($website['display_name'] === $display_name && $website['home_url'] === $home_url) {
                    $existing_index = $index;
                    break;
                }
            }

            if ($existing_index !== -1) {
                // Website already exists, update its data
                $websites[$existing_index] = $website_data;
                add_settings_error('bwgps_websites', esc_attr('settings_updated'), 'Website updated successfully.', 'updated');
            } else {
                // Website does not exist, add it to the list
                $websites[] = $website_data;
                add_settings_error('bwgps_websites', esc_attr('settings_updated'), 'Website added successfully.', 'updated');
            }

            // Update the websites list in the database
            update_option('bwgps_websites', $websites);

            // Clear the website draft
            delete_option('bwgps_website_draft');

        } elseif (isset($_POST['save_website_draft'])) {
            // Handle website draft form submission
            $website_draft = array(
                'display_name' => sanitize_text_field($_POST['display_name']),
                'home_url' => esc_url_raw($_POST['home_url']),
                'image_css' => sanitize_text_field($_POST['image_css']),
                'description_css' => sanitize_text_field($_POST['description_css']),
                'price_css' => sanitize_text_field($_POST['price_css']),
                'sku_css' => sanitize_text_field($_POST['sku_css']),
                'short_desc_css' => sanitize_text_field($_POST['short_desc_css']),
                'title_css' => sanitize_text_field($_POST['title_css']),
                'example_product_url' => sanitize_text_field($_POST['example_product_url'])
            );

            update_option('bwgps_website_draft', $website_draft);
            add_settings_error('bwgps_websites', esc_attr('settings_updated'), 'Saved draft successfully.', 'updated');

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

        } elseif (isset($_POST['edit_website'])) {
            // Handle website editing
            $index_to_edit = intval($_POST['website_index']);
            $websites = get_option('bwgps_websites', array());
            if (isset($websites[$index_to_edit])) {
                $website_to_edit = $websites[$index_to_edit];
                
                // Set the website draft data as the values for the form fields
                $website_draft = array(
                    'display_name' => $website_to_edit['display_name'],
                    'home_url' => $website_to_edit['home_url'],
                    'image_css' => $website_to_edit['image_css'],
                    'description_css' => $website_to_edit['description_css'],
                    'price_css' => $website_to_edit['price_css'],
                    'sku_css' => $website_to_edit['sku_css'],
                    'short_desc_css' => $website_to_edit['short_desc_css'],
                    'title_css' => $website_to_edit['title_css'],
                    'example_product_url' => $website_to_edit['example_product_url']
                );

                update_option('bwgps_website_draft', $website_draft);
                add_settings_error('bwgps_websites', esc_attr('settings_updated'), 'Website loaded for editing successfully.', 'updated');
               
            }
        }elseif (isset($_POST['save_plugin_settings'])) {
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

        }elseif (isset($_POST['import_products'])) {
            // 'import_products' button is pressed. Process the form.

            // Sanitize and get the selected website from the dropdown
            $website_slug = sanitize_text_field($_POST['website']);
            $websites = get_option('bwgps_websites', array());
            $website = array_filter($websites, function ($website) use ($website_slug) {
                return sanitize_title($website['display_name']) === $website_slug;
            });
            if (empty($website)) {
                // Handle case where website is not found
                return;
            }
            $website = array_shift($website);  // Get the first item

            // Split the product URLs by line, sanitize, and filter out any empty values
            $product_urls = array_filter(array_map('trim', explode("\n", sanitize_textarea_field($_POST['product_urls']))));

            // Call import_products with all the URLs at once
            $this->import_products($website, $product_urls);
        }

	}

	public function test_website_ajax_handler() {
        // Check for permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        if (!isset($_POST['website_data']) || !is_array($_POST['website_data'])) {
            wp_send_json_error('Invalid data');
            return;
        }

        // Access the correct key in $_POST
        $data = $_POST['website_data'];

        // Your sanitization and handling logic here
        $website_draft = array(
            'image_css' => sanitize_text_field($data['image_css']),
            'description_css' => sanitize_text_field($data['description_css']),
            'price_css' => sanitize_text_field($data['price_css']),
            'sku_css' => sanitize_text_field($data['sku_css']),
            'short_desc_css' => sanitize_text_field($data['short_desc_css']),
            'title_css' => sanitize_text_field($data['title_css']),
            'example_product_url' => sanitize_text_field($data['example_product_url'])
        );

        error_log('Website draft: ' . print_r($website_draft, true));

        // Perform the website scraping
        $scrape_data = $this->scrape_website($website_draft);
        // Return the scraped data as JSON response
        wp_send_json_success($scrape_data);
    }



		private function scrape_website($website_draft) {
            // Check if a URL is provided
            if (empty($website_draft['example_product_url'])) {
                error_log('URL not provided');
                return;
            }

            $client = new GuzzleHttp\Client();
            $url = filter_var($website_draft['example_product_url'], FILTER_VALIDATE_URL);
            if (!$url) {
                error_log('Invalid URL');
                return;
            }

            $response = $client->request('GET', $website_draft['example_product_url']);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);
            $converter = new CssSelectorConverter();

            // Check if the title CSS selector is not "n/a"
            if ($website_draft['title_css'] !== 'n/a') {
                $title_css = $converter->toXPath($website_draft['title_css']);
                $titleNodes = $crawler->filterXPath($title_css);
                $title = $titleNodes->count() > 0 ? $titleNodes->text() : '';
            } else {
                $title = '';
            }
            error_log('We haz the title');

            // Check if the title CSS selector is not "n/a"
            if ($website_draft['sku_css'] !== 'n/a') {
                $sku_css = $converter->toXPath($website_draft['sku_css']);
                $skuNodes = $crawler->filterXPath($sku_css);
                $sku = $skuNodes->count() > 0 ? $skuNodes->text() : '';
            } else {
                $title = '';
            }
            error_log('We haz the SKU');

            // Check if the price CSS selector is not "n/a"
            if ($website_draft['price_css'] !== 'n/a') {
                $price_css = $converter->toXPath($website_draft['price_css']);
                $priceNodes = $crawler->filterXPath($price_css);
                $price = $priceNodes->count() > 0 ? $priceNodes->text() : '';
            } else {
                $price = '';
            }
            error_log('We haz the price');

            // Check if the description CSS selector is not "n/a"
            if ($website_draft['description_css'] !== 'n/a') {
                $description_css = $converter->toXPath($website_draft['description_css']);
                $descriptionNodes = $crawler->filterXPath($description_css);
                $description = $descriptionNodes->count() > 0 ? $descriptionNodes->text() : '';
            } else {
                $description = '';
            }
            error_log('We haz the description');

            // Check if the short description CSS selector is not "n/a"
            if ($website_draft['short_desc_css'] !== 'n/a') {
                $short_desc_css = $converter->toXPath($website_draft['short_desc_css']);
                $shortDescNodes = $crawler->filterXPath($short_desc_css);
                $short_description = $shortDescNodes->count() > 0 ? $shortDescNodes->text() : '';
            } else {
                $short_description = '';
            }
            error_log('We haz the short description');

            // Check if the image CSS selector is not "n/a"
            if ($website_draft['image_css'] !== 'n/a') {
                $image_css = $converter->toXPath($website_draft['image_css']);
                $imageNodes = $crawler->filterXPath($image_css);
                $images = [];
                foreach ($imageNodes as $node) {
                    $src = $node->getAttribute('src');
                    if ($src && !preg_match('/data:image/', $src)) {
                        $images[] = $src;
                    } else {
                        $amsrc = $node->getAttribute('data-amsrc');
                        if ($amsrc) {
                            $images[] = $amsrc;
                        }
                    }
                }
            } else {
                $images = [];
            }
            error_log('We haz the images');
            // Extract more data fields as needed

		    // Log the scraped data for debugging
		    error_log('Scraped Title: ' . $title);
		    error_log('Scraped Price: ' . $price);
            error_log('Scraped SKU: ' . $sku);
		    error_log('Scraped Description: ' . $description);
		    error_log('Scraped Short Description: ' . $short_description);
            error_log('Scraped Images: ' . implode(', ', $images));

		    $scraped_data = array(
		        'title' => $title,
		        'description' => $description,
		        'short_description' => $short_description,
		        'price' => $price,
                'sku' => $sku,
                'images' => $images  // Add the images array to the scraped data

		        // Add more fields as needed
		    );

		    return $scraped_data;
		}
      

/*
BEGIN FUNTIONS FOR ACTUAL PRODUCT SCRAPING AND IMPORTING
*/

        public function import_products($website, $product_urls) {
            // Scrape each URL and create a product with the scraped data
            foreach ($product_urls as $url) {
                $scraped_data = $this->scrape_single_product($website, trim($url));
                $this->create_product($scraped_data);
            }
        }
      private function scrape_single_product($website, $product_url) {
            // Check if a URL is provided
            if (empty($product_url)) {
                error_log('URL not provided');
                return;
            }

            $client = new GuzzleHttp\Client();
            $url = filter_var($product_url, FILTER_VALIDATE_URL);
            if (!$url) {
                error_log('Invalid URL');
                return;
            }

            $response = $client->request('GET', $product_url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);
            $converter = new CssSelectorConverter();

            // Extract title
            $title = $this->extract_data($crawler, $converter, $website, 'title_css');

            // Extract price
            $price = $this->extract_data($crawler, $converter, $website, 'price_css');

            // Extract price
            $sku = $this->extract_data($crawler, $converter, $website, 'sku_css');

            // Extract description
            $description = $this->extract_data($crawler, $converter, $website, 'description_css');

            // Extract short description
            $short_description = $this->extract_data($crawler, $converter, $website, 'short_desc_css');

            // Extract images
            $images = $this->extract_images($crawler, $converter, $website, 'image_css');

            // Extract more data fields as needed

            // Log the scraped data for debugging
            error_log('Scraped Title: ' . $title);
            error_log('Scraped Price: ' . $price);
            error_log('Scraped SKU: ' . $sku);
            error_log('Scraped Description: ' . $description);
            error_log('Scraped Short Description: ' . $short_description);
            error_log('Scraped Images: ' . implode(', ', $images));

            $scraped_data = [
                'title' => $title,
                'description' => $description,
                'short_description' => $short_description,
                'price' => $price,
                'sku' => $sku,
                'images' => $images,
                // Add more fields as needed
            ];

            return $scraped_data;
        }

        // Extract text data using CSS selector
        private function extract_data($crawler, $converter, $website, $css_key) {
            if ($website[$css_key] !== 'n/a') {
                $css = $converter->toXPath($website[$css_key]);
                $nodes = $crawler->filterXPath($css);
                return $nodes->count() > 0 ? $nodes->text() : '';
            } else {
                return '';
            }
        }

        // Extract image URLs using CSS selector
        private function extract_images($crawler, $converter, $website, $css_key) {
            if ($website[$css_key] !== 'n/a') {
                $css = $converter->toXPath($website[$css_key]);
                $imageNodes = $crawler->filterXPath($css);
                $images = [];
                foreach ($imageNodes as $node) {
                    $src = $node->getAttribute('src');
                    if ($src && !preg_match('/data:image/', $src)) {
                        $images[] = $src;
                    } else {
                        $amsrc = $node->getAttribute('data-amsrc');
                        if ($amsrc) {
                            $images[] = $amsrc;
                        }
                    }
                }
                return $images;
            } else {
                return [];
            }
        }
      
        private function create_product($data) {
            set_time_limit(300);
            // Check if WooCommerce is active
            if (!class_exists('WooCommerce')) {
                error_log('WooCommerce is not active');
                return;
            }

            // Create a new product object
            $product = new WC_Product();

            // Set product data
            $product->set_name($data['title']);
            $product->set_short_description($data['short_description']);
            $product->set_description($data['description']);
            $product->set_regular_price($data['price']);
            // Check if SKU is a duplicate
            $new_sku = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['sku']);
            $new_sku = $this->generate_unique_sku($new_sku);
            $product->set_sku($new_sku);
            $product->set_status('publish'); // Set the product status as published

            // Save the product and get its ID
            $product_id = $product->save();

            // Check if there is any image for the product
            if (count($data['images']) > 0) {
                // Get placeholder image ID
                $placeholder_image_id = get_option('bwgps_media_image_id', '');

                // Ensure the image URLs are absolute
                $data['images'] = array_map(function ($url) {
                    return strpos($url, '//') === 0 ? 'https:' . $url : $url;
                }, $data['images']);

                // Set the first image as the featured image
                $this->set_product_image($product_id, $data['images'][0], $placeholder_image_id, true, $data['title']);

                // If there are additional images, add them as gallery images
                if (count($data['images']) > 1) {
                    $gallery_image_ids = [];
                    for ($i = 1; $i < count($data['images']); $i++) {
                        $gallery_image_ids[] = $this->set_product_image($product_id, $data['images'][$i], $placeholder_image_id, false, $data['title']);
                    }
                    update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_image_ids));
                }
            }
        }
        private function set_product_image($product_id, $image_url, $placeholder_image_id, $is_featured = true, $title = '') {
            // Check if the image URL is valid
            if (filter_var($image_url, FILTER_VALIDATE_URL)) {
                // Set variables for storage, fix file filename for query strings.
                preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $image_url, $matches);
                if (!$matches) {
                    error_log('Invalid image URL');
                    return $placeholder_image_id;
                }

                $file_array = array();
                $file_array['name'] = basename($matches[0]);
                $file_array['tmp_name'] = download_url($image_url);

                // If error storing temporarily, return the error.
                if (is_wp_error($file_array['tmp_name'])) {
                    error_log("Error storing image temporarily: " . $file_array['tmp_name']->get_error_message());
                    return $placeholder_image_id;
                }

                // Do the validation and storage stuff.
                $media_image_id = media_handle_sideload($file_array, $product_id, $title);


                // If error storing permanently, unlink.
                if (is_wp_error($media_image_id)) {
                    @unlink($file_array['tmp_name']);
                    error_log("Error storing image permanently: " . $media_image_id->get_error_message());
                    return $placeholder_image_id;
                }

                // If the upload is successful, set the image as the product's featured image
                if ($is_featured) {
                    set_post_thumbnail($product_id, $media_image_id);
                }
                return $media_image_id;
            }

            // If the image URL is not valid or the upload fails, set the placeholder image as the product's featured image
            if ($is_featured) {
                set_post_thumbnail($product_id, $placeholder_image_id);
            }
            return $placeholder_image_id;
        }


       private function is_duplicate_sku($sku) {
            global $wpdb;
            $product_id = $wpdb->get_var($wpdb->prepare("
                SELECT posts.ID
                FROM $wpdb->posts as posts
                LEFT JOIN $wpdb->postmeta as postmeta ON posts.ID = postmeta.post_id
                WHERE posts.post_status IN ('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')
                AND postmeta.meta_key = '_sku'
                AND postmeta.meta_value = '%s'
                LIMIT 1
             ", $sku));

            return ($product_id) ? true : false;
        }

        private function generate_unique_sku($sku) {
            $count = 1;
            $unique_sku = $sku;

            while ($this->is_duplicate_sku($unique_sku)) {
                $unique_sku = $sku . '-' . $count++;
            }

            return $unique_sku;
        }


    public function run() {
        $bwgps_admin = new BWG_Product_Scraper_Admin($this->version);
        $bwgps_admin->handle_form_submission();
    }

}