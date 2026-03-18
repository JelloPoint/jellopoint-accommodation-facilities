<?php
namespace JelloPoint\AccommodationFacilities\Widgets;

use Elementor\Widget_Base;
use JelloPoint\AccommodationFacilities\Facilities_Renderer;

require_once __DIR__ . '/traits/facilities-controls.php';
require_once __DIR__ . '/traits/facilities-style.php';

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Facilities_Widget extends Widget_Base {
	use Traits\Facilities_Controls;
	use Traits\Facilities_Style;

	public function get_name() {
		return 'jpaf_facilities';
	}

	public function get_title() {
		return __( 'Facilities (JelloPoint)', 'jellopoint-accommodation-facilities' );
	}

	public function get_icon() {
		return 'eicon-icon-box';
	}

	public function get_categories() {
		return [ 'jellopoint-widgets' ];
	}

	public function get_keywords() {
		return [ 'facilities', 'amenities', 'accommodation', 'jellopoint', 'gite' ];
	}

	public function get_style_depends() {
		return [ 'jpaf-facilities' ];
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function render() {
		echo Facilities_Renderer::render( $this->get_settings_for_display() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
