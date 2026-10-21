<div class="asl-customizer" id="asl-ui-customizer">
  <header class="asl-customizer__header">
    <div class="asl-customizer__header-copy">
      <h1><?php esc_html_e('UI Customizer', 'asl_locator'); ?></h1>
      <p><?php esc_html_e('Design the layout, filters and appearance of your store locator.', 'asl_locator'); ?></p>
    </div>
    <div class="asl-customizer__header-actions">
      <a href="<?php echo esc_url(admin_url('admin.php?page=asl-settings')); ?>"><span aria-hidden="true">←</span>
        <?php esc_html_e('Back to Settings', 'asl_locator'); ?></a>
    </div>
  </header>

  <div class="asl-customizer__toolbar">
    <label for="asl-ui-template"><?php esc_html_e('Template', 'asl_locator'); ?></label>
    <div class="asl-template-select"><span class="dashicons dashicons-layout" aria-hidden="true"></span>
      <select id="asl-ui-template" name="ui-template">
        <optgroup label="<?php esc_attr_e('Included', 'asl_locator'); ?>">
          <option value="template-0"><?php echo esc_html__('Template', 'asl_locator'); ?> 0</option>
        </optgroup>
        <?php if (defined('ASL_WC_VERSION')): ?>
          <option value="template-wc"><?php echo esc_attr__('WC Addon', 'asl_locator') ?></option>
        <?php endif; ?>
        <optgroup label="<?php esc_attr_e('Available in Pro', 'asl_locator'); ?>">
          <?php foreach (['1', '2', '3', '4', '5', '6', '7'] as $asl_pro_template) : ?>
            <option value="" disabled><?php echo esc_html(sprintf(__('Template %s — Pro', 'asl_locator'), $asl_pro_template)); ?></option>
          <?php endforeach; ?>
          <option value="" disabled><?php esc_html_e('List Template — Pro', 'asl_locator'); ?></option>
          <option value="" disabled><?php esc_html_e('List Template 2 — Pro', 'asl_locator'); ?></option>
        </optgroup>
      </select>
    </div>
    <button type="button" class="asl-button asl-button--outline" id="btn-asl-load_uitemp"><span
        class="dashicons dashicons-download"
        aria-hidden="true"></span><?php esc_html_e('Load Template', 'asl_locator'); ?></button>
    <button type="button" class="asl-button asl-button--save" id="btn-asl-save_uitemp" disabled><span
        class="dashicons dashicons-saved"
        aria-hidden="true"></span><span><?php esc_html_e('Save Settings', 'asl_locator'); ?></span></button>
    <button type="button" class="asl-button asl-button--reset" id="btn-asl-reset_uitemp" disabled><span
        class="dashicons dashicons-image-rotate"
        aria-hidden="true"></span><?php esc_html_e('Reset Template', 'asl_locator'); ?></button>
    <a class="asl-template-pro-hint" href="<?php echo esc_url(defined('ASL_UPGRADE_URL') ? ASL_UPGRADE_URL : 'https://agilestorelocator.com/pricing/'); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-star-filled" aria-hidden="true"></span><?php esc_html_e('9 more templates in Pro', 'asl_locator'); ?></a>
  </div>

  <div class="asl-customizer__notice" id="asl-customizer-notice" role="status" aria-live="polite" hidden></div>

  <main class="asl-customizer__workspace is-panel-open">
    <nav class="asl-customizer-rail" role="tablist" aria-orientation="vertical"
      aria-label="<?php esc_attr_e('Customizer settings', 'asl_locator'); ?>">
      <?php foreach (['design' => ['Design', 'art'], 'layout' => ['Layout', 'screenoptions'], 'filters' => ['Filters', 'filter'], 'map' => ['Map', 'location-alt']] as $tab => $item): ?>
        <button type="button" id="asl-tab-<?php echo esc_attr($tab); ?>" role="tab"
          aria-controls="asl-pane-<?php echo esc_attr($tab); ?>"
          aria-selected="<?php echo $tab === 'design' ? 'true' : 'false'; ?>" data-asl-tab="<?php echo esc_attr($tab); ?>"
          class="<?php echo $tab === 'design' ? 'is-active' : ''; ?>"><span
            class="dashicons dashicons-<?php echo esc_attr($item[1]); ?>"
            aria-hidden="true"></span><?php echo esc_html__($item[0], 'asl_locator'); ?></button>
      <?php endforeach; ?>
    </nav>
    <section class="asl-customizer__editor" aria-label="<?php esc_attr_e('Settings panel', 'asl_locator'); ?>">
      <header class="asl-editor-heading">
        <div>
          <h2 id="asl-panel-title"><?php esc_html_e('Design', 'asl_locator'); ?></h2>
          <p><?php esc_html_e('Customize your store locator in real time.', 'asl_locator'); ?></p>
        </div><button type="button" id="asl-close-panel"
          aria-label="<?php esc_attr_e('Close settings panel', 'asl_locator'); ?>"><span
            class="dashicons dashicons-no-alt"></span></button>
      </header>
      <form id="frm-asl-ui-customizer">
        <div id="asl-fields-section">
          <div class="asl-customizer__loading"><span
              class="spinner is-active"></span><?php esc_html_e('Loading customizer...', 'asl_locator'); ?></div>
        </div>
      </form>
      <footer class="asl-customizer__help"><a href="https://agilestorelocator.com/wiki/" target="_blank"
          rel="noopener noreferrer"><?php esc_html_e('View Documentation', 'asl_locator'); ?> <span
            class="dashicons dashicons-external"></span></a></footer>
    </section>
    <section class="asl-preview-panel" aria-label="<?php esc_attr_e('Live store locator preview', 'asl_locator'); ?>">
      <div class="asl-preview-toolbar">
        <div class="asl-device-tabs" role="group" aria-label="<?php esc_attr_e('Preview device', 'asl_locator'); ?>">
          <?php foreach (['desktop' => ['Desktop', 'desktop'], 'tablet' => ['Tablet', 'tablet'], 'mobile' => ['Mobile', 'smartphone']] as $device => $item): ?>
            <button type="button" data-asl-device="<?php echo esc_attr($device); ?>"
              aria-pressed="<?php echo $device === 'desktop' ? 'true' : 'false'; ?>"
              class="<?php echo $device === 'desktop' ? 'is-active' : ''; ?>"><span
                class="dashicons dashicons-<?php echo esc_attr($item[1]); ?>"></span><?php echo esc_html__($item[0], 'asl_locator'); ?></button>
          <?php endforeach; ?>
        </div><span class="asl-live-badge"><i></i><?php esc_html_e('Live Preview', 'asl_locator'); ?></span>
      </div>
      <div class="asl-preview-stage" data-device="desktop"><iframe id="asl-live-preview"
          title="<?php esc_attr_e('Store locator live preview', 'asl_locator'); ?>"></iframe></div>
    </section>
  </main>
</div>
