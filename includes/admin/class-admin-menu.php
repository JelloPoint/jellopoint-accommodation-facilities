<?php
namespace JelloPoint\AccommodationFacilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Menu {

	public static function init() : void {
		add_action( 'admin_menu', [ __CLASS__, 'register_menu' ], 30 );
	}

	public static function register_menu() : void {
		$parent_slug = self::get_parent_slug();
		$created_top = false;

		if ( ! $parent_slug ) {
			$parent_slug = 'jpaf-facilities';
			$created_top = true;
			add_menu_page(
				__( 'Accommodation Facilities', 'jellopoint-accommodation-facilities' ),
				__( 'JelloPoint Facilities', 'jellopoint-accommodation-facilities' ),
				'manage_options',
				$parent_slug,
				[ '\\JelloPoint\\AccommodationFacilities\\Admin_Facilities_Page', 'render_page' ],
				'dashicons-screenoptions',
				58
			);
		}

		if ( ! $created_top ) {
			add_submenu_page(
				$parent_slug,
				__( 'Accommodation Facilities', 'jellopoint-accommodation-facilities' ),
				__( 'Facilities', 'jellopoint-accommodation-facilities' ),
				'manage_options',
				'jpaf-facilities',
				[ '\\JelloPoint\\AccommodationFacilities\\Admin_Facilities_Page', 'render_page' ]
			);
		}

		add_submenu_page(
			$parent_slug,
			__( 'Icon Categories', 'jellopoint-accommodation-facilities' ),
			__( 'Icon Categories', 'jellopoint-accommodation-facilities' ),
			'manage_options',
			'jpaf-icon-categories',
			[ '\\JelloPoint\\AccommodationFacilities\\Admin_Categories_Page', 'render_page' ]
		);

		add_submenu_page(
			$parent_slug,
			__( 'Facility Groups', 'jellopoint-accommodation-facilities' ),
			__( 'Facility Groups', 'jellopoint-accommodation-facilities' ),
			'manage_options',
			'jpaf-facility-groups',
			[ '\\JelloPoint\\AccommodationFacilities\\Admin_Groups_Page', 'render_page' ]
		);
	}

	private static function get_parent_slug() : string {
		global $menu;
		if ( ! is_array( $menu ) ) {
			return '';
		}
		foreach ( $menu as $menu_item ) {
			if ( empty( $menu_item[2] ) || empty( $menu_item[0] ) ) {
				continue;
			}
			$title = wp_strip_all_tags( (string) $menu_item[0] );
			if ( false !== stripos( $title, 'JelloPoint' ) ) {
				return (string) $menu_item[2];
			}
		}
		return '';
	}
}
