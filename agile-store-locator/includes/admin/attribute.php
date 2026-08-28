<?php

namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AgileStoreLocator\Admin\Base;

/**
 * Attribute Manager for Brand & Special or any other dropdown
 *
 * @link       https://agilestorelocator.com
 * @since      4.7.32
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/Attribute
 */

class Attribute extends Base
{
    private $attr_tables;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->attr_tables = \AgileStoreLocator\Model\Attribute::get_controls_keys();

        parent::__construct();
    }

    /**
     * [delete_attribute Delete Attribute]
     * @return [type] [description]
     */
    public function delete_attribute()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $table  = isset($_REQUEST['name']) ? sanitize_text_field($_REQUEST['name']) : null;
        $title  = isset($_REQUEST['title']) ? sanitize_text_field($_REQUEST['title']) : null;
        $value  = isset($_REQUEST['value']) ? sanitize_text_field($_REQUEST['value']) : null;

        $multiple = isset($_REQUEST['multiple']) ? $_REQUEST['multiple'] : null;
        $delete_sql;
        $cResults;

        //  To filter the table name
        $table = (in_array($table, $this->attr_tables)) ? $table : $this->attr_tables[0];

        if ($multiple) {
            //  Clean it
            $item_ids      = implode(',', array_map('intval', $_POST['item_ids']));

            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . $table . ' WHERE id IN (' . $item_ids . ')';
            $cResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . $table . ' WHERE id IN (' . $item_ids . ')');
        } else {
            $category_id   = intval($_REQUEST['category_id']);

            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . $table . ' WHERE id = ' . $category_id;
            $cResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . $table . ' WHERE id = ' . $category_id);
        }

        if (count($cResults) != 0) {
            if ($wpdb->query($delete_sql)) {
                if ($table === 'brands') {
                    $deleted_brand_ids = array_map(function($row) { return absint($row->id); }, $cResults);
                    $deleted_brand_ids = array_filter($deleted_brand_ids);

                    if (!empty($deleted_brand_ids)) {
                        $wpdb->query('UPDATE ' . ASL_PREFIX . 'specials SET brand_id = NULL WHERE brand_id IN (' . implode(',', $deleted_brand_ids) . ')');
                    }
                }

                $response->success = true;
            } else {
                $response->error = esc_attr__('Error occurred while deleting record', 'asl_locator');
                $response->msg   = $wpdb->show_errors();
            }
        } else {
            $response->error = esc_attr__('Error occurred while deleting record', 'asl_locator');
        }

        if ($response->success) {
            $response->msg = $title . ' ' . esc_attr__('deleted successfully', 'asl_locator');
        }

        return $this->send_response($response);
    }

    /**
     * [add_attribute description]
     */
    public function add_attribute()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $table  = isset($_REQUEST['name']) ? sanitize_text_field($_REQUEST['name']) : null;
        $title  = isset($_REQUEST['title']) ? sanitize_text_field($_REQUEST['title']) : null;
        $value  = isset($_REQUEST['value']) ? sanitize_text_field($_REQUEST['value']) : null;
        $ordr   = isset($_REQUEST['ordr']) && is_numeric($_REQUEST['ordr']) ? $_REQUEST['ordr'] : 0;
        $brand_id = isset($_REQUEST['brand_id']) && is_numeric($_REQUEST['brand_id']) ? absint($_REQUEST['brand_id']) : null;

        //  Filter the Table Name
        $table = (in_array($table, $this->attr_tables)) ? $table : $this->attr_tables[0];

        $value = stripslashes($value);

        $insert_data = ['name' => $this->clean_input($value), 'ordr' => $ordr, 'lang' => $this->lang];

        if ($table === 'specials') {
            $insert_data['brand_id'] = $brand_id ?: null;
        }

        if ($value && $wpdb->insert(ASL_PREFIX . $table, $insert_data)) {
            $response->msg     = $title . esc_attr__(' added successfully', 'asl_locator');
            $response->success = true;
            $response->id      = $wpdb->insert_id;
        } else {
            $response->msg = esc_attr__('Error occurred while saving record', 'asl_locator');
        }

        return $this->send_response($response);
    }

    /**
     * [update_attribute description]
     * @return [type] [description]
     */
    public function update_attribute()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $table  = isset($_REQUEST['name']) ? sanitize_text_field($_REQUEST['name']) : null;
        $title  = isset($_REQUEST['title']) ? sanitize_text_field($_REQUEST['title']) : null;
        $value  = isset($_REQUEST['value']) ? sanitize_text_field($_REQUEST['value']) : null;
        $at_id  = isset($_REQUEST['id']) ? sanitize_text_field($_REQUEST['id']) : null;
        $ordr   = isset($_REQUEST['ordr']) && is_numeric($_REQUEST['ordr']) ? sanitize_text_field($_REQUEST['ordr']) : 0;
        $brand_id = isset($_REQUEST['brand_id']) && is_numeric($_REQUEST['brand_id']) ? absint($_REQUEST['brand_id']) : null;

        //  Filter the Table Name
        $table = (in_array($table, $this->attr_tables)) ? $table : $this->attr_tables[0];

        $value = stripslashes($value);

        $update_data = ['name' => $this->clean_input($value), 'ordr' => $ordr];

        if ($table === 'specials') {
            $update_data['brand_id'] = $brand_id ?: null;
        }

        if ($at_id && $value && $wpdb->update(ASL_PREFIX . $table, $update_data, ['id' => $at_id]) !== false) {
            $response->msg     = $title . ' ' . esc_attr__('Updated Successfully', 'asl_locator');
            $response->success = true;
        } else {
            $response->msg = esc_attr__('Error occurred while saving record', 'asl_locator');
        }

        return $this->send_response($response);
    }

    /**
     * [get_attributes Get the Attribute]
     * @return [type] [description]
     */
    public function get_attributes()
    {
        global $wpdb;

        // Pagination and table request
        $start  = isset($_REQUEST['iDisplayStart']) ? intval($_REQUEST['iDisplayStart']) : 0;
        $length = isset($_REQUEST['iDisplayLength']) && $_REQUEST['iDisplayLength'] != '-1'
                  ? intval($_REQUEST['iDisplayLength']) : 10;
        $sEcho  = isset($_REQUEST['sEcho']) ? intval($_REQUEST['sEcho']) : 1;
        $table  = isset($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : null;

        // Validate requested table against allowed attribute tables
        if (!$table || !in_array($table, $this->attr_tables, true)) {
            $table = $this->attr_tables[0]; // fallback to default safe table
        }

        // Define and whitelist columns
        $is_specials     = ($table === 'specials');
        $acolumns        = $is_specials ? ['s.id', 's.id', 's.name', 'b.name', 's.ordr', 's.created_on'] : ['id', 'id', 'name', 'ordr', 'created_on'];
        $allowed_columns = $is_specials ? ['id', 'name', 'brand_id', 'ordr', 'created_on'] : ['id', 'name', 'ordr', 'created_on'];

        $clause     = [];
        $sql_params = [];

        // Filtering with validation and SQL injection protection
        if (isset($_REQUEST['filter']) && is_array($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if (!$key || !$value || $key === 'undefined' || $value === 'undefined') {
                    continue;
                }

                $key   = sanitize_text_field($key);
                $value = sanitize_text_field($value);

                if (in_array($key, $allowed_columns, true)) {
                    if ($is_specials && $key === 'brand_id') {
                        $clause[]     = 'b.`name` LIKE %s';
                        $sql_params[] = '%' . $wpdb->esc_like($value) . '%';
                    } else {
                        $column_prefix = $is_specials ? 's.' : '';
                        $clause[]      = "{$column_prefix}`$key` LIKE %s";
                        $sql_params[]  = '%' . $wpdb->esc_like($value) . '%';
                    }
                }
            }
        }

        // Always filter by language
        $clause[]     = ($is_specials ? 's.' : '').'`lang` = %s';
        $sql_params[] = $this->lang;

        $sWhere = $clause ? 'WHERE ' . implode(' AND ', $clause) : '';
        $sLimit = "LIMIT $start, $length";

        // Sorting with safety checks
        $sOrder = '';
        if (isset($_REQUEST['iSortCol_0']) && isset($_REQUEST['iSortingCols'])) {
            for ($i = 0; $i < intval($_REQUEST['iSortingCols']); $i++) {
                $col_index = intval($_REQUEST['iSortCol_' . $i]);
                $sort_dir  = (isset($_REQUEST['sSortDir_' . $i]) && strtolower($_REQUEST['sSortDir_' . $i]) === 'asc') ? 'ASC' : 'DESC';

                if (isset($acolumns[$col_index])) {
                    $order_column = $acolumns[$col_index];
                    $safe_order_columns = $is_specials ? ['s.id', 's.name', 'b.name', 's.ordr', 's.created_on'] : $allowed_columns;

                    if (in_array($order_column, $safe_order_columns, true)) {
                        $sOrder = (strpos($order_column, '.') !== false) ? "ORDER BY {$order_column} $sort_dir" : "ORDER BY `{$order_column}` $sort_dir";
                    }
                    break;
                }
            }
        }

        // Final table name
        $db_table = ASL_PREFIX . $table;
        $fields   = $is_specials ? 's.id, s.id AS check_id, s.name, COALESCE(b.name, "") AS brand_name, s.brand_id, s.ordr, s.created_on' : implode(',', $acolumns);

        // Data query
        $sql         = $is_specials ? "SELECT $fields FROM $db_table s LEFT JOIN ".ASL_PREFIX."brands b ON s.brand_id = b.id" : "SELECT $fields FROM $db_table";
        $data_query  = "$sql $sWhere $sOrder $sLimit";
        $data_output = $wpdb->get_results($wpdb->prepare($data_query, ...$sql_params));

        // Trigger plugin activator if a table column is missing
        if (!$data_output && $wpdb->last_error) {
            \AgileStoreLocator\Activator::activate();
        }

        // Count query
        $sqlCount       = $is_specials ? "SELECT COUNT(*) as count FROM $db_table s LEFT JOIN ".ASL_PREFIX."brands b ON s.brand_id = b.id" : "SELECT COUNT(*) as count FROM $db_table";
        $count_query    = "$sqlCount $sWhere";
        $r              = $wpdb->get_results($wpdb->prepare($count_query, ...$sql_params));
        $iFilteredTotal = isset($r[0]->count) ? intval($r[0]->count) : 0;

        // Output format
        $output = [
            'sEcho'                => $sEcho,
            'iTotalRecords'        => $iFilteredTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData'               => []
        ];

        // Row formatting
        foreach ($data_output as $row) {
            $row->name = isset($row->name) ? esc_html($row->name) : '';

            $brand_id_attr = $is_specials ? ' data-brand-id="' . esc_attr($row->brand_id) . '"' : '';
            $row->brand_name = $is_specials ? esc_html($row->brand_name) : '';
            $row->action = '<div class="edit-options">
            <a data-ordr="' . esc_attr($row->ordr) . '" data-value="' . esc_attr($row->name) . '" data-id="' . esc_attr($row->id) . '"' . $brand_id_attr . ' title="Edit" class="edit_attr"><svg width="14" height="14"><use xlink:href="#i-edit"></use></svg></a>
            <a title="Delete" data-id="' . esc_attr($row->id) . '" class="delete_attr g-trash"><svg width="14" height="14"><use xlink:href="#i-trash"></use></svg></a>
        </div>';

            $row->check = '<div class="custom-control custom-checkbox">
            <input type="checkbox" data-id="' . esc_attr($row->id) . '" class="custom-control-input" id="asl-chk-' . esc_attr($row->id) . '">
            <label class="custom-control-label" for="asl-chk-' . esc_attr($row->id) . '"></label>
        </div>';

            $output['aaData'][] = $row;
        }

        return $this->send_response($output);
    }
}
