<?php
namespace JelloPoint\AccommodationFacilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Groups_Page {

	public static function init() : void {
		add_action( 'admin_init', [ __CLASS__, 'handle_post' ] );
	}

	public static function handle_post() : void {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$page = isset( $_REQUEST['page'] ) ? sanitize_key( wp_unslash( $_REQUEST['page'] ) ) : '';
		if ( 'jpaf-facility-groups' !== $page ) {
			return;
		}
		$action = isset( $_REQUEST['jpaf_action'] ) ? sanitize_key( wp_unslash( $_REQUEST['jpaf_action'] ) ) : '';
		if ( 'delete' === $action ) {
			check_admin_referer( 'jpaf_delete_facility_group' );
			$id = isset( $_GET['id'] ) ? sanitize_title( wp_unslash( $_GET['id'] ) ) : '';
			if ( '' !== $id ) {
				Facility_Groups_Store::delete( $id );
			}
			self::redirect_with_notice( 'deleted' );
		}
		if ( empty( $_POST ) || ! in_array( $action, [ 'add', 'update' ], true ) ) {
			return;
		}
		check_admin_referer( 'jpaf_save_facility_group' );
		$data = [
			'id'           => isset( $_POST['group_id'] ) ? wp_unslash( $_POST['group_id'] ) : '',
			'label'        => isset( $_POST['label'] ) ? wp_unslash( $_POST['label'] ) : '',
			'description'  => isset( $_POST['description'] ) ? wp_unslash( $_POST['description'] ) : '',
			'facility_ids' => isset( $_POST['facility_ids'] ) ? (array) wp_unslash( $_POST['facility_ids'] ) : [],
			'sort_order'   => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : Facility_Groups_Store::next_sort_order(),
			'active'       => isset( $_POST['active'] ) ? 1 : 0,
		];
		if ( '' === trim( (string) $data['label'] ) ) {
			self::redirect_with_notice( 'missing_label' );
		}
		if ( 'update' === $action ) {
			$existing_id = isset( $_POST['existing_id'] ) ? sanitize_title( wp_unslash( $_POST['existing_id'] ) ) : '';
			Facility_Groups_Store::update( $existing_id, $data );
			self::redirect_with_notice( 'updated' );
		}
		Facility_Groups_Store::insert( $data );
		self::redirect_with_notice( 'added' );
	}

	private static function redirect_with_notice( string $notice ) : void {
		wp_safe_redirect( add_query_arg( [ 'page' => 'jpaf-facility-groups', 'notice' => $notice ], admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function render_page() : void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$items              = Facility_Groups_Store::get_all();
		$edit_id            = isset( $_GET['action'], $_GET['id'] ) && 'edit' === sanitize_key( wp_unslash( $_GET['action'] ) ) ? sanitize_title( wp_unslash( $_GET['id'] ) ) : '';
		$edit_item          = $edit_id ? Facility_Groups_Store::get_by_id( $edit_id ) : null;
		$facility_category  = isset( $_GET['facility_category_id'] ) ? sanitize_title( wp_unslash( $_GET['facility_category_id'] ) ) : '';
		$facilities         = Facilities_Store::get_active();
		if ( '' !== $facility_category ) {
			$facilities = array_values( array_filter( $facilities, static function ( array $facility ) use ( $facility_category ) : bool {
				return $facility_category === (string) $facility['category_id'];
			} ) );
		}
		if ( ! $edit_item ) {
			$edit_item = [
				'id'           => '',
				'label'        => '',
				'description'  => '',
				'facility_ids' => [],
				'sort_order'   => Facility_Groups_Store::next_sort_order(),
				'active'       => 1,
			];
		}
		$notice       = isset( $_GET['notice'] ) ? sanitize_key( wp_unslash( $_GET['notice'] ) ) : '';
		$categories   = Icon_Categories_Store::get_all();
		$facility_map = Facilities_Store::get_active();
		require __DIR__ . '/views/groups-page.php';
	}
}
