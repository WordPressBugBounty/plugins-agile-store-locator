<?php

namespace AgileStoreLocator\Form;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * The Custom Field Classes, used in the Form builder
 *
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Form
 * @author     AgileLogix <support@agilelogix.com>
 */
class CustomField extends Field {
    
    public function __construct($data, $value = '') {

        $require = (isset($data['require']) && $data['require'])? true: false;
        parent::__construct($data['label'], $data['name'], $data['type'], $value, $require);
        $this->options = $this->parseOptions(isset($data['options'])? $data['options']: []);
    }

    private function parseOptions($options) {

        if ($this->type === 'dropdown' || $this->type === 'radio') {
            return explode(',', $options);
        }
        return [];
    }

    public static function sanitizeValues($values, $schema) {
        if (!is_array($values) || !is_array($schema)) return $values;

        foreach ($schema as $field_name => $field) {
            if (($field['type'] ?? '') === 'page_link' && isset($values[$field_name])) {
                $values[$field_name] = self::sanitizePageLink($values[$field_name]);
            }
        }
        return $values;
    }

    private static function sanitizePageLink($value) {
        $value = trim((string) $value);
        if ($value === '' || strpos($value, '..') !== false || preg_match('#^(?:[a-z][a-z0-9+.-]*:)?//#i', $value)) return '';

        $path = wp_parse_url('/' . ltrim($value, '/'), PHP_URL_PATH);
        return is_string($path) && $path !== '' ? user_trailingslashit('/' . ltrim($path, '/')) : '';
    }
}
