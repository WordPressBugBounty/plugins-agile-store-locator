<h3 class="asl-layout-section-title"><?php esc_html_e('Width or Height', 'asl_locator'); ?></h3>
<div class="asl-appearance-controls">
  <label class="asl-appearance-row asl-appearance-row--select" for="asl-appearance-full_height">
    <span><?php esc_html_e('Full Height', 'asl_locator'); ?></span>
    <select id="asl-appearance-full_height" name="appearance[full_height]" data-asl-appearance>
      <?php foreach (['' => __('None', 'asl_locator'), 'full-height' => __('Full Height (Not Fixed)', 'asl_locator'), 'full-height asl-fixed' => __('Full Height (Fixed)', 'asl_locator')] as $value => $label) : ?>
      <option value="<?php echo esc_attr($value); ?>" <?php selected($appearance['full_height'] ?? '', $value); ?>><?php echo esc_html($label); ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="asl-appearance-row" for="asl-appearance-full_width">
    <span><?php esc_html_e('Full Width', 'asl_locator'); ?></span>
    <span class="asl-appearance-switch"><input type="checkbox" role="switch" id="asl-appearance-full_width" name="appearance[full_width]" value="1" data-asl-appearance <?php checked($appearance['full_width'], '1'); ?>><span class="asl-appearance-switch-track" aria-hidden="true"></span></span>
  </label>
</div>
<div class="asl-fixed-dimensions">
  <div class="asl-fixed-dimensions__heights">
    <?php foreach (['locator-height' => __('Height', 'asl_locator'), 'locator-phone-height' => __('Height on phones', 'asl_locator')] as $key => $label) : ?>
    <label class="asl-dimension-field" for="asl-<?php echo esc_attr($key); ?>">
      <span><?php echo esc_html($label); ?></span>
      <input type="text" id="asl-<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" data-asl-dimension value="<?php echo esc_attr(isset($fields->$key) ? $fields->$key : ''); ?>" placeholder="<?php echo $key === 'locator-height' ? '800px' : esc_attr__('Same', 'asl_locator'); ?>" pattern="(?:[0-9]+(?:\.[0-9]+)?|\.[0-9]+)(?:px|vh|vw|rem|em|%|vmin|vmax)" aria-describedby="asl-dimension-size-help" spellcheck="false">
    </label>
    <?php endforeach; ?>
  </div>
  <p id="asl-dimension-size-help" class="asl-layout-hint"><?php esc_html_e('e.g. 480px, 60vh', 'asl_locator'); ?></p>
  <label class="asl-dimension-field" for="asl-locator-width">
    <span><?php esc_html_e('Width', 'asl_locator'); ?></span>
    <input type="text" id="asl-locator-width" name="locator-width" data-asl-dimension value="<?php echo esc_attr(isset($fields->{'locator-width'}) ? $fields->{'locator-width'} : ''); ?>" placeholder="100%" pattern="(?:[0-9]+(?:\.[0-9]+)?|\.[0-9]+)(?:px|vh|vw|rem|em|%|vmin|vmax)" spellcheck="false">
  </label>
</div>
