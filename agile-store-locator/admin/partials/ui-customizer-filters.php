<?php
$filter_value = static function ($key, $default = '') use ($filter_settings) {
    return isset($filter_settings[$key]) ? (string) $filter_settings[$key] : (string) $default;
};
$switches = [
    'advance_filter' => [__('Enable Filters', 'asl_locator'), __('Show the advanced filter controls.', 'asl_locator')],
    'hide_search' => [__('Hide Search', 'asl_locator'), __('Hide the main address search controls from the store locator.', 'asl_locator')],
    'address_ddl' => [__('Address Dropdowns', 'asl_locator'), __('Show cascading Country, State and City dropdowns.', 'asl_locator')],
    'ddl_search' => [__('Search Inside Dropdowns', 'asl_locator'), __('Add a search box inside category and attribute dropdowns.', 'asl_locator')],
    'brand_special_cascade' => [__('Brand–Speciality Cascade', 'asl_locator'), __('Limit Speciality options to the selected Brand.', 'asl_locator')],
    'and_filter' => [__('Require All Categories', 'asl_locator'), __('When enabled, stores must belong to every selected category. Otherwise, matching any category is enough.', 'asl_locator')],
    'image_filter' => [__('Category Images', 'asl_locator'), __('Show assigned icons in the category filter.', 'asl_locator')],
    'image_filter_title' => [__('Category Image Labels', 'asl_locator'), __('Show category names alongside their icons.', 'asl_locator')],
    'time_switch' => [__('Open Now Filter', 'asl_locator'), __('Let visitors display stores that are currently open.', 'asl_locator')],
    'show_opened' => [__('Open Now by Default', 'asl_locator'), __('Initially display only stores that are currently open.', 'asl_locator')],
    'distance_slider' => [__('Distance Filter', 'asl_locator'), __('Let visitors restrict results by distance.', 'asl_locator')],
];
$locked_switches = ['address_ddl', 'brand_special_cascade', 'and_filter', 'image_filter', 'image_filter_title', 'time_switch', 'show_opened'];
$render_switch = static function ($key) use ($switches, $filter_value, $locked_switches) {
    $locked = in_array($key, $locked_switches, true);
    [$label, $description] = $switches[$key]; ?>
    <label class="asl-filter-setting<?php echo $locked ? ' asl-is-locked' : ''; ?>" for="asl-filter-<?php echo esc_attr($key); ?>" data-filter-setting="<?php echo esc_attr($key); ?>" <?php echo $locked ? 'data-asl-locked' : ''; ?>>
      <span class="asl-field-copy"><strong><?php echo esc_html($label); ?><?php if ($locked) : ?><span class="asl-pro-badge"><?php esc_html_e('PRO', 'asl_locator'); ?></span><?php endif; ?></strong><small><?php echo esc_html($description); ?></small></span>
      <span class="asl-appearance-switch"><input type="checkbox" role="switch" id="asl-filter-<?php echo esc_attr($key); ?>" name="filters[<?php echo esc_attr($key); ?>]" value="1" data-asl-filter <?php echo $locked ? 'disabled aria-disabled="true"' : ''; ?> <?php checked(!$locked && $filter_value($key, '0') === '1'); ?>><span class="asl-appearance-switch-track" aria-hidden="true"></span></span>
    </label><?php
};
$category_parents = [];
foreach ((array) $filter_categories as $category_item) $category_parents[(int) $category_item->id] = (int) $category_item->parent_id;
?>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Filter Controls', 'asl_locator'); ?></h3>
  <?php $render_switch('advance_filter'); $render_switch('hide_search'); $render_switch('address_ddl'); ?>
  <div class="asl-filter-setting asl-filter-setting--stacked asl-is-locked" data-asl-locked>
    <span class="asl-field-copy"><strong><?php esc_html_e('Dropdown Filters', 'asl_locator'); ?><span class="asl-pro-badge"><?php esc_html_e('PRO', 'asl_locator'); ?></span></strong><small><?php esc_html_e('Choose Brand, Speciality and registered custom attribute filters.', 'asl_locator'); ?></small></span>
    <?php $selected_attributes = []; ?>
    <span id="asl-filter-filter_ddl" class="asl-filter-checklist" role="group" aria-label="<?php esc_attr_e('Dropdown Filters', 'asl_locator'); ?>">
      <?php foreach ($filter_controls as $control) : ?><label><input type="checkbox" name="filters[filter_ddl][]" value="<?php echo esc_attr($control['field']); ?>" data-asl-filter disabled aria-disabled="true"><span><?php echo esc_html($control['label']); ?></span></label><?php endforeach; ?>
    </span>
  </div>
  <?php $render_switch('ddl_search'); ?>
  <label class="asl-filter-setting asl-filter-setting--stacked" for="asl-filter-filter_sort">
    <span class="asl-field-copy"><strong><?php esc_html_e('Filter Option Order', 'asl_locator'); ?></strong><small><?php esc_html_e('Sort category and attribute options alphabetically or by their custom order.', 'asl_locator'); ?></small></span>
    <select id="asl-filter-filter_sort" name="filters[filter_sort]" data-asl-filter><option value="ordr" <?php selected($filter_value('filter_sort', 'ordr'), 'ordr'); ?>><?php esc_html_e('Custom order', 'asl_locator'); ?></option><option value="name_" <?php selected($filter_value('filter_sort', 'ordr'), 'name_'); ?>><?php esc_html_e('Alphabetical', 'asl_locator'); ?></option></select>
  </label>
  <?php $render_switch('brand_special_cascade'); ?>
</div>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Categories', 'asl_locator'); ?></h3>
  <?php $render_switch('and_filter'); $render_switch('image_filter'); $render_switch('image_filter_title'); ?>
  <?php $selected_categories = array_filter(array_map('trim', explode(',', $filter_value('select_category')))); ?>
  <div class="asl-filter-setting asl-filter-setting--stacked"><span class="asl-field-copy"><strong><?php esc_html_e('Selected Categories', 'asl_locator'); ?></strong><small><?php esc_html_e('Select categories that are active when the locator loads.', 'asl_locator'); ?></small></span><span id="asl-filter-select_category" class="asl-filter-checklist" role="group" aria-label="<?php esc_attr_e('Selected Categories', 'asl_locator'); ?>"><?php foreach ((array) $filter_categories as $category) : $category_id = (string) $category->id; $depth = 0; $parent = (int) $category->parent_id; $seen = []; while ($parent && isset($category_parents[$parent]) && !isset($seen[$parent])) { $seen[$parent] = true; $depth++; $parent = $category_parents[$parent]; } ?><label style="--asl-filter-depth:<?php echo esc_attr($depth); ?>"><input type="checkbox" name="filters[select_category][]" value="<?php echo esc_attr($category_id); ?>" data-asl-filter <?php checked(in_array($category_id, $selected_categories, true)); ?>><span><?php echo esc_html($category->category_name); ?></span></label><?php endforeach; ?></span></div>
</div>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Availability', 'asl_locator'); ?></h3>
  <?php $render_switch('time_switch'); $render_switch('show_opened'); ?>
</div>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Distance', 'asl_locator'); ?></h3>
  <?php $render_switch('distance_slider'); ?>
  <div class="asl-filter-setting asl-filter-setting--stacked asl-distance-style-lock"><span class="asl-field-copy"><strong><?php esc_html_e('Distance Control Style', 'asl_locator'); ?></strong><small><?php esc_html_e('Choose how visitors set the search distance.', 'asl_locator'); ?></small></span><select id="asl-filter-distance_control" name="filters[distance_control]" data-asl-filter disabled aria-disabled="true"><option value="1" selected><?php esc_html_e('Dropdown', 'asl_locator'); ?></option></select><span class="asl-distance-style-preview" role="group" aria-label="<?php esc_attr_e('Distance Control Style', 'asl_locator'); ?>"><button type="button" data-asl-upsell aria-disabled="true"><em class="asl-pro-badge"><?php esc_html_e('PRO', 'asl_locator'); ?></em><i class="asl-distance-slider-icon" aria-hidden="true"><b></b></i><span><?php esc_html_e('Slider', 'asl_locator'); ?></span></button><span class="is-current"><em class="dashicons dashicons-yes" aria-hidden="true"></em><i class="dashicons dashicons-arrow-down-alt2"></i><span><?php esc_html_e('Dropdown', 'asl_locator'); ?></span></span><button type="button" data-asl-upsell aria-disabled="true"><em class="asl-pro-badge"><?php esc_html_e('PRO', 'asl_locator'); ?></em><i class="dashicons dashicons-location-alt"></i><span><?php esc_html_e('Map Boundary', 'asl_locator'); ?></span></button></span></div>
  <label class="asl-filter-setting asl-filter-setting--stacked" for="asl-filter-fixed_radius"><span class="asl-field-copy"><strong><?php esc_html_e('Fixed Radius', 'asl_locator'); ?></strong><small><?php esc_html_e('Restrict results to a fixed distance without a visitor-controlled radius.', 'asl_locator'); ?></small></span><input type="number" id="asl-filter-fixed_radius" name="filters[fixed_radius]" data-asl-filter min="1" step="1" value="<?php echo esc_attr($filter_value('fixed_radius')); ?>" placeholder="100"></label>
</div>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Secondary Search', 'asl_locator'); ?></h3>
  <?php $search_fields = ['title' => __('Title', 'asl_locator'), 'street' => __('Street', 'asl_locator'), 'city' => __('City', 'asl_locator'), 'state' => __('State', 'asl_locator'), 'postal_code' => __('Postal Code', 'asl_locator'), 'country' => __('Country', 'asl_locator'), 'description' => __('Description', 'asl_locator'), 'description_2' => __('Additional Description', 'asl_locator')]; ?>
  <div class="asl-filter-setting asl-filter-setting--stacked asl-is-locked" data-asl-locked><span class="asl-field-copy"><strong><?php esc_html_e('Secondary Store Search', 'asl_locator'); ?><span class="asl-pro-badge"><?php esc_html_e('PRO', 'asl_locator'); ?></span></strong><small><?php esc_html_e('Choose the store fields searched by the secondary search box.', 'asl_locator'); ?></small></span><span id="asl-filter-search_2" class="asl-filter-checklist" role="group" aria-label="<?php esc_attr_e('Secondary Store Search', 'asl_locator'); ?>"><?php foreach ($search_fields as $field => $label) : ?><label><input type="checkbox" name="filters[search_2][]" value="<?php echo esc_attr($field); ?>" data-asl-filter disabled aria-disabled="true"><span><?php echo esc_html($label); ?></span></label><?php endforeach; ?></span></div>
</div>
