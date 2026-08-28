<?php

$has_coordinates = isset($store_data->lat, $store_data->lng)
    && $store_data->lat !== ''
    && $store_data->lng !== '';
$display_address = isset($store_data->display_address)
    ? $store_data->display_address
    : $store_data->address;
$has_direction = ! empty($store_data->address) || (isset($atts['coords_direction']) && $has_coordinates);
$address = urlencode($store_data->address);

if (isset($atts['coords_direction']) && $has_coordinates) {
    $address = urlencode(trim($store_data->lat . ',' . $store_data->lng));
}

$direction_url = $has_direction ? 'https://www.google.com/maps/dir/?api=1&destination=' . $address : '';
$locations_url = ! empty($atts['locations_url']) ? $atts['locations_url'] : '';
$gallery_images = (isset($store_data->gallery_images) && is_array($store_data->gallery_images))
    ? $store_data->gallery_images
    : [];
$descriptions = array_filter([
    isset($store_data->description) ? $store_data->description : '',
    isset($store_data->description_2) ? $store_data->description_2 : '',
]);
$show_descriptions = ! empty($descriptions)
    && isset($all_configs['additional_info'])
    && $all_configs['additional_info'] == '1';
$show_business_hours = ! empty($store_data->open_hours)
    && (! isset($all_configs['hide_hours']) || $all_configs['hide_hours'] != '1');
$category_items = isset($store_data->categories) && is_iterable($store_data->categories)
    ? $store_data->categories
    : [];
$show_categories = ! empty($category_items)
    && isset($all_configs['show_categories'])
    && $all_configs['show_categories'] == '1';
$has_contact_details = ! empty($display_address)
    || ! empty($store_data->phone)
    || ! empty($store_data->email)
    || ! empty($store_data->open_hours);
$has_rating_summary = ! empty($store_data->rating) || ! empty($store_data->review);
$current_open_status = ! empty($store_data->hours)
    ? \AgileStoreLocator\Helper::currentOpenStatus($store_data->hours)
    : null;
$full_open_hours = ! empty($store_data->open_hours) ? $store_data->open_hours : '';
$page_color_defaults = [
    'primary'          => '#46ac1b',
    'header'           => '#ecf6e8',
    'header-color'     => '#32373c',
    'infobox-color'    => '#555d66',
    'infobox-bg'       => '#FFFFFF',
    'infobox-a'        => '#46ac1b',
    'action-btn-color' => '#FFFFFF',
    'action-btn-bg'    => '#46ac1b',
    'color'            => '#555d66',
    'list-bg'          => '#FFFFFF',
    'list-title'       => '#32373c',
    'list-sub-title'   => '#6a6a6a',
    'highlighted'      => '#F7F7F7',
];
$page_colors = \AgileStoreLocator\Helper::get_ui_template_colors(0, $page_color_defaults);
$page_color_styles = [];

foreach ($page_colors as $color_key => $color_value) {
    $page_color_styles[] = '--sl-' . $color_key . ':' . $color_value;
}

if ($full_open_hours) {
    $day_labels = [
        'sun' => 'store_page_sunday',
        'mon' => 'store_page_monday',
        'tue' => 'store_page_tuesday',
        'wed' => 'store_page_wednesday',
        'thu' => 'store_page_thursday',
        'fri' => 'store_page_friday',
        'sat' => 'store_page_saturday',
    ];

    foreach ($day_labels as $short_day => $full_day) {
        $full_open_hours = str_replace(
            '>' . asl_esc_lbl($short_day) . '<',
            '>' . asl_esc_lbl($full_day) . '<',
            $full_open_hours
        );
    }

    $full_open_hours = str_replace(
        '<span class="sl-time">' . asl_esc_lbl('closed') . '</span>',
        '<span class="sl-time asl-time-closed">' . asl_esc_lbl('closed') . '</span>',
        $full_open_hours
    );
}
?>

<section
    class="asl-cont asl-store-pg"
    data-config='<?php echo esc_attr(wp_json_encode($all_configs)); ?>'
    style="<?php echo esc_attr(implode(';', $page_color_styles)); ?>"
>
    <div class="sl-container">
        <?php if ($locations_url) : ?>
            <nav class="asl-store-breadcrumb" aria-label="<?php echo esc_attr(asl_esc_lbl('store_page_breadcrumb')); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php echo asl_esc_lbl('store_page_home'); ?></a>
                <span aria-hidden="true">›</span>
                <a href="<?php echo esc_url($locations_url); ?>"><?php echo asl_esc_lbl('store_page_locations'); ?></a>
                <span aria-hidden="true">›</span>
                <span aria-current="page"><?php echo esc_html($store_data->title); ?></span>
            </nav>
        <?php endif; ?>

        <div class="asl-store-hero">
            <div class="asl-store-summary">
                <div class="asl-store-heading">
                    <?php if (! empty($store_data->path)) : ?>
                        <img
                            class="asl-store-logo"
                            src="<?php echo esc_url(ASL_UPLOAD_URL . 'Logo/' . $store_data->path); ?>"
                            alt="<?php echo esc_attr($store_data->title); ?>"
                        >
                    <?php endif; ?>

                    <div class="asl-store-heading-copy<?php echo ! $has_rating_summary ? ' asl-store-heading-copy-no-rating' : ''; ?>">
                        <div class="asl-store-title-row">
                            <h1 class="sl-store-title"><?php echo esc_html($store_data->title); ?></h1>
                            <?php if (! empty($store_data->verified)) : ?>
                                <span class="asl-verified">
                                    <span aria-hidden="true">✓</span>
                                    <?php echo asl_esc_lbl('store_page_verified'); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($has_rating_summary) : ?>
                            <div class="asl-store-rating">
                                <?php if (! empty($store_data->rating)) : ?>
                                    <?php $rating_width = min(100, max(0, (float) $store_data->rating * 20)); ?>
                                    <span class="asl-rating-stars" aria-label="<?php echo esc_attr(sprintf(asl_esc_lbl('store_page_rating'), $store_data->rating)); ?>">
                                        <span class="asl-rating-stars-fill" style="width: <?php echo esc_attr($rating_width); ?>%">★★★★★</span>
                                        <span class="asl-rating-stars-empty" aria-hidden="true">★★★★★</span>
                                    </span>
                                    <span class="asl-rating-value"><?php echo esc_html($store_data->rating); ?></span>
                                <?php endif; ?>
                                <?php if (! empty($store_data->review)) : ?>
                                    <span class="asl-review-count">
                                        (<?php echo esc_html($store_data->review); ?> <?php echo (int) $store_data->review === 1 ? asl_esc_lbl('store_page_review') : asl_esc_lbl('store_page_reviews'); ?>)
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($has_contact_details) : ?>
                    <ul class="asl-store-contact">
                        <?php if (! empty($display_address)) : ?>
                            <li><i class="icon-location" aria-hidden="true"></i><span><?php echo esc_html($display_address); ?></span></li>
                        <?php endif; ?>
                        <?php if (! empty($store_data->phone)) : ?>
                            <li><i class="icon-mobile-1" aria-hidden="true"></i><a href="tel:<?php echo esc_attr($store_data->phone); ?>"><?php echo esc_html($store_data->phone); ?></a></li>
                        <?php endif; ?>
                        <?php if (! empty($store_data->email)) : ?>
                            <li><i class="icon-mail" aria-hidden="true"></i><a href="mailto:<?php echo esc_attr($store_data->email); ?>"><?php echo esc_html($store_data->email); ?></a></li>
                        <?php endif; ?>
                        <?php if (! empty($store_data->open_hours)) : ?>
                            <li class="asl-store-hours-summary">
                                <i class="icon-clock" aria-hidden="true"></i>
                                <?php if ($current_open_status !== null) : ?>
                                    <div class="asl-current-hours">
                                        <span class="asl-current-status <?php echo $current_open_status['is_open'] ? 'is-open' : 'is-closed'; ?>">
                                            <?php echo $current_open_status['is_open'] ? asl_esc_lbl('opened') : asl_esc_lbl('closed'); ?>
                                        </span>
                                        <?php if (! empty($current_open_status['hours'])) : ?>
                                            <span class="asl-current-time"><?php echo esc_html(implode(', ', $current_open_status['hours'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>

                <?php if (! empty($direction_url) || ! empty($store_data->phone) || ! empty($store_data->website)) : ?>
                    <div class="asl-store-actions">
                        <?php if (! empty($direction_url)) : ?>
                            <a href="<?php echo esc_url($direction_url); ?>" target="_blank" rel="noopener" class="asl-store-btn asl-store-btn-primary">
                                <i class="icon-direction" aria-hidden="true"></i><?php echo asl_esc_lbl('store_page_directions'); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (! empty($store_data->phone)) : ?>
                            <a href="tel:<?php echo esc_attr($store_data->phone); ?>" class="asl-store-btn asl-store-btn-secondary">
                                <i class="icon-mobile-1" aria-hidden="true"></i><?php echo asl_esc_lbl('store_page_call'); ?>
                            </a>
                        <?php endif; ?>
                        <?php if (! empty($store_data->website)) : ?>
                            <a href="<?php echo esc_url($store_data->website); ?>" target="_blank" rel="noopener" class="asl-store-btn asl-store-btn-secondary">
                                <i class="icon-link" aria-hidden="true"></i><?php echo asl_esc_lbl('website'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (! empty($store_data->map)) : ?>
                <div class="asl-store-map-wrap">
                    <div class="asl-detail-map"></div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($gallery_images) : ?>
            <section class="asl-store-gallery" aria-labelledby="asl-gallery-title">
                <div class="asl-section-heading">
                    <h2 id="asl-gallery-title"><?php echo asl_esc_lbl('store_page_photos'); ?></h2>
                    <?php if (count($gallery_images) > 4) : ?>
                        <button type="button" class="asl-gallery-toggle" aria-expanded="false" aria-controls="asl-store-gallery-grid">
                            <?php echo asl_esc_lbl('store_page_view_photos'); ?>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="asl-gallery-grid" id="asl-store-gallery-grid">
                    <?php foreach ($gallery_images as $image_index => $img_url) : ?>
                        <a
                            class="asl-gallery-item<?php echo $image_index >= 4 ? ' asl-gallery-extra' : ''; ?>"
                            href="<?php echo esc_url($img_url); ?>"
                            target="_blank"
                            rel="noopener"
                            <?php echo $image_index >= 4 ? 'hidden' : ''; ?>
                        >
                            <img src="<?php echo esc_url($img_url); ?>" loading="lazy" alt="<?php echo esc_attr(sprintf(asl_esc_lbl('store_page_photo_alt'), $store_data->title)); ?>">
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($show_descriptions || $show_business_hours || $show_categories) : ?>
            <div class="asl-store-details">
                <?php if ($show_descriptions) : ?>
                    <section class="asl-detail-card">
                        <h2><?php echo asl_esc_lbl('store_page_about'); ?></h2>
                        <div class="asl-description">
                            <?php foreach ($descriptions as $description) : ?>
                                <?php echo wp_kses_post($description); ?>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($show_business_hours) : ?>
                    <section class="asl-detail-card asl-business-hours">
                        <h2><?php echo asl_esc_lbl('store_page_business_hours'); ?></h2>
                        <?php echo wp_kses_post($full_open_hours); ?>
                    </section>
                <?php endif; ?>

                <?php if ($show_categories) : ?>
                    <section class="asl-detail-card">
                        <h2><?php echo asl_esc_lbl('categories_tab'); ?></h2>
                        <ul class="asl-category-list">
                            <?php foreach ($category_items as $category) : ?>
                                <?php if (! empty($category->category_name)) : ?>
                                    <li>
                                        <?php if (! empty($category->icon)) : ?>
                                            <img src="<?php echo esc_url(ASL_UPLOAD_URL . 'svg/' . $category->icon); ?>" alt="" aria-hidden="true">
                                        <?php else : ?>
                                            <i class="icon-tag" aria-hidden="true"></i>
                                        <?php endif; ?>
                                        <span><?php echo esc_html($category->category_name); ?></span>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php echo $google_schema; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
