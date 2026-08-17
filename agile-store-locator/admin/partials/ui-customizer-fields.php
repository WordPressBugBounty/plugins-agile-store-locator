<?php
$color_descriptions = [
    'primary' => __('Main brand color, buttons and highlights', 'asl_locator'), 'header' => __('Background of the header area', 'asl_locator'),
    'header-color' => __('Text color in the header', 'asl_locator'), 'infobox-color' => __('Text color in the info window', 'asl_locator'),
    'infobox-bg' => __('Background of the info window', 'asl_locator'), 'infobox-a' => __('Link color in the info window', 'asl_locator'),
    'search-text' => __('Color of the search text', 'asl_locator'), 'search-btn-color' => __('Text color of the search button', 'asl_locator'),
    'search-btn-bg' => __('Background of the search button', 'asl_locator'), 'action-btn-color' => __('Text color of action buttons', 'asl_locator'),
    'action-btn-bg' => __('Background of action buttons', 'asl_locator'), 'color' => __('General text color', 'asl_locator'),
    'list-bg' => __('Background of the list area', 'asl_locator'), 'list-title' => __('Color of list titles', 'asl_locator'),
    'list-sub-title' => __('Color of list subtitles', 'asl_locator'), 'highlighted' => __('Color for highlighted elements', 'asl_locator'),
];
?>
<div class="asl-customizer-tabs" role="tablist">
  <button type="button" class="is-active" role="tab" aria-selected="true" data-asl-tab="asl-color"><span class="dashicons dashicons-art"></span><?php esc_html_e('Colors', 'asl_locator'); ?></button>
  <button type="button" role="tab" aria-selected="false" data-asl-tab="sl-font-size"><span class="dashicons dashicons-editor-textcolor"></span><?php esc_html_e('Font Size', 'asl_locator'); ?></button>
</div>
<div id="asl-color" class="asl-customizer-pane is-active" role="tabpanel"><div class="asl-fields-grid">
<?php foreach ($colors[$template] as $color => $type) : $label = ucwords(str_replace('-', ' ', $color)); $value = isset($fields->$color) ? $fields->$color : $default_colors[$color]; $help = $color_descriptions[$color] ?? __('Customize this locator color', 'asl_locator'); ?>
  <label class="asl-color-field" for="asl-<?php echo esc_attr($color); ?>-text"><span class="asl-field-icon"><span class="dashicons dashicons-admin-customizer"></span></span><span class="asl-field-copy"><strong><?php echo esc_html($label); ?></strong><small><?php echo esc_html($help); ?></small></span><span class="asl-color-control"><input type="text" id="asl-<?php echo esc_attr($color); ?>-text" value="<?php echo esc_attr($value); ?>" class="hexcolor" name="<?php echo esc_attr($color); ?>" maxlength="7" spellcheck="false"><input type="color" class="colorpicker <?php echo esc_attr($type); ?>" id="asl-<?php echo esc_attr($color); ?>" value="<?php echo esc_attr($value); ?>" aria-label="<?php echo esc_attr(sprintf(__('Choose %s', 'asl_locator'), $label)); ?>"></span></label>
<?php endforeach; ?>
</div></div>
<div id="sl-font-size" class="asl-customizer-pane" role="tabpanel" hidden><div class="asl-fields-grid">
<?php foreach ($default_fonts as $font_key => $font_value) : $font_label = $font_labels[$font_key] ?? ucwords(str_replace('-', ' ', $font_key)); $font_size = isset($fields->$font_key) ? $fields->$font_key : $font_value; ?>
  <label class="asl-font-field" for="asl-<?php echo esc_attr($font_key); ?>"><span class="asl-field-icon"><span class="dashicons dashicons-editor-textcolor"></span></span><span class="asl-field-copy"><strong><?php echo esc_html($font_label); ?></strong><small><?php esc_html_e('Font size in pixels', 'asl_locator'); ?></small></span><span class="asl-font-control"><input type="number" name="<?php echo esc_attr($font_key); ?>" id="asl-<?php echo esc_attr($font_key); ?>" min="8" max="72" value="<?php echo esc_attr($font_size); ?>"><em>px</em></span></label>
<?php endforeach; ?>
</div></div>
