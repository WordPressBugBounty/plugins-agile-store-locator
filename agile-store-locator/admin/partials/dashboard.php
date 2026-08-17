<?php
/** Free plugin dashboard. @var array $all_configs @var array $all_stats */
$asl_has_api       = !empty($all_configs['api_key']);
$asl_is_easy       = \AgileStoreLocator\Helper::expertise_level();
$asl_onboarding    = \AgileStoreLocator\Admin\Dashboard::get_onboarding_state((int) $all_stats['stores']);
$asl_locator_setup = \AgileStoreLocator\Admin\Dashboard::get_locator_setup_state();
if ($asl_onboarding['add_to_website'] !== $asl_locator_setup['completed']) {
    \AgileStoreLocator\Admin\Dashboard::set_onboarding_step('add_to_website', $asl_locator_setup['completed']);
    $asl_onboarding['add_to_website'] = $asl_locator_setup['completed'];
}
$asl_maps_status   = $asl_onboarding['connect_google_maps'] ? 'connected' : ($asl_has_api ? 'needs-attention' : 'not-configured');
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
$asl_page_url      = admin_url('post-new.php?post_type=page');
$asl_locator_view_url = $asl_locator_setup['valid_page'] ? get_permalink($asl_locator_setup['page_id']) : '';
$asl_upgrade_url   = defined('ASL_UPGRADE_URL') ? ASL_UPGRADE_URL : 'https://agilestorelocator.com/pricing/';
$asl_docs_url      = 'https://agilestorelocator.com/wiki/';
$asl_support_url   = 'https://wordpress.org/support/plugin/agile-store-locator/';
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
    <symbol id="asl-i-analytics" viewBox="0 0 24 24"><path d="M4 20V11h3v9M10 20V6h3v14M16 20V3h3v17M2 20h20"/></symbol>
    <symbol id="asl-i-video" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m10 9 5 3-5 3V9Z"/></symbol>
  </svg>
  <header class="asl-dash-head"><div><h1><?php esc_html_e('Welcome to Agile Store Locator', 'asl_locator'); ?> <span>👋</span></h1><p><?php esc_html_e('Manage your stores, customize your locator and help customers find you easily.', 'asl_locator'); ?></p></div><div class="asl-head-actions"><nav aria-label="<?php esc_attr_e('Dashboard links', 'asl_locator'); ?>"><a href="<?php echo esc_url($asl_docs_url); ?>" target="_blank"><svg><use href="#asl-i-book"/></svg><?php esc_html_e('Documentation', 'asl_locator'); ?></a><a href="<?php echo esc_url($asl_support_url); ?>" target="_blank"><svg><use href="#asl-i-help"/></svg><?php esc_html_e('Support', 'asl_locator'); ?></a></nav><div class="asl-level-switch"><span><?php esc_html_e('Easy', 'asl_locator'); ?></span><label><input id="asl-level-switch" type="checkbox" <?php checked(!$asl_is_easy); ?>><i></i><b class="screen-reader-text"><?php esc_html_e('Toggle Easy or Advanced level', 'asl_locator'); ?></b></label><span><?php esc_html_e('Advanced', 'asl_locator'); ?></span></div></div></header>

  <section class="asl-card asl-setup"><div class="asl-section-title"><h2><?php esc_html_e('Your Store Locator Setup', 'asl_locator'); ?></h2><span><?php echo esc_html(sprintf(__('%1$d of 4 completed', 'asl_locator'), $asl_completed)); ?></span></div><div class="asl-setup-grid">
    <article class="asl-step asl-maps-step <?php echo $asl_onboarding['connect_google_maps'] ? 'is-done' : ($asl_current_step === 'connect_google_maps' ? 'is-current' : ''); ?>" data-maps-status="<?php echo esc_attr($asl_maps_status); ?>"><span class="asl-step-number"><?php echo $asl_onboarding['connect_google_maps'] ? '✓' : '1'; ?></span><div class="asl-step-icon mint"><svg><use href="#asl-i-map"/></svg></div><h3><?php echo $asl_onboarding['connect_google_maps'] ? esc_html__('Google Maps Connected', 'asl_locator') : esc_html__('1. Connect Google Maps', 'asl_locator'); ?></h3><p><?php esc_html_e('Add and verify your Google Maps API key.', 'asl_locator'); ?></p><span class="asl-step-status <?php echo esc_attr($asl_maps_status); ?>"><?php echo 'connected' === $asl_maps_status ? '✓ ' . esc_html__('Connected', 'asl_locator') : ('needs-attention' === $asl_maps_status ? '◉ ' . esc_html__('Needs attention', 'asl_locator') : '○ ' . esc_html__('Not configured', 'asl_locator')); ?></span><a class="asl-btn" href="#asl-google-maps-setup" data-bs-toggle="sl_offcanvas" role="button" aria-controls="asl-google-maps-setup"><?php echo $asl_onboarding['connect_google_maps'] ? esc_html__('Manage API Key', 'asl_locator') : esc_html__('Connect Google Maps', 'asl_locator'); ?></a></article>
    <article class="asl-step <?php echo $asl_onboarding['add_stores'] ? 'is-done' : ($asl_current_step === 'add_stores' ? 'is-current' : ''); ?>"><span class="asl-step-number"><?php echo $asl_onboarding['add_stores'] ? '✓' : '2'; ?></span><div class="asl-step-icon mint"><svg><use href="#asl-i-store"/></svg></div><h3><?php esc_html_e('2. Add Your First Store', 'asl_locator'); ?></h3><p><?php esc_html_e('Add at least one store to show on the map.', 'asl_locator'); ?></p><a class="asl-btn" href="<?php echo esc_url($asl_add_url); ?>"><?php esc_html_e('Add New Store', 'asl_locator'); ?></a></article>
    <article class="asl-step <?php echo $asl_onboarding['add_to_website'] ? 'is-done' : ($asl_locator_setup['missing_page'] ? 'is-attention' : ($asl_current_step === 'add_to_website' ? 'is-current' : '')); ?>"><span class="asl-step-number"><?php echo $asl_onboarding['add_to_website'] ? '✓' : ($asl_locator_setup['missing_page'] ? '⚠' : '3'); ?></span><div class="asl-step-icon gray"><svg><use href="#asl-i-code"/></svg></div><?php if ($asl_locator_setup['missing_page'] && !$asl_locator_setup['manual']) : ?><h3><?php esc_html_e('Locator Page Not Found', 'asl_locator'); ?></h3><p><?php esc_html_e('The page connected to your store locator is no longer available.', 'asl_locator'); ?></p><a class="asl-btn" href="#asl-create-locator" data-bs-toggle="sl_offcanvas" role="button" aria-controls="asl-create-locator"><?php esc_html_e('Add to Website', 'asl_locator'); ?></a><?php elseif ($asl_locator_setup['valid_page']) : ?><h3><?php esc_html_e('3. Added to Your Website', 'asl_locator'); ?></h3><p><?php esc_html_e('Your Store Locator page is ready.', 'asl_locator'); ?></p><a class="asl-btn" href="<?php echo esc_url($asl_locator_view_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('View Locator', 'asl_locator'); ?></a><?php elseif ($asl_locator_setup['manual']) : ?><h3><?php esc_html_e('3. Added to Your Website', 'asl_locator'); ?></h3><p><?php esc_html_e('Your locator has been added to your website.', 'asl_locator'); ?></p><a class="asl-btn" href="#asl-create-locator" data-bs-toggle="sl_offcanvas" role="button" aria-controls="asl-create-locator"><?php esc_html_e('Manage Setup', 'asl_locator'); ?></a><?php else : ?><h3><?php esc_html_e('3. Add to Your Website', 'asl_locator'); ?></h3><p><?php esc_html_e('Create a page for your store locator or add it manually.', 'asl_locator'); ?></p><a class="asl-btn" href="#asl-create-locator" data-bs-toggle="sl_offcanvas" role="button" aria-controls="asl-create-locator"><?php esc_html_e('Add to Website', 'asl_locator'); ?></a><?php endif; ?></article>
    <article class="asl-step <?php echo $asl_onboarding['customize_locator'] ? 'is-done' : ($asl_current_step === 'customize_locator' ? 'is-current' : ''); ?>"><span class="asl-step-number"><?php echo $asl_onboarding['customize_locator'] ? '✓' : '4'; ?></span><div class="asl-step-icon blue"><svg><use href="#asl-i-brush"/></svg></div><h3><?php esc_html_e('4. Customize Locator', 'asl_locator'); ?></h3><p><?php esc_html_e('Personalize the locator colors to match your website.', 'asl_locator'); ?></p><a class="asl-btn" href="<?php echo esc_url($asl_customize_url); ?>"><?php esc_html_e('Customize Colors', 'asl_locator'); ?></a></article>
  </div><div class="asl-tip"><span>i</span><?php esc_html_e('Tip: Complete all steps to make your store locator live and help customers find you.', 'asl_locator'); ?></div></section>

  <section class="asl-stats"><article class="stat-blue"><div><svg><use href="#asl-i-store"/></svg></div><strong><?php echo esc_html($all_stats['stores']); ?></strong><span><?php esc_html_e('Stores', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_manage_url); ?>"><?php esc_html_e('Manage Stores', 'asl_locator'); ?> →</a></article><article class="stat-green"><div><svg><use href="#asl-i-folder"/></svg></div><strong><?php echo esc_html($all_stats['categories']); ?></strong><span><?php esc_html_e('Categories', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_category_url); ?>"><?php esc_html_e('Manage Categories', 'asl_locator'); ?> →</a></article><article class="stat-purple"><div><svg><use href="#asl-i-pin"/></svg></div><strong><?php echo esc_html($all_stats['markers']); ?></strong><span><?php esc_html_e('Markers', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_markers_url); ?>"><?php esc_html_e('Manage Markers', 'asl_locator'); ?> →</a></article><article class="stat-orange"><div><svg><use href="#asl-i-search"/></svg></div><strong><?php echo esc_html($all_stats['searches']); ?>+</strong><span><?php esc_html_e('Searches', 'asl_locator'); ?><small><?php esc_html_e('This Month', 'asl_locator'); ?></small></span><a href="#asl-analytics"><?php esc_html_e('View Analytics', 'asl_locator'); ?> →</a></article></section>

  <section class="asl-card asl-quick"><h2><?php esc_html_e('Quick Actions', 'asl_locator'); ?></h2><div class="asl-quick-grid"><a href="<?php echo esc_url($asl_add_url); ?>"><i class="blue">+</i><span><b><?php esc_html_e('Add New Store', 'asl_locator'); ?></b><?php esc_html_e('Add a new store to your locator.', 'asl_locator'); ?></span></a><a href="<?php echo esc_url($asl_manage_url); ?>"><i class="teal"><svg><use href="#asl-i-list"/></svg></i><span><b><?php esc_html_e('Manage Stores', 'asl_locator'); ?></b><?php esc_html_e('View, edit or delete your stores.', 'asl_locator'); ?></span></a><a href="<?php echo esc_url($asl_customize_url); ?>"><i class="purple"><svg><use href="#asl-i-brush"/></svg></i><span><b><?php esc_html_e('Customize Locator', 'asl_locator'); ?></b><?php esc_html_e('Change layout, map and design.', 'asl_locator'); ?></span></a><a href="<?php echo esc_url($asl_page_url); ?>"><i class="orange"><svg><use href="#asl-i-external"/></svg></i><span><b><?php esc_html_e('View Locator', 'asl_locator'); ?></b><?php esc_html_e('Preview your store locator on site.', 'asl_locator'); ?></span></a></div></section>

  <section class="asl-card asl-analytics" id="asl-analytics"><div class="asl-section-title"><h2><?php esc_html_e('Analytics Overview', 'asl_locator'); ?></h2><a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank"><?php esc_html_e('View Full Analytics', 'asl_locator'); ?> →</a></div><div class="asl-analytics-content" aria-hidden="true"><div class="asl-chart"><b><?php esc_html_e('Searches (Last 7 Days)', 'asl_locator'); ?></b><svg viewBox="0 0 650 205" preserveAspectRatio="none"><g class="grid"><path d="M30 20H640M30 65H640M30 110H640M30 155H640M30 200H640"/></g><path class="area" d="M30 160 130 90 230 42 330 125 430 50 525 65 625 140V200H30Z"/><path class="line" d="M30 160 130 90 230 42 330 125 430 50 525 65 625 140"/><g class="dots"><circle cx="30" cy="160" r="4"/><circle cx="130" cy="90" r="4"/><circle cx="230" cy="42" r="4"/><circle cx="330" cy="125" r="4"/><circle cx="430" cy="50" r="4"/><circle cx="525" cy="65" r="4"/><circle cx="625" cy="140" r="4"/></g></svg><div class="asl-days"><span>May 8</span><span>May 9</span><span>May 10</span><span>May 11</span><span>May 12</span><span>May 13</span><span>May 14</span></div></div><div class="asl-top-locations"><b><?php esc_html_e('Top Searched Locations', 'asl_locator'); ?></b><ol><li><span>New York, NY</span><em>32</em></li><li><span>Los Angeles, CA</span><em>28</em></li><li><span>Chicago, IL</span><em>19</em></li><li><span>Houston, TX</span><em>14</em></li><li><span>Miami, FL</span><em>11</em></li></ol></div></div><div class="asl-pro-lock"><svg><use href="#asl-i-lock"/></svg><strong><?php esc_html_e('Analytics is a Pro feature', 'asl_locator'); ?></strong><span><?php esc_html_e('Upgrade to unlock detailed store performance insights.', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a></div></section>

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
      <div class="asl-upgrade-actions"><a class="primary" href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a><a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank"><?php esc_html_e('Compare Plans', 'asl_locator'); ?> →</a></div>
    </div>
  </section>

  <section class="asl-bottom-grid">
    <article class="asl-card"><h2><?php esc_html_e('Basic System Info', 'asl_locator'); ?></h2><dl><div><dt><?php esc_html_e('Plugin Version', 'asl_locator'); ?></dt><dd><?php echo esc_html(ASL_CVERSION); ?></dd></div><div><dt><?php esc_html_e('Google Maps API', 'asl_locator'); ?></dt><dd class="<?php echo esc_attr('connected' === $asl_maps_status ? 'connected' : ('needs-attention' === $asl_maps_status ? 'attention' : 'missing')); ?>"><?php echo 'connected' === $asl_maps_status ? '● ' . esc_html__('Connected', 'asl_locator') : ('needs-attention' === $asl_maps_status ? '● ' . esc_html__('Needs attention', 'asl_locator') : '● ' . esc_html__('Not configured', 'asl_locator')); ?></dd></div><div><dt><?php esc_html_e('PHP Version', 'asl_locator'); ?></dt><dd><?php echo esc_html(phpversion()); ?></dd></div><div><dt><?php esc_html_e('WordPress Version', 'asl_locator'); ?></dt><dd><?php echo esc_html(get_bloginfo('version')); ?></dd></div><div><dt><?php esc_html_e('Memory Limit', 'asl_locator'); ?></dt><dd><?php echo esc_html(ini_get('memory_limit')); ?></dd></div></dl><a class="asl-card-link" href="<?php echo esc_url(admin_url('site-health.php')); ?>"><?php esc_html_e('System Status', 'asl_locator'); ?> →</a></article>
    <article class="asl-card asl-resources"><h2><?php esc_html_e('Help & Resources', 'asl_locator'); ?></h2><ul>
      <li class="resource-docs"><span><i><svg><use href="#asl-i-book"/></svg></i><?php esc_html_e('Documentation', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_docs_url); ?>" target="_blank"><?php esc_html_e('View Docs', 'asl_locator'); ?> →</a></li>
      <li class="resource-video"><span><i><svg><use href="#asl-i-video"/></svg></i><?php esc_html_e('Video Tutorials', 'asl_locator'); ?></span><a href="https://www.youtube.com/@agilelogix" target="_blank"><?php esc_html_e('Watch Now', 'asl_locator'); ?> →</a></li>
      <li class="resource-support"><span><i><svg><use href="#asl-i-help"/></svg></i><?php esc_html_e('Support Center', 'asl_locator'); ?></span><a href="<?php echo esc_url($asl_support_url); ?>" target="_blank"><?php esc_html_e('Get Help', 'asl_locator'); ?> →</a></li>
    </ul><div class="asl-help"><span><b>i</b><?php esc_html_e("Need help? We're here for you!", 'asl_locator'); ?><small><?php esc_html_e('Contact our support team anytime.', 'asl_locator'); ?></small></span><a href="<?php echo esc_url($asl_support_url); ?>" target="_blank"><?php esc_html_e('Get Support', 'asl_locator'); ?></a></div></article>
    <article class="asl-card asl-shortcode" id="asl-shortcode"><h2><?php esc_html_e('Store Locator Shortcode', 'asl_locator'); ?></h2><p><?php esc_html_e('Add your locator anywhere using the shortcode below.', 'asl_locator'); ?></p><div><code>[ASL_STORELOCATOR]</code><button type="button" class="asl-copy-shortcode" data-copy-label="<?php esc_attr_e('Copy shortcode', 'asl_locator'); ?>" aria-label="<?php esc_attr_e('Copy shortcode', 'asl_locator'); ?>"><svg><use href="#asl-i-copy"/></svg></button></div><p><?php esc_html_e('Works with WordPress, Elementor, and other page builders.', 'asl_locator'); ?></p><a class="asl-shortcode-guide" href="<?php echo esc_url($asl_docs_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Shortcode Guide', 'asl_locator'); ?> →</a></article>
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
  <aside class="sl_offcanvas sl_offcanvas-end" tabindex="-1" id="asl-google-maps-setup" aria-labelledby="asl-google-maps-setup-label">
    <div class="sl_offcanvas-header asl-maps-setup-head">
      <div class="asl-maps-setup-title"><i><svg><use href="#asl-i-map"/></svg></i><div><h5 id="asl-google-maps-setup-label">Connect Google Maps</h5><p>Add and verify your Maps JavaScript API key.</p></div></div>
      <button type="button" class="btn-close" data-bs-dismiss="sl_offcanvas" aria-label="Close"></button>
    </div>
    <div class="sl_offcanvas-body asl-maps-setup-body">
      <div class="asl-maps-state is-<?php echo esc_attr($asl_maps_status); ?>"><i></i><div><strong><?php echo 'connected' === $asl_maps_status ? 'Google Maps is connected' : ('needs-attention' === $asl_maps_status ? 'Your API key needs attention' : 'Google Maps is not configured'); ?></strong><span><?php echo 'connected' === $asl_maps_status ? 'Your saved key passed the Maps JavaScript API check.' : ('needs-attention' === $asl_maps_status ? 'A key is saved, but it has not passed validation.' : 'Add an API key to enable maps in your store locator.'); ?></span></div></div>
      <div class="asl-maps-feedback" id="asl-maps-feedback" role="status" aria-live="polite"></div>
      <div class="asl-create-field asl-maps-key-field"><label for="asl-dashboard-maps-key">Google Maps API key</label><div class="asl-maps-key-control"><input type="password" id="asl-dashboard-maps-key" class="form-control" value="<?php echo esc_attr($all_configs['api_key']); ?>" placeholder="Paste your Google Maps API key"><button type="button" id="asl-toggle-maps-key" aria-label="Show API key">Show</button></div><small>The key must allow Maps JavaScript API requests from this website.</small></div>
      <div class="asl-maps-requirements"><h6>Before you connect</h6><ul><li><b>1</b>Enable the Maps JavaScript API in Google Cloud</li><li><b>2</b>Add this website to the key’s HTTP referrer restrictions</li><li><b>3</b>Make sure billing is enabled for the Google Cloud project</li></ul></div>
      <a class="asl-maps-guide" href="https://agilestorelocator.com/blog/enable-google-maps-api-agile-store-locator-plugin/" target="_blank"><svg><use href="#asl-i-book"/></svg>View Google Maps setup guide →</a>
      <div class="asl-maps-video"><div class="asl-maps-video-head"><h6>Video tutorial</h6><a href="https://www.youtube.com/watch?v=gJWVJsUOasg" target="_blank">Watch on YouTube →</a></div><div class="asl-maps-video-frame"><iframe src="https://www.youtube-nocookie.com/embed/gJWVJsUOasg" title="How to configure a Google Maps API key" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div></div>
      <div id="asl-maps-validation-canvas" class="asl-maps-validation-canvas" aria-hidden="true"></div>
    </div>
    <div class="asl-maps-setup-footer"><button type="button" class="btn asl-create-cancel" data-bs-dismiss="sl_offcanvas">Cancel</button><button type="button" class="btn asl-maps-verify" id="asl-maps-verify">Save &amp; Verify</button></div>
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
  var ratingPanel = document.querySelector('#asl-rating');
  var ajaxUrl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
  var nonce = '<?php echo esc_js(wp_create_nonce('asl-nounce')); ?>';

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

  function copyShortcode() {
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText('[ASL_STORELOCATOR]');
    }
    var textarea = document.createElement('textarea');
    textarea.value = '[ASL_STORELOCATOR]';
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    return Promise.resolve();
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

  if (mapsVerifyButton && mapsKeyField && mapsFeedback) {
    mapsVerifyButton.addEventListener('click', function () {
      var apiKey = mapsKeyField.value.trim();
      mapsFeedback.className = 'asl-maps-feedback';
      mapsFeedback.textContent = '';

      if (!apiKey) {
        mapsFeedback.classList.add('is-error');
        mapsFeedback.textContent = 'Please enter a Google Maps API key.';
        mapsKeyField.focus();
        return;
      }

      mapsVerifyButton.disabled = true;
      mapsVerifyButton.textContent = 'Saving…';
      aslPost({
        'sl-action': 'save_setting',
        dashboard_google_maps_setup: '1',
        'data[api_key]': apiKey
      }).then(function (response) {
        if (!response.success) throw new Error(response.error || response.msg || 'Unable to save the API key.');
        mapsVerifyButton.textContent = 'Verifying…';
        mapsFeedback.classList.add('is-loading');
        mapsFeedback.textContent = 'Testing the Maps JavaScript API configuration…';
        return validateMapsJavaScriptKey(apiKey);
      }).then(function () {
        return aslPost({
          'sl-action': 'save_onboarding_step',
          step: 'connect_google_maps',
          completed: '1'
        });
      }).then(function (response) {
        if (!response.success) throw new Error(response.error || 'Unable to save the connected state.');
        mapsFeedback.className = 'asl-maps-feedback is-success';
        mapsFeedback.textContent = 'Google Maps connected successfully.';
        mapsVerifyButton.textContent = 'Connected ✓';
        setTimeout(function () { window.location.reload(); }, 900);
      }).catch(function (error) {
        mapsFeedback.className = 'asl-maps-feedback is-error';
        mapsFeedback.textContent = error.message || 'Google Maps could not be verified.';
        mapsVerifyButton.disabled = false;
        mapsVerifyButton.textContent = 'Save & Verify';
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
