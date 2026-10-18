<?php
$layout_labels = ['left' => __('Left', 'asl_locator'), 'right' => __('Right', 'asl_locator')];
if ($template === 'template-0') {
    $layout_labels['bottom'] = __('Below Map', 'asl_locator');
    $layout_labels['cards'] = __('Card Grid', 'asl_locator');
}
$layout_supported = in_array(str_replace('template-', '', $template), \AgileStoreLocator\Helper::customizer_layout_templates(), true);
$layout_value = !empty($fields->{'results-layout'}) ? $fields->{'results-layout'} : 'left';
$layout_choice = $layout_value;
$toggle_labels = ['display_list' => __('Display List', 'asl_locator'), 'full_width' => __('Full Width', 'asl_locator'), 'hide_logo' => __('Hide Logo', 'asl_locator'), 'hide_hours' => __('Hide Hours', 'asl_locator'), 'show_categories' => __('Show Categories', 'asl_locator'), 'show_sort' => __('Show Sort By', 'asl_locator'), 'direction_btn' => __('Show Directions Button', 'asl_locator'), 'slug_link' => __('Show Website Button', 'asl_locator')];
$select_labels = [
    'full_height' => [__('Full Height', 'asl_locator'), ['' => __('None', 'asl_locator'), 'full-height' => __('Full Height (Not Fixed)', 'asl_locator'), 'full-height asl-fixed' => __('Full Height (Fixed)', 'asl_locator')]],
    'tabs_layout' => [__('Tabs Layout', 'asl_locator'), ['0' => __('Dropdowns', 'asl_locator'), '1' => __('Clickable Tabs', 'asl_locator'), '2' => __('Checkboxes', 'asl_locator')]],
    'action_button_display' => [__('Action Button Content', 'asl_locator'), ['icons' => __('Icons only', 'asl_locator'), 'text' => __('Text only', 'asl_locator'), 'both' => __('Icons and text', 'asl_locator')]],
];
?>
<h3><?php esc_html_e('Result Layout', 'asl_locator'); ?></h3>
<div class="asl-layout-options <?php echo $template === 'template-0' ? 'asl-layout-options--four' : ''; ?>" role="group" aria-label="<?php esc_attr_e('Result Layout', 'asl_locator'); ?>">
<?php foreach ($layout_labels as $value => $label) : ?>
  <label class="asl-layout-choice">
    <input type="radio" name="ui-results-layout" value="<?php echo esc_attr($value); ?>" data-asl-result-layout <?php checked($layout_choice, $value); ?> <?php disabled(!$layout_supported); ?>>
    <span class="asl-layout-thumbnail asl-layout-thumbnail--<?php echo esc_attr($value); ?>" aria-hidden="true"><i class="asl-layout-mini-search"></i><i class="asl-layout-mini-map"></i><span class="asl-layout-mini-list"><b></b><b></b><b></b></span><span class="asl-layout-choice-check dashicons dashicons-yes"></span></span>
    <span class="asl-layout-choice-label"><?php echo esc_html($label); ?></span>
  </label>
<?php endforeach; ?>
</div>
<?php if ($layout_supported) : ?>
<input type="hidden" name="results-layout" value="<?php echo esc_attr($layout_value); ?>" data-asl-layout>
<?php foreach (['sm', 'md', 'lg', 'xl'] as $breakpoint) : $key = 'list-width-' . $breakpoint; ?>
<input type="hidden" name="<?php echo esc_attr($key); ?>" value="" data-asl-layout>
<?php endforeach; ?>
<div class="asl-proportion-control asl-is-locked" data-asl-locked>
  <label for="asl-list-map-proportion"><span><?php esc_html_e('List / Map Proportion', 'asl_locator'); ?><span class="asl-pro-badge"><?php esc_html_e('PRO', 'asl_locator'); ?></span></span><span id="asl-proportion-device"><?php esc_html_e('Desktop', 'asl_locator'); ?></span></label>
  <input type="range" id="asl-list-map-proportion" min="30" max="70" step="1" value="40" aria-describedby="asl-proportion-stacked" disabled aria-disabled="true">
  <div class="asl-proportion-values"><span><?php esc_html_e('List', 'asl_locator'); ?> <strong id="asl-proportion-list">40%</strong></span><span><?php esc_html_e('Map', 'asl_locator'); ?> <strong id="asl-proportion-map">60%</strong></span></div>
  <p id="asl-proportion-stacked" class="asl-layout-hint" hidden><?php esc_html_e('List and map stack on this screen size, each at 100% width.', 'asl_locator'); ?></p>
</div>
<?php else : ?><p class="asl-layout-hint"><?php esc_html_e('Result Layout is available for Templates 0, 1, 2, 3, 5 and 6.', 'asl_locator'); ?></p><?php endif; ?>
<h3 class="asl-layout-section-title"><?php esc_html_e('Appearance', 'asl_locator'); ?></h3>
<div class="asl-appearance-controls">
<?php foreach (['display_list', 'tabs_layout', 'hide_logo', 'hide_hours', 'show_categories', 'show_sort', 'direction_btn', 'slug_link', 'action_button_display'] as $key) : if (in_array($key, ['show_sort', 'action_button_display'], true) && $template !== 'template-0') continue; ?>
  <?php $locked_appearance = $key === 'tabs_layout'; ?>
  <label class="asl-appearance-row <?php echo isset($select_labels[$key]) ? 'asl-appearance-row--select' : ''; ?><?php echo $locked_appearance ? ' asl-is-locked asl-tabs-layout-lock' : ''; ?>" for="asl-appearance-<?php echo esc_attr($key); ?>" <?php echo $locked_appearance ? 'data-asl-locked' : ''; ?>>
    <span><?php echo esc_html(isset($select_labels[$key]) ? $select_labels[$key][0] : $toggle_labels[$key]); ?><?php if ($locked_appearance) : ?><span class="asl-pro-badge"><?php esc_html_e('PRO', 'asl_locator'); ?></span><?php endif; ?></span>
    <?php if (isset($select_labels[$key])) : ?>
      <select id="asl-appearance-<?php echo esc_attr($key); ?>" name="appearance[<?php echo esc_attr($key); ?>]" data-asl-appearance <?php echo $locked_appearance ? 'disabled aria-disabled="true"' : ''; ?>>
        <?php foreach ($select_labels[$key][1] as $value => $label) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($locked_appearance ? '0' : $appearance[$key], (string) $value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?>
      </select>
      <?php if ($locked_appearance) : ?><span class="asl-tabs-layout-preview" aria-hidden="true"><span class="is-current"><i class="dashicons dashicons-arrow-down-alt2"></i><?php esc_html_e('Dropdowns', 'asl_locator'); ?></span><span><i class="dashicons dashicons-menu-alt3"></i><?php esc_html_e('Clickable Tabs', 'asl_locator'); ?></span><span><i class="dashicons dashicons-yes-alt"></i><?php esc_html_e('Checkboxes', 'asl_locator'); ?></span></span><?php endif; ?>
    <?php else : ?>
      <span class="asl-appearance-switch"><input type="checkbox" role="switch" id="asl-appearance-<?php echo esc_attr($key); ?>" name="appearance[<?php echo esc_attr($key); ?>]" value="1" data-asl-appearance <?php checked($appearance[$key], '1'); ?>><span class="asl-appearance-switch-track" aria-hidden="true"></span></span>
    <?php endif; ?>
  </label>
<?php endforeach; ?>
</div>
<p class="asl-layout-hint"><?php esc_html_e('Layout and proportions are saved for this template. Appearance settings also apply to your other store locators.', 'asl_locator'); ?></p>
<?php include ASL_PLUGIN_PATH . 'admin/partials/ui-customizer-dimensions.php'; ?>
