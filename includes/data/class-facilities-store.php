<?php
namespace JelloPoint\AccommodationFacilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Facilities_Store {

	private const OPTION_KEY = 'jpaf_facilities';

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
			static function( array $a, array $b ) : int {
				if ( (int) $a['sort_order'] !== (int) $b['sort_order'] ) {
					return (int) $a['sort_order'] <=> (int) $b['sort_order'];
				}
				return strcasecmp( (string) $a['label'], (string) $b['label'] );
			}
		);

		return array_values( $normalized );
	}

	public static function get_active() : array {
		return array_values( array_filter( self::get_all(), static function( array $item ) : bool {
			return ! empty( $item['active'] );
		} ) );
	}

	public static function get_by_id( string $id ) : ?array {
		$id = sanitize_title( $id );
		if ( '' === $id ) {
			return null;
		}

		foreach ( self::get_all() as $item ) {
			if ( $item['id'] === $id ) {
				return $item;
			}
		}

		return null;
	}

	public static function get_by_ids( array $ids, string $order_mode = 'selection' ) : array {
		$ids = array_values( array_filter( array_map( 'sanitize_title', $ids ) ) );
		if ( empty( $ids ) ) {
			return [];
		}

		$all = [];
		foreach ( self::get_all() as $item ) {
			$all[ $item['id'] ] = $item;
		}

		$selected = [];
		foreach ( $ids as $id ) {
			if ( isset( $all[ $id ] ) && ! empty( $all[ $id ]['active'] ) ) {
				$selected[] = $all[ $id ];
			}
		}

		if ( 'alphabetical' === $order_mode ) {
			usort( $selected, static function( array $a, array $b ) : int {
				return strcasecmp( (string) $a['label'], (string) $b['label'] );
			} );
		} elseif ( 'admin' === $order_mode ) {
			usort( $selected, static function( array $a, array $b ) : int {
				if ( (int) $a['sort_order'] !== (int) $b['sort_order'] ) {
					return (int) $a['sort_order'] <=> (int) $b['sort_order'];
				}
				return strcasecmp( (string) $a['label'], (string) $b['label'] );
			} );
		}

		return $selected;
	}

	public static function get_by_category_id( string $category_id, bool $active_only = false ) : array {
		$category_id = sanitize_title( $category_id );
		$items       = $active_only ? self::get_active() : self::get_all();
		if ( '' === $category_id ) {
			return $items;
		}
		return array_values( array_filter( $items, static function ( array $item ) use ( $category_id ) : bool {
			return $category_id === (string) $item['category_id'];
		} ) );
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

			$item['id']        = $id;
			$items[ $index ]   = self::normalize_item( $item );
			$found             = true;
			break;
		}

		if ( ! $found ) {
			return null;
		}

		self::save_all( $items );
		return self::get_by_id( $id );
	}

	public static function delete( string $id ) : bool {
		$id    = sanitize_title( $id );
		$items = self::get_all();

		$items = array_values( array_filter( $items, static function( array $item ) use ( $id ) : bool {
			return $item['id'] !== $id;
		} ) );

		return self::save_all( $items );
	}

	public static function save_sort_order( array $ids ) : bool {
		if ( empty( $ids ) ) {
			return false;
		}

		$lookup = [];
		foreach ( self::get_all() as $item ) {
			$lookup[ $item['id'] ] = $item;
		}

		$sorted = [];
		$order  = 10;
		foreach ( $ids as $id ) {
			$id = sanitize_title( $id );
			if ( '' === $id || ! isset( $lookup[ $id ] ) ) {
				continue;
			}

			$item               = $lookup[ $id ];
			$item['sort_order'] = $order;
			$sorted[]           = $item;
			unset( $lookup[ $id ] );
			$order += 10;
		}

		if ( ! empty( $lookup ) ) {
			foreach ( $lookup as $item ) {
				$item['sort_order'] = $order;
				$sorted[]           = $item;
				$order += 10;
			}
		}

		return self::save_all( $sorted );
	}

	public static function next_sort_order() : int {
		$items = self::get_all();
		if ( empty( $items ) ) {
			return 10;
		}
		return max( array_map( static function( array $item ) : int {
			return (int) $item['sort_order'];
		}, $items ) ) + 10;
	}

	public static function normalize_item( array $item ) : array {
		$item = self::sanitize_item( $item );
		return wp_parse_args(
			$item,
			[
				'id'                 => '',
				'label'              => '',
				'description'        => '',
				'icon_type'          => 'dashicon',
				'icon_value'         => 'dashicons-admin-site',
				'icon_attachment_id' => 0,
				'category_id'        => '',
				'sort_order'         => 10,
				'active'             => 1,
			]
		);
	}

	public static function sanitize_item( array $item ) : array {
		$label = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		$id    = isset( $item['id'] ) ? sanitize_title( $item['id'] ) : '';
		if ( '' === $id && '' !== $label ) {
			$id = sanitize_title( $label );
		}

		$icon_type = isset( $item['icon_type'] ) ? sanitize_key( $item['icon_type'] ) : 'dashicon';
		if ( ! in_array( $icon_type, [ 'dashicon', 'custom_class', 'svg' ], true ) ) {
			$icon_type = 'dashicon';
		}

		$icon_value = isset( $item['icon_value'] ) ? wp_unslash( (string) $item['icon_value'] ) : '';
		$icon_value = 'svg' === $icon_type ? esc_url_raw( $icon_value ) : sanitize_text_field( $icon_value );
		if ( 'dashicon' === $icon_type && 0 !== strpos( $icon_value, 'dashicons-' ) ) {
			$icon_value = 'dashicons-admin-site';
		}

		$category_id = isset( $item['category_id'] ) ? sanitize_title( $item['category_id'] ) : '';
		if ( '' !== $category_id && ! Icon_Categories_Store::get_by_id( $category_id ) ) {
			$category_id = '';
		}

		return [
			'id'                 => $id,
			'label'              => $label,
			'description'        => isset( $item['description'] ) ? sanitize_textarea_field( $item['description'] ) : '',
			'icon_type'          => $icon_type,
			'icon_value'         => $icon_value,
			'icon_attachment_id' => isset( $item['icon_attachment_id'] ) ? (int) $item['icon_attachment_id'] : 0,
			'category_id'        => $category_id,
			'sort_order'         => isset( $item['sort_order'] ) ? (int) $item['sort_order'] : 10,
			'active'             => ! empty( $item['active'] ) ? 1 : 0,
		];
	}

	public static function generate_unique_id( string $label, array $existing_ids = [] ) : string {
		$base = sanitize_title( $label );
		if ( '' === $base ) {
			$base = 'facility';
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

	public static function seed_defaults() : void {
		$items = get_option( self::OPTION_KEY, null );
		if ( is_array( $items ) && ! empty( $items ) ) {
			return;
		}
		self::save_all(
			[
				[
					'label'      => 'Free WiFi',
					'icon_type'  => 'dashicon',
					'icon_value' => 'dashicons-wifi',
					'sort_order' => 10,
					'active'     => 1,
				],
				[
					'label'      => 'Swimming Pool',
					'icon_type'  => 'dashicon',
					'icon_value' => 'dashicons-admin-site-alt3',
					'sort_order' => 20,
					'active'     => 1,
				],
				[
					'label'      => 'Parking',
					'icon_type'  => 'dashicon',
					'icon_value' => 'dashicons-car',
					'sort_order' => 30,
					'active'     => 1,
				],
				[
					'label'      => 'Pets Allowed',
					'icon_type'  => 'dashicon',
					'icon_value' => 'dashicons-pets',
					'sort_order' => 40,
					'active'     => 1,
				],
			]
		);
	}
}
