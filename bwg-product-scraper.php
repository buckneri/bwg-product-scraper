<?php
// /bwg-product-scraper.php
/*
Plugin Name: BWG Product Scraper
Description: Pulls product data from multiple websites.
Author: Your Name
Version: 1.0
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Include the main class file
require_once plugin_dir_path( __FILE__ ) . 'includes/class-bwg-product-scraper.php';

function run_bwg_product_scraper() {
    $bwgps = new BWG_Product_Scraper();
    $bwgps->run();
}

run_bwg_product_scraper();
