<?php

// Store Form tab content extracted for reuse
$store_form_fields_json = esc_attr(wp_json_encode($store_form_fields));
$sections = [
    'store_info' => esc_attr__('Store Information', 'asl_locator'),
    'location' => esc_attr__('Address Location', 'asl_locator'),
    'hours' => esc_attr__('Store Schedule', 'asl_locator'),
    'additional' => esc_attr__('Additional Data', 'asl_locator')
];
$section_meta = [
    'store_info' => [
        'icon' => 'dashicons-store',
        'description' => esc_html__('Core information visitors provide about a store.', 'asl_locator'),
    ],
    'location' => [
        'icon' => 'dashicons-location-alt',
        'description' => esc_html__('Address and geographic fields used to locate the store.', 'asl_locator'),
    ],
    'hours' => [
        'icon' => 'dashicons-clock',
        'description' => esc_html__('Opening hours and schedule information.', 'asl_locator'),
    ],
    'additional' => [
        'icon' => 'dashicons-plus-alt2',
        'description' => esc_html__('Optional, dropdown and custom store information.', 'asl_locator'),
    ],
];

$hidden_backend_fields = ['lat', 'lng'];
$locked_fields = ['title', 'city', 'postal_code', 'country', 'state', 'street'];
$hidden_items = [];

$bulk_edit_fields_setting = isset($all_configs['bulk_edit_fields']) ? $all_configs['bulk_edit_fields'] : '';
$bulk_edit_fields = $bulk_edit_fields_setting ? json_decode($bulk_edit_fields_setting, true) : ['description', 'open_hours'];
if (!is_array($bulk_edit_fields) || empty($bulk_edit_fields)) {
    $bulk_edit_fields = ['description', 'open_hours'];
}
$bulk_edit_field_options = [
    'description' => esc_attr__('Description', 'asl_locator'),
    'open_hours' => esc_attr__('Open Hours', 'asl_locator'),
    'marker_id' => esc_attr__('Marker', 'asl_locator'),
    'logo_id' => esc_attr__('Logo', 'asl_locator'),
    'categories' => esc_attr__('Categories', 'asl_locator')
];

// Add custom fields to bulk edit options
$bulk_edit_custom_fields = [];
foreach ($store_form_fields as $field_item) {
    if (isset($field_item['type']) && $field_item['type'] === 'custom' && isset($field_item['field'])) {
        $custom_key = 'custom:' . $field_item['field'];
        $bulk_edit_custom_fields[$custom_key] = isset($field_item['label']) ? $field_item['label'] : $field_item['field'];
    }
}
if (!empty($bulk_edit_custom_fields)) {
    $bulk_edit_field_options = array_merge($bulk_edit_field_options, $bulk_edit_custom_fields);
}

$grouped_fields = [];
foreach ($store_form_fields as $field_item) {
    $sec_key = isset($field_item['section']) && $field_item['section'] ? $field_item['section'] : 'store_info';
    if (!isset($grouped_fields[$sec_key])) {
        $grouped_fields[$sec_key] = [];
    }
    $grouped_fields[$sec_key][] = $field_item;
}
?>
<?php $asl_upgrade_url = defined('ASL_UPGRADE_URL') ? ASL_UPGRADE_URL : 'https://agilestorelocator.com/pricing/'; ?>
<section class="asl-pro-locked-section asl-pro-locked-panel asl-settings-pro-lock asl-store-form-pro-lock" aria-labelledby="asl-store-form-lock-title">
    <div class="asl-pro-lock-overlay">
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
        <strong id="asl-store-form-lock-title"><?php esc_html_e('Store Form tools are a Pro feature', 'asl_locator'); ?></strong>
        <span><?php esc_html_e('Upgrade to configure registration form sections and bulk edit fields.', 'asl_locator'); ?></span>
        <a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a>
    </div>
    <div class="asl-pro-locked-preview" aria-hidden="true">
<div class="row mt-2 asl-store-form-ui">
    <div class="col-12 mb-4">
        <header class="asl-store-form-intro">
            <span class="asl-store-form-intro__icon" aria-hidden="true"><span class="dashicons dashicons-feedback"></span></span>
            <div class="asl-store-form-intro__copy">
                <span><?php echo esc_html__('Frontend submission', 'asl_locator'); ?></span>
                <h5><?php echo esc_html__('Store Form Builder', 'asl_locator'); ?></h5>
                <p><?php echo esc_html__('Arrange the registration form, control field visibility and choose which values can be updated in bulk.', 'asl_locator'); ?></p>
            </div>
            <a class="asl-store-form-intro__guide" target="_blank" rel="noopener noreferrer" href="https://agilestorelocator.com/wiki/store-registration-form/">
                <?php echo esc_html__('Form Guide', 'asl_locator'); ?><span class="dashicons dashicons-external" aria-hidden="true"></span>
            </a>
        </header>
    </div>
    <div class="col-md-12 mb-4">
        <section class="asl-store-form-bulk">
            <header class="asl-store-form-section-header">
                <span class="asl-store-form-section-header__icon" aria-hidden="true"><span class="dashicons dashicons-edit-page"></span></span>
                <div>
                    <h5><?php echo esc_html__('Bulk Edit Fields', 'asl_locator'); ?></h5>
                    <p><?php echo esc_html__('Select which fields are available in the bulk editor for stores.', 'asl_locator'); ?></p>
                </div>
            </header>
            <div class="asl-store-form-bulk__body">
            <input type="hidden" id="asl-bulk-edit-fields-input" name="bulk_edit_fields" value="<?php echo esc_attr(wp_json_encode($bulk_edit_fields)); ?>">
            <div class="asl-store-form-bulk__options">
                <?php foreach ($bulk_edit_field_options as $field_key => $field_label): ?>
                        <label class="asl-store-form-bulk__option">
                            <input type="checkbox" class="asl-bulk-edit-field-toggle"
                                   data-field="<?php echo esc_attr($field_key); ?>"
                                   <?php if (in_array($field_key, $bulk_edit_fields, true)) echo 'checked'; ?>>
                            <span class="asl-store-form-bulk__check" aria-hidden="true"></span>
                            <span><?php echo esc_html($field_label); ?></span>
                        </label>
                <?php endforeach; ?>
            </div>
            </div>
        </section>
    </div>
    <div class="col-md-12 mb-4">
        <div class="alert alert-primary" role="status">
            <div class="asl-alert-content">
                <strong><?php echo esc_html__('Build your frontend form', 'asl_locator'); ?></strong>
                <p><?php echo esc_html__('Drag fields between sections to reorder them. Use each switch to show or hide optional fields.', 'asl_locator'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <input type="hidden" id="asl-store-form-fields-input" name="store_form_fields" value="<?php echo $store_form_fields_json; ?>">
        <div class="asl-store-form-builder-heading">
            <div>
                <span><?php echo esc_html__('Form structure', 'asl_locator'); ?></span>
                <h5><?php echo esc_html__('Registration Form Sections', 'asl_locator'); ?></h5>
            </div>
            <small><span class="dashicons dashicons-move" aria-hidden="true"></span><?php echo esc_html__('Drag fields to reorder', 'asl_locator'); ?></small>
        </div>
        <div class="asl-store-form-sections">
            <?php foreach ($sections as $sec_key => $sec_label): ?>
                    <section class="asl-store-form-section">
                        <header class="asl-store-form-section__header">
                            <span aria-hidden="true"><span class="dashicons <?php echo esc_attr($section_meta[$sec_key]['icon']); ?>"></span></span>
                            <div>
                                <h5><?php echo esc_html($sec_label); ?></h5>
                                <p><?php echo esc_html($section_meta[$sec_key]['description']); ?></p>
                            </div>
                        </header>
                        <ul class="list-group asl-store-form-fields" data-section="<?php echo esc_attr($sec_key); ?>">
                            <?php if(isset($grouped_fields[$sec_key])): ?>
                                <?php foreach ($grouped_fields[$sec_key] as $field): ?>
                                    <?php
                                        if (in_array($field['field'], $hidden_backend_fields, true)) {
                                            $hidden_items[] = $field;
                                            continue;
                                        }
                                        $is_locked = in_array($field['field'], $locked_fields, true);
                                        if ($is_locked) {
                                            $field['enabled'] = 1;
                                        }
                                    ?>
                                    <li class="list-group-item" data-key="<?php echo esc_attr($field['key']); ?>" data-field="<?php echo esc_attr($field['field']); ?>" data-type="<?php echo esc_attr(isset($field['type']) ? $field['type'] : 'core'); ?>" data-section="<?php echo esc_attr(isset($field['section']) ? $field['section'] : $sec_key); ?>" data-label="<?php echo esc_attr($field['label']); ?>" data-enabled="<?php echo isset($field['enabled']) ? esc_attr($field['enabled']) : 1; ?>">
                                        <span class="asl-drag-handle" aria-label="<?php echo esc_attr__('Drag to reorder', 'asl_locator'); ?>"><span class="dashicons dashicons-move"></span></span>
                                        <span class="asl-store-form-field-name"><?php echo esc_html($field['label']); ?></span>
                                        <span class="badge asl-store-form-field-type"><?php echo esc_html(isset($field['type']) ? $field['type'] : ''); ?></span>
                                        <?php if ($is_locked): ?>
                                            <span class="asl-store-form-required"><span class="dashicons dashicons-lock" aria-hidden="true"></span><?php echo esc_html__('Required', 'asl_locator'); ?></span>
                                        <?php else: ?>
                                            <label class="switch mb-0" aria-label="<?php echo esc_attr(sprintf(__('Toggle %s field', 'asl_locator'), $field['label'])); ?>">
                                                    <input type="checkbox" class="asl-store-field-toggle" <?php if (!isset($field['enabled']) || $field['enabled']) echo 'checked'; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </section>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (!empty($hidden_items)): ?>
    <div class="d-none">
        <?php foreach ($hidden_items as $field): ?>
            <ul class="list-group asl-store-form-fields" data-section="<?php echo esc_attr(isset($field['section']) ? $field['section'] : 'location'); ?>">
                <li class="list-group-item d-flex align-items-center justify-content-between" data-key="<?php echo esc_attr($field['key']); ?>" data-field="<?php echo esc_attr($field['field']); ?>" data-type="<?php echo esc_attr(isset($field['type']) ? $field['type'] : 'core'); ?>" data-section="<?php echo esc_attr(isset($field['section']) ? $field['section'] : 'location'); ?>" data-label="<?php echo esc_attr($field['label']); ?>" data-enabled="<?php echo isset($field['enabled']) ? esc_attr($field['enabled']) : 1; ?>">
                    <span class="asl-drag-handle me-3">&#9776;</span>
                    <span class="flex-fill"><?php echo esc_attr($field['label']); ?></span>
                    <span class="badge bg-secondary text-uppercase me-2"><?php echo esc_attr(isset($field['type']) ? $field['type'] : ''); ?></span>
                    <label class="switch mb-0">
                        <input type="checkbox" class="asl-store-field-toggle" <?php if (!isset($field['enabled']) || $field['enabled']) echo 'checked'; ?>>
                        <span class="slider round"></span>
                    </label>
                </li>
            </ul>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
    </div>
</section>
