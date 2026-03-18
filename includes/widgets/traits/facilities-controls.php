<?php
namespace JelloPoint\AccommodationFacilities\Widgets\Traits;

use Elementor\Controls_Manager;
use JelloPoint\AccommodationFacilities\Facilities_Store;
use JelloPoint\AccommodationFacilities\Facility_Groups_Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Facilities_Controls {

	protected function register_content_controls() : void {
		$this->start_controls_section(
			'jpaf_content',
			[
				'label' => __( 'Content', 'jellopoint-accommodation-facilities' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'source_type',
			[
				'label'   => __( 'Source Type', 'jellopoint-accommodation-facilities' ),
				'type'    => Controls_Manager::SELECT,
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
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->get_group_options(),
				'label_block' => true,
				'condition'   => [ 'source_type' => 'group' ],
			]
		);

		$this->add_control(
			'facilities',
			[
				'label'       => __( 'Facilities', 'jellopoint-accommodation-facilities' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_facility_options(),
				'multiple'    => true,
				'label_block' => true,
				'condition'   => [ 'source_type' => 'manual' ],
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout', 'jellopoint-accommodation-facilities' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'grid' => __( 'Grid', 'jellopoint-accommodation-facilities' ),
					'list' => __( 'List', 'jellopoint-accommodation-facilities' ),
				],
			]
		);

		$this->add_control(
			'columns',
			[
				'label'   => __( 'Columns', 'jellopoint-accommodation-facilities' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [ '1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4' ],
				'condition' => [ 'layout' => 'grid' ],
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'        => __( 'Show Description', 'jellopoint-accommodation-facilities' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'jellopoint-accommodation-facilities' ),
				'label_off'    => __( 'No', 'jellopoint-accommodation-facilities' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'order_mode',
			[
				'label'   => __( 'Order', 'jellopoint-accommodation-facilities' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'selection',
				'options' => [
					'selection'    => __( 'Selection order', 'jellopoint-accommodation-facilities' ),
					'admin'        => __( 'Admin sort order', 'jellopoint-accommodation-facilities' ),
					'alphabetical' => __( 'Alphabetical', 'jellopoint-accommodation-facilities' ),
				],
				'condition' => [ 'source_type' => 'manual' ],
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'   => __( 'Icon Position', 'jellopoint-accommodation-facilities' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [ 'title' => __( 'Left', 'jellopoint-accommodation-facilities' ), 'icon'  => 'eicon-h-align-left' ],
					'top'  => [ 'title' => __( 'Top', 'jellopoint-accommodation-facilities' ), 'icon'  => 'eicon-v-align-top' ],
				],
				'toggle' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function get_facility_options() : array {
		$options = [];
		foreach ( Facilities_Store::get_active() as $facility ) {
			$options[ $facility['id'] ] = $facility['label'];
		}
		return $options;
	}

	protected function get_group_options() : array {
		$options = [];
		foreach ( Facility_Groups_Store::get_active() as $group ) {
			$options[ $group['id'] ] = $group['label'];
		}
		return $options;
	}
}
