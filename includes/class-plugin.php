<?php
namespace JelloPoint\AccommodationFacilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/data/class-facilities-store.php';
require_once __DIR__ . '/data/class-icon-categories-store.php';
require_once __DIR__ . '/data/class-facility-groups-store.php';
require_once __DIR__ . '/helpers/icons.php';
require_once __DIR__ . '/render/class-facilities-renderer.php';
require_once __DIR__ . '/admin/class-admin-menu.php';
require_once __DIR__ . '/admin/class-admin-facilities-page.php';
require_once __DIR__ . '/admin/class-admin-categories-page.php';
require_once __DIR__ . '/admin/class-admin-groups-page.php';

class Plugin {

	private static $bootstrapped = false;

	public static function init() : void {
		if ( self::$bootstrapped ) {
			return;
		}

		self::$bootstrapped = true;

		add_action( 'plugins_loaded', [ __CLASS__, 'load_textdomain' ] );
		add_action( 'init', [ __CLASS__, 'register_assets' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_frontend_assets' ] );

		if ( is_admin() ) {
			Admin_Menu::init();
			Admin_Facilities_Page::init();
			Admin_Categories_Page::init();
			Admin_Groups_Page::init();
		}

		add_action( 'elementor/elements/categories_registered', [ __CLASS__, 'register_elementor_category' ] );
		add_action( 'elementor/widgets/register', [ __CLASS__, 'register_elementor_widget' ] );
	}

	public static function load_textdomain() : void {
		load_plugin_textdomain( JPAF_TEXT_DOMAIN, false, dirname( plugin_basename( JPAF_PLUGIN_FILE ) ) . '/languages' );
	}

	public static function register_assets() : void {
		wp_register_style(
			'jpaf-facilities',
			JPAF_PLUGIN_URL . 'assets/css/facilities.css',
			[],
			JPAF_VERSION
		);
	}

	public static function enqueue_frontend_assets() : void {
		if ( ! wp_style_is( 'jpaf-facilities', 'registered' ) ) {
			self::register_assets();
		}

		// Important: Dashicons are not reliably available on the frontend unless explicitly enqueued.
		wp_enqueue_style( 'dashicons' );
	}

	public static function register_elementor_category( $elements_manager ) : void {
		if ( ! is_object( $elements_manager ) || ! method_exists( $elements_manager, 'add_category' ) ) {
			return;
		}

		$elements_manager->add_category(
			'jellopoint-widgets',
			[
				'title' => __( 'JelloPoint', 'jellopoint-accommodation-facilities' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}

	public static function register_elementor_widget( $widgets_manager ) : void {
		if ( ! class_exists( '\\Elementor\\Widget_Base' ) ) {
			return;
		}

		require_once __DIR__ . '/widgets/class-facilities-widget.php';

		if ( class_exists( '\\JelloPoint\\AccommodationFacilities\\Widgets\\Facilities_Widget' ) ) {
			$widgets_manager->register( new \JelloPoint\AccommodationFacilities\Widgets\Facilities_Widget() );
		}
	}
}