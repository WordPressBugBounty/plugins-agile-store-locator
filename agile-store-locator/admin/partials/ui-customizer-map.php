<?php
$map_value = static function ($key, $default = '') use ($map_settings) {
    return isset($map_settings[$key]) ? (string) $map_settings[$key] : (string) $default;
};
$map_vendor = strtolower($map_value('map_vendor', 'google'));
$tile_provider = strtolower($map_value('tile_provider', 'geoapify'));
$advanced_markers = $map_vendor === 'google' && $map_value('advanced_marker') !== '';
$provider_labels = [
    'osm' => __('OpenStreetMap', 'asl_locator'),
    'openfreemap' => __('OpenFreeMap', 'asl_locator'),
    'geoapify' => __('Geoapify', 'asl_locator'),
    'mapbox' => __('Mapbox', 'asl_locator'),
    'maptiler' => __('MapTiler', 'asl_locator'),
    'custom' => __('Custom Style URL', 'asl_locator'),
];
$provider_styles = [
    'openfreemap' => ['default' => __('Liberty', 'asl_locator'), 'bright' => __('Bright', 'asl_locator'), 'positron' => __('Positron', 'asl_locator'), 'dark' => __('Dark', 'asl_locator'), 'fiord' => __('Fiord', 'asl_locator')],
    'geoapify' => ['default' => __('Bright Smooth', 'asl_locator'), 'osm-carto' => __('OSM Carto', 'asl_locator'), 'positron' => __('Positron', 'asl_locator'), 'dark-matter' => __('Dark Matter', 'asl_locator')],
    'mapbox' => ['default' => __('Streets', 'asl_locator'), 'outdoors-v12' => __('Outdoors', 'asl_locator'), 'light-v11' => __('Light', 'asl_locator'), 'dark-v11' => __('Dark', 'asl_locator'), 'satellite-v9' => __('Satellite', 'asl_locator'), 'satellite-streets-v12' => __('Satellite Streets', 'asl_locator')],
    'maptiler' => ['default' => __('Streets', 'asl_locator'), 'basic-v2' => __('Basic', 'asl_locator'), 'bright-v2' => __('Bright', 'asl_locator'), 'outdoor-v2' => __('Outdoor', 'asl_locator'), 'topo-v2' => __('Topographic', 'asl_locator'), 'satellite' => __('Satellite', 'asl_locator')],
];
$active_marker = $map_value('active_marker');
$scroll_wheel = in_array($map_value('scroll_wheel'), ['default', '0', '1'], true) ? $map_value('scroll_wheel') : 'default';
if (!array_key_exists('active_marker', $map_settings)) {
    foreach ((array) $map_markers as $marker) {
        if (isset($marker->marker_name) && strtolower((string) $marker->marker_name) === 'active') {
            $active_marker = (string) $marker->icon;
            break;
        }
    }
}
$switches = [
    'category_marker' => [__('Category Markers', 'asl_locator'), __('Use assigned category icons instead of regular store markers.', 'asl_locator')],
    'marker_title' => [__('Marker Hover Title', 'asl_locator'), __('Show the store name and categories when a marker is hovered.', 'asl_locator')],
    'marker_label' => [__('Marker Labels', 'asl_locator'), __('Number store markers to match their order in distance results.', 'asl_locator')],
    'geo_marker' => [__('Visitor Location Marker', 'asl_locator'), __('Show the visitor’s searched or detected location on the map.', 'asl_locator')],
    'radius_circle' => [__('Search Radius Circle', 'asl_locator'), __('Draw the selected search radius around the visitor’s location.', 'asl_locator')],
    'cluster' => [__('Marker Clustering', 'asl_locator'), __('Group nearby store markers while the map is zoomed out.', 'asl_locator')],
    'fit_bound' => [__('Fit Map to Results', 'asl_locator'), __('Automatically frame the stores currently shown in the results.', 'asl_locator')],
];
$render_map_switch = static function ($key, $default = '0') use ($switches, $map_value) {
    [$label, $description] = $switches[$key]; ?>
    <label class="asl-filter-setting" for="asl-map-<?php echo esc_attr($key); ?>" data-map-setting="<?php echo esc_attr($key); ?>">
      <span class="asl-field-copy"><strong><?php echo esc_html($label); ?></strong><small><?php echo esc_html($description); ?></small></span>
      <span class="asl-appearance-switch"><input type="checkbox" role="switch" id="asl-map-<?php echo esc_attr($key); ?>" name="map[<?php echo esc_attr($key); ?>]" value="1" data-asl-map <?php checked($map_value($key, $default), '1'); ?>><span class="asl-appearance-switch-track" aria-hidden="true"></span></span>
    </label><?php
};
$render_zoom_slider = static function ($key, $label, $description, $default) use ($map_value) {
    $value = max(1, min(20, (int) $map_value($key, $default))); ?>
    <label class="asl-filter-setting asl-filter-setting--stacked asl-map-range-control" for="asl-map-<?php echo esc_attr($key); ?>">
      <span class="asl-field-copy"><strong><?php echo esc_html($label); ?></strong><small><?php echo esc_html($description); ?></small></span>
      <span class="asl-map-range-heading"><span><?php esc_html_e('Zoom level', 'asl_locator'); ?></span><strong data-asl-map-range-value><?php echo esc_html($value); ?></strong></span>
      <input type="range" class="asl-map-range" id="asl-map-<?php echo esc_attr($key); ?>" name="map[<?php echo esc_attr($key); ?>]" data-asl-map min="1" max="20" step="1" value="<?php echo esc_attr($value); ?>">
    </label><?php
};
?>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Map Style', 'asl_locator'); ?></h3>
  <div class="asl-filter-setting asl-filter-setting--stacked">
    <span class="asl-field-copy"><strong><?php esc_html_e('Current Provider', 'asl_locator'); ?></strong><small><?php esc_html_e('The map provider is configured in the main Maps settings.', 'asl_locator'); ?></small></span>
    <span><strong><?php echo esc_html($map_vendor === 'maplibre' ? ($provider_labels[$tile_provider] ?? ucfirst($tile_provider)) : __('Google Maps', 'asl_locator')); ?></strong> · <a href="<?php echo esc_url(admin_url('admin.php?page=asl-settings#maps-tab')); ?>"><?php esc_html_e('Manage provider', 'asl_locator'); ?></a></span>
  </div>
  <?php if ($map_vendor === 'maplibre' && isset($provider_styles[$tile_provider])) : ?>
    <label class="asl-filter-setting asl-filter-setting--stacked" for="asl-map-tile_provider_style">
      <span class="asl-field-copy"><strong><?php esc_html_e('Map Style', 'asl_locator'); ?></strong><small><?php esc_html_e('Choose the visual style supplied by the active map provider.', 'asl_locator'); ?></small></span>
      <select id="asl-map-tile_provider_style" name="map[tile_provider_style]" data-asl-map><?php foreach ($provider_styles[$tile_provider] as $value => $label) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($map_value('tile_provider_style', 'default'), $value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select>
    </label>
  <?php elseif ($map_vendor === 'google') : ?>
    <p class="asl-layout-hint"><?php esc_html_e('Google map styling remains available in the Map Customizer.', 'asl_locator'); ?></p>
  <?php else : ?>
    <p class="asl-layout-hint"><?php esc_html_e('This provider does not offer selectable preset styles.', 'asl_locator'); ?></p>
  <?php endif; ?>
</div>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Store Markers', 'asl_locator'); ?></h3>
  <?php if ($advanced_markers) : ?><p class="asl-layout-hint"><?php esc_html_e('Selected markers, labels and clustering are unavailable while Google Advanced Markers are enabled.', 'asl_locator'); ?></p><?php endif; ?>
  <label class="asl-filter-setting asl-filter-setting--stacked" for="asl-map-active_marker" data-map-setting="active_marker" <?php echo $advanced_markers ? 'hidden' : ''; ?>>
    <span class="asl-field-copy"><strong><?php esc_html_e('Selected Store Marker', 'asl_locator'); ?></strong><small><?php esc_html_e('Choose the marker shown for the selected store, or select None to disable it.', 'asl_locator'); ?></small></span>
    <select id="asl-map-active_marker" name="map[active_marker]" data-asl-map><option value="" <?php selected($active_marker, ''); ?>><?php esc_html_e('None', 'asl_locator'); ?></option><?php foreach ((array) $map_markers as $marker) : ?><option value="<?php echo esc_attr($marker->icon); ?>" <?php selected($active_marker, (string) $marker->icon); ?>><?php echo esc_html($marker->marker_name); ?></option><?php endforeach; ?></select>
  </label>
  <label class="asl-filter-setting asl-is-locked" for="asl-map-category_marker" data-map-setting="category_marker" data-asl-locked>
    <span class="asl-field-copy"><strong><?php esc_html_e('Category Markers', 'asl_locator'); ?><span class="asl-pro-badge"><?php esc_html_e('PRO', 'asl_locator'); ?></span></strong><small><?php esc_html_e('Use assigned category icons instead of regular store markers.', 'asl_locator'); ?></small></span>
    <span class="asl-appearance-switch"><input type="checkbox" role="switch" id="asl-map-category_marker" name="map[category_marker]" value="1" data-asl-map disabled aria-disabled="true"><span class="asl-appearance-switch-track" aria-hidden="true"></span></span>
  </label>
  <?php $render_map_switch('marker_title', '1'); ?>
  <div data-map-setting="marker_label" <?php echo $advanced_markers ? 'hidden' : ''; ?>><?php $render_map_switch('marker_label'); ?></div>
</div>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Visitor Location', 'asl_locator'); ?></h3>
  <?php $render_map_switch('geo_marker', '1'); ?>
  <label class="asl-filter-setting asl-filter-setting--stacked" for="asl-map-geo_marker_id" data-map-setting="geo_marker_id">
    <span class="asl-field-copy"><strong><?php esc_html_e('Visitor Marker Icon', 'asl_locator'); ?></strong><small><?php esc_html_e('Choose the marker used for the visitor’s location.', 'asl_locator'); ?></small></span>
    <select id="asl-map-geo_marker_id" name="map[geo_marker_id]" data-asl-map><option value=""><?php esc_html_e('Default marker', 'asl_locator'); ?></option><?php foreach ((array) $map_markers as $marker) : ?><option value="<?php echo esc_attr($marker->id); ?>" <?php selected($map_value('geo_marker_id'), (string) $marker->id); ?>><?php echo esc_html($marker->marker_name); ?></option><?php endforeach; ?></select>
  </label>
  <?php $render_map_switch('radius_circle'); ?>
</div>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Marker Clustering', 'asl_locator'); ?></h3>
  <div data-map-setting="cluster" <?php echo $advanced_markers ? 'hidden' : ''; ?>><?php $render_map_switch('cluster', '1'); ?></div>
  <label class="asl-filter-setting asl-filter-setting--stacked" for="asl-map-cluster_max_zoom" data-map-setting="cluster_max_zoom" <?php echo $advanced_markers ? 'hidden' : ''; ?>><span class="asl-field-copy"><strong><?php esc_html_e('Cluster Until Zoom', 'asl_locator'); ?></strong><small><?php esc_html_e('Set the zoom level where markers stop being grouped.', 'asl_locator'); ?></small></span><input type="number" id="asl-map-cluster_max_zoom" name="map[cluster_max_zoom]" data-asl-map min="0" max="22" value="<?php echo esc_attr($map_value('cluster_max_zoom', '9')); ?>"></label>
  <label class="asl-filter-setting asl-filter-setting--stacked" for="asl-map-cluster_grid_size" data-map-setting="cluster_grid_size" <?php echo $advanced_markers ? 'hidden' : ''; ?>><span class="asl-field-copy"><strong><?php esc_html_e('Cluster Area Size', 'asl_locator'); ?></strong><small><?php esc_html_e('Adjust how close markers must be before they form a cluster.', 'asl_locator'); ?></small></span><input type="number" id="asl-map-cluster_grid_size" name="map[cluster_grid_size]" data-asl-map min="20" max="200" step="5" value="<?php echo esc_attr($map_value('cluster_grid_size', '90')); ?>"></label>
</div>

<div class="asl-filter-group">
  <h3><?php esc_html_e('Zoom Behavior', 'asl_locator'); ?></h3>
  <?php
  $render_zoom_slider('zoom', __('Initial Zoom', 'asl_locator'), __('Set the zoom level when the locator first loads.', 'asl_locator'), '9');
  $render_zoom_slider('zoom_li', __('Store Selection Zoom', 'asl_locator'), __('Set the zoom level after a visitor selects a store.', 'asl_locator'), '13');
  $render_zoom_slider('search_zoom', __('Search Result Zoom', 'asl_locator'), __('Set the zoom level after an address search.', 'asl_locator'), '12');
  ?>
  <?php $render_map_switch('fit_bound', '1'); ?>
  <label class="asl-filter-setting asl-filter-setting--stacked" for="asl-map-scroll_wheel"><span class="asl-field-copy"><strong><?php esc_html_e('Scroll Wheel Zoom', 'asl_locator'); ?></strong><small><?php esc_html_e('Choose whether scrolling over the locator zooms the map.', 'asl_locator'); ?></small></span><select id="asl-map-scroll_wheel" name="map[scroll_wheel]" data-asl-map><option value="default" <?php selected($scroll_wheel, 'default'); ?>><?php esc_html_e('Provider default', 'asl_locator'); ?></option><option value="1" <?php selected($scroll_wheel, '1'); ?>><?php esc_html_e('Enabled', 'asl_locator'); ?></option><option value="0" <?php selected($scroll_wheel, '0'); ?>><?php esc_html_e('Disabled', 'asl_locator'); ?></option></select></label>
  <p class="asl-layout-hint"><a href="<?php echo esc_url(admin_url('admin.php?page=customize-map')); ?>"><?php esc_html_e('Open Map Customization', 'asl_locator'); ?></a></p>
</div>
