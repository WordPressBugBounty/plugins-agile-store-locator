<!-- Container -->
<div class="asl-p-cont asl-new-bg asl-admin-grid-page asl-grid-markers">
<div class="hide">
  <svg xmlns="http://www.w3.org/2000/svg">
    <symbol id="i-plus" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <title>Add</title>
        <path d="M16 2 L16 30 M2 16 L30 16" />
    </symbol>
     <symbol id="i-trash" viewBox="0 0 32 32" fill="none" stroke="currentcolor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2">>
                <title><?php echo esc_attr__('Trash','asl_locator') ?></title>
                <path
                    d="M28 6 L6 6 8 30 24 30 26 6 4 6 M16 12 L16 24 M21 12 L20 24 M11 12 L12 24 M12 6 L13 2 19 2 20 6" />
            </symbol>
            <symbol id="i-edit" fill="currentColor" viewBox="0 0 17 17">
                <title><?php echo esc_attr__('Edit','asl_locator') ?></title>
                <path
                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                <path fill-rule="evenodd"
                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
            </symbol>
            <symbol id="i-info" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M16 14 L16 23 M16 8 L16 10" />
                <circle cx="16" cy="16" r="14" />
            </symbol>
            <symbol id="asl-admin-icon-marker" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M16 29s10-9.2 10-17A10 10 0 1 0 6 12c0 7.8 10 17 10 17Z" />
                <circle cx="16" cy="12" r="3.5" />
            </symbol>
            <symbol id="asl-admin-icon-external" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M14 4h6v6M20 4l-9 9M20 13v7H4V4h7" />
            </symbol>
  </svg>
</div>
  <div class="container sl-manage-marker-page">
    <div class="row asl-inner-cont">
      <div class="col-md-12">
        <div class="card p-0 mb-4 asl-grid-shell">
          <header class="asl-admin-page-header">
            <span class="asl-admin-page-header__icon" aria-hidden="true"><svg><use href="#asl-admin-icon-marker"></use></svg></span>
            <div class="asl-admin-page-header__copy">
              <h3><?php echo esc_html__('Manage Markers','asl_locator') ?></h3>
              <p><?php echo esc_html__('Create, customize and manage map markers for your store locator.','asl_locator') ?></p>
            </div>
            <a target="_blank" rel="noopener noreferrer" class="asl-admin-page-header__guide" href="https://agilestorelocator.com/wiki/manage-markers/">
              <?php echo esc_html__('Guide', 'asl_locator') ?><svg aria-hidden="true"><use href="#asl-admin-icon-external"></use></svg>
            </a>
          </header>
          <div class="card-body asl-grid-content">

          <?php if(!is_writable(ASL_UPLOAD_DIR.'icon')): ?>
            <h6  class="alert alert-danger" style="font-size: 14px"><?php echo ASL_UPLOAD_DIR.'icon' ?> <= <?php echo esc_attr__('Directory is not writable, marker image upload will fail, make directory writable.','asl_locator') ?></h6>
          <?php endif; ?>
            <div class="asl-grid-notice">
              <span aria-hidden="true"><svg><use href="#i-info"></use></svg></span>
              <p><?php echo esc_html__('Markers help your visitors identify different store types on the map.','asl_locator') ?></p>
              <a target="_blank" rel="noopener noreferrer" href="https://agilestorelocator.com/marker-generator-tool/">
                <svg aria-hidden="true"><use href="#asl-admin-icon-external"></use></svg><?php echo esc_html__('Generate Marker','asl_locator') ?>
              </a>
            </div>

            <div class="asl-grid-toolbar">
              <p><?php echo esc_html__('Use the filters below each column to find specific markers.','asl_locator') ?></p>
              <div>
                <button type="button" id="btn-asl-delete-all" class="btn asl-grid-btn asl-grid-btn--danger"><i><svg><use href="#i-trash"></use></svg></i><?php echo esc_html__('Delete Selected','asl_locator') ?></button>
                <button type="button" id="btn-asl-new-c" class="btn asl-grid-btn asl-grid-btn--primary"><i><svg><use href="#i-plus"></use></svg></i><?php echo esc_html__('Add New Marker','asl_locator') ?></button>
              </div>
            </div>

            <div class="asl-grid-table-card">
              <div class="table-responsive asl-grid-table-scroll">
                <table id="tbl_markers" class="table asl-grid-data-table">
                    <thead>
                      <tr class="asl-grid-filter-row">
                        <th aria-hidden="true"></th>
                        <th><label><?php echo esc_html__('Search ID','asl_locator') ?><input class="form-control" type="text" data-id="id" placeholder="<?php echo esc_attr__('Enter ID','asl_locator') ?>" /></label></th>
                        <th><label><?php echo esc_html__('Search Name','asl_locator') ?><input class="form-control" type="text" data-id="marker_name" placeholder="<?php echo esc_attr__('Enter name','asl_locator') ?>" /></label></th>
                        <th><label><?php echo esc_html__('Search Icon','asl_locator') ?><input class="form-control" type="text" data-id="icon" placeholder="<?php echo esc_attr__('Enter icon name','asl_locator') ?>" /></label></th>
                        <th aria-hidden="true"></th>
                      </tr>
                      <tr>
                        <th align="center"><a class="select-all"><?php echo esc_attr__('Select All','asl_locator') ?></a></th>
                        <th align="center"><?php echo esc_attr__('Marker ID','asl_locator') ?></th>
                        <th align="center"><?php echo esc_attr__('Name','asl_locator') ?></th>
                        <th align="center"><?php echo esc_attr__('Icon','asl_locator') ?></th>
                        <th class="text-center"><?php echo esc_attr__('Action','asl_locator') ?>&nbsp;</th>
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

<!-- Edit Alert -->
<div class="smodal fade" id="asl-update-modal" role="dialog">
  <div class="smodal-dialog" role="document">
    <div class="smodal-content">
      <form id="frm-updatemarker" name="frm-updatemarker">
      <div class="smodal-header">
        <h5 class="smodal-title"><?php echo esc_attr__('Update Marker','asl_locator') ?></h5>
        <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="smodal-body p-0">
        <div class="row p-3">
          <div class="col-md-12 form-group mb-3">
            <label class="control-label">Marker ID</label>
            <input type="text" class="form-control" readonly="readonly"  name="data[marker_id]" id="update_marker_id_input">
          </div>
          <div class="col-md-12 form-group mb-3">
            <label for="txt_name"  class="control-label"><?php echo esc_attr__('Name','asl_locator') ?></label>
            <input type="text" class="form-control validate[required]" name="data[marker_name]" id="update_marker_name">
          </div>
          <div class="col-md-12 form-group mb-3" id="updatemarker_image">
             <img  src="" id="update_marker_icon" alt="" data-id="same" style="max-width: 80px;max-height: 80px"/>
             <button type="button" class="btn btn-secondary" id="change_image"><?php echo esc_attr__('Change','asl_locator') ?></button>
          </div>

          <div class="col-md-12 form-group mb-3" style="display:none" id="updatemarker_editimage">                  
            <div class="input-group" id="drop-zone">
              <input type="file" accept=".jpg,.png,.jpeg,.gif,.JPG,.svg" class="form-control" name="files" id="file-logo-1" />
              <span class="input-group-text"><?php echo esc_attr__('Icon','asl_locator') ?></span>
              <!-- <div class="input-group-prepend">
              </div>
              <div class="custom-file">
                <label  class="custom-file-label" for="file-logo-1"><?php echo esc_attr__('File Path...','asl_locator') ?></label>
              </div> -->
            </div>
            <div class="form-group">
              <div class="progress hideelement" style="display:none" id="progress_bar_">
                  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                      <span style="position:relative" class="sr-only">0% Complete</span>
                  </div>
              </div>
            </div>
            <ul></ul>
          </div>
          <p id="message_update"></p>
        </div>
        <div class="smodal-footer">
          <button class="btn btn-primary btn-start mrg-r-15" id="btn-asl-update-markers"   type="button" data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>"><?php echo esc_attr__('Update Marker','asl_locator') ?></button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="smodal"><?php echo esc_attr__('Cancel','asl_locator') ?></button>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="smodal fade" id="asl-add-modal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="smodal-dialog" role="document">
    <div class="smodal-content">
      <form id="frm-addmarker" name="frm-upload-marker">
      <div class="smodal-header">
        <h5 class="smodal-title"><?php echo esc_attr__('Upload Marker','asl_locator') ?></h5>
        <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="smodal-body">
        <div class="row ">
          <div class="col-md-12 form-group mb-3 ">
              <label for="txt_marker-name"><?php echo esc_attr__('Marker Name','asl_locator') ?></label>
              <input type="text" id="txt_marker-name" name="data[marker_name]" class="form-control">
          </div>
          <div class="col-md-12 form-group mb-3" id="drop-zone-2">
            
             <div class="input-group">
               <input name="files" type="file" class="form-control" accept=".jpg,.png,.jpeg,.gif,.JPG,.svg" id="file-logo-2">
               <span class="input-group-text"><?php echo esc_attr__('Icon','asl_locator') ?></span>
               <!-- <label  class="input-group-text" for="file-logo-2"><?php echo esc_attr__('File Path...','asl_locator') ?></label> -->
               <!-- <div class="input-group-prepend">
              </div>
              <div class="custom-file">
              </div> -->
            </div>
          </div>
          <div class="form-group">
            <div class="progress hideelement progress_bar_" style="display:none">
              <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                <span style="position:relative" class="sr-only">0% Complete</span>
              </div>
            </div>
          </div>
          <ul></ul>
          <div class="col-12"><p id="message_upload_1" class="alert alert-warning hide"></p></div>
        </div>
      </div>
      <div class="smodal-footer">
        <div class="row">
          <div class="col-12 p-0">
            <button type="button" data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>" class="btn btn-start btn-primary"><?php echo esc_attr__('Upload','asl_locator') ?></button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="smodal"><?php echo esc_attr__('Close','asl_locator') ?></button>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>

</div>
<!-- asl-cont end-->


<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo ASL_UPLOAD_URL ?>'
};
window.addEventListener("load", function() {
asl_engine.pages.manage_markers();
});
</script>
