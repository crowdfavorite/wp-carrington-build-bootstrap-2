<?php
/*
* Plugin Name: Twitter Bootstrap Build Settings
* Description: Add in classes, markup and row types for Twitter Bootstrap responsive scaffolding compatibility.
* Version: 1.0
* Author: Crowd Favorite
* Author URI: http://crowdfavorite.com
*/

class CFCT_Build_Bootstrap {
		public $row_classes_change_map = array();
		public $block_classes_change_map = array();
		
		public $old_row_classname_to_new = array(
				// Rows
				'row-c4-1234' => 'row-fluid',

				'row-c6-12-34-56' => 'row-fluid',

				'row-c6-1234-56' => 'row-fluid',
				'row-c6-12-3456' => 'row-fluid',

				'row-c4-12-34' => 'row-fluid',

				// Weird rows
				'row-c6-12-3456 row-c6-12-float-left' => 'row-fluid',
				'row-c6-1234-56 row-c6-56-float-right' => 'row-fluid'
		);
 
		public $old_block_classname_to_new = array(
				// All New Grid classes in Carrington

				// Proper ordering
				'c4-1234' => 'span12',
		 
				'c6-1234' => 'span8',
				'c6-3456' => 'span8',

				'c4-12' => 'span6',
				'c4-34' => 'span6',

				'c6-12' => 'span4',
				'c6-34' => 'span4',
				'c6-56' => 'span4',

				'c6-12' => 'span4',
				'c6-34' => 'span4',
				'c6-56' => 'span4'

				// All Gew Grid classes - Backwards
				// 'span12' => 'c6-123456',
				// 'span12' => 'c4-1234',

				// 'span10' => 'c6-12345',
				// 'span10' => 'c6-23456',

				// 'span9' => 'c4-123',
				// 'span9' => 'c4-234',

				// 'span8' => 'c6-1234',
				// 'span8' => 'c6-2345',
				// 'span8' => 'c6-3456',

				// 'span6' => 'c6-123',
				// 'span6' => 'c6-234',
				// 'span6' => 'c6-345',
				// 'span6' => 'c6-456',
				// 'span6' => 'c4-12',
				// 'span6' => 'c4-23',
				// 'span6' => 'c4-34',

				// 'span4' => 'c6-12',
				// 'span4' => 'c6-23',
				// 'span4' => 'c6-34',
				// 'span4' => 'c6-45',
				// 'span4' => 'c6-56',

				// 'span3' => 'c4-1',
				// 'span3' => 'c4-2',
				// 'span3' => 'c4-3',
				// 'span3' => 'c4-4',

				// 'span2' => 'c6-1',
				// 'span2' => 'c6-2',
				// 'span2' => 'c6-3',
				// 'span2' => 'c6-4',
				// 'span2' => 'c6-5',
				// 'span2' => 'c6-6'
		);
		
		public function __construct() {
				foreach ($this->old_row_classname_to_new as $new => $old) {
						$this->push_row_class_change($old, $new);
				}
				foreach ($this->old_block_classname_to_new as $new => $old) {
						$this->push_block_class_change($old, $new);
				}
				
				return $this;
		}
		
		public function attach_hooks() {
				// Restore generated block ID class
				add_filter(
						'cfct-generated-block-classes',
						array($this, 'add_block_id_class'),
						10, 2
				);
				
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
						'cfct-block-template',
						array($this, 'restore_block_template'),
						10, 2
				);
				
				/** 
				Review
				 **/
				/* We still use the old-school row filter keys to avoid
				breaking backwards compat with filters. */
				$row_class_filters = array(
						'cfct-row-abc-classes', // cfct-block-c4-1234-classes ?
						'cfct-row-d-e-classes', // cfct-block-c4-12-34-classes ?
						'cfct-row-a-bc-classes', // row-c6-12-3456 ?
						'cfct-row-ab-c-classes', // row-c6-1234-56 ?
						'cfct-row-a-b-c-classes', // row-c6-12-34-56 ?
						'cfct-row-float-c-classes', // row-c6-12-3456 row-c6-12-float-left ?
						'cfct-row-float-a-classes' // row-c6-1234-56 row-c6-56-float-right ?
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
						// 'cfct-block-c4-1234-classes',
						
						// /* Halves */
						// 'cfct-block-c4-12-classes',
						// 'cfct-block-c4-34-classes',
						
						// /* Thirds */
						// 'cfct-block-c6-12-classes',
						// 'cfct-block-c6-34-classes',
						// 'cfct-block-c6-56-classes',
						
						// /* 2 Thirds */
						// 'cfct-block-c6-1234-classes',
						// 'cfct-block-c6-3456-classes'

						/* Full */
						'cfct-block-span9-classes',

						/* 5 Sixths */
						'cfct-block-span10-classes',
						
						/* 3 Quarters */
						'cfct-block-span9-classes',
						
						/* 2 Thirds */
						'cfct-block-span8-classes',

						/* Halves */
						'cfct-block-span6-classes',

						/* Thirds */
						'cfct-block-span4-classes',

						/* Quarters */
						'cfct-block-span3-classes',

						/* Sixths */
						'cfct-block-span2-classes'
				);
				
				foreach($block_class_filters as $filter_key) {
						add_filter(
								$filter_key,
								array($this, 'restore_block_classes'),
								10, 2
						);
				}
				
				/** 
				Review
				 **/
				// Also reversed source-order to keep things simple
				add_filter(
						'cfct-block-c6-12-floated-classes',
						array($this, 'restore_c6_12_floated_classes'),
						10, 2
				);
				add_filter(
						'cfct-block-c6-3456-floated-classes',
						array($this, 'restore_c6_3456_floated_classes'),
						10, 2
				);
				
				add_filter(
						'cfct-block-c6-56-floated-classes',
						array($this, 'restore_c6_56_floated_classes'),
						10, 2
				);
				add_filter(
						'cfct-block-c6-1234-floated-classes',
						array($this, 'restore_c6_1234_floated_classes'),
						10, 2
				);
		}
		
		/** 
		Review
		**/
		/**
		 * Backwards-compat block ID class
		 */
		public function add_block_id_class($generated_classes, $block_id) {
				// add a block class that tells us which numeric position he is
				$generated_classes[] = 'block-'.$block_id;
				return $generated_classes;
		}
		
		/** 
		Review
		**/
		/**
		 * Backwards-compat row markup
		 */
		public function restore_row_html($html, $classname, $classes) {
				return '<div id="{id}" class="{class}">{blocks}</div>';
		}
		
		/** 
		Review
		**/
		/**
		 * Brings back block ID
		 */
		public function restore_block_template($html, $block_instance) {
				return '<div id="{id}" class="{class}">{modules}</div>';
		}
		
		public function push_row_class_change($old, $new) {
				$this->row_classes_change_map[] = array(
						'old' => cfct_tpl::extract_classes($old),
						'new' => cfct_tpl::extract_classes($new)
				);
		}
		
		public function push_block_class_change($old, $new) {
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
						
						$intersect = array_intersect($classes, $pair['new']);
						/* Does the row have the same classes that we've recorded as new?
						Then it's a match, so add the equivalent old classes. */
						if (count($intersect) == count($pair['new'])) {
								$classes = array_merge($classes, $pair['old']);
						}
				}
				
				return cfct_tpl::clean_classes($classes);
		}
		
		public function restore_row_classes($classes, $row_instance) {
				$classes = $this->restore_classes(
						$this->row_classes_change_map, $classes
				);
				return $classes;
		}
		
		public function restore_block_classes($classes, $block_instance) {
				$classes = $this->restore_classes(
						$this->block_classes_change_map, $classes
				);
				return $classes;
		}
		
		/** 
		Review
		**/
		// Weird block classes
		
		public function restore_c6_12_floated_classes($classes, $block_instance) {
				$classes[] = 'cfct-block-float-a';
				$classes[] = 'cfct-block-a';
				return $classes;
		}
		
		public function restore_c6_3456_floated_classes($classes, $block_instance) {
				$classes[] = 'cfct-block-float-abc';
				$classes[] = 'cfct-block-bc';
				return $classes;
		}
		
		public function restore_c6_56_floated_classes($classes, $block_instance) {
				$classes[] = 'cfct-block-float-c';
				$classes[] = 'cfct-block-c';
				return $classes;
		}
		
		public function restore_c6_1234_floated_classes($classes, $block_instance) {
				$classes[] = 'cfct-block-float-abc';
				$classes[] = 'cfct-block-ab';
				return $classes;
		}
		
		/**
		 * A bit janky, but since we don't have error handling in WP,
		 * do a feature check to make sure this version of Build is compatible with
		 * this plugin.
		 */
		public static function check_features() {
				if (!function_exists('cfct_build')) {
						return new WP_Error('function not found', 'Carrington Build needs to be activated for \"Restore Deprecated Build Settings\" to take effect.');
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
//                 $diagnostics = self::check_features();
//                 if (is_wp_error($diagnostics)) {
//                         $cb = create_function('', 'echo "<div class=\'message error\'>
//         <p>'.$diagnostics->get_error_message().'</p>
// </div>";');
//                         add_action('admin_notices', $cb);
//                 }
//                 else {
						$instance = new CFCT_Build_Bootstrap();
						$instance->attach_hooks();
				//}
		}
}
add_action('init', array('CFCT_Build_Bootstrap', 'init'));
?>