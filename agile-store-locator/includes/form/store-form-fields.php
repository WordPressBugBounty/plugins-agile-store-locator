<?php

namespace AgileStoreLocator\Form;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class StoreFormFields
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $available_fields = [];

    /**
     * @var array
     */
    protected $dropdown_controls = [];

    /**
     * @var array
     */
    protected $custom_fields = [];

    /**
     * StoreFormFields constructor.
     *
     * @param array $dropdown_controls Attribute dropdown controls keyed by field name.
     * @param array $custom_fields     Custom fields configuration.
     */
    public function __construct($dropdown_controls = [], $custom_fields = [])
    {
        $this->dropdown_controls = is_array($dropdown_controls) ? $dropdown_controls : [];
        $this->custom_fields     = is_array($custom_fields) ? $custom_fields : [];

        $this->available_fields = $this->build_available_fields();
        $this->fields           = $this->merge_with_saved($this->available_fields);
    }

    /**
     * Get all fields with saved order and enabled state.
     *
     * @return array
     */
    public function get_fields()
    {
        return $this->fields;
    }

    /**
     * Get only enabled fields.
     *
     * @return array
     */
    public function get_enabled_fields()
    {
        return array_values(array_filter($this->fields, function ($field) {
            return !isset($field['enabled']) || $field['enabled'];
        }));
    }

    /**
     * Check if a field is enabled.
     *
     * @param string $field_name
     * @return bool
     */
    public function is_enabled($field_name)
    {
        foreach ($this->fields as $field) {
            if ($field['field'] === $field_name) {
                return !isset($field['enabled']) || $field['enabled'];
            }
        }

        return false;
    }

    /**
     * Get enabled custom fields keyed by name.
     *
     * @return array
     */
    public function get_enabled_custom_fields()
    {
        $enabled = [];

        foreach ($this->fields as $field) {
            if (isset($field['type']) && $field['type'] === 'custom' && (!isset($field['enabled']) || $field['enabled'])) {
                $key = isset($field['field']) ? $field['field'] : null;
                if ($key && isset($field['meta'])) {
                    $enabled[$key] = $field['meta'];
                }
            }
        }

        return $enabled;
    }

    /**
     * Get enabled dropdown (attribute) fields.
     *
     * @return array
     */
    public function get_enabled_dropdown_fields()
    {
        $enabled = [];

        foreach ($this->fields as $field) {
            if (isset($field['type']) && $field['type'] === 'dropdown' && (!isset($field['enabled']) || $field['enabled'])) {
                $enabled[] = $field['field'];
            }
        }

        return $enabled;
    }

    /**
     * Build the base list of available fields.
     *
     * @return array
     */
    protected function build_available_fields()
    {
        $fields = [
            ['key' => 'title', 'field' => 'title', 'label' => asl_esc_lbl('reg_company'), 'section' => 'store_info', 'type' => 'core'],
            ['key' => 'description', 'field' => 'description', 'label' => asl_esc_lbl('reg_name'), 'section' => 'store_info', 'type' => 'core'],
            ['key' => 'website', 'field' => 'website', 'label' => asl_esc_lbl('reg_web_url'), 'section' => 'store_info', 'type' => 'core'],
            ['key' => 'phone', 'field' => 'phone', 'label' => asl_esc_lbl('phone'), 'section' => 'store_info', 'type' => 'core'],
            ['key' => 'fax', 'field' => 'fax', 'label' => asl_esc_lbl('fax'), 'section' => 'store_info', 'type' => 'core'],
            ['key' => 'email', 'field' => 'email', 'label' => asl_esc_lbl('email'), 'section' => 'store_info', 'type' => 'core'],
            ['key' => 'categories', 'field' => 'categories', 'label' => asl_esc_lbl('categories_tab'), 'section' => 'store_info', 'type' => 'core'],
            ['key' => 'street', 'field' => 'street', 'label' => asl_esc_lbl('reg_street'), 'section' => 'location', 'type' => 'core'],
            ['key' => 'city', 'field' => 'city', 'label' => asl_esc_lbl('label_city'), 'section' => 'location', 'type' => 'core'],
            ['key' => 'state', 'field' => 'state', 'label' => asl_esc_lbl('label_state'), 'section' => 'location', 'type' => 'core'],
            ['key' => 'postal_code', 'field' => 'postal_code', 'label' => asl_esc_lbl('reg_post_code'), 'section' => 'location', 'type' => 'core'],
            ['key' => 'country', 'field' => 'country', 'label' => asl_esc_lbl('label_country'), 'section' => 'location', 'type' => 'core'],
            ['key' => 'map', 'field' => 'map', 'label' => asl_esc_lbl('reg_map'), 'section' => 'location', 'type' => 'core'],
            ['key' => 'lat', 'field' => 'lat', 'label' => asl_esc_lbl('reg_lat'), 'section' => 'location', 'type' => 'core'],
            ['key' => 'lng', 'field' => 'lng', 'label' => asl_esc_lbl('reg_lng'), 'section' => 'location', 'type' => 'core'],
            ['key' => 'open_hours', 'field' => 'open_hours', 'label' => asl_esc_lbl('store_schedule'), 'section' => 'hours', 'type' => 'core'],
            ['key' => 'description_2', 'field' => 'description_2', 'label' => asl_esc_lbl('reg_add_desc'), 'section' => 'additional', 'type' => 'core'],
        ];

        // Attribute dropdowns
        foreach ($this->dropdown_controls as $field => $control) {
            $control_field = isset($control['field']) ? $control['field'] : $field;
            $fields[] = [
                'key'     => 'dropdown:' . $field,
                'field'   => $control_field,
                'label'   => isset($control['plural']) ? $control['plural'] : (isset($control['label']) ? $control['label'] : ucfirst($field)),
                'section' => 'store_info',
                'type'    => 'dropdown'
            ];
        }

        // Custom fields
        foreach ($this->custom_fields as $custom_field) {
            if (!isset($custom_field['name'])) {
                continue;
            }
            $fields[] = [
                'key'     => 'custom:' . $custom_field['name'],
                'field'   => $custom_field['name'],
                'label'   => isset($custom_field['label']) ? $custom_field['label'] : $custom_field['name'],
                'section' => 'additional',
                'type'    => 'custom',
                'meta'    => $custom_field
            ];
        }

        return $fields;
    }

    /**
     * Merge saved settings with available fields to preserve order and visibility.
     *
     * @param array $available_fields
     * @return array
     */
    protected function merge_with_saved($available_fields)
    {
        $locked_fields = ['title', 'city', 'state', 'postal_code', 'country', 'street'];
        $saved_setting = \AgileStoreLocator\Helper::get_setting('store_form_fields');
        $available_map = [];
        $merged        = [];

        foreach ($available_fields as $field) {
            $available_map[$field['key']] = $field;
        }

        if ($saved_setting) {
            $saved = json_decode(stripslashes($saved_setting), true);

            if (is_array($saved)) {
                foreach ($saved as $saved_field) {
                    $key = isset($saved_field['key']) ? $saved_field['key'] : null;
                    if ($key && isset($available_map[$key])) {
                        $merged_field = $available_map[$key];

                        // Preserve custom section/label from saved data if provided
                        if (isset($saved_field['section']) && $saved_field['section']) {
                            $merged_field['section'] = $saved_field['section'];
                        }

                        if (isset($saved_field['label']) && $saved_field['label']) {
                            $merged_field['label'] = $saved_field['label'];
                        }

                        $merged_field['enabled'] = isset($saved_field['enabled']) ? $saved_field['enabled'] : 1;

                        if (in_array($merged_field['field'], $locked_fields, true)) {
                            $merged_field['enabled'] = 1;
                        }

                        $merged[] = $merged_field;
                        unset($available_map[$key]);
                    }
                }
            }
        }

        // Append any new fields that were not in the saved list
        if (!empty($available_map)) {
            foreach ($available_fields as $field) {
                if (isset($available_map[$field['key']])) {
                    if (in_array($field['field'], $locked_fields, true)) {
                        $field['enabled'] = 1;
                    }
                    $merged[] = $field;
                }
            }
        }

        // Ensure locked fields stay enabled even if legacy data disabled them
        foreach ($merged as &$field) {
            if (in_array($field['field'], $locked_fields, true)) {
                $field['enabled'] = 1;
            }
        }
        unset($field);

        return $merged;
    }
}
