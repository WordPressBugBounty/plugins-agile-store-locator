<?php

namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AgileStoreLocator\Admin\Base;

/**
 * The category manager functionality of the admin.
 *
 * @link       https://agilestorelocator.com
 * @since      4.7.32
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/Category
 */

class Category extends Base
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
    }

    ////////////////////////////////
    /////////ALL Category Methods //
    ////////////////////////////////

    /**
     * [add_category Add Category Method]
     */
    public function add_category()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        //  Forms Data
        $form_data = isset($_REQUEST['data']) && is_array($_REQUEST['data'])
            ? stripslashes_deep($_REQUEST['data'])
            : [];

        $category_name = isset($form_data['category_name'])
            ? $this->clean_input($form_data['category_name'])
            : '';

        if ($category_name === '') {
            $response->msg = esc_attr__('Category name is required.', 'asl_locator');
            return $this->send_response($response);
        }

        // New categories appear after their siblings until an admin drags them elsewhere.
        $parent_id = isset($form_data['parent_id']) ? absint($form_data['parent_id']) : 0;
        $order_id = (int) $wpdb->get_var($wpdb->prepare(
            'SELECT COALESCE(MAX(ordr), -1) + 1 FROM ' . ASL_PREFIX . 'categories WHERE lang = %s AND parent_id = %d',
            $this->lang,
            $parent_id
        ));

        //  Parameters to Save
        $data_params = [
            'parent_id'     => $parent_id,
            'category_name' => $category_name,
            'ordr'          => $order_id,
            'color'         => $this->category_color($form_data['color'] ?? '')
        ];

        //  lang
        $data_params['lang']    = $this->lang;
        $data_params['icon']    = 'default.png';

        // Upload an icon when supplied; otherwise retain the shared default.
        $has_icon = isset($_FILES['files'])
            && isset($_FILES['files']['error'])
            && (int) $_FILES['files']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($has_icon) {
            $upload_result = $this->_file_uploader($_FILES['files'], 'svg');

            if (isset($upload_result['success']) && $upload_result['success']) {
                $data_params['icon'] = $upload_result['file_name'];
            } else {
                $response->msg = !empty($upload_result['error']) ? $upload_result['error'] : esc_attr__('Error! Failed to upload the image.', 'asl_locator');
                return $this->send_response($response);
            }
        }

        //  Insert the Category Record
        if ($wpdb->insert(ASL_PREFIX . 'categories', $data_params, ['%s', '%s', '%s'])) {
            $response->msg     = esc_attr__('Category added successfully', 'asl_locator');
            $response->data    = $data_params;
            $response->success = true;
        } else {
            $response->msg = esc_attr__('Error occurred while saving record', 'asl_locator'); //$form_data
        }

        return $this->send_response($response);
    }

    /**
     * [delete_category delete category/categories]
     * @return [type] [description]
     */
    public function delete_category()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $multiple = isset($_REQUEST['multiple']) ? $_REQUEST['multiple'] : null;
        $delete_sql;
        $cResults;

        if ($multiple) {
            $item_ids      = implode(',', array_map('intval', $_POST['item_ids']));
            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . 'categories WHERE id IN (' . $item_ids . ')';
            $cResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'categories WHERE id IN (' . $item_ids . ')');
        } else {
            $category_id   = intval($_REQUEST['category_id']);
            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . 'categories WHERE id = ' . $category_id;
            $cResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'categories WHERE id = ' . $category_id);
        }

        if (count($cResults) != 0) {
            if ($wpdb->query($delete_sql)) {
                $response->success = true;
                foreach ($cResults as $c) {
                    $inputFileName = ASL_UPLOAD_DIR . 'icon/' . sanitize_file_name($c->icon);

                    if (file_exists($inputFileName) && $c->icon != 'default.png') {
                        unlink($inputFileName);
                    }
                }
            } else {
                $response->error = esc_attr__('Error occurred while deleting record', 'asl_locator'); //$form_data
                $response->msg   = $wpdb->show_errors();
            }
        } else {
            $response->error = esc_attr__('Error occurred while deleting record', 'asl_locator');
        }

        if ($response->success) {
            $response->msg = ($multiple) ? __('Categories deleted successfully.', 'asl_locator') : esc_attr__('Category deleted successfully.', 'asl_locator');
        }

        return $this->send_response($response);
    }

    /**
     * [update_category update category with icon]
     * @return [type] [description]
     */
    public function update_category()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $data        = stripslashes_deep($_REQUEST['data']);

        //  Parameters to Save
        $data_params = ['category_name' => $this->clean_input($data['category_name']), 'parent_id' => $this->clean_input($data['parent_id']),
            'color' => $this->category_color($data['color'] ?? '')];

        $previous_parent = $wpdb->get_var($wpdb->prepare(
            'SELECT parent_id FROM ' . ASL_PREFIX . 'categories WHERE id = %d',
            absint($data['category_id'])
        ));
        if (null !== $previous_parent && (int) $previous_parent !== (int) $data_params['parent_id']) {
            $data_params['ordr'] = (int) $wpdb->get_var($wpdb->prepare(
                'SELECT COALESCE(MAX(ordr), -1) + 1 FROM ' . ASL_PREFIX . 'categories WHERE lang = %s AND parent_id = %d',
                $this->lang,
                (int) $data_params['parent_id']
            ));
        }

        // Have Icon to Update?
        if ($data['action'] == 'notsame') {
            //  Upload the Icon File
            $upload_result  = $this->_file_uploader($_FILES['files'], 'svg');

            //  Validate the Upload Success
            if (isset($upload_result['success']) && $upload_result['success']) {
                $file_name    = $upload_result['file_name'];

                //  Add the newly uploaded file
                $data_params['icon'] = $file_name;

                //  Delete the old icon if exist
                $old_icon     = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . ASL_PREFIX . 'categories WHERE id = %d', $data['category_id']));

                //  Delete the old file, if exist
                if ($old_icon[0]->icon !== 'default.png' && file_exists(ASL_UPLOAD_DIR . 'svg/' . $old_icon[0]->icon)) {
                    unlink(ASL_UPLOAD_DIR . 'svg/' . sanitize_file_name($old_icon[0]->icon));
                }
            } else {
                $response->msg      = ($upload_result['error']) ? $upload_result['error'] : esc_attr__('Error! Failed to upload the image.', 'asl_locator');
                return $this->send_response($response);
            }
        }

        $wpdb->update(ASL_PREFIX . 'categories', $data_params, ['id' => $data['category_id']]);
        $response->msg      = esc_attr__('Category updated successfully.', 'asl_locator');
        $response->post     = $data;
        $response->success  = true;

        return $this->send_response($response);
    }

    /**
     * [get_category_by_id get category by id]
     * @return [type] [description]
     */
    public function get_category_by_id()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $category_id = isset($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : 0;

        $response->item    = $wpdb->get_row('SELECT * FROM ' . ASL_PREFIX . "categories WHERE id = $category_id");

        $response->item->parent = $response->item->parent_id ? \AgileStoreLocator\Model\Category::get_parent('', $response->item->parent_id) : null;

        if ($response->item) {
            $response->success = true;
        } else {
            $response->error = esc_attr__('Error occurred while geting record', 'asl_locator'); //$form_data
        }
        return $this->send_response($response);
    }

    /** Accept solid six-digit hex colors only; an empty value means no custom color. */
    private function category_color($color)
    {
        $color = is_string($color) ? trim($color) : '';
        return preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? strtolower($color) : null;
    }

    /**
     * [get_categories GET the Categories]
     * @return [type] [description]
     */
    public function get_categories()
    {
        global $wpdb;

        $sEcho  = isset($_REQUEST['sEcho']) ? intval($_REQUEST['sEcho']) : 1;

        // The ordering view always loads the complete category list.
        $acolumns        = ['id', 'category_name', 'id', 'parent_id', 'icon', 'id'];
        $allowed_columns = ['id', 'category_name', 'icon', 'parent_id'];

        $clause     = [];
        $sql_params = [];

        // Filtering
        if (isset($_REQUEST['filter']) && is_array($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if (!$key || !$value || $key === 'undefined' || $value === 'undefined') {
                    continue;
                }

                $key   = sanitize_text_field($key);
                $value = sanitize_text_field($value);

                // Only allow filtering on whitelisted columns
                if (in_array($key, $allowed_columns, true)) {
                    $clause[]     = "`$key` LIKE %s";
                    $sql_params[] = '%' . $wpdb->esc_like($value) . '%';
                }
            }
        }

        // Always filter by language
        $clause[]     = '`lang` = %s';
        $sql_params[] = $this->lang;

        $sWhere = $clause ? 'WHERE ' . implode(' AND ', $clause) : '';

        $fields = implode(', ', $acolumns) . ', color, ordr, lang';
        $table  = ASL_PREFIX . 'categories';

        $sql       = "SELECT $fields FROM $table";
        $sqlCount  = "SELECT COUNT(*) as count FROM $table";

        // Get top-level categories for later use (parent_id = 0)
        $parent_categories = $wpdb->get_results($wpdb->prepare("SELECT `id`, `category_name` FROM $table WHERE `parent_id` = 0 AND `lang` = %s ORDER BY `ordr` ASC, `id` ASC", $this->lang));
        if (!count($parent_categories) && strpos($wpdb->last_error, 'parent_id') !== false) {
            \AgileStoreLocator\Activator::add_cat_parent_id();
        }

        // Prepare and execute data query
        $data_query   = "$sql $sWhere ORDER BY `parent_id` ASC, `ordr` ASC, `id` ASC";
        $data_query   = $sql_params ? $wpdb->prepare($data_query, ...$sql_params) : $data_query;
        $data_output  = $wpdb->get_results($data_query);
        $children_by_parent = [];
        foreach ($data_output as $row) {
            $children_by_parent[(int) $row->parent_id][] = $row;
        }
        $ordered_rows = [];
        $seen = [];
        $append_branch = function ($parent_id) use (&$append_branch, &$ordered_rows, &$seen, $children_by_parent) {
            foreach ($children_by_parent[$parent_id] ?? [] as $row) {
                if (isset($seen[$row->id])) continue;
                $seen[$row->id] = true;
                $ordered_rows[] = $row;
                $append_branch((int) $row->id);
            }
        };
        $append_branch(0);
        foreach ($data_output as $row) {
            if (!isset($seen[$row->id])) $ordered_rows[] = $row;
        }
        $data_output = $ordered_rows;
        $error_status = $wpdb->last_error;

        // Prepare and execute count query
        $count_query    = "$sqlCount $sWhere";
        $count_query    = $sql_params ? $wpdb->prepare($count_query, ...$sql_params) : $count_query;
        $r              = $wpdb->get_results($count_query);
        $iFilteredTotal = $r[0]->count ?? 0;

        // Final response output
        $output = [
            'sEcho'                => $sEcho,
            'error'                => $error_status,
            'iTotalRecords'        => $iFilteredTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'parent_categories'    => $parent_categories,
            'aaData'               => []
        ];

        foreach ($data_output as $row) {
            $row->parent_name = '—';

            // Match parent name from parent_categories
            if ($row->parent_id) {
                foreach ($parent_categories as $parent) {
                    if ($parent->id == $row->parent_id) {
                        $row->parent_name = esc_attr($parent->category_name);
                        break;
                    }
                }
            }

            // Add icon image HTML
            $row->icon = "<img src='" . ASL_UPLOAD_URL . 'svg/' . esc_attr($row->icon) . "' alt='' style='width:20px'/>";

            // Add action buttons
            $row->action = '<div class="edit-options">
            <a data-id="' . esc_attr($row->id) . '" title="Edit" class="edit_category"><svg width="14" height="14"><use xlink:href="#i-edit"></use></svg></a>
            <a title="Delete" data-id="' . esc_attr($row->id) . '" class="delete_category g-trash"><svg width="14" height="14"><use xlink:href="#i-trash"></use></svg></a>
        </div>';

            $row->handle = '<span class="asl-category-drag-handle" title="' . esc_attr__('Drag to reorder', 'asl_locator') . '" aria-hidden="true">⠿</span>';

            // Escape category name
            $row->category_name = esc_attr($row->category_name);

            $output['aaData'][] = $row;
        }

        return $this->send_response($output);
    }

    /** Save a complete, language-scoped ordering without changing parent relationships. */
    public function save_category_order()
    {
        global $wpdb;
        $ids = isset($_POST['category_ids']) && is_array($_POST['category_ids']) ? array_map('absint', $_POST['category_ids']) : [];
        $rows = $wpdb->get_results($wpdb->prepare(
            'SELECT id, parent_id FROM ' . ASL_PREFIX . 'categories WHERE lang = %s', $this->lang
        ));
        $expected = array_map('intval', wp_list_pluck($rows, 'id'));
        if (count($ids) !== count($expected) || count(array_unique($ids)) !== count($ids) || array_diff($expected, $ids) || array_diff($ids, $expected)) {
            return $this->send_response(['success' => false, 'msg' => esc_html__('Category list changed. Reload the page and try again.', 'asl_locator')]);
        }
        $parents = [];
        foreach ($rows as $row) $parents[(int) $row->id] = (int) $row->parent_id;
        $position = [];
        foreach ($ids as $id) {
            $parent = $parents[$id];
            $order = $position[$parent] ?? 0;
            if (false === $wpdb->update(ASL_PREFIX . 'categories', ['ordr' => $order], ['id' => $id], ['%d'], ['%d'])) {
                return $this->send_response(['success' => false, 'msg' => esc_html__('Could not save category order.', 'asl_locator')]);
            }
            $position[$parent] = $order + 1;
        }
        return $this->send_response(['success' => true, 'msg' => esc_html__('Category order saved.', 'asl_locator')]);
    }
}
