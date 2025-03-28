<div class="asl-p-cont asl-new-bg">
	<div class="hide">
		<svg xmlns="http://www.w3.org/2000/svg">
		  <symbol id="i-trash" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
		  		<title><?php echo esc_attr__('Trash','asl_locator') ?></title>
			    <path d="M28 6 L6 6 8 30 24 30 26 6 4 6 M16 12 L16 24 M21 12 L20 24 M11 12 L12 24 M12 6 L13 2 19 2 20 6" />
			</symbol>
			<symbol id="i-clock" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
		    <circle cx="16" cy="16" r="14" />
		    <path d="M16 8 L16 16 20 20" />
			</symbol>
			<symbol id="i-plus" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
		  	<title><?php echo esc_attr__('Add','asl_locator') ?></title>
		    <path d="M16 2 L16 30 M2 16 L30 16" />
			</symbol>
      <symbol id="i-chevron-top" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
          <path d="M30 20 L16 8 2 20" />
      </symbol>
      <symbol id="i-chevron-bottom" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
          <path d="M30 12 L16 24 2 12" />
      </symbol>
		</svg>
	</div>
	<div class="container">
		<div class="row asl-inner-cont">
			<div class="col-md-12">
				<div class="card p-0 mb-4">
					<h3 class="card-title"><?php echo esc_attr__('Edit Store','asl_locator') ?><?php echo \AgileStoreLocator\Helper::getLangControl(); ?></h3>
          <div class="card-body">
              <form id="frm-addstore">
              		<div class="row">
										<div class="col-md-8">
											<div class="alert alert-dismissable alert-danger hide">
												 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
												<h4><?php echo esc_attr__('Alert!','asl_locator') ?></h4> <strong><?php echo esc_attr__('Warning!','asl_locator') ?></strong><?php echo esc_attr__('Best check yourself ','asl_locator') ?><a href="#" class="alert-link"><?php echo esc_attr__('alert link','asl_locator') ?></a>
											</div>
										</div>
									</div>
              		<input type="hidden" id="update_id" value="<?php echo esc_attr($store->id) ?>" />
                  <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="txt_title"><?php echo esc_attr__('Title','asl_locator') ?></label>
                        <input type="text" id="txt_title" value="<?php echo esc_attr($store->title) ?>" name="data[title]" class="form-control">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="txt_website"><?php echo esc_attr__('Website','asl_locator') ?></label>
                        <input type="text" id="txt_website" value="<?php echo esc_attr($store->website) ?>" name="data[website]" placeholder="http://example.com" class="form-control">
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="txt_description"><?php echo esc_attr__('Description','asl_locator') ?></label>
                        <textarea id="txt_description" name="data[description]" rows="3"  placeholder="Enter Description" maxlength="500" class="input-medium form-control"><?php echo esc_attr($store->description); ?></textarea>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="txt_description_2"><?php echo esc_attr__('Additional Description','asl_locator') ?></label>
                        <textarea id="txt_description_2" name="data[description_2]" rows="3"  placeholder="Additional Description" maxlength="500" class="input-medium form-control"><?php echo esc_attr($store->description_2); ?></textarea>
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="txt_phone"><?php echo esc_attr__('Phone','asl_locator') ?></label>
                        <input type="text" id="txt_phone" value="<?php echo esc_attr($store->phone) ?>" name="data[phone]" class="form-control">
                        
                    </div>
                    
                    <div class="col-md-6 form-group mb-3">
                        <label for="txt_fax"><?php echo esc_attr__('Fax','asl_locator') ?></label>
                        <input type="text"  id="txt_fax" value="<?php echo esc_attr($store->fax) ?>" name="data[fax]" class="form-control">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12 asl-tabs">
                      <div class="asl-tabs asl-store-tabs p-0 mb-4 mt-4">
                       <div class="asl-tabs-body">
                          <ul class="nav nav-pills justify-content-center">
                             <li class="active rounded"><a data-toggle="pill" href="#sl-store-address"><?php echo esc_attr__('Store Address','asl_locator') ?></a></li>
                             <li class="rounded"><a data-toggle="pill" href="#sl-other-details"><?php echo esc_attr__('Other Details','asl_locator') ?></a></li>
                             <li class="rounded"><a data-toggle="pill" href="#sl-stores-timings"><?php echo esc_attr__('Store Timing','asl_locator') ?></a></li>
                             <?php if(class_exists('ASL_WC_Instance') && class_exists('WooCommerce')): ?>
                             <li class="rounded"><a data-toggle="pill" href="#sl-woocommerce"><?php echo esc_attr__('WooCommerce','asl_locator') ?></a></li>
                            <?php endif; ?>
                          </ul>
                          <div class="tab-content">
                            <div id="sl-store-address" class="tab-pane in active p-0">
                              <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="txt_email"><?php echo esc_attr__('Email','asl_locator') ?></label>
                                    <input type="text" id="txt_email" value="<?php echo esc_attr($store->email) ?>" name="data[email]" class="form-control validate[custom[email]]">
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="txt_street"><?php echo esc_attr__('Street','asl_locator') ?></label>
                                    <input type="text" id="txt_street" value="<?php echo esc_attr($store->street) ?>" name="data[street]" class="form-control">
                                </div>
                                
                                <div class="col-md-6 form-group mb-3">
                                  <label for="txt_city"><?php echo esc_attr__('City','asl_locator') ?></label>
                                  <input type="text" id="txt_city" value="<?php echo esc_attr($store->city) ?>" name="data[city]" class="form-control validate[required]">
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                  <label for="txt_state"><?php echo esc_attr__('State','asl_locator') ?></label>
                                  <input type="text" id="txt_state" value="<?php echo esc_attr($store->state) ?>" name="data[state]" class="form-control">
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                  <label for="txt_postal_code"><?php echo esc_attr__('Postal Code','asl_locator') ?></label>
                                  <input type="text" id="txt_postal_code" value="<?php echo esc_attr($store->postal_code) ?>" name="data[postal_code]" class="form-control">
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                  <label for="txt_country"><?php echo esc_attr__('Country','asl_locator') ?></label>
                                  <select id="txt_country" style="width:100%" name="data[country]" class="custom-select validate[required]">
                                    <option value=""><?php echo esc_attr__('Select Country','asl_locator') ?></option>  
                                    <?php foreach($countries as $country): ?>
                                      <option <?php if($store->country == $country->id) echo 'selected' ?> value="<?php echo esc_attr($country->id) ?>"><?php echo esc_attr($country->country) ?></option>
                                    <?php endforeach ?>
                                  </select>
                                </div>
                                <div class="col-12">
                                  <div class="row">
                                    <div class="col-md-6">
                                      <div id="map_canvas" class="map_canvas"></div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group mb-3">
                                        <label for="asl_txt_lat"><?php echo esc_attr__('Latitude','asl_locator') ?></label>
                                        <input type="text" id="asl_txt_lat" value="<?php echo esc_attr($store->lat) ?>" name="data[lat]" value="0.0" readonly="true" class="form-control">
                                      </div>
                                      <div class="form-group mb-3">
                                        <label for="asl_txt_lng"><?php echo esc_attr__('Longitude','asl_locator') ?></label>
                                        <input type="text" id="asl_txt_lng" value="<?php echo esc_attr($store->lng) ?>" name="data[lng]" value="0.0" readonly="true" class="form-control">
                                      </div>
                                      <div class="form-group">
                                          <a id="lnk-edit-coord" class="btn float-right btn-warning"><?php echo esc_attr__('Change Coordinates','asl_locator') ?></a>
                                      </div>
                                    </div>
                                    <div class="col-12">
                                      <div class="dump-message"></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div id="sl-other-details" class="tab-pane p-0">
                              <div class="row">
                                <?php if(!defined('ASL_WC_PLUGIN')): ?>
                                <div class="col-md-6 form-group mb-3">
                                  <div class="form-group">
                                    <label for="ddl-asl-markers"><?php echo esc_attr__('Marker','asl_locator') ?></label>
                                      <div class="input-group" style="opacity: 0.5">
                                      <img src="<?php echo ASL_URL_PATH.'admin/images/ph-marker.png' ?>" alt="marker">
                                      </div>
                                  </div>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                  <label for="ddl-asl-logos"><?php echo esc_attr__('Logo','asl_locator') ?></label>
                                  <div class="input-group" style="opacity: 0.5">
                                    <img src="<?php echo ASL_URL_PATH.'admin/images/ph-logo.png' ?>" alt="marker">
                                  </div>
                                </div>
                                <?php endif; ?>
                                <div class="col-md-6 form-group mb-3">
                                  <label for="ddl_categories"><?php echo esc_attr__('Category','asl_locator') ?></label>
                                  <select name="ddl_categories"  id="ddl_categories" multiple class="chosen-select-width form-control">                     
                                    <?php foreach($category as $catego): ?>
                                      <option 
                                          <?php foreach($storecategory as $scategory ){ ?>
                                            <?php if($scategory->category_id == $catego->id) echo 'selected' ?>
                                          <?php }?>
                                          value="<?php echo esc_attr($catego->id) ?>"><?php echo esc_attr($catego->category_name) ?></option>
                                    <?php endforeach ?>
                                  </select>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                  <label for="txt-ordering"><?php echo esc_attr__('Priority Order','asl_locator') ?></label>
                                  <input type="number" id="txt-ordering" name="data[ordr]" value="<?php echo esc_attr($store->ordr) ?>" placeholder="0" class="form-control validate[integer]">
                                  <small class="form-text text-muted"><?php echo esc_attr__('Descending Order for the list, higher number on top.','asl_locator') ?></small>
                                </div>
                                <?php

                                  // Organize fields into sections based on their types
                                  foreach ($fields as $fieldName => $fieldData) {
                                    
                                    $field_value = isset($custom_data[$fieldName])? esc_attr($custom_data[$fieldName]): '';

                                    $field = new \AgileStoreLocator\Form\CustomField($fieldData, $field_value);

                                    echo '<div class="col-md-6 form-group mb-3">';
                                    echo $field->render('asl-custom');
                                    echo '</div>'; 
                                  }
                                ?>
                                <div class="col-md-6 form-group mb-3 align-items-center">
                                  <label for="sl-disabled"><?php echo esc_attr__('Disabled','asl_locator') ?></label>
                                  <div class="a-swith a-swith-alone">
                                    <input type="checkbox" class="cmn-toggle cmn-toggle-round" <?php if($store->is_disabled == 1) echo 'checked' ?> name="data[is_disabled]" id="sl-disabled">
                                    <label for="sl-disabled"></label>
                                    <span><?php echo esc_attr__('No','asl_locator') ?></span>
                                    <span><?php echo esc_attr__('Yes','asl_locator') ?></span>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div id="sl-stores-timings" class="tab-pane p-0">
                              <?php
                                $open_hours = json_decode($store->open_hours);
                              ?>
                              <div class="row">
                                <div class="col-12">
                                  <div class="float-right">
                                    <a id="asl-time-cp" class="btn btn-info btn-sm mb-3" title="<?php echo esc_attr__('Copy/Paste Monday Timing','asl_locator') ?>"><?php echo esc_attr__('Same Everyday','asl_locator') ?></a>
                                  </div>
                                </div>
                                <div class="col-12">
                                  <div class="table-responsive">
                                    <table class="table table-bordered table-stripped asl-time-details">
                                      <tbody>
                                        <tr>
                                          <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Monday','asl_locator') ?></span></td>
                                          <td colspan="3">
                                            <div class="asl-all-day-times" data-day="mon">
                                              <?php 
                                              if(isset($open_hours->mon) && is_array($open_hours->mon))
                                              foreach($open_hours->mon as $mon): $o_hour = explode(' - ', $mon); ?>
                                              <div class="form-group">
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[0]) ?>" class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]" placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[1]) ?>" class="form-control asl-end-time asltimepicker validate[required]" placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <span class="add-k-delete glyp-trash">
                                                  <svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>
                                                </span>
                                              </div>
                                              <?php endforeach; ?>
                                              <div class="asl-closed-lbl">
                                                <div class="a-swith">
                                                  <input id="cmn-toggle-0" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->mon && $open_hours->mon == '1') echo 'checked="checked"' ?>>
                                                  <label for="cmn-toggle-0"></label>
                                                  <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                  <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                </div>
                                              </div>
                                            </div>
                                          </td>
                                          <td>
                                            <span class="add-k-add glyp-add">
                                              <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                                            </span>
                                          </td>
                                          
                                        </tr>
                                        <tr>
                                          <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Tuesday','asl_locator') ?></span></td>
                                          <td colspan="3">
                                            <div class="asl-all-day-times" data-day="tue">
                                              <?php 
                                              if(is_array($open_hours->tue))
                                              foreach($open_hours->tue as $tue): $o_hour = explode(' - ', $tue); ?>
                                              <div class="form-group">
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[0]) ?>" class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]" placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[1]) ?>" class="form-control asl-end-time asltimepicker validate[required]" placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <span class="add-k-delete glyp-trash">
                                                  <svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>
                                                </span>
                                              </div>
                                              <?php endforeach; ?>
                                              <div class="asl-closed-lbl">
                                                <div class="a-swith">
                                                  <input id="cmn-toggle-1" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->tue && $open_hours->tue == '1') echo 'checked="checked"' ?>>
                                                  <label for="cmn-toggle-1"></label>
                                                  <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                  <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                </div>
                                              </div>
                                            </div>
                                          </td>
                                          <td>
                                            <span class="add-k-add glyp-add">
                                              <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                                            </span>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Wednesday','asl_locator') ?></span></td>
                                          <td colspan="3">
                                            <div class="asl-all-day-times" data-day="wed">
                                              <?php 
                                              if(is_array($open_hours->wed))
                                              foreach($open_hours->wed as $wed): $o_hour = explode(' - ', $wed); ?>
                                              <div class="form-group">
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[0]) ?>" class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]" placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[1]) ?>" class="form-control asl-end-time asltimepicker validate[required]" placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <span class="add-k-delete glyp-trash">
                                                  <svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>
                                                </span>
                                              </div>
                                              <?php endforeach; ?>
                                              <div class="asl-closed-lbl">
                                                <div class="a-swith">
                                                    <input id="cmn-toggle-2" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->wed && $open_hours->wed == '1') echo 'checked="checked"' ?>>
                                                    <label for="cmn-toggle-2"></label>
                                                    <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                    <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                </div>
                                              </div>
                                            </div>
                                          </td>
                                          <td>
                                            <span class="add-k-add glyp-add">
                                              <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                                            </span>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Thursday','asl_locator') ?></span></td>
                                          <td colspan="3">
                                            <div class="asl-all-day-times" data-day="thu">
                                              <?php 
                                              if(is_array($open_hours->thu))
                                              foreach($open_hours->thu as $thu): $o_hour = explode(' - ', $thu); ?>
                                              <div class="form-group">
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[0]) ?>" class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]" placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[1]) ?>" class="form-control asl-end-time asltimepicker validate[required]" placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <span class="add-k-delete glyp-trash">
                                                  <svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>
                                                </span>
                                              </div>
                                              <?php endforeach; ?>
                                              <div class="asl-closed-lbl">
                                                  <div class="a-swith">
                                                    <input id="cmn-toggle-3" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->thu && $open_hours->thu == '1') echo 'checked="checked"' ?>>
                                                    <label for="cmn-toggle-3"></label>
                                                    <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                    <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                  </div>
                                                </div>
                                              </div>
                                          </td>
                                          <td>
                                            <span class="add-k-add glyp-add">
                                              <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                                            </span>
                                          </td>
                                          
                                        </tr>
                                        <tr>
                                          <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Friday','asl_locator') ?></span></td>
                                          <td colspan="3">
                                            <div class="asl-all-day-times" data-day="fri">
                                              <?php 
                                              if(is_array($open_hours->fri))
                                              foreach($open_hours->fri as $fri): $o_hour = explode(' - ', $fri); ?>
                                              <div class="form-group">
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[0]) ?>" class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]" placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[1]) ?>" class="form-control asl-end-time asltimepicker validate[required]" placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <span class="add-k-delete glyp-trash">
                                                  <svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>
                                                </span>
                                              </div>
                                              <?php endforeach; ?>
                                              <div class="asl-closed-lbl">
                                                <div class="a-swith">
                                                      <input id="cmn-toggle-4" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->fri && $open_hours->fri == '1') echo 'checked="checked"' ?>>
                                                      <label for="cmn-toggle-4"></label>
                                                      <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                      <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                  </div>
                                              </div>
                                            </div>
                                          </td>
                                          <td>
                                            <span class="add-k-add glyp-add">
                                              <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                                            </span>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Saturday','asl_locator') ?></span></td>
                                          <td colspan="3">
                                            <div class="asl-all-day-times" data-day="sat">
                                              <?php 
                                              if(is_array($open_hours->sat))
                                              foreach($open_hours->sat as $sat): $o_hour = explode(' - ', $sat); ?>
                                              <div class="form-group">
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[0]) ?>" class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]" placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[1]) ?>" class="form-control asl-end-time asltimepicker validate[required]" placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <span class="add-k-delete glyp-trash">
                                                  <svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>
                                                </span>
                                              </div>
                                              <?php endforeach; ?>
                                              <div class="asl-closed-lbl">
                                                  <div class="a-swith">
                                                    <input id="cmn-toggle-5" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->sat && $open_hours->sat == '1') echo 'checked="checked"' ?>>
                                                    <label for="cmn-toggle-5"></label>
                                                    <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                    <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                  </div>
                                              </div>
                                            </div>
                                          </td>
                                          <td>
                                            <span class="add-k-add glyp-add">
                                              <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                                            </span>
                                          </td>
                                          
                                        </tr>
                                        <tr>
                                          <td colspan="1"><span class="lbl-day"><?php echo esc_attr__('Sunday','asl_locator') ?></span></td>
                                          <td colspan="3">
                                            <div class="asl-all-day-times" data-day="sun">
                                              <?php 
                                              if(is_array($open_hours->sun))
                                              foreach($open_hours->sun as $sun): $o_hour = explode(' - ', $sun); ?>
                                              <div class="form-group">
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[0]) ?>" class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]" placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <div class="input-group bootstrap-asltimepicker">
                                                  <input type="text" value="<?php echo esc_attr($o_hour[1]) ?>" class="form-control asl-end-time asltimepicker validate[required]" placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                  <span class="input-group-append add-on"><span class="input-group-text"><svg width="16" height="16"><use xlink:href="#i-clock"></use></svg></span></span>
                                                </div>
                                                <span class="add-k-delete glyp-trash">
                                                  <svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>
                                                </span>
                                              </div>
                                              <?php endforeach; ?>
                                              <div class="asl-closed-lbl">
                                                  <div class="a-swith">
                                                        <input id="cmn-toggle-6" class="cmn-toggle cmn-toggle-round" type="checkbox" <?php if($open_hours->sun && $open_hours->sun == '1') echo 'checked="checked"' ?>>
                                                        <label for="cmn-toggle-6"></label>
                                                        <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                        <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                    </div>
                                                </div>
                                              </div>
                                          </td>
                                          <td>
                                            <span class="add-k-add glyp-add">
                                              <svg width="16" height="16"><use xlink:href="#i-plus"></use></svg>
                                            </span>
                                          </td>
                                          
                                        </tr>
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <?php if(class_exists('ASL_WC_Instance')): ?>
                            <div id="sl-woocommerce" class="tab-pane p-0">
                              <?php ASLWC\Admin\StoreSetting::storeEditForm($store->id); ?>
                            </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                  	<div class="col-12 mt-3">
                  		<button type="button" class="float-right btn btn-success mrg-r-10" data-loading-text="<?php echo esc_attr__('Saving Store...','asl_locator') ?>" data-completed-text="<?php echo esc_attr__('Store Saved','asl_locator') ?>" id="btn-asl-add"><?php echo esc_attr__('Update Store','asl_locator') ?></button>
                  	</div>
                  </div>
              </form>
          </div>
        </div>
			</div>
		</div>
	</div>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">

	var asl_configs =  <?php echo wp_json_encode($all_configs); ?>;
	var ASL_Instance = {
		url: '<?php echo ASL_UPLOAD_URL; ?>',
    plugin_url: '<?php echo ASL_URL_PATH; ?>',
		sideurl: '<?php echo get_site_url();?>'
	};
  var asl_logos   = <?php echo wp_json_encode($logos); ?>;
	
  window.addEventListener("load", function() {
	asl_engine.pages.edit_store(<?php echo wp_json_encode($store) ?>);
  });
</script>
