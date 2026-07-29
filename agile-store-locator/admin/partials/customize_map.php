<div class="asl-p-cont asl-new-bg">
    <div class="container">
        <div class="asl-customize-map">
            <div class="card p-0 mb-4 asl-inner-cont">
                <div class="card-title asl-map-customizer-header">
                    <div>
                        <h3><?php echo esc_html__('Customize Map', 'asl_locator'); ?></h3>
                        <p class="card-text"><?php echo esc_html__('Draw map areas, configure layers, and choose the controls shown to visitors.', 'asl_locator'); ?></p>
                    </div>
                    <button type="button" id="asl-save-map" data-loading-text="<?php echo esc_attr__('Saving...', 'asl_locator'); ?>" class="btn btn-success">
                        <?php echo esc_html__('Save Customization', 'asl_locator'); ?>
                    </button>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3 asl-map-customizer-panels">
                        <div class="col-xl-7 col-12">
                            <section class="asl-map-option-card h-100" aria-labelledby="asl-drawing-title">
                                <div class="asl-option-card-heading">
                                    <span class="dashicons dashicons-edit"></span>
                                    <div>
                                        <h4 id="asl-drawing-title"><?php echo esc_html__('Draw Shapes & Layers', 'asl_locator'); ?></h4>
                                        <p><?php echo esc_html__('Choose a tool, then draw directly on the map preview.', 'asl_locator'); ?></p>
                                    </div>
                                </div>

                                <div class="asl-drawing-tools" role="toolbar" aria-label="<?php echo esc_attr__('Map drawing tools', 'asl_locator'); ?>">
                                    <?php
                                    $drawing_tools = [
                                        'polygon'  => ['dashicons-editor-code', __('Polygon', 'asl_locator')],
                                        'circle'   => ['dashicons-marker', __('Circle', 'asl_locator')],
                                        'polyline' => ['dashicons-minus', __('Polyline', 'asl_locator')],
                                        'rectangle'=> ['dashicons-editor-table', __('Rectangle', 'asl_locator')],
                                    ];

                                    foreach ($drawing_tools as $tool => $tool_data) :
                                    ?>
                                        <button type="button" class="asl-drawing-tool" data-drawing-mode="<?php echo esc_attr($tool); ?>" aria-pressed="false">
                                            <span class="dashicons <?php echo esc_attr($tool_data[0]); ?>"></span>
                                            <span><?php echo esc_html($tool_data[1]); ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                                <p id="asl-drawing-instructions" class="asl-drawing-instructions" aria-live="polite">
                                    <?php echo esc_html__('Select a drawing tool to begin.', 'asl_locator'); ?>
                                </p>

                                <div id="asl-drawing-actions" class="asl-drawing-actions" hidden>
                                    <button type="button" id="asl-finish-drawing" class="btn btn-sm btn-primary" disabled>
                                        <?php echo esc_html__('Finish Drawing', 'asl_locator'); ?>
                                    </button>
                                    <button type="button" id="asl-cancel-drawing" class="btn btn-sm btn-outline-secondary">
                                        <?php echo esc_html__('Cancel', 'asl_locator'); ?>
                                    </button>
                                </div>

                                <div class="asl-shape-editor" aria-labelledby="asl-shape-style-title">
                                    <div class="asl-shape-editor-title">
                                        <h5 id="asl-shape-style-title"><?php echo esc_html__('Selected Shape', 'asl_locator'); ?></h5>
                                        <span id="asl-selected-shape-label"><?php echo esc_html__('None selected', 'asl_locator'); ?></span>
                                    </div>

                                    <div class="asl-shape-editor-controls">
                                        <div class="asl-shape-fill-options" id="asl-fill-option" role="group" aria-label="<?php echo esc_attr__('Shape fill style', 'asl_locator'); ?>">
                                            <button type="button" class="btn btn-sm btn-outline-secondary active" data-value="1" aria-pressed="true"><?php echo esc_html__('Solid', 'asl_locator'); ?></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-value="0" aria-pressed="false"><?php echo esc_html__('Border only', 'asl_locator'); ?></button>
                                        </div>

                                        <div class="color_scheme">
                                            <div class="map_cange" role="radiogroup" aria-label="<?php echo esc_attr__('Shape color', 'asl_locator'); ?>">
                                                <?php
                                                $shape_colors = ['#CC3333', '#E11619', '#542733', '#278BBC', '#78C1E4', '#ACD55D', '#A8BD78', '#EAAE40', '#E68EC1', '#B39571'];
                                                foreach ($shape_colors as $index => $shape_color) :
                                                ?>
                                                    <span>
                                                        <input type="radio" id="asl-color_scheme-<?php echo esc_attr($index); ?>" value="<?php echo esc_attr($shape_color); ?>" name="data[color_scheme]"<?php checked($index, 0); ?>>
                                                        <label class="color-box color-<?php echo esc_attr($index); ?>" for="asl-color_scheme-<?php echo esc_attr($index); ?>" title="<?php echo esc_attr($shape_color); ?>" style="background-color: <?php echo esc_attr($shape_color); ?>"></label>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="asl-shape-actions">
                                            <button class="btn btn-sm btn-outline-danger" type="button" id="asl-delete-shape" disabled>
                                                <span class="dashicons dashicons-trash"></span><?php echo esc_html__('Delete Selected', 'asl_locator'); ?>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" type="button" id="asl-clear-all">
                                                <?php echo esc_html__('Clear All', 'asl_locator'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <form id="frm-asl-layers" class="asl-setting-cont">
                                    <h5><?php echo esc_html__('Map Layers', 'asl_locator'); ?></h5>
                                    <div class="asl-layer-grid">
                                        <?php
                                        $layer_options = [
                                            'trafic_layer'     => __('Traffic', 'asl_locator'),
                                            'transit_layer'    => __('Transit', 'asl_locator'),
                                            'bike_layer'       => __('Bicycle', 'asl_locator'),
                                            'marker_animations'=> __('Marker Animation', 'asl_locator'),
                                        ];

                                        foreach ($layer_options as $layer_key => $layer_label) :
                                        ?>
                                            <label class="asl-check-option" for="asl-<?php echo esc_attr($layer_key); ?>">
                                                <input type="checkbox" id="asl-<?php echo esc_attr($layer_key); ?>" name="data[<?php echo esc_attr($layer_key); ?>]">
                                                <span class="asl-check-indicator"></span>
                                                <span><?php echo esc_html($layer_label); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </form>
                            </section>
                        </div>

                        <div class="col-xl-5 col-12">
                            <section class="asl-map-option-card h-100" aria-labelledby="asl-map-controls-title">
                                <div class="asl-option-card-heading">
                                    <span class="dashicons dashicons-admin-generic"></span>
                                    <div>
                                        <h4 id="asl-map-controls-title"><?php echo esc_html__('Map Controls', 'asl_locator'); ?></h4>
                                        <p><?php echo esc_html__('These defaults can still be overridden by shortcode attributes.', 'asl_locator'); ?></p>
                                    </div>
                                </div>

                                <div class="asl-control-grid">
                                    <?php
                                    $map_controls = [
                                        'cameracontrol'     => __('Camera Control', 'asl_locator'),
                                        'zoomcontrol'       => __('Zoom Control (Old Style)', 'asl_locator'),
                                        'streetviewcontrol' => __('Street View', 'asl_locator'),
                                        'fullscreencontrol' => __('Fullscreen', 'asl_locator'),
                                        'maptypecontrol'    => __('Map Type Control', 'asl_locator'),
                                    ];

                                    foreach ($map_controls as $control_key => $control_label) :
                                        $control_enabled = !isset($map_control_defaults[$control_key]) || $map_control_defaults[$control_key];
                                    ?>
                                        <label class="asl-check-option" for="asl-<?php echo esc_attr($control_key); ?>">
                                            <input type="checkbox" class="asl-map-control-toggle" id="asl-<?php echo esc_attr($control_key); ?>" data-control="<?php echo esc_attr($control_key); ?>"<?php checked($control_enabled); ?>>
                                            <span class="asl-check-indicator"></span>
                                            <span><?php echo esc_html($control_label); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <div class="asl-map-control-note">
                                    <span class="dashicons dashicons-info-outline"></span>
                                    <p>
                                        <?php
                                        printf(
                                            wp_kses(
                                                __('The default map type remains in <a href="%s">ASL Settings</a>. Camera Control and Zoom Control (Old Style) are alternative navigation controls and cannot be enabled together.', 'asl_locator'),
                                                ['a' => ['href' => []]]
                                            ),
                                            esc_url(admin_url('admin.php?page=asl-settings'))
                                        );
                                        ?>
                                    </p>
                                </div>
                            </section>
                        </div>
                    </div>

                    <section class="asl-map-preview-card mt-3" aria-labelledby="asl-map-preview-title">
                        <div class="asl-map-preview-heading">
                            <div>
                                <h4 id="asl-map-preview-title"><?php echo esc_html__('Live Map Preview', 'asl_locator'); ?></h4>
                                <p><?php echo esc_html__('Search for a location, draw shapes, and preview layers and controls before saving.', 'asl_locator'); ?></p>
                            </div>
                            <span id="asl-map-dirty-status" class="asl-map-dirty-status" aria-live="polite"></span>
                        </div>
                        <label class="screen-reader-text" for="asl-setting-search-box"><?php echo esc_html__('Search location', 'asl_locator'); ?></label>
                        <input id="asl-setting-search-box" type="search" class="form-control mb-3" placeholder="<?php echo esc_attr__('Search location', 'asl_locator'); ?>">
                        <div class="map_canvas" id="map_canvas" aria-label="<?php echo esc_attr__('Map customization preview', 'asl_locator'); ?>"></div>
                    </section>

                    <section class="asl-kml-card mt-3" aria-labelledby="asl-kml-title">
                        <div class="asl-kml-card-heading">
                            <div>
                                <h4 id="asl-kml-title">
                                    <?php
                                    printf(
                                        esc_html__('Uploaded KML Files (%d)', 'asl_locator'),
                                        count($kml_files)
                                    );
                                    ?>
                                </h4>
                                <p><?php echo esc_html__('Manage your uploaded KML overlays.', 'asl_locator'); ?></p>
                            </div>
                            <div class="asl-kml-header-actions">
                                <label class="asl-kml-search" for="asl-kml-search">
                                    <span class="screen-reader-text"><?php echo esc_html__('Search KML files', 'asl_locator'); ?></span>
                                    <input type="search" id="asl-kml-search" class="form-control" placeholder="<?php echo esc_attr__('Search KML files...', 'asl_locator'); ?>">
                                    <span class="dashicons dashicons-search"></span>
                                </label>
                                <a target="_blank" rel="noopener noreferrer" href="https://agilestorelocator.com/wiki/intro-to-kml-files/" class="asl-kml-guide-link" title="<?php echo esc_attr__('KML guide', 'asl_locator'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                    <span class="screen-reader-text"><?php echo esc_html__('KML guide', 'asl_locator'); ?></span>
                                </a>
                            </div>
                        </div>

                        <form id="sl-frm-kml" class="asl-kml-upload-form">
                            <div class="asl-kml-upload" id="drop-zone-1">
                                <input type="file" class="form-control" name="files" id="file-img-2" accept=".kml,.kmz">
                                <button type="button" id="btn-asl-upload-kml" class="btn btn-primary btn-start">
                                    <span class="dashicons dashicons-upload"></span><?php echo esc_html__('Upload KML', 'asl_locator'); ?>
                                </button>
                            </div>
                            <div class="form-group mb-0"><ul></ul></div>
                        </form>

                        <div class="asl-kml-table-wrap">
                            <table class="asl-kml-table">
                                <thead>
                                    <tr>
                                        <th><?php echo esc_html__('File Name', 'asl_locator'); ?></th>
                                        <th><?php echo esc_html__('Uploaded On', 'asl_locator'); ?></th>
                                        <th><?php echo esc_html__('File Size', 'asl_locator'); ?></th>
                                        <th><?php echo esc_html__('Overlay Count', 'asl_locator'); ?></th>
                                        <th><?php echo esc_html__('Status', 'asl_locator'); ?></th>
                                        <th class="asl-kml-actions-heading"><?php echo esc_html__('Actions', 'asl_locator'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="asl-kml-table-body">
                                    <?php if (empty($kml_files)) : ?>
                                        <tr class="asl-kml-empty-row">
                                            <td colspan="6"><?php echo esc_html__('No KML files uploaded.', 'asl_locator'); ?></td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($kml_files as $file) : ?>
                                            <tr class="asl-kml-file-row" data-search="<?php echo esc_attr(strtolower($file['name'])); ?>">
                                                <td>
                                                    <div class="asl-kml-file-cell">
                                                        <span class="asl-kml-file-icon" aria-hidden="true">KML</span>
                                                        <div>
                                                            <strong class="asl-file-name"><?php echo esc_html($file['name']); ?></strong>
                                                            <span><?php echo esc_html(strtoupper(pathinfo($file['name'], PATHINFO_EXTENSION))); ?> <?php echo esc_html__('overlay', 'asl_locator'); ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo esc_html($file['uploaded_on']); ?></td>
                                                <td><?php echo esc_html($file['size']); ?></td>
                                                <td>
                                                    <?php if ($file['overlay_count'] !== null) : ?>
                                                        <span class="asl-kml-overlay-count"><span class="dashicons dashicons-location"></span><?php echo esc_html(sprintf(_n('%d overlay', '%d overlays', $file['overlay_count'], 'asl_locator'), $file['overlay_count'])); ?></span>
                                                    <?php else : ?>
                                                        <span aria-label="<?php echo esc_attr__('Unavailable', 'asl_locator'); ?>">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="asl-kml-status"><?php echo esc_html__('Available', 'asl_locator'); ?></span></td>
                                                <td>
                                                    <div class="asl-kml-row-actions">
                                                        <button type="button" class="asl-kml-action asl-kml-preview" data-url="<?php echo esc_url($file['url']); ?>" title="<?php echo esc_attr__('Preview on map', 'asl_locator'); ?>" aria-pressed="false">
                                                            <span class="dashicons dashicons-visibility"></span>
                                                        </button>
                                                        <a class="asl-kml-action" href="<?php echo esc_url($file['url']); ?>" download title="<?php echo esc_attr__('Download', 'asl_locator'); ?>">
                                                            <span class="dashicons dashicons-download"></span>
                                                        </a>
                                                        <button type="button" data-file="<?php echo esc_attr($file['name']); ?>" title="<?php echo esc_attr__('Delete', 'asl_locator'); ?>" class="asl-kml-action asl-trash-icon">
                                                            <span class="dashicons dashicons-trash"></span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <tr id="asl-kml-no-results" hidden>
                                        <td colspan="6"><?php echo esc_html__('No matching KML files found.', 'asl_locator'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <div class="asl-map-save-footer">
                        <span><?php echo esc_html__('Changes are not published until you save.', 'asl_locator'); ?></span>
                        <button type="button" class="btn btn-success asl-save-map-secondary" data-loading-text="<?php echo esc_attr__('Saving...', 'asl_locator'); ?>"><?php echo esc_html__('Save Customization', 'asl_locator'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$asl_customize_map_l10n = [
    'select_tool'       => __('Select a drawing tool to begin.', 'asl_locator'),
    'polygon'          => __('Click to add polygon points, then select Finish Drawing or double-click.', 'asl_locator'),
    'polyline'         => __('Click to add line points, then select Finish Drawing or double-click.', 'asl_locator'),
    'circle'           => __('Click the center, move the pointer, then click again to finish.', 'asl_locator'),
    'rectangle'        => __('Click one corner, move the pointer, then click the opposite corner.', 'asl_locator'),
    'none_selected'    => __('None selected', 'asl_locator'),
    'unsaved'          => __('Unsaved changes', 'asl_locator'),
    'saved'            => __('All changes saved', 'asl_locator'),
    'confirm_clear'    => __('Remove all drawn shapes?', 'asl_locator'),
    'confirm_delete_kml'=> __('Delete this KML file?', 'asl_locator'),
    'no_kml'           => __('Choose a KML or KMZ file to upload.', 'asl_locator'),
    'kml_available'     => __('Available', 'asl_locator'),
    'kml_previewing'    => __('Previewing', 'asl_locator'),
    'kml_preview_error' => __('The KML file could not be previewed. Confirm that it is publicly accessible and valid.', 'asl_locator'),
];
$asl_customize_map_script = sprintf(
    "var ASL_Instance = %s;\nvar asl_configs = %s;\nvar asl_map_customize = %s;\nvar asl_customize_map_l10n = %s;\n" .
    "window.addEventListener('load', function() {\n" .
    "    asl_engine.pages.customize_map(asl_map_customize);\n" .
    "});",
    wp_json_encode(['url' => ASL_UPLOAD_URL]),
    wp_json_encode($all_configs),
    wp_json_encode($map_customize),
    wp_json_encode($asl_customize_map_l10n)
);

wp_add_inline_script('agile-store-locator-jscript', $asl_customize_map_script, 'after');
?>
