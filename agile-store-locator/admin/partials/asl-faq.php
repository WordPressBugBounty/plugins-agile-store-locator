<?php

$asl_wc_ad_url = 'https://agilestorelocator.com/multistore-woocommerce-addons/?utm_source=WordPress&utm_medium=Banner&utm_campaign=WP.org&utm_term=ASL&utm_content=';
$asl_sync_ad_url = 'https://agilestorelocator.com/agile-sync-addon/?utm_source=wordpress&utm_medium=asl-settings&utm_campaign=agile-sync-addon';

$faq_basic = array(
  array(
    'q'   => __('How do I add the store locator to my website?', 'asl_locator'),
    'ans' => __('Create or edit a WordPress page, add the <b>[ASL_STORELOCATOR]</b> shortcode, and publish the page. Your store locator will appear where the shortcode is placed.', 'asl_locator'),
  ),
  array(
    'q'   => __('Can I use the store locator without paying for Google Maps?', 'asl_locator'),
    'ans' => __('Yes. Open <b>ASL Settings → Maps</b> and select the free map option. It can display your stores without a Google Maps account. Some address-search features may still require a separate free or paid search service.', 'asl_locator'),
  ),
  array(
    'q'   => __('Which map option should I choose?', 'asl_locator'),
    'ans' => __('<b>Google Maps</b> is a familiar choice with Google search features, but it normally requires an API key and billing account. Choose the <b>free map option</b> when you want to display stores without relying on Google Maps.', 'asl_locator'),
  ),
  array(
    'q'   => __('How do I add my first store?', 'asl_locator'),
    'ans' => __('Go to <b>Manage Stores → Add New Store</b>, enter the store name and address, confirm its location, and save it. You can also assign a category, marker, logo, opening hours, and contact details.', 'asl_locator'),
  ),
  array(
    'q'   => __('Why is my store not appearing on the map?', 'asl_locator'),
    'ans' => __('Check that the store is enabled and has valid latitude and longitude values. If cache is enabled, refresh the JSON cache after saving your changes.', 'asl_locator'),
  ),
  array(
    'q'   => __('How can visitors search for a nearby store?', 'asl_locator'),
    'ans' => __('Enable location or address search under <b>ASL Settings</b>. Visitors can then enter an address, city, or postal code to find the closest stores.', 'asl_locator'),
  ),
  array(
    'q'   => __('How do I change the map’s starting location?', 'asl_locator'),
    'ans' => __('Open <b>ASL Settings → Maps</b>, set the default location and zoom level, then save your settings. This is the area visitors see before starting a search.', 'asl_locator'),
  ),
  array(
    'q'   => __('How do I change store markers, logos, and categories?', 'asl_locator'),
    'ans' => __('Use <b>Manage Markers</b>, <b>Manage Logos</b>, and <b>Manage Categories</b> from the Store Locator menu. After creating them, assign them to stores from the store editor.', 'asl_locator'),
  ),
  array(
    'q'   => __('Can I synchronize stores with Google Sheets?', 'asl_locator'),
    'ans' => __('Yes, with the separate <a href="https://agilestorelocator.com/agile-sync-addon/" target="_blank" rel="noopener noreferrer">Agile Sync Addon</a>. It automatically synchronizes store information between Google Sheets and Agile Store Locator.', 'asl_locator'),
  ),
  array(
    'q'   => __('Why are my latest changes not appearing?', 'asl_locator'),
    'ans' => __('Save the changed store or setting, then refresh the JSON cache if it is enabled. You may also need to clear your browser, WordPress, or CDN cache.', 'asl_locator'),
  ),
  array(
    'q'   => __('How do I change the colors and design?', 'asl_locator'),
    'ans' => __('Use the <b>Customizer</b> section in ASL Settings to change colors, fonts, labels, and templates. Preview your changes before showing them to visitors.', 'asl_locator'),
  ),
  array(
    'q'   => __('Can I display different stores on different pages?', 'asl_locator'),
    'ans' => __('Yes. Shortcode options let you filter the locator by categories, stores, or other values, so each page can display a different group of locations.', 'asl_locator'),
  ),
  array(
    'q'   => __('Can customers select a store during WooCommerce checkout?', 'asl_locator'),
    'ans' => __('Yes, with the separate <a href="https://agilelogix.com/product/multi-store-addons-for-woocommerce/" target="_blank" rel="noopener noreferrer">Multi Store Addon for WooCommerce</a>. It adds store selection, local pickup, location-based inventory, and distance-rate shipping.', 'asl_locator'),
  ),
  array(
    'q'   => __('What should I do if the map does not load?', 'asl_locator'),
    'ans' => __('First confirm that the selected map option is configured correctly. For Google Maps, check the API key, billing status, allowed website address, and required APIs. You can also switch to the free map option under <b>ASL Settings → Maps</b>.', 'asl_locator'),
  ),
  array(
    'q'   => __('How do I contact support?', 'asl_locator'),
    'ans' => __('Email <a href="mailto:support@agilelogix.com">support@agilelogix.com</a>. Include your WordPress version, Store Locator version, map option, page URL, and a screenshot or exact error message.', 'asl_locator'),
  ),
);

$faq_links = array(
  array(
    'title' => 'How to translate the static content of the plugin?',
    'link'  => 'https://agilestorelocator.com/wiki/language-translation-store-locator/'
  ),
  array(
    'title' => 'How can we avoid the template to be overwrite by updates?',
    'link'  => 'https://agilestorelocator.com/wiki/customize-template-without-modifying-core-plugin/'
  ),
  array(
    'title' => 'How can we pre-load filter values by the URL?',
    'link'  => 'https://agilestorelocator.com/wiki/load-parameter-with-query-string/'
  ),
  array(
    'title' => 'How to create multiple Store Locator on different pages?',
    'link'  => 'https://agilestorelocator.com/wiki/create-multiple-store-locator-different-wordpress-pages/'
  ),
  array(
    'title' => 'How can we sort by the categories?',
    'link'  => 'https://agilestorelocator.com/wiki/sort-store-attribute/'
  ),
  array(
    'title' => 'How can we change the address format?',
    'link'  => 'https://agilestorelocator.com/wiki/change-address-format/'
  ),
  array(
    'title' => 'How to change the user location marker?',
    'link'  => 'https://agilestorelocator.com/wiki/change-user-location-marker-image/'
  ),
  array(
    'title' => 'How to add custom tag in the template?',
    'link'  => 'https://agilestorelocator.com/wiki/custom-script-method-store-locator/'
  ),
  array(
    'title' => 'Why Store Locator doesn’t appear at all?',
    'link'  => 'https://agilestorelocator.com/wiki/store-locator-doesnot-appear/'
  ),
  array(
    'title' => 'How to change the cluster color or size?',
    'link'  => 'https://agilestorelocator.com/wiki/store-locator-clusters/'
  ),
  array(
    'title' => 'How to change the font sizing?',
    'link'  => 'https://agilestorelocator.com/wiki/how-to-adjust-the-font-size/'
  ),
  array(
    'title' => 'How to change the "Website" text?',
    'link'  => 'https://agilestorelocator.com/wiki/language-translation-store-locator/'
  )
);

$faq_videos = array(
  array(
    'id'    => 'gJWVJsUOasg',
    'title' => __('Create a Google Maps API Key in Just 5 Minutes', 'asl_locator'),
  ),
  array(
    'id'    => 'SEVrxMzH4N4',
    'title' => __('Customize Google Maps with Advanced Markers', 'asl_locator'),
  ),
  array(
    'id'    => 'g_nSnEYqQ3U',
    'title' => __('Sync Store Data Automatically with Google Sheets', 'asl_locator'),
  ),
  array(
    'id'    => 'phlknhbPeHE',
    'title' => __('Manage WooCommerce Inventory Across Multiple Stores', 'asl_locator'),
  ),
);

?>

<div class="row asl-setting-cont mx-auto px-0">
        <div class="col-md-12">
          <div class="asl-seting-faq p-0 mb-4 mt-0">
             <h3 class="card-title asl-settings-section-title">
                <span class="asl-settings-section-title__icon" aria-hidden="true"><svg><use href="#asl-admin-icon-help"></use></svg></span>
                <span><?php echo esc_html__('FAQ & Help','asl_locator') ?></span>
             </h3>
             <div class="asl-seting-body">
                <div class="alert border-0 alert-primary d-flex py-3 mb-4" role="alert">
                   <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                      <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                   </svg>
                   <p class="m-0"><?php echo esc_attr__('Create a support ticket by emailing us at ','asl_locator') ?> <a target="_blank" href="mailto:support@agilelogix.com" class="text-decoration-underline"><?php echo esc_attr__('support@agilelogix.com','asl_locator') ?></a>, <?php echo esc_attr__('we will get back to you as soon as possible, please include ("Store Locator" in the Subject) to avoid the spam list.','asl_locator') ?></p>
                </div>
                <!-- Accordian -->
                <div class="faqs-accordion" id="accordionfaqs">
                  <?php foreach ($faq_basic as $key => $faq): ?>
                   <div class="cards p-0">
                      <div class="card-header py-3 px-2">
                         <h2 class="mb-0 d-flex align-items-center">
                            <span><?php echo esc_html(str_pad((string) ($key + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#asl-faq-collapse-<?php echo esc_attr($key); ?>" aria-expanded="false" aria-controls="asl-faq-collapse-<?php echo esc_attr($key); ?>">
                            <?php echo esc_html($faq['q']); ?>
                            </button>
                         </h2>
                      </div>
                      <div id="asl-faq-collapse-<?php echo esc_attr($key); ?>" class="collapse" data-parent="#accordionfaqs">
                         <div class="card-body">
                            <p><?php echo wp_kses_post($faq['ans']); ?></p>
                         </div>
                      </div>
                   </div>
                  <?php endforeach; ?>
                </div>
                <!-- Accordian -->
                <!-- FAQ Videos -->
                <section class="asl-video-sec" aria-labelledby="asl-faq-videos-title">
                   <div class="top-title mt-5 mb-3 d-flex align-items-center justify-content-between">
                      <div>
                        <b id="asl-faq-videos-title"><?php echo esc_html__('FAQ Videos','asl_locator') ?></b>
                        <p><?php echo esc_html__('Short guides to help you configure and manage your store locator.', 'asl_locator') ?></p>
                      </div>
                      <a target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/channel/UCtr44_UG4DoxcEAhzWepYJw/videos" class="d-flex align-items-center"><?php echo esc_html__('See All Videos','asl_locator') ?> <span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span></a>
                   </div>
                   <div class="row g-3 asl-video-grid">
                      <?php foreach ($faq_videos as $faq_video) : ?>
                        <div class="col-xl-3 col-md-6 col-12">
                          <a class="asl-video-card" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url('https://www.youtube.com/watch?v=' . $faq_video['id']); ?>">
                            <span class="asl-video-card__media">
                              <img loading="lazy" src="<?php echo esc_url('https://i.ytimg.com/vi/' . $faq_video['id'] . '/hqdefault.jpg'); ?>" alt="">
                              <span class="asl-video-card__play" aria-hidden="true"><span></span></span>
                            </span>
                            <span class="asl-video-card__body">
                              <strong><?php echo esc_html($faq_video['title']); ?></strong>
                              <span><?php echo esc_html__('Watch tutorial', 'asl_locator'); ?><span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span></span>
                            </span>
                          </a>
                        </div>
                      <?php endforeach; ?>
                   </div>
                </section>
                <!-- FAQ Videos End -->
                <!-- FAQ'S Links -->
                <div class="asl-faq-link mt-5">
                   <div class="top-title text-center">
                      <b><?php echo esc_attr__('FAQ Links','asl_locator') ?></b>
                   </div>
                   <div class="row faq-slider">
                      <?php foreach ($faq_links as $key => $faq): ?>
                      <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                         <a target="_blank" class="asl-faq-inner-box card" href="<?php echo $faq['link'] ?>">
                            <div class="row align-items-center">
                               <div class="col-9">
                                  <span class="link-title"><?php echo $faq['title'] ?></span>
                               </div>
                               <div class="col-3">
                                  <span class="dashicons dashicons-admin-links"></span>
                               </div>
                            </div>
                         </a>
                      </div>
                      <?php endforeach; ?>
                   </div>
                </div>
                <!-- FAQ'S Links -->
                <div class="row">
                   <div class="col-md-12">
                      <div class="alert alert-light asl-rating-alert" role="alert">
                         <p class="text-muted"><?php echo esc_attr__('If you have any problem with the plugin or suggestion, please email us at','asl_locator') ?> <a  href="mailto:support@agilelogix.com"><?php echo esc_attr__('support@agilelogix.com','asl_locator') ?></a> <?php echo esc_attr__('We will respond as soon as possible to resolve your problem, please include ("Store Locator" in the Subject) to avoid the spam list.','asl_locator') ?></p>
                         <div class="d-flex align-items-center">
                            <a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/agile-store-locator/reviews/#new-post"><?php echo esc_html__('If you like our Plugin, please rate us 5 stars.','asl_locator') ?></a>
                            <ul class="reviews-stars d-flex p-0 ml-2 mb-0">
                               <li class="mb-0"><span class="dashicons dashicons-star-filled"></span></li>
                               <li class="mb-0"><span class="dashicons dashicons-star-filled"></span></li>
                               <li class="mb-0"><span class="dashicons dashicons-star-filled"></span></li>
                               <li class="mb-0"><span class="dashicons dashicons-star-filled"></span></li>
                               <li class="mb-0"><span class="dashicons dashicons-star-filled"></span></li>
                            </ul>
                         </div>
                      </div>
                   </div>
                </div>
                <!-- Agile Sync Addon -->
                <aside class="asl-sync-addon-image-banner" aria-label="<?php echo esc_attr__('Agile Sync Addon', 'asl_locator'); ?>">
                  <a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($asl_sync_ad_url); ?>">
                    <img
                      loading="lazy"
                      decoding="async"
                      width="1774"
                      height="887"
                      src="https://cdn.agilestorelocator.com/asl-wc/agile-sync-addon-banner.png"
                      alt="<?php echo esc_attr__('Agile Sync Addon: automatically synchronize Agile Store Locator with Google Sheets, Salesforce, Smartsheet, and REST APIs.', 'asl_locator'); ?>">
                  </a>
                </aside>
                <!-- Agile Sync Addon End -->
             </div>
          </div>
        </div>
      </div>
