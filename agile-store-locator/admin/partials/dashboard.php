<?php
/** Free plugin dashboard. @var array $all_configs @var array $all_stats */
$asl_has_api       = !empty($all_configs['api_key']);
$asl_map_vendor    = isset($all_configs['map_vendor']) ? strtolower((string) $all_configs['map_vendor']) : 'google';
$asl_tile_provider = isset($all_configs['tile_provider']) ? strtolower((string) $all_configs['tile_provider']) : 'osm';
$asl_is_easy       = \AgileStoreLocator\Helper::expertise_level();
$asl_onboarding    = \AgileStoreLocator\Admin\Dashboard::get_onboarding_state((int) $all_stats['stores']);
$asl_locator_setup = \AgileStoreLocator\Admin\Dashboard::get_locator_setup_state();
if ($asl_onboarding['add_to_website'] !== $asl_locator_setup['completed']) {
    \AgileStoreLocator\Admin\Dashboard::set_onboarding_step('add_to_website', $asl_locator_setup['completed']);
    $asl_onboarding['add_to_website'] = $asl_locator_setup['completed'];
}
$asl_maplibre_credentials = [
    'osm'      => true,
    'openfreemap' => true,
    'geoapify' => !empty($all_configs['geoapify_api_key']),
    'mapbox'   => !empty($all_configs['mapbox_access_token']),
    'maptiler' => !empty($all_configs['tile_provider_api_key']),
    'custom'   => !empty($all_configs['maplibre_style_url']),
];
$asl_maplibre_ready = 'maplibre' === $asl_map_vendor
    && !empty($asl_maplibre_credentials[$asl_tile_provider]);
$asl_maps_ready = $asl_maplibre_ready
    || ('google' === $asl_map_vendor && $asl_has_api && $asl_onboarding['connect_google_maps']);
if ($asl_onboarding['connect_google_maps'] !== $asl_maps_ready) {
    \AgileStoreLocator\Admin\Dashboard::set_onboarding_step('connect_google_maps', $asl_maps_ready);
    $asl_onboarding['connect_google_maps'] = $asl_maps_ready;
}
$asl_maps_status = $asl_maps_ready
    ? 'connected'
    : (('google' === $asl_map_vendor && $asl_has_api) || 'maplibre' === $asl_map_vendor ? 'needs-attention' : 'not-configured');
$asl_map_provider_name = 'maplibre' === $asl_map_vendor ? __('Free Maps', 'asl_locator') : __('Google Maps', 'asl_locator');
$asl_completed     = count(array_filter($asl_onboarding));
$asl_step_keys     = array_keys($asl_onboarding);
$asl_current_step  = null;
foreach ($asl_step_keys as $asl_step_key) {
    if (!$asl_onboarding[$asl_step_key]) {
        $asl_current_step = $asl_step_key;
        break;
    }
}
$asl_manage_url    = admin_url('admin.php?page=manage-agile-store');
$asl_add_url       = admin_url('admin.php?page=create-agile-store');
$asl_category_url  = admin_url('admin.php?page=manage-asl-categories');
$asl_settings_url  = admin_url('admin.php?page=asl-settings');
$asl_customize_url = admin_url('admin.php?page=sl-ui-customizer');
$asl_markers_url   = admin_url('admin.php?page=manage-store-markers');
$asl_locator_view_url = $asl_locator_setup['valid_page'] ? get_permalink($asl_locator_setup['page_id']) : '';
$asl_upgrade_url   = defined('ASL_UPGRADE_URL') ? ASL_UPGRADE_URL : 'https://agilestorelocator.com/pricing/';
$asl_docs_url      = 'https://agilestorelocator.com/wiki/';
$asl_shortcode_docs_url = 'https://agilestorelocator.com/wiki/use-agile-store-locator-shortcodes/';
$asl_support_url   = 'https://agilestorelocator.com/support/';
?>
<div class="asl-dashboard">
  <svg class="asl-svg-sprite" aria-hidden="true">
    <symbol id="asl-i-book" viewBox="0 0 24 24"><path d="M4 4.8A3.8 3.8 0 0 1 7.8 4H11v15H7.8A3.8 3.8 0 0 0 4 20V4.8Zm16 0A3.8 3.8 0 0 0 16.2 4H13v15h3.2A3.8 3.8 0 0 1 20 20V4.8Z"/></symbol>
    <symbol id="asl-i-help" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="9" r="2.5"/><path d="M7.8 18c.5-2.4 2-3.7 4.2-3.7s3.7 1.3 4.2 3.7"/></symbol>
    <symbol id="asl-i-map" viewBox="0 0 24 24"><path d="m3 6 5-2 8 2 5-2v14l-5 2-8-2-5 2V6Zm5-2v14m8-12v14"/></symbol>
    <symbol id="asl-i-store" viewBox="0 0 24 24"><path d="M4 10v10h16V10M3 10l2-6h14l2 6M8 20v-6h8v6"/><path d="M3 10a3 3 0 0 0 5 2 3 3 0 0 0 4 0 3 3 0 0 0 4 0 3 3 0 0 0 5-2"/></symbol>
    <symbol id="asl-i-brush" viewBox="0 0 24 24"><path d="m14 13 6.2-6.2a2.1 2.1 0 0 0-3-3L11 10m3 3-4-4-6 6c-1.8 1.8-1.8 4.2-1.8 6.8 2.6 0 5 0 6.8-1.8l5-5Z"/></symbol>
    <symbol id="asl-i-code" viewBox="0 0 24 24"><path d="m8 5-6 7 6 7m8-14 6 7-6 7M14 3l-4 18"/></symbol>
    <symbol id="asl-i-folder" viewBox="0 0 24 24"><path d="M3 6h7l2 2h9v11H3V6Z"/></symbol>
    <symbol id="asl-i-pin" viewBox="0 0 24 24"><path d="M12 22s7-7.1 7-13a7 7 0 1 0-14 0c0 5.9 7 13 7 13Z"/><circle cx="12" cy="9" r="2.5"/></symbol>
    <symbol id="asl-i-search" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="7.5"/><path d="m16 16 5 5"/></symbol>
    <symbol id="asl-i-list" viewBox="0 0 24 24"><path d="M9 6h12M9 12h12M9 18h12M3.5 6h.01M3.5 12h.01M3.5 18h.01"/></symbol>
    <symbol id="asl-i-external" viewBox="0 0 24 24"><path d="M14 4h6v6m0-6-9 9M20 13v7H4V4h7"/></symbol>
    <symbol id="asl-i-lock" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></symbol>
    <symbol id="asl-i-copy" viewBox="0 0 24 24"><rect x="8" y="8" width="11" height="13" rx="2"/><path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h3"/></symbol>
    <symbol id="asl-i-file-export" viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6V3Z"/><path d="M14 3v5h5M12 11v7m0 0-3-3m3 3 3-3"/></symbol>
    <symbol id="asl-i-filter" viewBox="0 0 24 24"><path d="M3 5h18l-7 8v6l-4 2v-8L3 5Z"/></symbol>
    <symbol id="asl-i-register" viewBox="0 0 24 24"><rect x="4" y="3" width="16" height="18" rx="2"/><circle cx="12" cy="9" r="2.2"/><path d="M8.5 16c.4-2 1.6-3 3.5-3s3.1 1 3.5 3"/></symbol>
    <symbol id="asl-i-lead" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="12" cy="10" r="2"/><path d="M8 16c.5-1.8 1.8-2.8 4-2.8s3.5 1 4 2.8M7 7h2"/></symbol>
    <symbol id="asl-i-details" viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6V3Z"/><path d="M14 3v5h5M9 12h6M9 16h6"/></symbol>
    <symbol id="asl-i-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></symbol>
    <symbol id="asl-i-analytics" viewBox="0 0 24 24"><path d="M4 20V11h3v9M10 20V6h3v14M16 20V3h3v17M2 20h20"/></symbol>
    <symbol id="asl-i-video" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m10 9 5 3-5 3V9Z"/></symbol>
  </svg>
  <header class="asl-dash-head"><div><h1><?php esc_html_e('Welcome to Agile Store Locator', 'asl_locator'); ?> <span>👋</span></h1><p><?php esc_html_e('Manage your stores, customize your locator and help customers find you easily.', 'asl_locator'); ?></p></div><div class="asl-head-actions"><nav aria-label="<?php esc_attr_e('Dashboard links', 'asl_locator'); ?>"><a href="<?php echo esc_url($asl_docs_url); ?>" target="_blank"><svg><use href="#asl-i-book"/></svg><?php esc_html_e('Documentation', 'asl_locator'); ?></a><a href="<?php echo esc_url($asl_support_url); ?>" target="_blank"><svg><use href="#asl-i-help"/></svg><?php esc_html_e('Support', 'asl_locator'); ?></a><a href="#asl-feedback" role="button" aria-haspopup="dialog" class="asl-feedback-link"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5H4l-3 3V11.5a10 10 0 0 1 20 0Z"/><path d="M7 10h8M7 14h5"/></svg><?php esc_html_e('Send Feedback', 'asl_locator'); ?></a></nav><div class="asl-level-switch"><span><?php esc_html_e('Easy', 'asl_locator'); ?></span><label><input id="asl-level-switch" type="checkbox" <?php checked(!$asl_is_easy); ?>><i></i><b class="screen-reader-text"><?php esc_html_e('Toggle Easy or Advanced level', 'asl_locator'); ?></b></label><span><?php esc_html_e('Advanced', 'asl_locator'); ?></span></div></div></header>

  <section class="asl-card asl-setup"><div class="asl-section-title"><h2><?php esc_html_e('Your Store Locator Setup', 'asl_locator'); ?></h2><span><?php echo esc_html(sprintf(__('%1$d of 4 completed', 'asl_locator'), $asl_completed)); ?></span></div><div class="asl-setup-grid">
    <article class="asl-step asl-maps-step <?php echo $asl_maps_ready ? 'is-done' : ($asl_current_step === 'connect_google_maps' ? 'is-current' : ''); ?>" data-maps-status="<?php echo esc_attr($asl_maps_status); ?>"><span class="asl-step-number"><?php echo $asl_maps_ready ? '✓' : '1'; ?></span><div class="asl-step-icon mint"><svg><use href="#asl-i-map"/></svg></div><h3><?php echo $asl_maps_ready ? esc_html(sprintf(__('%s Ready', 'asl_locator'), $asl_map_provider_name)) : esc_html__('1. Set Up Your Map', 'asl_locator'); ?></h3><p><?php echo $asl_maps_ready ? esc_html(sprintf(__('Your locator uses %s.', 'asl_locator'), $asl_map_provider_name)) : esc_html__('Choose the map service used by your store locator.', 'asl_locator'); ?></p><span class="asl-step-status <?php echo esc_attr($asl_maps_status); ?>"><?php echo 'connected' === $asl_maps_status ? '✓ ' . esc_html__('Ready', 'asl_locator') : ('needs-attention' === $asl_maps_status ? '◉ ' . esc_html__('Needs attention', 'asl_locator') : '○ ' . esc_html__('Not configured', 'asl_locator')); ?></span><a class="asl-btn" href="#asl-map-setup" data-bs-toggle="sl_offcanvas" role="button" aria-controls="asl-map-setup"><?php echo $asl_maps_ready ? esc_html__('Manage Map', 'asl_locator') : esc_html__('Set Up Map', 'asl_locator'); ?></a></article>
    <article class="asl-step <?php echo $asl_onboarding['add_stores'] ? 'is-done' : ($asl_current_step === 'add_stores' ? 'is-current' : ''); ?>"><span class="asl-step-number"><?php echo $asl_onboarding['add_stores'] ? '✓' : '2'; ?></span><div class="asl-step-icon mint"><svg><use href="#asl-i-store"/></svg></div><h3><?php esc_html_e('2. Add Your First Store', 'asl_locator'); ?></h3><p><?php esc_html_e('Add at least one store to show on the map.', 'asl_locator'); ?></p><a class="asl-btn" href="<?php echo esc_url($asl_add_url); ?>"><?php esc_html_e('Add New Store', 'asl_locator'); ?></a></article>
    <article class="asl-step <?php echo $asl_onboarding['add_to_website'] ? 'is-done' : ($asl_locator_setup['missing_page'] ? 'is-attention' : ($asl_current_step === 'add_to_website' ? 'is-current' : '')); ?>"><span class="asl-step-number"><?php echo $asl_onboarding['add_to_website'] ? '✓' : ($asl_locator_setup['missing_page'] ? '⚠' : '3'); ?></span><div class="asl-step-icon gray"><svg><use href="#asl-i-code"/></svg></div><?php if ($asl_locator_setup['missing_page'] && !$asl_locator_setup['manual']) : ?><h3><?php esc_html_e('Locator Page Not Found', 'asl_locator'); ?></h3><p><?php esc_html_e('The page connected to your store locator is no longer available.', 'asl_locator'); ?></p><a class="asl-btn" href="#asl-create-locator" data-bs-toggle="sl_offcanvas" role="button" aria-controls="asl-create-locator"><?php esc_html_e('Add to Website', 'asl_locator'); ?></a><?php elseif ($asl_locator_setup['valid_page']) : ?><h3><?php esc_html_e('3. Added to Your Website', 'asl_locator'); ?></h3><p><?php esc_html_e('Your Store Locator page is ready.', 'asl_locator'); ?></p><a class="asl-btn" href="<?php echo esc_url($asl_locator_view_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('View Locator', 'asl_locator'); ?></a><?php elseif ($asl_locator_setup['manual']) : ?><h3><?php esc_html_e('3. Added to Your Website', 'asl_locator'); ?></h3><p><?php esc_html_e('Your locator has been added to your website.', 'asl_locator'); ?></p><a class="asl-btn" href="#asl-create-locator" data-bs-toggle="sl_offcanvas" role="button" aria-controls="asl-create-locator"><?php esc_html_e('Manage Setup', 'asl_locator'); ?></a><?php else : ?><h3><?php esc_html_e('3. Add to Your Website', 'asl_locator'); ?></h3><p><?php esc_html_e('Create a page for your store locator or add it manually.', 'asl_locator'); ?></p><a class="asl-btn" href="#asl-create-locator" data-bs-toggle="sl_offcanvas" role="button" aria-controls="asl-create-locator"><?php esc_html_e('Add to Website', 'asl_locator'); ?></a><?php endif; ?></article>
    <article class="asl-step <?php echo $asl_onboarding['customize_locator'] ? 'is-done' : ($asl_current_step === 'customize_locator' ? 'is-current' : ''); ?>"><span class="asl-step-number"><?php echo $asl_onboarding['customize_locator'] ? '✓' : '4'; ?></span><div class="asl-step-icon blue"><svg><use href="#asl-i-brush"/></svg></div><h3><?php esc_html_e('4. Customize Locator', 'asl_locator'); ?></h3><p><?php esc_html_e('Personalize the locator design, layout, filters and appearance.', 'asl_locator'); ?></p><a class="asl-btn" href="<?php echo esc_url($asl_customize_url); ?>"><?php esc_html_e('Open Customizer', 'asl_locator'); ?></a></article>
  </div><div class="asl-tip"><span>i</span><?php esc_html_e('Tip: Complete all steps to make your store locator live and help customers find you.', 'asl_locator'); ?></div></section>

  <section class="asl-stats"><article class="stat-blue"><div><svg><use href="#asl-i-store"/></svg></div><strong><?php echo esc_html($all_stats['stores']); ?></strong><span><?php esc_html_e('Stores', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_manage_url); ?>"><?php esc_html_e('Manage Stores', 'asl_locator'); ?> →</a></article><article class="stat-green"><div><svg><use href="#asl-i-folder"/></svg></div><strong><?php echo esc_html($all_stats['categories']); ?></strong><span><?php esc_html_e('Categories', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_category_url); ?>"><?php esc_html_e('Manage Categories', 'asl_locator'); ?> →</a></article><article class="stat-purple"><div><svg><use href="#asl-i-pin"/></svg></div><strong><?php echo esc_html($all_stats['markers']); ?></strong><span><?php esc_html_e('Markers', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_markers_url); ?>"><?php esc_html_e('Manage Markers', 'asl_locator'); ?> →</a></article><article class="stat-orange asl-activity-stat"><div><svg><use href="#asl-i-search"/></svg></div><strong class="asl-activity-counts"><span><b><?php echo esc_html($all_stats['searches']); ?></b><small><?php esc_html_e('Searches', 'asl_locator'); ?></small></span><i aria-hidden="true"></i><span><b><?php echo esc_html($all_stats['clicks']); ?></b><small><?php esc_html_e('Interactions', 'asl_locator'); ?></small></span></strong><span class="asl-activity-period"><?php esc_html_e('This Month', 'asl_locator'); ?></span><a href="#asl-analytics"><?php esc_html_e('View Analytics', 'asl_locator'); ?> →</a></article></section>

  <section class="asl-card asl-quick"><h2><?php esc_html_e('Quick Actions', 'asl_locator'); ?></h2><div class="asl-quick-grid"><a href="<?php echo esc_url($asl_add_url); ?>"><i class="blue">+</i><span><b><?php esc_html_e('Add New Store', 'asl_locator'); ?></b><?php esc_html_e('Add a new store to your locator.', 'asl_locator'); ?></span></a><a href="<?php echo esc_url($asl_manage_url); ?>"><i class="teal"><svg><use href="#asl-i-list"/></svg></i><span><b><?php esc_html_e('Manage Stores', 'asl_locator'); ?></b><?php esc_html_e('View, edit or delete your stores.', 'asl_locator'); ?></span></a><a href="<?php echo esc_url($asl_customize_url); ?>"><i class="purple"><svg><use href="#asl-i-brush"/></svg></i><span><b><?php esc_html_e('Customize Locator', 'asl_locator'); ?></b><?php esc_html_e('Change layout, map and design.', 'asl_locator'); ?></span></a><a href="<?php echo esc_url($asl_settings_url); ?>"><i class="orange"><svg><use href="#asl-i-map"/></svg></i><span><b><?php esc_html_e('Settings', 'asl_locator'); ?></b><?php esc_html_e('Configure the map center, zoom and search type.', 'asl_locator'); ?></span></a></div></section>

  <section class="asl-card asl-p-cont asl-store-pages-card mb-4" id="asl-store-pages" aria-labelledby="asl-store-pages-heading">
    <?php
    $asl_store_page_id = \AgileStoreLocator\Helper::get_configs('rewrite_id');
    $asl_store_page_exists = !empty($asl_store_page_id);
    $asl_store_page = $asl_store_page_exists ? get_post((int) $asl_store_page_id) : null;
    $asl_store_update_error = $asl_store_page_exists ? \AgileStoreLocator\Admin\Dashboard::store_page_update_error($asl_store_page) : '';
    ?>
    <div class="row g-4 align-items-center">
      <div class="col-12 col-lg-7 asl-store-pages-copy">
        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
          <h2 id="asl-store-pages-heading" class="mb-0"><?php esc_html_e('Store Pages', 'asl_locator'); ?></h2>
          <span class="asl-store-pages-badge"><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><?php echo $asl_store_page_exists ? ($asl_store_update_error ? esc_html__('Needs attention', 'asl_locator') : esc_html__('Configured', 'asl_locator')) : esc_html__('Recommended', 'asl_locator'); ?></span>
        </div>
        <p class="asl-store-pages-description"><?php esc_html_e('Give every store its own page so customers can view details, share locations and get directions.', 'asl_locator'); ?></p>
        <div class="d-flex flex-wrap gap-2 mb-3 asl-store-pages-actions">
          <a class="btn btn-primary d-inline-flex align-items-center gap-2" href="#asl-create-store-page" data-bs-toggle="sl_offcanvas" role="button" aria-controls="asl-create-store-page"><span class="dashicons <?php echo $asl_store_page_exists ? 'dashicons-edit' : 'dashicons-plus-alt2'; ?>" aria-hidden="true"></span><?php echo $asl_store_page_exists ? esc_html__('Update Page', 'asl_locator') : esc_html__('Create Page', 'asl_locator'); ?></a>
          <a class="btn btn-light d-inline-flex align-items-center gap-2" href="<?php echo esc_url($asl_settings_url . '#sl-detail'); ?>"><span class="dashicons dashicons-admin-generic" aria-hidden="true"></span><?php esc_html_e('Store Page Settings', 'asl_locator'); ?></a>
          <?php if ($asl_store_page && current_user_can('edit_post', $asl_store_page->ID)) : ?><a class="btn btn-link" href="<?php echo esc_url(get_edit_post_link($asl_store_page->ID, 'raw')); ?>"><?php esc_html_e('Edit in WordPress', 'asl_locator'); ?> <span aria-hidden="true">↗</span></a><?php endif; ?>
        </div>
        <ul class="row g-3 list-unstyled mb-0 asl-store-pages-benefits">
          <li class="col-12 col-sm-4 d-flex gap-2"><span class="asl-store-benefit-icon blue dashicons dashicons-admin-links" aria-hidden="true"></span><div><strong><?php esc_html_e('Unique URL', 'asl_locator'); ?></strong><span><?php esc_html_e('Each store gets its own page.', 'asl_locator'); ?></span></div></li>
          <li class="col-12 col-sm-4 d-flex gap-2"><span class="asl-store-benefit-icon teal dashicons dashicons-share" aria-hidden="true"></span><div><strong><?php esc_html_e('Easy to Share', 'asl_locator'); ?></strong><span><?php esc_html_e('Share on social media or anywhere.', 'asl_locator'); ?></span></div></li>
          <li class="col-12 col-sm-4 d-flex gap-2"><span class="asl-store-benefit-icon purple dashicons dashicons-search" aria-hidden="true"></span><div><strong><?php esc_html_e('Store Details', 'asl_locator'); ?></strong><span><?php esc_html_e('Help customers plan their visit.', 'asl_locator'); ?></span></div></li>
        </ul>
      </div>
      <div class="col-12 col-lg-5" aria-hidden="true">
        <div class="asl-store-pages-art">
          <div class="asl-store-pages-art-map"><svg viewBox="0 0 48 60" fill="none"><path d="M24 58S3 35 3 23a21 21 0 0 1 42 0c0 12-21 35-21 35Z" fill="#f15c55" stroke="#fff" stroke-width="2"/><circle cx="24" cy="23" r="8" fill="#fff"/></svg></div>
          <div class="asl-store-pages-mini-browser">
            <div class="asl-store-pages-browser-bar"><i></i><i></i><i></i></div>
            <div class="asl-store-pages-mini-body">
              <svg class="asl-store-pages-building" viewBox="0 0 100 140" fill="none"><rect width="100" height="140" rx="6" fill="#dfedff"/><circle cx="80" cy="22" r="12" fill="#fff"/><path fill="#bed6c7" d="m0 90 24-24 29 32 47-28v70H0z"/><path fill="#fff" d="M14 48h72v83H14z"/><path fill="#2569df" d="M9 53h82L80 35H20z"/><path fill="#d7e8fc" d="M23 65h22v25H23zm33 0h22v25H56z"/><path fill="#285380" d="M39 103h23v28H39z"/><path stroke="#aac9eb" stroke-width="2" d="M34 65v25m33-25v25"/><path stroke="#c4d3c9" stroke-width="3" d="M0 133h100"/></svg>
              <div class="asl-store-pages-mini-details"><strong><?php esc_html_e('Downtown Store', 'asl_locator'); ?></strong><div class="asl-store-pages-skeleton"><i></i><i></i><i></i></div><div class="asl-store-pages-mini-actions"><span><span class="dashicons dashicons-location"></span><?php esc_html_e('Directions', 'asl_locator'); ?></span><span><span class="dashicons dashicons-phone"></span><?php esc_html_e('Call Store', 'asl_locator'); ?></span><span><span class="dashicons dashicons-admin-site-alt3"></span><?php esc_html_e('Website', 'asl_locator'); ?></span></div></div>
            </div>
          </div>
          <div class="asl-store-pages-url"><span class="dashicons dashicons-admin-links"></span><span>…/stores/<b>downtown</b></span></div>
        </div>
      </div>
    </div>
  </section>


  <?php if (false) : // Pro analytics dashboard is not rendered in the Free edition. ?>
  <section class="asl-card asl-pro-analytics asl-p-cont asl-new-bg asl-main-dashboard" id="asl-analytics">
    <div class="asl-analytics-sec">
      <div class="asl-pro-analytics__header">
        <div>
          <span class="asl-pro-analytics__eyebrow"><?php esc_html_e('Performance', 'asl_locator'); ?></span>
          <h2><?php esc_html_e('Analytics Overview', 'asl_locator'); ?></h2>
          <p><?php esc_html_e('See how customers find and interact with your stores during the selected period.', 'asl_locator'); ?></p>
        </div>
        <div class="asl-pro-analytics__controls">
          <label for="sl-datetimepicker"><?php esc_html_e('Date range', 'asl_locator'); ?></label>
          <div class="asl-pro-analytics__date">
            <span class="dashicons dashicons-calendar-alt" aria-hidden="true"></span>
            <input type="text" id="sl-datetimepicker" class="form-control" aria-label="<?php esc_attr_e('Analytics date range', 'asl_locator'); ?>">
          </div>
          <button type="button" title="<?php esc_attr_e('Export Analytics', 'asl_locator'); ?>" id="sl-btn-export-stats" class="btn btn-outline-primary asl-pro-analytics__export">
            <span class="dashicons dashicons-download" aria-hidden="true"></span>
            <?php esc_html_e('Export', 'asl_locator'); ?>
          </button>
        </div>
      </div>
      <div class="tab-content" id="asl-tabs">
        <div class="tab-pane active asl-dashboard-section asl-pro-analytics__chart" id="asl-chart">
          <div class="asl-pro-analytics__card-title">
            <div><span class="dashicons dashicons-chart-line" aria-hidden="true"></span><h3><?php esc_html_e('Searches & Interactions', 'asl_locator'); ?></h3></div>
            <small><?php esc_html_e('Activity over time', 'asl_locator'); ?></small>
          </div>
          <div class="canvas-holder">
            <canvas id="asl_search_canvas"></canvas>
          </div>
        </div>

        <div class="tab-pane active asl-dashboard-section" id="asl-views">
          <select id="asl-search-len" class="d-none" aria-hidden="true">
            <option value="5" selected>5</option>
            <option value="0"><?php echo esc_attr__('ALL','asl_locator') ?></option>
          </select>
         <div class="row">
           <div class="col-md-6">
            <div class="asl-table-box asl-analytics-card">
              <div class="asl-analytics-card-heading">
                <div class="asl-analytics-card-title">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.1 12s3.6-6 9.9-6 9.9 6 9.9 6-3.6 6-9.9 6-9.9-6-9.9-6Zm9.9 3.2a3.2 3.2 0 1 0 0-6.4 3.2 3.2 0 0 0 0 6.4Z"/></svg>
                  <h3><?php echo esc_html__('Most Viewed Stores','asl_locator') ?></h3>
                </div>
                <button type="button" class="btn asl-view-all"><?php echo esc_html__('View All','asl_locator') ?></button>
              </div>
              <div class="asl-list-header">
                <div class="row">
                  <div class="col-7">
                    <div class="list-items">
                      <?php echo esc_attr__('Store','asl_locator') ?>
                    </div>
                  </div>
                  <div class="col-2 text-right">
                    <div class="list-items">
                      <button type="button" class="asl-sort-button is-active" data-list="stores" data-sort="views" data-direction="desc" aria-sort="descending">
                        <?php echo esc_html__('Views','asl_locator') ?><span class="asl-sort-indicator" aria-hidden="true">&#9660;</span>
                      </button>
                    </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="list-items">
                      <button type="button" class="asl-sort-button" data-list="stores" data-sort="change" data-direction="desc" aria-sort="none">
                        <?php echo esc_html__('Change','asl_locator') ?><span class="asl-sort-indicator" aria-hidden="true"></span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <ul class="list-group" id="asl-stores-views">
              </ul>
            </div>
           </div>
           <div class="col-md-6"> 
            <div class="asl-table-box asl-analytics-card">
              <div class="asl-analytics-card-heading">
                <div class="asl-analytics-card-title">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7Zm0 9.6A2.6 2.6 0 1 1 12 6a2.6 2.6 0 0 1 0 5.6Z"/></svg>
                  <h3><?php echo esc_html__('Top Search Locations','asl_locator') ?></h3>
                </div>
                <button type="button" class="btn asl-view-all"><?php echo esc_html__('View All','asl_locator') ?></button>
              </div>
              <div class="asl-list-header">
                <div class="row">
                  <div class="col-7">
                    <div class="list-items">
                      <?php echo esc_attr__('Location','asl_locator') ?>
                    </div>
                  </div>
                  <div class="col-2 text-right">
                    <div class="list-items">
                      <button type="button" class="asl-sort-button is-active" data-list="searches" data-sort="views" data-direction="desc" aria-sort="descending">
                        <?php echo esc_html__('Searches','asl_locator') ?><span class="asl-sort-indicator" aria-hidden="true">&#9660;</span>
                      </button>
                    </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="list-items">
                      <button type="button" class="asl-sort-button" data-list="searches" data-sort="change" data-direction="desc" aria-sort="none">
                        <?php echo esc_html__('Change','asl_locator') ?><span class="asl-sort-indicator" aria-hidden="true"></span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <ul class="list-group" id="asl-searches-views">
              </ul>
            </div>
           </div>
         </div>

        </div>
      </div> 
    </div>
  </section>

  <section class="asl-card asl-more-features" aria-labelledby="asl-more-features-title">
    <div class="asl-more-features__heading"><h2 id="asl-more-features-title"><?php esc_html_e('Explore More Features', 'asl_locator'); ?></h2><p><?php esc_html_e('Get more from your store locator with these powerful tools.', 'asl_locator'); ?></p></div>
    <div class="asl-more-features__grid">
      <article class="asl-feature-card feature-import"><i><svg><use href="#asl-i-file-export"/></svg></i><div><h3><?php esc_html_e('Import / Export', 'asl_locator'); ?></h3><p><?php esc_html_e('Import stores in bulk or export your location data with ease.', 'asl_locator'); ?></p><a href="https://agilestorelocator.com/wiki/can-import-stores-using-excel-sheet/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Manage', 'asl_locator'); ?> <span>→</span></a></div></article>
      <article class="asl-feature-card feature-search"><i><svg><use href="#asl-i-search"/></svg></i><div><h3><?php esc_html_e('Search Widget', 'asl_locator'); ?></h3><p><?php esc_html_e('Add a powerful location search box anywhere on your site.', 'asl_locator'); ?></p><a href="https://agilestorelocator.com/wiki/address-search-widget/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Configure', 'asl_locator'); ?> <span>→</span></a></div></article>
      <article class="asl-feature-card feature-register"><i><svg><use href="#asl-i-register"/></svg></i><div><h3><?php esc_html_e('Store Registration Form', 'asl_locator'); ?></h3><p><?php esc_html_e('Allow businesses to submit new store locations from the frontend.', 'asl_locator'); ?></p><a href="https://agilestorelocator.com/wiki/store-registration-form/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Configure', 'asl_locator'); ?> <span>→</span></a></div></article>
      <article class="asl-feature-card feature-details"><i><svg><use href="#asl-i-details"/></svg></i><div><h3><?php esc_html_e('Store Detail Page', 'asl_locator'); ?></h3><p><?php esc_html_e('Create dedicated pages with complete information for each location.', 'asl_locator'); ?></p><a href="https://agilestorelocator.com/wiki/store-details-page/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Manage', 'asl_locator'); ?> <span>→</span></a></div></article>
      <article class="asl-feature-card feature-lead"><i><svg><use href="#asl-i-mail"/></svg></i><div><h3><?php esc_html_e('Lead Contact Form', 'asl_locator'); ?></h3><p><?php esc_html_e('Capture inquiries and route leads to the appropriate store automatically.', 'asl_locator'); ?></p><a href="https://agilestorelocator.com/wiki/integrating-wpforms-with-agile-store-locator/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Configure', 'asl_locator'); ?> <span>→</span></a></div></article>
    </div>
  </section>
  <?php endif; ?>

  <section class="asl-card asl-analytics" id="asl-analytics"><div class="asl-section-title"><h2><?php esc_html_e('Analytics Overview', 'asl_locator'); ?></h2><a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('View Full Analytics', 'asl_locator'); ?> →</a></div><div class="asl-analytics-content" aria-hidden="true"><div class="asl-chart"><b><?php esc_html_e('Searches (Last 7 Days)', 'asl_locator'); ?></b><svg viewBox="0 0 650 205" preserveAspectRatio="none"><g class="grid"><path d="M30 20H640M30 65H640M30 110H640M30 155H640M30 200H640"/></g><path class="area" d="M30 160 130 90 230 42 330 125 430 50 525 65 625 140V200H30Z"/><path class="line" d="M30 160 130 90 230 42 330 125 430 50 525 65 625 140"/><g class="dots"><circle cx="30" cy="160" r="4"/><circle cx="130" cy="90" r="4"/><circle cx="230" cy="42" r="4"/><circle cx="330" cy="125" r="4"/><circle cx="430" cy="50" r="4"/><circle cx="525" cy="65" r="4"/><circle cx="625" cy="140" r="4"/></g></svg><div class="asl-days"><span>May 8</span><span>May 9</span><span>May 10</span><span>May 11</span><span>May 12</span><span>May 13</span><span>May 14</span></div></div><div class="asl-top-locations"><b><?php esc_html_e('Top Searched Locations', 'asl_locator'); ?></b><ol><li><span>New York, NY</span><em>32</em></li><li><span>Los Angeles, CA</span><em>28</em></li><li><span>Chicago, IL</span><em>19</em></li><li><span>Houston, TX</span><em>14</em></li><li><span>Miami, FL</span><em>11</em></li></ol></div></div><div class="asl-pro-lock"><svg><use href="#asl-i-lock"/></svg><strong><?php esc_html_e('Analytics is a Pro feature', 'asl_locator'); ?></strong><span><?php esc_html_e('Upgrade to unlock detailed store performance insights.', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a></div></section>

  <section class="asl-upgrade">
    <div class="asl-upgrade-art"><div class="map-road one"></div><div class="map-road two"></div><span class="pin one">●</span><span class="pin two">●</span><b><svg><use href="#asl-i-lock"/></svg></b></div>
    <div><h2><?php esc_html_e('Unlock More Powerful Features', 'asl_locator'); ?></h2><p><?php esc_html_e('Upgrade to Pro and take your store locator to the next level.', 'asl_locator'); ?></p>
      <div class="asl-feature-pills">
        <span class="cyan"><svg><use href="#asl-i-file-export"/></svg><?php esc_html_e('CSV Import & Export', 'asl_locator'); ?></span>
        <span class="cyan"><svg><use href="#asl-i-filter"/></svg><?php esc_html_e('Advanced Filters', 'asl_locator'); ?></span>
        <span class="green"><svg><use href="#asl-i-search"/></svg><?php esc_html_e('Search Widget', 'asl_locator'); ?></span>
        <span class="blue"><svg><use href="#asl-i-register"/></svg><?php esc_html_e('Registration Form', 'asl_locator'); ?></span>
        <span class="blue"><svg><use href="#asl-i-lead"/></svg><?php esc_html_e('Lead Forms', 'asl_locator'); ?></span>
        <span class="blue"><svg><use href="#asl-i-analytics"/></svg><?php esc_html_e('Analytics & Reports', 'asl_locator'); ?></span>
      </div>
      <div class="asl-upgrade-actions"><a class="primary" href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a><a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Compare Plans', 'asl_locator'); ?> →</a></div>
    </div>
  </section>

  <section class="asl-bottom-grid">
    <article class="asl-card asl-system-info">
      <h2><?php esc_html_e('Basic System Info', 'asl_locator'); ?></h2>
      <dl>
        <div><dt><?php esc_html_e('Plugin Version', 'asl_locator'); ?></dt><dd><?php echo esc_html(ASL_CVERSION); ?></dd></div>
        <div><dt><?php esc_html_e('Google Maps API', 'asl_locator'); ?></dt><dd class="<?php echo esc_attr('connected' === $asl_maps_status ? 'connected' : ('needs-attention' === $asl_maps_status ? 'attention' : 'missing')); ?>"><?php echo 'connected' === $asl_maps_status ? '● ' . esc_html__('Connected', 'asl_locator') : ('needs-attention' === $asl_maps_status ? '● ' . esc_html__('Needs attention', 'asl_locator') : '● ' . esc_html__('Not configured', 'asl_locator')); ?></dd></div>
        <div><dt><?php esc_html_e('PHP Version', 'asl_locator'); ?></dt><dd><?php echo esc_html(phpversion()); ?></dd></div>
        <div><dt><?php esc_html_e('WordPress Version', 'asl_locator'); ?></dt><dd><?php echo esc_html(get_bloginfo('version')); ?></dd></div>
        <div><dt><?php esc_html_e('Memory Limit', 'asl_locator'); ?></dt><dd><?php echo esc_html(ini_get('memory_limit')); ?></dd></div>
      </dl>
      <a class="asl-card-link" href="<?php echo esc_url(admin_url('site-health.php')); ?>"><?php esc_html_e('System Status', 'asl_locator'); ?> →</a>
    </article>
    <article class="asl-card asl-resources"><h2><?php esc_html_e('Help & Resources', 'asl_locator'); ?></h2><ul>
      <li class="resource-docs"><span><i><svg><use href="#asl-i-book"/></svg></i><?php esc_html_e('Documentation', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_docs_url); ?>" target="_blank"><?php esc_html_e('View Docs', 'asl_locator'); ?> →</a></li>
      <li class="resource-video"><span><i><svg><use href="#asl-i-video"/></svg></i><?php esc_html_e('Video Tutorials', 'asl_locator'); ?></span><a href="https://www.youtube.com/@agilelogix" target="_blank"><?php esc_html_e('Watch Now', 'asl_locator'); ?> →</a></li>
      <li class="resource-support"><span><i><svg><use href="#asl-i-help"/></svg></i><?php esc_html_e('Support Center', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_support_url); ?>" target="_blank"><?php esc_html_e('Get Help', 'asl_locator'); ?> →</a></li>
    </ul><div class="asl-help"><span><b>i</b><?php esc_html_e("Need help? We're here for you!", 'asl_locator'); ?><small><?php esc_html_e('Contact our support team anytime.', 'asl_locator'); ?></small></span><a href="<?php echo esc_url($asl_support_url); ?>" target="_blank"><?php esc_html_e('Get Support', 'asl_locator'); ?></a></div></article>
    <article class="asl-card asl-shortcode" id="asl-shortcode"><h2><?php esc_html_e('Store Locator Shortcode', 'asl_locator'); ?></h2><p><?php esc_html_e('Add your locator anywhere using the shortcode below.', 'asl_locator'); ?></p><div><code>[ASL_STORELOCATOR]</code><button type="button" class="asl-copy-shortcode" data-copy-label="<?php esc_attr_e('Copy shortcode', 'asl_locator'); ?>" aria-label="<?php esc_attr_e('Copy shortcode', 'asl_locator'); ?>"><svg><use href="#asl-i-copy"/></svg></button></div><p><?php esc_html_e('Works with WordPress, Elementor, and other page builders.', 'asl_locator'); ?></p><a class="asl-shortcode-guide" href="<?php echo esc_url($asl_shortcode_docs_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Shortcode Guide', 'asl_locator'); ?> →</a></article>
  </section>

  <section class="asl-rating" id="asl-rating" aria-labelledby="asl-rating-title">
    <div class="asl-rating-mark" aria-hidden="true"><span>★</span><i>✦</i><i>✦</i><i>✦</i></div>
    <div class="asl-rating-copy">
      <div class="asl-rating-stars" aria-hidden="true">★ ★ ★ ★ ★</div>
      <h2 id="asl-rating-title"><?php esc_html_e('Enjoying Agile Store Locator?', 'asl_locator'); ?></h2>
      <p><?php esc_html_e("If Agile Store Locator is helping you and your customers find locations easily, we'd appreciate a quick review on WordPress.org.", 'asl_locator'); ?></p>
    </div>
    <div class="asl-rating-actions">
      <div>
        <a class="asl-rating-review" href="https://wordpress.org/support/plugin/agile-store-locator/reviews/#new-post" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Leave a Review', 'asl_locator'); ?> <span aria-hidden="true">→</span><svg aria-hidden="true"><use href="#asl-i-external"/></svg></a>
        <button type="button" data-rating-action="later"><?php esc_html_e('Maybe Later', 'asl_locator'); ?></button>
        <button type="button" data-rating-action="reviewed"><?php esc_html_e('Already Reviewed', 'asl_locator'); ?></button>
      </div>
      <p><span aria-hidden="true">♢</span><?php esc_html_e('It only takes a minute and it means a lot to us. Thank you!', 'asl_locator'); ?></p>
    </div>
  </section>
</div>

<div class="asl-p-cont asl-maps-setup-shell">
  <aside class="sl_offcanvas sl_offcanvas-end" tabindex="-1" id="asl-map-setup" aria-labelledby="asl-map-setup-label">
    <div class="sl_offcanvas-header asl-maps-setup-head">
      <div class="asl-maps-setup-title"><i><svg><use href="#asl-i-map"/></svg></i><div><h5 id="asl-map-setup-label"><?php esc_html_e('Set Up Your Map', 'asl_locator'); ?></h5><p><?php esc_html_e('Choose the map service used by your store locator.', 'asl_locator'); ?></p></div></div>
      <button type="button" class="btn-close" data-bs-dismiss="sl_offcanvas" aria-label="<?php esc_attr_e('Close', 'asl_locator'); ?>"></button>
    </div>
    <div class="sl_offcanvas-body asl-maps-setup-body">
      <div class="asl-maps-state is-<?php echo esc_attr($asl_maps_status); ?>"><i></i><div><strong><?php echo $asl_maps_ready ? esc_html(sprintf(__('%s is ready', 'asl_locator'), $asl_map_provider_name)) : esc_html__('Your map needs setup', 'asl_locator'); ?></strong><span><?php echo $asl_maps_ready ? esc_html__('Your selected map service is ready for the store locator.', 'asl_locator') : esc_html__('Select one of the simple options below to continue.', 'asl_locator'); ?></span></div></div>
      <div class="asl-maps-feedback" id="asl-maps-feedback" role="status" aria-live="polite"></div>
      <fieldset class="asl-map-provider-choices">
        <legend><?php esc_html_e('Choose a map service', 'asl_locator'); ?></legend>
        <label class="asl-map-provider-choice"><span class="asl-map-provider-heading d-flex align-items-center"><input class="form-check-input" type="radio" name="asl-dashboard-map-provider" value="google" <?php checked('google', $asl_map_vendor); ?>><strong><?php esc_html_e('Google Maps', 'asl_locator'); ?></strong></span><small><?php esc_html_e('Connect your Google account or use an existing API key.', 'asl_locator'); ?></small><i aria-hidden="true">✓</i></label>
        <label class="asl-map-provider-choice"><span class="asl-map-provider-heading d-flex align-items-center"><input class="form-check-input" type="radio" name="asl-dashboard-map-provider" value="maplibre" <?php checked('maplibre', $asl_map_vendor); ?>><strong><?php esc_html_e('Free Maps (MapLibre)', 'asl_locator'); ?></strong></span><small><?php esc_html_e('OpenStreetMap with Nominatim address geocoding. No API keys required.', 'asl_locator'); ?></small><i aria-hidden="true">✓</i></label>
      </fieldset>
      <div class="asl-dashboard-provider-panel mt-3" data-map-provider-panel="google">
        <?php \AgileStoreLocator\Admin\GoogleConnect::render(); ?>
        <div id="asl-manual-key-panel">
        <div class="asl-create-field asl-maps-key-field"><label for="asl-dashboard-maps-key"><?php esc_html_e('Google Maps API key', 'asl_locator'); ?></label><div class="asl-maps-key-control"><input type="password" id="asl-dashboard-maps-key" class="form-control" value="<?php echo esc_attr($all_configs['api_key']); ?>" placeholder="<?php esc_attr_e('Paste your Google Maps API key', 'asl_locator'); ?>"><button type="button" id="asl-toggle-maps-key" aria-label="<?php esc_attr_e('Show API key', 'asl_locator'); ?>"><?php esc_html_e('Show', 'asl_locator'); ?></button></div><small><?php esc_html_e('The key must allow Maps JavaScript API requests from this website.', 'asl_locator'); ?></small></div>
        <a class="asl-maps-guide" href="https://agilestorelocator.com/blog/enable-google-maps-api-agile-store-locator-plugin/" target="_blank" rel="noopener noreferrer"><svg><use href="#asl-i-book"/></svg><?php esc_html_e('Google Maps setup guide', 'asl_locator'); ?> →</a>
        <div class="asl-maps-video">
          <div class="asl-maps-video-head"><h6><?php esc_html_e('Video tutorial', 'asl_locator'); ?></h6><a href="https://www.youtube.com/watch?v=gJWVJsUOasg" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Watch on YouTube', 'asl_locator'); ?> →</a></div>
          <div class="asl-maps-video-frame"><iframe src="https://www.youtube-nocookie.com/embed/gJWVJsUOasg" title="<?php esc_attr_e('How to configure a Google Maps API key', 'asl_locator'); ?>" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>
        </div>
      </div>
      </div>
      <div class="asl-dashboard-provider-panel" data-map-provider-panel="maplibre">
        <div class="asl-maplibre-summary"><strong><?php esc_html_e('Simple free setup', 'asl_locator'); ?></strong><p><?php esc_html_e('MapLibre will use OpenStreetMap tiles so you can get started without a map API key.', 'asl_locator'); ?></p></div>
        <a class="asl-maps-guide" href="<?php echo esc_url($asl_settings_url); ?>"><svg><use href="#asl-i-map"/></svg><?php esc_html_e('Configure another MapLibre provider in ASL Settings', 'asl_locator'); ?> →</a>
      </div>
      <div id="asl-maps-validation-canvas" class="asl-maps-validation-canvas" aria-hidden="true"></div>
    </div>
    <div class="asl-maps-setup-footer"><button type="button" class="btn asl-create-cancel" data-bs-dismiss="sl_offcanvas"><?php esc_html_e('Cancel', 'asl_locator'); ?></button><button type="button" class="btn asl-maps-verify" id="asl-maps-verify"><?php esc_html_e('Save and Verify', 'asl_locator'); ?></button></div>
  </aside>
</div>

<div class="asl-p-cont asl-create-locator-shell">
  <aside class="sl_offcanvas sl_offcanvas-end" tabindex="-1" id="asl-create-locator" aria-labelledby="asl-create-locator-label">
    <div class="sl_offcanvas-header asl-create-locator-head">
      <div class="asl-create-locator-title"><i><svg><use href="#asl-i-pin"/></svg></i><div><h5 id="asl-create-locator-label"><?php esc_html_e('Create Locator Page', 'asl_locator'); ?></h5><p><?php esc_html_e('Add your store locator to a new WordPress page.', 'asl_locator'); ?></p></div></div>
      <button type="button" class="btn-close" data-bs-dismiss="sl_offcanvas" aria-label="<?php esc_attr_e('Close', 'asl_locator'); ?>"></button>
    </div>
    <div class="sl_offcanvas-body asl-create-locator-body">
      <div class="asl-create-intro"><i>i</i><p><strong><?php esc_html_e('Ready to publish your locator?', 'asl_locator'); ?></strong><span><?php esc_html_e('We’ll prepare a new page with the Agile Store Locator shortcode already added.', 'asl_locator'); ?></span></p></div>
      <div class="asl-create-feedback" id="asl-create-page-feedback" role="status" aria-live="polite"></div>

      <div class="asl-create-field">
        <label for="asl-locator-page-title"><?php esc_html_e('Page title', 'asl_locator'); ?></label>
        <input type="text" id="asl-locator-page-title" class="form-control" value="<?php esc_attr_e('Store Locator', 'asl_locator'); ?>" placeholder="<?php esc_attr_e('Enter a page title', 'asl_locator'); ?>">
        <small><?php esc_html_e('This is the title visitors will see on your website.', 'asl_locator'); ?></small>
      </div>

      <div class="asl-create-field">
        <label><?php esc_html_e('Locator shortcode', 'asl_locator'); ?></label>
        <div class="asl-create-code"><code>[ASL_STORELOCATOR]</code><span><svg><use href="#asl-i-copy"/></svg></span></div>
        <small><?php esc_html_e('The shortcode will be inserted into the page automatically.', 'asl_locator'); ?></small>
      </div>

      <div class="asl-create-preview">
        <div class="asl-create-preview-head"><span><?php esc_html_e('Page preview', 'asl_locator'); ?></span><em><?php esc_html_e('Draft', 'asl_locator'); ?></em></div>
        <div class="asl-create-browser"><div><i></i><i></i><i></i></div><section><span><?php esc_html_e('Store Locator', 'asl_locator'); ?></span><p><?php esc_html_e('Your interactive store locator will appear here.', 'asl_locator'); ?></p><b><svg><use href="#asl-i-pin"/></svg></b></section></div>
      </div>

      <div class="asl-create-checklist"><h6><?php esc_html_e('What happens next?', 'asl_locator'); ?></h6><ul><li><b>✓</b><?php esc_html_e('A new WordPress page will be created', 'asl_locator'); ?></li><li><b>✓</b><?php esc_html_e('The locator shortcode will be inserted', 'asl_locator'); ?></li><li><b>✓</b><?php esc_html_e('You can review the page before publishing', 'asl_locator'); ?></li></ul></div>

      <section class="asl-manual-setup" aria-labelledby="asl-manual-setup-title">
        <h6 id="asl-manual-setup-title"><?php esc_html_e('Already using a page builder?', 'asl_locator'); ?></h6>
        <p><?php esc_html_e('Copy the shortcode and add it to any page using WordPress, Elementor, Divi, or another page builder.', 'asl_locator'); ?></p>
        <div class="asl-manual-code"><code>[ASL_STORELOCATOR]</code><button type="button" class="asl-copy-shortcode asl-manual-copy" data-copy-label="<?php esc_attr_e('Copy Shortcode', 'asl_locator'); ?>" aria-label="<?php esc_attr_e('Copy shortcode', 'asl_locator'); ?>"><span class="asl-copy-button-label"><?php esc_html_e('Copy Shortcode', 'asl_locator'); ?></span></button></div>
        <small><?php esc_html_e('Using Elementor? Add a Shortcode widget and paste the shortcode above.', 'asl_locator'); ?></small>
        <button type="button" class="btn btn-outline-success asl-manual-complete mt-2 btn-sm" id="asl-manual-complete"><?php esc_html_e("I've Added It", 'asl_locator'); ?></button>
      </section>
    </div>
    <div class="asl-create-locator-footer"><button type="button" class="btn asl-create-cancel" data-bs-dismiss="sl_offcanvas"><?php esc_html_e('Cancel', 'asl_locator'); ?></button><button type="button" class="btn asl-create-submit" id="asl-create-page-submit"><?php esc_html_e('Create Page', 'asl_locator'); ?> <span>→</span></button></div>
  </aside>
</div>
<div class="asl-p-cont asl-create-locator-shell">
  <aside class="sl_offcanvas sl_offcanvas-end" tabindex="-1" id="asl-create-store-page" aria-labelledby="asl-create-store-page-label">
    <div class="sl_offcanvas-header asl-create-locator-head">
      <div class="asl-create-locator-title"><i><svg><use href="#asl-i-details"/></svg></i><div><h5 id="asl-create-store-page-label"><?php echo $asl_store_page_exists ? esc_html__('Update Store Page', 'asl_locator') : esc_html__('Create Store Page', 'asl_locator'); ?></h5><p><?php esc_html_e('Set up individual store detail pages on your website.', 'asl_locator'); ?></p></div></div>
      <button type="button" class="btn-close" data-bs-dismiss="sl_offcanvas" aria-label="<?php esc_attr_e('Close', 'asl_locator'); ?>"></button>
    </div>
    <div class="sl_offcanvas-body asl-create-locator-body">
      <div class="asl-create-feedback" id="asl-store-page-feedback" role="status" aria-live="polite"></div>
      <?php if ($asl_store_update_error) : ?><div class="alert alert-warning" role="status"><?php echo esc_html($asl_store_update_error); ?>
        <?php if ($asl_store_page && current_user_can('edit_post', $asl_store_page->ID)) : ?><a class="d-block mt-2" href="<?php echo esc_url(get_edit_post_link($asl_store_page->ID, 'raw')); ?>"><?php esc_html_e('Open page editor', 'asl_locator'); ?></a><?php endif; ?>
      </div><?php endif; ?>
      <?php if ($asl_store_page && 'publish' === $asl_store_page->post_status && !$asl_store_update_error) : ?><p class="text-muted mb-3"><?php esc_html_e('This page is published. Updated options will take effect immediately.', 'asl_locator'); ?></p><?php endif; ?>
      <div class="asl-create-field mb-4">
        <label for="asl-store-page-title"><?php esc_html_e('Page title', 'asl_locator'); ?></label>
        <input type="text" class="form-control" id="asl-store-page-title" aria-describedby="asl-store-title-help" value="<?php echo esc_attr($asl_store_page_exists && $asl_store_page ? $asl_store_page->post_title : __('Stores', 'asl_locator')); ?>">
        <small id="asl-store-title-help"><?php esc_html_e('This will be used as the base title for store pages.', 'asl_locator'); ?></small>
      </div>
      <div class="row g-3 mb-3 align-items-stretch">
        <div class="col-12 col-md-6">
          <div class="card h-100 asl-store-panel-card">
            <div class="card-body p-3">
              <div class="d-flex gap-3 mb-3 asl-store-section-heading"><span class="asl-store-section-icon dashicons dashicons-admin-generic" aria-hidden="true"></span><div><h6 id="asl-store-options-label"><?php esc_html_e('Page options', 'asl_locator'); ?></h6><p><?php esc_html_e('Choose what information to show on each store page.', 'asl_locator'); ?></p></div></div>
              <fieldset id="asl-store-page-options" aria-labelledby="asl-store-options-label">
        <?php
        $asl_store_options = [
            'map' => [__('Show map', 'asl_locator'), true],
            'hide_hours' => [__('Show business hours', 'asl_locator'), empty($all_configs['hide_hours'])],
            'closed_label' => [__('Show closed days', 'asl_locator'), false],
            'additional_info' => [__('Show descriptions', 'asl_locator'), !empty($all_configs['additional_info'])],
            'show_categories' => [__('Show categories', 'asl_locator'), !empty($all_configs['show_categories'])],
            'store_page_show_country' => [__('Show country in address', 'asl_locator'), !isset($all_configs['store_page_show_country']) || '0' !== $all_configs['store_page_show_country']],
            'store_schema' => [__('Enable structured data', 'asl_locator'), !empty($all_configs['store_schema'])],
            'coords_direction' => [__('Use coordinates for directions', 'asl_locator'), false],
        ];
        $asl_saved_shortcode = $asl_store_page_exists && $asl_store_page ? \AgileStoreLocator\Admin\Dashboard::get_store_page_shortcode($asl_store_page->post_content) : null;
        $asl_saved_attributes = $asl_saved_shortcode ? shortcode_parse_atts($asl_saved_shortcode[3]) : [];
        $asl_saved_attributes = is_array($asl_saved_attributes) ? $asl_saved_attributes : [];
        foreach ($asl_store_options as $asl_key => &$asl_details) {
            if ('coords_direction' === $asl_key) {
                $asl_details[1] = isset($asl_saved_attributes[$asl_key]);
            } elseif (isset($asl_saved_attributes[$asl_key])) {
                $asl_details[1] = 'hide_hours' === $asl_key ? '1' !== $asl_saved_attributes[$asl_key] : '1' === $asl_saved_attributes[$asl_key];
            }
        }
        unset($asl_details);
        $asl_store_option_help = [
            'map' => __('Display a map showing the store location.', 'asl_locator'),
            'hide_hours' => __('Display the store’s opening hours.', 'asl_locator'),
            'closed_label' => __('Include days when the store is closed in business hours.', 'asl_locator'),
            'additional_info' => __('Display the store descriptions when provided.', 'asl_locator'),
            'show_categories' => __('Display the categories assigned to the store.', 'asl_locator'),
            'store_page_show_country' => __('Include the country in the store address.', 'asl_locator'),
            'store_schema' => __('Add store structured data for search engines.', 'asl_locator'),
            'coords_direction' => __('Use latitude and longitude instead of the address for directions.', 'asl_locator'),
        ];
        foreach ($asl_store_options as $asl_option => $asl_option_details) : ?>
                <div class="d-flex align-items-center gap-2 asl-store-option-row">
                  <div class="form-check form-switch flex-grow-1 mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="asl-store-option-<?php echo esc_attr($asl_option); ?>" data-store-page-option="<?php echo esc_attr($asl_option); ?>" aria-describedby="asl-store-help-<?php echo esc_attr($asl_option); ?>" <?php checked($asl_option_details[1]); ?>>
                    <label class="form-check-label" for="asl-store-option-<?php echo esc_attr($asl_option); ?>"><?php echo esc_html($asl_option_details[0]); ?></label>
                  </div>
                  <span class="dashicons dashicons-info-outline asl-store-option-help" title="<?php echo esc_attr($asl_store_option_help[$asl_option]); ?>" aria-hidden="true"></span>
                  <span class="visually-hidden" id="asl-store-help-<?php echo esc_attr($asl_option); ?>"><?php echo esc_html($asl_store_option_help[$asl_option]); ?></span>
                </div>
        <?php endforeach; ?>
              </fieldset>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <section class="card h-100 asl-store-panel-card asl-store-preview" aria-labelledby="asl-store-preview-label">
            <div class="card-body p-3">
              <div class="d-flex gap-3 mb-3 asl-store-section-heading"><span class="asl-store-section-icon dashicons dashicons-visibility" aria-hidden="true"></span><div><h6 id="asl-store-preview-label"><?php esc_html_e('Preview', 'asl_locator'); ?></h6><p><?php esc_html_e('Example layout with your selected options.', 'asl_locator'); ?></p></div></div>
              <div class="asl-store-sample">
                <div class="asl-store-sample-browser" aria-hidden="true"><i></i><i></i><i></i></div>
                <div class="p-3">
                  <div class="asl-store-sample-art" aria-hidden="true"><svg viewBox="0 0 320 130" fill="none"><path fill="#e7f1ff" d="M0 0h320v130H0z"/><circle cx="270" cy="27" r="17" fill="#fff"/><path fill="#c9decf" d="m0 105 65-53 64 53 70-35 121 60H0z"/><rect x="69" y="35" width="182" height="90" rx="3" fill="#fff"/><path fill="#2069e8" d="M62 48h196l-13-22H75z"/><path fill="#dbeafe" d="M82 62h59v49H82zm97 0h59v49h-59z"/><path fill="#174a85" d="M150 64h20v61h-20z"/><path stroke="#a3c4ed" stroke-width="3" d="M111 62v49m97-49v49M82 86h59m38 0h59"/><path stroke="#b1c5b5" stroke-width="5" d="M25 125V96m270 29V96"/></svg></div>
                  <h6 class="mt-3 mb-2"><?php esc_html_e('Downtown Store', 'asl_locator'); ?></h6>
                  <p class="asl-store-sample-line"><span class="dashicons dashicons-location" aria-hidden="true"></span><span><?php esc_html_e('123 Main Street, Dubai', 'asl_locator'); ?><span data-store-preview="country"><?php esc_html_e(', UAE', 'asl_locator'); ?></span></span></p>
                  <p class="asl-store-sample-line" data-store-preview="hours"><span class="dashicons dashicons-clock" aria-hidden="true"></span><span><b class="asl-store-open"><?php esc_html_e('Open now', 'asl_locator'); ?></b> · 9:00 AM – 8:00 PM</span></p>
                  <p class="asl-store-sample-muted" data-store-preview="closed"><?php esc_html_e('Sunday: Closed', 'asl_locator'); ?></p>
                  <p class="asl-store-sample-line"><span class="dashicons dashicons-phone" aria-hidden="true"></span><span>+971 4 123 4567</span></p>
                  <p class="asl-store-sample-line"><span class="dashicons dashicons-admin-site-alt3" aria-hidden="true"></span><span>www.example.com</span></p>
                  <p class="asl-store-sample-muted" data-store-preview="description"><?php esc_html_e('Visit our friendly team for local advice and in-store services.', 'asl_locator'); ?></p>
                  <div class="d-flex flex-wrap gap-2 my-3" data-store-preview="categories"><span class="badge rounded-pill text-primary bg-primary-subtle"><?php esc_html_e('Electronics', 'asl_locator'); ?></span><span class="badge rounded-pill text-success bg-success-subtle"><?php esc_html_e('Accessories', 'asl_locator'); ?></span></div>
                  <div class="asl-store-sample-map" data-store-preview="map" aria-label="<?php esc_attr_e('Example store map', 'asl_locator'); ?>"><span class="dashicons dashicons-location-alt" aria-hidden="true"></span></div>
                  <p class="asl-store-sample-muted mt-2 mb-0" data-store-preview="coordinates"><?php esc_html_e('Directions use store coordinates.', 'asl_locator'); ?></p>
                </div>
              </div>
              <small class="d-block mt-2 text-muted"><?php esc_html_e('Illustrative preview. Your theme and store details determine the final appearance.', 'asl_locator'); ?></small>
            </div>
          </section>
        </div>
      </div>
      <section class="card asl-store-panel-card mb-3" aria-labelledby="asl-store-shortcode-label"><div class="card-body p-3">
        <div class="d-flex gap-3 mb-3 asl-store-section-heading"><span class="asl-store-section-icon dashicons dashicons-editor-code" aria-hidden="true"></span><div><h6 id="asl-store-shortcode-label"><?php esc_html_e('Store shortcode', 'asl_locator'); ?></h6><p><?php esc_html_e('The page ID, URL slug and permalink rules will be configured automatically.', 'asl_locator'); ?></p></div></div>
        <div class="d-flex align-items-center gap-3 asl-store-shortcode-row"><pre class="flex-grow-1 mb-0"><code id="asl-store-shortcode-preview">[ASL_STORE]</code></pre><button type="button" id="asl-copy-store-shortcode" class="btn btn-outline-primary d-inline-flex align-items-center gap-2"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span><span><?php esc_html_e('Copy', 'asl_locator'); ?></span></button></div>
        <span class="visually-hidden" id="asl-store-copy-status" role="status" aria-live="polite"></span>
      </div></section>
      <div class="alert alert-primary d-flex gap-3 mb-0 asl-store-next" role="note"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span><div><h6><?php esc_html_e('What happens next?', 'asl_locator'); ?></h6><p class="mb-0"><?php echo $asl_store_page_exists ? esc_html__('Your page title and store shortcode options will be updated. The page URL, other content and publishing status will be preserved.', 'asl_locator') : esc_html__('A draft WordPress page will be created with the store shortcode. Review and publish it to make store details available to your customers.', 'asl_locator'); ?></p></div></div>
    </div>
    <div class="asl-create-locator-footer"><button type="button" class="btn btn-outline-secondary asl-create-cancel" data-bs-dismiss="sl_offcanvas"><?php esc_html_e('Cancel', 'asl_locator'); ?></button><button type="button" class="btn btn-primary asl-create-submit" id="asl-create-store-page-submit" <?php disabled(!empty($asl_store_update_error)); ?>><?php echo $asl_store_page_exists ? esc_html__('Update Page', 'asl_locator') : esc_html__('Create Page', 'asl_locator'); ?> →</button></div>
  </aside>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var copyButtons = document.querySelectorAll('.asl-copy-shortcode');
  var levelSwitch = document.querySelector('#asl-level-switch');
  var createButton = document.querySelector('#asl-create-page-submit');
  var manualCompleteButton = document.querySelector('#asl-manual-complete');
  var titleField = document.querySelector('#asl-locator-page-title');
  var previewTitle = document.querySelector('.asl-create-browser section > span');
  var feedback = document.querySelector('#asl-create-page-feedback');
  var mapsKeyField = document.querySelector('#asl-dashboard-maps-key');
  var mapsVerifyButton = document.querySelector('#asl-maps-verify');
  var mapsFeedback = document.querySelector('#asl-maps-feedback');
  var mapsKeyToggle = document.querySelector('#asl-toggle-maps-key');
  var mapProviderFields = document.querySelectorAll('input[name="asl-dashboard-map-provider"]');
  var mapProviderPanels = document.querySelectorAll('[data-map-provider-panel]');
  var ajaxUrl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
  var nonce = '<?php echo esc_js(wp_create_nonce('asl-nounce')); ?>';

  var ratingPanel = document.querySelector('#asl-rating');
  if (ratingPanel) {
    var ratingStorageKey = 'asl_dashboard_rating_prompt';
    var ratingState = null;

    try {
      ratingState = JSON.parse(window.localStorage.getItem(ratingStorageKey));
    } catch (error) {
      ratingState = null;
    }

    if (ratingState && ('reviewed' === ratingState.action || ('later' === ratingState.action && ratingState.until > Date.now()))) {
      ratingPanel.hidden = true;
    }

    ratingPanel.querySelectorAll('[data-rating-action]').forEach(function (button) {
      button.addEventListener('click', function () {
        var action = button.getAttribute('data-rating-action');
        var state = {action: action};

        if ('later' === action) {
          state.until = Date.now() + (7 * 24 * 60 * 60 * 1000);
        }

        try {
          window.localStorage.setItem(ratingStorageKey, JSON.stringify(state));
        } catch (error) {
          // The prompt can still be dismissed for this page view when storage is unavailable.
        }

        ratingPanel.hidden = true;
      });
    });
  }

  function aslPost(data) {
    data.action = 'asl_ajax_handler';
    data['asl-nounce'] = nonce;
    return fetch(ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
      body: new URLSearchParams(data).toString()
    }).then(function (response) {
      if (!response.ok) throw new Error('The request could not be completed.');
      return response.json();
    });
  }

  var storePageButton = document.querySelector('#asl-create-store-page-submit');
  var storePageOptions = document.querySelectorAll('[data-store-page-option]');
  function getStorePageOptions() {
    var options = {};
    storePageOptions.forEach(function (input) {
      var key = input.getAttribute('data-store-page-option');
      // Directions use attribute presence, so omit it when disabled.
      if (key === 'coords_direction' && !input.checked) return;
      options[key] = (key === 'hide_hours' ? !input.checked : input.checked) ? '1' : '0';
    });
    return options;
  }
  function updateStoreShortcode() {
    var options = getStorePageOptions();
    document.querySelector('#asl-store-shortcode-preview').textContent = '[ASL_STORE' + Object.keys(options).map(function (key) {
      return ' ' + key + '="' + options[key] + '"';
    }).join('') + ']';
    var visibility = {
      map: options.map === '1', hours: options.hide_hours === '0',
      closed: options.hide_hours === '0' && options.closed_label === '1',
      description: options.additional_info === '1', categories: options.show_categories === '1',
      country: options.store_page_show_country === '1', coordinates: options.coords_direction === '1'
    };
    document.querySelectorAll('[data-store-preview]').forEach(function (element) {
      element.hidden = !visibility[element.getAttribute('data-store-preview')];
    });
  }
  storePageOptions.forEach(function (input) { input.addEventListener('change', updateStoreShortcode); });
  updateStoreShortcode();
  document.querySelector('#asl-copy-store-shortcode').addEventListener('click', function () {
    var button = this;
    var label = button.querySelector('span:last-child');
    button.disabled = true;
    copyShortcode(document.querySelector('#asl-store-shortcode-preview').textContent).then(function () {
      label.textContent = '<?php echo esc_js(__('Copied', 'asl_locator')); ?>';
      document.querySelector('#asl-store-copy-status').textContent = '<?php echo esc_js(__('Shortcode copied.', 'asl_locator')); ?>';
    }).catch(function () {
      document.querySelector('#asl-store-copy-status').textContent = '<?php echo esc_js(__('Unable to copy. Select and copy the shortcode manually.', 'asl_locator')); ?>';
    }).finally(function () {
      button.disabled = false;
      setTimeout(function () { label.textContent = '<?php echo esc_js(__('Copy', 'asl_locator')); ?>'; }, 1800);
    });
  });
  storePageButton.addEventListener('click', function () {
    var field = document.querySelector('#asl-store-page-title');
    var status = document.querySelector('#asl-store-page-feedback');
    status.className = 'asl-create-feedback';
    if (!field.value.trim()) {
      status.classList.add('is-error');
      status.textContent = '<?php echo esc_js(__('Please enter a page title.', 'asl_locator')); ?>';
      field.focus();
      return;
    }
    storePageButton.disabled = true;
    storePageButton.textContent = '<?php echo esc_js($asl_store_page_exists ? __('Updating…', 'asl_locator') : __('Creating…', 'asl_locator')); ?>';
    var storePageData = {'sl-action': 'create_store_page', page_title: field.value.trim(), page_version: '<?php echo esc_js($asl_store_page ? hash('sha256', $asl_store_page->post_title . "\n" . $asl_store_page->post_content) : ''); ?>'};
    var selectedOptions = getStorePageOptions();
    Object.keys(selectedOptions).forEach(function (key) { storePageData['page_options[' + key + ']'] = selectedOptions[key]; });
    aslPost(storePageData).then(function (response) {
      if (!response.success) throw new Error(response.error);
      window.location.href = response.edit_url;
    }).catch(function (error) {
      status.classList.add('is-error');
      status.textContent = error.message || '<?php echo esc_js(__('Unable to create the page.', 'asl_locator')); ?>';
      storePageButton.disabled = false;
      storePageButton.textContent = '<?php echo esc_js($asl_store_page_exists ? __('Update Page', 'asl_locator') : __('Create Page', 'asl_locator')); ?>';
    });
  });

  function validateMapsJavaScriptKey(apiKey) {
    return new Promise(function (resolve, reject) {
      var token = 'asl-' + Date.now() + '-' + Math.random().toString(36).slice(2);
      var iframe = document.createElement('iframe');
      var finished = false;
      var timeout;

      function cleanup() {
        clearTimeout(timeout);
        window.removeEventListener('message', onMessage);
        if (iframe.parentNode) iframe.parentNode.removeChild(iframe);
      }

      function fail(message) {
        if (finished) return;
        finished = true;
        cleanup();
        reject(new Error(message));
      }

      function onMessage(event) {
        var data = event.data || {};
        if (event.source !== iframe.contentWindow || data.source !== 'asl-maps-validation' || data.token !== token) {
          return;
        }
        if ('error' === data.status) {
          fail(data.message || 'Google rejected this API configuration.');
          return;
        }
        if ('success' === data.status && !finished) {
          finished = true;
          cleanup();
          resolve();
        }
      }

      window.addEventListener('message', onMessage);
      iframe.className = 'asl-maps-test-frame';
      iframe.setAttribute('aria-hidden', 'true');
      iframe.setAttribute('tabindex', '-1');
      document.body.appendChild(iframe);

      var frameScript = [
        '(function(){',
        'var token=' + JSON.stringify(token) + ';',
        'var sent=false;',
        'function send(status,message){if(sent)return;sent=true;parent.postMessage({source:"asl-maps-validation",token:token,status:status,message:message||""},"*");}',
        'window.gm_authFailure=function(){send("error","Google rejected this key. It may be expired, restricted for a different referrer, or attached to a project without billing.");};',
        'window.addEventListener("error",function(event){var message=String(event.message||"");if(/Google Maps|MapError|RequestDenied/i.test(message)){send("error",message);}});',
        'window.aslValidationReady=function(){',
          'try{',
            'if(!window.google||!google.maps){send("error","Maps JavaScript API did not initialize.");return;}',
            'if(!google.maps.places||!google.maps.places.AutocompleteService){send("error","The Places library is not enabled or unavailable.");return;}',
            'if(!google.maps.drawing||!google.maps.drawing.DrawingManager){send("error","The Drawing library is not enabled or unavailable.");return;}',
            'new google.maps.Map(document.getElementById("map"),{center:{lat:40.7128,lng:-74.0060},zoom:8,disableDefaultUI:true});',
            'var service=new google.maps.places.AutocompleteService();',
            'service.getPlacePredictions({input:"New York"},function(results,status){',
              'if(status===google.maps.places.PlacesServiceStatus.REQUEST_DENIED){send("error","Google denied the Places request. Check API activation, billing, and referrer restrictions.");return;}',
              'setTimeout(function(){send("success");},8000);',
            '});',
          '}catch(error){send("error",error&&error.message?error.message:"Google Maps could not initialize.");}',
        '};',
        'var script=document.createElement("script");',
        'script.async=true;script.defer=true;',
        'script.onerror=function(){send("error","The Google Maps JavaScript API request could not be loaded.");};',
        'script.src="https://maps.googleapis.com/maps/api/js?libraries=places,drawing&key=' + encodeURIComponent(apiKey) + '&callback=aslValidationReady&v=weekly";',
        'document.head.appendChild(script);',
        '})();'
      ].join('');
      var frameDocument = iframe.contentDocument;
      frameDocument.open();
      frameDocument.write('<!doctype html><html><head><meta charset="utf-8"></head><body><div id="map" style="width:80px;height:80px"></div><script>' + frameScript + '<\/script></body></html>');
      frameDocument.close();

      timeout = setTimeout(function () {
        fail('Google Maps validation timed out. Check API activation, billing, libraries, and referrer restrictions.');
      }, 20000);
    });
  }

  function copyShortcode(value) {
    value = typeof value === 'string' ? value : '[ASL_STORELOCATOR]';
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(value);
    }
    var textarea = document.createElement('textarea');
    textarea.value = value;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    return Promise.resolve();
  }

  function selectedMapProvider() {
    var selected = document.querySelector('input[name="asl-dashboard-map-provider"]:checked');
    return selected ? selected.value : 'google';
  }

  function updateMapProviderSetup() {
    var provider = selectedMapProvider();
    mapProviderFields.forEach(function (field) {
      field.closest('.asl-map-provider-choice').classList.toggle('is-selected', field.checked);
    });
    mapProviderPanels.forEach(function (panel) {
      panel.hidden = panel.getAttribute('data-map-provider-panel') !== provider;
    });
    if (mapsVerifyButton) {
      mapsVerifyButton.textContent = provider === 'maplibre'
        ? '<?php echo esc_js(__('Use Free Maps', 'asl_locator')); ?>'
        : '<?php echo esc_js(__('Save and Verify', 'asl_locator')); ?>';
    }
  }

  copyButtons.forEach(function (copyButton) {
    copyButton.addEventListener('click', function () {
      var originalContent = copyButton.innerHTML;
      var originalLabel = copyButton.getAttribute('aria-label');
      copyShortcode().then(function () {
        copyButton.classList.add('copied');
        copyButton.textContent = '<?php echo esc_js(__('Copied!', 'asl_locator')); ?>';
        copyButton.setAttribute('aria-label', '<?php echo esc_js(__('Shortcode copied', 'asl_locator')); ?>');
        setTimeout(function () {
          copyButton.classList.remove('copied');
          copyButton.innerHTML = originalContent;
          copyButton.setAttribute('aria-label', originalLabel);
        }, 1400);
      });
    });
  });

  if (titleField && previewTitle) {
    titleField.addEventListener('input', function () {
      previewTitle.textContent = titleField.value.trim() || '<?php echo esc_js(__('Store Locator', 'asl_locator')); ?>';
    });
  }

  if (manualCompleteButton) {
    manualCompleteButton.addEventListener('click', function () {
      manualCompleteButton.disabled = true;
      manualCompleteButton.textContent = '<?php echo esc_js(__('Saving…', 'asl_locator')); ?>';
      aslPost({'sl-action': 'complete_locator_manually'}).then(function (response) {
        if (!response.success) throw new Error(response.error || '<?php echo esc_js(__('Unable to save the setup status.', 'asl_locator')); ?>');
        manualCompleteButton.textContent = '<?php echo esc_js(__('Added ✓', 'asl_locator')); ?>';
        setTimeout(function () { window.location.reload(); }, 700);
      }).catch(function (error) {
        feedback.className = 'asl-create-feedback is-error';
        feedback.textContent = error.message;
        manualCompleteButton.disabled = false;
        manualCompleteButton.textContent = '<?php echo esc_js(__("I've Added It", 'asl_locator')); ?>';
      });
    });
  }

  if (levelSwitch) {
    levelSwitch.addEventListener('change', function () {
      levelSwitch.disabled = true;
      var data = new URLSearchParams({
        action: 'asl_ajax_handler',
        'sl-action': 'expertise_level',
        // The saved option uses 1 for Easy mode and 0 for Advanced mode.
        status: levelSwitch.checked ? '0' : '1',
        'asl-nounce': nonce
      });
      fetch(ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        body: data.toString()
      }).then(function (response) {
        if (!response.ok) throw new Error();
        return response.json();
      }).then(function () {
        window.location.reload();
      }).catch(function () {
        levelSwitch.checked = !levelSwitch.checked;
        levelSwitch.disabled = false;
      });
    });
  }

  if (mapsKeyToggle && mapsKeyField) {
    mapsKeyToggle.addEventListener('click', function () {
      var show = mapsKeyField.type === 'password';
      mapsKeyField.type = show ? 'text' : 'password';
      mapsKeyToggle.textContent = show ? 'Hide' : 'Show';
      mapsKeyToggle.setAttribute('aria-label', (show ? 'Hide' : 'Show') + ' API key');
    });
  }

  mapProviderFields.forEach(function (field) {
    field.addEventListener('change', updateMapProviderSetup);
  });
  updateMapProviderSetup();

  if (mapsVerifyButton && mapsKeyField && mapsFeedback) {
    mapsVerifyButton.addEventListener('click', function () {
      var provider = selectedMapProvider();
      var apiKey = mapsKeyField.value.trim();
      mapsFeedback.className = 'asl-maps-feedback';
      mapsFeedback.textContent = '';

      if (provider === 'google' && !apiKey) {
        mapsFeedback.classList.add('is-error');
        mapsFeedback.textContent = '<?php echo esc_js(__('Please enter a Google Maps API key.', 'asl_locator')); ?>';
        mapsKeyField.focus();
        return;
      }

      mapsVerifyButton.disabled = true;
      mapsVerifyButton.textContent = '<?php echo esc_js(__('Saving…', 'asl_locator')); ?>';
      aslPost({
        'sl-action': 'save_setting',
        dashboard_map_provider_setup: '1',
        map_provider: provider,
        'data[api_key]': apiKey
      }).then(function (response) {
        if (!response.success) throw new Error(response.error || response.msg || '<?php echo esc_js(__('Unable to save the map provider.', 'asl_locator')); ?>');
        if (provider === 'maplibre') return null;
        mapsVerifyButton.textContent = '<?php echo esc_js(__('Verifying…', 'asl_locator')); ?>';
        mapsFeedback.classList.add('is-loading');
        mapsFeedback.textContent = '<?php echo esc_js(__('Testing the Maps JavaScript API configuration…', 'asl_locator')); ?>';
        return validateMapsJavaScriptKey(apiKey);
      }).then(function () {
        if (provider === 'maplibre') return null;
        return aslPost({
          'sl-action': 'save_onboarding_step',
          step: 'connect_google_maps',
          completed: '1'
        });
      }).then(function (response) {
        if (provider === 'google' && (!response || !response.success)) throw new Error((response && response.error) || '<?php echo esc_js(__('Unable to save the connected state.', 'asl_locator')); ?>');
        mapsFeedback.className = 'asl-maps-feedback is-success';
        mapsFeedback.textContent = provider === 'maplibre'
          ? '<?php echo esc_js(__('Free Maps enabled successfully.', 'asl_locator')); ?>'
          : '<?php echo esc_js(__('Google Maps connected successfully.', 'asl_locator')); ?>';
        mapsVerifyButton.textContent = '<?php echo esc_js(__('Ready ✓', 'asl_locator')); ?>';
        setTimeout(function () { window.location.reload(); }, 900);
      }).catch(function (error) {
        mapsFeedback.className = 'asl-maps-feedback is-error';
        mapsFeedback.textContent = error.message || '<?php echo esc_js(__('The map service could not be configured.', 'asl_locator')); ?>';
        mapsVerifyButton.disabled = false;
        updateMapProviderSetup();
      });
    });
  }

  if (createButton && titleField && feedback) {
    createButton.addEventListener('click', function () {
      var pageTitle = titleField.value.trim();
      feedback.className = 'asl-create-feedback';
      feedback.textContent = '';

      if (!pageTitle) {
        feedback.classList.add('is-error');
        feedback.textContent = 'Please enter a page title.';
        titleField.focus();
        return;
      }

      createButton.disabled = true;
      createButton.innerHTML = 'Creating…';
      var data = new URLSearchParams({
        action: 'asl_ajax_handler',
        'sl-action': 'create_locator_page',
        page_title: pageTitle,
        'asl-nounce': nonce
      });

      fetch(ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        body: data.toString()
      }).then(function (response) {
        if (!response.ok) throw new Error('Unable to create the page.');
        return response.json();
      }).then(function (response) {
        if (!response.success) throw new Error(response.error || response.msg || 'Unable to create the page.');
        feedback.classList.add('is-success');
        feedback.innerHTML = '<strong>' + response.msg + '</strong><a href="' + response.view_url + '" target="_blank">View page →</a>';
        createButton.innerHTML = 'Page Created ✓';
        setTimeout(function () { window.location.reload(); }, 900);
      }).catch(function (error) {
        feedback.classList.add('is-error');
        feedback.textContent = error.message || 'Unable to create the page.';
        createButton.disabled = false;
        createButton.innerHTML = 'Create Page <span>→</span>';
      });
    });
  }
});
</script>
