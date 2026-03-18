<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="<?php echo esc_attr( $classes ); ?>">
	<?php foreach ( $facilities as $facility ) : ?>
		<?php include __DIR__ . '/../partials/facility-item.php'; ?>
	<?php endforeach; ?>
</div>
