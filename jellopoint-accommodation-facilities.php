<?php
/**
 * Plugin Name: JelloPoint - Accommodation Facilities
 * Description: Central facilities library with Elementor widget for accommodation websites.
 * Version: 1.2.0
 * Author: JelloPoint
 * Text Domain: jellopoint-accommodation-facilities
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JPAF_VERSION', '1.2.0' );
define( 'JPAF_PLUGIN_FILE', __FILE__ );
define( 'JPAF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JPAF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JPAF_TEXT_DOMAIN', 'jellopoint-accommodation-facilities' );

require_once JPAF_PLUGIN_DIR . 'includes/class-plugin.php';

\JelloPoint\AccommodationFacilities\Plugin::init();
