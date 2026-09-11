<?php

//	simple level
$level_mode = \AgileStoreLocator\Helper::expertise_level();
$asl_upgrade_url = defined('ASL_UPGRADE_URL') ? ASL_UPGRADE_URL : 'https://agilestorelocator.com/pricing/';
?>
<?php
if($level_mode === true){ ?>
<style type="text/css">
.sl-complx {
    display: none !important;
}
</style>
<?php } ?>
<div class="asl-p-cont asl-new-bg asl-admin-tabs-page asl-admin-header-page asl-grid-settings">
    <div class="hide">
        <svg xmlns="http://www.w3.org/2000/svg">
            <symbol id="i-trash" viewBox="0 0 32 32" fill="none" stroke="currentcolor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2">>
                <title><?php echo esc_attr__('Trash','asl_locator') ?></title>
                <path
                    d="M28 6 L6 6 8 30 24 30 26 6 4 6 M16 12 L16 24 M21 12 L20 24 M11 12 L12 24 M12 6 L13 2 19 2 20 6" />
            </symbol>
            <symbol id="i-clock" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <circle cx="16" cy="16" r="14" />
                <path d="M16 8 L16 16 20 20" />
            </symbol>
            <symbol id="i-plus" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Add','asl_locator') ?></title>
                <path d="M16 2 L16 30 M2 16 L30 16" />
            </symbol>
            <symbol id="i-chevron-top" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M30 20 L16 8 2 20" />
            </symbol>
            <symbol id="i-chevron-bottom" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M30 12 L16 24 2 12" />
            </symbol>
            <symbol id="asl-admin-icon-settings" viewBox="0 0 32 32" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <circle cx="16" cy="16" r="5" />
                <path d="M13 3h6l1 4 3 2 4-1 3 5-3 3v3l3 3-3 5-4-1-3 2-1 4h-6l-1-4-3-2-4 1-3-5 3-3v-3l-3-3 3-5 4 1 3-2z" />
            </symbol>
            <symbol id="asl-admin-icon-cache" viewBox="0 0 32 32" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <ellipse cx="16" cy="7" rx="11" ry="4" />
                <path d="M5 7v9c0 2.2 4.9 4 11 4s11-1.8 11-4V7M5 16v9c0 2.2 4.9 4 11 4s11-1.8 11-4v-9" />
            </symbol>
            <symbol id="asl-admin-icon-fields" viewBox="0 0 32 32" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <rect x="4" y="5" width="24" height="22" rx="3" />
                <path d="M10 11h12M10 16h12M10 21h7" />
            </symbol>
            <symbol id="asl-admin-icon-help" viewBox="0 0 32 32" fill="none" stroke="currentColor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <circle cx="16" cy="16" r="13" />
                <path d="M11.8 12a4.4 4.4 0 1 1 6.4 3.9c-1.4.8-2.2 1.6-2.2 3.1M16 24h.01" />
            </symbol>
        </svg>
    </div>
    <div class="container sl-user-setting-page">
        <div class="row asl-setting-cont">
            <div class="col-md-12">
                <div class="asl-tabs p-0 mb-4 mt-4 card asl-grid-shell">
                    <header class="asl-admin-page-header">
                        <span class="asl-admin-page-header__icon" aria-hidden="true">
                            <svg><use href="#asl-admin-icon-settings"></use></svg>
                        </span>
                        <div class="asl-admin-page-header__copy">
                            <h3><?php echo esc_html__('ASL Settings (Free Version - ','asl_locator').esc_html(ASL_CVERSION) ?>)</h3>
                            <p><?php echo esc_html__('Set general options and preferences.','asl_locator') ?></p>
                        </div>
                        <div class="asl-admin-page-header__actions asl-settings-header-actions">
                            <a href="#asl-feedback" role="button" aria-haspopup="dialog" class="asl-feedback-link btn btn-light bg-white text-primary border-0 d-inline-flex align-items-center"><span class="dashicons dashicons-format-chat me-1" aria-hidden="true"></span><?php esc_html_e('Send Feedback', 'asl_locator'); ?></a>
                            <a id="asl-btn-export-config" data-loading-text="Exporting..."
                                class="btn btn-light bg-white text-primary border-0 d-inline-flex align-items-center">
                                <span class="dashicons dashicons-download me-1" aria-hidden="true"></span>
                                <?php echo esc_attr__('Export Settings','asl_locator') ?>
                            </a><a
                                id="asl-btn-import-config"
                                class="btn btn-light bg-white text-primary border-0 d-inline-flex align-items-center">
                                <span class="dashicons dashicons-upload me-1" aria-hidden="true"></span>
                                <?php echo esc_attr__('Import Settings','asl_locator') ?>
                            </a>
                        </div>
                    </header>
                   
                    <div class="asl-tabs-body">
                        <?php if ($level_mode === true) : ?>
                            <div class="asl-expert-mode-notice" role="status">
                                <span class="dashicons dashicons-info-outline" aria-hidden="true"></span>
                                <div>
                                    <strong><?php esc_html_e('Easy mode is enabled', 'asl_locator'); ?></strong>
                                    <p><?php esc_html_e('Only simple options are currently visible. Enable Expert mode to view all settings.', 'asl_locator'); ?></p>
                                </div>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=agile-dashboard')); ?>"><?php esc_html_e('Enable Expert Mode', 'asl_locator'); ?> <span aria-hidden="true">→</span></a>
                            </div>
                        <?php endif; ?>
                        <ul class="nav nav-pills justify-content-center">
                            <li class="active rounded"><a data-toggle="pill"
                                    href="#sl-gen-tab"><?php echo esc_attr__('General','asl_locator') ?></a></li>
                            <li class="rounded"><a data-toggle="pill"
                                    href="#maps-tab"><?php echo esc_attr__('Maps','asl_locator') ?></a></li>
                            <li class="rounded"><a data-toggle="pill"
                                    href="#sl-ui-tab"><?php echo esc_attr__('UI Settings','asl_locator') ?></a></li>
                            <li class="rounded sl-complx"><a data-toggle="pill"
                                    href="#sl-detail"><?php echo esc_attr__('Detail Page','asl_locator') ?></a></li>
                            <li class="rounded sl-complx"><a data-toggle="pill"
                                    href="#sl-register"><?php echo esc_attr__('Notifications','asl_locator') ?></a></li>
                            <li class="rounded sl-complx"><a data-toggle="pill"
                                    href="#sl-customizer"><?php echo esc_attr__('Customizer','asl_locator') ?></a></li>
                            <li class="rounded sl-complx"><a data-toggle="pill"
                                    href="#sl-store-form"><?php echo esc_attr__('Store Form','asl_locator') ?></a></li>
                            <li class="rounded sl-complx"><a data-toggle="pill"
                                    href="#sl-labels"><?php echo esc_attr__('Labels','asl_locator') ?></a></li>
                            <li class="rounded asl-premium-tab asl-pro-features-tab"><a data-toggle="pill"
                                    href="#sl-pro"><?php echo esc_html__('Pro Features','asl_locator') ?><span class="dashicons dashicons-star-filled" aria-hidden="true"></span></a></li>
                            <?php if(!defined ( 'ASL_WC_VERSION' )):?>
                            <li class="rounded sl-complx asl-premium-tab asl-wc-tab"><a data-toggle="pill"
                                    href="#sl-wc"><?php echo esc_html__('WooCommerce','asl_locator') ?><span class="dashicons dashicons-star-filled" aria-hidden="true"></span></a></li>
                            <?php endif; ?>
                        </ul>
                        <form id="frm-usersetting">
                            <div class="tab-content">
                                <div id="sl-gen-tab" class="tab-pane in active">
                                    <div class="row mt-2">
                                        <?php ob_start(); ?>
                                        <section class="col-12 asl-map-settings-group" data-map-settings-group="provider">
                                            <header class="asl-map-settings-group__header">
                                                <span class="dashicons dashicons-admin-site-alt3" aria-hidden="true"></span>
                                                <div><h5><?php echo esc_html__('Map Provider','asl_locator') ?></h5><p><?php echo esc_html__('Choose the map engine and, for MapLibre, the service that supplies the map.','asl_locator') ?></p></div>
                                            </header>
                                            <div class="row">
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-map-vendor-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label" for="asl-map_vendor"><?php echo esc_attr__('Map Vendor','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <div class="asl-map-vendor-cards" role="group" aria-label="<?php echo esc_attr__('Map Vendor','asl_locator') ?>">
                                                        <button type="button" class="asl-map-vendor-card" data-map-vendor="google">
                                                            <span class="asl-map-vendor-preview" aria-hidden="true"><img src="<?php echo esc_url(ASL_URL_PATH . 'public/css/images/google-maps.png'); ?>" alt=""></span>
                                                            <span class="asl-map-vendor-check" aria-hidden="true">✓</span>
                                                            <strong><?php echo esc_html__('Google Maps','asl_locator') ?></strong>
                                                            <small><?php echo esc_html__('Feature-rich maps with global coverage.','asl_locator') ?></small>
                                                        </button>
                                                        <button type="button" class="asl-map-vendor-card" data-map-vendor="maplibre">
                                                            <span class="asl-map-vendor-preview" aria-hidden="true"><img src="<?php echo esc_url(ASL_URL_PATH . 'admin/images/map-vendors/maplibre.png'); ?>" alt=""></span>
                                                            <span class="asl-map-vendor-check" aria-hidden="true">✓</span>
                                                            <strong><?php echo esc_html__('MapLibre','asl_locator') ?></strong>
                                                            <small><?php echo esc_html__('Open source and self-hosted map solution.','asl_locator') ?></small>
                                                        </button>
                                                    </div>
                                                    <select class="form-control asl-map-vendor-native" name="data[map_vendor]" id="asl-map_vendor" aria-label="<?php echo esc_attr__('Map Vendor','asl_locator') ?>">
                                                        <option value="google"><?php echo esc_html__('Google Maps','asl_locator') ?></option>
                                                        <option value="maplibre"><?php echo esc_html__('MapLibre','asl_locator') ?></option>
                                                    </select>
                                                    <p class="help-p"><a href="https://agilestorelocator.com/wiki/maplibre-wordpress-store-locator-free-maps/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Map providers, tile providers and map styles guide','asl_locator') ?></a></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-tile-provider-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label" for="asl-tile_provider"><?php echo esc_attr__('Tile Provider','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select class="form-control" name="data[tile_provider]" id="asl-tile_provider">
                                                        <option value="osm"><?php echo esc_html__('OpenStreetMap (No API Key)','asl_locator') ?></option>
                                                        <option value="geoapify"><?php echo esc_html__('Geoapify','asl_locator') ?></option>
                                                        <option value="mapbox"><?php echo esc_html__('Mapbox','asl_locator') ?></option>
                                                        <option value="maptiler"><?php echo esc_html__('MapTiler','asl_locator') ?></option>
                                                        <option value="custom"><?php echo esc_html__('Custom Style URL','asl_locator') ?></option>
                                                    </select>
                                                    <p class="help-p asl-osm-tile-help"><?php echo esc_html__('For testing and light interactive use. Address search is configured separately and may still require an API key.','asl_locator') ?> <a href="https://operations.osmfoundation.org/policies/tiles/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Tile usage policy','asl_locator') ?></a></p>
                                                </div>
                                            </div>
                                        </div>
                                            </div>
                                        </section>
                                        <section class="col-12 asl-map-settings-group sl-complx" data-map-settings-group="appearance">
                                            <header class="asl-map-settings-group__header">
                                                <span class="dashicons dashicons-art" aria-hidden="true"></span>
                                                <div><h5><?php echo esc_html__('Map Appearance','asl_locator') ?></h5><p><?php echo esc_html__('Select a provider style or optionally supply an advanced MapLibre style URL.','asl_locator') ?></p></div>
                                            </header>
                                            <div class="row">
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-google-map-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-map_type"><?php echo esc_attr__('Default Map','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select id="asl-map_type" name="data[map_type]"
                                                        class="custom-select">
                                                        <option value="hybrid">
                                                            <?php echo esc_attr__('Hybrid','asl_locator') ?></option>
                                                        <option value="roadmap">
                                                            <?php echo esc_attr__('Road Map','asl_locator') ?></option>
                                                        <option value="satellite">
                                                            <?php echo esc_attr__('Satellite','asl_locator') ?></option>
                                                        <option value="terrain">
                                                            <?php echo esc_attr__('Terrain','asl_locator') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-tile-provider-style-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label" for="asl-tile_provider_style"><?php echo esc_attr__('Map Style','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select class="form-control" name="data[tile_provider_style]" id="asl-tile_provider_style">
                                                        <option value="default"><?php echo esc_html__('Default Provider Style','asl_locator') ?></option>
                                                    </select>
                                                    <p class="help-p"><?php echo esc_html__('Choose a style supplied by the selected tile provider.','asl_locator') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-maplibre-style-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label" for="asl-maplibre_style_url"><?php echo esc_attr__('Style URL Override','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" inputmode="url" class="form-control" name="data[maplibre_style_url]" id="asl-maplibre_style_url" placeholder="https://maps.example.com/styles/asl/style.json">
                                                    <p class="help-p"><?php echo esc_html__('Optional MapLibre style JSON URL. It overrides the selected preset and may contain {api_key} or {access_token}.','asl_locator') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                            </div>
                                        </section>
                                        <section class="col-12 asl-map-settings-group" data-map-settings-group="search">
                                            <header class="asl-map-settings-group__header">
                                                <span class="dashicons dashicons-search" aria-hidden="true"></span>
                                                <div><h5><?php echo esc_html__('Visitor Search','asl_locator') ?></h5><p><?php echo esc_html__('Choose what visitors can search and which address service provides results.','asl_locator') ?></p></div>
                                            </header>
                                            <div class="row">
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-search-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label" for="asl-search_mode"><?php echo esc_attr__('Search','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select class="form-control" id="asl-search_mode">
                                                        <option value="automatic"><?php echo esc_html__('Address — Automatic (Recommended)','asl_locator') ?></option>
                                                        <option value="google_new"><?php echo esc_html__('Address — Google Places','asl_locator') ?></option>
                                                        <option value="google_legacy"><?php echo esc_html__('Address — Google Places (Legacy)','asl_locator') ?></option>
                                                        <option value="nominatim"><?php echo esc_html__('Address — Nominatim (Geocoding only, no API key)','asl_locator') ?></option>
                                                        <option value="geoapify"><?php echo esc_html__('Address — Geoapify','asl_locator') ?></option>
                                                        <option value="mapbox"><?php echo esc_html__('Address — Mapbox','asl_locator') ?></option>
                                                        <option value="store_name" disabled><?php echo esc_html__('Store Name — Pro','asl_locator') ?></option>
                                                        <option value="store_location" disabled><?php echo esc_html__('Store City or State — Pro','asl_locator') ?></option>
                                                        <option value="geocode_enter"><?php echo esc_html__('Address — Search on Enter','asl_locator') ?></option>
                                                        <option value="disabled"><?php echo esc_html__('Address Search Disabled','asl_locator') ?></option>
                                                    </select>
                                                    <input type="hidden" name="data[search_provider]" id="asl-search_provider">
                                                    <input type="hidden" name="data[search_type]" id="asl-search_type">
                                                    <p class="help-p"><?php echo esc_html__('Choose what visitors search and, for addresses, which service supplies suggestions.','asl_locator') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                            </div>
                                        </section>
                                        <section class="col-12 asl-map-settings-group" data-map-settings-group="credentials">
                                            <header class="asl-map-settings-group__header">
                                                <span class="dashicons dashicons-lock" aria-hidden="true"></span>
                                                <div><h5><?php echo esc_html__('API Credentials','asl_locator') ?></h5><p><?php echo esc_html__('Enter only the credentials required by the selected map and search services.','asl_locator') ?></p></div>
                                            </header>
                                            <div class="row">
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-mapbox-key-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label" for="asl-mapbox_access_token"><?php echo esc_attr__('Mapbox Access Token','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control" name="data[mapbox_access_token]" id="asl-mapbox_access_token" placeholder="<?php echo esc_attr__('PUBLIC ACCESS TOKEN','asl_locator') ?>">
                                                    <p class="help-p"><?php echo esc_html__('Get your access token from','asl_locator') ?> <a href="https://agilestorelocator.com/wiki/mapbox-access-token-wordpress/"><?php echo esc_html__('Mapbox','asl_locator') ?></a>.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-geoapify-key-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label" for="asl-geoapify_api_key"><?php echo esc_attr__('Geoapify API Key','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control" name="data[geoapify_api_key]" id="asl-geoapify_api_key" placeholder="<?php echo esc_attr__('API KEY','asl_locator') ?>">
                                                    <p class="help-p"><?php echo esc_html__('Get your free API key from','asl_locator') ?> <a href="https://www.geoapify.com/get-started-with-maps-api/"><?php echo esc_html__('Geoapify','asl_locator') ?></a>.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-tile-provider-key-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label" for="asl-tile_provider_api_key"><?php echo esc_attr__('MapTiler API Key','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control" name="data[tile_provider_api_key]" id="asl-tile_provider_api_key" placeholder="<?php echo esc_attr__('API KEY','asl_locator') ?>">
                                                    <p class="help-p"><?php echo esc_html__('Get your API key from','asl_locator') ?> <a href="https://cloud.maptiler.com/account/keys/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('MapTiler','asl_locator') ?></a>.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-google-key-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-api_key"><?php echo esc_attr__('Google API Key','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control" name="data[api_key]"
                                                        id="asl-api_key"
                                                        placeholder="<?php echo esc_attr__('API KEY','asl_locator') ?>">
                                                    <p class="help-p"><?php echo esc_html__('Get your API key from','asl_locator') ?> <a href="https://agilestorelocator.com/blog/enable-google-maps-api-agile-store-locator-plugin/"><?php echo esc_html__('Google Cloud Console','asl_locator') ?></a>.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-google-server-key-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-server_key"><?php echo esc_attr__('Google Server API Key','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control" name="data[server_key]"
                                                        id="asl-server_key"
                                                        placeholder="<?php echo esc_attr__('Google API KEY (Geocoding)','asl_locator') ?>">
                                                    <p class="help-p"><a href="https://agilestorelocator.com/wiki/what-is-google-server-key/"><?php echo esc_attr__('What is Google Server Key?','asl_locator') ?></a>
                                                        | <a href="https://agilestorelocator.com/wiki/validate-your-server-api-keys/"><?php echo esc_attr__('Troubleshoot','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                            </div>
                                        </section>
                                        <?php $asl_map_provider_settings = ob_get_clean(); ?>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-default_lat"><?php echo esc_attr__('Default Map Center','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <div class="input-group">
                                                        <input type="number" class="form-control validate[required]"
                                                            name="data[default_lat]" id="asl-default_lat"
                                                            placeholder="<?php echo esc_attr__('Latitude','asl_locator') ?>">
                                                        <input type="number" class="form-control validate[required]"
                                                            name="data[default_lng]" id="asl-default_lng"
                                                            placeholder="<?php echo esc_attr__('Longitude','asl_locator') ?>">
                                                        <button data-bs-toggle="smodal" data-bs-target="#asl-map-modal"
                                                            id="asl-setting-search-button"
                                                            class="btn btn-dark no-shade-focus "
                                                            type="button"><?php echo esc_attr__('Change','asl_locator') ?></button>
                                                    </div>
                                                    <p class="help-p"><a target="_blank" class="text-muted"
                                                            href="https://www.google.com/maps"><?php echo esc_attr__('Get your coordinates by right click on the map','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-distance_control"><?php echo esc_attr__('Distance Control','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-distance_control-0"><input type="radio"
                                                                name="data[distance_control]" value="0"
                                                                id="asl-distance_control-0"><?php echo esc_attr__('Slider','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-distance_control-1"><input type="radio"
                                                                name="data[distance_control]" value="1"
                                                                id="asl-distance_control-1"><?php echo esc_attr__('Dropdown','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-distance_control-2"><input type="radio"
                                                                name="data[distance_control]" value="2"
                                                                id="asl-distance_control-2"><?php echo esc_attr__('Boundary Box','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p"><a class="text-muted" target="_blank"
                                                            href="https://agilestorelocator.com/wiki/set-radius-value-distance-range-slider/"><?php echo esc_attr__('Select the distance filter control','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-direction_redirect"><?php echo esc_attr__('Store Direction','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select name="data[direction_redirect]" id="asl-direction_redirect"
                                                        class="custom-select">
                                                        <option value="2">
                                                            <?php echo esc_attr__('Open in Google Maps (All Devices)','asl_locator') ?>
                                                        </option>
                                                        <option value="0">
                                                            <?php echo esc_attr__('Google Direction Legacy','asl_locator') ?>
                                                        </option>
                                                        <option value="3">
                                                            <?php echo esc_attr__('Draw Direction with Route API','asl_locator') ?>
                                                        </option>
                                                        <option value="1">
                                                            <?php echo esc_attr__('Open in Google Maps (Mobile)','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Select how you want the direction to work.','asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/load-google-maps-app-mobile-direction/"
                                                            target="_blank"><?php echo esc_attr__('Guide Help','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-dropdown_range"><?php echo esc_attr__('Distance Options','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control" name="data[dropdown_range]"
                                                        id="asl-dropdown_range" placeholder="Example: 10,20,30">
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Enter the search dropdown options values, comma separated. Add default value with * symbol.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="prompt_location"><?php echo esc_attr__('Geolocation','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select id="asl-prompt_location" name="data[prompt_location]"
                                                        class="custom-select">
                                                        <option value="0">
                                                            <?php echo esc_attr__('Disable','asl_locator') ?></option>
                                                        <option value="1">
                                                            <?php echo esc_attr__('Geo-location Modal','asl_locator') ?>
                                                        </option>
                                                        <option value="2">
                                                            <?php echo esc_attr__('Type your Location Modal','asl_locator') ?>
                                                        </option>
                                                        <option value="3">
                                                            <?php echo esc_attr__('Geolocation On Load','asl_locator') ?>
                                                        </option>
                                                        <option value="4">
                                                            <?php echo esc_attr__('GeoJS IP Service (Free API)','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                    <p class="help-p"><a target="_blank" class="text-muted"
                                                            href="https://agilestorelocator.com/wiki/prompt-geo-location-dialog/"><?php echo esc_attr__('How Geolocation works?','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="search_destin"><?php echo esc_attr__('Search Result','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select id="asl-search_destin" name="data[search_destin]"
                                                        class="custom-select">
                                                        <option value="0">
                                                            <?php echo esc_attr__('Default','asl_locator') ?></option>
                                                        <option value="1">
                                                            <?php echo esc_attr__('Show My Nearest Location From Search','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                    <p class="help-p"><span
                                                            class="red"><?php echo esc_attr__('Warning! search will pinpoint the nearest available store, and will NOT center to actual location','asl_locator') ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="sort_by"><?php echo esc_attr__('Sort List','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select id="asl-sort_by" name="data[sort_by]" class="custom-select">
                                                        <option value="">
                                                            <?php echo esc_attr__('Default (Distance)','asl_locator') ?>
                                                        </option>
                                                        <option value="id">
                                                            <?php echo esc_attr__('Store ID','asl_locator') ?></option>
                                                        <option value="title">
                                                            <?php echo esc_attr__('Title','asl_locator') ?></option>
                                                        <option value="city">
                                                            <?php echo esc_attr__('City','asl_locator') ?></option>
                                                        <option value="state">
                                                            <?php echo esc_attr__('State','asl_locator') ?></option>
                                                        <option value="logo_id">
                                                            <?php echo esc_attr__('Logo ID','asl_locator') ?></option>
                                                        <option value="cat">
                                                            <?php echo esc_attr__('Categories','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                    <p class="help-p"><a target="_blank" class="text-muted"
                                                            href="https://agilestorelocator.com/wiki/sort-store-attribute/"><?php echo esc_attr__('Sort your listing based on fields, default is Distance','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="stores_limit"><?php echo esc_attr__('Stores Limit','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="number" class="form-control validate[integer]"
                                                        name="data[stores_limit]" id="asl-stores_limit">
                                                    <p class="help-p"><a target="_blank" class="text-muted"
                                                            href="https://agilestorelocator.com/wiki/show-limited-stores-sort-by-distance/"><?php echo esc_attr__('To show a limited number of stores.','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="load_all"><?php echo esc_attr__('Marker Load','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select name="data[load_all]" id="asl-load_all"
                                                        class="custom-select">
                                                        <option value="1">
                                                            <?php echo esc_attr__('Load All (Recommended)','asl_locator') ?></option>
                                                        <option value="0">
                                                            <?php echo esc_attr__('Load on Bound','asl_locator') ?>
                                                        </option>
                                                        <option value="2">
                                                            <?php echo esc_attr__('Load via Button','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Use Load on Bound in case of 1K+ markers','asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/store-data-loading-types/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Guide Doc', 'asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-distance_unit"><?php echo esc_attr__('Distance Unit','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-distance_unit-KM"><input type="radio"
                                                                name="data[distance_unit]" value="KM"
                                                                id="asl-distance_unit-KM"><?php echo esc_attr__('KM','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-distance_unit-Miles"><input type="radio"
                                                                name="data[distance_unit]" value="Miles"
                                                                id="asl-distance_unit-Miles"><?php echo esc_attr__('Miles','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Select the distance unit to use on Store Locator','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="geo_button"><?php echo esc_attr__('Search Button Type','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-geo_button-0"><input type="radio"
                                                                name="data[geo_button]" value="0"
                                                                id="asl-geo_button-0"><?php echo esc_attr__('Search Location','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-geo_button-1"><input type="radio"
                                                                name="data[geo_button]" value="1"
                                                                id="asl-geo_button-1"><?php echo esc_attr__('Geo-Location','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Select either to display the geolocation button or the search button next to address search','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="single_cat_select"><?php echo esc_attr__('Category Select','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-single_cat_select-0"><input type="radio"
                                                                name="data[single_cat_select]" value="0"
                                                                id="asl-single_cat_select-0"><?php echo esc_attr__('Multiple Category Selection','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-single_cat_select-1"><input type="radio"
                                                                name="data[single_cat_select]" value="1"
                                                                id="asl-single_cat_select-1"><?php echo esc_attr__('Single Category Selection','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('To make the category selection mode','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-country_restrict"><?php echo esc_attr__('Restrict Search','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control validate[minSize[2]]"
                                                        name="data[country_restrict]" id="asl-country_restrict"
                                                        placeholder="Example: US">
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Enter 2 alphabet country, for multiple countries comma separated','asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/restrict-search-to-specific-countries/"
                                                            target="_blank"><?php echo esc_attr__('Guide Doc','asl_locator') ?></a>
                                                        | <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2"
                                                            target="_blank" rel="nofollow"><?php echo esc_attr__('Code','asl_locator') ?></a></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-first_load"><?php echo esc_attr__('List Load','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select name="data[first_load]" id="asl-first_load"
                                                        class="custom-select">
                                                        <option value="1">
                                                            <?php echo esc_attr__('Default','asl_locator') ?></option>
                                                        <option value="2">
                                                            <?php echo esc_attr__('No List and Markers','asl_locator') ?>
                                                        </option>
                                                        <option value="3">
                                                            <?php echo esc_attr__('No List and Markers with Full Map','asl_locator') ?>
                                                        </option>
                                                        <option value="4">
                                                            <?php echo esc_attr__('No List with Markers','asl_locator') ?>
                                                        </option>
                                                        <option value="5">
                                                            <?php echo esc_attr__('No List & Map at Load','asl_locator') ?>
                                                        </option>
                                                        <option value="6">
                                                            <?php echo esc_attr__('Only Show Stores on Search or Filter Selection','asl_locator') ?>
                                                        </option>
                                                        <option value="7">
                                                            <?php echo esc_attr__('Only Show Selected Store on List','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Show no stores on the page load','asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/hide-markers-map-loaded/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Guide Doc', 'asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="filter_ddl"><?php echo esc_attr__('Dropdown Filters','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <?php
                                          $ddl_controls = AgileStoreLocator\Model\Attribute::get_controls();
                                          ?>
                                                    <select multiple id="asl-filter_ddl"
                                                        class="custom-select asl-chosen">
                                                        <?php foreach($ddl_controls as $ddl_control): ?>
                                                        <option value="<?php echo esc_attr__($ddl_control['field']) ?>">
                                                            <?php echo esc_attr__($ddl_control['label'],'asl_locator') ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Additional Dropdowns filters based on Brand and Specialities data.','asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/brand-and-special-dropdowns-additional-dropdowns/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Guide Doc', 'asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Start Branches -->
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-branches"><?php echo esc_attr__('Store Branches','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select name="data[branches]" id="asl-branches"
                                                        class="custom-select">
                                                        <option value="0">
                                                            <?php echo esc_attr__('Disable','asl_locator') ?></option>
                                                        <option value="1">
                                                            <?php echo esc_attr__('Show Branches – Parent Markers on Map','asl_locator') ?>
                                                        </option>
                                                        <option value="2">
                                                            <?php echo esc_attr__('Show Branches – All Markers on Map','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Ability to group stores in a single store','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Branches -->
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-advance_filter"><?php echo esc_attr__('Advance Filter','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-advance_filter"><input
                                                            type="checkbox" value="1" class="custom-control-input"
                                                            name="data[advance_filter]" id="asl-advance_filter"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p"><a
                                                            href="https://agilestorelocator.com/wiki/enable-disable-advance-features/"
                                                            target="_blank"><?php echo esc_attr__('Disabling it will remove all the filters','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-address_ddl"><?php echo esc_attr__('Address Dropdowns','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-address_ddl"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[address_ddl]" id="asl-address_ddl"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p"><a
                                                            href="https://agilestorelocator.com/wiki/drop-down-menus-address/"
                                                            target="_blank"><?php echo esc_attr__('Dropdown controls for Country, State and City.','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-time_switch"><?php echo esc_attr__('Time Switch','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-time_switch"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[time_switch]" id="asl-time_switch"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Control will show a switch to see opened stores at the current time','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-distance_slider"><?php echo esc_attr__('Distance Control','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-distance_slider"><input
                                                            type="checkbox" value="1" class="custom-control-input"
                                                            name="data[distance_slider]" id="asl-distance_slider"><span
                                                            class="slider round"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-analytics"><?php echo esc_attr__('Analytics','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-analytics"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[analytics]" id="asl-analytics"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p"><a
                                                            href="https://agilestorelocator.com/wiki/intro-store-locator-analytics/"
                                                            target="_blank"><?php echo esc_attr__('Enable the Analytics','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-sort_by_bound"><?php echo esc_attr__('Sort By Bound','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-sort_by_bound"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[sort_by_bound]" id="asl-sort_by_bound"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Refresh list to show nearest stores in the view.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-target_blank"><?php echo esc_attr__('Open Link New Tab','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-target_blank"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[target_blank]" id="asl-target_blank"><span
                                                            class="slider round"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-user_center"><?php echo esc_attr__('Default Location Center','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-user_center"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[user_center]" id="asl-user_center"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p"><a
                                                            href="https://agilestorelocator.com/wiki/why-the-google-map-zoom-in-on-the-page-load/"
                                                            target="_blank"><?php echo esc_attr__('Store Locator will consider Default coordinates as the center point','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-geo_marker"><?php echo esc_attr__('Geo-Location Marker','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-geo_marker"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[geo_marker]" id="asl-geo_marker"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('To remove the user own location marker','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-and_filter"><?php echo esc_attr__('AND Filter','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-and_filter"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[and_filter]" id="asl-and_filter"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('To change the category filter logic from OR to AND','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-category_marker"><?php echo esc_attr__('Category Marker','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-category_marker"><input
                                                            type="checkbox" value="1" class="custom-control-input"
                                                            name="data[category_marker]" id="asl-category_marker"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p"><a
                                                            href="https://agilestorelocator.com/wiki/enable-category-markers/"
                                                            target="_blank"><?php echo esc_attr__('Manage Markers will be replaced by the Category Icons','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-category_bound"><?php echo esc_attr__('Category Bound','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-category_bound"><input
                                                            type="checkbox" value="1" class="custom-control-input"
                                                            name="data[category_bound]" id="asl-category_bound"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Fit bound to markers when a category is selected','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-sort_random"><?php echo esc_attr__('Sort Random','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-sort_random"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[sort_random]" id="asl-sort_random"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Sort stores list randomly on the load of the Store Locator (Enabling it will disable Default Location Marker)','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-locale"><?php echo esc_attr__('Data WPML','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-locale"><input type="checkbox"
                                                            value="1" class="custom-control-input" name="data[locale]"
                                                            id="asl-locale"><span class="slider round"></span></label>
                                                    <p class="help-p text-danger">
                                                        (<?php echo esc_attr__('Enabling it will hide all your stores data if data is not assigned for the correct language','asl_locator') ?>)
                                                        | <a href="https://agilestorelocator.com/wiki/language-translation-store-locator/"
                                                            target="_blank"
                                                            rel="nofollow"><?php echo esc_attr__('Documentation','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-gdpr"><?php echo esc_attr__('GDPR','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-gdpr-0"><input type="radio" name="data[gdpr]"
                                                                value="0"
                                                                id="asl-gdpr-0"><?php echo esc_attr__('Disable','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-gdpr-1"><input type="radio" name="data[gdpr]"
                                                                value="1"
                                                                id="asl-gdpr-1"><?php echo esc_attr__('Plugin GDPR','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-gdpr-2"><input type="radio" name="data[gdpr]"
                                                                value="2"
                                                                id="asl-gdpr-2"><?php echo esc_attr__('Borlabs Cookies','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p"><a class="text-muted" target="_blank"
                                                            href="https://agilestorelocator.com/wiki/gdpr-consent-for-google-maps-library/"><?php echo esc_attr__('GDPR Consent for the Google Maps Library','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Store schedule switch -->
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-store_schedule"><?php echo esc_attr__('Store Schedule','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-store_schedule"><input
                                                            type="checkbox" value="1" class="custom-control-input"
                                                            name="data[store_schedule]" id="asl-store_schedule"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Schedule stores to display on specified duration','asl_locator') ?>
                                                        | <span
                                                            class="red"><?php echo esc_attr__('Beta version','asl_locator') ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 asl-general-pro-summary">
                                            <div class="asl-general-pro-summary__icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                            </div>
                                            <div class="asl-general-pro-summary__content">
                                                <span><?php esc_html_e('Advanced General Options', 'asl_locator'); ?></span>
                                                <h5><?php esc_html_e('Unlock more control with Pro', 'asl_locator'); ?></h5>
                                                <p><?php esc_html_e('Fine-tune search behavior, distance controls, category logic, analytics, and multilingual store data.', 'asl_locator'); ?></p>
                                                <div class="asl-general-pro-summary__features" aria-label="<?php esc_attr_e('Pro features', 'asl_locator'); ?>">
                                                    <span><?php esc_html_e('Advanced Search', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Distance Control', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Distance Options', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Category Logic', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Address Dropdowns', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Dropdown Filters', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Store Branches', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('List Loading', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Store Scheduling', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Marker Loading', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Time Filter', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('Analytics', 'asl_locator'); ?></span>
                                                    <span><?php esc_html_e('WPML Data', 'asl_locator'); ?></span>
                                                </div>
                                            </div>
                                            <a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?><span aria-hidden="true">→</span></a>
                                        </div>
                                    </div>
                                </div>
                                <div id="maps-tab" class="tab-pane">
                                    <section class="asl-map-config-card" aria-labelledby="asl-map-config-title">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row asl-map-provider-settings">
                                                <?php echo $asl_map_provider_settings; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped setting markup captured above. ?>
                                            </div>
                                        </div>
                                        <section class="col-12 asl-map-settings-group" data-map-settings-group="behavior">
                                            <header class="asl-map-settings-group__header">
                                                <span class="dashicons dashicons-admin-settings" aria-hidden="true"></span>
                                                <div><h5><?php echo esc_html__('Map Display & Behavior','asl_locator') ?></h5><p><?php echo esc_html__('Set the initial map view, zoom behavior, language, controls, and Google map styling.','asl_locator') ?></p></div>
                                            </header>
                                            <div class="row">
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-zoom"><?php echo esc_attr__('Default Zoom','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select id="asl-zoom" name="data[zoom]" class="custom-select">
                                                        <?php for($index = 2;$index <= 20;$index++):?>
                                                        <option value="<?php echo $index ?>"><?php echo $index ?>
                                                        </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                    <p class="help-p"><a target="_blank"
                                                            href="https://agilestorelocator.com/wiki/why-the-google-map-zoom-in-on-the-page-load/"><?php echo esc_attr__('Why the Default Zoom is not working?','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-zoom_li"><?php echo esc_attr__('Clicked Zoom','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select id="asl-zoom_li" name="data[zoom_li]" class="custom-select">
                                                        <option value="">
                                                            <?php echo esc_attr__('Default Zoom','asl_locator') ?>
                                                        </option>
                                                        <?php for($index = 2;$index <= 20;$index++):?>
                                                        <option value="<?php echo $index ?>"><?php echo $index ?>
                                                        </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Zoom value when store list item is clicked','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-search_zoom"><?php echo esc_attr__('Search Zoom','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select id="asl-search_zoom" name="data[search_zoom]"
                                                        class="custom-select">
                                                        <option value="0">
                                                            <?php echo esc_attr__('Fit Bound Location','asl_locator') ?>
                                                        </option>
                                                        <?php for($index = 2;$index <= 20;$index++):?>
                                                        <option value="<?php echo $index ?>"><?php echo $index ?>
                                                        </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Zoom value when a search is performed, it works only when radius circle is disabled.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx asl-google-map-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-map_region"><?php echo esc_attr__('Map Region','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select id="asl-map_region" name="data[map_region]"
                                                        class="custom-select">
                                                        <option value=""><?php echo esc_attr__('None','asl_locator') ?>
                                                        </option>
                                                        <?php foreach($countries as $country): ?>
                                                        <option value="<?php echo esc_attr__($country->code) ?>">
                                                            <?php echo esc_attr__($country->country) ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx asl-google-map-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-map_language"><?php echo esc_attr__('Map Language','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control validate[minSize[2]]"
                                                        maxlength="5" name="data[map_language]" id="asl-map_language"
                                                        placeholder="Example: US">
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Enter the language code.','asl_locator') ?>
                                                        <a href="https://agilestorelocator.com/wiki/change-google-maps-language/"
                                                            target="_blank" rel="nofollow"><?php echo esc_attr__('Get Code','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx asl-google-map-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-google_search_type"><?php echo esc_attr__('Search Field','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select name="data[google_search_type]" id="asl-google_search_type"
                                                        class="custom-select">
                                                        <option value=""><?php echo esc_attr__('All','asl_locator') ?>
                                                        </option>
                                                        <option value="cities">
                                                            <?php echo esc_attr__('Cities (Cities)','asl_locator') ?>
                                                        </option>
                                                        <option value="regions">
                                                            <?php echo esc_attr__('Regions (Locality, City, State)','asl_locator') ?>
                                                        </option>
                                                        <option value="geocode">
                                                            <?php echo esc_attr__('Geocode','asl_locator') ?></option>
                                                        <option value="address">
                                                            <?php echo esc_attr__('Address','asl_locator') ?></option>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('To restrict the Google Place API search scope','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-cluster"><?php echo esc_attr__('Cluster','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select id="asl-cluster" name="data[cluster]" class="custom-select">
                                                        <option value="0">
                                                            <?php echo esc_attr__('Disable','asl_locator') ?></option>
                                                        <option value="1">
                                                            <?php echo esc_attr__('Marker Clusterer Plus','asl_locator') ?>
                                                        </option>
                                                        <option value="2">
                                                            <?php echo esc_attr__('Marker Clusterer (New)','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Count of markers will appear as clusters','asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/store-locator-clusters/"
                                                            target="_blank"
                                                            rel="nofollow"><?php echo esc_attr__('Change Colors','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-scroll_wheel"><?php echo esc_attr__('Mouse Scroll','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-scroll_wheel"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[scroll_wheel]" id="asl-scroll_wheel"><span
                                                            class="slider round"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-radius_circle"><?php echo esc_attr__('Radius Circle','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-radius_circle"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[radius_circle]" id="asl-radius_circle"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('It only appear with dropdown control and overrides the search zoom value to fitbound','asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/radius-circle/"
                                                            target="_blank"
                                                            rel="nofollow"><?php echo esc_attr__('Radius Colors','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx asl-google-map-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-remove_maps_script"><?php echo esc_attr__('Remove Other Maps Scripts','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-remove_maps_script"><input
                                                            type="checkbox" value="1" class="custom-control-input"
                                                            name="data[remove_maps_script]"
                                                            id="asl-remove_maps_script"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Remove other Google Maps scripts in case of malfunctioning','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 asl-google-advanced-marker-setting">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-advanced_marker"><?php echo esc_attr__('Google Advanced Marker','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select name="data[advanced_marker]" id="asl-advanced_marker"
                                                        class="custom-select">
                                                        <option value="">
                                                            <?php echo esc_attr__('Disabled','asl_locator') ?></option>
                                                        <?php

                                          $adv_mkrs = \AgileStoreLocator\Helper::advanced_marker_tmpls();

                                          foreach ($adv_mkrs as $option) {
                                              $option_label = $option['label'];
                                              $option_value = $option['value'];
                                              $option_disabled = $option['disable'];

                                              echo '<option value="' . esc_attr($option_value) . '"';
                                              
                                              if ($option_disabled) {
                                                  echo ' disabled';
                                              }
                                              
                                              echo '>' . esc_html($option_label) . '</option>';
                                          }
                                          ?>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo __('Google newly launched advanced marker option, read the documentation guide about <a href="https://agilestorelocator.com/wiki/google-advanced-markers/">Google Advanced Markers</a>','asl_locator') ?>
                                                        </p>
                                                </div>
                                            </div>
                                        </div>
                                            </div>
                                        </section>
                                    </div>
                                    <section class="asl-map-settings-group asl-map-settings-group--google-style" data-map-settings-group="google-style">
                                        <header class="asl-map-settings-group__header">
                                            <span class="dashicons dashicons-art" aria-hidden="true"></span>
                                            <div><h5><?php echo esc_html__('Google Map Styling','asl_locator') ?></h5><p><?php echo esc_html__('Choose a predefined Google map theme or provide your own custom style.','asl_locator') ?></p></div>
                                        </header>
                                        <div class="row">
                                        <div class="col-md-12 form-group mb-3 map_layout">
                                            <div class="row">
                                                <div class="col-12 a-radio-select asl-map-layout-options">
                                                    <label class="custom-control-label asl-map-layout-label"
                                                        for="asl-map_layout"><?php echo esc_attr__('Map Layouts','asl_locator') ?></label>
                                                    <input type="radio" id="asl-map_layout-0" value="0"
                                                        name="data[map_layout]"><label for="asl-map_layout-0"><span
                                                            class="actv"></span><img
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/map/25-blue-water/25-blue-water.png" /></label>
                                                    <input type="radio" id="asl-map_layout-1" value="1"
                                                        name="data[map_layout]"><label for="asl-map_layout-1"><span
                                                            class="actv"></span><img
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/map/Flat Map/53-flat-map.png" /></label>
                                                    <input type="radio" id="asl-map_layout-2" value="2"
                                                        name="data[map_layout]"><label for="asl-map_layout-2"><span
                                                            class="actv"></span><img
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/map/Icy Blue/7-icy-blue.png" /></label>
                                                    <input type="radio" id="asl-map_layout-3" value="3"
                                                        name="data[map_layout]"><label for="asl-map_layout-3"><span
                                                            class="actv"></span><img
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/map/Pale Dawn/1-pale-dawn.png" /></label>
                                                    <input type="radio" id="asl-map_layout-4" value="4"
                                                        name="data[map_layout]"><label for="asl-map_layout-4"><span
                                                            class="actv"></span><img
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/map/cladme/6618-cladme.png" /></label>
                                                    <input type="radio" id="asl-map_layout-5" value="5"
                                                        name="data[map_layout]"><label for="asl-map_layout-5"><span
                                                            class="actv"></span><img
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/map/light monochrome/29-light-monochrome.png" /></label>
                                                    <input type="radio" id="asl-map_layout-6" value="6"
                                                        name="data[map_layout]"><label for="asl-map_layout-6"><span
                                                            class="actv"></span><img
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/map/mostly grayscale/4183-mostly-grayscale.png" /></label>
                                                    <input type="radio" id="asl-map_layout-7" value="7"
                                                        name="data[map_layout]"><label for="asl-map_layout-7"><span
                                                            class="actv"></span><img
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/map/turquoise water/8-turquoise-water.png" /></label>
                                                    <input type="radio" id="asl-map_layout-8" value="8"
                                                        name="data[map_layout]"><label for="asl-map_layout-8"><span
                                                            class="actv"></span><img
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/map/unsaturated browns/70-unsaturated-browns.png" /></label>
                                                    <input type="radio" id="asl-map_layout-9" value="9"
                                                        name="data[map_layout]"><label for="asl-map_layout-9"><span
                                                            class="actv"></span><span
                                                            class="ml-custom"><b><?php echo esc_attr__('Custom','asl_locator') ?></b></span></label>
                                                </div>
                                                <div class="col-12 col-md-6 mb-5 sl-complx asl-map-custom-setting">
                                                    <div class="form-group d-lg-flex d-md-block">
                                                        <label class="custom-control-label"
                                                            for="asl-map_layout_custom"><?php echo esc_attr__('Map Custom','asl_locator') ?></label>
                                                        <div class="form-group-inner">
                                                            <textarea id="asl-map_layout_custom" rows="12"
                                                                placeholder="<?php echo esc_attr__('Google Style','asl_locator') ?>"
                                                                class="input-medium form-control"><?php echo esc_textarea($custom_map_style) ?></textarea>
                                                            <p class="help-p"><a target="_blank"
                                                                    href="https://agilestorelocator.com/wiki/google-map-styles/"><?php echo esc_attr__('How to create custom maps?','asl_locator') ?></a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </section>
                                    </section>
                                </div>
                                <div id="sl-ui-tab" class="tab-pane">
                                    <div class="row mt-2">
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-map_top"><?php echo esc_attr__('Map & List Order','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select name="data[map_top]" id="asl-map_top" class="custom-select">
                                                        <option value="0">
                                                            <?php echo esc_attr__('List Top, Map Bottom','asl_locator') ?>
                                                        </option>
                                                        <option value="2">
                                                            <?php echo esc_attr__('Map Top, List Bottom','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-hide_search"><?php echo esc_attr__('Hide Search','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-hide_search"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[hide_search]" id="asl-hide_search"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p"><?php echo esc_html__('Hide the main address search controls from the store locator.', 'asl_locator') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-full_height"><?php echo esc_attr__('Full Height','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select name="data[full_height]" id="asl-full_height"
                                                        class="custom-select">
                                                        <option value=""><?php echo esc_attr__('None','asl_locator') ?>
                                                        </option>
                                                        <option value="full-height">
                                                            <?php echo esc_attr__('Full Height (Not Fixed)','asl_locator') ?>
                                                        </option>
                                                        <option value="full-height asl-fixed">
                                                            <?php echo esc_attr__('Full Height (Fixed)','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                    <p class="help-p"><a target="_blank"
                                                            href="https://agilestorelocator.com/wiki/can-we-adjust-the-height-of-the-store-locator-map/"
                                                            href=""><?php echo esc_attr__('Change Height of the Locator','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-full_width"><?php echo esc_attr__('Full Width','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-full_width"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[full_width]" id="asl-full_width"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Make the store locator full width 100% with respect to the parent container','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-tabs_layout"><?php echo esc_attr__('Tabs Layout','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-tabs_layout-0"><input type="radio"
                                                                name="data[tabs_layout]" value="0"
                                                                id="asl-tabs_layout-0"><?php echo esc_attr__('Dropdowns','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-tabs_layout-1"><input type="radio"
                                                                name="data[tabs_layout]" value="1"
                                                                id="asl-tabs_layout-1"><?php echo esc_attr__('Clickable Tabs','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('To show categories dropdown in tab options.','asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/tabs-for-filter-instead-of-dropdown/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Guide Doc', 'asl_locator') ?></a><br><span
                                                            class="red">(Supported in Template 0 & 1, 4)</span></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-additional_info"><?php echo esc_attr__('Description','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-additional_info-0"><input type="radio"
                                                                name="data[additional_info]" value="0"
                                                                id="asl-additional_info-0"><?php echo esc_attr__('Hide','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-additional_info-1"><input type="radio"
                                                                name="data[additional_info]" value="1"
                                                                id="asl-additional_info-1"><?php echo esc_attr__('In Store List','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-additional_info-2"><input type="radio"
                                                                name="data[additional_info]" value="2"
                                                                id="asl-additional_info-2"><?php echo esc_attr__('In Modal via Link','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('To show the description text either in listing or modal.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="week_hours"><?php echo esc_attr__('Hours Format','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-week_hours-0"><input type="radio"
                                                                name="data[week_hours]" value="0"
                                                                id="asl-week_hours-0"><?php echo esc_attr__('Today','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-week_hours-1"><input type="radio"
                                                                name="data[week_hours]" value="1"
                                                                id="asl-week_hours-1"><?php echo esc_attr__('7 Days','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-week_hours-2"><input type="radio"
                                                                name="data[week_hours]" value="2"
                                                                id="asl-week_hours-2"><?php echo esc_attr__('7 Days (Grouped)','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('To show only the current day hours or full week','asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/add-additional-time-slot-store-locator/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Guide Doc', 'asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-time_format"><?php echo esc_attr__('Time Format','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-time_format-0"><input type="radio"
                                                                name="data[time_format]" value="0"
                                                                id="asl-time_format-0"><?php echo esc_attr__('12 Hours','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-time_format-1"><input type="radio"
                                                                name="data[time_format]" value="1"
                                                                id="asl-time_format-1"><?php echo esc_attr__('24 Hours','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Select either 12 or 24 hours time format','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-zoom_btn"><?php echo esc_attr__('Zoom Button','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-zoom_btn"><input type="checkbox"
                                                            value="1" class="custom-control-input" name="data[zoom_btn]"
                                                            id="asl-zoom_btn"><span class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Show/Hide Zoom button in the infobox.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-direction_btn"><?php echo esc_attr__('Direction Button','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-direction_btn"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[direction_btn]" id="asl-direction_btn"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Show/Hide direction button in the listing and infobox.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-slug_link"><?php echo esc_attr__('Website Link','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-slug_link"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[slug_link]" id="asl-slug_link"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_html__('Enable the website link button.', 'asl_locator') ?>
                                                        | <a href="https://agilestorelocator.com/wiki/add-site-link-store-locator-list/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('Guide Doc', 'asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-show_categories"><?php echo esc_attr__('Show Categories','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-show_categories"><input
                                                            type="checkbox" value="1" class="custom-control-input"
                                                            name="data[show_categories]" id="asl-show_categories"><span
                                                            class="slider round"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-hide_hours"><?php echo esc_attr__('Hide Hours','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-hide_hours"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[hide_hours]" id="asl-hide_hours"><span
                                                            class="slider round"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-hide_logo"><?php echo esc_attr__('Hide Logo','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-hide_logo"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[hide_logo]" id="asl-hide_logo"><span
                                                            class="slider round"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-print_btn"><?php echo esc_attr__('Print Button','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-print_btn"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[print_btn]" id="asl-print_btn"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p"><a target="_blank"
                                                            href="https://agilestorelocator.com/wiki/custom-print-header-for-store-list/"><?php echo esc_attr__('Add Print Header or remove it','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-display_list"><?php echo esc_attr__('Display List','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-display_list"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[display_list]" id="asl-display_list"><span
                                                            class="slider round"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <section class="asl-pro-locked-section asl-pro-locked-panel asl-settings-pro-lock" aria-labelledby="asl-ui-lock-title">
                                        <div class="asl-pro-lock-overlay">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                            <strong id="asl-ui-lock-title"><?php esc_html_e('Advanced UI layouts are a Pro feature', 'asl_locator'); ?></strong>
                                            <span><?php esc_html_e('Upgrade to use tab layouts, additional UI templates, and InfoBox layouts.', 'asl_locator'); ?></span>
                                            <a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a>
                                        </div>
                                        <div class="asl-pro-locked-preview" aria-hidden="true">
                                    <div class="row mb-4 asl-ui-template-panel">
                                        <div class="col-12 p-0">
                                            <header class="asl-ui-template-panel__header">
                                                <span class="asl-ui-template-panel__icon" aria-hidden="true"><span class="dashicons dashicons-layout"></span></span>
                                                <div>
                                                    <span><?php echo esc_html__('Appearance', 'asl_locator') ?></span>
                                                    <h5><?php echo esc_html__('UI Templates', 'asl_locator') ?></h5>
                                                    <p><?php echo esc_html__('Choose the locator structure, layout and color scheme, then preview the selected design.', 'asl_locator') ?></p>
                                                </div>
                                                <a target="_blank" rel="noopener noreferrer" href="https://agilestorelocator.com/wiki/store-locator-templates/">
                                                    <?php echo esc_html__('Template Guide', 'asl_locator') ?><span class="dashicons dashicons-external" aria-hidden="true"></span>
                                                </a>
                                            </header>
                                        </div>
                                        <div class="col-lg-4 asl-ui-template-panel__controls">
                                            <div class="form-group">
                                                <label class="custom-control-label" for="asl-template"><?php echo esc_html__('Template','asl_locator') ?></label>
                                                <p class="asl-ui-template-field-help"><?php echo esc_html__('Select the base design for your store locator.', 'asl_locator') ?></p>
                                                <div class="input-group mb-3">
                                                    <select id="asl-template" class="form-select col-md-12"
                                                        name="data[template]">
                                                        <option value="0">
                                                            <?php echo esc_attr__('Template','asl_locator') ?> 0
                                                        </option>
                                                        <option value="1">
                                                            <?php echo esc_attr__('Template','asl_locator') ?> 1
                                                        </option>
                                                        <option value="2">
                                                            <?php echo esc_attr__('Template','asl_locator') ?> 2
                                                        </option>
                                                        <option value="3">
                                                            <?php echo esc_attr__('Template','asl_locator') ?> 3
                                                        </option>
                                                        <option value="4">
                                                            <?php echo esc_attr__('Template','asl_locator') ?> 4
                                                            (<?php echo esc_attr__('Grid','asl_locator') ?>)</option>
                                                        <option value="5">
                                                            <?php echo esc_attr__('Template','asl_locator') ?> 5
                                                        </option>
                                                        <option value="6">
                                                            <?php echo esc_attr__('Template','asl_locator') ?> 6
                                                            (<?php echo esc_attr__('Beta','asl_locator') ?>)</option>
                                                        <option value="list">
                                                            <?php echo esc_attr__('Template List','asl_locator') ?>
                                                        </option>
                                                        <option value="list-2">
                                                            <?php echo esc_attr__('Template List 2','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group layout-section">
                                                <label class="custom-control-label" for="asl-layout"><?php echo esc_html__('List Layout','asl_locator') ?></label>
                                                <p class="asl-ui-template-field-help"><?php echo esc_html__('Choose how store results are arranged.', 'asl_locator') ?></p>
                                                <div class="input-group mb-3">
                                                    <select id="asl-layout" class=" form-select" name="data[layout]">
                                                        <option value="0">
                                                            <?php echo esc_attr__('List Format','asl_locator') ?>
                                                        </option>
                                                        <option value="1">
                                                            <?php echo esc_attr__('Accordion (States, Cities, Countries)','asl_locator') ?>
                                                        </option>
                                                        <option value="2">
                                                            <?php echo esc_attr__('Accordion (Categories)','asl_locator') ?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div
                                                    class="mt-3 template-box box_layout_list box_layout_3 box_layout_6 box_layout_5 box_layout_0 hide">
                                                    <div class="form-group color_scheme">
                                                        <label class="custom-control-label"
                                                            for="asl-color_scheme"><?php echo esc_attr__('Color Schema','asl_locator') ?></label>
                                                        <div class="a-radio-select">
                                                            <?php for($_ind = 0; $_ind <= 9; $_ind++): ?>
                                                            <span>
                                                                <input type="radio"
                                                                    id="asl-color_scheme-<?php echo $_ind ?>"
                                                                    value="<?php echo $_ind ?>"
                                                                    name="data[color_scheme]">
                                                                <label class="color-box color-<?php echo $_ind ?>"
                                                                    for="asl-color_scheme-<?php echo $_ind ?>"></label>
                                                            </span>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="template-box box_layout_1 hide">
                                                    <div class="form-group color_scheme layout_2">
                                                        <label class="custom-control-label"
                                                            for="asl-color_scheme_1"><?php echo esc_attr__('Color Scheme','asl_locator') ?></label>
                                                        <div class="a-radio-select">
                                                            <?php for($_ind = 0; $_ind <= 9; $_ind++): ?>
                                                            <span>
                                                                <input type="radio"
                                                                    id="asl-color_scheme_1-<?php echo $_ind ?>"
                                                                    value="<?php echo $_ind ?>"
                                                                    name="data[color_scheme_1]">
                                                                <label class="color-box color-<?php echo $_ind ?>"
                                                                    for="asl-color_scheme_1-<?php echo $_ind ?>">
                                                                    <i class="actv"></i>
                                                                    <span class="co_1"></span>
                                                                    <span class="co_2"></span>
                                                                </label>
                                                            </span>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="template-box box_layout_2 hide">
                                                    <div class="form-group map_layout color_scheme layout_2">
                                                        <label class="custom-control-label"
                                                            for="asl-color_scheme"><?php echo esc_attr__('Color Scheme','asl_locator') ?></label>
                                                        <div class="a-radio-select">
                                                            <?php 
                                                $tmpl_2_colors = array(
                                                  '0' => array('#CC3333', '#542733'),
                                                  '1' => array('#008FED', '#2580C3'),
                                                  '2' => array('#93628F', '#4A2849'),
                                                  '3' => array('#FF9800', '#FFC107'),
                                                  '4' => array('#01524B', '#75C9D3'),
                                                  '5' => array('#ED468B', '#FDCC29'),
                                                  '6' => array('#D55121', '#FB9C6C'),
                                                  '7' => array('#D13D94', '#AD0066'),
                                                  '8' => array('#99BE3B', '#01735A'),
                                                  '9' => array('#3D5B99', '#EFF1F6')
                                                );
                                                foreach($tmpl_2_colors as $_ct => $ctv):
                                                ?>
                                                            <span>
                                                                <input type="radio"
                                                                    id="asl-color_scheme_2-<?php echo $_ct ?>"
                                                                    value="<?php echo $_ct ?>"
                                                                    name="data[color_scheme_2]">
                                                                <label class="color-box color-<?php echo $_ct ?>"
                                                                    for="asl-color_scheme_2-<?php echo $_ct ?>"
                                                                    style="background-color:<?php echo $ctv[0] ?>">
                                                                    <i class="actv"></i>
                                                                    <span class="co_1"></span>
                                                                </label>
                                                            </span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="help-p mb-3"><a href="<?php echo esc_url(admin_url('admin.php?page=sl-ui-customizer')) ?>"><?php echo esc_html__('Open Colors & Fonts Customizer','asl_locator') ?></a></p>
                                                <div class="box_layout_0 box_layout_3 box_layout_6 box_layout_5 box_layout_list hide">
                                                    <div class="form-group mb-3 Font_color">
                                                        <label class="custom-control-label"
                                                            for="asl-font_color_scheme"><?php echo esc_attr__('Font Colors','asl_locator') ?></label>
                                                        <div class="a-radio-select">
                                                            <?php for($_ind = 0; $_ind <= 4; $_ind++): ?>
                                                            <span>
                                                                <input type="radio"
                                                                    id="asl-font_color_scheme-<?php echo $_ind ?>"
                                                                    value="<?php echo $_ind ?>"
                                                                    name="data[font_color_scheme]">
                                                                <label class="font-color-box color-<?php echo $_ind ?>"
                                                                    for="asl-font_color_scheme-<?php echo $_ind ?>"></label>
                                                            </span>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 asl-ui-template-panel__preview">
                                            <div class="row">
                                                <div class="col-12">
                                                    <figure class="figure asl-ui-template-preview">
                                                        <span class="asl-ui-template-preview__badge"><span class="dashicons dashicons-visibility" aria-hidden="true"></span><?php echo esc_html__('Live selection', 'asl_locator') ?></span>
                                                        <img id="asl-tmpl-img"
                                                            src="<?php echo ASL_URL_PATH ?>admin/images/asl-tmpl-0-0.png"
                                                            alt="<?php echo esc_attr__('Selected store locator template preview', 'asl_locator') ?>" class="figure-img img-fluid rounded">
                                                        <figcaption class="figure-caption">
                                                            <strong><?php echo esc_html__('Selected Store Locator','asl_locator') ?></strong>
                                                            <span><?php echo esc_html__('The preview updates when you choose a template or color scheme.', 'asl_locator') ?></span>
                                                        </figcaption>
                                                    </figure>
                                                </div>
                                                <div class="col-12">
                                                    <a href="<?php echo esc_url(admin_url('admin.php?page=sl-ui-customizer')) ?>"
                                                        class="btn asl-ui-template-customizer-btn"><span class="dashicons dashicons-admin-appearance" aria-hidden="true"></span><?php echo esc_html__('Open UI Customizer','asl_locator') ?></a>
                                                </div>
                                                <div class="col-12">
                                                    <p class="help-p"><a target="_blank"
                                                            rel="noopener noreferrer" href="https://agilestorelocator.com/wiki/store-locator-templates/"><?php echo esc_html__('Learn how to use multiple store locators with different templates','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row asl-ui-infobox-panel">
                                        <div class="col-md-12 form-group mb-3 infobox_layout">
                                            <div class="asl-ui-infobox-panel__heading">
                                                <span class="dashicons dashicons-format-image" aria-hidden="true"></span>
                                                <div>
                                                    <label class="custom-control-label" for="asl-infobox_layout"><?php echo esc_html__('InfoBox Layout','asl_locator') ?></label>
                                                    <p><?php echo esc_html__('Choose how store details appear when a visitor selects a marker.', 'asl_locator') ?></p>
                                                </div>
                                            </div>
                                            <div class="a-radio-select">
                                                <input type="radio" id="asl-infobox_layout-0" value="0"
                                                    name="data[infobox_layout]"><label for="asl-infobox_layout-0"><img
                                                        src="<?php echo ASL_URL_PATH ?>/admin/images/infobox_1.png" /></label>
                                                <input type="radio" id="asl-infobox_layout-2" value="2"
                                                    name="data[infobox_layout]"><label for="asl-infobox_layout-2"><img
                                                        src="<?php echo ASL_URL_PATH ?>/admin/images/infobox_2.png" /></label>
                                                <input type="radio" id="asl-infobox_layout-1" value="1"
                                                    name="data[infobox_layout]"><label for="asl-infobox_layout-1"><img
                                                        src="<?php echo ASL_URL_PATH ?>/admin/images/infobox_3.png" /></label>
                                            </div>
                                        </div>
                                    </div>
                                        </div>
                                    </section>
                                </div>
                                <div id="sl-detail" class="tab-pane">
                                    <div class="row mt-2">
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-link_type"><?php echo esc_attr__('Website Link Type','asl_locator') ?></label>
                                                <div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-link_type-0"><input type="radio"
                                                                name="data[link_type]" value="0"
                                                                id="asl-link_type-0"><?php echo esc_attr__('Website Field','asl_locator') ?></label>
                                                    </div>
                                                    <div class="asl-wc-radio">
                                                        <label for="asl-link_type-1"><input type="radio"
                                                                name="data[link_type]" value="1"
                                                                id="asl-link_type-1"><?php echo esc_attr__('Page Slug','asl_locator') ?></label>
                                                    </div>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Select the URL type of website link, page slug will only work when page slug is provided','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-store_schema"><?php echo esc_attr__('Store JSON-LD','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-store_schema"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[store_schema]" id="asl-store_schema"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('JSON schema data for Google SEO','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-store_page_show_country"><?php echo esc_attr__('Show Country in Address','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-store_page_show_country"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[store_page_show_country]" id="asl-store_page_show_country"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Show the country in the address displayed on the store detail page. Directions and schema continue to use the full address.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-store_page_address_format"><?php echo esc_attr__('Store Page Address Format','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control"
                                                        name="data[store_page_address_format]" id="asl-store_page_address_format"
                                                        placeholder="{street}, {city}, {state} {postal_code}, {country}">
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Optional. Available placeholders: {street}, {city}, {state}, {postal_code}, {country}. Leave empty to use the default format.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-rewrite_slug"><?php echo esc_attr__('Store Page Slug','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control"
                                                            name="data[rewrite_slug]" id="asl-rewrite_slug"
                                                            placeholder="<?php echo esc_attr__('stores','asl_locator') ?>">
                                                        <input type="number" class="form-control"
                                                            name="data[rewrite_id]" id="asl-rewrite_id"
                                                            placeholder="<?php echo esc_attr__('156','asl_locator') ?>">
                                                    </div>
                                                    <ol class="help-p asl-store-slug-help">
                                                        <li><?php echo esc_html__('Provide the page-relative URL and the WordPress page ID.', 'asl_locator') ?></li>
                                                        <li><?php echo esc_html__('Add the [ASL_STORE] shortcode to the same page.', 'asl_locator') ?></li>
                                                        <li>
                                                            <a href="<?php echo esc_url(admin_url('options-permalink.php')) ?>"><?php echo esc_html__('Save Changes on the Permalinks page', 'asl_locator') ?></a>
                                                            <span aria-hidden="true"> · </span>
                                                            <a target="_blank" rel="noopener noreferrer" href="https://agilestorelocator.com/wiki/store-details-page/"><?php echo esc_html__('Store Detail Guide', 'asl_locator') ?></a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Create store slug -->
                                        <div class="col-md-6 col-sm-6 col-12 mb-5 sl-complx">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="slug_attr_ddl"><?php echo esc_attr__('Store Slug Fields','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <select multiple id="asl-slug_attr_ddl"
                                                        class="custom-select asl-chosen">
                                                        <?php foreach ($slug_attr as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>">
                                                            <?php echo esc_attr__($value, 'asl_locator') ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Title and City are default fields to create slug','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <!-- <button type="button" class="btn btn-primary float-right" data-loading-text="Saving..." data-completed-text="Settings Updated" id="btn-asl-slug_reset"> Reset Slug</button> -->
                                        </div>
                                        <!-- end  -->
                                    </div>
                                </div>
                                <div id="sl-register" class="tab-pane">
                                    <section class="asl-pro-locked-section asl-pro-locked-panel asl-settings-pro-lock asl-settings-pro-lock--muted" aria-label="<?php esc_attr_e('Lead and Cron management are Pro features', 'asl_locator'); ?>">
                                        <div class="asl-pro-locked-preview" aria-hidden="true" inert>
                                    <div class="row mt-2 mb-4">
                                        <div class="col-md-12">
                                            <a title="<?php echo esc_attr__('Filter & Export Leads','asl_locator') ?>"
                                                class="btn btn-primary btn-md float-right"
                                                href="<?php echo admin_url().'admin.php?page=sl-lead-manager' ?>"><?php echo esc_attr__('Lead Manager','asl_locator') ?></a>
                                            <a title="<?php echo esc_attr__('View Cron Logs','asl_locator') ?>"
                                                class="btn btn-light btn-md float-right mr-2" style="margin-right:1rem;"
                                                href="<?php echo admin_url().'admin.php?page=asl-cron-logs' ?>"><?php echo esc_attr__('Cron Logs','asl_locator') ?></a>
                                        </div>
                                    </div>
                                        </div>
                                    </section>
                                    <div class="row mt-2">
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-notify_email"><?php echo esc_attr__('Notification Email','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control" name="data[notify_email]"
                                                        id="asl-notify_email"
                                                        placeholder="<?php echo esc_attr__('Email address','asl_locator') ?>">
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Email address to recieve the email notification for stores registered through frontend form.','asl_locator') ?>
                                                        | <a target="_blank" class="text-muted"
                                                            href="https://agilestorelocator.com/wiki/how-to-add-a-lead-form/"><?php echo esc_attr__('How to add lead form?','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-admin_notify"><?php echo esc_attr__('Notification Status','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-admin_notify"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[admin_notify]" id="asl-admin_notify"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Enable all kind of email alerts & notifications.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <section class="asl-pro-locked-section asl-pro-locked-panel asl-settings-pro-lock asl-settings-pro-lock--muted" aria-label="<?php esc_attr_e('Cron logs and lead follow-up are Pro features', 'asl_locator'); ?>">
                                                <div class="asl-pro-locked-preview" aria-hidden="true" inert>
                                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-cron_log_enabled"><?php echo esc_attr__('Cron Log Viewer','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-cron_log_enabled"><input type="checkbox"
                                                            value="1" class="custom-control-input"
                                                            name="data[cron_log_enabled]" id="asl-cron_log_enabled"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Enable cron execution logs for CSV imports.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12 mb-5">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-lead_follow_up"><?php echo esc_attr__('Lead Follow-up Email','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-lead_follow_up"><input
                                                            type="checkbox" value="1" class="custom-control-input"
                                                            name="data[lead_follow_up]" id="asl-lead_follow_up"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Send a follow-up notification to the dealer lead after 48 hours.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                    <section class="asl-pro-locked-section asl-pro-locked-panel asl-settings-pro-lock" aria-labelledby="asl-cf7-lock-title">
                                        <div class="asl-pro-lock-overlay">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                            <strong id="asl-cf7-lock-title"><?php esc_html_e('Contact Form 7 Hook is a Pro feature', 'asl_locator'); ?></strong>
                                            <span><?php esc_html_e('Upgrade to route Contact Form 7 messages to the closest store.', 'asl_locator'); ?></span>
                                            <a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a>
                                        </div>
                                        <div class="asl-pro-locked-preview" aria-hidden="true">
                                    <section class="asl-settings-integration-section" aria-labelledby="asl-cf7-section-title">
                                        <div class="asl-settings-integration-section__header">
                                            <span class="asl-settings-integration-section__icon" aria-hidden="true">
                                                <span class="dashicons dashicons-email-alt"></span>
                                            </span>
                                            <div class="asl-settings-integration-section__copy">
                                                <h5 id="asl-cf7-section-title"><?php echo esc_html__('Contact Form 7 Hook', 'asl_locator') ?></h5>
                                                <p><?php echo esc_html__('Send a copy of a Contact Form 7 email to the closest store, determined by the postal code submitted in the form.', 'asl_locator') ?></p>
                                            </div>
                                            <span class="asl-settings-integration-section__badge"><?php echo esc_html__('Integration', 'asl_locator') ?></span>
                                        </div>
                                        <div class="row asl-settings-integration-section__body">
                                        <div class="col-md-6 col-sm-6 col-12">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-cf7_field"><?php echo esc_attr__('CF7 Postal Code Field','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <input type="text" class="form-control" name="data[cf7_field]"
                                                        id="asl-cf7_field"
                                                        placeholder="<?php echo esc_attr__('postal-code','asl_locator') ?>">
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Postal Code Field ID in CF7 Form.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-12">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-cf7_hook"><?php echo esc_attr__('Enable Hook','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-cf7_hook"><input type="checkbox"
                                                            value="1" class="custom-control-input" name="data[cf7_hook]"
                                                            id="asl-cf7_hook"><span class="slider round"></span></label>
                                                    <p class="help-p">
                                                        <?php echo esc_attr__('Enable the CF7 integration hook.','asl_locator') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </section>
                                        </div>
                                    </section>
                                    <section class="asl-pro-locked-section asl-pro-locked-panel asl-settings-pro-lock" aria-labelledby="asl-wpforms-lock-title">
                                        <div class="asl-pro-lock-overlay">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                            <strong id="asl-wpforms-lock-title"><?php esc_html_e('WPForms Hook is a Pro feature', 'asl_locator'); ?></strong>
                                            <span><?php esc_html_e('Upgrade to send WPForms notifications to the relevant stores.', 'asl_locator'); ?></span>
                                            <a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a>
                                        </div>
                                        <div class="asl-pro-locked-preview" aria-hidden="true">
                                    <section class="asl-settings-integration-section" aria-labelledby="asl-wpforms-section-title">
                                        <div class="asl-settings-integration-section__header">
                                            <span class="asl-settings-integration-section__icon" aria-hidden="true">
                                                <span class="dashicons dashicons-feedback"></span>
                                            </span>
                                            <div class="asl-settings-integration-section__copy">
                                                <h5 id="asl-wpforms-section-title"><?php echo esc_html__('WPForms Hook', 'asl_locator') ?></h5>
                                                <p><?php echo esc_html__('Send WPForms notifications to the relevant store owners through the Agile Store Locator integration.', 'asl_locator') ?></p>
                                            </div>
                                            <span class="asl-settings-integration-section__badge"><?php echo esc_html__('Integration', 'asl_locator') ?></span>
                                        </div>
                                        <div class="row asl-settings-integration-section__body">
                                        <div class="col-md-6 col-sm-6 col-12">
                                            <div class="form-group d-lg-flex d-md-block">
                                                <label class="custom-control-label"
                                                    for="asl-wpfrm_store_notify"><?php echo esc_attr__('Notification to Store Emails','asl_locator') ?></label>
                                                <div class="form-group-inner">
                                                    <label class="switch" for="asl-wpfrm_store_notify"><input
                                                            type="checkbox" value="1" class="custom-control-input"
                                                            name="data[wpfrm_store_notify]"
                                                            id="asl-wpfrm_store_notify"><span
                                                            class="slider round"></span></label>
                                                    <p class="help-p"><a target="_blank" rel="noopener noreferrer" class="text-muted"
                                                            href="https://agilestorelocator.com/wiki/integrating-wpforms-with-agile-store-locator/"><?php echo esc_attr__('How to create a WPForms hidden field?','asl_locator') ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </section>
                                        </div>
                                    </section>
                                </div>
                                <div id="sl-customizer" class="tab-pane">
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <section class="asl-settings-customizer">
                                                <header class="asl-settings-customizer__header">
                                                    <span class="asl-settings-customizer__icon" aria-hidden="true"><span class="dashicons dashicons-editor-code"></span></span>
                                                    <div class="asl-settings-customizer__heading">
                                                        <span><?php echo esc_html__('Template editor','asl_locator') ?></span>
                                                        <h5><?php echo esc_html__('Customize Store Locator Templates','asl_locator') ?></h5>
                                                        <p><?php echo esc_html__('Edit the store list or marker InfoBox HTML safely without changing the plugin template files.','asl_locator') ?></p>
                                                    </div>
                                                    <a class="asl-settings-guide-link" target="_blank" rel="noopener noreferrer" href="https://agilestorelocator.com/wiki/store-locator-template-customizer/">
                                                        <?php echo esc_html__('Customizer Guide','asl_locator') ?><span class="dashicons dashicons-external" aria-hidden="true"></span>
                                                    </a>
                                                </header>
                                                <div class="asl-settings-customizer__body">
                                                    <div class="asl-settings-customizer__selectors">
                                                        <div class="asl-settings-customizer__field">
                                                            <label for="asl-customize-template"><?php echo esc_html__('Template','asl_locator') ?></label>
                                                            <p><?php echo esc_html__('Choose the locator template you want to edit.','asl_locator') ?></p>
                                                    <?php 
                                          // Get all the templates support customization
                                          $cust_tmpls = \AgileStoreLocator\Helper::customizer_tmpls();
                                       ?>
                                                            <select id="asl-customize-template" class="form-select">
                                                        <?php
                                          foreach($cust_tmpls as $cust_key => $cust_tmpl): ?>
                                                        <option value="<?php echo esc_attr($cust_key) ?>">
                                                            <?php echo esc_attr($cust_tmpl['label']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                        </div>
                                                        <div class="asl-settings-customizer__field">
                                                            <label for="asl-customize-section"><?php echo esc_html__('Section','asl_locator') ?></label>
                                                            <p><?php echo esc_html__('Select which part of the template to customize.','asl_locator') ?></p>
                                                            <select id="asl-customize-section" class="form-select">
                                                                <option value="list"><?php echo esc_html__('List','asl_locator') ?></option>
                                                                <option value="infobox"><?php echo esc_html__('InfoBox','asl_locator') ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="asl-settings-customizer__actions">
                                                <button type="button" class="btn asl-customizer-btn asl-customizer-btn--load"
                                                    data-loading-text="<?php echo esc_attr__('Loading...','asl_locator') ?>"
                                                    data-completed-text="Loaded"
                                                    id="btn-asl-load_ctemp"><span class="dashicons dashicons-download" aria-hidden="true"></span><?php echo esc_html__('Load Template','asl_locator') ?></button>
                                                <button type="button" class="btn asl-customizer-btn asl-customizer-btn--save"
                                                    data-loading-text="<?php echo esc_attr__('Saving...','asl_locator') ?>"
                                                    data-completed-text="Template Updated"
                                                    id="btn-asl-save_ctemp" disabled><span class="dashicons dashicons-saved" aria-hidden="true"></span><?php echo esc_html__('Save Template','asl_locator') ?></button>
                                                <a href="<?php echo esc_url(admin_url('admin.php?page=sl-ui-customizer')) ?>"
                                                    class="btn asl-customizer-btn asl-customizer-btn--secondary"><span class="dashicons dashicons-admin-appearance" aria-hidden="true"></span><?php echo esc_html__('Colors & Fonts','asl_locator') ?></a>
                                                <button type="button" class="btn asl-customizer-btn asl-customizer-btn--reset"
                                                    data-loading-text="<?php echo esc_attr__('Reseting...','asl_locator') ?>"
                                                    data-completed-text="Reset Done"
                                                    id="btn-asl-reset_ctemp" disabled><span class="dashicons dashicons-image-rotate" aria-hidden="true"></span><?php echo esc_html__('Reset Template','asl_locator') ?></button>
                                                    </div>
                                                    <div class="form-group layout-section asl-settings-customizer__editor">
                                                        <div class="asl-settings-customizer__editor-header">
                                                            <div>
                                                                <label for="sl-custom-template-textarea"><?php echo esc_html__('Template Editor','asl_locator') ?></label>
                                                                <p><?php echo esc_html__('Load a template to edit its HTML source.','asl_locator') ?></p>
                                                            </div>
                                                            <span><?php echo esc_html__('HTML','asl_locator') ?></span>
                                                        </div>
                                                        <div class="input-group-richtex sl-custom-tpl-text-section">
                                                            <textarea id="sl-custom-template-textarea"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <section class="asl-pro-locked-section asl-pro-locked-panel asl-settings-pro-lock" aria-labelledby="asl-template-backups-lock-title">
                                                <div class="asl-pro-lock-overlay">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                                                    <strong id="asl-template-backups-lock-title"><?php esc_html_e('Template Backups are a Pro feature', 'asl_locator'); ?></strong>
                                                    <span><?php esc_html_e('Upgrade to create and manage protected template backups.', 'asl_locator'); ?></span>
                                                    <a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a>
                                                </div>
                                                <div class="asl-pro-locked-preview" aria-hidden="true">
                                            <?php $tmpl_backups = \AgileStoreLocator\Helper::getBackupTemplates(); ?>
                                            <section class="asl-template-backups">
                                                <header class="asl-template-backups__header">
                                                    <div>
                                                        <span class="asl-template-backups__eyebrow"><?php esc_html_e('Theme files', 'asl_locator'); ?></span>
                                                        <h5><?php esc_html_e('Template Backups', 'asl_locator'); ?> <small><?php echo esc_html(count($tmpl_backups)); ?></small></h5>
                                                        <p><?php esc_html_e('Keep customized template files safely in your active theme so plugin updates do not overwrite them.', 'asl_locator'); ?></p>
                                                    </div>
                                                    <div class="asl-template-backups__actions">
                                                        <a class="asl-settings-guide-link" target="_blank" rel="noopener noreferrer" href="https://agilestorelocator.com/wiki/customize-template-without-modifying-core-plugin/">
                                                            <?php esc_html_e('Template Backup Guide', 'asl_locator'); ?><span class="dashicons dashicons-external" aria-hidden="true"></span>
                                                        </a>
                                                        <?php if ($tmpl_backups) : ?>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" id="sl-btn-tmpl-remove"><span class="dashicons dashicons-trash"></span><?php esc_html_e('Delete Backup', 'asl_locator'); ?></button>
                                                        <?php endif; ?>
                                                    </div>
                                                </header>
                                                <div class="asl-template-backups__grid">
                                                    <?php foreach ($tmpl_backups as $tmpl) : ?>
                                                        <?php $tmpl_image = file_exists(ASL_PLUGIN_PATH . 'admin/images/new/' . $tmpl['image']) ? $tmpl['image'] : 'tmpl-form.png'; ?>
                                                        <article class="asl-template-backup-card">
                                                            <div class="asl-template-backup-card__preview">
                                                                <img src="<?php echo esc_url(ASL_URL_PATH . 'admin/images/new/' . $tmpl_image); ?>" alt="">
                                                                <span><i></i><?php esc_html_e('Backed up', 'asl_locator'); ?></span>
                                                            </div>
                                                            <div class="asl-template-backup-card__body">
                                                                <strong><?php echo esc_html($tmpl['title']); ?></strong>
                                                                <small><?php echo esc_html($tmpl['file']); ?></small>
                                                            </div>
                                                        </article>
                                                    <?php endforeach; ?>
                                                    <?php if (!$tmpl_backups) : ?>
                                                        <div class="asl-template-backups__empty">
                                                            <span class="dashicons dashicons-backup"></span>
                                                            <strong><?php esc_html_e('No template backups yet', 'asl_locator'); ?></strong>
                                                            <small><?php esc_html_e('Create your first backup to protect template customizations.', 'asl_locator'); ?></small>
                                                        </div>
                                                    <?php endif; ?>
                                                    <button type="button" class="asl-template-backup-add" id="sl-btn-tmpl-backup">
                                                        <span class="dashicons dashicons-plus-alt2"></span>
                                                        <strong><?php esc_html_e('Create Backup', 'asl_locator'); ?></strong>
                                                        <small><?php esc_html_e('Choose a template file', 'asl_locator'); ?></small>
                                                    </button>
                                                </div>
                                            </section>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                                <div id="sl-pro" class="tab-pane">
                                    <?php
                                    $asl_pro_images  = array('pro-12.jpg', 'pro-18.jpg', 'pro-19.jpg', 'pro-13.jpg', 'pro-20.jpg', 'pro-14.jpg', 'pro-15.jpg', 'pro-16.jpg', 'pro-17.jpg', 'pro-21.jpg');
                                    ?>
                                    <section class="asl-pro-showcase">
                                        <div class="asl-pro-showcase__hero">
                                            <div class="asl-pro-showcase__hero-copy">
                                                <span class="asl-pro-showcase__eyebrow"><span class="dashicons dashicons-star-filled" aria-hidden="true"></span><?php esc_html_e('Agile Store Locator Pro', 'asl_locator'); ?></span>
                                                <h2><?php esc_html_e('Unlock more ways to build, manage, and grow your store locator', 'asl_locator'); ?></h2>
                                                <p><?php esc_html_e('Upgrade without losing your stores or settings. Pro adds advanced layouts, management tools, analytics, integrations, and more.', 'asl_locator'); ?></p>
                                                <div class="asl-pro-showcase__actions">
                                                    <a class="btn btn-primary" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($asl_upgrade_url); ?>"><?php esc_html_e('Explore Pro', 'asl_locator'); ?><span aria-hidden="true">→</span></a>
                                                    <span><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><?php esc_html_e('Your existing data stays intact', 'asl_locator'); ?></span>
                                                </div>
                                            </div>
                                            <div class="asl-pro-showcase__hero-badge" aria-hidden="true"><span class="dashicons dashicons-location-alt"></span><strong>PRO</strong></div>
                                        </div>

                                        <div class="asl-pro-showcase__heading">
                                            <div><span><?php esc_html_e('Feature preview', 'asl_locator'); ?></span><h3><?php esc_html_e('See what you can do with Pro', 'asl_locator'); ?></h3></div>
                                            <p><?php esc_html_e('A closer look at the additional tools and experiences available in the full version.', 'asl_locator'); ?></p>
                                        </div>
                                        <div class="asl-pro-showcase__grid">
                                            <?php foreach ($asl_pro_images as $asl_pro_image) : ?>
                                                <a class="asl-pro-showcase__card" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($asl_upgrade_url); ?>">
                                                    <img loading="lazy" src="<?php echo esc_url('https://cdn.agilestorelocator.com/pro/' . $asl_pro_image); ?>" alt="<?php esc_attr_e('Agile Store Locator Pro feature preview', 'asl_locator'); ?>">
                                                    <span><?php esc_html_e('Available in Pro', 'asl_locator'); ?><span aria-hidden="true">↗</span></span>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>

                                        <aside class="asl-pro-showcase__sync">
                                            <div class="asl-pro-showcase__sync-copy">
                                                <span><?php esc_html_e('Optional integration add-on', 'asl_locator'); ?></span>
                                                <h3><?php esc_html_e('Connect Salesforce, Google Sheets, Smartsheet, and REST APIs', 'asl_locator'); ?></h3>
                                                <p><?php esc_html_e('Agile Sync automates imports, maps custom fields, and helps keep store data up to date.', 'asl_locator'); ?></p>
                                                <a class="btn btn-outline-primary" target="_blank" rel="noopener noreferrer" href="https://agilestorelocator.com/agile-sync-addon/?utm_source=wordpress-org&amp;utm_medium=plugin&amp;utm_campaign=free-version&amp;utm_content=agile-sync-addon"><?php esc_html_e('Learn about Agile Sync', 'asl_locator'); ?><span aria-hidden="true">→</span></a>
                                            </div>
                                            <img loading="lazy" src="<?php echo esc_url('https://cdn.agilestorelocator.com/asl-wc/agile-sync-addon-banner.png'); ?>" alt="<?php esc_attr_e('Agile Sync integration preview', 'asl_locator'); ?>">
                                        </aside>
                                    </section>
                                </div>
                                <?php if(!defined ( 'ASL_WC_VERSION' )) {
                           include ASL_PLUGIN_PATH.'admin/partials/asl-wc-ads.php';
                           } 
                        ?>
                                <div id="sl-store-form" class="tab-pane">
                                    <?php include ASL_PLUGIN_PATH.'admin/partials/store-form-tab.php'; ?>
                                </div>
                                <!-- ASL Labels Stat-->
                                <div id="sl-labels" class="tab-pane">
                                    <?php include ASL_PLUGIN_PATH.'admin/partials/labels.php'; ?>
                                </div>
                                <!-- ASL Labels End-->
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="button"
                                        class="btn btn-success text-white float-end btn-asl-user_setting asl-btn-setting-main"
                                        data-loading-text="<?php echo esc_attr__('Saving...','asl_locator') ?>"
                                        data-completed-text="Settings Updated"><?php echo esc_attr__('Save Settings','asl_locator') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row sl-complx">
            <div class="col-md-12">
                <section class="asl-pro-locked-section asl-pro-locked-panel asl-settings-pro-lock" aria-labelledby="asl-json-cache-lock-title">
                    <div class="asl-pro-lock-overlay">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        <strong id="asl-json-cache-lock-title"><?php esc_html_e('JSON Cache is a Pro feature', 'asl_locator'); ?></strong>
                        <span><?php esc_html_e('Upgrade to manage and refresh the high-performance store cache.', 'asl_locator'); ?></span>
                        <a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a>
                    </div>
                    <div class="asl-pro-locked-preview" aria-hidden="true">
                <div class="card p-0 mb-4">
                    <h3 class="card-title asl-settings-section-title">
                        <span class="asl-settings-section-title__icon" aria-hidden="true"><svg><use href="#asl-admin-icon-cache"></use></svg></span>
                        <span><?php echo esc_html__('Manage JSON Cache','asl_locator') ?></span>
                    </h3>
                    <div class="card-body">
                        <h3 class="alert alert-warning" style="width:100%;font-size: 14px"><span
                                style="margin-right: 10px"><?php echo esc_attr__('Warning! JSON cache loading preloads all the data to serve it with great speed, but everytime you make any changes you have to hit the "Refresh Cache" button, if your JSON file is cached by browser or CDN such as cloudflare change the Query Parameter value.','asl_locator') ?>
                            </span></h3>
                        <div class="row">
                            <div class="col-12">
                                <form id="frm-asl-cache">
                                    <table class="table table-striped">
                                        <thead class="thead-primary">
                                            <tr>
                                                <th scope="col"><?php echo esc_attr__('Lang','asl_locator') ?></th>
                                                <th scope="col"><?php echo esc_attr__('Cache','asl_locator') ?></th>
                                                <th scope="col"><?php echo esc_attr__('Version','asl_locator') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                    foreach ($active_langs as $lang): ?>
                                            <tr>
                                                <td><?php echo esc_attr($lang) ?></td>
                                                <td>
                                                    <div class="a-swith">
                                                        <input value="1"
                                                            <?php if(isset($cache_settings[$lang]) && $cache_settings[$lang] == '1') echo 'checked' ?>
                                                            name="<?php echo esc_attr($lang) ?>"
                                                            id="asl-fast-cache-<?php echo esc_attr($lang) ?>"
                                                            data-lang="<?php echo esc_attr($lang) ?>"
                                                            class="cmn-toggle cmn-toggle-round" type="checkbox">
                                                        <label
                                                            for="asl-fast-cache-<?php echo esc_attr($lang) ?>"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <div class="input-group">
                                                            <input name="<?php echo esc_attr($lang) ?>-ver"
                                                                value="<?php if(isset($cache_settings[$lang.'-ver'])) echo esc_attr($cache_settings[$lang.'-ver']); else echo '1'; ?>"
                                                                id="asl-cache-ver-<?php echo esc_attr__($lang) ?>"
                                                                type="number"
                                                                style="max-width: 200px;min-height: 34px;height: 34px;"
                                                                class="form-control"
                                                                placeholder="<?php echo esc_attr__('Query Parameter','asl_locator') ?>"
                                                                aria-label="<?php echo esc_attr__('Query Parameter','asl_locator') ?>">
                                                            <div class="input-group-append">
                                                                <button data-lang="<?php echo esc_attr($lang) ?>"
                                                                    type="button"
                                                                    data-loading-text="<?php echo esc_attr__('Refreshing...','asl_locator') ?>"
                                                                    class="btn btn-primary sl-refresh-cache"><?php echo esc_attr__('Refresh Cache','asl_locator') ?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="row asl-inner-cont ">
            <div class="col-md-12 sl-complx">
                <div class="card p-0 mb-4">
                    <h3 class="card-title asl-settings-section-title">
                        <span class="asl-settings-section-title__icon" aria-hidden="true"><svg><use href="#asl-admin-icon-fields"></use></svg></span>
                        <span><?php echo esc_html__('Manage Additional Fields','asl_locator') ?></span>
                    </h3>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p><?php echo esc_attr__('Create extra fields for your stores and choose which tab they appear in. These fields are also available through CSV import and export.','asl_locator') ?>
                                    <?php echo esc_attr__('To show the additional fields on the template, please add the fields in the template as in this ','asl_locator') ?><a
                                        target="_blank"
                                        href="https://www.youtube.com/watch?v=WpPUMxlNX4M"><?php echo esc_attr__('Video Guide','asl_locator') ?></a>
                                </p>
                                <div class="alert alert-primary" role="status">
                                    <div class="asl-alert-content">
                                        <?php echo wp_kses_post(__('The <b>Internal Field Name</b> is generated automatically for new fields. Open <b>Advanced settings</b> only when you need to review it or add a CSS class.','asl_locator')) ?>
                                    </div>
                                </div>
                                <form id="frm-asl-custom-fields">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-stripped asl-attr-manage">
                                            <thead>
                                                <tr>
                                                    <th><?php echo esc_html__('Field Label','asl_locator') ?></th>
                                                    <th><?php echo esc_html__('Field Type','asl_locator') ?></th>
                                                    <th><?php echo esc_html__('Show Field In','asl_locator') ?></th>
                                                    <th><?php echo esc_html__('Required','asl_locator') ?></th>
                                                    <th><?php echo esc_html__('Actions','asl_locator') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 

                                       $field_types = [
                                          'text'      => esc_attr__('Text', 'asl_locator'),
                                          'textarea'  => esc_attr__('Textarea', 'asl_locator'),
                                          'richtext'   => esc_attr__('Rich Textarea', 'asl_locator'),
                                          'dropdown'  => esc_attr__('Dropdown', 'asl_locator'),
                                          'radio'     => esc_attr__('Radio List', 'asl_locator'),
                                          'checkbox'  => esc_attr__('Checkbox', 'asl_locator'),
                                          'gallery'    => esc_attr__('Gallery', 'asl_locator'),
                                          'page_link'  => esc_attr__('Internal Page Link', 'asl_locator')
                                       ];

                                       $field_index = 0;

                                       foreach($fields as $field): 
                                          
                                          $field_index++;
                                       	$field_name      = strip_tags($field['name']);
                                          $field_option    = isset($field['options'])? strip_tags($field['options']): '';
                                          $field_type      = strip_tags($field['type']);
                              				$field_label     = strip_tags($field['label']);
                              				$css_class 	     = isset($field['css_class'])? strip_tags($field['css_class']): '';
                                          $field_require   = (isset($field['require']) && $field['require'])? true: false;
                                          $field_section   = isset($field['section']) && $field['section'] === 'address' ? 'address' : 'other';

                                       	?>
                                                <tr class="asl-custom-field-row">
                                                    <td>
                                                        <div class="form-group mb-2"><input
                                                                value="<?php echo esc_attr__($field_label); ?>"
                                                                type="text"
                                                                aria-label="<?php echo esc_attr__('Field Label', 'asl_locator'); ?>"
                                                                class="asl-attr-label form-control validate[required,funcCall[ASLValidateLabel]]">
                                                        </div>
                                                        <div class="asl-field-choices <?php echo in_array($field_type, ['dropdown', 'radio'], true) ? '' : 'd-none'; ?> mb-2">
                                                            <label class="small font-weight-bold"><?php echo esc_html__('Choices', 'asl_locator'); ?></label>
                                                            <input value="<?php echo esc_attr__($field_option); ?>" type="text"
                                                                placeholder="<?php echo esc_attr__('Example: Small, Medium, Large', 'asl_locator'); ?>"
                                                                class="asl-attr-options form-control validate[funcCall[ASLValidateOptions]]">
                                                            <small class="form-text text-muted"><?php echo esc_html__('Separate each choice with a comma.', 'asl_locator'); ?></small>
                                                        </div>
                                                        <details class="asl-field-advanced">
                                                            <summary><?php echo esc_html__('Advanced settings', 'asl_locator'); ?></summary>
                                                            <div class="form-group mt-2 mb-2">
                                                                <label class="small font-weight-bold"><?php echo esc_html__('Internal Field Name', 'asl_locator'); ?></label>
                                                                <input value="<?php echo esc_attr__($field_name); ?>" type="text" data-auto-name="0"
                                                                    class="asl-attr-name form-control validate[required,funcCall[ASLValidateName]]">
                                                                <small class="form-text text-muted"><?php echo esc_html__('Changing this name can disconnect previously saved values.', 'asl_locator'); ?></small>
                                                            </div>
                                                            <div class="form-group mb-2">
                                                                <label class="small font-weight-bold"><?php echo esc_html__('CSS Class', 'asl_locator'); ?></label>
                                                                <input maxlength="50" value="<?php echo esc_attr__($css_class); ?>" type="text" class="asl-attr-class form-control">
                                                            </div>
                                                        </details>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control asl-attr-type">
                                                                <?php
                                                   foreach ($field_types as $value => $label) {
                                                      $selected = ($field_type === $value) ? 'selected' : '';
                                                      echo "<option value='".esc_attr__($value)."' $selected>".esc_attr__($label)."</option>";
                                                   }
                                                ?>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control asl-attr-section">
                                                                <option value="other" <?php selected($field_section, 'other'); ?>><?php echo esc_html__('Other Details tab', 'asl_locator'); ?></option>
                                                                <option value="address" <?php selected($field_section, 'address'); ?>><?php echo esc_html__('Store Address tab', 'asl_locator'); ?></option>
                                                            </select>
                                                            <small class="form-text text-muted"><?php echo esc_html__('Choose where this field appears when editing a store.', 'asl_locator'); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group-inner mt-2 d-flex align-items-center">
                                                            <label class="switch"
                                                                for="asl-cf-req-<?php echo $field_index ?>"><input
                                                                    type="checkbox"
                                                                    <?php if($field_require) echo 'checked' ?> value="1"
                                                                    class="asl-attr-require custom-control-input"
                                                                    id="asl-cf-req-<?php echo $field_index ?>"><span
                                                                    class="slider round"></span></label>
                                                            <span class="asl-required-status ml-2"><?php echo $field_require ? esc_html__('Yes', 'asl_locator') : esc_html__('No', 'asl_locator'); ?></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-link text-danger add-k-delete glyp-trash" title="<?php echo esc_attr__('Remove field', 'asl_locator'); ?>">
                                                            <svg width="16" height="16">
                                                                <use xlink:href="#i-trash"></use>
                                                            </svg>
                                                            <span><?php echo esc_html__('Remove', 'asl_locator'); ?></span>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-dark mrg-r-10 mt-3 float-left"
                                    id="btn-asl-add-field">
                                    <i>
                                        <svg style="margin-top:-3px;" width="13" height="13">
                                            <use xlink:href="#i-plus"></use>
                                        </svg>
                                    </i>
                                    <?php echo esc_html__('Add Custom Field','asl_locator') ?>
                                </button>
                                <button type="button" class="btn btn-success mt-3 float-right"
                                    data-loading-text="<?php echo esc_attr__('Saving...','asl_locator') ?>"
                                    data-completed-text="Fields Updated"
                                    id="btn-asl-save-schema"><?php echo esc_attr__('Save Fields','asl_locator') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            include ASL_PLUGIN_PATH.'admin/partials/asl-faq.php';
            ?>
        </div>
    </div>
    <!-- Map Modal -->
    <div class="smodal fade" id="asl-map-modal" role="dialog">
        <div class="smodal-dialog" role="document">
            <div class="smodal-content">
                <div class="smodal-header">
                    <h5 class="smodal-title"><?php echo esc_attr__('Set Coordinates & Zoom','asl_locator') ?></h5>
                    <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="smodal-body">
                    <p><b><?php echo esc_attr__('Set your store locator default coordinates and zoom level using the marker below, drag the marker to pinpoint.','asl_locator') ?></b>
                    </p>
                    <div class="row">
                        <div class="col-12">
                        </div>
                        <div class="col-12 mb-2">
                            <input id="asl-setting-search-box" type="text" class="form-control"
                                placeholder="<?php echo esc_attr__('Search Location','asl_locator') ?>">
                        </div>
                        <div class="col-12">
                            <div class="map_canvas" style="height:300px" id="map_canvas"></div>
                        </div>
                        <div class="col-12">
                            <button id="asl-setting-set-coordinates" class="btn btn-dark btn-block mt-2"
                                type="button"><?php echo esc_attr__('Use Default Location & Zoom','asl_locator') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
        url: '<?php echo ASL_UPLOAD_URL ?>',
        plugin_url: '<?php echo ASL_URL_PATH ?>',
        tmpls: <?php echo wp_json_encode($cust_tmpls) ?>
    },
    asl_configs = <?php echo wp_json_encode($all_configs); ?>;

window.addEventListener("load", function() {
    asl_engine.pages.user_setting(asl_configs);
});
</script>
