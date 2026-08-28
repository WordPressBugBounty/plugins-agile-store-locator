<?php
  $added_custom_fields = \AgileStoreLocator\Helper::get_setting('fields');
  $added_custom_fields = $added_custom_fields ? $added_custom_fields : '{}';
  $added_custom_fields = json_decode($added_custom_fields);
  if (is_object($added_custom_fields)) {
    $added_custom_fields = array_values(get_object_vars($added_custom_fields));
  } elseif (!is_array($added_custom_fields)) {
    $added_custom_fields = [];
  }
?>
<!-- Container -->
<div class="asl-p-cont asl-new-bg asl-admin-grid-page asl-grid-stores">
    <div class="hide">
        <svg xmlns="http://www.w3.org/2000/svg">
            <symbol id="i-plus" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Add', 'asl_locator') ?></title>
                <path d="M16 2 L16 30 M2 16 L30 16" />
            </symbol>
            <symbol id="i-clipboard" fill="currentColor" viewBox="0 0 17 17">
                <title><?php echo esc_attr__('Duplicate','asl_locator') ?></title>
                <path fill-rule="evenodd"
                    d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
            </symbol>
            <symbol id="i-clock" viewBox="0 0 32 32" width="16" height="17" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <circle cx="16" cy="16" r="14" />
                <path d="M16 8 L16 16 20 20" />
            </symbol>
            <symbol id="i-trash" viewBox="0 0 32 32" fill="none" stroke="currentcolor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2">>
                <title><?php echo esc_attr__('Trash','asl_locator') ?></title>
                <path
                    d="M28 6 L6 6 8 30 24 30 26 6 4 6 M16 12 L16 24 M21 12 L20 24 M11 12 L12 24 M12 6 L13 2 19 2 20 6" />
            </symbol>
            <symbol id="i-chevron-top" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M30 20 L16 8 2 20" />
            </symbol>
            <symbol id="i-chevron-bottom" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M30 12 L16 24 2 12" />
            </symbol>
            <symbol id="i-edit" fill="currentColor" viewBox="0 0 17 17">
                <title><?php echo esc_attr__('Edit','asl_locator') ?></title>
                <path
                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                <path fill-rule="evenodd"
                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
            </symbol>
            <symbol id="i-info" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M16 14 L16 23 M16 8 L16 10" />
                <circle cx="16" cy="16" r="14" />
            </symbol>
            <symbol id="i-geo-location" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                </path>
                <circle cx="12" cy="10" r="3"></circle>
            </symbol>
            <symbol id="i-gear" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                <path
                    d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                <path
                    d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
            </symbol>
            <symbol id="asl-admin-icon-store" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M5 13h22v15H5zM3 13l3-8h20l3 8" />
                <path d="M3 13a4 4 0 0 0 7 2 4 4 0 0 0 6 0 4 4 0 0 0 6 0 4 4 0 0 0 7-2M10 28v-8h7v8" />
            </symbol>
            <symbol id="asl-admin-icon-external" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M14 4h6v6M20 4l-9 9M20 13v7H4V4h7" />
            </symbol>
        </svg>
    </div>
    <div class="container sl-manage-store-page">
        <div class="row asl-inner-cont">
            <div class="col-md-12">
                <div class="card p-0 mb-4 asl-grid-shell">
                    <header class="asl-admin-page-header">
                        <span class="asl-admin-page-header__icon" aria-hidden="true"><svg><use href="#asl-admin-icon-store"></use></svg></span>
                        <div class="asl-admin-page-header__copy">
                            <h3>
                                <span><?php echo esc_attr__('Manage Stores', 'asl_locator');
                                //  Add the name
                                if (isset($_GET['categories'])) {
                                    echo ' (' . \AgileStoreLocator\Helper::get_category_name($_GET['categories']) . ')';
                                }
                                ?></span>
                            </h3>
                            <p>
                                <?php echo esc_attr__('You can add, edit, delete and organize store details.', 'asl_locator') ?>
                            </p>
                        </div>
                        <div class="asl-admin-page-header__actions">
                            <?php echo \AgileStoreLocator\Helper::getLangControl(); ?>
                            <a target="_blank" rel="noopener noreferrer" class="asl-admin-page-header__guide" href="https://agilestorelocator.com/wiki/manage-stores-locator-listing/">
                                <?php echo esc_html__('Guide', 'asl_locator') ?><svg aria-hidden="true"><use href="#asl-admin-icon-external"></use></svg>
                            </a>
                        </div>
                    </header>
                    <div class="card-body asl-grid-content">
                            <div class="asl-grid-notice">
                                <span aria-hidden="true"><svg><use href="#i-info"></use></svg></span>
                                <p><?php echo esc_html__('Search, organize, validate, and update all stores from one place.','asl_locator') ?></p>
                            </div>

                            <div class="asl-grid-toolbar asl-grid-store-toolbar">
                                <div class="asl-grid-store-actions">
                                    <button type="button" id="btn-asl-delete-all"
                                        class="btn asl-grid-btn asl-grid-btn--danger"><i class="mr-1"><svg width="12"
                                                height="12">
                                                <use xlink:href="#i-trash"></use>
                                            </svg></i><?php echo esc_attr__('Delete Selected', 'asl_locator') ?></button>
                                    <button type="button" id="btn-validate-coords"
                                        data-loading-text="<?php echo esc_attr__('Validating...', 'asl_locator') ?>"
                                        class="btn asl-grid-btn asl-grid-btn--secondary"><i class="mr-1"><svg
                                                width="12" height="12">
                                                <use xlink:href="#i-geo-location"></use>
                                            </svg></i><?php echo esc_attr__('Validate Coordinates', 'asl_locator') ?></button>
                                    <button type="button" id="btn-asl-bulk-edit"
                                        class="btn asl-grid-btn asl-grid-btn--secondary sl-complx" aria-controls="sl-bulk-edit"><i class="mr-1"><svg
                                                width="12" height="12">
                                                <use xlink:href="#i-edit"></use>
                                            </svg></i><?php echo esc_attr__('Bulk Edit', 'asl_locator') ?></button>
                                    <a href="<?php echo admin_url() . 'admin.php?page=create-agile-store' ?>"
                                        class="btn asl-grid-btn asl-grid-btn--primary"><i><svg width="13" height="13">
                                                <use xlink:href="#i-plus"></use>
                                            </svg></i><?php echo esc_attr__('New Store', 'asl_locator') ?></a>
                                </div>
                                <div class="asl-grid-store-controls row g-2 align-items-center">
                                    <div class="col-12 col-md-auto">
                                        <div class="input-group flex-nowrap w-100 asl-grid-status-control">
                                            <select class="form-select flex-grow-1" id="asl-ddl-status">
                                                <option value="1">
                                                    <?php echo esc_attr__('Enable', 'asl_locator') ?></option>
                                                <option value="0">
                                                    <?php echo esc_attr__('Disable', 'asl_locator') ?></option>
                                            </select>
                                            <div class="input-group-text">
                                                <button id="btn-change-status"
                                                    type="button"><?php echo esc_attr__('Change Status', 'asl_locator') ?></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-auto">
                                        <button class="btn w-100 asl-grid-btn asl-grid-btn--secondary show-hide-colm" data-bs-toggle="smodal"
                                            data-bs-target="#sl-fields-sh" type="button"><i class="mr-1"><svg width="12"
                                                    height="12">
                                                    <use xlink:href="#i-gear"></use>
                                                </svg></i><?php echo esc_attr__('Show/Hide Columns', 'asl_locator') ?></button>
                                    </div>
                                </div>
                            </div>
                            <?php if ($pending_stores > 0) : ?>
                            <div id="alert-pending-stores" class="alert alert-primary mt-3 mb-3" role="alert">
                                <?php echo esc_attr__('You have pending stores to approve them.', 'asl_locator') ?> <a
                                    id="btn-pending-stores" class="btn ml-md-2 btn-warning btn-sm"
                                    data-pending="<?php echo esc_attr__('Hide Pending Stores', 'asl_locator') ?>"
                                    data-all="<?php echo esc_attr__('Show Pending Stores', 'asl_locator') ?>"
                                    data-loading-text="<?php echo esc_attr__('Loading Stores', 'asl_locator') ?>"><span><?php echo esc_attr__('Show Pending Stores', 'asl_locator') ?></span>
                                    <i class="badge badge-light"><?php echo $pending_stores ?></i></a></div>
                            <?php endif; ?>

                            <div class="asl-grid-table-card">
                              <div class="table-responsive asl-grid-table-scroll">
                                <table id="tbl_stores" class="table asl-grid-data-table">
                                    <thead>
                                        <tr class="asl-grid-filter-row">
                                            <th aria-hidden="true"></th>
                                            <th aria-hidden="true"></th>

                                            <!-- Schedule Store -->
                                            <th>
                                                <label><?php echo esc_html__('Search Schedule', 'asl_locator') ?>
                                                <select class="form-control" data-id="scheduled">
                                                    <option value=""><?php echo esc_attr('All', 'asl_locator') ?>
                                                    </option>
                                                    <option value="1"><?php echo esc_attr('Scheduled', 'asl_locator') ?>
                                                    </option>
                                                    <option value="2"><?php echo esc_attr('Running', 'asl_locator') ?>
                                                    </option>
                                                    <option value="3"><?php echo esc_attr('Expired', 'asl_locator') ?>
                                                    </option>
                                                </select>
                                                </label>
                                            </th>

                                            <th><label><?php echo esc_html__('Search ID', 'asl_locator') ?><input type="text" class="form-control" data-id="id" placeholder="<?php echo esc_attr__('Enter ID', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Title', 'asl_locator') ?><input type="text" class="form-control" data-id="title" placeholder="<?php echo esc_attr__('Enter title', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Latitude', 'asl_locator') ?><input type="text" class="form-control" data-id="lat" placeholder="<?php echo esc_attr__('Enter latitude', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Longitude', 'asl_locator') ?><input type="text" class="form-control" data-id="lng" placeholder="<?php echo esc_attr__('Enter longitude', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Street', 'asl_locator') ?><input type="text" class="form-control" data-id="street" placeholder="<?php echo esc_attr__('Enter street', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search State', 'asl_locator') ?><input type="text" class="form-control" data-id="state" placeholder="<?php echo esc_attr__('Enter state', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search City', 'asl_locator') ?><input type="text" class="form-control" data-id="city" placeholder="<?php echo esc_attr__('Enter city', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Country', 'asl_locator') ?><input type="text" class="form-control" data-id="country" placeholder="<?php echo esc_attr__('Enter country', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Phone', 'asl_locator') ?><input type="text" class="form-control" data-id="phone" placeholder="<?php echo esc_attr__('Enter phone', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Email', 'asl_locator') ?><input type="text" class="form-control" data-id="email" placeholder="<?php echo esc_attr__('Enter email', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search URL', 'asl_locator') ?><input type="text" class="form-control" data-id="website" placeholder="<?php echo esc_attr__('Enter URL', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Postal Code', 'asl_locator') ?><input type="text" class="form-control" data-id="postal_code" placeholder="<?php echo esc_attr__('Enter postal code', 'asl_locator') ?>" /></label></th>
                                            <th>
                                                <label><?php echo esc_html__('Search Status', 'asl_locator') ?>
                                                <select data-id="is_disabled" class="form-control">
                                                    <option value=""><?php echo esc_attr__('All', 'asl_locator') ?>
                                                    </option>
                                                    <option value="1">
                                                        <?php echo esc_attr__('Disabled', 'asl_locator') ?></option>
                                                </select>
                                                </label>
                                            </th>
                                            <th aria-hidden="true"></th>
                                            <th><label><?php echo esc_html__('Search Marker ID', 'asl_locator') ?><input type="text" class="form-control" data-id="marker_id" placeholder="<?php echo esc_attr__('Enter marker ID', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Logo ID', 'asl_locator') ?><input type="text" class="form-control" data-id="logo_id" placeholder="<?php echo esc_attr__('Enter logo ID', 'asl_locator') ?>" /></label></th>
                                            <th><label><?php echo esc_html__('Search Date', 'asl_locator') ?><input type="text" class="form-control" data-id="created_on" placeholder="<?php echo esc_attr__('Enter date', 'asl_locator') ?>" /></label></th>
                                            <?php foreach($added_custom_fields as $custom_field) : ?>
                                            <th><label><?php echo esc_html($custom_field->label) ?><input type="text" class="form-control" data-id="<?php echo esc_attr($custom_field->name); ?>" placeholder="<?php echo esc_attr($custom_field->label) ?>" /></label></th>
                                            <?php endforeach; ?>
                                        </tr>
                                        <tr>
                                            <th><a
                                                    class="select-all"><?php echo esc_attr__('Select All', 'asl_locator') ?></a>
                                            </th>
                                            <th class="text-center">
                                                <?php echo esc_attr__('Actions', 'asl_locator') ?>&nbsp;</th>
                                            <th class="text-center">
                                                <?php echo esc_attr__('Schedule', 'asl_locator') ?>&nbsp;</th>
                                            <th><?php echo esc_attr__('Store ID', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Title', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Lat', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Lng', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Street', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('State', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('City', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Country', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Phone', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Email', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('URL', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Postal Code', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Status', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Categories', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Marker ID', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Logo ID', 'asl_locator') ?></th>
                                            <th><?php echo esc_attr__('Created On', 'asl_locator') ?></th>
                                            <?php foreach($added_custom_fields as $custom_field) : ?>
                                            <th><?php echo esc_attr__($custom_field->label, 'asl_locator') ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                              </div>
                            </div>

                        <div class="dump-message asl-dumper"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="smodal fade" tabindex="-1" id="sl-fields-sh" role="dialog">
        <div class="smodal-dialog" role="document">
            <div class="smodal-content">
                <form id="frm-fields-sh" name="frm-fields-sh">
                    <div class="smodal-header">
                        <h5 class="smodal-title"><?php echo esc_attr__('Columns Visiblity', 'asl_locator') ?></h5>
                        <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="smodal-body">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label
                                    for="ddl-fs-cntrl"><?php echo esc_attr__('Hidden Columns', 'asl_locator') ?></label>
                                <select id="ddl-fs-cntrl" multiple class="chosen-select-width form-control">
                                    <?php foreach ($field_columns as $col_key => $col_val) : ?>
                                    <option value="<?php echo esc_attr($col_key) ?>"><?php echo esc_attr($col_val) ?>
                                    </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="smodal-footer">
                        <button type="button" id="sl-btn-sh"
                            data-loading-text="<?php echo esc_attr__('Submitting ...', 'asl_locator') ?>"
                            class="btn btn-start btn-primary"><?php echo esc_attr__('Save', 'asl_locator') ?></button>
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="smodal"><?php echo esc_attr__('Close', 'asl_locator') ?></button>
                    </div>

                </form>
            </div>
        </div>
    </div>

  <!-- Store Schedule Modal -->
  <div class="smodal fade"  id="sl-schedule-store" role="dialog">
    <div class="smodal-dialog" role="document">
      <div class="smodal-content">
        <form id="frm-schedule-store" name="frm-schedule-store" class="sl-schedule-store-frm">
          <div class="smodal-header">
            <h5 class="smodal-title"><?php echo esc_attr('Schedule Store', 'asl_locator') ?></h5>
            <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="smodal-body">
            <div class="row">

              <div class="col-md-12 mb-3">
                <div class="form-group">
                  <label class="custom-control-label" for="ddl-fs-edate"><?php echo esc_attr('Start Date ', 'asl_locator') ?></label>
                  <div class="form-group-inner">
                    <input id="asl-sched-start-date" type="text" name="asl-sched-start-date" required="required" class="form-control">
                  </div>
                </div>
              </div>

              <div class="col-md-12 mb-3">
                <div class="form-group">
                  <label class="custom-control-label" for="ddl-fs-edate"><?php echo esc_attr('End Date', 'asl_locator') ?></label>
                  <div class="form-group-inner">
                    <input id="asl-sched-end-date" type="text" name="asl-sched-end-date" required="required" class="form-control">
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group d-lg-flex d-md-block disable_now">
                  <label class="custom-control-label me-2" for="ddl-fs-date-switch"><?php echo esc_attr('Disable', 'asl_locator') ?></label>
                  <div class="form-group-inner">
                    <label class="switch" for="ddl-fs-date-switch"><input type="checkbox" value="" class="custom-control-input" name="ddl-fs-date-switch" id="ddl-fs-date-switch"><span class="slider round"></span></label>
                  </div>
                </div>
              </div>

              <!-- Store ID -->
              <input type="text" name="store_id" id="store_id" value="" hidden="hidden" />

            </div>
          </div>

          <div class="smodal-footer">
            <button type="button" id="btn-schedule" data-loading-text="<?php echo esc_attr('Submitting ...', 'asl_locator') ?>" class="btn btn-start btn-primary btn-schedule"><?php echo esc_attr('Save', 'asl_locator') ?></button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="smodal"><?php echo esc_attr('Close', 'asl_locator') ?></button>
          </div>

        </form>
      </div>
    </div>
  </div>
  <!-- Store Schedule Modal -->

  <?php include ASL_PLUGIN_PATH.'admin/partials/bulk-edit-offcanvas.php'; ?>

</div>


<?php
  $dt_custom_columns = [];
  foreach ($added_custom_fields as $field) {
    $dt_custom_columns[] = ['data' => $field->name];
  }
?>

<!-- SCRIPTS -->
<script type="text/javascript">
// All config data
var asl_configs = <?php echo wp_json_encode($all_configs); ?>;
var dt_custom_columns = <?php echo json_encode($dt_custom_columns); ?>;
var asl_bulk_logos = <?php echo json_encode($logos); ?>;

var ASL_Instance = {
    manage_stores_url: '<?php echo admin_url() . 'admin.php?page=edit-agile-store&store_id=' ?>',
    url: '<?php echo ASL_UPLOAD_URL ?>'
};

var asl_hidden_columns = <?php echo (empty($hidden_fields)) ? '[]' : $hidden_fields; ?>;

window.addEventListener("load", function() {
    asl_engine.pages.manage_stores();

    //  When schedule is enabled
    if (asl_configs.store_schedule == '1') {
        asl_engine.pages.schedule_stores();
    }
});
</script>
