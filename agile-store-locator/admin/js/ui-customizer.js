(function ($) {
  'use strict';
  var $root = $('#asl-ui-customizer');
  if (!$root.length || typeof ASL_CUSTOMIZER === 'undefined') return;
  var $fields = $('#asl-fields-section'), $load = $('#btn-asl-load_uitemp'), $save = $('#btn-asl-save_uitemp'), $reset = $('#btn-asl-reset_uitemp'), $notice = $('#asl-customizer-notice');
  var template = '', activeTab = 'design', dirty = false, loading = false, frame = document.getElementById('asl-live-preview');
  var appearanceBaseline = {}, filterBaseline = {}, mapBaseline = {}, previewTimer, mapResizeTimer, activeDevice = 'desktop';
  function request(action, data) { return $.ajax({url: ASL_CUSTOMIZER.ajaxUrl, method: 'POST', dataType: 'json', data: $.extend({action: 'asl_ajax_handler', 'sl-action': action, 'asl-nounce': ASL_CUSTOMIZER.nonce}, data || {})}); }
  function message(text, type) { $notice.attr('class', 'asl-customizer__notice is-' + (type || 'success')).text(text).prop('hidden', false); clearTimeout(message.timer); message.timer = setTimeout(function () {$notice.prop('hidden', true);}, 4500); }
  function busy($button, state, text) { if (!$button.data('label')) $button.data('label', $button.html()); $button.prop('disabled', state).toggleClass('is-busy', state).html(state ? '<span class="spinner is-active"></span>' + text : $button.data('label')); }
  function serialize() { var data = {}; $('#frm-asl-ui-customizer').serializeArray().forEach(function (field) {if (field.name.indexOf('appearance[') !== 0 && field.name.indexOf('filters[') !== 0 && field.name.indexOf('map[') !== 0 && field.name.indexOf('ui-') !== 0) data[field.name] = field.value;}); return data; }
  function layoutData() { var data = {}; $fields.find('[data-asl-layout]').each(function () {data[this.name] = this.value;}); return data; }
  function dimensionData() {var data = {}; $fields.find('[data-asl-dimension]').each(function () {data[this.name] = this.value.trim().toLowerCase();}); return data;}
  function validDimension(value) {return value === '' || (/^(?:\d+(?:\.\d+)?|\.\d+)(?:px|vh|vw|rem|em|%|vmin|vmax)$/.test(value) && parseFloat(value) > 0);}
  function syncDimensionControls() {
    var dimensions = dimensionData(), valid = true;
    $fields.find('[data-asl-dimension]').each(function () {var size = dimensions[this.name]; this.setCustomValidity(validDimension(size) ? '' : 'Enter a positive CSS size, for example 480px, 60vh or 100%.'); if (!validDimension(size)) valid = false;});
    if (!valid) return false;
    [['full_height', !!(dimensions['locator-height'] || dimensions['locator-phone-height'])], ['full_width', !!dimensions['locator-width']]].forEach(function (item) {
      var $control = $('#asl-appearance-' + item[0]);
      if (item[0] === 'full_height') {
        if (item[1]) $control.val('');
        $control.removeData('dimensionPrevious').prop('disabled', item[1]);
        return;
      }
      if (item[1]) {
        if ($control.data('dimensionPrevious') === undefined) $control.data('dimensionPrevious', item[0] === 'full_width' ? $control.prop('checked') : $control.val());
        if (item[0] === 'full_width') $control.prop('checked', false); else $control.val('');
        $control.prop('disabled', true);
      } else {
        var previous = $control.data('dimensionPrevious');
        if (previous !== undefined) {if (item[0] === 'full_width') $control.prop('checked', previous); else $control.val(previous); $control.removeData('dimensionPrevious');}
        $control.prop('disabled', false);
      }
    });
    return true;
  }
  function applyDimensionPreview() {
    var locator = previewLocator();
    if (!locator || !$fields.find('[data-asl-dimension]').length || !syncDimensionControls()) return;
    var dimensions = dimensionData(), appearance = serializeAppearance(), fixedHeight = !!(dimensions['locator-height'] || dimensions['locator-phone-height']), fixedWidth = !!dimensions['locator-width'];
    locator.classList.toggle('sl-custom-height', !!dimensions['locator-height']);
    locator.classList.toggle('sl-custom-phone-height', !!dimensions['locator-phone-height']);
    locator.classList.toggle('sl-custom-width', fixedWidth);
    $.each(dimensions, function (key, value) {if (value) locator.style.setProperty('--sl-' + key, value); else locator.style.removeProperty('--sl-' + key);});
    locator.classList.remove('full-height', 'asl-fixed');
    if (!fixedHeight && appearance.full_height) appearance.full_height.split(' ').forEach(function (name) {locator.classList.add(name);});
    locator.classList.toggle('full-width', !fixedWidth && appearance.full_width === '1');
    var container = locator.querySelector('.asl-wrapper > .sl-container, .asl-wrapper > .sl-container-fluid');
    if (container) {var fluid = !fixedWidth && appearance.full_width === '1'; container.classList.toggle('sl-container-fluid', fluid); container.classList.toggle('sl-container', !fluid);}
    resizeMap();
  }
  function previewLocator() { try {return frame.contentDocument && frame.contentDocument.getElementById('asl-storelocator');} catch (error) {return null;} }
  function resizeMap() {
    clearTimeout(mapResizeTimer);
    mapResizeTimer = setTimeout(function () {
      try {
        var win = frame.contentWindow, map = win.asl_map || (win.asl_view && win.asl_view.getMap && win.asl_view.getMap());
        if (map && typeof map.resize === 'function') map.resize();
        else if (map && map.map_ && typeof map.map_.resize === 'function') map.map_.resize();
        else if (map && win.google && win.google.maps && win.google.maps.event) win.google.maps.event.trigger(map, 'resize');
        win.dispatchEvent(new win.Event('resize'));
      } catch (error) { /* The iframe may still be loading. */ }
    }, 70);
  }
  function syncProportion() {
    var locator = previewLocator(), range = document.getElementById('asl-list-map-proportion');
    if (!range) return;
    var locked = $(range).closest('[data-asl-locked]').length > 0;
    var breakpoint = activeDevice === 'desktop' ? 'lg' : (activeDevice === 'tablet' ? 'md' : 'sm');
    var defaults = {}, side = {};
    if (locator) {try {defaults = JSON.parse(locator.getAttribute('data-sl-layout-defaults') || '{}'); side = JSON.parse(locator.getAttribute('data-sl-layout-side') || '{}');} catch (error) {}}
    var stacked = activeDevice === 'mobile' || !side[breakpoint];
    var below = locator && (locator.classList.contains('sl-results-bottom') || locator.classList.contains('sl-results-cards'));
    $fields.find('.asl-proportion-control').prop('hidden', !!below);
    if (locator && !stacked && !below && !layoutData()['results-layout']) {
      var list = locator.querySelector('.sl-proportion-list'), map = locator.querySelector('.sl-proportion-map');
      if (list && map && list.getBoundingClientRect().x !== map.getBoundingClientRect().x) {
        $fields.find('[data-asl-result-layout][value="' + (list.getBoundingClientRect().x < map.getBoundingClientRect().x ? 'left' : 'right') + '"]').prop('checked', true);
      }
    }
    var stored = layoutData()['list-width-' + breakpoint], value = Math.max(30, Math.min(70, Number(stored || defaults[breakpoint] || 40)));
    range.value = value; range.disabled = locked || stacked || !locator;
    range.style.background = 'linear-gradient(to right,#1677df 0%,#1677df ' + ((value - 30) / 40 * 100) + '%,#e5eaf1 ' + ((value - 30) / 40 * 100) + '%,#e5eaf1 100%)';
    $('#asl-proportion-device').text($root.find('[data-asl-device="' + activeDevice + '"]').text());
    $('#asl-proportion-list').text(stacked ? '100%' : value + '%');
    $('#asl-proportion-map').text(stacked ? '100%' : (100 - value) + '%');
    $('#asl-proportion-stacked').prop('hidden', !stacked);
    range.setAttribute('aria-valuetext', stacked ? 'List 100%, Map 100%' : 'List ' + value + '%, Map ' + (100 - value) + '%');
  }
  function applyLayoutPreview() {
    var locator = previewLocator(), data = layoutData();
    if (!locator || !$fields.find('[data-asl-layout]').length) return;
    var side;
    try {side = JSON.parse(locator.getAttribute('data-sl-layout-side') || '{}');} catch (error) {return;}
    ['sm', 'md', 'lg', 'xl'].forEach(function (breakpoint) {
      var value = data['list-width-' + breakpoint], enabled = side[breakpoint] && /^\d+$/.test(value) && Number(value) >= 30 && Number(value) <= 70;
      locator.classList.toggle('sl-proportion-' + breakpoint, !!enabled);
      if (enabled) {
        locator.style.setProperty('--sl-list-width-' + breakpoint, value + '%');
        locator.style.setProperty('--sl-map-width-' + breakpoint, (100 - Number(value)) + '%');
      } else {locator.style.removeProperty('--sl-list-width-' + breakpoint); locator.style.removeProperty('--sl-map-width-' + breakpoint);}
    });
    if (['left', 'right', 'bottom', 'cards'].indexOf(data['results-layout']) !== -1) {
      locator.classList.remove('sl-result-left', 'sl-result-right', 'sl-results-left', 'sl-results-right', 'sl-results-bottom', 'sl-results-cards');
      locator.classList.add((data['results-layout'] === 'bottom' || data['results-layout'] === 'cards' ? 'sl-results-' : 'sl-result-') + data['results-layout']);
    }
    syncProportion(); resizeMap();
  }
  function serializeAppearance() {
    var data = {};
    $fields.find('[data-asl-appearance]').each(function () {
      var key = this.name.slice(11, -1);
      if (this.type === 'radio' && !this.checked) return;
      var previous = $(this).data('dimensionPrevious');
      data[key] = this.type === 'checkbox' ? ((previous !== undefined ? previous : this.checked) ? '1' : '0') : (previous !== undefined ? previous : this.value);
    });
    return data;
  }
  function serializeFilters() {
    var data = {};
    $fields.find('[data-asl-filter]').each(function () {
      var match = this.name.match(/^filters\[([^\]]+)\]/), key, isList;
      if (!match) return;
      key = match[1];
      isList = /\[\]$/.test(this.name);
      if (this.type === 'checkbox' && isList) {
        if (!Object.prototype.hasOwnProperty.call(data, key)) data[key] = [];
        if (this.checked) data[key].push(this.value);
      }
      else if (this.type === 'checkbox') data[key] = this.checked ? '1' : '0';
      else if (this.multiple) data[key] = ($(this).val() || []).join(',');
      else data[key] = this.value;
    });
    $.each(data, function (key, value) {if ($.isArray(value)) data[key] = value.join(',');});
    return data;
  }
  function serializeMapSettings() {
    var data = {};
    $fields.find('[data-asl-map]').each(function () {
      var match = this.name && this.name.match(/^map\[([^\]]+)\]/);
      if (!match) return;
      data[match[1]] = this.type === 'checkbox' ? (this.checked ? '1' : '0') : this.value;
    });
    return data;
  }
  function syncFilterDependencies() {
    var filters = serializeFilters(), hasBrand = (filters.filter_ddl || '').split(',').indexOf('brand') !== -1, hasSpecial = (filters.filter_ddl || '').split(',').indexOf('special') !== -1;
    $fields.find('[data-filter-setting="brand_special_cascade"]').toggle(hasBrand && hasSpecial || $fields.find('[data-filter-setting="brand_special_cascade"]').is('[data-asl-locked]'));
    $fields.find('[data-filter-setting="image_filter_title"]').toggle(filters.image_filter === '1' || $fields.find('[data-filter-setting="image_filter_title"]').is('[data-asl-locked]'));
    $fields.find('[data-filter-setting="show_opened"]').toggle(filters.time_switch === '1' || $fields.find('[data-filter-setting="show_opened"]').is('[data-asl-locked]'));
    $('#asl-filter-distance_control, #asl-filter-fixed_radius').closest('.asl-filter-setting').toggle(filters.distance_slider === '1');
  }
  function syncMapDependencies() {
    var settings = serializeMapSettings();
    $fields.find('[data-map-setting="geo_marker_id"]').toggle(settings.geo_marker === '1');
    $fields.find('[data-map-setting="cluster_max_zoom"], [data-map-setting="cluster_grid_size"]').toggle(settings.cluster === '1');
    $fields.find('.asl-map-range').each(function () {
      var min = Number(this.min), max = Number(this.max), value = Number(this.value), percent = max > min ? (value - min) / (max - min) * 100 : 0;
      this.style.background = 'linear-gradient(to right,#1677df 0%,#1677df ' + percent + '%,#e5eaf1 ' + percent + '%,#e5eaf1 100%)';
      $(this).closest('.asl-map-range-control').find('[data-asl-map-range-value]').text(value);
      this.setAttribute('aria-valuetext', 'Zoom level ' + value);
    });
  }
  function snapshot() { return JSON.stringify({design: serialize(), appearance: serializeAppearance(), filters: serializeFilters(), map: serializeMapSettings()}); }
  function reloadPreview() {
    clearTimeout(previewTimer);
    if (!template) return;
    var url = new URL(ASL_CUSTOMIZER.previewUrl, window.location.href);
    url.searchParams.set('template', template.replace(/^template-/, ''));
    $.each(serializeAppearance(), function (key, value) {url.searchParams.set('appearance[' + key + ']', value);});
    $.each(serializeFilters(), function (key, value) {url.searchParams.set('filters[' + key + ']', value);});
    $.each(serializeMapSettings(), function (key, value) {url.searchParams.set('map[' + key + ']', value);});
    $.each(layoutData(), function (key, value) {url.searchParams.set('locator_layout[' + key + ']', value);});
    $.each(dimensionData(), function (key, value) {if (validDimension(value)) url.searchParams.set('locator_dimensions[' + key + ']', value);});
    frame.src = url.href;
  }
  function lighten(hex, percent) { var value = hex.replace('#', ''); if (!/^[0-9a-f]{6}$/i.test(value)) return hex; return '#' + [0, 2, 4].map(function (offset) {var channel = parseInt(value.slice(offset, offset + 2), 16); return Math.min(255, Math.round(channel + (255 - channel) * percent / 100)).toString(16).padStart(2, '0');}).join(''); }
  function syncPrimaryColorLinks() {
    if (template !== 'template-0') return;
    var quickStyle = $fields.find('[name="quick-style"]').val();
    var tabUsesPrimary = quickStyle === 'default' || quickStyle === 'dark';
    $fields.find('[name="header-color"]').siblings('.colorpicker').toggleClass('clr-copy', quickStyle === 'modern');
    $fields.find('[name="header"]').siblings('.colorpicker').toggleClass('light-90', quickStyle !== 'dark' && quickStyle !== 'modern');
    $fields.find('[name="tab-selected-bg"], [name="tab-selected-border"]').siblings('.colorpicker').toggleClass('clr-copy', tabUsesPrimary);
  }
  function updatePreview() {
    var doc;
    try { doc = frame.contentDocument; } catch (error) { return; }
    if (!doc) return;
    var locator = doc.getElementById('asl-storelocator') || doc.querySelector('.asl-cont');
    if (!locator) return;
    if (['template-0', 'template-1', 'template-2', 'template-3', 'template-4', 'template-5', 'template-6', 'template-list', 'template-list-2'].indexOf(template) !== -1) {
      ['default', 'modern', 'minimal', 'dark'].forEach(function (style) {locator.classList.remove('sl-design-' + style, 'sl-design-' + style + '-tmpl');});
      var quickStyle = $fields.find('[name="quick-style"]').val();
      if (['default', 'modern', 'minimal', 'dark'].indexOf(quickStyle) === -1) quickStyle = 'default';
      locator.classList.add('sl-design-' + quickStyle + '-tmpl');
    }
    $.each(serialize(), function (key, value) {
      if (key === 'results-layout' || key.indexOf('list-width-') === 0 || key.indexOf('locator-') === 0) return;
      if (!/^[a-z0-9-]+$/.test(key)) return;
      if (/^#[0-9a-f]{6}$/i.test(value) || (template === 'template-0' && key === 'image-bg' && value === 'transparent')) locator.style.setProperty('--sl-' + key, value);
      else if (/^\d+$/.test(value) && Number(value) <= 72) locator.style.setProperty('--sl-' + key, value + 'px');
    });
    applyLayoutPreview();
    applyDimensionPreview();
  }
  function showTab(tab, open) {
    activeTab = tab;
    var $tabs = $root.find('[data-asl-tab]');
    $tabs.removeClass('is-active').attr({'aria-selected': 'false', tabindex: '-1'});
    var $tab = $tabs.filter('[data-asl-tab="' + tab + '"]');
    $tab.addClass('is-active').attr({'aria-selected': 'true', tabindex: '0'});
    $('#asl-panel-title').text($tab.text());
    $fields.find('.asl-customizer-pane').prop('hidden', true).removeClass('is-active');
    $('#asl-pane-' + tab).prop('hidden', false).addClass('is-active');
    if (open) $root.find('.asl-customizer__workspace').addClass('is-panel-open');
  }
  function loadTemplate() {
    if (loading) return;
    var selected = $('#asl-ui-template').val();
    loading = true;
    busy($load, true, ASL_CUSTOMIZER.strings.loading);
    $save.add($reset).prop('disabled', true);
    request('load_ui_settings', {template: selected}).done(function (response) {
      if (!response.success) {message(response.error || ASL_CUSTOMIZER.strings.error, 'error'); return;}
      template = selected;
      $fields.html(response.html);
      syncPrimaryColorLinks();
      var defaultColors = $fields.find('[data-asl-default-colors]').data('asl-default-colors');
      if (defaultColors && defaultColors.primary) {
        var $sample = $fields.find('.asl-style-default');
        $sample.css({background: defaultColors.header || defaultColors['list-panel-bg'] || defaultColors['list-bg'], borderColor: defaultColors['search-bar-border'] || defaultColors.primary});
        $sample.find('i').css('background', defaultColors['list-bg']);
        $sample.find('b').css('background', defaultColors.primary);
        $sample.find('em').css('background', defaultColors.highlighted);
      }
      showTab(activeTab, false);
      dirty = false;
      appearanceBaseline = serializeAppearance();
      filterBaseline = serializeFilters();
      mapBaseline = serializeMapSettings();
      syncFilterDependencies();
      syncMapDependencies();
      syncDimensionControls();
      syncProportion();
      reloadPreview();
    }).fail(function () {message(ASL_CUSTOMIZER.strings.error, 'error');}).always(function () {
      loading = false;
      busy($load, false);
      $save.add($reset).prop('disabled', !template);
      if (template) $('#asl-ui-template').val(template);
    });
  }
  $(frame).on('load', updatePreview);
  $('#frm-asl-ui-customizer').on('submit', function (event) {event.preventDefault(); if (!$save.prop('disabled')) $save.trigger('click');});
  $load.on('click', function () {
    if (dirty && !window.confirm(ASL_CUSTOMIZER.strings.discard)) return;
    loadTemplate();
  });
  $save.on('click', function () {
    if (!template || loading) return;
    var form = document.getElementById('frm-asl-ui-customizer'), invalid = form.querySelector(':invalid');
    if (invalid) { showTab($(invalid).closest('.asl-customizer-pane').attr('id').replace('asl-pane-', ''), true); form.reportValidity(); return; }
    var data = serialize();
    var appearance = serializeAppearance(), filters = serializeFilters(), mapSettings = serializeMapSettings(), changedAppearance = {}, changedFilters = {}, changedMapSettings = {}, savedSnapshot = snapshot();
    $.each(appearance, function (key, value) {if (appearanceBaseline[key] !== value) changedAppearance[key] = value;});
    $.each(filters, function (key, value) {if (filterBaseline[key] !== value) changedFilters[key] = value;});
    $.each(mapSettings, function (key, value) {if (mapBaseline[key] !== value) changedMapSettings[key] = value;});
    busy($save, true, ASL_CUSTOMIZER.strings.saving);
    $load.add($reset).add('#asl-ui-template').prop('disabled', true);
    request('sl_theme_ui_save', {sl_template: template, sl_formData: data, sl_appearance: changedAppearance, sl_filters: changedFilters, sl_map: changedMapSettings}).done(function (response) {
      if (response.success) {appearanceBaseline = appearance; filterBaseline = filters; mapBaseline = mapSettings; if (savedSnapshot === snapshot()) dirty = false;}
      message(response.success ? ASL_CUSTOMIZER.strings.saved : (response.error || ASL_CUSTOMIZER.strings.error), response.success ? 'success' : 'error');
    }).fail(function () {message(ASL_CUSTOMIZER.strings.error, 'error');}).always(function () {busy($save, false); $load.add($reset).add('#asl-ui-template').prop('disabled', false);});
  });
  $reset.on('click', function () {
    if (!template || loading || !window.confirm(ASL_CUSTOMIZER.strings.confirm)) return;
    busy($reset, true, ASL_CUSTOMIZER.strings.resetting);
    $save.add($load).add('#asl-ui-template').prop('disabled', true);
    request('reset_ui_template', {template: template}).done(function (response) {
      if (response.success) { $('#asl-ui-template').val(template); loadTemplate(); }
      message(response.success ? response.msg : (response.error || ASL_CUSTOMIZER.strings.error), response.success ? 'success' : 'error');
    }).fail(function () {message(ASL_CUSTOMIZER.strings.error, 'error');}).always(function () {busy($reset, false); $('#asl-ui-template').prop('disabled', false); if (!loading) $load.add($save).prop('disabled', false);});
  });
  $root.on('click', '[data-asl-tab]', function () {showTab($(this).data('asl-tab'), true);});
  $root.on('keydown', '[data-asl-tab]', function (event) {
    var tabs = $root.find('[data-asl-tab]'), index = tabs.index(this);
    if (event.key === 'ArrowDown' || event.key === 'ArrowRight') index = (index + 1) % tabs.length;
    else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') index = (index + tabs.length - 1) % tabs.length;
    else if (event.key === 'Home') index = 0;
    else if (event.key === 'End') index = tabs.length - 1;
    else return;
    event.preventDefault(); tabs.eq(index).trigger('click').trigger('focus');
  });
  $('#asl-close-panel').on('click', function () {$root.find('.asl-customizer__workspace').removeClass('is-panel-open'); $root.find('[data-asl-tab="' + activeTab + '"]').trigger('focus');});
  $root.on('keydown', function (event) {if (event.key === 'Escape') $('#asl-close-panel').trigger('click');});
  $root.on('click', '[data-asl-device]', function () {
    $root.find('[data-asl-device]').removeClass('is-active').attr('aria-pressed', 'false');
    $(this).addClass('is-active').attr('aria-pressed', 'true');
    $root.find('.asl-preview-stage').attr('data-device', $(this).data('asl-device'));
    activeDevice = $(this).data('asl-device'); syncProportion(); resizeMap();
  });
  $fields.on('input change', '.colorpicker', function () {
    var $picker = $(this);
    $picker.siblings('.hexcolor').val($picker.val());
    if ($picker.hasClass('clr-primary')) {
      $fields.find('.clr-copy').each(function () {$(this).val($picker.val()).siblings('.hexcolor').val($picker.val());});
      $fields.find('[class*="light-"]').each(function () {var match = this.className.match(/light-(\d+)/); if (match) $(this).val(lighten($picker.val(), parseInt(match[1], 10))).siblings('.hexcolor').val($(this).val());});
    }
    dirty = true; updatePreview();
  });
  $fields.on('input change', '.hexcolor', function () {dirty = true; if (template === 'template-0' && this.name === 'image-bg' && this.value === 'transparent') updatePreview(); if (/^#[0-9a-f]{6}$/i.test($(this).val())) $(this).siblings('.colorpicker').val($(this).val()).trigger('change');});
  $fields.on('input change', 'input[type="number"]', function () {dirty = true; if (this.checkValidity()) updatePreview();});
  $fields.on('change', '[data-asl-appearance]', function () {dirty = true; clearTimeout(previewTimer); previewTimer = setTimeout(reloadPreview, 300);});
  $fields.on('input change', '[data-asl-filter]', function () {dirty = true; syncFilterDependencies(); clearTimeout(previewTimer); previewTimer = setTimeout(reloadPreview, 300);});
  $fields.on('input change', '[data-asl-map]', function () {dirty = true; syncMapDependencies(); clearTimeout(previewTimer); previewTimer = setTimeout(reloadPreview, 300);});
  $fields.on('input change', '[data-asl-dimension]', function (event) {
    if (event.type === 'change') this.value = this.value.trim().toLowerCase();
    dirty = true;
    if (syncDimensionControls()) applyDimensionPreview();
  });
  $fields.on('change', '[data-asl-result-layout]', function () {$fields.find('[name="results-layout"]').val(this.value); dirty = true; applyLayoutPreview();});
  $fields.on('input change', '#asl-list-map-proportion', function () {
    if ($(this).closest('[data-asl-locked]').length) return;
    var breakpoints = activeDevice === 'desktop' ? ['lg', 'xl'] : (activeDevice === 'tablet' ? ['md'] : ['sm']);
    var value = this.value;
    if (!$fields.find('[name="results-layout"]').val()) {
      $fields.find('[name="results-layout"]').val($fields.find('[data-asl-result-layout]:checked').val() || 'left');
    }
    breakpoints.forEach(function (breakpoint) {$fields.find('[name="list-width-' + breakpoint + '"]').val(value);});
    dirty = true; applyLayoutPreview();
  });
  $fields.on('click', '[data-asl-style]', function () {
    if ($(this).is('[data-asl-locked]')) {
      return;
    }
    var style = $(this).data('asl-style');
    var selectedStyle = style;
    if (template === 'template-0') {
      if (style === 'default') style = 'modern';
      else if (style === 'modern') style = 'default';
    }
    // Define every color role so switching presets never inherits the previous palette.
    var palettes = {
      "default": {
        "primary": "#3454d1",
        "surface": "#ffffff",
        "panel": "#f2f5ff",
        "soft": "#e3eaff",
        "text": "#374567",
        "title": "#17264d",
        "muted": "#61708e",
        "border": "#c6d2ef",
        "buttonText": "#ffffff"
      },
      "modern": {
        "primary": "#292524",
        "surface": "#fffdfa",
        "panel": "#f7f3ee",
        "soft": "#eae2d8",
        "text": "#49413b",
        "title": "#292524",
        "muted": "#75685e",
        "border": "#d6c8ba",
        "buttonText": "#fffdfa"
      },
      "minimal": {
        "primary": "#087f74",
        "surface": "#ffffff",
        "panel": "#f2faf7",
        "soft": "#dcefe8",
        "text": "#345650",
        "title": "#163e36",
        "muted": "#5d7870",
        "border": "#bdd9cf",
        "buttonText": "#ffffff"
      },
      "dark": {
        "primary": "#b8a4ff",
        "surface": "#171b2e",
        "panel": "#101426",
        "soft": "#2c304b",
        "text": "#e3e6f2",
        "title": "#faf9ff",
        "muted": "#adb6d0",
        "border": "#505975",
        "buttonText": "#171b2e"
      }
    };
    // Share Template 0's Quick Style color schemes with Templates 2, 3, 4, and 5.
    if (['template-0', 'template-2', 'template-3', 'template-4', 'template-5'].indexOf(template) !== -1) {
      palettes = {
        default: {primary: '#2563eb', surface: '#ffffff', panel: '#f3f8ff', soft: '#eff6ff', text: '#111827', title: '#111827', muted: '#6b7280', border: '#e5e7eb', buttonText: '#ffffff'},
        modern: {primary: '#dc2626', surface: '#ffffff', panel: '#fef2f2', soft: '#fee2e2', text: '#111827', title: '#111827', muted: '#6b7280', border: '#e5e7eb', buttonText: '#ffffff'},
        minimal: {primary: '#10b981', surface: '#ffffff', panel: '#f8faf9', soft: '#ecfdf5', text: '#111827', title: '#111827', muted: '#6b7280', border: '#e5e7eb', buttonText: '#ffffff'},
        dark: {primary: '#ef4444', surface: '#111827', panel: '#0b1220', soft: '#7f1d1d', text: '#f9fafb', title: '#f9fafb', muted: '#9ca3af', border: '#1f2937', buttonText: '#ffffff'}
      };
    }
    if (template === 'template-0' && style === 'default') {
      palettes.default = {primary: '#2563eb', surface: '#ffffff', panel: '#ffffff', soft: '#f2f6fc', text: '#777971', title: '#252823', muted: '#777971', border: '#e5e2da', buttonText: '#ffffff'};
    }
    if (template === 'template-2' && style === 'modern') {
      palettes.modern = {primary: '#2563eb', surface: '#ffffff', panel: '#edf5ff', soft: '#213e60', text: '#d8e5f2', title: '#f1f7ff', muted: '#adc4db', border: '#cbdcf0', buttonText: '#ffffff'};
    }
    if (template === 'template-4' && style === 'default') {
      palettes.default = {primary: '#ff451d', surface: '#ffffff', panel: '#ffffff', soft: '#fff0eb', text: '#526779', title: '#263e50', muted: '#526779', border: '#dce3e8', buttonText: '#ffffff'};
    }
    if (template === 'template-4' && style === 'modern') {
      palettes.modern = {primary: '#ff451d', surface: '#f4fbfe', panel: '#e5f6fd', soft: '#ffede7', text: '#526779', title: '#263e50', muted: '#526779', border: '#cce5f0', buttonText: '#ffffff'};
    }
    if (template === 'template-4' && style === 'minimal') {
      palettes.minimal = {primary: '#0b3550', surface: '#f8fafc', panel: '#ffffff', soft: '#e9eff3', text: '#40566b', title: '#172f45', muted: '#526779', border: '#dce3e8', buttonText: '#ffffff'};
    }
    if (template === 'template-4' && style === 'dark') {
      palettes.dark = {primary: '#ff451d', surface: '#0c293b', panel: '#031b2a', soft: '#173b51', text: '#c6d5df', title: '#edf4f8', muted: '#b3c6d3', border: '#345264', buttonText: '#ffffff'};
    }
    if (template === 'template-3') {
      palettes = {
        default: {primary: '#ed2905', surface: '#ffffff', panel: '#ffffff', soft: '#ffede7', text: '#405263', title: '#172c3d', muted: '#627384', border: '#e1e6eb', buttonText: '#ffffff'},
        modern: {primary: '#ff6425', surface: '#f8fcff', panel: '#ffffff', soft: '#fff0e8', text: '#40586d', title: '#17344c', muted: '#637d91', border: '#e2edf4', buttonText: '#ffffff'},
        minimal: {primary: '#06476d', surface: '#ffffff', panel: '#ffffff', soft: '#edf3f7', text: '#344e63', title: '#163247', muted: '#61788a', border: '#dce5ec', buttonText: '#ffffff'},
        dark: {primary: '#ff6b0a', surface: '#082331', panel: '#061c28', soft: '#123447', text: '#d2dfe7', title: '#f1f6fa', muted: '#a9bfcd', border: '#284452', buttonText: '#ffffff'}
      };
    }
    if (template === 'template-5') {
      palettes = {
        default: {primary: '#e92505', surface: '#ffffff', panel: '#ffffff', soft: '#fff0eb', text: '#8a8f96', title: '#252b35', muted: '#6b7280', border: '#e5e7eb', buttonText: '#ffffff'},
        modern: {primary: '#ff6425', surface: '#f0faff', panel: '#f0faff', soft: '#e5f3fa', text: '#85919b', title: '#252f3c', muted: '#6c7885', border: '#dceaf2', buttonText: '#ffffff'},
        minimal: {primary: '#082746', surface: '#ffffff', panel: '#ffffff', soft: '#edf2f7', text: '#8a8f96', title: '#182535', muted: '#6b7280', border: '#e0e5eb', buttonText: '#ffffff'},
        dark: {primary: '#ff650a', surface: '#091f2e', panel: '#061b29', soft: '#193747', text: '#bdcbd5', title: '#f5f7fa', muted: '#c2cdd7', border: '#344c5c', buttonText: '#ffffff'}
      };
    }
    if (template === 'template-1') {
      palettes = {
        default: {primary: '#064f80', surface: '#ffffff', panel: '#06476d', soft: '#e5f2fa', text: '#334b60', title: '#064f80', muted: '#6b8195', border: '#e7eef4', buttonText: '#ffffff'},
        modern: {primary: '#ff5b0a', surface: '#ffffff', panel: '#eaf5fc', soft: '#ffe7d8', text: '#344e64', title: '#06476d', muted: '#6b8195', border: '#c8dce9', buttonText: '#ffffff'},
        minimal: {primary: '#064f80', surface: '#f8fafc', panel: '#ffffff', soft: '#edf2f7', text: '#405b72', title: '#06476d', muted: '#72869c', border: '#dce5ee', buttonText: '#ffffff'},
        dark: {primary: '#ff6500', surface: '#0e202c', panel: '#0b1e2b', soft: '#183344', text: '#e4edf4', title: '#ffffff', muted: '#afc2d0', border: '#2d4759', buttonText: '#ffffff'}
      };
    }
    if (template === 'template-6') {
      palettes = {
        default: {primary: '#c90000', surface: '#ffffff', panel: '#f5fbff', soft: '#fff0f0', text: '#c90000', title: '#c90000', muted: '#c90000', border: '#e7bcbc', buttonText: '#ffffff'},
        modern: {primary: '#ff5000', surface: '#ffffff', panel: '#fff4ed', soft: '#ffe6d8', text: '#ff5000', title: '#ff5000', muted: '#ff5000', border: '#ffc6aa', buttonText: '#ffffff'},
        minimal: {primary: '#082f49', surface: '#ffffff', panel: '#f8fafc', soft: '#edf2f7', text: '#17364f', title: '#082f49', muted: '#657181', border: '#d5dde5', buttonText: '#ffffff'},
        dark: {primary: '#ff5000', surface: '#031b29', panel: '#031b29', soft: '#153545', text: '#edf3f8', title: '#ffffff', muted: '#b8c6d1', border: '#405560', buttonText: '#ffffff'}
      };
    }
    var palette = palettes[style];
    if (!palette) return;
    $fields.find('[name="quick-style"]').val(selectedStyle);
    var colors = {primary: palette.primary};
    if (template === 'template-3') {
      colors['radius-size'] = style === 'default' ? '0' : '24';
    }
    function assign(keys, value) {keys.forEach(function (key) {colors[key] = value;});}
    assign(['header', 'list-panel-bg', 'panel-background'], palette.panel);
    if (template === 'template-2' && style === 'minimal') {
      colors.header = '#ffffff';
    }
    assign(['list-bg', 'infobox-bg', 'input-background', 'control-background', 'card-background'], palette.surface);
    assign(['secondary', 'highlighted', 'image-bg', 'state-btn-bg-color'], palette.soft);
    assign(['color', 'infobox-color', 'input-color', 'search-text', 'label-color', 'control-color', 'list-font-color'], palette.text);
    assign(['header-color', 'list-title', 'heading-color', 'highlighted-list-color', 'state-btn-color'], palette.title);
    assign(['list-sub-title', 'head-sub-title'], palette.muted);
    assign(['infobox-a', 'marker-color', 'search-btn-bg', 'action-btn-bg', 'btn-bg-color', 'button-background', 'state-active-btn-bg-color', 'list-order-btn-bg-color', 'state-label-shadow-color'], palette.primary);
    assign(['search-btn-color', 'action-btn-color', 'btn-color', 'button-color', 'state-active-btn-color', 'list-order-btn-color', 'state-label-color'], palette.buttonText);
    colors['control-border-color'] = palette.border;
    colors['action-sec-btn-bg'] = palette.surface;
    colors['action-sec-btn-color'] = palette.primary;
    colors['search-bar-bg'] = palette.surface;
    colors['search-bar-color'] = palette.text;
    colors['search-bar-placeholder'] = palette.muted;
    colors['search-bar-border'] = palette.border;
    if (template === 'template-6') {
      colors['input-color'] = style === 'dark' ? '#b8c6d1' : '#6b7280';
      colors['state-btn-bg-color'] = palette.panel;
      colors['state-btn-color'] = style === 'dark' ? '#ffffff' : palette.primary;
      colors['state-label-color'] = palette.primary;
      colors['state-label-shadow-color'] = style === 'dark' ? palette.panel : '#ffffff';
      colors['heading-color'] = style === 'dark' ? '#ffffff' : palette.primary;
      colors['list-title'] = style === 'dark' ? '#ffffff' : palette.primary;
      colors['list-font-color'] = palette.text;
    }
    if (template === 'template-5') {
      colors.label = style === 'dark' ? '#ffffff' : '#000000';
      colors['header-color'] = (style === 'default' || style === 'modern') ? palette.primary : (style === 'minimal' ? palette.primary : palette.title);
      colors['search-bar-bg'] = style === 'dark' ? '#19313f' : '#f3f4f5';
      colors['search-bar-color'] = style === 'dark' ? palette.text : '#374151';
      colors['search-bar-placeholder'] = style === 'dark' ? '#9aafbd' : '#9298a0';
      colors['marker-color'] = style === 'default' ? '#ff4b50' : (style === 'dark' ? '#ff9500' : palette.primary);
    }
    if (template === 'template-1') {
      colors['header-color'] = style === 'default' ? '#ffffff' : palette.title;
      colors['radius-size'] = style === 'minimal' ? '4' : '6';
      colors['control-bg'] = palette.panel;
      colors['control-color'] = colors['header-color'];
      if (style === 'default') {
        colors['search-btn-bg'] = palette.surface;
        colors['search-btn-color'] = palette.primary;
      }
      colors['head-title'] = style === 'minimal' ? palette.title : '#ffffff';
      colors['highlighted-list-color'] = palette.title;
      colors.secondary = style === 'default' ? '#ff6b24' : (style === 'minimal' ? '#ded2b8' : palette.primary);
      colors['search-bar-bg'] = style === 'modern' ? '#ffffff' : (style === 'minimal' ? '#e8e1d1' : (style === 'dark' ? '#172f40' : palette.surface));
      colors['action-sec-btn-bg'] = (style === 'minimal' || style === 'dark') ? palette.soft : palette.primary;
      colors['action-sec-btn-color'] = (style === 'minimal' || style === 'dark') ? palette.primary : palette.buttonText;
      if (style === 'dark') {
        colors['control-bg'] = palette.panel;
        colors['control-color'] = '#ffffff';
        colors['action-sec-btn-bg'] = palette.primary;
        colors['action-sec-btn-color'] = '#ffffff';
      }
      colors['tab-selected-bg'] = colors.secondary;
      colors['tab-selected-color'] = style === 'minimal' ? palette.title : '#ffffff';
      if (style === 'minimal') {
        colors.secondary = '#ff5b0a';
        colors['head-title'] = '#ffffff';
        colors['search-bar-color'] = '#064f80';
        colors['search-bar-bg'] = '#ffffff';
        colors['search-btn-bg'] = '#ffffff';
        colors['search-btn-color'] = palette.primary;
        colors['control-bg'] = '#ffffff';
        colors['control-color'] = '#064f80';
        colors['action-sec-btn-bg'] = '#b2bfcc';
        colors['action-sec-btn-color'] = '#ffffff';
        colors['tab-selected-bg'] = palette.primary;
        colors['tab-selected-color'] = palette.soft;
      }
      if (style === 'modern') {
        colors.highlighted = colors.header;
        colors.secondary = '#087bb6';
        colors['control-bg'] = palette.primary;
        colors['control-color'] = '#ffffff';
        colors['tab-selected-bg'] = palette.primary;
        colors['tab-selected-color'] = '#ffffff';
      }
    }
    if (template === 'template-0') {
      if (style === 'modern' || style === 'minimal') colors.color = '#555D66';
      colors['tab-selected-bg'] = (style === 'default' || style === 'minimal') ? palette.surface : palette.primary;
      colors['tab-selected-color'] = (style === 'default' || style === 'minimal') ? palette.primary : palette.buttonText;
      if (style === 'minimal') colors['action-sec-btn-color'] = '#059669';
      if (style === 'default') {
        colors.color = '#555D66';
        colors['header-color'] = palette.primary;
        colors['image-bg'] = '#e1eafb';
        colors['tab-selected-bg'] = '#e1eafb';
        colors['tab-selected-color'] = palette.primary;
        colors['action-sec-btn-bg'] = palette.surface;
        colors['action-sec-btn-color'] = palette.primary;
        colors['action-btn-bg'] = palette.primary;
        colors['action-btn-color'] = palette.buttonText;
      }
      if (style === 'dark') {
        colors.highlighted = '#243244';
        colors['search-bar-bg'] = '#1f2937';
        colors['image-bg'] = 'transparent';
        colors['action-sec-btn-bg'] = '#ffffff';
      }
      colors['tab-selected-border'] = colors['tab-selected-bg'];
      colors['action-sec-btn-border'] = style === 'dark' ? colors['action-sec-btn-bg'] : palette.primary;
    }

    if (template === 'template-0' && style === 'minimal') {
      colors['header-color'] = palette.primary;
      colors['action-btn-bg'] = palette.surface;
      colors['action-btn-color'] = style === 'minimal' ? '#059669' : palette.muted;
    }
    colors['action-btn-border'] = colors['action-btn-bg'];
    if (template === 'template-2' && style === 'modern') {
      colors['header-color'] = '#17365a';
      colors['list-bg'] = '#102a43';
      colors['search-bar-color'] = '#244363';
      colors['search-bar-placeholder'] = '#647d96';
      colors['control-color'] = '#244363';
      colors['control-bg'] = '#ffffff';
      colors['infobox-color'] = '#244363';
      colors['tab-selected-bg'] = palette.primary;
      colors['tab-selected-color'] = palette.buttonText;
      colors['action-sec-btn-bg'] = '#213e60';
      colors['action-sec-btn-color'] = '#dbeafe';
    }
    if (template === 'template-0' && style === 'minimal') {
      colors['action-btn-border'] = palette.primary;
    }
    if (template === 'template-4' && style === 'default') {
      colors['list-bg'] = '#fbfdfd';
      colors['action-sec-btn-color'] = palette.title;
      colors['action-sec-btn-bg'] = palette.surface;
      colors['control-color'] = palette.surface;
      colors['control-bg'] = palette.text;
      colors['tab-selected-bg'] = palette.primary;
      colors['tab-selected-color'] = palette.buttonText;
      colors['highlighted-list-color'] = palette.title;
    }
    if (template === 'template-4' && style === 'modern') {
      colors['search-bar-bg'] = '#ffffff';
      colors['action-sec-btn-color'] = palette.primary;
      colors['action-sec-btn-bg'] = palette.surface;
      colors['control-color'] = palette.surface;
      colors['control-bg'] = palette.text;
      colors['tab-selected-bg'] = palette.primary;
      colors['tab-selected-color'] = palette.buttonText;
      colors['highlighted-list-color'] = palette.title;
    }
    if (template === 'template-4' && style === 'minimal') {
      colors['action-sec-btn-color'] = palette.primary;
      colors['action-sec-btn-bg'] = '#ffffff';
      colors['control-color'] = '#ffffff';
      colors['control-bg'] = palette.text;
      colors['tab-selected-bg'] = palette.primary;
      colors['tab-selected-color'] = palette.buttonText;
      colors['highlighted-list-color'] = palette.title;
    }
    if (template === 'template-4' && style === 'dark') {
      colors['search-bar-bg'] = '#14364e';
      colors['action-sec-btn-color'] = palette.text;
      colors['action-sec-btn-bg'] = palette.panel;
      colors['control-color'] = palette.text;
      colors['control-bg'] = palette.surface;
      colors['tab-selected-bg'] = palette.primary;
      colors['tab-selected-color'] = palette.buttonText;
      colors['highlighted-list-color'] = palette.title;
    }
    if (selectedStyle === 'default' && /^template-([0-6]|list|list-2)$/.test(template)) {
      // Use the server's registered default colors, including extension fields.
      colors = $fields.find('[data-asl-default-colors]').data('asl-default-colors');
      if (!colors || !colors.primary) return;
    }
    $.each(colors, function (key, value) {$fields.find('[name="' + key + '"]').val(value).siblings('.colorpicker').val(value === 'transparent' ? '#ffffff' : value);});
    syncPrimaryColorLinks();
    $fields.find('[data-asl-style]').attr('aria-pressed', 'false'); $(this).attr('aria-pressed', 'true');
    dirty = true; updatePreview();
  });
  $(window).on('beforeunload', function (event) {if (dirty) {event.preventDefault(); event.originalEvent.returnValue = '';}});
  if ($('#asl-ui-template option:not(:disabled)[value="' + ASL_CUSTOMIZER.template + '"]').length) $('#asl-ui-template').val(ASL_CUSTOMIZER.template);
  loadTemplate();
})(jQuery);
