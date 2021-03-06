<?php
/*
Plugin Name: Piklist Datatables
Description: Adds datatable field to piklist, with and without ajax support
Version: 0.0.2
Author: Manuel Abadía
Plugin Type: Piklist
Text Domain: piklist-datatables
License: GPL2
*/

// if accessed directly, exit
if (!defined('ABSPATH')) {
	exit;
}

/**
 * The Piklist Datatables Plugin class
 */
class Piklist_Datatables_Plugin {
	private static $inst = null;

	/**
	 * Returns the one and only instance of this class
	 *
	 * @since 0.0.2
	 */
	public static function Instance()
    {
        if (self::$inst === null) {
			self::$inst = new self();

			// piklist plugin check
			add_action('init', array(self::$inst, 'check_for_piklist'));

			// scripts/styles registration
			add_filter('piklist_field_assets', array(self::$inst, 'field_assets'));

			// datatables behaviour
			add_filter("piklist_request_field", array(self::$inst, 'request_field'));
			add_filter("piklist_pre_render_field", array(self::$inst, 'pre_render_field'));
        }

        return self::$inst;
    }

	/**
	 * Private Constructor
	 *
	 * @since 0.0.1
	 */
	private function __construct() {
	}

	/**
	 * Checks that piklist is installed
	 *
	 * @return void
	 * @since 0.0.1
	 */
	function check_for_piklist(){
		if(is_admin()){
			include_once(plugin_dir_path( __FILE__ ) . 'class-piklist-checker.php');
	
			if (!piklist_checker::check(__FILE__)){
				return;
			}
		}
	}

	/**
	 * Sets the callback to register the resources for the datatable type
	 *
	 * @param array $field_assets The fields with its corresponding assets
	 * @return array The updated array
	 * @since 0.0.1
	 */
	function field_assets($field_assets) {
		$field_assets['datatable'] = array('callback' => array(self::$inst, 'render_field_assets'));

		return $field_assets;
	}

	/**
	 * Registers the CSS and JS files required for the data tables to work properly
	 *
	 * @param string $type The field type
	 * @return void
	 * @since 0.0.1
	 */
	function render_field_assets($type) {
		// the chosen options at https://datatables.net/download/index were: 
		// 1) jQueryUI, 2) DataTables, 3) Buttons (HTML5->JsZip,Print view), FixedHeader, Responsive, RowGroup
		wp_enqueue_style('piklist-datatables', plugins_url('lib/css/datatables.min.css', __FILE__));

		wp_enqueue_script('piklist-datatables', plugins_url('lib/js/datatables.min.js', __FILE__), array('jquery'), false, true);
		wp_enqueue_script('piklist-datatables-setup', plugins_url('parts/js/datatables-setup.js', __FILE__), array('piklist-datatables'), false, true);

		/**
		* Notifies that is time to add additional assets related to the datatables field
		*
		* @since 0.0.2
		*/
		do_action('piklist_datatables_field_assets');
	}

	/**
	 * Performs the initialization for the datatable field
	 *
	 * @param array $field The settings for the field
	 * @return array The updated field
	 * @since 0.0.1
	 */
	function request_field($field) {
		if ($field['type'] == 'datatable') {
			foreach(array('config', 'query', 'columns', 'table_data') as $section) {
				if (!isset($field['options'][$section])) {
					$field['options'][$section] = array();
				}
			}

			// sets the default configuration options for non initialized entries
			static $default_config = array(
				'generate_footer' => null,			// generates the footer
				'enable_paging' => null,			// enables or disables pagination
				'enable_ordering' => null,			// enables or disables ordering of columns
				'enable_search' => null,			// enables or disables search
				'show_info' => null,				// shows information about the table including information about filtered data
				'order' => null,					// specifies the sorting order. 2D array with column index and sort order (f.e. [[ 3, 'desc' ]])
				'scroll_x' => null,					// allows scrolling in the x axis (boolean)
				'scroll_y' => null,					// sets the height for scrolling in the y axis (size string)
				'enable_responsive' => null,		// enables the responsive extension
				'fix_header' => null,				// fixes the header on the top of the screen while scrolling down the table
				'show_export_buttons' => null,		// to show the different export buttons
				'paging_type' => null,				// numbers, simple, simple_numbers, full, full_numbers, first_last_numbers
				'page_size' => null,				// the selected page size. If it is not set, it will be the first entry of the page_sizes property
				'page_sizes' => null,				// 1D array of integers with different page sizes. Use -1 for all. Use a 2D array for string translation
				'group_by_column' => null,			// the column index to use for grouping (columns start at 0)
				'style' => 'display',				// string with the style. Options are: cell-border, compact, hover, order-column, row-border, stripe
													// display = stripe hover order-column row-border

				'data_source_type' => 'field',		// sets one of the data source types: dom, json_var, ajax_client, ajax_server, field
				'data_source_param' => null,		// dom: an element selector, json_var: a variable name, ajax_client, ajax_server: an url

				'language' => null,					// languaje file to be used for the different messages displayed (see lib\js\i18n for the names)
			);

			/**
			* Filters the default config options
			*
			* @param array $default_config The default config parameters
			* @param array $field The settings for the field
			*
			* @since 0.0.1
			*/
			$config_options = apply_filters('piklist_datatables_default_config_options', $default_config, $field);

			$field['options']['config'] = wp_parse_args($field['options']['config'], $config_options);

        	// sets the default query options for non initialized entries. The  most common ones supported by the REST API are:
        	// order, orderby, include, exclude, before, after, slug, status, type. However, each type has its own particularities
        	static $default_query = array();

			/**
			* Filters the default query options
			*
			* @param array $default_query The default query parameters
			* @param array $field The settings for the field
			*
			* @since 0.0.1
			*/
			$query_options = apply_filters('piklist_datatables_default_query_options', $default_query, $field);

			// sets the authetification nonce to be able to access the user id in the rest requests
			if (!isset($query_options['_wpnonce'])) {
				$query_options['_wpnonce'] = wp_create_nonce('wp_rest');
			}

			$field['options']['query'] = wp_parse_args($field['options']['query'], $query_options);

			// sets the default column options for non initialized entries
        	static $default_column = array(
				'title' => null,					// the title to use for this column (required)
				'field_name' => null,				// the field to use for this columns
				'visible' => null,					// if the column will be visible
				'sortable' => null,					// if we can sort by this column
				'searchable' => null,				// if the search uses this column
				'width' => null,					// css value for the width
				'type' => null,						// the field type in case you want more control in client-side processing mode
				'render' => null,					// a javascript function to modify the data read from the data source
			);

			/**
			* Filters the default column options
			*
			* @param array $default_column The default column parameters
			* @param array $field The settings for the field
			*
			* @since 0.0.1
			*/
			$column_options = apply_filters('piklist_datatables_default_column_options', $default_column, $field);

			foreach($field['options']['columns'] as $key => $column) {
				$field['options']['columns'][$key] = wp_parse_args($field['options']['columns'][$key], $column_options);
			}
			
			// $field['options']['table_data'] contains the table data if we're in field mode
		}
		return $field;
	}

	/**
	 * The main functionality of the datatable field is here
	 *
	 * @param array $field The settings for the field
	 * @return array The updated field
	 * @since 0.0.1
	 */
	function pre_render_field($field) {
		if ($field['type'] == 'datatable') {

			$attributes =& $field['attributes'];
			$options =& $field['options'];

			if (($options['config']['data_source_type'] == 'ajax_server') || ($options['config']['data_source_type'] == 'ajax_client')) {
				if (!isset($options['config']['data_source_param'])) {
					$query_url = '/wp/v2/';
					$query_parameters = $options['query'];

					$query_entity = 'posts';

					if (isset($options['query']['type'])) {
						$query_entity = $options['query']['type'];
						unset($options['query']['type']);
					}

					$query_url = $query_url . $query_entity;

					$options['config']['data_source_param'] = get_home_url(null, '/wp-json') . $query_url;
				}

				$query_parameters = $options['query'];

				/**
				* Filters the parameters that will be passed to the REST request
				*
				* @param array $query_paramters The parameters read from the field configuration
				* @param array $field The settings for the field
				*
				* @since 0.0.1
				*/
				$query_parameters = apply_filters('piklist_datatables_rest_query_paramters', $query_parameters, $field);

				if (!empty(implode(null, $query_parameters))) {
					$options['config']['data_source_param'] = $options['config']['data_source_param'] . '?' . http_build_query($query_parameters);
				}
			}

			// if the height is smaller than the vertical scroll size, adjusts the height
			if (isset($options['config']['scroll_y'])) {
				$attributes['data-scroll-collapse'] = true;
			}

			// resolves the language plugin url if the language is set
			if (isset($options['config']['language'])) {
				$options['config']['language'] = plugins_url('lib/js/i18n/' . $options['config']['language'] . '.json', __FILE__);
			}

			array_push($attributes['class'], 'piklist-datatable');
			if (isset($options['config']['style'])) {
				array_push($attributes['class'], $options['config']['style']);
			}

			// column mappings
			static $column_mappings = array(
				'field_name' => 'data',
				'visible' => 'visible',
				'sortable' => 'orderable',
				'searchable' => 'searchable',
				'width' => 'width',
				'type' => 'type',
				'render' => 'render',
			);

			// saves the values to configure the columns
			$columns = array();
			foreach ($options['columns'] as $col) {
				$current_col = array();
				foreach($column_mappings as $key => $val) {
					if (isset($col[$key])) {
						$current_col[$val] = $col[$key];
					}
				}

				array_push($columns, $current_col ? $current_col : null);
			}
			if (!empty($columns)) {
				$attributes['data-the-columns'] = json_encode($columns);
			}

			// in field mode, we pass the data as data-* attributes
			if ($options['config']['data_source_type'] == 'field') {
				$attributes['data-data'] = json_encode($options['table_data']);
			}

			// the current implementation uses datatables but that could change in the future,
			// so we use friendly names for the configuration
			static $data_mappings = array(
				'enable_paging' => 'paging',
				'enable_ordering' => 'ordering',
				'enable_search' => 'searching',
				'show_info' => 'info',
				'order' => 'order',
				'scroll_x' => 'scroll-X',
				'scroll_y' => 'scroll-Y',
				'enable_responsive' => 'responsive',
				'fix_header' => 'fixed-header',
				'show_export_buttons' => 'show-export-buttons',
				'paging_type' => 'paging-type',
				'page_size' => 'page-length',
				'page_sizes' => 'length-menu',
				'group_by_column' => 'group-by-column',

				'data_source_type' => 'data-source-type',
				'data_source_param' => 'data-source-param',

				'language' => 'language-file',
			);

			// saves the data values to configure the field
			foreach($data_mappings as $key => $val) {
				if (isset($options['config'][$key])) {
					if (is_array($options['config'][$key])) {
						$attributes['data-' . $val] = json_encode($options['config'][$key]);
					} else {
						$attributes['data-' . $val] = $options['config'][$key];
					}
				}
			}
		}
		return $field;
	}
}

// creates the one an only instance of this plugin
Piklist_Datatables_Plugin::Instance();

include_once(plugin_dir_path( __FILE__ ) . 'includes/javascript-helpers.php');
?>