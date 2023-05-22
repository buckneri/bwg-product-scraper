<?php
// /includes/class-bwg-product-scraper.php
class BWG_Product_Scraper {

    private $version;

    public function __construct() {
        $this->version = '1.0';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bwg-product-scraper-admin.php';
    }

    public function run() {
        $bwgps_admin = new BWG_Product_Scraper_Admin( $this->version );
    }
}
