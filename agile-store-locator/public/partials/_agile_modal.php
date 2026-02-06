<div id="agile-modal-direction" class="agile-modal fade">
    <div class="agile-modal-backdrop-in"></div>
    <div class="agile-modal-dialog in">
        <div class="agile-modal-content">
            <div class="sl-form-group d-flex justify-content-between">
                <h4><?php echo asl_esc_lbl('modal_get_direc') ?></h4>
                <button type="button" class="close-directions sl-close" data-dismiss="agile-modal"
                    aria-label="<?php echo asl_esc_lbl('close') ?>"><i aria-hidden="true" class="icon-cancel-1"></i></button>
            </div>
            <div class="sl-form-group">
                <label for="frm-lbl"><?php echo asl_esc_lbl('modal_from') ?>:</label>
                <input type="text" class="form-control frm-place" id="frm-lbl"
                    placeholder="<?php echo asl_esc_lbl('enter_loc') ?>">
            </div>
            <div class="sl-form-group">
                <label for="to-lbl"><?php echo asl_esc_lbl('modal_to') ?>:</label>
                <input readonly="true" type="text" class="directions-to form-control" id="to-lbl"
                    placeholder="<?php echo asl_esc_lbl('modal_pre_des_add') ?>">
            </div>
            <div class="sl-form-group mb-0">
                <label for="rbtn-km" class="checkbox-inline">
                    <input type="radio" name="dist-type" id="rbtn-km" value="0"> <?php echo asl_esc_lbl('km') ?>
                </label>
                <label for="rbtn-mile" class="checkbox-inline">
                    <input type="radio" name="dist-type" checked id="rbtn-mile" value="1">
                    <?php echo asl_esc_lbl('miles') ?>
                </label>
            </div>
            <div class="sl-form-group mb-0">
                <button type="submit" aria-label="<?php echo asl_esc_lbl('modal_get_direc') ?>"
                    class="btn btn-default btn-submit"><?php echo asl_esc_lbl('modal_get_direc') ?></button>
            </div>
        </div>
    </div>
</div>

<div id="asl-geolocation-agile-modal" class="agile-modal fade">
    <div class="agile-modal-backdrop-in"></div>
    <div class="agile-modal-dialog in">
        <div class="agile-modal-content">
            <?php if($all_configs['prompt_location'] == '2'): ?>
            <div class="sl-form-group d-flex justify-content-between">
                <h2><?php echo asl_esc_lbl('modal_geo_pos') ?></h2>
                <button type="button" class="close-directions sl-close" data-dismiss="agile-modal"
                    aria-label="<?php echo asl_esc_lbl('close') ?>"><i aria-hidden="true" class="icon-cancel-1"></i></button>
            </div>
            <div class="sl-form-group  sl-geolocation">
                <label for="asl-current-loc" class="sr-only"><?php echo asl_esc_lbl('modal_your_add') ?></label>
                <input type="text" class="form-control" id="asl-current-loc"
                placeholder="<?php echo asl_esc_lbl('modal_your_add') ?>">
            </div>
            <div class="sl-form-group mb-0">
                <button type="button" id="asl-btn-locate" aria-label="<?php echo asl_esc_lbl('modal_locate') ?>" class="btn btn-block btn-default"><?php echo asl_esc_lbl('modal_locate') ?></button>
            </div>
            <?php else: ?>
              <div class="sl-form-group sl-icon-group">
              <button type="button" class="close-directions sl-close" data-dismiss="agile-modal"
                    aria-label="<?php echo asl_esc_lbl('close') ?>"><i aria-hidden="true" class="icon-cancel-1"></i></button>
                <div class="sl-loct-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                        </path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <h2><?php echo asl_esc_lbl('modal_use_my_loc_title') ?></h2>
                <p><?php echo asl_esc_lbl('modal_use_my_loc') ?></p>
            </div>
            <div class="sl-form-group text-center mb-0">
                <button type="button" id="asl-btn-geolocation"
                    class="btn btn-block btn-default"><?php echo asl_esc_lbl('modal_use_loc') ?></button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="asl-desc-agile-modal" class="agile-modal fade">
    <div class="agile-modal-backdrop-in"></div>
    <div class="agile-modal-dialog in">
        <div class="agile-modal-content">
            <div class="sl-row">
                <div class="pol-md-12">
                    <div class="sl-form-group d-flex justify-content-between">
                        <h4 class="sl-title"><?php echo asl_esc_lbl('view_desc') ?></h4>
                        <button type="button" class="close-directions sl-close" data-dismiss="agile-modal"
                            aria-label="Close"><i aria-hidden="true" class="icon-cancel-1"></i></button>
                    </div>
                    <div class="sl-desc"></div>
                </div>
            </div>
        </div>
    </div>
</div>