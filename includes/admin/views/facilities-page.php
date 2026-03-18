<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$editing = ! empty( $edit_item['id'] );
?>
<div class="wrap jpaf-admin-wrap">
	<div class="jpaf-admin-header">
		<div>
			<h1><?php esc_html_e( 'JelloPoint Accommodation Facilities', 'jellopoint-accommodation-facilities' ); ?></h1>
			<p><?php esc_html_e( 'Manage your central facilities library here and reuse it in the Elementor widget.', 'jellopoint-accommodation-facilities' ); ?></p>
		</div>
		<?php if ( $editing ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facilities' ) ); ?>" class="button button-secondary jpaf-add-new-top"><?php esc_html_e( 'Add new facility', 'jellopoint-accommodation-facilities' ); ?></a>
		<?php endif; ?>
	</div>

	<?php if ( 'added' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Facility added.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'updated' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Facility updated.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'deleted' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Facility deleted.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'sorted' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Facility order saved.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'sort_failed' === $notice ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Please drag at least one facility before saving the new order.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'missing_label' === $notice ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Please enter a facility name.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php endif; ?>

	<div class="jpaf-admin-grid">
		<div class="jpaf-card jpaf-form-card<?php echo $editing ? ' is-editing' : ''; ?>" id="jpaf-form-card">
			<div class="jpaf-card-header">
				<div>
					<h2><?php echo $editing ? esc_html__( 'Edit Facility', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add Facility', 'jellopoint-accommodation-facilities' ); ?></h2>
					<p class="description"><?php echo $editing ? esc_html__( 'You are editing an existing facility. Save when you are done or cancel to create a new one.', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Create and manage reusable facility items for your accommodation websites.', 'jellopoint-accommodation-facilities' ); ?></p>
				</div>
				<?php if ( $editing ) : ?><span class="jpaf-edit-badge"><?php echo esc_html( $edit_item['label'] ); ?></span><?php endif; ?>
			</div>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facilities' ) ); ?>" class="jpaf-facility-form">
				<?php wp_nonce_field( 'jpaf_save_facility' ); ?>
				<input type="hidden" name="page" value="jpaf-facilities" />
				<input type="hidden" name="jpaf_action" value="<?php echo $editing ? 'update' : 'add'; ?>" />
				<input type="hidden" name="existing_id" value="<?php echo esc_attr( $edit_item['id'] ); ?>" />
				<input type="hidden" name="facility_id" value="<?php echo esc_attr( $edit_item['id'] ); ?>" />
				<input type="hidden" name="icon_attachment_id" id="jpaf-icon-attachment-id" value="<?php echo esc_attr( (string) ( $edit_item['icon_attachment_id'] ?? 0 ) ); ?>" />
				<div class="jpaf-form-grid">
					<div class="jpaf-field jpaf-field--full"><label for="jpaf-label"><?php esc_html_e( 'Facility name', 'jellopoint-accommodation-facilities' ); ?></label><input name="label" id="jpaf-label" type="text" value="<?php echo esc_attr( $edit_item['label'] ); ?>" required /></div>
					<div class="jpaf-field jpaf-field--full"><label for="jpaf-description"><?php esc_html_e( 'Description', 'jellopoint-accommodation-facilities' ); ?></label><textarea name="description" id="jpaf-description" rows="4"><?php echo esc_textarea( $edit_item['description'] ); ?></textarea></div>
					<div class="jpaf-field"><label for="jpaf-icon-type"><?php esc_html_e( 'Icon type', 'jellopoint-accommodation-facilities' ); ?></label><select name="icon_type" id="jpaf-icon-type"><option value="dashicon" <?php selected( $edit_item['icon_type'], 'dashicon' ); ?>><?php esc_html_e( 'Dashicon class', 'jellopoint-accommodation-facilities' ); ?></option><option value="custom_class" <?php selected( $edit_item['icon_type'], 'custom_class' ); ?>><?php esc_html_e( 'Custom icon class', 'jellopoint-accommodation-facilities' ); ?></option><option value="svg" <?php selected( $edit_item['icon_type'], 'svg' ); ?>><?php esc_html_e( 'Uploaded SVG', 'jellopoint-accommodation-facilities' ); ?></option></select></div>
					<div class="jpaf-field"><label for="jpaf-category-id"><?php esc_html_e( 'Icon category', 'jellopoint-accommodation-facilities' ); ?></label><select name="category_id" id="jpaf-category-id"><option value=""><?php esc_html_e( 'None', 'jellopoint-accommodation-facilities' ); ?></option><?php foreach ( $categories as $category ) : ?><option value="<?php echo esc_attr( $category['id'] ); ?>" <?php selected( $edit_item['category_id'], $category['id'] ); ?>><?php echo esc_html( $category['label'] ); ?></option><?php endforeach; ?></select><p class="description"><?php esc_html_e( 'Admin-only organization for large icon libraries.', 'jellopoint-accommodation-facilities' ); ?></p></div>
					<div class="jpaf-field"><label for="jpaf-sort-order"><?php esc_html_e( 'Sort order', 'jellopoint-accommodation-facilities' ); ?></label><input name="sort_order" id="jpaf-sort-order" type="number" value="<?php echo esc_attr( (string) $edit_item['sort_order'] ); ?>" /></div>
					<div class="jpaf-field jpaf-field--full jpaf-icon-value-wrap"><label for="jpaf-icon-value"><?php esc_html_e( 'Icon value', 'jellopoint-accommodation-facilities' ); ?></label><div class="jpaf-icon-input-row"><input name="icon_value" id="jpaf-icon-value" type="text" class="jpaf-icon-input" value="<?php echo esc_attr( $edit_item['icon_value'] ); ?>" /><button type="button" class="button jpaf-upload-svg-button"><?php esc_html_e( 'Upload / choose SVG', 'jellopoint-accommodation-facilities' ); ?></button></div><p class="description jpaf-icon-help jpaf-help-dashicon"><?php esc_html_e( 'Example: dashicons-wifi', 'jellopoint-accommodation-facilities' ); ?></p><p class="description jpaf-icon-help jpaf-help-custom"><?php esc_html_e( 'Example: fa-solid fa-wifi', 'jellopoint-accommodation-facilities' ); ?></p><p class="description jpaf-icon-help jpaf-help-svg"><?php esc_html_e( 'Select an SVG from the media library or upload a new one.', 'jellopoint-accommodation-facilities' ); ?></p><div class="jpaf-icon-preview"><?php echo \JelloPoint\AccommodationFacilities\jpaf_get_icon_html( $edit_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></div>
					<div class="jpaf-field jpaf-field--full"><label class="jpaf-checkbox-label"><input name="active" type="checkbox" value="1" <?php checked( ! empty( $edit_item['active'] ) ); ?> /> <?php esc_html_e( 'Show this facility in widget choices', 'jellopoint-accommodation-facilities' ); ?></label></div>
				</div>
				<div class="jpaf-form-actions"><button type="submit" class="button button-primary"><?php echo $editing ? esc_html__( 'Save changes', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add facility', 'jellopoint-accommodation-facilities' ); ?></button><?php if ( $editing ) : ?><a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facilities' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Cancel editing', 'jellopoint-accommodation-facilities' ); ?></a><?php endif; ?></div>
			</form>
		</div>

		<div class="jpaf-card">
			<div class="jpaf-card-header">
				<div>
					<h2><?php esc_html_e( 'Facilities Library', 'jellopoint-accommodation-facilities' ); ?></h2>
					<p class="description"><?php esc_html_e( 'Filter the library by icon category, then drag rows to change the order.', 'jellopoint-accommodation-facilities' ); ?></p>
				</div>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facilities' ) ); ?>" class="jpaf-sort-form">
					<?php wp_nonce_field( 'jpaf_sort_facilities' ); ?>
					<input type="hidden" name="page" value="jpaf-facilities" />
					<input type="hidden" name="jpaf_action" value="sort" />
					<input type="hidden" name="sorted_ids" id="jpaf-sorted-ids" value="" />
					<button type="submit" class="button button-secondary"><?php esc_html_e( 'Save order', 'jellopoint-accommodation-facilities' ); ?></button>
				</form>
			</div>

			<form method="get" class="jpaf-filter-bar">
				<input type="hidden" name="page" value="jpaf-facilities" />
				<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search facilities…', 'jellopoint-accommodation-facilities' ); ?>" />
				<select name="category_id"><option value=""><?php esc_html_e( 'All icon categories', 'jellopoint-accommodation-facilities' ); ?></option><?php foreach ( $categories as $category ) : ?><option value="<?php echo esc_attr( $category['id'] ); ?>" <?php selected( $category_id, $category['id'] ); ?>><?php echo esc_html( $category['label'] ); ?></option><?php endforeach; ?></select>
				<button type="submit" class="button button-secondary"><?php esc_html_e( 'Filter', 'jellopoint-accommodation-facilities' ); ?></button>
				<?php if ( '' !== $search || '' !== $category_id ) : ?><a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facilities' ) ); ?>" class="button button-link-delete"><?php esc_html_e( 'Reset', 'jellopoint-accommodation-facilities' ); ?></a><?php endif; ?>
			</form>

			<?php if ( empty( $items ) ) : ?>
				<p><?php esc_html_e( 'No facilities found for the current filter.', 'jellopoint-accommodation-facilities' ); ?></p>
			<?php else : ?>
				<table class="widefat fixed striped jpaf-library-table">
					<thead><tr><th style="width:54px;"><?php esc_html_e( 'Sort', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:80px;"><?php esc_html_e( 'Icon', 'jellopoint-accommodation-facilities' ); ?></th><th><?php esc_html_e( 'Name', 'jellopoint-accommodation-facilities' ); ?></th><th><?php esc_html_e( 'Description', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:160px;"><?php esc_html_e( 'Icon Category', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:100px;"><?php esc_html_e( 'Order', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:90px;"><?php esc_html_e( 'Status', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:140px;"><?php esc_html_e( 'Actions', 'jellopoint-accommodation-facilities' ); ?></th></tr></thead>
					<tbody id="jpaf-sortable-library">
						<?php foreach ( $items as $item ) : ?>
							<tr data-id="<?php echo esc_attr( $item['id'] ); ?>"<?php echo $editing && $item['id'] === $edit_item['id'] ? ' class="is-editing-row"' : ''; ?>>
								<td class="jpaf-sort-cell"><span class="jpaf-drag-handle" aria-label="<?php esc_attr_e( 'Drag to sort', 'jellopoint-accommodation-facilities' ); ?>" title="<?php esc_attr_e( 'Drag to sort', 'jellopoint-accommodation-facilities' ); ?>"><span class="dashicons dashicons-menu"></span></span></td>
								<td class="jpaf-icon-cell"><?php echo \JelloPoint\AccommodationFacilities\jpaf_get_icon_html( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
								<td><strong><?php echo esc_html( $item['label'] ); ?></strong><div class="description"><?php echo esc_html( $item['id'] ); ?></div></td>
								<td><?php echo esc_html( $item['description'] ); ?></td>
								<td><?php echo esc_html( $category_map[ $item['category_id'] ] ?? '—' ); ?></td>
								<td><?php echo esc_html( (string) $item['sort_order'] ); ?></td>
								<td><?php echo ! empty( $item['active'] ) ? '<span class="jpaf-status jpaf-status--active">' . esc_html__( 'Active', 'jellopoint-accommodation-facilities' ) . '</span>' : '<span class="jpaf-status">' . esc_html__( 'Inactive', 'jellopoint-accommodation-facilities' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
								<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facilities&action=edit&id=' . rawurlencode( $item['id'] ) . '#jpaf-form-card' ) ); ?>"><?php esc_html_e( 'Edit', 'jellopoint-accommodation-facilities' ); ?></a> | <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=jpaf-facilities&jpaf_action=delete&id=' . rawurlencode( $item['id'] ) ), 'jpaf_delete_facility' ) ); ?>" class="jpaf-delete-link"><?php esc_html_e( 'Delete', 'jellopoint-accommodation-facilities' ); ?></a></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>
