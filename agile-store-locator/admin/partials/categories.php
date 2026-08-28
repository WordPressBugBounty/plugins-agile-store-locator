<!-- Container -->
<div class="asl-p-cont asl-new-bg asl-admin-grid-page asl-grid-categories">
    <div class="hide">
        <svg xmlns="http://www.w3.org/2000/svg">
            <symbol id="i-plus" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Add','asl_locator') ?></title>
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
            <svg id="i-alert" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Warning','asl_locator') ?></title>
                <path d="M16 3 L30 29 2 29 Z M16 11 L16 19 M16 23 L16 25" />
            </svg>
            <symbol id="i-info" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
            </symbol>
            <symbol id="asl-admin-icon-categories" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M4 8.5h9l2.5 3H28v14H4z" />
                <path d="M4 11.5v-5h9l2 2h8" />
            </symbol>
            <symbol id="asl-admin-icon-external" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M14 4h6v6M20 4l-9 9M20 13v7H4V4h7" />
            </symbol>
        </svg>
    </div>
    <div class="container sl-manage-cat-page">
        <div class="row asl-inner-cont">
            <div class="col-md-12">
                <div class="card p-0 mb-4 asl-grid-shell">
                    <header class="asl-admin-page-header">
                        <span class="asl-admin-page-header__icon" aria-hidden="true"><svg><use href="#asl-admin-icon-categories"></use></svg></span>
                        <div class="asl-admin-page-header__copy">
                            <h3><?php echo esc_html__('Manage Categories','asl_locator') ?></h3>
                            <p><?php echo esc_html__('Make categories and group stores.', 'asl_locator') ?></p>
                        </div>
                        <div class="asl-admin-page-header__actions">
                            <?php echo \AgileStoreLocator\Helper::getLangControl(); ?>
                            <a target="_blank" rel="noopener noreferrer" class="asl-admin-page-header__guide" href="https://agilestorelocator.com/wiki/manage-categories/">
                                <?php echo esc_html__('Guide', 'asl_locator') ?><svg aria-hidden="true"><use href="#asl-admin-icon-external"></use></svg>
                            </a>
                        </div>
                    </header>
                    <div class="card-body asl-grid-content">
                                    <?php if(!is_writable(ASL_UPLOAD_DIR.'svg')): ?>
                                    <h3 class="alert alert-danger" style="font-size: 14px">
                                    <?php echo esc_attr__('Directory is not writable, Category Image Upload will Fail, Make directory writable.','asl_locator') ?>:<br> 
                                    <?php echo ASL_UPLOAD_DIR.'svg' ?>
                                    </h3>
                                    <?php endif; ?>

                                    <div class="asl-grid-notice">
                                        <span aria-hidden="true"><svg><use href="#i-info"></use></svg></span>
                                        <p><?php echo esc_html__('Categories let you group stores and help visitors narrow their results.','asl_locator') ?></p>
                                    </div>

                                    <div class="asl-grid-toolbar">
                                        <p><?php echo esc_html__('Use the filters below each column to find specific categories.','asl_locator') ?></p>
                                        <div>
                                            <button type="button" id="btn-asl-delete-all" class="btn asl-grid-btn asl-grid-btn--danger"><i><svg><use href="#i-trash"></use></svg></i><?php echo esc_html__('Delete Selected','asl_locator') ?></button>
                                            <button type="button" id="btn-asl-new-c" class="btn asl-grid-btn asl-grid-btn--primary"><i><svg><use href="#i-plus"></use></svg></i><?php echo esc_html__('New Category','asl_locator') ?></button>
                                        </div>
                                    </div>

                                    <div class="asl-grid-table-card">
                                      <div class="table-responsive asl-grid-table-scroll">
                                        <table id="tbl_categories" class="table asl-grid-data-table">
                                            <thead>
                                                <tr class="asl-grid-filter-row">
                                                    <th aria-hidden="true"></th>
                                                    <th><label><?php echo esc_html__('Search ID','asl_locator') ?><input type="text" class="form-control" data-id="id" placeholder="<?php echo esc_attr__('Enter ID','asl_locator') ?>" /></label></th>
                                                    <th><label><?php echo esc_html__('Search Name','asl_locator') ?><input type="text" class="form-control" data-id="category_name" placeholder="<?php echo esc_attr__('Enter name','asl_locator') ?>" /></label></th>
                                                    <th><label><?php echo esc_html__('Search Parent','asl_locator') ?><input type="text" class="form-control" data-id="parent_id" placeholder="<?php echo esc_attr__('Enter parent ID','asl_locator') ?>" /></label></th>
                                                    <th><label><?php echo esc_html__('Search Order','asl_locator') ?><input type="text" class="form-control" data-id="ordr" placeholder="<?php echo esc_attr__('Enter order ID','asl_locator') ?>" /></label></th>
                                                    <th><label><?php echo esc_html__('Search Icon','asl_locator') ?><input type="text" class="form-control" data-id="icon" placeholder="<?php echo esc_attr__('Enter icon name','asl_locator') ?>" /></label></th>
                                                    <th><label><?php echo esc_html__('Search Date','asl_locator') ?><input type="text" class="form-control" data-id="created_on" placeholder="<?php echo esc_attr__('Enter date','asl_locator') ?>" /></label></th>
                                                    <th aria-hidden="true"></th>
                                                </tr>
                                                <tr>
                                                    <th scope="col"><a
                                                            class="select-all"><?php echo esc_attr__('Select All','asl_locator') ?></a>
                                                    </th>
                                                    <th scope="col" class="text-start">
                                                        <?php echo esc_attr__('Category ID','asl_locator') ?></th>
                                                    <th scope="col"><?php echo esc_attr__('Name','asl_locator') ?>
                                                    </th>
                                                    <th scope="col"><?php echo esc_attr__('Parent','asl_locator') ?>
                                                    </th>
                                                    <th scope="col"><?php echo esc_attr__('Order ID','asl_locator') ?></th>
                                                    <th scope="col">
                                                        <?php echo esc_attr__('Icon','asl_locator') ?></th>
                                                    <th scope="col">
                                                        <?php echo esc_attr__('Created On','asl_locator') ?></th>
                                                    <th class="text-center" scope="col">
                                                        <?php echo esc_attr__('Action','asl_locator') ?>&nbsp;</th>
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
                <form id="frm-updatecategory" name="frm-updatecategory">
                    <div class="smodal-header">
                        <h5 class="smodal-title"><?php echo esc_attr__('Update Category','asl_locator') ?></h5>
                        <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="smodal-body">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label
                                    class="control-label"><?php echo esc_attr__('Category ID','asl_locator') ?></label>
                                <input type="text" class="form-control" readonly="readonly" name="data[category_id]"
                                    id="update_category_id_input">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="txt_name"
                                    class="control-label"><?php echo esc_attr__('Name','asl_locator') ?></label>
                                <input type="text" class="form-control validate[required]" name="data[category_name]"
                                    id="update_category_name">
                            </div>
                            <div class="col-md-12 form-group mb-3 sl-complx">
                                <label for="update_parent_id"
                                    class="control-label"><?php echo esc_attr__('Parent','asl_locator') ?></label>
                                <select name="data[parent_id]" id="update_parent_id"
                                    class="form-control validate[required]"></select>
                            </div>
                            <div class="col-md-12 form-group mb-3 sl-complx">
                                <label for="update_category_ordr"
                                    class="control-label"><?php echo esc_attr__('Order','asl_locator') ?></label>
                                <input type="number" class="form-control validate[required]" name="data[ordr]"
                                    id="update_category_ordr">
                            </div>
                            <div class="col-md-12 form-group mb-3" id="updatecategory_image">
                                <img src="" id="update_category_icon" alt="" data-id="same"
                                    style="max-width: 80px;max-height: 80px" />
                                <button type="button" class="btn btn-secondary"
                                    id="change_image"><?php echo esc_attr__('Change','asl_locator') ?></button>
                            </div>

                            <div class="col-md-12 form-group mb-3" style="display:none" id="updatecategory_editimage">
                                <div class="input-group" id="drop-zone">
                                    <input type="file" class="form-control"
                                    name="files"
                                    id="file-img-1" />
                                    <span
                                    class="input-group-text"><?php echo esc_attr__('Image','asl_locator') ?></span>
                                    <!-- <div class="input-group-prepend">
                                        style="width:98%;opacity:0;position:absolute;top:0;left:0" 
                                    </div>
                                    <div class="custom-file">
                                        <label class="custom-file-label"
                                            for="file-img-1"><?php echo esc_attr__('File Path...','asl_locator') ?></label>
                                    </div> -->
                                </div>

                                <div class="form-group">
                                    <div class="progress hideelement" style="display:none" id="progress_bar_">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="60"
                                            aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                            <span style="position:relative" class="sr-only">0% Complete</span>
                                        </div>
                                    </div>
                                </div>
                                <ul></ul>
                            </div>
                            <p id="message_update"></p>
                        </div>
                        <div class="smodal-footer">
                            <button class="btn btn-primary btn-start mrg-r-15" id="btn-asl-update-categories"
                                type="button"
                                data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>"><?php echo esc_attr__('Update Category','asl_locator') ?></button>
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="smodal"><?php echo esc_attr__('Close','asl_locator') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- asl-cont end-->

    <div class="smodal fade asl-category-modal" id="asl-add-modal" role="dialog" aria-labelledby="asl-add-category-title" aria-hidden="true">
        <div class="smodal-dialog" role="document">
            <div class="smodal-content">
                <form id="frm-addcategory" name="frm-addcategory">
                    <div class="smodal-header">
                        <div class="asl-smodal-heading">
                            <span class="asl-smodal-heading-icon dashicons dashicons-category" aria-hidden="true"></span>
                            <div>
                                <h5 class="smodal-title" id="asl-add-category-title"><?php echo esc_attr__('Add New Category','asl_locator') ?></h5>
                                <p><?php echo esc_html__('Create a new category to organize your stores.', 'asl_locator'); ?></p>
                            </div>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="smodal-body">
                        <div class="alert alert-primary asl-smodal-notice" role="alert">
                            <span class="dashicons dashicons-info-outline" aria-hidden="true"></span>
                            <div>
                                <strong><?php echo esc_html__('Category icon is optional', 'asl_locator'); ?></strong>
                                <span><?php echo esc_html__('The default icon will be used when none is uploaded.', 'asl_locator'); ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label for="add_category_name"
                                    class="control-label"><?php echo esc_attr__('Name','asl_locator') ?></label>
                                <input type="text" class="form-control validate[required]"
                                    id="add_category_name" name="data[category_name]" placeholder="<?php echo esc_attr__('Enter category name', 'asl_locator'); ?>">
                            </div>
                            <div class="col-md-12 form-group mb-3 sl-complx">
                                <label for="parent_id"
                                    class="control-label"><?php echo esc_attr__('Parent','asl_locator') ?></label>
                                <select name="data[parent_id]" id="parent_id" class="form-control"></select>
                            </div>
                            <div class="col-md-12 form-group mb-3 sl-complx">
                                <label for="add_category_ordr"
                                    class="control-label"><?php echo esc_attr__('Order','asl_locator') ?></label>
                                <input type="number" class="form-control" name="data[ordr]"
                                    id="add_category_ordr" placeholder="<?php echo esc_attr__('Enter display order (optional)', 'asl_locator'); ?>">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <div class="input-group" id="drop-zone-1">
                                    <input type="file" class="form-control"
                                    name="files"
                                    id="file-img-2" accept="image/png,image/jpeg,image/gif,image/svg+xml" />
                                    <span
                                    class="input-group-text"><?php echo esc_attr__('Icon (Optional)','asl_locator') ?></span>
                                    <!-- <div class="input-group-prepend">
                                        style="width:98%;opacity:0;position:absolute;top:0;left:0" 
                                    </div>
                                    <div class="custom-file">
                                        <label class="custom-file-label"
                                            for="file-img-2"><?php echo esc_attr__('File Path...','asl_locator') ?></label>
                                    </div> -->
                                </div>
                            </div>

                            <div class="col-md-12 form-group mb-3">
                                <div class="progress hideelement" style="display:none" id="progress_bar">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                        aria-valuemax="100" style="width:0%;">
                                        <span style="position:relative" class="sr-only">0% Complete</span>
                                    </div>
                                </div>
                            </div>
                            <ul></ul>
                            <p id="message_upload" class="alert alert-warning hide"></p>
                        </div>
                    </div>
                    <div class="smodal-footer">
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="smodal"><?php echo esc_attr__('Cancel','asl_locator') ?></button>
                                <button class="btn btn-primary btn-start" id="btn-asl-add-categories"
                                    type="button"
                                    data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>"><?php echo esc_attr__('Add Category','asl_locator') ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>








<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
    manage_stores_url: '<?php echo admin_url().'admin.php?page=manage-agile-store' ?>&categories=',
    url: '<?php echo ASL_UPLOAD_URL ?>'
};

window.addEventListener("load", function() {
    asl_engine.pages.manage_categories();
});
</script>
