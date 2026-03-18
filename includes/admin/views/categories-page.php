<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$editing = ! empty( $edit_item['id'] );
?>
<div class="wrap jpaf-admin-wrap">
	<div class="jpaf-admin-header">
		<div>
			<h1><?php esc_html_e( 'Icon Categories', 'jellopoint-accommodation-facilities' ); ?></h1>
			<p><?php esc_html_e( 'Use icon categories to organize large icon libraries in the admin area.', 'jellopoint-accommodation-facilities' ); ?></p>
		</div>
	</div>

	<?php if ( 'added' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Icon category added.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'updated' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Icon category updated.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'deleted' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Icon category deleted.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php elseif ( 'missing_label' === $notice ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Please enter a category name.', 'jellopoint-accommodation-facilities' ); ?></p></div>
	<?php endif; ?>

	<div class="jpaf-admin-grid">
		<div class="jpaf-card jpaf-form-card<?php echo $editing ? ' is-editing' : ''; ?>">
			<div class="jpaf-card-header"><div><h2><?php echo $editing ? esc_html__( 'Edit Icon Category', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add Icon Category', 'jellopoint-accommodation-facilities' ); ?></h2></div></div>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-icon-categories' ) ); ?>" class="jpaf-facility-form">
				<?php wp_nonce_field( 'jpaf_save_icon_category' ); ?>
				<input type="hidden" name="page" value="jpaf-icon-categories" />
				<input type="hidden" name="jpaf_action" value="<?php echo $editing ? 'update' : 'add'; ?>" />
				<input type="hidden" name="existing_id" value="<?php echo esc_attr( $edit_item['id'] ); ?>" />
				<input type="hidden" name="category_id" value="<?php echo esc_attr( $edit_item['id'] ); ?>" />
				<div class="jpaf-form-grid">
					<div class="jpaf-field jpaf-field--full"><label for="jpaf-category-label"><?php esc_html_e( 'Category name', 'jellopoint-accommodation-facilities' ); ?></label><input id="jpaf-category-label" name="label" type="text" value="<?php echo esc_attr( $edit_item['label'] ); ?>" required /></div>
					<div class="jpaf-field jpaf-field--full"><label for="jpaf-category-description"><?php esc_html_e( 'Description', 'jellopoint-accommodation-facilities' ); ?></label><textarea id="jpaf-category-description" name="description" rows="4"><?php echo esc_textarea( $edit_item['description'] ); ?></textarea></div>
					<div class="jpaf-field"><label for="jpaf-category-sort-order"><?php esc_html_e( 'Sort order', 'jellopoint-accommodation-facilities' ); ?></label><input id="jpaf-category-sort-order" name="sort_order" type="number" value="<?php echo esc_attr( (string) $edit_item['sort_order'] ); ?>" /></div>
					<div class="jpaf-field jpaf-field--full"><label class="jpaf-checkbox-label"><input name="active" type="checkbox" value="1" <?php checked( ! empty( $edit_item['active'] ) ); ?> /> <?php esc_html_e( 'Active', 'jellopoint-accommodation-facilities' ); ?></label></div>
				</div>
				<div class="jpaf-form-actions"><button type="submit" class="button button-primary"><?php echo $editing ? esc_html__( 'Save changes', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add category', 'jellopoint-accommodation-facilities' ); ?></button><?php if ( $editing ) : ?><a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-icon-categories' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Cancel editing', 'jellopoint-accommodation-facilities' ); ?></a><?php endif; ?></div>
			</form>
		</div>
		<div class="jpaf-card">
			<div class="jpaf-card-header"><div><h2><?php esc_html_e( 'Icon Categories', 'jellopoint-accommodation-facilities' ); ?></h2></div></div>
			<table class="widefat fixed striped jpaf-library-table">
				<thead><tr><th><?php esc_html_e( 'Name', 'jellopoint-accommodation-facilities' ); ?></th><th><?php esc_html_e( 'Description', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:100px;"><?php esc_html_e( 'Order', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:90px;"><?php esc_html_e( 'Status', 'jellopoint-accommodation-facilities' ); ?></th><th style="width:140px;"><?php esc_html_e( 'Actions', 'jellopoint-accommodation-facilities' ); ?></th></tr></thead>
				<tbody>
				<?php if ( empty( $items ) ) : ?>
					<tr><td colspan="5"><?php esc_html_e( 'No icon categories found yet.', 'jellopoint-accommodation-facilities' ); ?></td></tr>
				<?php else : foreach ( $items as $item ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $item['label'] ); ?></strong><div class="description"><?php echo esc_html( $item['id'] ); ?></div></td>
						<td><?php echo esc_html( $item['description'] ); ?></td>
						<td><?php echo esc_html( (string) $item['sort_order'] ); ?></td>
						<td><?php echo ! empty( $item['active'] ) ? '<span class="jpaf-status jpaf-status--active">' . esc_html__( 'Active', 'jellopoint-accommodation-facilities' ) . '</span>' : '<span class="jpaf-status">' . esc_html__( 'Inactive', 'jellopoint-accommodation-facilities' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
						<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-icon-categories&action=edit&id=' . rawurlencode( $item['id'] ) ) ); ?>"><?php esc_html_e( 'Edit', 'jellopoint-accommodation-facilities' ); ?></a> | <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=jpaf-icon-categories&jpaf_action=delete&id=' . rawurlencode( $item['id'] ) ), 'jpaf_delete_icon_category' ) ); ?>" class="jpaf-delete-link"><?php esc_html_e( 'Delete', 'jellopoint-accommodation-facilities' ); ?></a></td>
					</tr>
				<?php endforeach; endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
