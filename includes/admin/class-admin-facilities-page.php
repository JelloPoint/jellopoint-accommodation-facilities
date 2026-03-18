<?php
namespace JelloPoint\AccommodationFacilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Facilities_Page {

	public static function init() : void {
		add_action( 'admin_init', [ __CLASS__, 'maybe_seed_defaults' ] );
		add_action( 'admin_init', [ __CLASS__, 'handle_post' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
		add_filter( 'upload_mimes', [ __CLASS__, 'allow_svg_uploads' ] );
	}

	public static function maybe_seed_defaults() : void {
		if ( get_option( Facilities_Store::get_option_key(), null ) === null ) {
			Facilities_Store::seed_defaults();
		}
	}

	public static function allow_svg_uploads( array $mimes ) : array {
		if ( current_user_can( 'manage_options' ) ) {
			$mimes['svg']  = 'image/svg+xml';
			$mimes['svgz'] = 'image/svg+xml';
		}
		return $mimes;
	}

	public static function enqueue_assets( string $hook ) : void {
		if ( false === strpos( $hook, 'jpaf-' ) ) {
			return;
		}
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'jpaf-admin', JPAF_PLUGIN_URL . 'assets/admin/facilities-admin.css', [], JPAF_VERSION );
		wp_enqueue_script( 'jpaf-admin', JPAF_PLUGIN_URL . 'assets/admin/facilities-admin.js', [ 'jquery', 'jquery-ui-sortable' ], JPAF_VERSION, true );
		wp_localize_script(
			'jpaf-admin',
			'jpafAdmin',
			[
				'deleteConfirm' => __( 'Delete this item?', 'jellopoint-accommodation-facilities' ),
				'chooseSvg'     => __( 'Choose SVG icon', 'jellopoint-accommodation-facilities' ),
				'useThisSvg'    => __( 'Use this SVG', 'jellopoint-accommodation-facilities' ),
			]
		);
	}

	public static function handle_post() : void {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$page = isset( $_REQUEST['page'] ) ? sanitize_key( wp_unslash( $_REQUEST['page'] ) ) : '';
		if ( 'jpaf-facilities' !== $page ) {
			return;
		}

		$action = isset( $_REQUEST['jpaf_action'] ) ? sanitize_key( wp_unslash( $_REQUEST['jpaf_action'] ) ) : '';
		if ( 'delete' === $action ) {
			check_admin_referer( 'jpaf_delete_facility' );
			$id = isset( $_GET['id'] ) ? sanitize_title( wp_unslash( $_GET['id'] ) ) : '';
			if ( '' !== $id ) {
				Facilities_Store::delete( $id );
			}
			self::redirect_with_notice( 'deleted' );
		}

		if ( 'sort' === $action && ! empty( $_POST ) ) {
			check_admin_referer( 'jpaf_sort_facilities' );
			$order = isset( $_POST['sorted_ids'] ) ? (string) wp_unslash( $_POST['sorted_ids'] ) : '';
			$ids   = array_values( array_filter( array_map( 'sanitize_title', explode( ',', $order ) ) ) );
			if ( ! empty( $ids ) ) {
				Facilities_Store::save_sort_order( $ids );
				self::redirect_with_notice( 'sorted' );
			}
			self::redirect_with_notice( 'sort_failed' );
		}

		if ( empty( $_POST ) || ! in_array( $action, [ 'add', 'update' ], true ) ) {
			return;
		}

		check_admin_referer( 'jpaf_save_facility' );

		$data = [
			'id'                 => isset( $_POST['facility_id'] ) ? wp_unslash( $_POST['facility_id'] ) : '',
			'label'              => isset( $_POST['label'] ) ? wp_unslash( $_POST['label'] ) : '',
			'description'        => isset( $_POST['description'] ) ? wp_unslash( $_POST['description'] ) : '',
			'icon_type'          => isset( $_POST['icon_type'] ) ? wp_unslash( $_POST['icon_type'] ) : 'dashicon',
			'icon_value'         => isset( $_POST['icon_value'] ) ? wp_unslash( $_POST['icon_value'] ) : 'dashicons-admin-site',
			'icon_attachment_id' => isset( $_POST['icon_attachment_id'] ) ? (int) $_POST['icon_attachment_id'] : 0,
			'category_id'        => isset( $_POST['category_id'] ) ? wp_unslash( $_POST['category_id'] ) : '',
			'sort_order'         => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : Facilities_Store::next_sort_order(),
			'active'             => isset( $_POST['active'] ) ? 1 : 0,
		];

		if ( '' === trim( (string) $data['label'] ) ) {
			self::redirect_with_notice( 'missing_label' );
		}

		if ( 'update' === $action ) {
			$existing_id = isset( $_POST['existing_id'] ) ? sanitize_title( wp_unslash( $_POST['existing_id'] ) ) : '';
			Facilities_Store::update( $existing_id, $data );
			self::redirect_with_notice( 'updated' );
		}

		Facilities_Store::insert( $data );
		self::redirect_with_notice( 'added' );
	}

	private static function redirect_with_notice( string $notice ) : void {
		wp_safe_redirect( add_query_arg( [ 'page' => 'jpaf-facilities', 'notice' => $notice ], admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function render_page() : void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$search      = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$category_id = isset( $_GET['category_id'] ) ? sanitize_title( wp_unslash( $_GET['category_id'] ) ) : '';
		$items       = Facilities_Store::get_all();
		if ( '' !== $category_id ) {
			$items = array_values( array_filter( $items, static function ( array $item ) use ( $category_id ) : bool {
				return $category_id === (string) $item['category_id'];
			} ) );
		}
		if ( '' !== $search ) {
			$needle = function_exists( 'mb_strtolower' ) ? mb_strtolower( $search ) : strtolower( $search );
			$items  = array_values( array_filter( $items, static function ( array $item ) use ( $needle ) : bool {
				$haystack = trim( $item['label'] . ' ' . $item['description'] . ' ' . $item['id'] );
				$haystack = function_exists( 'mb_strtolower' ) ? mb_strtolower( $haystack ) : strtolower( $haystack );
				return false !== strpos( $haystack, $needle );
			} ) );
		}
		$edit_id   = isset( $_GET['action'], $_GET['id'] ) && 'edit' === sanitize_key( wp_unslash( $_GET['action'] ) ) ? sanitize_title( wp_unslash( $_GET['id'] ) ) : '';
		$edit_item = $edit_id ? Facilities_Store::get_by_id( $edit_id ) : null;
		if ( ! $edit_item ) {
			$edit_item = [
				'id'                 => '',
				'label'              => '',
				'description'        => '',
				'icon_type'          => 'dashicon',
				'icon_value'         => 'dashicons-admin-site',
				'icon_attachment_id' => 0,
				'category_id'        => '',
				'sort_order'         => Facilities_Store::next_sort_order(),
				'active'             => 1,
			];
		}
		$notice        = isset( $_GET['notice'] ) ? sanitize_key( wp_unslash( $_GET['notice'] ) ) : '';
		$categories    = Icon_Categories_Store::get_all();
		$category_map  = Icon_Categories_Store::get_label_map();
		require __DIR__ . '/views/facilities-page.php';
	}
}
