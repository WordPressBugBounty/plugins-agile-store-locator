var asl_engine = window['asl_engine'] || {};

(function($, app_engine) {
  'use strict';

    /**
     * [shortcode_generator description]
     * @return {[type]} [description]
     */
    app_engine['shortcode_generator'] = function() {

      var $modal = $('#insert-sl-shortcode');

      // Keep the modal in the same stacking context as Bootstrap's backdrop.
      // WPBakery raises .modal-backdrop to z-index 100001 on the editor page.
      if ($modal.length) {
        $modal.appendTo(document.body).css('z-index', 100002);
      }

      function hide_shortcode_modal() {
        if ($modal.length && window.bootstrap && window.bootstrap.Modal) {
          window.bootstrap.Modal.getOrCreateInstance($modal[0]).hide();
        }
      }

      $modal.find('[data-bs-dismiss="smodal"]').on('click', function() {
        hide_shortcode_modal();
      });

      var picker_map = null;
      var picker_marker = null;
      var $latitude = $modal.find('#asl-shortcode-default-lat');
      var $longitude = $modal.find('#asl-shortcode-default-lng');
      var $picker = $modal.find('#asl-shortcode-map-picker');
      var $map_canvas = $modal.find('#asl-shortcode-picker-map');

      function set_center(lng, lat) {
        $latitude.val(Number(lat).toFixed(6));
        $longitude.val(Number(lng).toFixed(6));
        if (picker_marker) picker_marker.setLngLat([lng, lat]);
      }

      $modal.find('#asl-shortcode-pick-center').on('click', function() {
        var open = $picker.prop('hidden');
        $picker.prop('hidden', !open);
        $(this).attr('aria-expanded', open ? 'true' : 'false');
        if (!open) return;
        if (picker_map) {
          picker_map.resize();
          return;
        }
        if (!window.maplibregl || typeof window.maplibregl.Map !== 'function') {
          $modal.find('#asl-shortcode-map-error').prop('hidden', false);
          return;
        }
        var lat = Number($latitude.val() || $map_canvas.data('lat'));
        var lng = Number($longitude.val() || $map_canvas.data('lng'));
        var zoom = Number($map_canvas.data('zoom'));
        if (!Number.isFinite(lat) || lat < -90 || lat > 90) lat = 0;
        if (!Number.isFinite(lng) || lng < -180 || lng > 180) lng = 0;
        if (!Number.isFinite(zoom) || zoom < 1 || zoom > 18) zoom = 4;
        try {
          picker_map = new maplibregl.Map({
            container: $map_canvas[0],
            center: [lng, lat],
            zoom: zoom,
            style: {
              version: 8,
              sources: { osm: { type: 'raster', tiles: ['https://tile.openstreetmap.org/{z}/{x}/{y}.png'], tileSize: 256, attribution: '© OpenStreetMap contributors' } },
              layers: [{ id: 'osm', type: 'raster', source: 'osm' }]
            }
          });
        } catch (error) {
          $modal.find('#asl-shortcode-map-error').prop('hidden', false);
          return;
        }
        picker_map.addControl(new maplibregl.NavigationControl(), 'top-right');
        picker_marker = new maplibregl.Marker({ draggable: true }).setLngLat([lng, lat]).addTo(picker_map);
        picker_map.on('click', function(event) { set_center(event.lngLat.lng, event.lngLat.lat); });
        picker_marker.on('dragend', function() {
          var position = picker_marker.getLngLat();
          set_center(position.lng, position.lat);
        });
        picker_map.on('error', function() { $modal.find('#asl-shortcode-map-error').prop('hidden', false); });
        setTimeout(function() { picker_map.resize(); }, 50);
      });

      $latitude.add($longitude).on('change', function() {
        var lat = Number($latitude.val());
        var lng = Number($longitude.val());
        if (picker_map && $latitude.val() && $longitude.val() && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
          picker_map.setCenter([lng, lat]);
          picker_marker.setLngLat([lng, lat]);
        }
      });

      // Generate Shortcode
      $('#sl-add-shortcode').on('click',function(){

        var $form = $modal.find('#sl-shortcode-popup');
        var shortcode_attrs = [];
        var latitude = $latitude.val();
        var longitude = $longitude.val();
        var lat_number = Number(latitude);
        var lng_number = Number(longitude);
        if ((latitude || longitude) && (!latitude || !longitude || !Number.isFinite(lat_number) || !Number.isFinite(lng_number) || lat_number < -90 || lat_number > 90 || lng_number < -180 || lng_number > 180)) {
          $latitude[0].setCustomValidity($latitude.data('center-error'));
          $latitude[0].reportValidity();
          return;
        }
        $latitude[0].setCustomValidity('');
        var search_settings = {
          automatic: ['automatic', '4'], google_new: ['google', '4'],
          google_legacy: ['google', '0'], nominatim: ['nominatim', '4'],
          geoapify: ['geoapify', '4'], mapbox: ['mapbox', '4'],
          geocode_enter: ['google', '3'], disabled: ['disabled', '0']
        };

        $form.serializeArray().forEach(function(field) {
          if (!field.value || field.name === 'category' || field.name === 'default_lat' || field.name === 'default_lng') return;
          if (field.name === 'template' && field.value !== '0') return;
          if (field.name === 'search_mode') {
            var search = search_settings[field.value];
            if (search) {
              shortcode_attrs.push('search_provider="' + search[0] + '"');
              shortcode_attrs.push('search_type="' + search[1] + '"');
            }
            return;
          }
          shortcode_attrs.push(field.name + '="' + field.value.replace(/["\\\[\]]/g, '') + '"');
        });

        var categories = $form.find('[name="category"]:checked').map(function() { return this.value; }).get();
        categories = categories.filter(function(id) { return /^\d+$/.test(id); });
        if (categories.length) shortcode_attrs.push('category="' + categories.join(',') + '"');
        if (latitude && longitude) {
          shortcode_attrs.push('default_lat="' + lat_number + '"');
          shortcode_attrs.push('default_lng="' + lng_number + '"');
        }

        var shortcode = '[ASL_STORELOCATOR' + (shortcode_attrs.length ? ' ' + shortcode_attrs.join(' ') : '') + ']';
        if (window.asl_gutenberg_attrs && typeof window.asl_gutenberg_attrs.setAttributes === 'function') {
          window.asl_gutenberg_attrs.setAttributes({ shortcode: shortcode });
        } else {
          var prev_content = tmce_getContent('content');
          tmce_setContent(prev_content + shortcode);
          tmce_focus('content');
        }

          hide_shortcode_modal();

      });


      // get tmce content 
      function tmce_getContent(editor_id, textarea_id) {
        if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
        if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
        
        if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
          return tinyMCE.get(editor_id).getContent();
        }else{
          return jQuery('#'+textarea_id).val();
        }
      }

      // set tmce content
      function tmce_setContent(content, editor_id, textarea_id) {
        if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
        if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
        
        if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
          return tinyMCE.get(editor_id).setContent(content);
        }else{
          return jQuery('#'+textarea_id).val(content);
        }
      }

      // Focus on tmce
      function tmce_focus(editor_id, textarea_id) {
        if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
        if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
        
        if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
          return tinyMCE.get(editor_id).focus();
        }else{
          return jQuery('#'+textarea_id).focus();
        }
      }
    };


})(jQuery, asl_engine);
