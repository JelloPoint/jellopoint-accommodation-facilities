<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="jpaf-facility">
	<div class="jpaf-facility__icon">
		<?php echo \JelloPoint\AccommodationFacilities\jpaf_get_icon_html( $facility ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="jpaf-facility__content">
		<div class="jpaf-facility__label"><?php echo esc_html( $facility['label'] ); ?></div>
		<?php if ( $show_desc && ! empty( $facility['description'] ) ) : ?>
			<div class="jpaf-facility__description"><?php echo esc_html( $facility['description'] ); ?></div>
		<?php endif; ?>
	</div>
</div>
