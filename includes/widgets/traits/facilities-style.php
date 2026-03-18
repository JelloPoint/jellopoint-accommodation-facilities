<?php
namespace JelloPoint\AccommodationFacilities\Widgets\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Facilities_Style {

	protected function register_style_controls() : void {
		$this->start_controls_section(
			'jpaf_style_container',
			[
				'label' => __( 'Container', 'jellopoint-accommodation-facilities' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'jpaf_column_gap',
			[
				'label'      => __( 'Column Gap', 'jellopoint-accommodation-facilities' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .jpaf-facilities' => '--jpaf-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'jpaf_row_gap',
			[
				'label'      => __( 'Row Gap', 'jellopoint-accommodation-facilities' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .jpaf-facilities' => '--jpaf-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'jpaf_container_padding',
			[
				'label'      => __( 'Padding', 'jellopoint-accommodation-facilities' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .jpaf-facilities' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'jpaf_container_background',
			[
				'label'     => __( 'Background Color', 'jellopoint-accommodation-facilities' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jpaf-facilities' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'jpaf_style_item',
			[
				'label' => __( 'Item', 'jellopoint-accommodation-facilities' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'jpaf_item_padding',
			[
				'label'      => __( 'Padding', 'jellopoint-accommodation-facilities' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .jpaf-facility' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'jpaf_item_background',
			[
				'label'     => __( 'Background Color', 'jellopoint-accommodation-facilities' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jpaf-facility' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'jpaf_item_border',
				'selector' => '{{WRAPPER}} .jpaf-facility',
			]
		);

		$this->add_responsive_control(
			'jpaf_item_radius',
			[
				'label'      => __( 'Border Radius', 'jellopoint-accommodation-facilities' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .jpaf-facility' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'jpaf_style_icon',
			[
				'label' => __( 'Icon', 'jellopoint-accommodation-facilities' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'jpaf_icon_size',
			[
				'label'      => __( 'Size', 'jellopoint-accommodation-facilities' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 12, 'max' => 80 ] ],
				'selectors'  => [
					'{{WRAPPER}} .jpaf-facility__icon' => '--jpaf-icon-size: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'jpaf_icon_color',
			[
				'label'     => __( 'Color', 'jellopoint-accommodation-facilities' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jpaf-facility__icon' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'jpaf_icon_background',
			[
				'label'     => __( 'Background Color', 'jellopoint-accommodation-facilities' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jpaf-facility__icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'jpaf_icon_spacing',
			[
				'label'      => __( 'Spacing', 'jellopoint-accommodation-facilities' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'selectors'  => [
					'{{WRAPPER}} .jpaf-icon-left .jpaf-facility' => 'column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jpaf-icon-top .jpaf-facility' => 'row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'jpaf_style_label',
			[
				'label' => __( 'Label', 'jellopoint-accommodation-facilities' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'jpaf_label_typography',
				'selector' => '{{WRAPPER}} .jpaf-facility__label',
			]
		);

		$this->add_control(
			'jpaf_label_color',
			[
				'label'     => __( 'Color', 'jellopoint-accommodation-facilities' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jpaf-facility__label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'jpaf_style_description',
			[
				'label' => __( 'Description', 'jellopoint-accommodation-facilities' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'jpaf_description_typography',
				'selector' => '{{WRAPPER}} .jpaf-facility__description',
			]
		);

		$this->add_control(
			'jpaf_description_color',
			[
				'label'     => __( 'Color', 'jellopoint-accommodation-facilities' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jpaf-facility__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}
}
