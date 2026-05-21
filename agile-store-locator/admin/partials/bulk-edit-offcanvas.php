<?php
  if (!defined('ABSPATH')) {
    exit;
  }
  $bulk_edit_fields_map = array_flip($bulk_edit_fields);
  $bulk_custom_fields = [];
  if (is_array($added_custom_fields)) {
    foreach ($added_custom_fields as $custom_field) {
      $field_name = is_object($custom_field) ? $custom_field->name : (isset($custom_field['name']) ? $custom_field['name'] : '');
      if (!$field_name) {
        continue;
      }
      $bulk_key = 'custom:' . $field_name;
      if (isset($bulk_edit_fields_map[$bulk_key])) {
        $bulk_custom_fields[] = $custom_field;
      }
    }
  }
?>

  <!-- Bulk Edit Offcanvas -->
  <div class="sl_offcanvas sl_offcanvas-end" tabindex="-1" id="sl-bulk-edit" aria-labelledby="sl-bulk-edit-label">
    <div class="sl_offcanvas-header">
      <h5 id="sl-bulk-edit-label"><?php echo esc_attr__('Bulk Edit Stores', 'asl_locator') ?></h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="sl_offcanvas" aria-label="Close"></button>
    </div>
    <div class="sl_offcanvas-body">
      <p class="small text-muted mb-3">
        <?php echo esc_attr__('Applies to', 'asl_locator') ?>
        <strong><span id="sl-bulk-edit-count">0</span></strong>
        <?php echo esc_attr__('selected stores.', 'asl_locator') ?>
      </p>
      <form id="frm-bulk-edit-stores" name="frm-bulk-edit-stores">
        <input type="hidden" id="asl-bulk-edit-store-ids" value="" />
        <?php if (isset($bulk_edit_fields_map['description'])): ?>
        <div class="form-group d-flex align-items-center justify-content-between mb-2">
          <label class="mb-0" for="asl-bulk-edit-apply-description">
            <?php echo esc_attr__('Update Description', 'asl_locator') ?>
          </label>
          <label class="switch mb-0">
            <input type="checkbox" id="asl-bulk-edit-apply-description">
            <span class="slider round"></span>
          </label>
        </div>
        <div class="mb-3 asl-bulk-field asl-bulk-field-description">
          <label for="asl-bulk-edit-description" class="form-label">
            <?php echo esc_attr__('Description', 'asl_locator') ?>
          </label>
          <textarea class="form-control" id="asl-bulk-edit-description" rows="4" disabled="disabled"
            placeholder="<?php echo esc_attr__('Enter description to apply to selected stores', 'asl_locator') ?>"></textarea>
        </div>
        <?php endif; ?>
        <?php if (isset($bulk_edit_fields_map['open_hours'])): ?>
        <div class="form-group d-flex align-items-center justify-content-between mb-2">
          <label class="mb-0" for="asl-bulk-edit-apply-open-hours">
            <?php echo esc_attr__('Update Open Hours', 'asl_locator') ?>
          </label>
          <label class="switch mb-0">
            <input type="checkbox" id="asl-bulk-edit-apply-open-hours">
            <span class="slider round"></span>
          </label>
        </div>
        <div class="mb-3 asl-bulk-open-hours asl-bulk-field asl-bulk-field-open-hours">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0"><?php echo esc_attr__('Store Timing', 'asl_locator') ?></h6>
            <a id="asl-bulk-time-cp" class="btn btn-sm">
              <i class="me-2"><svg width="16" height="17"><use xlink:href="#i-plus"></use></svg></i>
              <?php echo esc_attr__('Same Everyday', 'asl_locator') ?>
            </a>
          </div>
          <div class="table-responsive">
            <table class="table asl-time-details">
              <tbody>
                <tr>
                  <td class="text-center">
                    <input type="checkbox" class="asl-bulk-open-day" data-day="mon" disabled="disabled" title="<?php echo esc_attr__('Apply Monday', 'asl_locator') ?>">
                  </td>
                  <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Monday','asl_locator') ?></span></td>
                  <td colspan="3">
                    <div class="asl-all-day-times" data-day="mon">
                      <div class="asl-closed-lbl">
                        <div class="a-swith">
                          <input id="bulk-cmn-toggle-0" class="cmn-toggle cmn-toggle-round" type="checkbox" disabled="disabled">
                          <label for="bulk-cmn-toggle-0"></label>
                          <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                          <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                        </div>
                      </div>
                    </div>
                  <input type="text" class="form-control form-control-sm asl-day-label" data-day-label="mon" placeholder="<?php echo esc_attr__('Day Label (optional)', 'asl_locator') ?>" value="">
                  </td>
                  <td>
                    <span class="add-k-add glyp-add float-end text-primary">
                      <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                    </span>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <input type="checkbox" class="asl-bulk-open-day" data-day="tue" disabled="disabled" title="<?php echo esc_attr__('Apply Tuesday', 'asl_locator') ?>">
                  </td>
                  <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Tuesday','asl_locator') ?></span></td>
                  <td colspan="3">
                    <div class="asl-all-day-times" data-day="tue">
                      <div class="asl-closed-lbl">
                        <div class="a-swith">
                          <input id="bulk-cmn-toggle-1" class="cmn-toggle cmn-toggle-round" type="checkbox" disabled="disabled">
                          <label for="bulk-cmn-toggle-1"></label>
                          <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                          <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                        </div>
                      </div>
                    </div>
                  <input type="text" class="form-control form-control-sm asl-day-label" data-day-label="tue" placeholder="<?php echo esc_attr__('Day Label (optional)', 'asl_locator') ?>" value="">
                  </td>
                  <td>
                    <span class="add-k-add glyp-add float-end text-primary">
                      <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                    </span>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <input type="checkbox" class="asl-bulk-open-day" data-day="wed" disabled="disabled" title="<?php echo esc_attr__('Apply Wednesday', 'asl_locator') ?>">
                  </td>
                  <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Wednesday','asl_locator') ?></span></td>
                  <td colspan="3">
                    <div class="asl-all-day-times" data-day="wed">
                      <div class="asl-closed-lbl">
                        <div class="a-swith">
                          <input id="bulk-cmn-toggle-2" class="cmn-toggle cmn-toggle-round" type="checkbox" disabled="disabled">
                          <label for="bulk-cmn-toggle-2"></label>
                          <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                          <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                        </div>
                      </div>
                    </div>
                  <input type="text" class="form-control form-control-sm asl-day-label" data-day-label="wed" placeholder="<?php echo esc_attr__('Day Label (optional)', 'asl_locator') ?>" value="">
                  </td>
                  <td>
                    <span class="add-k-add glyp-add float-end text-primary">
                      <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                    </span>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <input type="checkbox" class="asl-bulk-open-day" data-day="thu" disabled="disabled" title="<?php echo esc_attr__('Apply Thursday', 'asl_locator') ?>">
                  </td>
                  <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Thursday','asl_locator') ?></span></td>
                  <td colspan="3">
                    <div class="asl-all-day-times" data-day="thu">
                      <div class="asl-closed-lbl">
                        <div class="a-swith">
                          <input id="bulk-cmn-toggle-3" class="cmn-toggle cmn-toggle-round" type="checkbox" disabled="disabled">
                          <label for="bulk-cmn-toggle-3"></label>
                          <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                          <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                        </div>
                      </div>
                    </div>
                  <input type="text" class="form-control form-control-sm asl-day-label" data-day-label="thu" placeholder="<?php echo esc_attr__('Day Label (optional)', 'asl_locator') ?>" value="">
                  </td>
                  <td>
                    <span class="add-k-add glyp-add float-end text-primary">
                      <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                    </span>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <input type="checkbox" class="asl-bulk-open-day" data-day="fri" disabled="disabled" title="<?php echo esc_attr__('Apply Friday', 'asl_locator') ?>">
                  </td>
                  <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Friday','asl_locator') ?></span></td>
                  <td colspan="3">
                    <div class="asl-all-day-times" data-day="fri">
                      <div class="asl-closed-lbl">
                        <div class="a-swith">
                          <input id="bulk-cmn-toggle-4" class="cmn-toggle cmn-toggle-round" type="checkbox" disabled="disabled">
                          <label for="bulk-cmn-toggle-4"></label>
                          <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                          <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                        </div>
                      </div>
                    </div>
                  <input type="text" class="form-control form-control-sm asl-day-label" data-day-label="fri" placeholder="<?php echo esc_attr__('Day Label (optional)', 'asl_locator') ?>" value="">
                  </td>
                  <td>
                    <span class="add-k-add glyp-add float-end text-primary">
                      <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                    </span>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <input type="checkbox" class="asl-bulk-open-day" data-day="sat" disabled="disabled" title="<?php echo esc_attr__('Apply Saturday', 'asl_locator') ?>">
                  </td>
                  <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Saturday','asl_locator') ?></span></td>
                  <td colspan="3">
                    <div class="asl-all-day-times" data-day="sat">
                      <div class="asl-closed-lbl">
                        <div class="a-swith">
                          <input id="bulk-cmn-toggle-5" class="cmn-toggle cmn-toggle-round" type="checkbox" disabled="disabled">
                          <label for="bulk-cmn-toggle-5"></label>
                          <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                          <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                        </div>
                      </div>
                    </div>
                  <input type="text" class="form-control form-control-sm asl-day-label" data-day-label="sat" placeholder="<?php echo esc_attr__('Day Label (optional)', 'asl_locator') ?>" value="">
                  </td>
                  <td>
                    <span class="add-k-add glyp-add float-end text-primary">
                      <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                    </span>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">
                    <input type="checkbox" class="asl-bulk-open-day" data-day="sun" disabled="disabled" title="<?php echo esc_attr__('Apply Sunday', 'asl_locator') ?>">
                  </td>
                  <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Sunday','asl_locator') ?></span></td>
                  <td colspan="3">
                    <div class="asl-all-day-times" data-day="sun">
                      <div class="asl-closed-lbl">
                        <div class="a-swith">
                          <input id="bulk-cmn-toggle-6" class="cmn-toggle cmn-toggle-round" type="checkbox" disabled="disabled">
                          <label for="bulk-cmn-toggle-6"></label>
                          <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                          <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                        </div>
                      </div>
                    </div>
                  <input type="text" class="form-control form-control-sm asl-day-label" data-day-label="sun" placeholder="<?php echo esc_attr__('Day Label (optional)', 'asl_locator') ?>" value="">
                  </td>
                  <td>
                    <span class="add-k-add glyp-add float-end text-primary">
                      <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>
        <?php if (isset($bulk_edit_fields_map['marker_id'])): ?>
        <div class="form-group d-flex align-items-center justify-content-between mb-2">
          <label class="mb-0" for="asl-bulk-edit-apply-marker">
            <?php echo esc_attr__('Update Marker', 'asl_locator') ?>
          </label>
          <label class="switch mb-0">
            <input type="checkbox" id="asl-bulk-edit-apply-marker">
            <span class="slider round"></span>
          </label>
        </div>
        <div class="mb-3 asl-bulk-field asl-bulk-field-marker">
          <label for="ddl-asl-bulk-markers" class="form-label"><?php echo esc_attr__('Marker', 'asl_locator') ?></label>
          <div class="input-group">
            <select id="ddl-asl-bulk-markers">
              <?php foreach($markers as $m):?>
              <option value="<?php echo esc_attr($m->id) ?>"
                data-imagesrc="<?php echo ASL_UPLOAD_URL.'icon/'.$m->icon;?>"
                data-description="&nbsp;">
                <?php echo esc_attr($m->marker_name);?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <?php endif; ?>
        <?php if (isset($bulk_edit_fields_map['logo_id'])): ?>
        <div class="form-group d-flex align-items-center justify-content-between mb-2">
          <label class="mb-0" for="asl-bulk-edit-apply-logo">
            <?php echo esc_attr__('Update Logo', 'asl_locator') ?>
          </label>
          <label class="switch mb-0">
            <input type="checkbox" id="asl-bulk-edit-apply-logo">
            <span class="slider round"></span>
          </label>
        </div>
        <div class="mb-3 asl-bulk-field asl-bulk-field-logo">
          <label for="ddl-asl-bulk-logos" class="form-label"><?php echo esc_attr__('Logo', 'asl_locator') ?></label>
          <div class="input-group">
            <div id="ddl-asl-bulk-logos"></div>
          </div>
        </div>
        <?php endif; ?>
        <?php if (isset($bulk_edit_fields_map['categories'])): ?>
        <div class="form-group d-flex align-items-center justify-content-between mb-2">
          <label class="mb-0" for="asl-bulk-edit-apply-categories">
            <?php echo esc_attr__('Update Categories', 'asl_locator') ?>
          </label>
          <label class="switch mb-0">
            <input type="checkbox" id="asl-bulk-edit-apply-categories">
            <span class="slider round"></span>
          </label>
        </div>
        <div class="mb-3 asl-bulk-field asl-bulk-field-categories">
          <label for="ddl-asl-bulk-categories" class="form-label"><?php echo esc_attr__('Categories', 'asl_locator') ?></label>
          <select name="ddl_asl_bulk_categories" id="ddl-asl-bulk-categories" multiple
            class="chosen-select-width form-control">
            <?php foreach($category as $catego): ?>
            <?php if ($catego->parent_id) continue; ?>
            <option value="<?php echo esc_attr($catego->id) ?>">
              <?php echo esc_attr($catego->category_name) ?>
            </option>
            <?php foreach($category as $sub_catego): ?>
            <?php if ($catego->id != $sub_catego->parent_id) continue; ?>
            <option value="<?php echo esc_attr($sub_catego->id) ?>">
              <?php echo esc_attr($catego->category_name) ?> >
              <?php echo esc_attr($sub_catego->category_name) ?>
            </option>
            <?php endforeach ?>
            <?php endforeach ?>
          </select>
        </div>
        <?php endif; ?>
        <?php if (!empty($bulk_custom_fields)): ?>
          <?php foreach ($bulk_custom_fields as $custom_field): ?>
            <?php
              $field_name = is_object($custom_field) ? $custom_field->name : (isset($custom_field['name']) ? $custom_field['name'] : '');
              $field_label = is_object($custom_field) ? (isset($custom_field->label) ? $custom_field->label : $field_name) : (isset($custom_field['label']) ? $custom_field['label'] : $field_name);
              $field_id = 'asl-bulk-edit-apply-custom-' . sanitize_key($field_name);
            ?>
            <div class="form-group d-flex align-items-center justify-content-between mb-2">
              <label class="mb-0" for="<?php echo esc_attr($field_id); ?>">
                <?php echo esc_attr(sprintf(__('Update %s', 'asl_locator'), $field_label)); ?>
              </label>
              <label class="switch mb-0">
                <input class="asl-bulk-custom-toggle" type="checkbox"
                  id="<?php echo esc_attr($field_id); ?>" data-field="<?php echo esc_attr($field_name); ?>">
                <span class="slider round"></span>
              </label>
            </div>
            <div class="mb-3 asl-bulk-field asl-bulk-field-custom" data-field="<?php echo esc_attr($field_name); ?>">
              <?php
                $field_data = is_object($custom_field) ? get_object_vars($custom_field) : $custom_field;
                $custom_control = new \AgileStoreLocator\Form\CustomField($field_data, '');
                echo $custom_control->render('asl-bulk-custom');
              ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </form>
      <div class="d-flex justify-content-end gap-2">
        <button type="button" id="btn-asl-bulk-edit-apply"
          data-loading-text="<?php echo esc_attr__('Submitting ...', 'asl_locator') ?>"
          class="btn btn-primary"><?php echo esc_attr__('Apply Changes', 'asl_locator') ?></button>
        <button type="button" class="btn btn-secondary"
          data-bs-dismiss="sl_offcanvas"><?php echo esc_attr__('Close', 'asl_locator') ?></button>
      </div>
    </div>
  </div>
  <!-- Bulk Edit Offcanvas -->
