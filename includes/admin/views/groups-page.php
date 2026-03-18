<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$groups             = isset( $groups ) && is_array( $groups ) ? $groups : [];
$current_group      = isset( $current_group ) && is_array( $current_group ) ? $current_group : [];
$facilities         = isset( $facilities ) && is_array( $facilities ) ? $facilities : [];
$editing            = ! empty( $editing );
$selected_ids       = ! empty( $current_group['facility_ids'] ) && is_array( $current_group['facility_ids'] ) ? $current_group['facility_ids'] : [];
$current_group_id   = ! empty( $current_group['id'] ) ? $current_group['id'] : '';
$current_label      = ! empty( $current_group['label'] ) ? $current_group['label'] : '';
$current_desc       = ! empty( $current_group['description'] ) ? $current_group['description'] : '';
$current_sort_order = isset( $current_group['sort_order'] ) ? (int) $current_group['sort_order'] : 10;
$current_active     = isset( $current_group['active'] ) ? (int) $current_group['active'] : 1;
?>
<div class="wrap jpaf-admin-wrap">
	<div class="jpaf-admin-header">
		<div>
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Facility Groups', 'jellopoint-accommodation-facilities' ); ?></h1>
			<p><?php esc_html_e( 'Create reusable groups of facilities that you can select inside the Elementor widget.', 'jellopoint-accommodation-facilities' ); ?></p>
		</div>
		<?php if ( $editing ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facility-groups' ) ); ?>" class="page-title-action">
				<?php esc_html_e( 'Add New Group', 'jellopoint-accommodation-facilities' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="jpaf-admin-grid jpaf-admin-grid--wide-left">
		<div class="jpaf-card jpaf-form-card <?php echo $editing ? 'is-editing' : ''; ?>">
			<div class="jpaf-card-header">
				<div>
					<h2><?php echo $editing ? esc_html__( 'Edit Group', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add Group', 'jellopoint-accommodation-facilities' ); ?></h2>
					<p><?php esc_html_e( 'Select the facilities you want to include in this reusable group.', 'jellopoint-accommodation-facilities' ); ?></p>
				</div>
				<?php if ( $editing ) : ?>
					<span class="jpaf-edit-badge"><?php echo esc_html( $current_label ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( $editing ) : ?>
				<div class="jpaf-editing-notice">
					<strong><?php esc_html_e( 'Currently editing:', 'jellopoint-accommodation-facilities' ); ?></strong>
					<span><?php echo esc_html( $current_label ); ?></span>
				</div>
			<?php endif; ?>

			<form method="post" class="jpaf-facility-form">
				<?php wp_nonce_field( 'jpaf_save_group', 'jpaf_group_nonce' ); ?>
				<input type="hidden" name="jpaf_action" value="<?php echo $editing ? 'update_group' : 'add_group'; ?>">
				<?php if ( $editing ) : ?>
					<input type="hidden" name="group_id" value="<?php echo esc_attr( $current_group_id ); ?>">
				<?php endif; ?>

				<div class="jpaf-form-grid">
					<div class="jpaf-field jpaf-field--full">
						<label for="jpaf-group-label"><?php esc_html_e( 'Group Name', 'jellopoint-accommodation-facilities' ); ?></label>
						<input type="text" id="jpaf-group-label" name="label" value="<?php echo esc_attr( $current_label ); ?>" required>
					</div>

					<div class="jpaf-field jpaf-field--full">
						<label for="jpaf-group-description"><?php esc_html_e( 'Description', 'jellopoint-accommodation-facilities' ); ?></label>
						<textarea id="jpaf-group-description" name="description" rows="3"><?php echo esc_textarea( $current_desc ); ?></textarea>
					</div>

					<div class="jpaf-field">
						<label for="jpaf-group-sort-order"><?php esc_html_e( 'Sort Order', 'jellopoint-accommodation-facilities' ); ?></label>
						<input type="number" id="jpaf-group-sort-order" name="sort_order" value="<?php echo esc_attr( $current_sort_order ); ?>">
					</div>

					<div class="jpaf-field">
						<label>&nbsp;</label>
						<label class="jpaf-checkbox-label">
							<input type="checkbox" name="active" value="1" <?php checked( 1, $current_active ); ?>>
							<?php esc_html_e( 'Active', 'jellopoint-accommodation-facilities' ); ?>
						</label>
					</div>

					<div class="jpaf-field jpaf-field--full">
						<label for="jpaf-group-facility-search"><?php esc_html_e( 'Select Facilities', 'jellopoint-accommodation-facilities' ); ?></label>

						<div class="jpaf-filter-bar">
							<input
								type="search"
								id="jpaf-group-facility-search"
								class="regular-text"
								placeholder="<?php esc_attr_e( 'Search facilities…', 'jellopoint-accommodation-facilities' ); ?>"
								autocomplete="off"
							>
						</div>

						<div class="jpaf-checkbox-list" id="jpaf-group-facility-list">
							<?php if ( empty( $facilities ) ) : ?>
								<p><?php esc_html_e( 'No facilities found.', 'jellopoint-accommodation-facilities' ); ?></p>
							<?php else : ?>
								<?php foreach ( $facilities as $facility ) : ?>
									<?php
									$facility_id    = ! empty( $facility['id'] ) ? $facility['id'] : '';
									$facility_label = ! empty( $facility['label'] ) ? $facility['label'] : '';
									$search_text    = strtolower( trim( $facility_label . ' ' . $facility_id ) );

									if ( '' === $facility_id || '' === $facility_label ) {
										continue;
									}
									?>
									<label
										class="jpaf-checkbox-item"
										data-search="<?php echo esc_attr( $search_text ); ?>"
									>
										<input
											type="checkbox"
											name="facility_ids[]"
											value="<?php echo esc_attr( $facility_id ); ?>"
											<?php checked( in_array( $facility_id, $selected_ids, true ) ); ?>
										>
										<span class="jpaf-checkbox-icon">
											<?php
											if ( function_exists( 'jpaf_get_icon_html' ) ) {
												echo jpaf_get_icon_html( $facility ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											}
											?>
										</span>
										<span class="jpaf-checkbox-label-text"><?php echo esc_html( $facility_label ); ?></span>
									</label>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>

						<p class="description">
							<?php esc_html_e( 'Type to quickly filter the available facilities.', 'jellopoint-accommodation-facilities' ); ?>
						</p>
					</div>
				</div>

				<div class="jpaf-form-actions">
					<button type="submit" class="button button-primary">
						<?php echo $editing ? esc_html__( 'Update Group', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add Group', 'jellopoint-accommodation-facilities' ); ?>
					</button>

					<?php if ( $editing ) : ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facility-groups' ) ); ?>" class="button">
							<?php esc_html_e( 'Cancel', 'jellopoint-accommodation-facilities' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<div class="jpaf-card">
			<div class="jpaf-card-header">
				<div>
					<h2><?php esc_html_e( 'Groups Library', 'jellopoint-accommodation-facilities' ); ?></h2>
					<p><?php esc_html_e( 'Manage your reusable facility groups.', 'jellopoint-accommodation-facilities' ); ?></p>
				</div>
			</div>

			<?php if ( empty( $groups ) ) : ?>
				<p><?php esc_html_e( 'No groups found yet.', 'jellopoint-accommodation-facilities' ); ?></p>
			<?php else : ?>
				<table class="widefat fixed striped jpaf-library-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Name', 'jellopoint-accommodation-facilities' ); ?></th>
							<th><?php esc_html_e( 'Facilities', 'jellopoint-accommodation-facilities' ); ?></th>
							<th><?php esc_html_e( 'Sort', 'jellopoint-accommodation-facilities' ); ?></th>
							<th><?php esc_html_e( 'Status', 'jellopoint-accommodation-facilities' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'jellopoint-accommodation-facilities' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $groups as $group ) : ?>
							<?php
							$group_id    = ! empty( $group['id'] ) ? $group['id'] : '';
							$group_label = ! empty( $group['label'] ) ? $group['label'] : '';
							$is_active   = ! empty( $group['active'] );
							$count       = ! empty( $group['facility_ids'] ) && is_array( $group['facility_ids'] ) ? count( $group['facility_ids'] ) : 0;

							if ( '' === $group_id || '' === $group_label ) {
								continue;
							}
							?>
							<tr class="<?php echo ( $editing && $current_group_id === $group_id ) ? 'is-editing-row' : ''; ?>">
								<td><strong><?php echo esc_html( $group_label ); ?></strong></td>
								<td><?php echo esc_html( $count ); ?></td>
								<td><?php echo isset( $group['sort_order'] ) ? esc_html( (string) (int) $group['sort_order'] ) : '0'; ?></td>
								<td>
									<span class="jpaf-status <?php echo $is_active ? 'jpaf-status--active' : ''; ?>">
										<?php echo $is_active ? esc_html__( 'Active', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Inactive', 'jellopoint-accommodation-facilities' ); ?>
									</span>
								</td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facility-groups&action=edit&id=' . rawurlencode( $group_id ) ) ); ?>">
										<?php esc_html_e( 'Edit', 'jellopoint-accommodation-facilities' ); ?>
									</a>
									|
									<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=jpaf-facility-groups&action=delete&id=' . rawurlencode( $group_id ) ), 'jpaf_delete_group_' . $group_id ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete this group?', 'jellopoint-accommodation-facilities' ) ); ?>');">
										<?php esc_html_e( 'Delete', 'jellopoint-accommodation-facilities' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var searchInput = document.getElementById('jpaf-group-facility-search');
	var list = document.getElementById('jpaf-group-facility-list');

	if (!searchInput || !list) {
		return;
	}

	var items = list.querySelectorAll('.jpaf-checkbox-item');

	searchInput.addEventListener('input', function () {
		var query = (searchInput.value || '').toLowerCase().trim();

		items.forEach(function (item) {
			var haystack = (item.getAttribute('data-search') || '').toLowerCase();

			if (!query || haystack.indexOf(query) !== -1) {
				item.style.display = '';
			} else {
				item.style.display = 'none';
			}
		});
	});
});
</script>