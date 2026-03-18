<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait JPAF_Facilities_Controls {

	protected function register_content_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'jellopoint-accommodation-facilities' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'source_type',
			[
				'label'   => __( 'Source Type', 'jellopoint-accommodation-facilities' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => [
					'manual' => __( 'Manual Selection', 'jellopoint-accommodation-facilities' ),
					'group'  => __( 'Facility Group', 'jellopoint-accommodation-facilities' ),
				],
			]
		);

		$this->add_control(
			'facility_group',
			[
				'label'       => __( 'Select Group', 'jellopoint-accommodation-facilities' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => $this->jpaf_get_group_options(),
				'label_block' => true,
				'condition'   => [
					'source_type' => 'group',
				],
			]
		);

		$this->add_control(
			'facilities',
			[
				'label'       => __( 'Facilities', 'jellopoint-accommodation-facilities' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->jpaf_get_facility_options(),
				'label_block' => true,
				'condition'   => [
					'source_type' => 'manual',
				],
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout', 'jellopoint-accommodation-facilities' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'grid' => __( 'Grid', 'jellopoint-accommodation-facilities' ),
					'list' => __( 'List', 'jellopoint-accommodation-facilities' ),
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __( 'Columns', 'jellopoint-accommodation-facilities' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'condition'      => [
					'layout' => 'grid',
				],
				'selectors'      => [
					'{{WRAPPER}} .jpaf-facilities' => '--jpaf-columns: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'        => __( 'Show Description', 'jellopoint-accommodation-facilities' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'jellopoint-accommodation-facilities' ),
				'label_off'    => __( 'Hide', 'jellopoint-accommodation-facilities' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'order_by',
			[
				'label'   => __( 'Order', 'jellopoint-accommodation-facilities' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'selection',
				'options' => [
					'selection' => __( 'Selection Order', 'jellopoint-accommodation-facilities' ),
					'admin'     => __( 'Admin Sort Order', 'jellopoint-accommodation-facilities' ),
					'alphabetic'=> __( 'Alphabetical', 'jellopoint-accommodation-facilities' ),
				],
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'   => __( 'Icon Position', 'jellopoint-accommodation-facilities' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => __( 'Left', 'jellopoint-accommodation-facilities' ),
					'top'  => __( 'Top', 'jellopoint-accommodation-facilities' ),
				],
			]
		);

		$this->end_controls_section();
	}

	
	protected function jpaf_get_facility_options() {
		$options = [];

		if ( ! class_exists( 'JPAF_Facilities_Store' ) ) {
			return $options;
		}

		$items = JPAF_Facilities_Store::get_active();

		if ( empty( $items ) || ! is_array( $items ) ) {
			return $options;
		}

		foreach ( $items as $item ) {
			if ( empty( $item['id'] ) || empty( $item['label'] ) ) {
				continue;
			}

			$options[ $item['id'] ] = $item['label'];
		}

		return $options;
	}

	protected function jpaf_get_group_options() {
		$options = [];

		if ( ! class_exists( 'JPAF_Facility_Groups_Store' ) ) {
			return $options;
		}

		$groups = JPAF_Facility_Groups_Store::get_active();

		if ( empty( $groups ) || ! is_array( $groups ) ) {
			return $options;
		}

		foreach ( $groups as $group ) {
			if ( empty( $group['id'] ) || empty( $group['label'] ) ) {
				continue;
			}

			$options[ $group['id'] ] = $group['label'];
		}

		return $options;
	}
}