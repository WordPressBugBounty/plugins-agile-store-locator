<?php
$asl_shortcode_categories = \AgileStoreLocator\Model\Category::get_categories( $this->lang );
$asl_map_center = \AgileStoreLocator\Helper::get_configs( array('default_lat', 'default_lng', 'zoom') );
?>
<!-- sModal -->
<div class="smodal asl-p-cont fade sl-cont sl-main-shortcode-popup" id="insert-sl-shortcode"  role="dialog" aria-labelledby="insert-sl-shortcodeLabel" aria-hidden="true">
  <div class="smodal-dialog smodal-dialog-centered" role="document">
    <div class="smodal-content">
      <div class="smodal-header">
        <div class="asl-smodal-heading">
          <span class="asl-smodal-heading-icon dashicons dashicons-store" aria-hidden="true"></span>
          <div class="asl-shortcode-heading">
            <h5 class="smodal-title" id="insert-sl-shortcodeLabel"><?php esc_html_e('Add Store Locator','asl_locator'); ?></h5>
            <p class="asl-shortcode-intro"><?php esc_html_e('Customize this page or keep your saved settings.','asl_locator'); ?></p>
          </div>
        </div>
        <button type="button" class="close asl-shortcode-x" data-bs-dismiss="smodal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="smodal-body">
        <form id="sl-shortcode-popup">
          <!-- Card body -->
          <div class="sl-inner-settings sl-inner-tab">
            <details class="asl-shortcode-panel" open>
            <summary class="asl-shortcode-section-title"><span class="dashicons dashicons-desktop" aria-hidden="true"></span><?php esc_html_e('Appearance & Search','asl_locator'); ?><span class="asl-shortcode-chevron" aria-hidden="true"></span></summary>
            <div class="asl-shortcode-panel-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label class="form-control-label" for="asl-template"><?php echo esc_attr__('Select Template','asl_locator'); ?></label>
                  <div class="field-group-inner">
                    <select class="custom-select custom-nice-select input" id="asl-template" name="template">
                      <option value=""><?php esc_html_e('Use saved setting','asl_locator'); ?></option>
                      <option value="0"><?php echo esc_attr__('Template 0','asl_locator'); ?></option>
                      <option value="0-legacy"><?php echo esc_attr__('Template 0 (Legacy)','asl_locator'); ?></option>
                      <?php foreach (array('1','2','3','4','5','6','7') as $asl_template): ?>
                        <option value="<?php echo esc_attr($asl_template); ?>" disabled><?php echo esc_html(sprintf(__('Template %s — Pro','asl_locator'), $asl_template)); ?></option>
                      <?php endforeach; ?>
                      <option value="list" disabled><?php esc_html_e('Template List — Pro','asl_locator'); ?></option>
                      <option value="list-2" disabled><?php esc_html_e('Template List 2 — Pro','asl_locator'); ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-control-label" for="asl-shortcode-search-mode"><?php esc_html_e('Search mode','asl_locator'); ?></label>
                  <div class="field-group-inner">
                   <select name="search_mode" id="asl-shortcode-search-mode" class="custom-select">
                      <option value=""><?php esc_html_e('Use saved setting','asl_locator'); ?></option>
                      <option value="automatic"><?php esc_html_e('Address — Automatic','asl_locator'); ?></option>
                      <option value="google_new"><?php esc_html_e('Address — Google Places','asl_locator'); ?></option>
                      <option value="google_legacy"><?php esc_html_e('Address — Google Places (Legacy)','asl_locator'); ?></option>
                      <option value="nominatim"><?php esc_html_e('Address — Nominatim','asl_locator'); ?></option>
                      <option value="geoapify"><?php esc_html_e('Address — Geoapify','asl_locator'); ?></option>
                      <option value="mapbox"><?php esc_html_e('Address — Mapbox','asl_locator'); ?></option>
                      <option value="store_name" disabled><?php esc_html_e('Store name — Pro','asl_locator'); ?></option>
                      <option value="store_location" disabled><?php esc_html_e('Store city or state — Pro','asl_locator'); ?></option>
                      <option value="geocode_enter"><?php esc_html_e('Address — Search on Enter','asl_locator'); ?></option>
                      <option value="disabled"><?php esc_html_e('Address search disabled','asl_locator'); ?></option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-12 asl-shortcode-categories">
                <div class="form-group">
                  <div class="asl-shortcode-category-heading">
                    <label class="form-control-label" id="asl-shortcode-category-label"><?php esc_html_e('Restrict to Categories','asl_locator'); ?> <span class="asl-shortcode-info" title="<?php esc_attr_e('Only stores in the selected categories will appear.','asl_locator'); ?>" aria-label="<?php esc_attr_e('Only stores in the selected categories will appear.','asl_locator'); ?>">?</span></label>
                  </div>
                  <div id="asl-shortcode-category" class="asl-shortcode-category-list" role="group" aria-labelledby="asl-shortcode-category-label">
                    <?php foreach ((array) $asl_shortcode_categories as $asl_category): ?>
                      <label><input type="checkbox" name="category" value="<?php echo esc_attr($asl_category->id); ?>"> <span class="asl-shortcode-category-dot" style="background-color:<?php echo esc_attr(sanitize_hex_color($asl_category->color) ?: '#1769d2'); ?>"></span><span><?php echo esc_html($asl_category->category_name); ?></span></label>
                    <?php endforeach; ?>
                    <?php if (empty($asl_shortcode_categories)): ?><p><?php esc_html_e('No categories available.','asl_locator'); ?></p><?php endif; ?>
                  </div>
                  <p class="asl-shortcode-help"><?php esc_html_e('Select one or more categories. Leave empty to show all stores.','asl_locator'); ?></p>
                </div>
              </div>
            </div>
            </div></details>
            <details class="asl-shortcode-panel" open>
            <summary class="asl-shortcode-section-title"><span class="dashicons dashicons-location" aria-hidden="true"></span><?php esc_html_e('Location & Display','asl_locator'); ?><span class="asl-shortcode-chevron" aria-hidden="true"></span></summary>
            <div class="asl-shortcode-panel-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label class="form-control-label" for="asl-prompt_location"><?php echo esc_attr__('Geo-Location Dialog','asl_locator'); ?></label>
                  <div class="field-group-inner">
                    <select id="asl-prompt_location" class="custom-select" name="prompt_location">
                        <option value=""><?php esc_html_e('Use saved setting','asl_locator'); ?></option>
                        <option value="0"><?php echo esc_attr__('Disable','asl_locator') ?></option>
                        <option value="1"><?php echo esc_attr__('Geo-location Modal','asl_locator') ?></option>
                        <option value="2"><?php echo esc_attr__('Type your Location Modal','asl_locator') ?></option>
                        <option value="3"><?php echo esc_attr__('Geolocation On Load','asl_locator') ?></option>
                        <option value="4"><?php echo esc_attr__('GeoJS IP Service (Free API)','asl_locator') ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group asl-shortcode-center-field">
                  <label class="form-control-label" for="asl-shortcode-default-lat"><?php esc_html_e('Map center for this page','asl_locator'); ?></label>
                  <div class="asl-shortcode-center-inputs">
                    <input type="number" id="asl-shortcode-default-lat" name="default_lat" min="-90" max="90" step="any" placeholder="<?php esc_attr_e('Latitude','asl_locator'); ?>" aria-label="<?php esc_attr_e('Latitude','asl_locator'); ?>" data-center-error="<?php esc_attr_e('Enter a valid latitude and longitude together.','asl_locator'); ?>">
                    <input type="number" id="asl-shortcode-default-lng" name="default_lng" min="-180" max="180" step="any" placeholder="<?php esc_attr_e('Longitude','asl_locator'); ?>" aria-label="<?php esc_attr_e('Longitude','asl_locator'); ?>">
                    <button type="button" id="asl-shortcode-pick-center" class="btn btn-outline-primary" aria-expanded="false" aria-controls="asl-shortcode-map-picker"><?php esc_html_e('Pick on map','asl_locator'); ?></button>
                  </div>
                  <p class="asl-shortcode-help"><?php esc_html_e('Leave both fields empty to use the saved map center.','asl_locator'); ?></p>
                  <div id="asl-shortcode-map-picker" class="asl-shortcode-map-picker" hidden>
                    <p class="asl-shortcode-help"><?php esc_html_e('Click the map or drag the marker to choose the center.','asl_locator'); ?></p>
                    <div id="asl-shortcode-picker-map" class="asl-shortcode-picker-map" data-lat="<?php echo esc_attr($asl_map_center['default_lat'] ?? '0'); ?>" data-lng="<?php echo esc_attr($asl_map_center['default_lng'] ?? '0'); ?>" data-zoom="<?php echo esc_attr($asl_map_center['zoom'] ?? '4'); ?>"></div>
                    <p id="asl-shortcode-map-error" class="asl-shortcode-map-error" role="status" hidden><?php esc_html_e('Map preview is unavailable. You can still enter coordinates above.','asl_locator'); ?></p>
                  </div>
                </div>
                <div class="form-group">
                    <label class="custom-control-label" for="asl-distance_unit"><?php echo esc_attr__('Distance Unit','asl_locator') ?></label>
                    <div>
                       <div class="asl-wc-radio"><label for="asl-distance_unit-default"><input type="radio" name="distance_unit" value="" checked="checked" id="asl-distance_unit-default"><?php esc_html_e('Use saved setting','asl_locator'); ?></label></div>
                       <div class="asl-wc-radio">
                          <label for="asl-distance_unit-KM"><input type="radio" name="distance_unit" value="KM"  id="asl-distance_unit-KM"><?php echo esc_attr__('KM','asl_locator') ?></label>
                       </div>
                       <div class="asl-wc-radio">
                          <label for="asl-distance_unit-Miles"><input type="radio" name="distance_unit" value="Miles" id="asl-distance_unit-Miles"><?php echo esc_attr__('Miles','asl_locator') ?></label>
                       </div>
                    </div>
                </div>
                <div class="form-group">
                  <label class="custom-control-label" for="asl-time_format"><?php echo esc_attr__('Time Format','asl_locator') ?></label>
                  <div>
                     <div class="asl-wc-radio"><label for="asl-time_format-default"><input type="radio" checked="checked" name="time_format" value="" id="asl-time_format-default"><?php esc_html_e('Use saved setting','asl_locator'); ?></label></div>
                     <div class="asl-wc-radio">
                        <label for="asl-time_format-0"><input type="radio" name="time_format" value="0"  id="asl-time_format-0"><?php echo esc_attr__('12 Hours','asl_locator') ?></label>
                     </div>
                     <div class="asl-wc-radio">
                        <label for="asl-time_format-1"><input type="radio" name="time_format" value="1" id="asl-time_format-1"><?php echo esc_attr__('24 Hours','asl_locator') ?></label>
                     </div>
                     <p class="help-p"><?php echo esc_attr__('Select either 12 or 24 hours time format','asl_locator') ?></p>
                  </div>
                </div>
              </div>
            </div>
            </div></details>
          </div>
        </form>
      </div>
      <div class="smodal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="smodal"><?php echo esc_attr__('Close','asl_locator'); ?></button>
        <button type="button" id="sl-add-shortcode" class="btn btn-primary"><?php echo esc_attr__('Insert Shortcode','asl_locator'); ?> <span aria-hidden="true">→</span></button>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">
  var ASL_Instance = {
    url: '<?php echo ASL_UPLOAD_URL ?>'
  };
  window.addEventListener("load", function() {
    asl_engine.shortcode_generator();
  });
</script>
