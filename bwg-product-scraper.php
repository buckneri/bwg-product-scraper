<?php
// /bwg-product-scraper.php
/*
Plugin Name: BWG Product Scraper
Description: Pulls product data from multiple websites.
Author: Your Name
Version: 1.0
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if (!function_exists('wc_get_product')) {
    require_once ABSPATH . 'wp-load.php'; // Load WordPress Core
    require_once ABSPATH . 'wp-admin/includes/plugin.php'; // Load the Plugin API

    activate_plugin('woocommerce/woocommerce.php'); // Activate WooCommerce Plugin

    if (function_exists('WC')) {
        WC(); // Initialize WooCommerce
    }
}

// Include the Composer autoloader
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Include the main class file
require_once plugin_dir_path( __FILE__ ) . 'includes/class-bwg-product-scraper.php';

function run_bwg_product_scraper() {
    $bwgps = new BWG_Product_Scraper();
    $bwgps->run();
}

run_bwg_product_scraper();

