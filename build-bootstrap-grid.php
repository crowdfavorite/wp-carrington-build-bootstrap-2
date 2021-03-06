<?php
/**
 * Plugin Name: Twitter Bootstrap 2 for Carrington Build
 * Description: Add in Twitter Bootstrap grid classes and markup for Twitter Bootstrap-compatibility in Carrington Build.
 * Version: 1.0.2
 * Author: Crowd Favorite
 * Author URI: http://crowdfavorite.com
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright (c) 2013 Crowd Favorite, Ltd. All rights reserved.
 * http://crowdfavorite.com
 *
 * **********************************************************************
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * **********************************************************************
**/
 
class CFCT_Enable_Bootstrap_2 {
	public $row_classes_change_map = array();
	public $block_classes_change_map = array();

	public $old_row_classname_to_new = array(
		// Rows
		'row-c4-1234' => 'row-12',

		'row-c6-12-34-56' => 'row-4-4-4',

		'row-c6-1234-56' => 'row-8-4',
		'row-c6-12-3456' => 'row-4-8',

		'row-c4-12-34' => 'row-6-6',
	);

	public $old_block_classname_to_new = array(
		// All New Grid classes in Carrington
		'c4-1234' => 'span12',

		'c6-1234' => 'span8',
		'c6-3456' => 'span8',

		'c4-12' => 'span6',
		'c4-34' => 'span6',

		'c6-12' => 'span4',
		'c6-34' => 'span4',
		'c6-56' => 'span4'
	);
	
	public function __construct() {
		foreach ($this->old_row_classname_to_new as $old => $new) {
			$this->push_row_class_change($new, $old);
		}
		foreach ($this->old_block_classname_to_new as $old => $new) {
			$this->push_block_class_change($new, $old);
		}

		return $this;
	}

	// TODO - refactor to make this a static function
	public function attach_hooks() {
		// Restore generated block ID class
		add_filter(
			'cfct-generated-row-classes',
			array($this, 'add_generated_in_row_classes'),
			10, 3
		);

		add_filter(
			'cfct-row-html',
			array($this, 'restore_row_html'),
			10, 3
		);

		add_filter(
			'cfct-row-defaults',
			array($this, 'enable_proper_row_classes'),
			10
		);
		
		add_filter(
			'cfct-block-template',
			array($this, 'restore_block_template'),
			10, 2
		);
		
		/* We still use the old-school row filter keys to avoid
		breaking backwards compat with filters. */
		$row_class_filters = array(
			'cfct-row-abc-classes',
			'cfct-row-d-e-classes',
			'cfct-row-a-bc-classes',
			'cfct-row-ab-c-classes',
			'cfct-row-a-b-c-classes',
			'cfct-row-float-c-classes',
			'cfct-row-float-a-classes'
		);
		
		// Add row filter filters
		foreach ($row_class_filters as $filter_key) {
			add_filter(
				$filter_key,
				array($this, 'restore_row_classes'),
				10, 2
			);
		}

		$block_class_filters = array(
			/* Full */
			'cfct-block-c4-1234-classes',
			
			/* Halves */
			'cfct-block-c4-12-classes',
			'cfct-block-c4-34-classes',
			
			/* Thirds */
			'cfct-block-c6-12-classes',
			'cfct-block-c6-34-classes',
			'cfct-block-c6-56-classes',
	
			/* 2 Thirds */
			'cfct-block-c6-1234-classes',
			'cfct-block-c6-3456-classes'
		);
		
		foreach($block_class_filters as $filter_key) {
			add_filter(
				$filter_key,
				array($this, 'restore_block_classes'),
				10, 2
			);
		}
	}
	
	/**
	 * Backwards-compat row markup
	 */
	public function restore_row_html($html, $classname, $classes) {
		return '<div id="{id}" class="{class}">{blocks}</div>';
	}
	
	/**
	 * Brings back block ID
	 */
	public function restore_block_template($html, $block_instance) {
		return '<div id="{id}" class="{class}">{modules}</div>';
	}

	public function push_row_class_change($new, $old) {
		$this->row_classes_change_map[] = array(
			'old' => cfct_tpl::extract_classes($old),
			'new' => cfct_tpl::extract_classes($new)
		);
	}

	public function push_block_class_change($new, $old) {
		$this->block_classes_change_map[] = array(
			'old' => cfct_tpl::extract_classes($old),
			'new' => cfct_tpl::extract_classes($new)
		);
	}
	
	public function add_generated_in_row_classes($classes, $module_types, $row_instance) {
		$generated_classes = $row_instance->add_in_row_classes($module_types); 

		return array_merge($classes, $generated_classes);
	}
	
	public function restore_classes($ch_ch_ch_changes, $classes) {
		foreach ($ch_ch_ch_changes as $pair) {
			/* (turn and face the strain) */
			$intersect = array_intersect($classes, $pair['old']);
			/* Does the row have the same classes that we've recorded as new?
			Then it's a match, so add the equivalent old classes. */
			if (count($intersect) == count($pair['old'])) {
				$classes = array_replace($classes, $pair['new']);
			}
		}
		
		return cfct_tpl::clean_classes($classes);
	}
	
	public function restore_row_classes($classes, $row_instance) {
		$classes = $this->restore_classes(
			$this->row_classes_change_map,
			$classes
		);
		$classes[] = 'cfct-row';
		return $classes;
	}

	public function restore_block_classes($classes, $block_instance) {
		$classes = $this->restore_classes(
			$this->block_classes_change_map,
			$classes
		);
		return $classes;
	}
	
	public function enable_proper_row_classes($defaults) {
		$defaults['row_class'] = 'row-fluid';

		return $defaults;
	}

	/**
	 * A bit janky, but since we don't have error handling in WP,
	 * do a feature check to make sure this version of Build is compatible with
	 * this plugin.
	 */
	public static function check_features() {
		if (!function_exists('cfct_build')) {
			return new WP_Error('function not found', 'Carrington Build needs to be activated for \"Twitter Bootstrap Grid\" to take effect.');
		}
		if (!class_exists('cfct_tpl')) {
			return new WP_Error('class not found', 'Class cfct_tpl does not exist. You probably need to install a newer version of Carrington Build.');
		}
		if (!function_exists('cfct_build_register_row')) {
			return new WP_Error('function not found', 'Function cfct_build_register_row does not exist.');
		}
	}

	/**
	 * Hook this into init()
	 */
	public static function init() {
		$diagnostics = self::check_features();
		if (is_wp_error($diagnostics)) {
				$cb = create_function('', 'echo "<div class=\'message error\'>
	<p>'.$diagnostics->get_error_message().'</p>
</div>";');
			add_action('admin_notices', $cb);
		}
		else {
			$instance = new CFCT_Enable_Bootstrap_2();
			$instance->attach_hooks();
		}
	}
}
add_action('init', array('CFCT_Enable_Bootstrap_2', 'init'));
