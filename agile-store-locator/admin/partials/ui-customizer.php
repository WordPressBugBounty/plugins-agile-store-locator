
<div class="asl-customizer" id="asl-ui-customizer">
  <header class="asl-customizer__header">
    <h1><?php esc_html_e('Agile Store Locator UI Customizer', 'asl_locator'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=asl-settings')); ?>"><span aria-hidden="true">←</span> <?php esc_html_e('Back to Settings', 'asl_locator'); ?></a>
  </header>

  <div class="asl-customizer__toolbar">
    <label for="asl-ui-template"><?php esc_html_e('Template', 'asl_locator'); ?></label>
    <div class="asl-template-select"><span class="dashicons dashicons-layout" aria-hidden="true"></span>
      <select id="asl-ui-template" name="ui-template">
                             <option value="template-0"><?php echo esc_attr__('Template','asl_locator') ?> 0</option>
                             <option disabled value="template-1"><?php echo esc_attr__('Template','asl_locator') ?> 1</option>
                             <option disabled value="template-2"><?php echo esc_attr__('Template','asl_locator') ?> 2</option>
                             <option disabled value="template-3"><?php echo esc_attr__('Template','asl_locator') ?> 3</option>
                             <option disabled value="template-4"><?php echo esc_attr__('Template','asl_locator') ?> 4</option>
                             <option disabled value="template-5"><?php echo esc_attr__('Template','asl_locator') ?> 5</option>
                             <option disabled value="template-6"><?php echo esc_attr__('Template','asl_locator') ?> 6</option>
                             <option disabled value="template-list"><?php echo esc_attr__('Template List','asl_locator') ?></option>
                             <option disabled value="template-list-2"><?php echo esc_attr__('Template List 2','asl_locator') ?></option>
                             <?php if(defined ( 'ASL_WC_VERSION' )):?>
                              <option value="template-wc"><?php echo esc_attr__('WC Addon','asl_locator') ?></option>
                             <?php endif; ?>
      </select>
    </div>
    <button type="button" class="asl-button asl-button--outline" id="btn-asl-load_uitemp"><span class="dashicons dashicons-download" aria-hidden="true"></span><?php esc_html_e('Load Template', 'asl_locator'); ?></button>
    <button type="button" class="asl-button asl-button--save" id="btn-asl-save_uitemp" disabled><span class="dashicons dashicons-saved" aria-hidden="true"></span><span><?php esc_html_e('Save Settings', 'asl_locator'); ?></span></button>
    <button type="button" class="asl-button asl-button--reset" id="btn-asl-reset_uitemp" disabled><span class="dashicons dashicons-image-rotate" aria-hidden="true"></span><?php esc_html_e('Reset Template', 'asl_locator'); ?></button>
  </div>

  <div class="asl-customizer__notice" id="asl-customizer-notice" role="status" aria-live="polite" hidden></div>

  <main class="asl-customizer__workspace">
    <aside class="asl-preview-panel">
      <h2><?php esc_html_e('Live Preview', 'asl_locator'); ?></h2>
      <div class="asl-preview-tip"><span class="dashicons dashicons-lightbulb"></span><p><strong><?php esc_html_e('Quick tip:', 'asl_locator'); ?></strong> <?php echo wp_kses_post(__('Change only the <mark>Primary</mark> color to automatically update the matching theme colors.', 'asl_locator')); ?></p></div>
      <div class="asl-preview" id="asl-live-preview">
        <div class="asl-preview__header">
          <label><?php esc_html_e('Search Location', 'asl_locator'); ?></label>
          <div class="asl-preview__search"><span><?php esc_html_e('Enter a Location', 'asl_locator'); ?></span><button type="button" aria-label="<?php esc_attr_e('Search', 'asl_locator'); ?>"><span class="dashicons dashicons-search"></span></button></div>
        </div>
        <div class="asl-preview__summary"><strong><?php esc_html_e('Number Of Shops: 66', 'asl_locator'); ?></strong><span><?php esc_html_e('PRINT', 'asl_locator'); ?> <span class="dashicons dashicons-printer"></span></span></div>
        <div class="asl-preview__list">
          <article class="asl-preview__store" tabindex="0">
            <div class="asl-preview__store-copy"><h3><?php esc_html_e('Amanda Food Court', 'asl_locator'); ?></h3><p><span class="dashicons dashicons-location"></span>45 North Street<br><i></i>Uitenhage, Eastern Cape, 5043</p><p><span class="dashicons dashicons-smartphone"></span>041 111 3964</p><p><span class="dashicons dashicons-email-alt"></span>amanda.food.court@example.com</p><p><span class="dashicons dashicons-clock"></span>Mon - Fri: &nbsp;&nbsp; 09:30 AM - 06:30 PM</p><p><span class="dashicons dashicons-calendar-alt"></span>Mon, Tues, Wed, Thur, Fri</p><p><span class="dashicons dashicons-tag"></span>Entertainment &nbsp;&nbsp; Travel</p></div>
            <div class="asl-preview__photo" aria-hidden="true"><span class="dashicons dashicons-businessperson"></span></div>
            <div class="asl-preview__actions"><button type="button"><?php esc_html_e('Directions', 'asl_locator'); ?></button><button type="button"><?php esc_html_e('Website', 'asl_locator'); ?></button></div>
          </article>
          <article class="asl-preview__store" tabindex="0"><div class="asl-preview__store-copy"><h3><?php esc_html_e('Amanzi Club', 'asl_locator'); ?></h3><p><span class="dashicons dashicons-location"></span>46 Longfellow Street, Ridgeway</p></div><div class="asl-preview__photo asl-preview__photo--shop"><span class="dashicons dashicons-store"></span></div></article>
        </div>
      </div>
    </aside>

    <section class="asl-customizer__editor">
      <form id="frm-asl-ui-customizer">
        <div id="asl-fields-section"><div class="asl-customizer__loading"><span class="spinner is-active"></span><?php esc_html_e('Loading customizer…', 'asl_locator'); ?></div></div>
      </form>
      <footer class="asl-customizer__help"><span class="dashicons dashicons-info-outline"></span><span><?php esc_html_e('Need help? Check our documentation for details on customizing your store locator.', 'asl_locator'); ?></span><a href="https://agilestorelocator.com/wiki/" target="_blank" rel="noopener noreferrer"><?php esc_html_e('View Documentation', 'asl_locator'); ?> <span class="dashicons dashicons-external"></span></a></footer>
    </section>
  </main>
</div>
