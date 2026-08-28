<?php $asl_upgrade_url = defined('ASL_UPGRADE_URL') ? ASL_UPGRADE_URL : 'https://agilestorelocator.com/pricing/'; ?>
<!-- Container -->
<div class="asl-p-cont asl-new-bg asl-attributes-cont asl-admin-grid-page asl-grid-attributes">
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
            <symbol id="i-info" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M16 14v9M16 9h.01" />
                <circle cx="16" cy="16" r="14" />
            </symbol>
            <symbol id="asl-admin-icon-attributes" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M6 8h20M6 16h20M6 24h20" />
                <circle cx="11" cy="8" r="3" fill="currentColor" stroke="none" />
                <circle cx="21" cy="16" r="3" fill="currentColor" stroke="none" />
                <circle cx="14" cy="24" r="3" fill="currentColor" stroke="none" />
            </symbol>
        </svg>
    </div>
    <div class="container">
        <div class="row asl-inner-cont">
            <div class="col-md-12 ">
                <section class="asl-pro-locked-section asl-pro-locked-panel asl-settings-pro-lock asl-attributes-pro-lock" aria-labelledby="asl-attributes-lock-title">
                    <div class="asl-pro-lock-overlay">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        <strong id="asl-attributes-lock-title"><?php esc_html_e('Manage Attributes is a Pro feature', 'asl_locator'); ?></strong>
                        <span><?php esc_html_e('Upgrade to create and manage searchable store attributes.', 'asl_locator'); ?></span>
                        <a href="<?php echo esc_url($asl_upgrade_url); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Upgrade to Pro', 'asl_locator'); ?></a>
                    </div>
                    <div class="asl-pro-locked-preview" aria-hidden="true" inert>
                <div class="asl-tabs mb-4 mt-4 card p-0 asl-grid-shell">
                        <header class="asl-admin-page-header">
                            <span class="asl-admin-page-header__icon" aria-hidden="true"><svg><use href="#asl-admin-icon-attributes"></use></svg></span>
                            <div class="asl-admin-page-header__copy">
                                <h3><?php echo esc_html__('Manage Attributes','asl_locator') ?></h3>
                                <p><?php echo esc_html__('Add and manage extra features for stores.','asl_locator') ?></p>
                            </div>
                            <div class="asl-admin-page-header__actions">
                            <?php echo \AgileStoreLocator\Helper::getLangControl(); ?>
                            </div>
                        </header>
                    <div class="asl-tabs-body asl-grid-content">
                        <div class="asl-grid-notice">
                            <span aria-hidden="true"><svg><use href="#i-info"></use></svg></span>
                            <p><?php echo esc_html__('Attributes add searchable store details; use the tabs to manage each attribute type.','asl_locator') ?></p>
                        </div>
                        <ul class="nav nav-pills justify-content-center">
                            <?php 
                     $counter = 1;
                     $ddl_controls = \AgileStoreLocator\Model\Attribute::get_controls();
                     foreach($ddl_controls as $control_key => $control_page):
                     ?>
                            <li class="rounded <?php echo ($counter == 1 ) ? 'active' : '' ?>"><a data-toggle="pill"
                                    href="#<?php echo $control_key ?>_tab"><?php echo asl_esc_lbl("manage"); ?>
                                    <?php echo $control_page['label']; ?></a>
                            </li>
                            <?php 
                     $counter++;
                     endforeach; 
                     ?>

                        </ul>
                        <div class="tab-content">
                            <?php 
                        $counter = 1;
                        $ddl_controls = \AgileStoreLocator\Model\Attribute::get_controls();
                        foreach($ddl_controls as $control_key => $control_page):
                     ?>
                            <div id="<?php echo $control_key ?>_tab"
                                class="tab-pane in <?php echo ($counter == 1 ) ? 'active' : '' ?>">
                                <div class="asl-attr-tab" data-tab-single="<?php echo $control_page['label'] ?>"
                                    data-tab-plural="<?php echo $control_page['plural'] ?>"
                                    data-tab-name="<?php echo $control_key ?>"
                                    data-tab-title="<?php echo $control_page['field'] ?>">
                                    <div class="asl-attr-listing">
                                        <div class="asl-grid-toolbar">
                                            <p><?php echo esc_html__('Use the filters below each column to find specific attributes.','asl_locator') ?></p>
                                            <div>
                                                <button type="button" id="btn-asl-delete-all"
                                                    class="btn btn-asl-delete-all asl-grid-btn asl-grid-btn--danger">
                                                    <i>
                                                        <svg width="13" height="13">
                                                            <use xlink:href="#i-trash"></use>
                                                        </svg>
                                                    </i>
                                                    <?php echo esc_attr__('Delete Selected','asl_locator') ?>
                                                </button>
                                                <button type="button" id="btn-asl-new-attr"
                                                    class="btn btn-asl-new-attr asl-grid-btn asl-grid-btn--primary">
                                                    <i>
                                                        <svg style="margin-top:-3px;"  width="13" height="13">
                                                            <use xlink:href="#i-plus"></use>
                                                        </svg>
                                                    </i>
                                                    <?php echo esc_attr__('Add New','asl_locator') ?>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="asl-attribute-table-wrap asl-grid-table-card">
                                            <table class="attribute-table table asl-grid-data-table">
                                                <thead>
                                                    <tr class="asl-grid-filter-row">
                                                        <th aria-hidden="true"></th>
                                                        <th><label><?php echo esc_html__('Search ID','asl_locator') ?><input type="text" class="form-control" data-id="id" placeholder="<?php echo esc_attr__('Enter ID','asl_locator') ?>" /></label></th>
                                                        <th><label><?php echo esc_html__('Search Name','asl_locator') ?><input type="text" class="form-control" data-id="name" placeholder="<?php echo esc_attr__('Enter name','asl_locator') ?>" /></label></th>
                                                        <?php if($control_key === 'specials'): ?>
                                                        <th><label><?php echo esc_html__('Search Brand','asl_locator') ?><input type="text" class="form-control" data-id="brand_id" placeholder="<?php echo esc_attr__('Enter brand name','asl_locator') ?>" /></label></th>
                                                        <?php endif; ?>
                                                        <th><label><?php echo esc_html__('Search Order','asl_locator') ?><input type="text" class="form-control" data-id="ordr" placeholder="<?php echo esc_attr__('Enter order','asl_locator') ?>" /></label></th>
                                                        <th><label><?php echo esc_html__('Search Date','asl_locator') ?><input type="text" class="form-control" data-id="created_on" placeholder="<?php echo esc_attr__('Enter date','asl_locator') ?>" /></label></th>
                                                        <th aria-hidden="true"></th>
                                                    </tr>
                                                    <tr>
                                                        <th><a class="select-all"><?php echo esc_attr__('Select All','asl_locator') ?></a>
                                                        </th>
                                                        <th align="center"><?php echo esc_attr__('ID','asl_locator') ?></th>
                                                        <th align="center"><?php echo esc_attr__('Name','asl_locator') ?>
                                                        </th>
                                                        <?php if($control_key === 'specials'): ?>
                                                        <th align="center"><?php echo esc_attr__('Brand','asl_locator') ?>
                                                        </th>
                                                        <?php endif; ?>
                                                        <th align="center"><?php echo esc_attr__('Order','asl_locator') ?>
                                                        </th>
                                                        <th align="center"><?php echo esc_attr__('Created On','asl_locator') ?></th>
                                                        <th class="text-center">
                                                            <?php echo esc_attr__('Action','asl_locator') ?>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                     $counter++; 
                     endforeach; 
                     ?>

                        </div>
                    </div>
                </div>
                    </div>
                </section>
            </div>

        </div>
    </div>
</div>
<!-- asl-cont end-->
<!-- SCRIPTS -->
<script type="text/javascript">
window.addEventListener("load", function() {
    asl_engine.pages.manage_attribute();
});
</script>
