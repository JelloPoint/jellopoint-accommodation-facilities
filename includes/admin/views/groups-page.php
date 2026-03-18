<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Expected variables:
 * - $groups
 * - $current_group
 * - $facilities
 * - $editing
 */

$groups        = isset( $groups ) && is_array( $groups ) ? $groups : [];
$facilities    = isset( $facilities ) && is_array( $facilities ) ? $facilities : [];
$current_group = isset( $current_group ) && is_array( $current_group ) ? $current_group : [];
$editing       = ! empty( $editing );

$group_id          = ! empty( $current_group['id'] ) ? $current_group['id'] : '';
$group_label       = ! empty( $current_group['label'] ) ? $current_group['label'] : '';
$group_description = ! empty( $current_group['description'] ) ? $current_group['description'] : '';
$group_sort_order  = isset( $current_group['sort_order'] ) ? (int) $current_group['sort_order'] : 10;
$group_active      = isset( $current_group['active'] ) ? (int) $current_group['active'] : 1;
$selected_ids      = ! empty( $current_group['facility_ids'] ) && is_array( $current_group['facility_ids'] ) ? $current_group['facility_ids'] : [];

?>
<div class="wrap jpaf-admin-page jpaf-admin-page--groups">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Facility Groups', 'jellopoint-accommodation-facilities' ); ?></h1>

	<?php if ( $editing ) : ?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facility-groups' ) ); ?>" class="page-title-action">
			<?php esc_html_e( 'Add New Group', 'jellopoint-accommodation-facilities' ); ?>
		</a>
	<?php endif; ?>

	<hr class="wp-header-end">

	<div class="jpaf-admin-grid" style="display:grid;grid-template-columns:minmax(320px,420px) 1fr;gap:24px;align-items:start;">
		<div class="jpaf-admin-card jpaf-admin-card--form" style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;">
			<h2 style="margin-top:0;">
				<?php echo $editing ? esc_html__( 'Edit Group', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add Group', 'jellopoint-accommodation-facilities' ); ?>
			</h2>

			<form method="post">
				<?php wp_nonce_field( 'jpaf_save_group', 'jpaf_group_nonce' ); ?>
				<input type="hidden" name="jpaf_action" value="<?php echo $editing ? 'update_group' : 'add_group'; ?>">
				<?php if ( $editing ) : ?>
					<input type="hidden" name="group_id" value="<?php echo esc_attr( $group_id ); ?>">
				<?php endif; ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="jpaf-group-label"><?php esc_html_e( 'Group Name', 'jellopoint-accommodation-facilities' ); ?></label>
							</th>
							<td>
								<input type="text" id="jpaf-group-label" name="label" class="regular-text" value="<?php echo esc_attr( $group_label ); ?>" required>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="jpaf-group-description"><?php esc_html_e( 'Description', 'jellopoint-accommodation-facilities' ); ?></label>
							</th>
							<td>
								<textarea id="jpaf-group-description" name="description" class="large-text" rows="3"><?php echo esc_textarea( $group_description ); ?></textarea>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="jpaf-group-sort-order"><?php esc_html_e( 'Sort Order', 'jellopoint-accommodation-facilities' ); ?></label>
							</th>
							<td>
								<input type="number" id="jpaf-group-sort-order" name="sort_order" class="small-text" value="<?php echo esc_attr( $group_sort_order ); ?>">
							</td>
						</tr>

						<tr>
							<th scope="row"><?php esc_html_e( 'Active', 'jellopoint-accommodation-facilities' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="active" value="1" <?php checked( $group_active, 1 ); ?>>
									<?php esc_html_e( 'Enabled', 'jellopoint-accommodation-facilities' ); ?>
								</label>
							</td>
						</tr>

						<tr>
							<th scope="row"><?php esc_html_e( 'Select Facilities', 'jellopoint-accommodation-facilities' ); ?></th>
							<td>
								<div class="jpaf-group-facility-list" style="max-height:420px;overflow:auto;border:1px solid #dcdcde;border-radius:6px;background:#fff;">
									<?php if ( empty( $facilities ) ) : ?>
										<p style="padding:12px;margin:0;"><?php esc_html_e( 'No facilities found.', 'jellopoint-accommodation-facilities' ); ?></p>
									<?php else : ?>
										<?php foreach ( $facilities as $facility ) : ?>
											<?php
											$facility_id    = ! empty( $facility['id'] ) ? $facility['id'] : '';
											$facility_label = ! empty( $facility['label'] ) ? $facility['label'] : $facility_id;

											if ( '' === $facility_id ) {
												continue;
											}

											$is_checked = in_array( $facility_id, $selected_ids, true );
											?>
											<label class="jpaf-group-facility-row" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-bottom:1px solid #f0f0f1;cursor:pointer;">
												<input type="checkbox" name="facility_ids[]" value="<?php echo esc_attr( $facility_id ); ?>" <?php checked( $is_checked ); ?> style="margin:0;">
												<span class="jpaf-group-facility-icon" style="width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;flex:0 0 24px;">
													<?php
													if ( function_exists( 'jpaf_get_icon_html' ) ) {
														echo jpaf_get_icon_html( $facility ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													}
													?>
												</span>
												<span class="jpaf-group-facility-label" style="line-height:1.3;">
													<?php echo esc_html( $facility_label ); ?>
												</span>
											</label>
										<?php endforeach; ?>
									<?php endif; ?>
								</div>
								<p class="description" style="margin-top:8px;">
									<?php esc_html_e( 'Choose the facilities that belong to this group.', 'jellopoint-accommodation-facilities' ); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit" style="margin-bottom:0;">
					<button type="submit" class="button button-primary">
						<?php echo $editing ? esc_html__( 'Update Group', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Add Group', 'jellopoint-accommodation-facilities' ); ?>
					</button>

					<?php if ( $editing ) : ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facility-groups' ) ); ?>" class="button">
							<?php esc_html_e( 'Cancel', 'jellopoint-accommodation-facilities' ); ?>
						</a>
					<?php endif; ?>
				</p>
			</form>
		</div>

		<div class="jpaf-admin-card jpaf-admin-card--table" style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;">
			<h2 style="margin-top:0;"><?php esc_html_e( 'Groups Library', 'jellopoint-accommodation-facilities' ); ?></h2>

			<?php if ( empty( $groups ) ) : ?>
				<p><?php esc_html_e( 'No facility groups found yet.', 'jellopoint-accommodation-facilities' ); ?></p>
			<?php else : ?>
				<table class="widefat fixed striped">
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
							$item_id     = ! empty( $group['id'] ) ? $group['id'] : '';
							$item_label  = ! empty( $group['label'] ) ? $group['label'] : $item_id;
							$item_count  = ! empty( $group['facility_ids'] ) && is_array( $group['facility_ids'] ) ? count( $group['facility_ids'] ) : 0;
							$item_sort   = isset( $group['sort_order'] ) ? (int) $group['sort_order'] : 0;
							$item_active = ! empty( $group['active'] );

							if ( '' === $item_id ) {
								continue;
							}
							?>
							<tr>
								<td><strong><?php echo esc_html( $item_label ); ?></strong></td>
								<td><?php echo esc_html( $item_count ); ?></td>
								<td><?php echo esc_html( $item_sort ); ?></td>
								<td><?php echo $item_active ? esc_html__( 'Active', 'jellopoint-accommodation-facilities' ) : esc_html__( 'Inactive', 'jellopoint-accommodation-facilities' ); ?></td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=jpaf-facility-groups&action=edit&id=' . rawurlencode( $item_id ) ) ); ?>">
										<?php esc_html_e( 'Edit', 'jellopoint-accommodation-facilities' ); ?>
									</a>
									|
									<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=jpaf-facility-groups&action=delete&id=' . rawurlencode( $item_id ) ), 'jpaf_delete_group_' . $item_id ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete this group?', 'jellopoint-accommodation-facilities' ) ); ?>');">
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