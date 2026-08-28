<?php

namespace AgileStoreLocator\Frontend;

use AgileStoreLocator\Activator;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * The public-facing functionality of the plugin is for the AJAX Requests.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/frontend
 * @author     AgileLogix <support@agilelogix.com>
 */

class Request {


	/**
	 * [load_stores Load the Stores using AJAX Request]
	 * @return [type] [description]
	 */
	public function load_stores($output_return = false, $_lang = null) {

		global $wpdb;

		$nonce = isset($_GET['nonce'])? $_GET['nonce']: null;
		//$this->add_test_stores();die;
		
		$load_all 	 = (isset($_REQUEST['load_all']) && $_REQUEST['load_all'] == '1' || $output_return)?	true:false;
		$accordion   = (isset($_REQUEST['layout']) && $_REQUEST['layout'] == '1')?true:false;
		$category    = (isset($_REQUEST['category']))? sanitize_text_field($_REQUEST['category']):null;
		$exclude_categories = (isset($_REQUEST['exclude_categories']))? sanitize_text_field($_REQUEST['exclude_categories']):null;
		$stores      = (isset($_REQUEST['stores']))? sanitize_text_field($_REQUEST['stores']):null;
		$lang      	 = (isset($_REQUEST['asl_lang']))? sanitize_text_field($_REQUEST['asl_lang']): '';
		$meta_key    = (isset($_REQUEST['asl_meta_key']))? sanitize_text_field($_REQUEST['asl_meta_key']): '';
		$meta_val    = (isset($_REQUEST['asl_meta_val']))? sanitize_text_field($_REQUEST['asl_meta_val']): null;
		$branches    = (isset($_REQUEST['branches']))? true: false;


		//	Get the fields
		$ddl_fields  = \AgileStoreLocator\Model\Attribute::get_fields();
	
		$ddl_filters = [];

		foreach($ddl_fields as $ddl_field) {

			$ddl_filters[$ddl_field] = (isset($_REQUEST[$ddl_field]))? sanitize_text_field($_REQUEST[$ddl_field]):null;	
		}

		// ddl_fields in the query
    $ddl_fields_str = \AgileStoreLocator\Model\Attribute::sql_query_fields();
		

		$address_filter = [
			'title'     	=> (isset($_REQUEST['title']))? sanitize_text_field($_REQUEST['title']): null,
			'state'     	=> (isset($_REQUEST['state']))? sanitize_text_field($_REQUEST['state']): null,
			'postal_code'	=> (isset($_REQUEST['postal_code']))? sanitize_text_field($_REQUEST['postal_code']): null,
			'city' 				=> (isset($_REQUEST['city']))? sanitize_text_field($_REQUEST['city']): null,
			'country' 		=> (isset($_REQUEST['country']))? sanitize_text_field($_REQUEST['country']): null
		];

		//	Link type we replace the website with the slug
		$slug_link   = (isset($_GET['slug_link']))?true:false;

		$ASL_PREFIX  = ASL_PREFIX;

		$bound   				= '';

		$join_sql 			= '';
		$country_field 	= '';

		//	Cache Lang
		if($_lang) {
			$lang = $_lang;
		}
		

		//Load on bound :: no Load all
		if(!$load_all && isset($_GET['nw']) && isset($_GET['se'])) {
			
			$nw     =  $_GET['nw'];
      $se     =  $_GET['se'];

      $a      = floatval($nw[0]);
      $b      = floatval($nw[1]);

      $c      = floatval($se[0]);
      $d      = floatval($se[1]);
	    

			$bound   = "AND (($a < $c AND s.lat BETWEEN $a AND $c) OR ($c < $a AND s.lat BETWEEN $c AND $a))
                  AND (($b < $d AND s.lng BETWEEN $b AND $d) OR ($d < $b AND s.lng BETWEEN $d AND $b))";
    }
    else {

		$country_field = " {$ASL_PREFIX}countries.`country`, {$ASL_PREFIX}countries.`iso_code_2` AS `country_code`,";
		$join_sql      = "LEFT JOIN {$ASL_PREFIX}countries ON s.`country` = {$ASL_PREFIX}countries.id";
    }
    

		$clause = '';
		$the_categories = [];
		$store_ids = [];

	    if($category) {

			$load_categories = explode(',', $category);
			$the_categories  = array();

			foreach($load_categories as $_c) {

				//	Clean it
				if(ctype_digit(strval($_c))) {
					$the_categories[] = $_c;
				}
			}

				if(count($the_categories) > 0) {

					$the_categories  = implode(',', $the_categories);
					$category_clause = " AND id IN (".$the_categories.')';
					$clause 		    .= " AND {$ASL_PREFIX}stores_categories.`category_id` IN (".$the_categories.")";
				}
			}

			if($exclude_categories) {

				$load_categories = explode(',', $exclude_categories);
				$excluded_categories = array();

				foreach($load_categories as $_c) {

					//	Clean it
					if(ctype_digit(strval($_c))) {
						$excluded_categories[] = $_c;
					}
				}

				if(count($excluded_categories) > 0) {

					$excluded_categories = implode(',', $excluded_categories);
					$clause .= " AND s.`id` NOT IN (SELECT store_id FROM {$ASL_PREFIX}stores_categories WHERE `category_id` IN (".$excluded_categories."))";
				}
			}


    // If marker param exist
		if($stores) {

			$stores = explode(',', $stores);

			//only number
			$store_ids = array();
			foreach($stores as $m) {

				if(ctype_digit(strval($m))) {
					$store_ids[] = $m;
				}
			}

			if($store_ids) {

				$store_ids = implode(',', $store_ids);
				$clause    .= " AND s.`id` IN ({$store_ids})";				
			}
		}


		//	Apply the where clause for the ddl_filter
		foreach($ddl_filters as $filter_key => $filter_value) {

			if($filter_value) {

				//  Clean the values
	      $filter_value = explode(',', $filter_value);
	      $filter_value = array_map( 'absint', $filter_value );
	      
	      //	When we have values
	      if($filter_value) {

	      	$conditions 	  = array_map(function($value) use ($filter_key) { return "FIND_IN_SET('$value', s.`$filter_key`)"; }, $filter_value);
					$clause 			 .= " AND (".implode(' OR ', $conditions).')';
	      }
			}
		}

		//	Add the branch Clauses in the query
		$branch_field = '';
		$branch_join 	= '';


		$meta_fields  = '';
		$meta_join  	= '';


		//	Filter by Meta
		if (preg_match('/^shipping_id_\d+$/', $meta_key) && ctype_digit(strval($meta_val))) {

			$join_sql   .= " LEFT JOIN {$ASL_PREFIX}stores_meta m ON s.id = m.store_id AND m.option_name = '$meta_key'";
			$clause  		.= "AND m.`option_value`  = $meta_val";
		}
		
		//	When we have branches enabled
		if($branches) {

			$branch_field = "GROUP_CONCAT(DISTINCT m.`store_id`) AS 'childs',";
			$branch_join  = "LEFT JOIN (SELECT option_value, store_id  FROM `{$ASL_PREFIX}stores_meta` WHERE  option_name = 'p_id') m ON s.id = m.option_value";
		}

		$query_params = [$lang];

		$query   = "SELECT s.`id`, `title`, {$branch_field} `description`, `street`,  `city`,  `state`, `postal_code`, {$country_field} `lat`,`lng`,`phone`,  `fax`,`email`,`website`,`logo_id`,{$ASL_PREFIX}storelogos.`path`,`marker_id`,`description_2`,`open_hours`, `ordr`, `custom`,`slug`,$ddl_fields_str,
					group_concat(DISTINCT category_id) as categories FROM {$ASL_PREFIX}stores as s 
					$branch_join
					LEFT JOIN {$ASL_PREFIX}storelogos ON logo_id = {$ASL_PREFIX}storelogos.id
					LEFT JOIN {$ASL_PREFIX}stores_categories ON s.`id` = {$ASL_PREFIX}stores_categories.store_id
					$join_sql
					WHERE (s.`pending` IS NULL OR s.`pending` = '') AND s.`lang` = %s AND (is_disabled is NULL OR is_disabled = 0) AND (`lat` != '' AND `lng` != '') {$bound} {$clause}";

		///	Address Filter Clause
		foreach ($address_filter as $addr_attr => $addr_value) {
			
			//	Country clause
			if($addr_attr == 'country') {
				$addr_value = \AgileStoreLocator\Model\Countries::get_country_id($addr_value);
			}

			if($addr_value) {

				$query  .= " AND `s`.`$addr_attr` = %s";
				$query_params[] = sanitize_text_field($addr_value); 
			}
		}


		//	call the prepare for the values, as they are strings
		if(count($query_params) > 0) {
			$query = $wpdb->prepare($query, $query_params);
		}

		//	Modify the Stores to add Where Clause
		$query  = apply_filters( 'asl_filter_stores_query', $query);

		//	add a limit of 25K
		$query .= " GROUP BY s.`id` ORDER BY `title` LIMIT 30000;";
	
		//	Modify the Stores Load Qery in the last
		$query  = apply_filters( 'asl_filter_stores_query_full', $query);		


		$all_results = $wpdb->get_results($query);

		$debug_error = false;

		if($debug_error) {

			$err_message = isset($wpdb->last_error)? $wpdb->last_error: null;
			
			if(!$all_results && $err_message) {

				$database = $wpdb->dbname;

				//  Check if the new columns are there or not
	      $sql  = "SELECT count(*) as c FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$ASL_PREFIX}stores' AND COLUMN_NAME = 'lang' AND TABLE_SCHEMA = '{$database}'";
	      $col_check_result = $wpdb->get_results($sql);
	      
	      if($col_check_result[0]->c == 0) {
	          
	          Activator::activate();
	      }

				echo json_encode([$err_message]);die;
			}
		}
		

		$days_in_words 	= array('sun'=> asl_esc_lbl('sun'), 'mon'=> asl_esc_lbl('mon'), 'tue'=> asl_esc_lbl('tue'), 'wed'=> asl_esc_lbl('wed'),'thu'=> asl_esc_lbl('thu'), 'fri'=> asl_esc_lbl('fri'), 'sat'=> asl_esc_lbl('sat'));
		$days 		   		= array('mon','tue','wed','thu','fri','sat','sun');


		//	Only fetch the config when link type is set to rewrite
		$slug_url = '';

		if($slug_link) {

			$rewrite_config = \AgileStoreLocator\Helper::get_configs(['rewrite_slug', 'rewrite_id']);

			if(isset($rewrite_config['rewrite_slug']) && $rewrite_config['rewrite_slug'] && $rewrite_config['rewrite_id']) {

				$slug_url = '/'.$rewrite_config['rewrite_slug'].'/';
			}
			//	rewrite data is incomplete
			else {

				$slug_link = null;
			}
		}

		// Get the custom fields
		$custom_fields = \AgileStoreLocator\Helper::get_custom_fields();

		// Make them text textarea
		if (!empty($custom_fields) && is_array($custom_fields)) {

			foreach ($custom_fields as $key => $field) {
				$custom_fields[$key]['is_textarea'] = isset($field['type']) && in_array($field['type'], ['textarea', 'richtext']);
			}
		}
		

		//	Loop over the rows
		foreach($all_results as $aRow) {

			if($aRow->description) {
				$aRow->description 	 = str_replace("\n", "<br>", $aRow->description);
			}

			if($aRow->description_2) {
				$aRow->description_2 = str_replace("\n", "<br>", $aRow->description_2);
			}

			//	Sanitize the Store
			$aRow = \AgileStoreLocator\Helper::sanitize_store($aRow);

			if($aRow->open_hours) {

				$days_are 	= array();
				$open_hours = json_decode($aRow->open_hours);

				foreach($days as $day) {

					if(!empty($open_hours->$day)) {
						$days_are[] = $days_in_words[$day];
					}
				}

				$aRow->days_str = implode(', ', $days_are);
			}


			//	Decode the Custom Fields
			if($custom_fields && $aRow->custom) {

				$custom_fields_data = json_decode($aRow->custom, true);

				// Loop over the custom fields
				foreach($custom_fields as $custom_key => $_field) {

					//	When we have custom field data
					if(isset($custom_fields_data[$custom_key])) {

						//	Replace the new line with <br>
						//$aRow->$custom_key = str_replace("\n", "<br>", wp_kses_post($custom_fields_data[$custom_key]));

						// Escape the custom field data
						$aRow->$custom_key = ($_field['is_textarea'])? wp_kses_post($custom_fields_data[$custom_key]): esc_attr($custom_fields_data[$custom_key]);
					}
				}
			}

			//	Country translation
			if(isset($aRow->country)) {
				$aRow->country = esc_attr__($aRow->country, 'asl_locator');
			}

			unset($aRow->custom);
	  }

	  //	apply the filter before JSON is sent
	  $filter_context = [
	  	'request'        => $_REQUEST,
	  	'load_all'       => $load_all,
	  	'accordion'      => $accordion,
	  	'category_ids'   => $the_categories,
	  	'store_ids'      => $store_ids,
	  	'language'       => $lang,
	  	'meta_key'       => $meta_key,
	  	'meta_val'       => $meta_val,
	  	'branches'       => $branches,
	  	'ddl_filters'    => $ddl_filters,
	  	'address_filter' => $address_filter,
	  	'bound'          => $bound,
	  	'query'          => $query,
	  	'query_params'   => $query_params,
	  	'slug_link'      => $slug_link,
	  ];
		$all_results   = apply_filters( 'asl_filter_stores_result', $all_results, $filter_context);

	  //	To Return the output object
	  if($output_return) {
	  	return $all_results;
	  }

		echo wp_json_encode($all_results);die;
	}


	/**
	 * [add_test_stores Not Used]
	 */
	private function add_test_stores() {
		
		global $wpdb;

		$file_ = '/home/dev/projects/wordpress_language/test.json';

		if(!file_exists($file_)) {

			die('file not found');
		}

		$content = file_get_contents($file_);
		$stores  = json_decode($content, true);

		
		foreach($stores as $store) {

			$store 		  = $store;
			$categories = $store['categories'];

			unset($store['id']);
			unset($store['days_str']);
			unset($store['categories']);
			unset($store['path']);
			unset($store['mobile']);
			unset($store['video']);

			$custom = [];
			$custom['n_arztname'] = $store['n_arztname'];


			$store['custom'] = json_encode($custom);

			$countries     = $wpdb->get_results("SELECT id,country FROM ".ASL_PREFIX."countries");
			$all_countries = array();

			foreach($countries as $_country) {

				$all_countries[$_country->country] = $_country->id;
			}

			$store['country'] = (isset($all_countries[$store['country']]))?$all_countries[$store['country']]:'222';
			
			
			if($wpdb->insert( ASL_PREFIX.'stores', $store)) {

				$store_id = $wpdb->insert_id;

				$categories = explode(',', $categories);

				foreach ($categories as $category) {

					$wpdb->insert(ASL_PREFIX.'stores_categories', 
					 	array('store_id'=>$store_id,'category_id'=>$category),
					 	array('%s','%s'));			
				}
			}
			else {

				$wpdb->show_errors = true;

				die($wpdb->print_error());
			}
		}

		die('all done');
	}

	/**
   * [fixURL Add https:// to the URL]
   * @param  [type] $url    [description]
   * @param  string $scheme [description]
   * @return [type]         [description]
   */
  private function fixURL($url, $scheme = 'http://') {

    if(!$url)
      return '';

    return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
  }


	/**
	 * [debug Private debug]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	private function debug($data) {

		echo '<pre>';
		print_r($data);
		echo '</pre>';
		die;
	}

}
