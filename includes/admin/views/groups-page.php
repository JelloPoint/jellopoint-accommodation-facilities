<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$editing = ! empty( $edit_item['id'] );
?>
<div class="wrap jpaf-admin-wrap">
	<div class="jpaf-admin-header">
		<div>
			<h1><?php esc_html_e( 'Facility Groups', 'jellopoint-accommodation-facilities' ); ?></h1>
			<p><?php esc_html_e( 'Create reusable groups of facilities and call those groups directly from the Elementor widget.', 'jellopoint-accommodation-facilities' ); ?></p>
		</div>
	</div>

	<?php if ( 'added' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Facility group added.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'updated' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Facility group updated.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'deleted' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Facility group deleted.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'missing_label' === $notice ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Please enter a group name.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php endif; ?>

	<div class="jpaf-admin-grid jpaf-admin-grid--wide-left">
		<div class="jpaf-card jpaf-form-card<?php echo $editing ? ' is-editing' : ''; ?>">
			<div class="jpaf-card-header"><div><h2><?php echo $editing ? esc_html__( 'Edit Facility Group', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add Facility Group', 'jellopoint-accommodation-facilities' ); ?></h2></div></div>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facility-groups' ) ); ?>" class="jpaf-facility-form">
				<?php wp_nonce_field( 'jpaf_save_facility_group' ); ?>
				<input type="hidden" name="page" value="jpaf-facility-groups" />
				<input type="hidden" name="jpaf_action" value="<?php echo $editing ? 'update' : 'add'; ?>" />
				<input type="hidden" name="existing_id" value="<?php echo esc_attr( $edit_item['id'] ); ?>" />
				<input type="hidden" name="group_id" value="<?php echo esc_attr( $edit_item['id'] ); ?>" />
				<div class="jpaf-form-grid">
					<div class="jpaf-field jpaf-field--full"><label for="jpaf-group-label"><?php esc_html_e( 'Group name', 'jellopoint-accommodation-facilities' ); ?></label><input id="jpaf-group-label" name="label" type="text" value="<?php echo esc_attr( $edit_item['label'] ); ?>" required /></div>
					<div class="jpaf-field jpaf-field--full"><label for="jpaf-group-description"><?php esc_html_e( 'Description', 'jellopoint-accommodation-facilities' ); ?></label><textarea id="jpaf-group-description" name="description" rows="4"><?php echo esc_textarea( $edit_item['description'] ); ?></textarea></div>
					<div class="jpaf-field"><label for="jpaf-group-sort-order"><?php esc_html_e( 'Sort order', 'jellopoint-accommodation-facilities' ); ?></label><input id="jpaf-group-sort-order" name="sort_order" type="number" value="<?php echo esc_attr( (string) $edit_item['sort_order'] ); ?>" /></div>
					<div class="jpaf-field jpaf-field--full"><label class="jpaf-checkbox-label"><input name="active" type="checkbox" value="1" <?php checked( ! empty( $edit_item['active'] ) ); ?> /> <?php esc_html_e( 'Available in the widget', 'jellopoint-accommodation-facilities' ); ?></label></div>
					<div class="jpaf-field jpaf-field--full">
						<label for="jpaf-facility-category-filter"><?php esc_html_e( 'Filter facilities by icon category', 'jellopoint-accommodation-facilities' ); ?></label>
						<select id="jpaf-facility-category-filter" class="jpaf-facility-category-filter"><option value=""><?php esc_html_e( 'All icon categories', 'jellopoint-accommodation-facilities' ); ?></option><?php foreach ( $categories as $category ) : ?><option value="<?php echo esc_attr( $category['id'] ); ?>" <?php selected( $facility_category, $category['id'] ); ?>><?php echo esc_html( $category['label'] ); ?></option><?php endforeach; ?></select>
					</div>
					<div class="jpaf-field jpaf-field--full">
						<label for="jpaf-facility-search"><?php esc_html_e( 'Select facilities', 'jellopoint-accommodation-facilities' ); ?></label>

						<div class="jpaf-filter-bar">
							<input
								type="search"
								id="jpaf-facility-search"
								class="regular-text"
								placeholder="<?php esc_attr_e( 'Search facilities…', 'jellopoint-accommodation-facilities' ); ?>"
								autocomplete="off"
							/>
						</div>

						<div class="jpaf-checkbox-list" id="jpaf-facility-list">
							<?php if ( empty( $facility_map ) ) : ?>
								<p><?php esc_html_e( 'No facilities available yet.', 'jellopoint-accommodation-facilities' ); ?></p>
							<?php else : foreach ( $facility_map as $facility ) : $cat = isset( $facility['category_id'] ) ? (string) $facility['category_id'] : ''; $search = strtolower( trim( (string) $facility['label'] . ' ' . (string) $facility['id'] ) ); ?>
								<label class="jpaf-checkbox-item" data-category-id="<?php echo esc_attr( $cat ); ?>" data-search="<?php echo esc_attr( $search ); ?>"><input type="checkbox" name="facility_ids[]" value="<?php echo esc_attr( $facility['id'] ); ?>" <?php checked( in_array( $facility['id'], (array) $edit_item['facility_ids'], true ) ); ?> /> <span class="jpaf-checkbox-icon"><?php echo \JelloPoint\AccommodationFacilities\jpaf_get_icon_html( $facility ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span> <span><?php echo esc_html( $facility['label'] ); ?></span></label>
							<?php endforeach; endif; ?>
						</div>
						<p class="description"><?php esc_html_e( 'The order of the checked items is saved as the group order.', 'jellopoint-accommodation-facilities' ); ?></p>
					</div>
				</div>
				<div class="jpaf-form-actions"><button type="submit" class="button button-primary"><?php echo $editing ? esc_html__( 'Save changes', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add group', 'jellopoint-accommodation-facilities' ); ?></button><?php if ( $editing ) : ?><a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facility-groups' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Cancel editing', 'jellopoint-accommodation-facilities' ); ?></a><?php endif; ?></div>
			</form>
		</div>
		<div class="jpaf-card">
			<div class="jpaf-card-header"><div><h2><?php esc_html_e( 'Facility Groups', 'jellopoint-accommodation-facilities' ); ?></h2></div></div>
			<table class="widefat fixed striped jpaf-library-table">
				<thead><tr><th><?php esc_html_e( 'Name', 'jellopoint-accommodation-facilities' ); ?></th><th><?php esc_html_e( 'Description', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:120px;"><?php esc_html_e( 'Facilities', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:100px;"><?php esc_html_e( 'Order', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:90px;"><?php esc_html_e( 'Status', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:140px;"><?php esc_html_e( 'Actions', 'jellopoint-accommodation-facilities' ); ?></th></tr></thead>
				<tbody>
				<?php if ( empty( $items ) ) : ?>
					<tr><td colspan="6"><?php esc_html_e( 'No groups found yet.', 'jellopoint-accommodation-facilities' ); ?></td></tr>
				<?php else : foreach ( $items as $item ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $item['label'] ); ?></strong><div class="description"><?php echo esc_html( $item['id'] ); ?></div></td>
						<td><?php echo esc_html( $item['description'] ); ?></td>
						<td><?php echo esc_html( (string) count( (array) $item['facility_ids'] ) ); ?></td>
						<td><?php echo esc_html( (string) $item['sort_order'] ); ?></td>
						<td><?php echo ! empty( $item['active'] ) ? '<span class="jpaf-status jpaf-status--active">' . esc_html__( 'Active', 'jellopoint-accommodation-facilities' ) . '</span>' : '<span class="jpaf-status">' . esc_html__( 'Inactive', 'jellopoint-accommodation-facilities' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
						<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facility-groups&action=edit&id=' . rawurlencode( $item['id'] ) ) ); ?>"><?php esc_html_e( 'Edit', 'jellopoint-accommodation-facilities' ); ?></a> | <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=jpaf-facility-groups&jpaf_action=delete&id=' . rawurlencode( $item['id'] ) ), 'jpaf_delete_facility_group' ) ); ?>" class="jpaf-delete-link"><?php esc_html_e( 'Delete', 'jellopoint-accommodation-facilities' ); ?></a></td>
					</tr>
				<?php endforeach; endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var searchInput = document.getElementById('jpaf-facility-search');
	var list = document.getElementById('jpaf-facility-list');

	if (!searchInput || !list) {
		return;
	}

	var items = list.querySelectorAll('.jpaf-checkbox-item');

	searchInput.addEventListener('input', function () {
		var query = (searchInput.value || '').toLowerCase().trim();

		items.forEach(function (item) {
			var haystack = (item.getAttribute('data-search') || '').toLowerCase();
			item.style.display = (!query || haystack.indexOf(query) !== -1) ? '' : 'none';
		});
	});
});
</script>