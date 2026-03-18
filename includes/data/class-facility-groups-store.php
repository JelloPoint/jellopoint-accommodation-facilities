<?php
namespace JelloPoint\AccommodationFacilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Facility_Groups_Store {

	private const OPTION_KEY = 'jpaf_facility_groups';

	public static function get_option_key() : string {
		return self::OPTION_KEY;
	}

	public static function get_all() : array {
		$items = get_option( self::OPTION_KEY, [] );
		if ( ! is_array( $items ) ) {
			$items = [];
		}
		$normalized = [];
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$normalized[] = self::normalize_item( $item );
		}
		usort(
			$normalized,
			static function ( array $a, array $b ) : int {
				if ( (int) $a['sort_order'] !== (int) $b['sort_order'] ) {
					return (int) $a['sort_order'] <=> (int) $b['sort_order'];
				}
				return strcasecmp( (string) $a['label'], (string) $b['label'] );
			}
		);
		return array_values( $normalized );
	}

	public static function get_active() : array {
		return array_values( array_filter( self::get_all(), static function ( array $item ) : bool {
			return ! empty( $item['active'] );
		} ) );
	}

	public static function get_by_id( string $id ) : ?array {
		$id = sanitize_title( $id );
		foreach ( self::get_all() as $item ) {
			if ( $item['id'] === $id ) {
				return $item;
			}
		}
		return null;
	}

	public static function get_label_map( bool $active_only = false ) : array {
		$items = $active_only ? self::get_active() : self::get_all();
		$map   = [];
		foreach ( $items as $item ) {
			$map[ $item['id'] ] = $item['label'];
		}
		return $map;
	}

	public static function save_all( array $items ) : bool {
		$clean = [];
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$normalized = self::normalize_item( $item );
			if ( '' !== $normalized['id'] ) {
				$clean[ $normalized['id'] ] = $normalized;
			}
		}
		return update_option( self::OPTION_KEY, array_values( $clean ), false );
	}

	public static function insert( array $item ) : array {
		$items      = self::get_all();
		$normalized = self::normalize_item( $item );
		if ( '' === $normalized['id'] ) {
			$normalized['id'] = self::generate_unique_id( $normalized['label'], wp_list_pluck( $items, 'id' ) );
		}
		$items[] = $normalized;
		self::save_all( $items );
		return $normalized;
	}

	public static function update( string $id, array $item ) : ?array {
		$id    = sanitize_title( $id );
		$items = self::get_all();
		$found = false;
		foreach ( $items as $index => $existing ) {
			if ( $existing['id'] !== $id ) {
				continue;
			}
			$item['id']      = $id;
			$items[ $index ] = self::normalize_item( $item );
			$found           = true;
			break;
		}
		if ( ! $found ) {
			return null;
		}
		self::save_all( $items );
		return self::get_by_id( $id );
	}

	public static function delete( string $id ) : bool {
		$id = sanitize_title( $id );
		$items = array_values( array_filter( self::get_all(), static function ( array $item ) use ( $id ) : bool {
			return $item['id'] !== $id;
		} ) );
		return self::save_all( $items );
	}

	public static function next_sort_order() : int {
		$items = self::get_all();
		if ( empty( $items ) ) {
			return 10;
		}
		return max( array_map( static function ( array $item ) : int {
			return (int) $item['sort_order'];
		}, $items ) ) + 10;
	}

	public static function normalize_item( array $item ) : array {
		$item = self::sanitize_item( $item );
		return wp_parse_args(
			$item,
			[
				'id'           => '',
				'label'        => '',
				'description'  => '',
				'facility_ids' => [],
				'sort_order'   => 10,
				'active'       => 1,
			]
		);
	}

	public static function sanitize_item( array $item ) : array {
		$label = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$id    = isset( $item['id'] ) ? sanitize_title( $item['id'] ) : '';
		if ( '' === $id && '' !== $label ) {
			$id = sanitize_title( $label );
		}
		$facility_ids = isset( $item['facility_ids'] ) ? (array) $item['facility_ids'] : [];
		$facility_ids = array_values( array_filter( array_map( 'sanitize_title', $facility_ids ) ) );
		return [
			'id'           => $id,
			'label'        => $label,
			'description'  => isset( $item['description'] ) ? sanitize_textarea_field( $item['description'] ) : '',
			'facility_ids' => $facility_ids,
			'sort_order'   => isset( $item['sort_order'] ) ? (int) $item['sort_order'] : 10,
			'active'       => ! empty( $item['active'] ) ? 1 : 0,
		];
	}

	public static function generate_unique_id( string $label, array $existing_ids = [] ) : string {
		$base = sanitize_title( $label );
		if ( '' === $base ) {
			$base = 'facility-group';
		}
		if ( ! in_array( $base, $existing_ids, true ) ) {
			return $base;
		}
		$i = 2;
		while ( in_array( $base . '-' . $i, $existing_ids, true ) ) {
			$i++;
		}
		return $base . '-' . $i;
	}

	public static function get_facility_ids( string $group_id ) : array {
		$group = self::get_by_id( $group_id );
		return $group ? (array) $group['facility_ids'] : [];
	}
}
