<?php
namespace JelloPoint\AccommodationFacilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Categories_Page {

	public static function init() : void {
		add_action( 'admin_init', [ __CLASS__, 'handle_post' ] );
	}

	public static function handle_post() : void {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$page = isset( $_REQUEST['page'] ) ? sanitize_key( wp_unslash( $_REQUEST['page'] ) ) : '';
		if ( 'jpaf-icon-categories' !== $page ) {
			return;
		}
		$action = isset( $_REQUEST['jpaf_action'] ) ? sanitize_key( wp_unslash( $_REQUEST['jpaf_action'] ) ) : '';
		if ( 'delete' === $action ) {
			check_admin_referer( 'jpaf_delete_icon_category' );
			$id = isset( $_GET['id'] ) ? sanitize_title( wp_unslash( $_GET['id'] ) ) : '';
			if ( '' !== $id ) {
				Icon_Categories_Store::delete( $id );
				self::clear_facility_category_references( $id );
			}
			self::redirect_with_notice( 'deleted' );
		}
		if ( empty( $_POST ) || ! in_array( $action, [ 'add', 'update' ], true ) ) {
			return;
		}
		check_admin_referer( 'jpaf_save_icon_category' );
		$data = [
			'id'          => isset( $_POST['category_id'] ) ? wp_unslash( $_POST['category_id'] ) : '',
			'label'       => isset( $_POST['label'] ) ? wp_unslash( $_POST['label'] ) : '',
			'description' => isset( $_POST['description'] ) ? wp_unslash( $_POST['description'] ) : '',
			'sort_order'  => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : Icon_Categories_Store::next_sort_order(),
			'active'      => isset( $_POST['active'] ) ? 1 : 0,
		];
		if ( '' === trim( (string) $data['label'] ) ) {
			self::redirect_with_notice( 'missing_label' );
		}
		if ( 'update' === $action ) {
			$existing_id = isset( $_POST['existing_id'] ) ? sanitize_title( wp_unslash( $_POST['existing_id'] ) ) : '';
			Icon_Categories_Store::update( $existing_id, $data );
			self::redirect_with_notice( 'updated' );
		}
		Icon_Categories_Store::insert( $data );
		self::redirect_with_notice( 'added' );
	}

	private static function clear_facility_category_references( string $category_id ) : void {
		$items = Facilities_Store::get_all();
		foreach ( $items as &$item ) {
			if ( $category_id === (string) $item['category_id'] ) {
				$item['category_id'] = '';
			}
		}
		Facilities_Store::save_all( $items );
	}

	private static function redirect_with_notice( string $notice ) : void {
		wp_safe_redirect( add_query_arg( [ 'page' => 'jpaf-icon-categories', 'notice' => $notice ], admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function render_page() : void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$items     = Icon_Categories_Store::get_all();
		$edit_id   = isset( $_GET['action'], $_GET['id'] ) && 'edit' === sanitize_key( wp_unslash( $_GET['action'] ) ) ? sanitize_title( wp_unslash( $_GET['id'] ) ) : '';
		$edit_item = $edit_id ? Icon_Categories_Store::get_by_id( $edit_id ) : null;
		if ( ! $edit_item ) {
			$edit_item = [
				'id'          => '',
				'label'       => '',
				'description' => '',
				'sort_order'  => Icon_Categories_Store::next_sort_order(),
				'active'      => 1,
			];
		}
		$notice = isset( $_GET['notice'] ) ? sanitize_key( wp_unslash( $_GET['notice'] ) ) : '';
		require __DIR__ . '/views/categories-page.php';
	}
}
