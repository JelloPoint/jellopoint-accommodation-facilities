<?php
namespace JelloPoint\AccommodationFacilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Facilities_Renderer {

	public static function render( array $settings ) : string {
		$layout = isset( $settings['layout'] ) ? sanitize_key( $settings['layout'] ) : 'grid';
		if ( ! in_array( $layout, [ 'grid', 'list' ], true ) ) {
			$layout = 'grid';
		}

		$facilities = self::resolve_facilities( $settings );
		if ( empty( $facilities ) ) {
			return '';
		}

		$classes   = self::get_wrapper_classes( $settings, $layout );
		$template  = __DIR__ . '/templates/' . $layout . '.php';
		$show_desc = ! empty( $settings['show_description'] ) && 'yes' === $settings['show_description'];

		ob_start();
		include $template;
		return (string) ob_get_clean();
	}

	private static function resolve_facilities( array $settings ) : array {
		$source_type = isset( $settings['source_type'] ) ? sanitize_key( $settings['source_type'] ) : 'manual';
		if ( 'group' === $source_type ) {
			$group_id = isset( $settings['facility_group'] ) ? sanitize_title( $settings['facility_group'] ) : '';
			if ( '' === $group_id ) {
				return [];
			}
			return Facilities_Store::get_by_ids( Facility_Groups_Store::get_facility_ids( $group_id ), 'selection' );
		}
		$selected_ids = isset( $settings['facilities'] ) && is_array( $settings['facilities'] ) ? $settings['facilities'] : [];
		$order_mode   = isset( $settings['order_mode'] ) ? sanitize_key( $settings['order_mode'] ) : 'selection';
		return Facilities_Store::get_by_ids( $selected_ids, $order_mode );
	}

	private static function get_wrapper_classes( array $settings, string $layout ) : string {
		$columns       = isset( $settings['columns'] ) ? (int) $settings['columns'] : 3;
		$icon_position = isset( $settings['icon_position'] ) ? sanitize_key( $settings['icon_position'] ) : 'left';
		if ( $columns < 1 || $columns > 4 ) {
			$columns = 3;
		}
		if ( ! in_array( $icon_position, [ 'left', 'top' ], true ) ) {
			$icon_position = 'left';
		}
		return implode( ' ', [ 'jpaf-facilities', 'jpaf-layout-' . $layout, 'jpaf-columns-' . $columns, 'jpaf-icon-' . $icon_position ] );
	}
}
