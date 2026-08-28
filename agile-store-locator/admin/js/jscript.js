var asl_engine = window['asl_engine'] || {};

(function($, app_engine) {
  'use strict';

  /* Add the same searchable UI to every ddSlick control without changing its API. */
  if ($.fn.ddslick && !$.fn.ddslick.aslEnhanced) {
    var originalDdSlick = $.fn.ddslick;

    function enhanceDdSlick($controls) {
      $controls.each(function() {
        var $control = $(this),
            $options = $control.find('.dd-options');

        if (!$options.length || $options.find('.asl-dd-search').length) return;

        var $firstItem = $options.children('li').first();
        if (!$firstItem.length) return;

        var placeholder = (window.ASL_REMOTE && ASL_REMOTE.LANG.search_options) || 'Search...';
        var $searchInput = $('<input>', {type: 'search'}).attr({
          placeholder: placeholder,
          autocomplete: 'off',
          'aria-label': placeholder
        });
        var $search = $('<div>', {'class': 'asl-dd-search'}).append(
          $('<span>', {'class': 'asl-dd-search-icon', 'aria-hidden': 'true'}),
          $searchInput
        );

        $firstItem.prepend($search);
        $search.on('click mousedown', function(e) { e.stopPropagation(); });
        $search.find('input').on('input', function() {
          var query = $.trim($(this).val()).toLocaleLowerCase(),
              visible = 0;

          $options.children('li').each(function(index) {
            var $item = $(this),
                $option = $item.children('.dd-option'),
                label = $option.find('.dd-option-text').text().toLocaleLowerCase(),
                show = !query || label.indexOf(query) !== -1;

            $option.toggle(show);
            if (show) visible++;
            if (index > 0) $item.toggle(show);
          });

          $options.find('.asl-dd-empty').toggle(visible === 0);
        });

        $options.append(
          $('<li>', {'class': 'asl-dd-empty'}).text(
            (window.ASL_REMOTE && ASL_REMOTE.LANG.no_options_found) || 'No options found'
          )
        );
      });
    }

    $.fn.ddslick = function(option) {
      var controlIds = [];
      this.each(function() {
        if (this.id) controlIds.push(this.id);
      });
      var result = originalDdSlick.apply(this, arguments);
      if (typeof option === 'object' || typeof option === 'undefined') {
        var $renderedControls = $();
        $.each(controlIds, function(index, controlId) {
          var renderedControl = document.getElementById(controlId);
          if (renderedControl) $renderedControls = $renderedControls.add(renderedControl);
        });
        enhanceDdSlick($renderedControls);
      }
      return result;
    };
    $.fn.ddslick.aslEnhanced = true;
  }

  /* API method to get paging information */
  if($.fn.dataTableExt && $.fn.dataTableExt.oApi){
    
    $.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings ){return {"iStart":         oSettings._iDisplayStart,"iEnd":           oSettings.fnDisplayEnd(),"iLength":        oSettings._iDisplayLength,"iTotal":         oSettings.fnRecordsTotal(),"iFilteredTotal": oSettings.fnRecordsDisplay(),"iPage":          oSettings._iDisplayLength === -1 ?0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),"iTotalPages":    oSettings._iDisplayLength === -1 ?0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )};};

    /* Bootstrap style pagination control */
    $.extend($.fn.dataTableExt.oPagination,{bootstrap:{fnInit:function(i,a,e){var t=i.oLanguage.oPaginate,l=function(a){a.preventDefault(),i.oApi._fnPageChange(i,a.data.action)&&e(i)};$(a).addClass("pagination").append('<ul class="pagination mt-3"><li class="page-item prev disabled"><a class="page-link" href="#">&larr; '+t.sPrevious+'</a></li><li class="page-item next disabled"><a class="page-link" href="#">'+t.sNext+" &rarr; </a></li></ul>");var s=$("a",a);$(s[0]).bind("click.DT",{action:"previous"},l),$(s[1]).bind("click.DT",{action:"next"},l)},fnUpdate:function(i,e){var a,t,l,s,n,o=i.oInstance.fnPagingInfo(),g=i.aanFeatures.p,r=Math.floor(2.5);n=o.iTotalPages<5?(s=1,o.iTotalPages):o.iPage<=r?(s=1,5):o.iPage>=o.iTotalPages-r?(s=o.iTotalPages-5+1,o.iTotalPages):(s=o.iPage-r+1)+5-1;var d=g.length;for(a=0;a<d;a++){for($("li:gt(0)",g[a]).filter(":not(:last)").remove(),t=s;t<=n;t++)l=t==o.iPage+1?"active":"",$('<li class="page-item '+l+'"><a class="page-link" href="#">'+t+"</a></li>").insertBefore($("li:last",g[a])[0]).bind("click",function(a){a.preventDefault(),i._iDisplayStart=(parseInt($("a",this).text(),10)-1)*o.iLength,e(i)});0===o.iPage?$("li:first",g[a]).addClass("disabled"):$("li:first",g[a]).removeClass("disabled"),o.iPage===o.iTotalPages-1||0===o.iTotalPages?$("li:last",g[a]).addClass("disabled"):$("li:last",g[a]).removeClass("disabled")}}}});
  }
  /**
   * [toastIt toast it based on the error or message]
   * @param  {[type]} _response [description]
   * @return {[type]}           [description]
   */
  var toastIt = function(_response) {

    if(_response.success) {
      atoastr.success(_response.msg || _response.message);
    }
    else {
      atoastr.error(_response.error || _response.message || _response.msg);
    }
  };

  // Debounce function
  function ASLDebounce(func, delay) {
    let timeoutId;
    return function (...args) {
        const context = this; // Preserve the `this` context
        clearTimeout(timeoutId); // Clear the previous timer
        timeoutId = setTimeout(() => {
            func.apply(context, args); // Execute the function after the delay
        }, delay);
    };
  }

  /**
   * [codeAddress description]
   * @param  {[type]} _address  [description]
   * @param  {[type]} _callback [description]
   * @return {[type]}           [description]
   */
  function codeAddress(_address, _callback) {

    if (window.ASLCommonMap && asl_configs && asl_configs.map_vendor === 'maplibre') {
      ASLCommonMap.geocodeAddress(_address, asl_configs).then(function(results) {
        if (!results.length) throw new Error('ZERO_RESULTS');
        var point = results[0].location;
        map.setCenter(point);
        _callback({location: {
          lat: function() { return point.lat; },
          lng: function() { return point.lng; }
        }});
      }).catch(function(error) {
        if (error && error.code === 'NO_GEOCODER_CONFIGURED') return;
        atoastr.error(ASL_REMOTE.LANG.geocode_fail + error.message);
      });
      return;
    }

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'address': _address }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        _callback(results[0].geometry);
      } else {
        atoastr.error(ASL_REMOTE.LANG.geocode_fail + status);
      }
    });
  };

  /**
   * [generateUniqueId Unique ID]
   * @return {[type]} [description]
   */
  function generateUniqueId() {
    const timestamp = new Date().getTime();
    const random = Math.floor(Math.random() * 1000000); // Adjust the range as needed
    const uniqueId = `${timestamp}-${random}`;
    return uniqueId;
  };

  /**
   * [isEmpty description]
   * @param  {[type]}  obj [description]
   * @return {Boolean}     [description]
   */
  function isEmpty(obj) {

    if (obj == null) return true;
    if (typeof(obj) == 'string' && obj == '') return true;
    return Object.keys(obj).length === 0;
  };

  // Asynchronous Load
  var map,
      map_object = {
        is_loaded: true,
        marker: null,
        changed: false,
        store_location: null,
        search_box: null,
        map_marker: null,
        /**
         * [intialize description]
         * @param  {[type]} _callback [description]
         * @return {[type]}           [description]
         */
        intialize: function(_callback) {

          if (window.ASLCommonMap && asl_configs && asl_configs.map_vendor === 'maplibre') {
            this.cb = _callback;
            if (typeof window.asl_map_intialized === 'function') window.asl_map_intialized();
            return;
          }

          var API_KEY = '';
          if (asl_configs && asl_configs.api_key) {
            API_KEY = '&key=' + asl_configs.api_key;
          }

          var script = document.createElement('script');
          script.type = 'text/javascript';
          script.src = '//maps.googleapis.com/maps/api/js?libraries=places&' +
            'loading=async&callback=asl_map_intialized' + API_KEY + '&v=weekly';

          document.body.appendChild(script);
          this.cb = _callback;
        },
        /**
         * [render_a_map description]
         * @param  {[type]} _lat [description]
         * @param  {[type]} _lng [description]
         * @return {[type]}      [description]
         */
        render_a_map: function(_lat, _lng) {

          var hdlr      = this,
            map_div     = document.getElementById('map_canvas'),
            _draggable  = true;

          
          hdlr.store_location = (_lat && _lng) ? [parseFloat(_lat), parseFloat(_lng)] : [-37.815, 144.965];

          var isStoreForm = !!document.getElementById('frm-addstore'),
              isSettingsMapModal = !!document.getElementById('asl-map-modal');

          if (window.ASLCommonMap && (isStoreForm || isSettingsMapModal || (asl_configs && asl_configs.map_vendor === 'maplibre'))) {
            if (!map_div) return false;
            var commonLocation = {lat: hdlr.store_location[0], lng: hdlr.store_location[1]};
            var commonPicker = ASLCommonMap.createLocationPicker({
              container: map_div,
              config: asl_configs || {},
              center: commonLocation,
              zoom: (asl_configs && asl_configs.zoom) ? parseInt(asl_configs.zoom) : 5,
              searchZoom: 14,
              searchInput: document.getElementById('asl-setting-search-box'),
              latitudeInput: document.getElementById('asl_txt_lat'),
              longitudeInput: document.getElementById('asl_txt_lng'),
              markerUrl: ASL_Instance.url + 'icon/default.png',
              draggable: isStoreForm || isSettingsMapModal,
              onChange: function(location) {
                hdlr.store_location = [location.lat, location.lng];
                hdlr.changed = true;
                app_engine.pages.store_changed(hdlr.store_location);
              }
            });
            hdlr.common_picker = commonPicker;
            hdlr.map_instance = map = commonPicker.map;
            hdlr.map_marker = commonPicker.marker;
            return;
          }

          var latlng = new google.maps.LatLng(hdlr.store_location[0], hdlr.store_location[1]);

          if (!map_div) return false;

          var zoom_value = (window['asl_configs'] && asl_configs.zoom)? parseInt(asl_configs.zoom): 5;
          var mapOptions = {
            zoom: zoom_value,
            center: latlng,
            mapTypeId: (asl_configs && asl_configs.map_type) ? asl_configs.map_type : google.maps.MapTypeId.ROADMAP,
            styles: [{ "stylers": [{ "saturation": -100 }, { "gamma": 1 }] }, { "elementType": "labels.text.stroke", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.business", "elementType": "labels.text", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.business", "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.place_of_worship", "elementType": "labels.text", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.place_of_worship", "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] }, { "featureType": "road", "elementType": "geometry", "stylers": [{ "visibility": "simplified" }] }, { "featureType": "water", "stylers": [{ "visibility": "on" }, { "saturation": 50 }, { "gamma": 0 }, { "hue": "#50a5d1" }] }, { "featureType": "administrative.neighborhood", "elementType": "labels.text.fill", "stylers": [{ "color": "#333333" }] }, { "featureType": "road.local", "elementType": "labels.text", "stylers": [{ "weight": 0.5 }, { "color": "#333333" }] }, { "featureType": "transit.station", "elementType": "labels.icon", "stylers": [{ "gamma": 1 }, { "saturation": 50 }] }]
          };

          hdlr.map_instance = map = new google.maps.Map(map_div, mapOptions);

          // && navigator.geolocation && _draggable
          if ((!hdlr.store_location || isEmpty(hdlr.store_location[0]))) {

            hdlr.add_marker(latlng);
          }
          else if (hdlr.store_location) {
            if (isNaN(hdlr.store_location[0]) || isNaN(hdlr.store_location[1])) return;
            //var loc = new google.maps.LatLng(hdlr.store_location[0], hdlr.store_location[1]);
            hdlr.add_marker(latlng);
            map.panTo(latlng);
          }

          //  Add the searchbox
          var search_control = document.getElementById('asl-setting-search-box');
          
          if(search_control) {

            //  Add the alert after the search
            if(!asl_configs.api_key) {
              
              var error_alert = $('<div>').addClass('alert alert-danger mt-2').text(ASL_REMOTE.LANG.api_key_missing);
              $(search_control).after(error_alert);
            }

            hdlr.search_box = new google.maps.places.SearchBox(document.getElementById('asl-setting-search-box'));
            
            hdlr.map_instance.addListener('bounds_changed', function() {
              hdlr.search_box.setBounds(hdlr.map_instance.getBounds());
            });

            hdlr.search_box.addListener('places_changed', function() {
              hdlr.search();
            });
          }
        },
        /**
         * [search Add the search]
         * @return {[type]} [description]
         */
        search: function() {
            
          var hdlr   = this;
          var places = hdlr.search_box.getPlaces();
          
          if (!places || places.length == 0) {
            return;
          }

          var bounds = new google.maps.LatLngBounds();

          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }

            if (place.geometry.viewport) {
              bounds.union(place.geometry.viewport);
            } 
            else {
              bounds.extend(place.geometry.location);
            }

            hdlr.map_marker.setPosition(place.geometry.location); // Update marker position
          });

          hdlr.map_instance.fitBounds(bounds);
        },
        /**
         * [add_marker description]
         * @param {[type]} _loc [description]
         */
        add_marker: function(_loc) {

          var hdlr = this;

          //  Create a marker
          hdlr.map_marker = new google.maps.Marker({
            draggable: true,
            position: _loc,
            map: map
          });

          //  Marker icon
          var marker_icon = new google.maps.MarkerImage(ASL_Instance.url + 'icon/default.png');
          
          hdlr.map_marker.setIcon(marker_icon);
          hdlr.map_instance.panTo(_loc);

          //  Add the map marker event for the dragend
          google.maps.event.addListener(
            hdlr.map_marker,
            'dragend',
            function() {

              hdlr.store_location = [hdlr.map_marker.position.lat(), hdlr.map_marker.position.lng()];
              hdlr.changed = true;
              var loc = new google.maps.LatLng(hdlr.map_marker.position.lat(), hdlr.map_marker.position.lng());
              hdlr.map_instance.panTo(loc);

              app_engine.pages.store_changed(hdlr.store_location);
            });
        }
    };

  ////////////////////////////////////
  //  Add the lang control switcher //
  ////////////////////////////////////
  var lang_ctrl = document.querySelector('#asl-lang-ctrl');
  
  if(lang_ctrl) {


    //  set the control lang
    var lang_value  = (window.wpCookies.get('asl-lang') || ASL_REMOTE.sl_lang || '');

    lang_ctrl.value = lang_value;

    //  reset in the storage
    window.wpCookies.set('asl-lang', lang_value);

    //  Reload Event
    $(lang_ctrl).on('change', function(e) {

      //  change in the storage
      window.wpCookies.set('asl-lang', lang_ctrl.value);

      window.location.reload();
    });
  }

  /**
   * [uploader AJAX Uploader]
   * @param  {[type]} $form [description]
   * @param  {[type]} _URL  [description]
   * @param  {[type]} _done [description]
   * @return {[type]}       [description]
   */
  app_engine.uploader = function($form, _URL, _done /*,_submit_callback*/ ) {


    function formatFileSize(bytes) {
      if (typeof bytes !== 'number') {
        return ''
      }
      if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB'
      }
      if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB'
      }
      return (bytes / 1000).toFixed(2) + ' KB'
    };

    var ul = $form.find('ul');
    $form[0].reset();


    $form.fileupload({
        url: _URL,
        dataType: 'json',
        //multipart: false,
        done: function(e, _data) {

          ul.empty();
          _done(e, _data);

          $form.find('.progress-bar').css('width', '0%');
          $form.find('.progress').hide();

          //reset form if success
          if (_data.result.success) {}
        },
        add: function(e, _data) {

          ul.empty();

          //Check file Extension
          var exten = _data.files[0].name.split('.'),
            exten = exten[exten.length - 1];
          if (['jpg', 'png', 'jpeg', 'gif', 'JPG', 'svg', 'zip', 'csv', 'kml'].indexOf(exten) == -1) {

            atoastr.error((ASL_REMOTE.LANG.invalid_file_error));
            return false;
          }


          var tpl = $('<li class="working"><p class="col-12 text-muted"><span class="float-left"></span></p></li>');
          tpl.find('p').text(_data.files[0].name.substr(0, 50)).append('<i class="float-right">' + formatFileSize(_data.files[0].size) + '</i>');
          _data.context = tpl.appendTo(ul);

          var jqXHR = null;
          $form.find('.btn-start').off().on('click', function() {


            /*if(_submit_callback){
              if(!_submit_callback())return false;
            }*/

            jqXHR = _data.submit();

            $form.find('.progress').show()
          });


          $form.find('.custom-file-label').html(_data.files[0].name);
        },
        progress: function(e, _data) {
          var progress = parseInt(_data.loaded / _data.total * 100, 10);
          $form.find('.progress-bar').css('width', progress + '%');
          $form.find('.sr-only').html(progress + '%');

          if (progress == 100) {
            _data.context.removeClass('working');
          }
        },
        fail: function(e, _data) {
          _data.context.addClass('error');
          $form.find('.upload-status-box').html(ASL_REMOTE.LANG.upload_fail).addClass('bg-warning alert')
        }
        /*
        formData: function(_form) {

          var formData = [{
            name: '_data[action]',
            value: 'asl_add_store'
          }]

          //  console.log(formData);
          return formData;
        }*/
      })
      .on('fileuploadsubmit', function(e, _data) {

        _data.formData = $form.ASLSerializeObject();

        if(lang_ctrl && lang_ctrl.value && _data.formData)
          _data.formData['asl-lang'] = lang_ctrl.value;
      })
      .prop('disabled', !$.support.fileInput)
      .parent().addClass($.support.fileInput ? undefined : 'disabled');
  };

  

  //http://harvesthq.github.io/chosen/options.html
  app_engine['pages'] = {
    _validate_page: function() {},
    /**
     * [store_changed Stores Changed]
     * @param  {[type]} _position [description]
     * @return {[type]}           [description]
     */
    store_changed: function(_position) {

      if($('#asl_txt_lat')[0])
        $('#asl_txt_lat').val(_position[0]);
      
      if($('#asl_txt_lng')[0])
        $('#asl_txt_lng').val(_position[1]);
    },
    /**
     * [manage_attribute Manage Attribute]
     * @return {[type]}           [description]
     */
    manage_attribute: function() {
      
      /**
       * [asl_attributes_tabs For each dropdown tabs]
       * @param  {[type]} _options [description]
       * @return {[type]}          [description]
       */
      $.fn.asl_attributes_tabs = function(_options) {

        var options = $.extend({},_options);
        var panim = null;


        /**
         * [attr_main Run all the methods of the attributes]
         * @return {[type]} [description]
         */
        function attr_main() {
          
          //  Main This
          var $this      = $(this),
              $section   = $this.find('> .asl-attr-listing'),
              $table     = $section.find('table'),
              tab_title  = $this.data('tab-title'),
              tab_plural = $this.data('tab-plural'),
              tab_single = $this.data('tab-single'),
              tab_name   = $this.data('tab-name'),
              is_special_tab = tab_name == 'specials';

            function attributeBrandOptions(selected_id) {
              var brands = ASL_REMOTE.attribute_brands || [],
                  brand_label = $('<div>').text(ASL_REMOTE.LANG.brand).html(),
                  html = '<div class="form-group asl-attr-brand-row"><label for="aswal2-input-brand">' + brand_label + '</label><select id="aswal2-input-brand" class="form-control aswal2-select"><option value="0">' + $('<div>').text(ASL_REMOTE.LANG.none).html() + '</option>';

              for(var brand_index = 0; brand_index < brands.length; brand_index++) {
                var brand = brands[brand_index],
                    brand_id = String(brand.id || ''),
                    selected = String(selected_id || '0') == brand_id ? ' selected="selected"' : '';

                html += '<option value="' + brand_id + '"' + selected + '>' + $('<div>').text(brand.name || '').html() + '</option>';
              }

              html += '</select></div>';

              return html;
            }

            function attributeModalFields(selected_brand_id, order_value, current_value) {
              var safe_value = $('<div>').text(current_value || '').html();

              return '<div class="asl-attr-modal-fields">' +
                '<div class="form-group asl-attr-name-row"><label for="aswal2-input">Name</label><input type="text" value="' + safe_value + '" placeholder="Enter the value" id="aswal2-input" class="form-control asl-attr-name-input"></div>' +
                (is_special_tab ? attributeBrandOptions(selected_brand_id) : '') +
                '<div class="form-group asl-attr-order-row"><label for="aswal2-input-ordr">Order</label><input type="number" value="' + order_value + '" placeholder="Enter the priority number" id="aswal2-input-ordr" class="form-control aswal2-input aswal2-ordr"></div>' +
              '</div>';
            }

            var attr_columns = [
              { "data": "check" },
              { "data": "id" },
              { "data": "name" }
            ];

            if(is_special_tab) {
              attr_columns.push({ "data": "brand_name" });
            }

            attr_columns = attr_columns.concat([
              { "data": "ordr" },
              { "data": "created_on" },
              { "data": "action" }
            ]);

            var action_col_index = attr_columns.length - 1;
            var attr_column_defs = [
              { 'bSortable': false, "width": "140px", "targets": 0 },
              { "width": "150px", "targets": 1 },
              { "width": "300px", "targets": 2 },
              { "width": "170px", "targets": action_col_index - 2 },
              { "width": "220px", "targets": action_col_index - 1 },
              { "width": "180px", "targets": action_col_index },
              { 'bSortable': false, 'aTargets': [0, action_col_index] }
            ];

            if(is_special_tab) {
              attr_column_defs.push({ "width": "220px", "targets": 3 });
            }

            var asInitVals = {};
            $table.dataTable({
              "bProcessing": true,
              "sPaginationType": "bootstrap",
              "bFilter": false,
              "bServerSide": true,
              "scrollX": true,
              "scrollCollapse": true,
              "sScrollX": "100%",
              "sScrollXInner": "100%",
              /*"aoColumnDefs": [
                { 'bSortable': false, 'aTargets': [ 1 ] }
              ],*/
              "bAutoWidth": false,
              "columnDefs": attr_column_defs,
              "iDisplayLength": 10,
              "sAjaxSource": ASL_REMOTE.URL + "?action=asl_ajax_handler&asl-nounce=" + ASL_REMOTE.nounce + "&sl-action=get_attributes&type=" + tab_name,
              "columns": attr_columns,
              'fnServerData': function(sSource, aoData, fnCallback) {
                $.get(sSource, aoData, function(json) {
                  fnCallback(json);
                }, 'json');
              },
              "fnServerParams": function(aoData) {
      
                //  add lang
                if(lang_ctrl)
                  aoData.push({"name": 'asl-lang',"value": lang_ctrl.value});
      
                //  Add the filters
                $table.find("thead input").each(function(i) {
                  if (this.value != "") {
                    aoData.push({
                      "name": 'filter[' + $(this).attr('data-id') + ']',
                      "value": this.value
                    });
                  }
                });
              },
              "order": [
                [1, 'desc']
              ],
              "fnInitComplete": function() {
                $table.fnAdjustColumnSizing();
              }
            });

            $this.closest('.tab-content').siblings('.nav').find('a[data-toggle="pill"]').on('shown.bs.tab click', function() {
              window.setTimeout(function() {
                $table.fnAdjustColumnSizing();
              }, 0);
            });
      
            //New Attribute
            $section.find('.btn-asl-new-attr').on('click', function(e) {
              
              aswal({
                  title: ASL_REMOTE.LANG.create + " " + tab_single,
                  text: ASL_REMOTE.LANG.add_new_question.replace('%s', tab_single),
                  html: attributeModalFields(0, 0, ''),
                  showCancelButton: true,
                  focusCancel: true,
                  confirmButtonColor: "#28a745",
                  confirmButtonText: ASL_REMOTE.LANG.create_it,
                  customClass: 'aswal-attr-modal',
                  preConfirm: function() {
      
      
                    return new Promise(function(resolve) {

                      var _value = $('#aswal2-input').val();
      
                      if ($.trim(_value) != '') {
                        resolve(_value);
                      } 
                      else {
      
                        aswal.showValidationError( tab_single +' value is required.');
                        return false;
                      }
                    })
                  }
                  /*inputValidator: function(value) {
                    return !value && 'You need to write something!'
                }*/
                })
                .then(function(result) {
      
                  if (result) {
      
                    var $attr_ordr_input =  $('#aswal2-input-ordr');
                    
                    var add_payload = { title: tab_title, name: tab_name, value: result, ordr: $attr_ordr_input.val() };

                      if(is_special_tab) {
                        add_payload.brand_id = $('#aswal2-input-brand').val();
                      }

                    ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=add_attribute", add_payload, function(_response) {
	      
                      toastIt(_response);
	                      
	                      if (_response.success) {

                          if(tab_name == 'brands') {
                            ASL_REMOTE.attribute_brands = ASL_REMOTE.attribute_brands || [];
                            ASL_REMOTE.attribute_brands.push({id: _response.id, name: result, ordr: $attr_ordr_input.val()});
                          }
	                        
                        $table.fnDraw();
                        return;
                      }
      
                    }, 'json');
                  }
                }, aswal.noop);
            });
      
            //Select all button
            $section.find('.select-all').on('click', function(e) {
      
              $section.find('.table input').attr('checked', 'checked');
      
            });
      
            //Delete Selected Attributes:: bulk
            $section.find('.btn-asl-delete-all').on('click', function(e) {
      
              var $tmp_categories = $section.find('.table input:checked');
      
              if ($tmp_categories.length == 0) {
                displayMessage('No Category selected', $(".dump-message"), 'alert alert-danger static', true);
                return;
              }
      
              var item_ids = [];
              $tmp_categories.each(function(i) {
                item_ids.push($(this).attr('data-id'));
              });
      
      
              aswal({
                title: "Delete " + tab_title,
                text: "Are you sure you want to delete selected " + tab_title + " ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Delete it!"
              }).then(function() {
      
                ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_attribute", { title: tab_title, name: tab_name, item_ids: item_ids, multiple: true }, function(_response) {
      
                  toastIt(_response);
      
                  if (_response.success) {
                    if(tab_name == 'brands' && ASL_REMOTE.attribute_brands) {
                      ASL_REMOTE.attribute_brands = ASL_REMOTE.attribute_brands.filter(function(brand) {
                        return item_ids.indexOf(String(brand.id)) === -1 && item_ids.indexOf(parseInt(brand.id)) === -1;
                      });
                    }

                    $table.fnDraw();
                    return;
                  }
      
      
                }, 'json');
              });
            });
      
      
      
            //show edit attribute model
            $table.find('tbody').on('click', '.edit_attr', function(e) {
      
              var _value = $(this).data('value'),
                _id      = $(this).data('id'),
                _ordr    = $(this).data('ordr'),
                  _brand_id = $(this).data('brand-id') || 0;
      
      
              aswal({
                  title: "Update " + tab_title,
                  text: "Update existing " + tab_title + " to new name",
                  html: attributeModalFields(_brand_id, _ordr, _value),
                  showCancelButton: true,
                  confirmButtonColor: "#28a745",
                  confirmButtonText: "Update it!",
                  customClass: 'aswal-attr-modal',
                  preConfirm: function() {
      
                    return new Promise(function(resolve) {

                      var _value = $('#aswal2-input').val();
      
                      if ($.trim(_value) != '') {
                        resolve(_value);
                      } else {
      
                        aswal.showValidationError('Field is empty.');
                        return false;
                      }
                    })
                  }
              })
              .then(function(result) {
      
                if (result) {
      
                  var $attr_ordr_input =  $('#aswal2-input-ordr');
      
                  var update_payload = { id: _id, title: tab_title, name: tab_name, value: result, ordr: $attr_ordr_input.val() };

                    if(is_special_tab) {
                      update_payload.brand_id = $('#aswal2-input-brand').val();
                    }

                  ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=update_attribute", update_payload, function(_response) {
	      
                    toastIt(_response);
	      
	                    if (_response.success) {

                        if(tab_name == 'brands' && ASL_REMOTE.attribute_brands) {
                          for(var brand_index = 0; brand_index < ASL_REMOTE.attribute_brands.length; brand_index++) {
                            if(String(ASL_REMOTE.attribute_brands[brand_index].id) == String(_id)) {
                              ASL_REMOTE.attribute_brands[brand_index].name = result;
                              ASL_REMOTE.attribute_brands[brand_index].ordr = $attr_ordr_input.val();
                              break;
                            }
                          }
                        }
	      
                      $table.fnDraw();
                      return;
                    }
      
                  }, 'json');
                }
              }, aswal.noop);
      
            });
      
      
            //  Show delete attribute model
            $table.find('tbody').on('click', '.delete_attr', function(e) {
      
              var _category_id = $(this).attr("data-id");
      
              aswal({
                title: "Delete " + tab_title,
                text: "Are you sure you want to delete " + tab_title + " " + _category_id + " ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Delete it!",
              }).then(
                function() {
      
                  ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_attribute", { title: tab_title, name: tab_name, category_id: _category_id }, function(_response) {
      
                    toastIt(_response);
      
                    if (_response.success) {
                      if(tab_name == 'brands' && ASL_REMOTE.attribute_brands) {
                        ASL_REMOTE.attribute_brands = ASL_REMOTE.attribute_brands.filter(function(brand) {
                          return String(brand.id) != String(_category_id);
                        });
                      }

                      $table.fnDraw();
                      return;
                    }
      
                  }, 'json');
      
                }
              );
            });
      
      
            //  Search
            $section.find("thead input").keyup(function(e) {
      
              if (e.keyCode == 13) {
                $table.fnDraw();
              }
            });
        };
        
        /*loop for each*/
        this.each(attr_main);

        return this;
      };

	      //   Main Loop
	      $('.asl-attr-tab').asl_attributes_tabs({});

	      $('.asl-attributes-cont .nav-pills li').on('click', function(e) {
	        if(e.target.tagName && e.target.tagName.toLowerCase() == 'a') {
	          return;
	        }

	        var $tab_link = $(this).find('a[data-toggle="pill"]');

	        if($tab_link.length) {
	          $tab_link.trigger('click');
	        }
	      });

	    },
    /**
     * [manage_categories description]
     * @return {[type]} [description]
     */
    manage_categories: function() {

      var table = null;
      var parent_categories;

      var asInitVals = {};
      table = $('#tbl_categories').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bFilter": false,
        "bServerSide": true,
        "bAutoWidth": true,
        "columnDefs": [
          { 'bSortable': false, "width": "140px", "targets": 0 },
          { "width": "150px", "targets": 1 },
          { "width": "260px", "targets": 2,
            render: function (data, type, full, meta) {
              return '<a class="sl-store-title" href="'+ASL_Instance.manage_stores_url + full.id +'">' + data + "</a>";
            }
          },
          { "width": "220px", "targets": 3 },
          { "width": "170px", "targets": 4 },
          { "width": "200px", "targets": 5 },
          { "width": "220px", "targets": 6 },
          { 'bSortable': false, "width": "180px", "targets": 7 }
        ],
        "iDisplayLength": 10,
        "sAjaxSource": ASL_REMOTE.URL + "?action=asl_ajax_handler&asl-nounce=" + ASL_REMOTE.nounce + "&sl-action=get_categories",
        "columns": [
          { "data": "check" },
          { "data": "id" },
          { "data": "category_name" },
          { "data": "parent_name" },
          { "data": "ordr" },
          { "data": "icon" },
          { "data": "created_on" },
          { "data": "action" }
        ],
        'fnServerData': function(sSource, aoData, fnCallback) {

          $.get(sSource, aoData, function(json) {

            parent_categories = json.parent_categories;

            fnCallback(json);

          }, 'json');

        },
        "fnServerParams": function(aoData) {

          //  add lang
          if(lang_ctrl)
            aoData.push({"name": 'asl-lang',"value": lang_ctrl.value});

          //  Add the search
          $("#tbl_categories_wrapper thead .asl-grid-filter-row input").each(function(i) {

            if (this.value != "") {
              aoData.push({
                "name": 'filter[' + $(this).attr('data-id') + ']',
                "value": this.value
              });
            }
          });

          // Filter out the object with name "sColumns"
          aoData = aoData.map(function(item) {

            if (item.name === "sColumns") {
                item.value = "";
            }
            return item;
          });
        },
        "order": [
          [1, 'desc']
        ]
      });

      //prompt the category box
      $('#btn-asl-new-c').on('click', function() {
        $("#parent_id").html('<option value="0">None</option>');
        $.each(parent_categories, function(index, value) {
          $("#parent_id").append('<option value="' + value.id  + '">' + value.category_name  + '</option>');
        });
        $('#asl-add-modal').smodal('show');
      });

      //  Select all button
      $('.table .select-all').on('click', function(e) {

        $('.asl-p-cont .table input').attr('checked', 'checked');

      });

      //  Delete Selected Categories:: bulk
      $('#btn-asl-delete-all').on('click', function(e) {

        var $tmp_categories = $('.asl-p-cont .table input:checked');

        if ($tmp_categories.length == 0) {
          atoastr.error('No Category selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });


        aswal({
          title: ASL_REMOTE.LANG.delete_categories,
          text: ASL_REMOTE.LANG.warn_question + ' ' + ASL_REMOTE.LANG.delete_categories + '?',
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#dc3545",
          confirmButtonText: ASL_REMOTE.LANG.delete_it
        }).then(function() {

          ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_category", { item_ids: item_ids, multiple: true }, function(_response) {

            toastIt(_response);

            if (_response.success) {
              table.fnDraw();
              return;
            }

          }, 'json');
        });
      });


      //  To Add New Categories
      var url_to_upload = ASL_REMOTE.URL,
        $form           = $('#frm-addcategory');

      app_engine.uploader($form, url_to_upload + '?action=asl_ajax_handler&asl-nounce=' + ASL_REMOTE.nounce + '&sl-action=add_categories', function(e, data) {

        var data = data.result;

        toastIt(data);
            
        if (data.success) {

          //reset form
          $('#asl-add-modal').smodal('hide');
          $('#frm-addcategory').find('input:text, input:file').val('');
          $('#progress_bar').hide();
          //show table value
          table.fnDraw();
        } 
      });

      // Submit without an upload; the server assigns the default category icon.
      $('#btn-asl-add-categories').on('click', function(e) {

        if ($('#frm-addcategory ul li').length == 0) {
          e.preventDefault();
          var $button = $(this),
              $categoryForm = $('#frm-addcategory'),
              formData = {
                data: {
                  category_name: $categoryForm.find('[name="data[category_name]"]').val(),
                  parent_id: $categoryForm.find('[name="data[parent_id]"]').val() || 0,
                  ordr: $categoryForm.find('[name="data[ordr]"]').val() || 0
                }
              };

          if(lang_ctrl && lang_ctrl.value && formData)
            formData['asl-lang'] = lang_ctrl.value;

          $button.bootButton('loading');
          ServerCall(url_to_upload + '?action=asl_ajax_handler&asl-nounce=' + ASL_REMOTE.nounce + '&sl-action=add_categories', formData, function(data) {
            $button.bootButton('reset');
            toastIt(data);

            if (data.success) {
              $('#asl-add-modal').smodal('hide');
              $('#frm-addcategory').find('input:text, input:file').val('');
              $('#progress_bar').hide();
              table.fnDraw();
            }
          }, 'json');
        }
      });

      //show edit category model
      $('#tbl_categories tbody').on('click', '.edit_category', function(e) {

        $('#updatecategory_image').show();
        $('#updatecategory_editimage').hide();
        $('#asl-update-modal').smodal('show');
        $('#update_category_id_input').val($(this).attr("data-id"));

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=get_category_byid", { category_id: $(this).attr("data-id") }, function(_response) {

          if (_response.success) {

            $("#update_category_name").val(_response.item['category_name']);
            $("#update_parent_id").html('<option value="0">None</option>');
            $.each(parent_categories, function(index, value) {
              var selected = _response.item['parent_id'] == value.id ? 'selected' : '';
              if (value.id != _response.item['id']) {
                $("#update_parent_id").append('<option value="' + value.id  + '" '+ selected +'>' + value.category_name  + '</option>');
              }
            });

            $("#update_category_icon").attr("src", ASL_Instance.url + "svg/" + _response.item['icon']);
            $("#update_category_ordr").val(_response.item['ordr']);
          } else {

            atoastr.error(_response.error);
            return;
          }
        }, 'json');
      });

      //show edit category upload image
      $('#change_image').click(function() {

        $("#update_category_icon").attr("data-id", "")
        $('#updatecategory_image').hide();
        $('#updatecategory_editimage').show();
      });

      //  Update category without icon
      $('#btn-asl-update-categories').click(function() {

        if ($("#update_category_icon").attr("data-id") == "same") {

          ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=update_category", { data: { category_id: $("#update_category_id_input").val(), action: "same", category_name: $("#update_category_name").val(), "parent_id": $("#update_parent_id").val(), "ordr": $("#update_category_ordr").val()  } },
            function(_response) {

              toastIt(_response);

              if (_response.success) {
                $('#asl-update-modal').smodal('hide');
                table.fnDraw();
                return;
              }

            }, 'json');

        }

      });

      //  Update category with icon

      var url_to_upload = ASL_REMOTE.URL,
        $form = $('#frm-updatecategory');

      $form.append('<input type="hidden" name="data[action]" value="notsame" /> ');

      app_engine.uploader($form, url_to_upload + '?action=asl_ajax_handler&asl-nounce=' + ASL_REMOTE.nounce + '&sl-action=update_category', function(e, data) {

        var data = data.result;

        if (data.success) {

          atoastr.success(data.msg);
          $('#asl-update-modal').smodal('hide');
          $('#frm-updatecategory').find('input:text, input:file').val('');
          $('#progress_bar_').hide();
          table.fnDraw();
        }
        else
          atoastr.error(data.msg);
      });

      //show delete category model
      $('#tbl_categories tbody').on('click', '.delete_category', function(e) {

        var _category_id = $(this).attr("data-id");

        aswal({
          title: ASL_REMOTE.LANG.delete_category,
          text: ASL_REMOTE.LANG.warn_question + ' ' + ASL_REMOTE.LANG.delete_category + ' ' + _category_id + " ?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#dc3545",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(
          function() {

            ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_category", { category_id: _category_id }, function(_response) {

              toastIt(_response);

              if (_response.success) {
                table.fnDraw();
                return;
              }

            }, 'json');

          }
        );
      });



      $("#tbl_categories_wrapper thead .asl-grid-filter-row input").keyup(function(e) {

        if (e.keyCode == 13) {
          table.fnDraw();
        }
      });
    },
    /**
     * [manage_markers Manage Markers]
     * @return {[type]} [description]
     */
    manage_markers: function() {

      var table = null;

      //prompt the marker box
      $('#btn-asl-new-c').on('click', function() {
        $('#asl-add-modal').smodal('show');
      });


      var asInitVals = {};
      table = $('#tbl_markers').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bFilter": false,
        "bServerSide": true,
        //"scrollX": true,
        /*"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 1 ] }
        ],*/
        "bAutoWidth": true,
        "columnDefs": [
          { 'bSortable': false, "width": "140px", "targets": 0 },
          { "width": "180px", "targets": 1 },
          { "width": "300px", "targets": 2 },
          { "width": "220px", "targets": 3 },
          { "width": "180px", "targets": 4 },
          { 'bSortable': false, 'aTargets': [4] }
        ],
        "iDisplayLength": 10,
        "sAjaxSource": ASL_REMOTE.URL + "?action=asl_ajax_handler&asl-nounce=" + ASL_REMOTE.nounce + "&sl-action=get_markers",
        "columns": [
          { "data": "check" },
          { "data": "id" },
          { "data": "marker_name",
            render: function (data, type, full, meta) {
              return '<span class="sl-store-title">' + data + "</span>";
            }},
          { "data": "icon" },
          { "data": "action" }
        ],
        "fnServerParams": function(aoData) {

          $("#tbl_markers_wrapper thead .asl-grid-filter-row input").each(function(i) {

            if (this.value != "") {
              aoData.push({
                "name": 'filter[' + $(this).attr('data-id') + ']',
                "value": this.value
              });
            }
          });
        },
        "order": [
          [1, 'desc']
        ]
      });

      //TO ADD New Marker
      var url_to_upload = ASL_REMOTE.URL,
        $form = $('#frm-addmarker');

      app_engine.uploader($form, url_to_upload + '?action=asl_ajax_handler&asl-nounce=' + ASL_REMOTE.nounce + '&sl-action=add_markers', function(e, data) {

        var data = data.result;

        if (!data.success) {

          atoastr.error(data.msg);
        } else {

          atoastr.success(data.msg);
          //reset form
          $('#asl-add-modal').smodal('hide');
          $('#frm-addmarker').find('input:text, input:file').val('');
          $('#progress_bar').hide();
          //show table value
          table.fnDraw();
        }
      });


      //  Show edit marker model
      $('#tbl_markers tbody').on('click', '.edit_marker', function(e) {

        $('#message_update').empty().removeAttr('class');
        $('#updatemarker_image').show();
        $('#updatemarker_editimage').hide();
        $('#asl-update-modal').smodal('show');
        $('#update_marker_id_input').val($(this).attr("data-id"));

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=get_marker_byid", { marker_id: $(this).attr("data-id") }, function(_response) {

          if (_response.success) {

            $("#update_marker_name").val(_response.list[0]['marker_name']);
            $("#update_marker_icon").attr("src", ASL_Instance.url + "icon/" + _response.list[0]['icon']);
          } else {

            atoastr.error(_response.error);
            return;
          }
        }, 'json');
      });

      //show edit marker upload image
      $('#change_image').click(function() {

        $("#update_marker_icon").attr("data-id", "")
        $('#updatemarker_image').hide();
        $('#updatemarker_editimage').show();
      });

      //update marker without icon
      $('#btn-asl-update-markers').click(function() {

        if ($("#update_marker_icon").attr("data-id") == "same") {

          ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=update_marker", { data: { marker_id: $("#update_marker_id_input").val(), action: "same", marker_name: $("#update_marker_name").val() } },
            function(_response) {

              toastIt(_response);

              if (_response.success) {
                table.fnDraw();

                return;
              }

            }, 'json');

        }

      });

      //  Update marker with icon
      var url_to_upload = ASL_REMOTE.URL,
        $form = $('#frm-updatemarker');

      $form.append('<input type="hidden" name="data[action]" value="notsame" /> ');

      app_engine.uploader($form, url_to_upload + '?action=asl_ajax_handler&asl-nounce=' + ASL_REMOTE.nounce + '&sl-action=update_marker', function(e, data) {

        var data = data.result;

        if (data.success) {

          atoastr.success(data.msg);
          $('#asl-update-modal').smodal('hide');
          $('#frm-updatemarker').find('input:text, input:file').val('');
          $('#progress_bar_').hide();
          table.fnDraw();
        } else
          atoastr.error(data.msg);
      });

      //  Show delete marker model
      $('#tbl_markers tbody').on('click', '.delete_marker', function(e) {

        var _marker_id = $(this).attr("data-id");

        aswal({
          title: ASL_REMOTE.LANG.delete_marker,
          text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_marker + " " + _marker_id + "?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#dc3545",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(
          function() {

            ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_marker", { marker_id: _marker_id }, function(_response) {

              toastIt(_response);

              if (_response.success) {
                table.fnDraw();
                return;
              }

            }, 'json');

          }
        );
      });

      //////////////Delete Selected Categories////////////////

      //  Select all button
      $('.table .select-all').on('click', function(e) {

        $('.asl-p-cont .table input').attr('checked', 'checked');
      });

      //Bulk
      $('#btn-asl-delete-all').on('click', function(e) {

        var $tmp_markers = $('.asl-p-cont .table input:checked');

        if ($tmp_markers.length == 0) {
          atoastr.error('No Marker selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });


        aswal({
            title: ASL_REMOTE.LANG.delete_markers,
            text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_markers + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: ASL_REMOTE.LANG.delete_it
          })
          .then(function() {

            ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_marker", { item_ids: item_ids, multiple: true }, function(_response) {

              toastIt(_response);

              if (_response.success) {
              
                table.fnDraw();
                return;
              }

            }, 'json');
          }, aswal.noop);

      });

      $("#tbl_markers_wrapper thead .asl-grid-filter-row input").keyup(function(e) {

        if (e.keyCode == 13) {
          table.fnDraw();
        }
      });
    },
    /**
     * [_logo_media_uploader Media uploader for the logo]
     * @param  {[type]} _callback [when upload is completed]
     * @return {[type]}           [description]
     */
    _logo_media_uploader: function(_callback) {


      /// Upload the logos  
      $('.asl_upload_logo_btn').click(function(e){
        
        e.preventDefault();

        var multiple    = false,
          $class        = 'gallery',
          is_upload     = $(this).hasClass('asl_upload_logo_btn');

        if (is_upload) {
          multiple  = false;
          $class    = 'icon';
        }
        
        var button            = $(this),
            hiddenfield       = button.prev(),
            hiddenfieldvalue  = hiddenfield.val().split(","), /* the array of added image IDs */
            open_smodal       = button.closest('.smodal.show')[0],
            open_smodal_modal = open_smodal && window.bootstrap && bootstrap.Modal
              ? bootstrap.Modal.getInstance(open_smodal)
              : null,
            custom_uploader   = wp.media({
                                  title: ASL_REMOTE.LANG.select_logo || 'Insert images', /* popup title */
                                  library : {type : 'image'},
                                  button: {text: ASL_REMOTE.LANG.use_image || 'Use Image'}, /* "Insert" button text */
                                  multiple: multiple
                                })
            .on('open', function() {
              if (open_smodal_modal && open_smodal_modal._focustrap) {
                open_smodal_modal._focustrap.deactivate();
              }
            })
            .on('close', function() {
              if (open_smodal_modal && open_smodal_modal._focustrap) {
                open_smodal_modal._focustrap.activate();
              }
            })
            .on('select', function() {

              var attachments = custom_uploader.state().get('selection').map(function(a) {
                a.toJSON();

                return a;
              }),
              thesamepicture = false,
              i;

              /* loop through all the images */
              for (i = 0; i < attachments.length; ++i) {
                
                if (is_upload) {
                  $('ul.asl_logo_mtb').html('<li data-id="' + attachments[i].id + '"><img src="' + attachments[i].attributes.url + '"/></li>');
                }
                else{
                  /* add HTML element with an image */
                  $('ul.asl_logo_mtb').append('<li data-id="' + attachments[i].id + '"><img src="' + attachments[i].attributes.url + '"/></li>');
                }
                
                if (is_upload) {
                  /* add an image ID to the array of all images */
                  hiddenfieldvalue = attachments[i].id ;
                }
                else{
                  /* add an image ID to the array of all images */
                  hiddenfieldvalue.push( attachments[i].id );
                }
              }

              if (!is_upload) {
                /* refresh sortable */
                $( "ul.asl_"+$class+"_mtb" ).sortable( "refresh" );
              }

              if (is_upload) {
                /* add an image ID to the array of all images */
                hiddenfield.val( hiddenfieldvalue );
                button.removeAttr('aria-invalid');
                button.closest('form').find('#message_upload').first().addClass('hide').hide().text('');
              }
              else{
                /* add the IDs to the hidden field value */
                hiddenfield.val( hiddenfieldvalue.join() );
              }
          
              /* you can print a message for users if you want to let you know about the same images */
              if( thesamepicture == true )
                alert('The same images are not allowed.');
            
            }).open();
      });

      // Upload New Logo 
      $('.new_upload_logo').on('click',function(){

        var $button   = $(this),
            $form     = $button.closest('form'),
            $message  = $form.find('#message_upload').first(),
            $name     = $form.find('#txt_logo-name').first(),
            img_id    = $form.find('#add_img').first().val(),
            logo_name = $.trim($name.val()),
            error     = '';

        $name.removeClass('is-invalid').removeAttr('aria-invalid');
        $form.find('.asl_upload_logo_btn').removeAttr('aria-invalid');
        $message.addClass('hide').hide().text('');

        if (!logo_name) {
          error = $form.data('name-required') || 'Please enter a logo name.';
          $name.addClass('is-invalid').attr('aria-invalid', 'true').trigger('focus');
        }
        else if (!img_id) {
          error = $form.data('image-required') || 'Please select a logo image before uploading.';
          $form.find('.asl_upload_logo_btn').attr('aria-invalid', 'true').trigger('focus');
        }

        if (error) {
          $message.removeClass('hide').show().text(error);
          return;
        }

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=upload_logo", { data:{img_id:img_id,logo_name:logo_name} }, function(data) {
                  
          toastIt(data);

          //  run the call back
          if(_callback) {
            _callback(data);
          }

        }, 'json');
      });
    },
    /**
     * [manage_logos description]
     * @return {[type]} [description]
     */
    manage_logos: function() {


      /// Remove the logo images
      $('body').on('click', '.asl_remove_logo', function(){
        
        var id          = $(this).parent().attr('data-id'),
            gallery     = $(this).parent().parent(),
            hiddenfield = gallery.parent().next(),
            hiddenfieldvalue = hiddenfield.val().split(","),
            i = hiddenfieldvalue.indexOf(id);

        $(this).parent().remove();

        /* remove certain array element */
        if(i != -1) {
          hiddenfieldvalue.splice(i, 1);
        }

        /* add the IDs to the hidden field value */
        hiddenfield.val( hiddenfieldvalue.join() );

        return false;
      });

      var table = null;

      //prompt the logo box
      $('#btn-asl-new-c').on('click', function() {
        $('ul.asl_logo_mtb').html('');
        $('#asl-add-modal').smodal('show');
      });


      var asInitVals = {};
      table = $('#tbl_logos').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bFilter": false,
        "bServerSide": true,
        //"scrollX": true,
        /*"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 1 ] }
        ],*/
        "bAutoWidth": true,
        "columnDefs": [
          { 'bSortable': false, "width": "140px", "targets": 0 },
          { "width": "180px", "targets": 1 },
          { "width": "300px", "targets": 2 },
          { "width": "220px", "targets": 3 },
          { "width": "180px", "targets": 4 },
          { 'bSortable': false, 'aTargets': [4] }
        ],
        "iDisplayLength": 10,
        "sAjaxSource": ASL_REMOTE.URL + "?action=asl_ajax_handler&asl-nounce=" + ASL_REMOTE.nounce + "&sl-action=get_logos",
        "columns": [
          { "data": "check" },
          { "data": "id" },
          { "data": "name" },
          { "data": "path" },
          { "data": "action" }
        ],
        "fnServerParams": function(aoData) {

          $("#tbl_logos_wrapper thead .asl-grid-filter-row input").each(function(i) {

            if (this.value != "") {
              aoData.push({
                "name": 'filter[' + $(this).attr('data-id') + ']',
                "value": this.value
              });
            }
          });
        },
        "order": [
          [1, 'desc']
        ]
      });

      //  Add new logo uploader
      this._logo_media_uploader(function(_data){

        //  Upload response
        if (_data.success) {

          //  Reset form
          $('#asl-add-modal').smodal('hide');
          $('#frm-addlogo').find('input:text, input:file').val('');
        }

        //  refresh the table on success
        table.fnDraw();
      });
      

      // Show edit logo model
      $('#tbl_logos tbody').on('click', '.edit_logo', function(e) {

        $('ul.asl_logo_mtb').html('');
        $('#updatelogo_image').show();
        $('#updatelogo_editimage').hide();
        $('#asl-update-modal').smodal('show');
        $('#update_logo_id_input').val($(this).attr("data-id"));

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=get_logo_byid", { logo_id: $(this).attr("data-id") }, function(_response) {

          if (_response.success) {

            $("#update_logo_name").val(_response.list[0]['name']);
            $("#update_logo_icon").attr("src", ASL_Instance.url + "Logo/" + _response.list[0]['path']);
          } else {

            atoastr.error(_response.error);
            return;
          }
        }, 'json');
      });

      //  Show edit logo upload image
      $('#change_image').click(function() {

        $("#update_logo_icon").attr("data-id", "")
        $('#updatelogo_image').hide();
        $('#updatelogo_editimage').show();
      });


      //  Update logo without icon
      $('#btn-asl-update-logos').click(function() {
        
        var img_id      = $('#replace_logo').val(),
            logo_id     = $("#update_logo_id_input").val(),
            logo_name   = $('#update_logo_name').val();
        

        if (logo_name != '') {

          ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=update_logo", { data: { logo_id:logo_id , action:((img_id)? "notsame": ''), logo_name: logo_name , img_id:img_id } },
            function(_response) {

              if (_response.success) {

                atoastr.success(_response.msg);

                table.fnDraw();

                $('#asl-update-modal').smodal('hide');
                
                return;
              } 
              else {
                atoastr.error(_response.msg);
                return;
              }
            }, 'json');
        }
      });

      //show delete logo model
      $('#tbl_logos tbody').on('click', '.delete_logo', function(e) {

        var _logo_id = $(this).attr("data-id");

        aswal({
          title: ASL_REMOTE.LANG.delete_logo,
          text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_logo + " " + _logo_id + "?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(
          function() {

            ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_logo", { logo_id: _logo_id }, function(_response) {

              if (_response.success) {
                atoastr.success(_response.msg);
                table.fnDraw();
                return;
              } else {
                atoastr.success((_response.error || ASL_REMOTE.LANG.error_try_again));
                return;
              }

            }, 'json');

          }
        );
      });

      //////////////Delete Selected Categories////////////////

      //Select all button
      $('.table .select-all').on('click', function(e) {

        $('.asl-p-cont .table input').attr('checked', 'checked');
      });

      //Bulk
      $('#btn-asl-delete-all').on('click', function(e) {

        var $tmp_logos = $('.asl-p-cont .table input:checked');

        if ($tmp_logos.length == 0) {
          atoastr.error('No Logo selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });

        aswal({
          title: ASL_REMOTE.LANG.delete_logos,
          text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_logos + "?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#dc3545",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(function() {

          ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_logo", { item_ids: item_ids, multiple: true }, function(_response) {

            toastIt(_response);

            if (_response.success) {
              table.fnDraw();
              return;
            }
          }, 'json');
        });

      });


      $("#tbl_logos_wrapper thead .asl-grid-filter-row input").keyup(function(e) {

        if (e.keyCode == 13) {
          table.fnDraw();
        }
      });
    },

    /**
     * [manage_cards description]
     * @return {[type]} [description]
     */
    manage_cards: function() {

        var parent_row,
        updated_shortcode_str;

        function show_hide_table() {
          $('#tbl_shortcode tbody tr').each(function(i) {
            if ($(this).text().trim() == '') {
              $(this).remove();
            }
          });

          let shortcode_row = $('#tbl_shortcode tbody tr');

          if (shortcode_row.length) {
            $('#tbl_shortcode').removeClass('d-none');
            $('.no-shortcode').addClass('d-none');
          } else {
            $('#tbl_shortcode').addClass('d-none');
            $('.no-shortcode').removeClass('d-none');
          }
        }

        function update_shortcode(data, parent_row = false) {
            ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=cards_shortcode_presets', data, function(_response) {
                toastIt(_response);
                
                if (_response.success) {

                    switch (data.db_action) {
                        case 'add':
                            $('#asl-add-card').smodal('hide');
                            var table_row = asl_configs.html.table_row.replace('the_shortcode', _response.data.replaceAll('\\', ''));
                            $('#tbl_shortcode tbody').append(table_row);
                            break;

                        case 'delete':
                            parent_row.remove();
                            break;

                        case 'edit':
                            parent_row.find('td:first-child span').text(_response.data.replaceAll('\\', ''));
                            $('#asl-edit-card').smodal('hide');
                            break;
                    }

                    show_hide_table();
                }
            }, 'json');
        }

        // Switch template visibility according to template dropdown field
        function show_card_layout(modal) {
            const target_card = modal.find('.choose-card').val();
            modal.find('.cards .card-preview').addClass('hide');
            modal.find('.cards .' + target_card).removeClass('hide');
        }


        // Scan through active modal and return toggled-off 
        function get_hidden_fields(modal) {
            var hidden_fields = '';
            const hidden_fields_obj = modal.find('.field-toggles .switch input:checkbox:not(:checked)');

            hidden_fields_obj.each(function() {
                hidden_fields += $(this).data('target') + ',';
            });
            if (hidden_fields.length) {
                hidden_fields = ' hide_fields="' + hidden_fields.replace(/,\s*$/, "") + '"';
            }
            return hidden_fields;
        }


        // Scan through active modal and find filter fields
        function get_filters(modal) {
            var filter_fields = '';
            const filter_fields_obj = modal.find('.asl_cardFilterBox .asl_cardFilterCtnBox');
            filter_fields_obj.each(function() {
                if ($(this).find('select').val().length && $(this).find('input').val().length) {
                filter_fields += $(this).find('select').val() + '="' + $(this).find('input').val() + '" ';
                }
            });
            
            if (filter_fields.length) {
                modal.find('.asl_noFilters').addClass('hide');
                filter_fields = ' ' + filter_fields.trim();
            }

            return filter_fields;
        }


        // Scan through active modal and find the selected template from the dropdown
        function get_card(modal) {
            const card_card = modal.find('.choose-card').val();
            return ' card="' + card_card + '"';
        }


        function get_use_slider(modal) {
          const user_slider_obj = modal.find('.use-slider input[type=checkbox]:checked');
          if (user_slider_obj.length) {
            return ' slider="1"';
          }

          return '';
        }


        function get_heading_tag(modal) {
          const heading_tag = modal.find('.heading-tag').val();
          return ' heading_tag="' + heading_tag + '"';
        }

        function fields_to_shortcode(modal) {
            const card_layout     = get_card(modal);
            const hidden_fields   = get_hidden_fields(modal);
            const filters         = get_filters(modal);
            const use_slider      = get_use_slider(modal);
            const heading_tag     = get_heading_tag(modal);
            updated_shortcode_str = '[ASL_CARDS' + hidden_fields + filters + card_layout + use_slider + heading_tag + ']';
        }


        function shortcode_to_fields(existing_shortcode) {
            var filter_fields;
            var empty_field = asl_configs.html.filter_field;
            var field = '';
            var modal = $('#asl-edit-card');

            // Add empty filter field
            modal.find('.filter-fields-container').html(empty_field);

            // Show "No Filters are Applied" Text
            modal.find('.asl_noFilters').removeClass('hide');

            // Reset All Filter Fields
            modal.find('.choose-card option:selected').attr('selected', false);

            // Reset All Toggles
            modal.find('.fields-toggle input[data-target]').attr('checked', true);
            modal.find('.use-slider input[type=checkbox]').attr('checked', false);
            modal.find('.asl_previewCard *[data-field]').removeClass('hide');


            var attributes = existing_shortcode.replaceAll(']', '');
            attributes = attributes.replaceAll('[ASL_CARDS ', '');
            attributes = attributes.split('" ');

            var attribute;
            for (attribute of attributes) {

                attribute = attribute.split('="');

                attribute[1] = attribute[1].replaceAll('"', ''); // Attribute Value

                if (attribute[0] == 'hide_fields') {

                    var hide_fields_attr = attribute[1].split(',');
                    modal.find('.fields-toggle > div').each(function() {
                        var input_swt = $(this).find('.switch input');
                        var target = input_swt.attr('data-target');
                        if (hide_fields_attr.includes(target)) {
                            input_swt.attr('checked', false);
                            modal.find('.asl_previewCard *[data-field="' + target + '"]').addClass('hide');
                        }
                    });

                } else if (attribute[0] == 'heading_tag') {

                    modal.find('.heading-tag option[value=' + attribute[1] + ']').attr('selected', true);

                } else if (attribute[0] == 'card') {

                    modal.find('.choose-card option[value=' + attribute[1] + ']').attr('selected', true);

                } else if (attribute[0] == 'slider' && attribute[1]) {

                  modal.find('.use-slider input[type=checkbox]').attr('checked', true);

                } else {
                    field = empty_field;
                    field = field.replace('<option value="' + attribute[0] + '"', '<option value="' + attribute[0] + '" selected');
                    field = field.replace( '<input ', '<input value="' + attribute[1] + '" ');

                    filter_fields += field;

                    if (!modal.find('.asl_noFilters').hasClass('hide')) {
                    modal.find('.asl_noFilters').addClass('hide');
                    }
                }
                
                if (filter_fields !== undefined) {
                    modal.find('.filter-fields-container').html(filter_fields);
                }
            }

            show_card_layout(modal);
        }


        // Choose Template Event
        $('.choose-card').on('change', function() {
            const modal = $(this).parents('.asl_manageCardModal');
            show_card_layout(modal);
        });


        // Filter Dropdown change Event
        $('.filter-field').on('change', function() {
            const field_container = $(this).parents('.filter-fields-container');
            var selected_filter_fields = [];
            field_container.find('.custom-select').each(function() {
                selected_filter_fields.push($(this).val());
            });
        });


        // Toggle Field Switches Event
        $('.fields-toggle .switch input').on('change', function() {
            const target_val = $(this).attr('data-target');
            var target = $(this).parents('.smodal-body').find('.asl_previewCard *[data-field="' + target_val + '"]');

            if ($(this).prop('checked')) {
                target.removeClass('hide');
            } else {
                target.addClass('hide');
            }
        });


        // Add Filter Field on "New Field" Button click
        $('.smodal-body').on('click', '.btn-asl-add-field', function() {
            var field = asl_configs.html.filter_field;
            $(this).parents('.smodal-body').find('.filter-fields-container').append(field);
        });


        // Remove Filter Field
        $('.filter-fields-container').on('click', '.asl_cardFilterRemoveButton', function() {
            $(this).parents('.asl_cardFilterCtnBox').remove();
        });


        // Copy Shortcode
        $('#tbl_shortcode').on('click', '.copy-shortcode', function() {
            var bubble = $(this).find('.alert');
            var shortcode_text = $(this).siblings('span').text();

            navigator.clipboard
            .writeText(shortcode_text)
            .then(() => {
                bubble.addClass('show alert-success');
                bubble.text('Copied!');
              })
              .catch(() => {
                bubble.addClass('show alert-danger');
                bubble.text('Couldn\'t Copy!');
            });

            setTimeout(function () {
              bubble.removeClass('show alert-danger alert-success');
            }, 2000);
        });

      
        // Edit shortcode Modal
        $('#tbl_shortcode').on('click', '.btn-asl-edit', function(el) {
            parent_row = $(this).closest('tr');
            var existing_shortcode = $(this).parents('tr').find('td:first-child span').text();
            shortcode_to_fields(existing_shortcode);
        });


        // Add shortcode action
        $('#asl-add-card .btn-asl-save').on('click', function() {
            var modal = $(this).parents('.asl_manageCardModal');
            fields_to_shortcode(modal);
            var data = {shortcode: updated_shortcode_str, db_action: 'add'};
            update_shortcode(data);


        });


        // Edit shortcode action
        $('#asl-edit-card .btn-asl-save').on('click', function() {
            // parent_row = $(this).parents('tr');

            var existing_shortcode = parent_row.find('td:first-child span').text();
            var modal  = $(this).parents('.asl_manageCardModal');
            fields_to_shortcode(modal);
            var data   = {shortcode: existing_shortcode, updated_shortcode: updated_shortcode_str, db_action: 'edit'};
            update_shortcode(data, parent_row);


        });


        // Delete shortcode action
        var shortcode_str;
        $('#tbl_shortcode').on('click', '.btn-asl-delete', function() {
            var parent_row_to_delete = $(this).closest('tr');
            shortcode_str = parent_row_to_delete.find('td:first-child span').text();

            aswal({
                title: 'Do you really want to delete shortcode?',
                html: '<p>' + shortcode_str + '</p>',
                type: "warning",
                showCancelButton: true,
                allowOutsideClick: true,
                allowEscapeKey: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            })
            
            .then(
                function() {
                  var data = {shortcode: shortcode_str, db_action: 'delete'};
                  update_shortcode(data, parent_row_to_delete);
                }
            );


        });


    },

    /**
     * [manage_stores description]
     * @return {[type]} [description]
     */
    manage_stores: function() {

      var table          = null,
        row_duplicate_id = null,
        pending_stores   = false;


      var urlSearchParams = new URLSearchParams(window.location.search);
      var params          = Object.fromEntries(urlSearchParams.entries());
      var getSelectedStoreIds = function() {
        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {
          var _id = $(this).attr('data-id');
          if (_id) {
            item_ids.push(_id);
          }
        });
        return item_ids;
      };
      var $bulk_offcanvas = $('#sl-bulk-edit');
      var bulk_time_init  = false;
      var open_time_tmpl  = '9:30 AM';
      var close_time_tmpl = '6:30 PM';
      var bulk_fields_init = false;


      /*DUPLICATE STORES*/
      var duplicate_store = function(_id) {

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=duplicate_store", { store_id: _id }, function(_response) {

          toastIt(_response);

          if (_response.success) {
            
            table.fnDraw();
            return;
          }

        }, 'json');
      };

      //Prompt the DUPLICATE alert
      $('#tbl_stores').on('click', '.row-cpy', function() {

        row_duplicate_id = $(this).data('id');

        aswal({
            title: ASL_REMOTE.LANG.duplicate_stores,
            text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.duplicate_stores + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: ASL_REMOTE.LANG.duplicate_it,
          })
          .then(
            function() {

              duplicate_store(row_duplicate_id);
            }
          );
        });


      /*Delete Stores*/
      var _delete_all_stores = function() {

        var $this = $('#asl-delete-stores');
        $this.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=delete_all_stores', {}, function(_response) {

          $this.bootButton('reset');
          table.fnDraw();

          toastIt(_response);

        }, 'json');
      };

      /*Delete All stores*/
      $('#asl-delete-stores').on('click', function(e) {

        aswal({
          title: ASL_REMOTE.LANG.delete_all_stores,
          text: ASL_REMOTE.LANG.warn_question + ' ' + ASL_REMOTE.LANG.delete_all_stores + "?",
          type: "error",
          showCancelButton: true,
          confirmButtonColor: "#dc3545",
          confirmButtonText: ASL_REMOTE.LANG.delete_all
        }).then(
          function() {

            _delete_all_stores();
          }
        );
      });

      var columnDefs = [
          {"width": "140px", "targets": 0},
          {"width": "190px", "targets": 1 },
          {"width": "170px", "targets": 2},
          {"width": "140px", "targets": 3 },
          {"width": "260px", "targets": 4 },
          {"width": "170px", "targets": 5 },
          {"width": "170px", "targets": 6 },
          {"width": "280px", "targets": 7 },
          {"width": "190px", "targets": 8 },
          {"width": "190px", "targets": 9 },
          {"width": "190px", "targets": 10 },
          {"width": "190px", "targets": 11 },
          {"width": "240px", "targets": 12 },
          {"width": "240px", "targets": 13 },
          {"width": "190px", "targets": 14 },
          {"width": "170px", "targets": 15 },
          {"width": "260px", "targets": 16 },
          {"width": "180px", "targets": 17 },
          {"width": "180px", "targets": 18 },
          {"width": "220px", "targets": 19 },
        ];

        var col_number = columnDefs.length;
        for(var c in dt_custom_columns) {
          columnDefs.push({"width": "220px", "targets": col_number, "orderable": false });
          col_number++;
        }
        columnDefs.push({ 'bSortable': false, 'aTargets': [0, 1, 2, 14] });

      // Hide 'Schedule Store' Column when store schedule option is disable 
      if (asl_configs.store_schedule == 0) {
            
            columnDefs[2]['visible'] = false;
            
        }
        
      //  Loop over to hide
      for(var ch in asl_hidden_columns) {

        if (!asl_hidden_columns.hasOwnProperty(ch)) continue;
        
        if(asl_hidden_columns[ch] && columnDefs[asl_hidden_columns[ch]]){
          columnDefs[asl_hidden_columns[ch]]['visible'] = false;
        }
      }


      /**
       * [validate_coordinate Validate the coordinates]
       * @param  {[type]} $lat [description]
       * @param  {[type]} $lng [description]
       * @return {[type]}      [description]
       */
      function validate_coordinate($lat, $lng) {

        if($lat && $lng && !isNaN($lat) && !isNaN($lng)) {

          if ($lat < -90 || $lat > 90) {return false;}
          if ($lng < -180 || $lng > 180) {return false;}
          return true;
        }

        return false;
      };

      var invalid_rows  = 0;


      var dt_columns = [
        { "data": "check" },
        { "data": "action" },
        { "data": "scheduled"},
        { "data": "id",
            render: function (data, type, full, meta) {
            return '<span class="sl-store-id">' + data + "</span>";
          } },
        { "data": "title", 
          render: function (data, type, full, meta) {
            return '<a  class="sl-store-title"  href="'+ASL_Instance.manage_stores_url + full.id +'">' + data + "</a>";
          }},
        { "data": "lat"},
        { "data": "lng" },
        { "data": "street" },
        { "data": "state" },
        { "data": "city" },
        { "data": "country" },
        { "data": "phone" },
        { "data": "email" },
        { "data": "website" },
        { "data": "postal_code" },
        { "data": "is_disabled",  "render": function(data) {

          if (data == "1") {
            return '<span class="sl-status-inactive">'+ASL_REMOTE.LANG.disabled+'</span>';
          } else {
            return '<span class="sl-status-active">'+ASL_REMOTE.LANG.enabled+'</span>';
          }
        } },
        { "data": "categories" },
        { "data": "marker_id" },
        { "data": "logo_id" },
        { "data": "created_on" }
      ];

      for(var c in dt_custom_columns) {
        dt_columns.push(dt_custom_columns[c]);
      }

      var asInitVals = {};

      table = $('#tbl_stores').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bFilter": false,
        "bServerSide": true,
        "scrollX": true,
        "bAutoWidth": false,
        "columnDefs": columnDefs,
        "iDisplayLength": 10,
        "sAjaxSource": ASL_REMOTE.URL + "?action=asl_ajax_handler&asl-nounce=" + ASL_REMOTE.nounce + "&sl-action=get_store_list",
        "columns": dt_columns,
        createdRow: function( _row, _data, _dataIndex ) {

          // Change disable's store row color
          if( _data['is_disabled'] ==  '1'){
              
            $(_row).addClass('disabled_color');

           }

          // Change schedule's store row color
        // Change schedule's store row color
          if(_data['is_scheduled'] == '1') { 

              $(_row).addClass('scheduled_color');

          }
         
          if(!validate_coordinate(_data.lat, _data.lng)) {
            
            $(_row).addClass('sl-error-row');

            invalid_rows++;
          }
        },
        drawCallback: function(e) {
          
          if(invalid_rows) {

            toastIt({error: invalid_rows + ' invalid coordinates in loaded stores'});
          }

          invalid_rows = 0;
        },
        "fnServerParams": function(aoData) {

          //  add lang
          if(lang_ctrl)
            aoData.push({"name": 'asl-lang',"value": lang_ctrl.value});

          //  When pending stores is enabled
          if(pending_stores) {

            aoData.push({
              "name": 'filter[pending]',
              "value": '1'
            });
          }

          //  When categories filter is there
          if(params.categories) {

            aoData.push({
              "name": 'categories',
              "value": params.categories
            });
          }


          //  Get the rest of the values
          $("#tbl_stores_wrapper .dataTables_scrollHead thead input").each(function(i) {

            if (this.value != "") {
              aoData.push({
                "name": 'filter[' + $(this).attr('data-id') + ']',
                "value": this.value

              });
            }
          });



          // Schedule Store
          $("#tbl_stores_wrapper .dataTables_scrollHead thead select").each(function(i) {
              
              if (this.value != "") {

                  aoData.push({
                  "name": 'filter[' + $(this).attr('data-id') + ']',
                  "value": this.value

                });
              }
          });

          
          
          // Filter out the object with name "sColumns"
          aoData = aoData.map(function(item) {

            if (item.name === "sColumns") {
                item.value = "";
            }
            return item;
          });

        },
        "order": [[2, 'desc']]
      });

      //  Show the pending stores
      $('#btn-pending-stores').on('click', function(e) {

        var $pending_btn = $(this);

        //  Change State
        pending_stores   = !pending_stores;

        if(pending_stores) {
  
          $pending_btn.find('span').html($pending_btn[0].dataset.pending);

        }
        else {
          
          $pending_btn.find('span').html($pending_btn[0].dataset.all);
        }

        //  Recall the datatable
        table.fnDraw();
      });

      

      // Select all button
      $('.table .select-all').on('click', function(e) {

        $('.asl-p-cont .table input').attr('checked', 'checked');
      });

      // Bulk edit store fields
      var toggleBulkEditFields = function() {
        var has_description = $('#asl-bulk-edit-apply-description').is(':checked');
        var has_open_hours  = $('#asl-bulk-edit-apply-open-hours').is(':checked');
        var has_marker      = $('#asl-bulk-edit-apply-marker').is(':checked');
        var has_logo        = $('#asl-bulk-edit-apply-logo').is(':checked');
        var has_categories  = $('#asl-bulk-edit-apply-categories').is(':checked');

        $('#asl-bulk-edit-description').prop('disabled', !has_description);
        $bulk_offcanvas.find('.asl-time-details input').prop('disabled', !has_open_hours);
        $bulk_offcanvas.find('.asl-time-details .asl-bulk-open-day').prop('disabled', !has_open_hours);
        $bulk_offcanvas.find('.asl-bulk-field-description').toggleClass('is-disabled', !has_description);
        $bulk_offcanvas.find('.asl-bulk-field-open-hours').toggleClass('is-disabled', !has_open_hours);
        $bulk_offcanvas.find('.asl-bulk-field-marker').toggleClass('is-disabled', !has_marker);
        $bulk_offcanvas.find('.asl-bulk-field-logo').toggleClass('is-disabled', !has_logo);
        $bulk_offcanvas.find('.asl-bulk-field-categories').toggleClass('is-disabled', !has_categories);

        if ($('#ddl-asl-bulk-markers').length) {
          $('#ddl-asl-bulk-markers').prop('disabled', !has_marker);
        }

        if ($('#ddl-asl-bulk-categories').length) {
          $('#ddl-asl-bulk-categories').prop('disabled', !has_categories).trigger('chosen:updated');
        }

        $bulk_offcanvas.find('.asl-bulk-custom-toggle').each(function() {
          var $toggle = $(this);
          var field = $toggle.data('field');
          var enabled = $toggle.is(':checked');
          var $wrap = $bulk_offcanvas.find('.asl-bulk-field-custom[data-field="' + field + '"]');
          $wrap.toggleClass('is-disabled', !enabled);
          $wrap.find('input, select, textarea').prop('disabled', !enabled);
        });
      };

      $('#asl-bulk-edit-apply-description, #asl-bulk-edit-apply-open-hours, #asl-bulk-edit-apply-marker, #asl-bulk-edit-apply-logo, #asl-bulk-edit-apply-categories, .asl-bulk-custom-toggle').on('change', toggleBulkEditFields);
      toggleBulkEditFields();

      function bulkTimeChangeEvent(e) {
        if($(e.currentTarget).hasClass('asl-start-time')) {
          open_time_tmpl =  e.time.value;
        }
        else
          close_time_tmpl   =  e.time.value; 
      };

      function initBulkOpenHours() {
        if (bulk_time_init) return;
        bulk_time_init = true;

        $bulk_offcanvas.on('click', '.add-k-add', function(e) {
          if (!$('#asl-bulk-edit-apply-open-hours').is(':checked')) {
            return;
          }

          var $new_slot = $('<div class="form-group">\
                      <div class="input-group bootstrap-asltimepicker">\
                            <input type="text" class="form-control asltimepicker asl-start-time validate[required,funcCall[ASLmatchTime]]" placeholder="' + ASL_REMOTE.LANG.start_time + '"  value="'+open_time_tmpl+'">\
                            <span class="input-group-append add-on"><span class="input-group-text"><svg width="20" height="20"><use xlink:href="#i-clock"></use></svg></span></span>\
                          </div>\
                          <div class="input-append input-group bootstrap-asltimepicker">\
                            <input type="text" class="form-control asltimepicker asl-end-time validate[required]" placeholder="' + ASL_REMOTE.LANG.end_time + '" value="'+close_time_tmpl+'">\
                            <span class="input-group-append add-on"><span class="input-group-text"><svg width="20" height="20"><use xlink:href="#i-clock"></use></svg></span></span>\
                          </div>\
                          <span class="add-k-delete glyp-trash text-danger">\
                            <svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>\
                          </span>\
                      </div>');

          var $cur_slot = $(this).parent().prev().find('.asl-all-day-times .asl-closed-lbl');
          $cur_slot.before($new_slot);

          $new_slot.find('input.asltimepicker').removeAttr('id').attr('class', 'form-control asltimepicker validate[required]').asltimepicker({
            showMeridian: (asl_configs && asl_configs.time_format == '1') ? false : true,
            appendWidgetTo: '.asl-p-cont'
          })
          .on('changeTime.asltimepicker', bulkTimeChangeEvent);
        });

        $bulk_offcanvas.on('click', '.add-k-delete', function(e) {
          $(this).parent().remove();
        });

        $bulk_offcanvas.find('.asl-time-details .asltimepicker').asltimepicker({
          showMeridian: (asl_configs && asl_configs.time_format == '1') ? false : true,
          appendWidgetTo: '.asl-p-cont',
        })
        .on('changeTime.asltimepicker', bulkTimeChangeEvent);

        $('#asl-bulk-time-cp').on('click', function(e) {
          if (!$('#asl-bulk-edit-apply-open-hours').is(':checked')) {
            return;
          }

          var $monday    = $bulk_offcanvas.find('.asl-time-details .asl-all-day-times').eq(0),
              $rest_days = $bulk_offcanvas.find('.asl-time-details .asl-all-day-times:not(:first)');

          $rest_days.each(function(e) {
            var day_index = parseInt(e) + 1;
            $(this).html($monday.children().clone());
            $(this).find('.a-swith').find('label').attr('for', 'bulk-cmn-toggle-' + day_index);
            $(this).find('.a-swith').find('input').attr('id', 'bulk-cmn-toggle-' + day_index);
          });
          $bulk_offcanvas.find('.asl-time-details .asl-bulk-open-day').prop('checked', true);
        
          $bulk_offcanvas.find('.asl-time-details .asltimepicker').asltimepicker({
            showMeridian: (asl_configs && asl_configs.time_format == '1') ? false : true,
            appendWidgetTo: '.asl-p-cont',
          })
          .on('changeTime.asltimepicker', bulkTimeChangeEvent);
        });
      }

      function getBulkOpenHours() {
        var open_hours = {};

        $bulk_offcanvas.find('.asl-time-details .asl-all-day-times').each(function(e) {
          var $day = $(this),
            day_index = String($day.data('day'));
          var $day_toggle = $bulk_offcanvas.find('.asl-time-details .asl-bulk-open-day[data-day="' + day_index + '"]');
          if (!$day_toggle.length || !$day_toggle.is(':checked')) {
            return;
          }

          var day_label = $.trim($day.closest('tr').find('.asl-day-label').val() || '');
          open_hours[day_index + '_label'] = day_label;

          open_hours[day_index] = null;

          if ($day.find('.form-group').length > 0) {
            open_hours[day_index] = [];
          } else {
            open_hours[day_index] = ($day.find('.asl-closed-lbl input')[0].checked) ? '1' : '0';
          }

          $day.find('.form-group').each(function() {
            var $hours = $(this).find('input');
            open_hours[day_index].push($hours.eq(0).val() + ' - ' + $hours.eq(1).val());
          });
        });

        return JSON.stringify(open_hours);
      }

      function initBulkExtraFields() {
        if (bulk_fields_init) return;
        bulk_fields_init = true;

        if ($('#ddl-asl-bulk-markers').length) {
          $('#ddl-asl-bulk-markers').ddslick({
            imagePosition: "right",
            selectText: ASL_REMOTE.LANG.select_marker,
            truncateDescription: true
          });
        }

        if ($('#ddl-asl-bulk-logos').length && typeof asl_bulk_logos !== 'undefined') {
          var bulk_logos = asl_bulk_logos.slice(0);
          for (var i = 0; i < bulk_logos.length; i++) {
            if (bulk_logos[i].imageSrc) {
              bulk_logos[i].imageSrc = ASL_Instance.url + 'Logo/' + bulk_logos[i].imageSrc;
            }
          }
          bulk_logos.unshift({
            value: 0,
            text: ASL_REMOTE.LANG.no_logo,
            selected: true
          });

          $('#ddl-asl-bulk-logos').ddslick({
            data: bulk_logos,
            imagePosition: "right",
            selectText: ASL_REMOTE.LANG.select_logo,
            truncateDescription: true
          });
        }

        if ($('#ddl-asl-bulk-categories').length) {
          $('#ddl-asl-bulk-categories').chosen({
            width: "100%",
            placeholder_text_multiple: ASL_REMOTE.LANG.select_category,
            no_results_text: ASL_REMOTE.LANG.no_category
          });
        }

        function updateBulkGalleryPreview($input) {
          var url = $.trim($input.val());
          var $control = $input.closest(".asl-gallery-field-control");
          var $preview = $control.find(".asl-gallery-preview");
          var $img = $preview.find("img");

          if (url) {
            $img.attr("src", url);
            $preview.removeClass("is-empty");
          } else {
            $img.attr("src", "");
            $preview.addClass("is-empty");
          }
        }

        $bulk_offcanvas.find(".asl-gallery-field").each(function() {
          updateBulkGalleryPreview($(this));
        });

        $bulk_offcanvas.on("input change", ".asl-gallery-field", function() {
          updateBulkGalleryPreview($(this));
        });

        $bulk_offcanvas.on("click", ".asl-gallery-clear", function(e) {
          e.preventDefault();
          var $control = $(this).closest(".asl-gallery-field-control");
          var $input = $control.find(".asl-gallery-field");
          $input.val("");
          updateBulkGalleryPreview($input);
        });

        $bulk_offcanvas.on("click", ".asl-gallery-field-button", function(e) {
          e.preventDefault();
          var button = $(this);
          var input = button.siblings(".asl-gallery-field");
          var mediaUploader = wp.media({
              title: ASL_REMOTE.LANG.select_media,
              button: {
                  text: ASL_REMOTE.LANG.use_media
              },
              multiple: false
          }).on("select", function() {
              var attachment = mediaUploader.state().get("selection").first().toJSON();
              input.val(attachment.url);
              updateBulkGalleryPreview(input);
          }).open();
        });

        // Internal page link picker for custom fields in the bulk editor.
        var activeBulkPageLinkInput = null,
            bulkPageLinkSearchTimer = null;

        function closeBulkPageLinkPicker() {
          $(".asl-page-link-dialog").remove();
          activeBulkPageLinkInput = null;
        }

        function loadBulkPageLinkResults(search) {
          var $results = $(".asl-page-link-results");
          $results.html('<div class="asl-page-link-message">Searching&hellip;</div>');

          $.get(ASL_REMOTE.URL, {
            action: "asl_search_internal_pages",
            nonce: ASL_REMOTE.nounce,
            search: search || ""
          }).done(function(response) {
            var items = response && response.success && response.data ? response.data.items : [];
            $results.empty();

            if (!items || !items.length) {
              $results.html('<div class="asl-page-link-message">No matching pages found.</div>');
              return;
            }

            $.each(items, function(index, item) {
              var $button = $('<button type="button" class="asl-page-link-result"></button>');
              $button.append($("<strong></strong>").text(item.title || "(no title)"));
              $button.append($("<span></span>").text((item.type || "Page") + " · " + item.url));
              $button.data("url", item.url);
              $results.append($button);
            });
          }).fail(function() {
            $results.html('<div class="asl-page-link-message asl-page-link-error">Unable to load pages. Please try again.</div>');
          });
        }

        $bulk_offcanvas.on("click", ".asl-page-link-button", function(e) {
          e.preventDefault();

          activeBulkPageLinkInput = $(this).closest(".asl-page-link-control").find(".asl-page-link-value")[0] || null;
          if (!activeBulkPageLinkInput) {
            return;
          }

          $("body").append(
            '<div class="asl-page-link-dialog" role="dialog" aria-modal="true" aria-labelledby="asl-page-link-title">' +
              '<div class="asl-page-link-backdrop"></div>' +
              '<div class="asl-page-link-panel">' +
                '<div class="asl-page-link-header"><h2 id="asl-page-link-title">Select a page</h2><button type="button" class="asl-page-link-close" aria-label="Close">&times;</button></div>' +
                '<div class="asl-page-link-search"><label for="asl-page-link-search-input">Search pages and posts</label><input id="asl-page-link-search-input" type="search" autocomplete="off" placeholder="Start typing a page title&hellip;"></div>' +
                '<div class="asl-page-link-results"></div>' +
              '</div>' +
            '</div>'
          );

          loadBulkPageLinkResults("");
          $("#asl-page-link-search-input").trigger("focus");
        });

        $(document).on("input.aslBulkPageLink", "#asl-page-link-search-input", function() {
          var search = this.value;
          clearTimeout(bulkPageLinkSearchTimer);
          bulkPageLinkSearchTimer = setTimeout(function() { loadBulkPageLinkResults(search); }, 250);
        });

        $(document).on("click.aslBulkPageLink", ".asl-page-link-result", function() {
          if (activeBulkPageLinkInput) {
            $(activeBulkPageLinkInput).val($(this).data("url")).trigger("change");
          }
          closeBulkPageLinkPicker();
        });

        $(document).on("click.aslBulkPageLink", ".asl-page-link-close, .asl-page-link-backdrop", closeBulkPageLinkPicker);

        $(document).on("keydown.aslBulkPageLink", function(e) {
          if (e.key === "Escape" && activeBulkPageLinkInput) {
            closeBulkPageLinkPicker();
          }
        });

        $bulk_offcanvas.on("click", ".asl-page-link-clear", function(e) {
          e.preventDefault();
          $(this).closest(".asl-page-link-control").find(".asl-page-link-value").val("").trigger("change");
        });
      }

      $('#btn-asl-bulk-edit').on('click', function(e) {
        var item_ids = getSelectedStoreIds();

        if (item_ids.length === 0) {
          e.preventDefault();
          e.stopPropagation();
          atoastr.error('No Store selected');
          return;
        }

        initBulkOpenHours();
        initBulkExtraFields();
        $bulk_offcanvas.find('.asl-time-details .asl-bulk-open-day').prop('checked', false);
        $bulk_offcanvas.find('.asl-time-details .asl-day-label').val('');
        $('#sl-bulk-edit-count').text(item_ids.length);
        $('#asl-bulk-edit-store-ids').val(item_ids.join(','));
        if (window.bootstrap && bootstrap.Offcanvas) {
          bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('sl-bulk-edit')).show();
        }
      });

      $('#btn-asl-bulk-edit-apply').on('click', function(e) {
        var item_ids = getSelectedStoreIds();
        var apply_description = $('#asl-bulk-edit-apply-description').is(':checked');
        var apply_open_hours  = $('#asl-bulk-edit-apply-open-hours').is(':checked');
        var apply_marker_id   = $('#asl-bulk-edit-apply-marker').is(':checked');
        var apply_logo_id     = $('#asl-bulk-edit-apply-logo').is(':checked');
        var apply_categories  = $('#asl-bulk-edit-apply-categories').is(':checked');
        var $btn = $(this);
        var custom_fields = {};

        if (item_ids.length === 0) {
          atoastr.error('No Store selected');
          return;
        }

        $bulk_offcanvas.find('.asl-bulk-custom-toggle:checked').each(function() {
          var field = $(this).data('field');
          var field_name = 'asl-bulk-custom[' + field + ']';
          var $wrap = $bulk_offcanvas.find('.asl-bulk-field-custom[data-field="' + field + '"]');
          var $inputs = $wrap.find('[name="' + field_name + '"]');

          if (!$inputs.length) {
            return;
          }

          var $first = $inputs.first();
          var tag = $first.prop('tagName');
          var type = ($first.attr('type') || '').toLowerCase();

          if (type === 'radio') {
            custom_fields[field] = $wrap.find('[name="' + field_name + '"]:checked').val() || '';
          } else if (type === 'checkbox') {
            custom_fields[field] = $first.is(':checked') ? $first.val() : '';
          } else if (tag === 'SELECT') {
            custom_fields[field] = $first.val();
          } else {
            custom_fields[field] = $first.val();
          }
        });

        if (!apply_description && !apply_open_hours && !apply_marker_id && !apply_logo_id && !apply_categories && $.isEmptyObject(custom_fields)) {
          atoastr.error('Select at least one field to update');
          return;
        }

        var bulk_open_hours = apply_open_hours ? getBulkOpenHours() : '';
        if (apply_open_hours && $.isEmptyObject(JSON.parse(bulk_open_hours))) {
          atoastr.error('Select at least one day to update');
          return;
        }

        $btn.bootButton('loading');

        var selected_marker_id = null;
        if ($('#ddl-asl-bulk-markers').length) {
          selected_marker_id = ($('#ddl-asl-bulk-markers').data('ddslick').selectedData) ? $('#ddl-asl-bulk-markers').data('ddslick').selectedData.value : $('#ddl-asl-bulk-markers').val();
        }

        var selected_logo_id = null;
        if ($('#ddl-asl-bulk-logos').length) {
          selected_logo_id = ($('#ddl-asl-bulk-logos').data('ddslick').selectedData) ? $('#ddl-asl-bulk-logos').data('ddslick').selectedData.value : $('#ddl-asl-bulk-logos .dd-selected-value').val();
        }

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=bulk_update_store_attributes", {
          item_ids: item_ids,
          apply_description: apply_description ? 1 : 0,
          apply_open_hours: apply_open_hours ? 1 : 0,
          apply_marker_id: apply_marker_id ? 1 : 0,
          apply_logo_id: apply_logo_id ? 1 : 0,
          apply_categories: apply_categories ? 1 : 0,
          description: $('#asl-bulk-edit-description').val(),
          open_hours: bulk_open_hours,
          marker_id: selected_marker_id,
          logo_id: selected_logo_id,
          categories: $('#ddl-asl-bulk-categories').length ? $('#ddl-asl-bulk-categories').val() : [],
          custom_fields: custom_fields
        }, function(_response) {
          $btn.bootButton('reset');
          toastIt(_response);

          if (_response.success) {
            var offcanvas_el = document.getElementById('sl-bulk-edit');
            if (offcanvas_el && window.bootstrap && bootstrap.Offcanvas) {
              bootstrap.Offcanvas.getOrCreateInstance(offcanvas_el).hide();
            }
            table.fnDraw();
          }
        }, 'json');
      });

      //Delete Selected Stores:: bulk
      $('#btn-asl-delete-all').on('click', function(e) {

        var $tmp_stores = $('.asl-p-cont .table input:checked');

        if ($tmp_stores.length == 0) {
          atoastr.error('No Store selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });


        aswal({
            title: ASL_REMOTE.LANG.delete_stores,
            text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_stores + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: ASL_REMOTE.LANG.delete_it,
          })
          .then(
            function() {

              ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_store", { item_ids: item_ids, multiple: true }, function(_response) {

                toastIt(_response);

                if (_response.success) {
                
                  table.fnDraw();
                  return;
                }

              }, 'json');
            }
          );
      });

      //Change the Status
      $('#btn-change-status').on('click', function(e) {

        var $tmp_stores = $('.asl-p-cont .table input:checked');

        if ($tmp_stores.length == 0) {
          atoastr.error('No Store Selected');
          return;
        }

        var item_ids = [];
        $('.asl-p-cont .table input:checked').each(function(i) {

          item_ids.push($(this).attr('data-id'));
        });


        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=store_status", { item_ids: item_ids, multiple: true, status: $('#asl-ddl-status').val() }, function(_response) {

          toastIt(_response);

          if (_response.success) {
          
            table.fnDraw();
            return;
          }

        }, 'json');
      });


      //Validate the Coordinates
      $('#btn-validate-coords').on('click', function(e) {

        var $btn = $(this);

        $btn.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=validate_coords", { }, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

        }, 'json');
      });


      //  show delete store model
      $('#tbl_stores tbody').on('click', '.glyphicon-trash', function(e) {

        var _store_id = $(this).attr("data-id");

        aswal({
          title: ASL_REMOTE.LANG.delete_store,
          text: ASL_REMOTE.LANG.warn_question + " " + ASL_REMOTE.LANG.delete_store + " " + _store_id + "?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#dc3545",
          confirmButtonText: ASL_REMOTE.LANG.delete_it,
        }).then(function() {

          ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=delete_store", { store_id: _store_id }, function(_response) {

            toastIt(_response);

            if (_response.success) {
             
              table.fnDraw();
              return;
            }

          }, 'json');

        });
      });


      //  Approve Pending Stores
      $('#tbl_stores tbody').on('click', '.btn-approve', function(e) {

        var _store_id = $(this).attr("data-id");

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=approve_stores", { store_id: _store_id }, function(_response) {

          toastIt(_response);

          if (_response.success) {
            
            //  Update the Pending Count
            if(parseInt(_response.pending_count) != 0) {
              $('#btn-pending-stores i').html(_response.pending_count);
            }
            //  Remove the alert
            else {
              $('#alert-pending-stores').remove();
              pending_stores   = false;
            }

            table.fnDraw();

            return;
          }

        }, 'json');
      });


      $("#tbl_stores_wrapper .dataTables_scrollHead thead .asl-grid-filter-row input").keyup(function(e) {

        if (e.keyCode == 13) {
          table.fnDraw();
        }
      });

      // Disable Select Controls
      $("thead select").on('change',function(e) {
          table.fnDraw();
      });

      
      //  Load default values for the hidden columns
      if(asl_hidden_columns) {
        $('#ddl-fs-cntrl').val(asl_hidden_columns);
      }

      //the Show/hide columns
      $('#ddl-fs-cntrl').chosen({
        width: "100%",
        placeholder_text_multiple: ASL_REMOTE.LANG.select_columns,
        no_results_text: ASL_REMOTE.LANG.no_columns
      });


      //  Show/Hide the Columns
      $('#sl-btn-sh').on('click', function(e) {

        var sh_columns = $('#ddl-fs-cntrl').val();
        var $btn       = $(this);

        $btn.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=change_options", {'content': sh_columns, 'stype': 'hidden'}, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

          if (_response.success) {

            $('#sl-fields-sh').smodal('hide');
            window.location.reload();
          }

        }, 'json');
      });

    },
     /**
     * [schedule_stores description]
     * @return {[type]}                    [description]
     */
     schedule_stores: function() {

       // Initialize start daterangepicker
       $('#asl-sched-start-date').daterangepicker({
           "singleDatePicker": true,
           "timePicker": true,
           "showDropdowns": true,
           "autoApply": false,
           "alwaysShowCalendars": true,
           "opens": "center",
           "drops": "auto",
           "minDate": moment(),
           "autoUpdateInput": false,
            "locale": {"format": "DD/MM/YYYY h:mm","cancelLabel": 'Clear'},

         });

     // Start daterangepicker apply handler 
       $('#asl-sched-start-date').on('apply.daterangepicker', function(ev, picker) {

         $(this).val(picker.startDate.format("DD/MM/YYYY h:mm"));

         var sdate = $("#asl-sched-start-date").val();

         // Reinitialize end daterangepicker
         $('#asl-sched-end-date').daterangepicker({
             "singleDatePicker": true,
             "timePicker": true,
             "showDropdowns": true,
             "autoApply": true,
             "alwaysShowCalendars": true,
             "opens": "center",
             "drops": "auto",
             "minDate": sdate,
             "locale": {"format": "DD/MM/YYYY h:mm"},
             "autoUpdateInput": false
           });

           $('#asl-sched-end-date').on('apply.daterangepicker', function(ev, picker) {

             $(this).val(picker.startDate.format("DD/MM/YYYY h:mm"));
         
             });

           });

           // Start daterangepicker cancel handler 

       $('#asl-sched-start-date').on('cancel.daterangepicker', function(ev, picker) {

           $('#asl-sched-start-date').val('');

           // Reinitialize end daterangepicker
           $('#asl-sched-end-date').daterangepicker({
               "singleDatePicker": true,
               "timePicker": true,
               "showDropdowns": true,
               "autoApply": true,
               "alwaysShowCalendars": true,
               "opens": "center",
               "drops": "auto",
               "minDate": moment(),
               "locale": {"format": "DD/MM/YYYY h:mm"},
               "autoUpdateInput":false
             });

           });

           // Reinitialize end daterangepicker
            $('#asl-sched-end-date').daterangepicker({
             "singleDatePicker": true,
             "timePicker": true,
             "showDropdowns": true,
             "autoApply": true,
             "alwaysShowCalendars": true,
             "opens": "center",
             "drops": "auto",
             "minDate": moment(),
             "locale": {"format": "DD/MM/YYYY h:mm"},
             "autoUpdateInput": false
           });

         $('#asl-sched-end-date').on('apply.daterangepicker', function(ev, picker) {

               $(this).val(picker.startDate.format("DD/MM/YYYY h:mm"));
             
         });


       // =======================

        // Get Current store id and pass into modal
         $('#tbl_stores tbody').on('click', '.sl-schedule-store_id', function(e) {

             var store_id = $(this).data('id');

             // Unset all fields
             $(".smodal-body #asl-sched-start-date").val('');
             $(".smodal-body #asl-sched-end-date").val('');
             $(".smodal-body #ddl-fs-date-switch").prop('checked', false); 

             // Send store id on modal
             $(".smodal-body #store_id").val( store_id );

             // Ajax call for get start date and end date
             ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=get_schedule_detail',{ store_id: store_id }, function(_response) {

             if (_response.success) {

               // Set modal start date and end date
                $(".smodal-body #asl-sched-start-date").val( _response.store_schedule[0]['option_value'] );
                $(".smodal-body #asl-sched-end-date").val( _response.store_schedule[1]['option_value'] );
               }
             }, 'json');


             // Ajax call for disable switch
             ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=edit_schedule_store_switch',{ store_id: store_id }, function(_response) {

             if (_response.success) {

               // Set modal switch value
               var toggle =  (_response.store_schedule[0]['is_disabled'] == 1) ? 'true' : '';
               $(".smodal-body #ddl-fs-date-switch").prop('checked', toggle); 

               }
             }, 'json');



             });




           // Schedule store
           $('.btn-schedule').on('click', function(e) {

             
            var sdate    = $("#asl-sched-start-date").val(),
                edate    = $("#asl-sched-end-date").val(),
                store_id = $("#store_id").val(),
                disable_switch   = ($("#ddl-fs-date-switch").is(':checked')) ? '1' : '0';


            // Date validation atleat select one field.
              
              if (isEmpty(sdate) && isEmpty(edate)) {

                  atoastr.error('Both field should be not empty please select at least one field.');
                  return false;

              }

              // Start date cannot be greater than the end date
              if (sdate > edate && !isEmpty(edate)) {

                  atoastr.error('The start date cannot be greater than the end date. Please ensure that you have entered the correct dates and try again.');
                  return false;

              }   

             var $btn       = $(this);

             $btn.bootButton('loading');

             ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=schedule_the_store", { store_id: store_id, sdate: sdate , edate : edate, disable_switch : disable_switch }, function(_response) {

               $btn.bootButton('reset');

               toastIt(_response);

               if (_response.success) {

                 $('#sl-schedule-store').smodal('hide');
                 window.location.reload();
               }

             }, 'json');

             
           });


       },
    /**
     * [customize_map description]
     * @param  {[type]} _asl_map_customize [description]
     * @return {[type]}                    [description]
     */
    customize_map: function(_asl_map_customize) {
      var mapLayers = {};
      var kmlPreviewLayer = null;
      var $activeKmlPreview = null;
      var isDirty = false;
      var isInitializing = true;
      var layerConstructors = {
        trafic_layer: function() { return new google.maps.TrafficLayer(); },
        transit_layer: function() { return new google.maps.TransitLayer(); },
        bike_layer: function() { return new google.maps.BicyclingLayer(); }
      };

      _asl_map_customize = (_asl_map_customize && typeof _asl_map_customize === 'object') ? _asl_map_customize : {};

      function setDirty(dirty) {
        isDirty = dirty;
        var $status = $('#asl-map-dirty-status');
        var labels = window.asl_customize_map_l10n || {};
        $status
          .toggleClass('is-dirty', dirty)
          .text(dirty ? labels.unsaved : labels.saved);
      }

      function markDirty() {
        if (!isInitializing) {
          setDirty(true);
        }
      }

      function setLayer(layerKey, enabled) {
        if (!map_object.map_instance || !layerConstructors[layerKey]) {
          return;
        }

        if (enabled && !mapLayers[layerKey]) {
          mapLayers[layerKey] = layerConstructors[layerKey]();
        }

        if (mapLayers[layerKey]) {
          mapLayers[layerKey].setMap(enabled ? map_object.map_instance : null);
        }
      }

      function readMapControls() {
        var controls = {};
        $('.asl-map-control-toggle').each(function() {
          controls[$(this).data('control')] = this.checked ? 1 : 0;
        });
        return controls;
      }

      function applyMapControls() {
        if (!map_object.map_instance) {
          return;
        }

        var controls = readMapControls();
        map_object.map_instance.setOptions({
          cameraControl: controls.cameracontrol === 1,
          zoomControl: controls.zoomcontrol === 1,
          streetViewControl: controls.streetviewcontrol === 1,
          fullscreenControl: controls.fullscreencontrol === 1,
          mapTypeControl: controls.maptypecontrol === 1
        });
      }

      function saveCustomization($button) {
        var customization = {
          trafic_layer: $('#asl-trafic_layer').prop('checked') ? 1 : 0,
          transit_layer: $('#asl-transit_layer').prop('checked') ? 1 : 0,
          bike_layer: $('#asl-bike_layer').prop('checked') ? 1 : 0,
          marker_animations: $('#asl-marker_animations').prop('checked') ? 1 : 0,
          map_controls: readMapControls(),
          drawing: (asl_configs && asl_configs.map_vendor === 'maplibre')
            ? (_asl_map_customize.drawing || {})
            : asl_drawing.get_data()
        };

        var $buttons = $('#asl-save-map, .asl-save-map-secondary');
        $buttons.prop('disabled', true);
        $button.bootButton('loading');

        ServerCall(ASL_REMOTE.URL, {
          action: 'asl_ajax_handler',
          'sl-action': 'save_custom_map',
          data_map: JSON.stringify(customization)
        }, function(response) {
          $button.bootButton('reset');
          $buttons.prop('disabled', false);
          toastIt(response);
          if (response.success) {
            setDirty(false);
          }
        }, 'json');
      }

      window['asl_map_intialized'] = function() {
        var drawingData = _asl_map_customize.drawing || {};
        var center = Array.isArray(drawingData.center) && drawingData.center.length === 2
          ? drawingData.center
          : [asl_configs.default_lat, asl_configs.default_lng];

        map_object.render_a_map(center[0], center[1]);

        if (drawingData.zoom && !isNaN(drawingData.zoom)) {
          map_object.map_instance.setZoom(parseInt(drawingData.zoom));
        }

        if (asl_configs && asl_configs.map_vendor === 'maplibre') {
          $('#asl-trafic_layer, #asl-transit_layer, #asl-bike_layer, #asl-marker_animations')
            .prop('checked', false)
            .prop('disabled', true);
          $('#sl-frm-kml, .asl-drawing-tools').hide();
          applyMapControls();
          isInitializing = false;
          setDirty(false);
          return;
        }

        asl_drawing.initialize(map_object.map_instance);

        ['trafic_layer', 'transit_layer', 'bike_layer', 'marker_animations'].forEach(function(optionKey) {
          $('#' + 'asl-' + optionKey).prop('checked', parseInt(_asl_map_customize[optionKey]) === 1);
        });

        Object.keys(layerConstructors).forEach(function(layerKey) {
          setLayer(layerKey, parseInt(_asl_map_customize[layerKey]) === 1);
        });

        if (map_object.map_marker && parseInt(_asl_map_customize.marker_animations) === 1) {
          map_object.map_marker.setAnimation(google.maps.Animation.BOUNCE);
        }

        if (drawingData.shapes) {
          asl_drawing.loadData(drawingData);
        }

        applyMapControls();

        google.maps.event.addListener(map_object.map_instance, 'zoom_changed', markDirty);
        google.maps.event.addListener(map_object.map_instance, 'dragend', markDirty);

        isInitializing = false;
        setDirty(false);
      };

      if (!(window.google && google.maps)) {
        map_object.intialize();
      } else {
        window.asl_map_intialized();
      }

      Object.keys(layerConstructors).forEach(function(layerKey) {
        $('#asl-' + layerKey).off('.aslCustomizeMap').on('change.aslCustomizeMap', function() {
          setLayer(layerKey, this.checked);
          markDirty();
        });
      });

      $('#asl-marker_animations').off('.aslCustomizeMap').on('change.aslCustomizeMap', function() {
        if (map_object.map_marker) {
          map_object.map_marker.setAnimation(this.checked ? google.maps.Animation.BOUNCE : null);
        }
        markDirty();
      });

      $('.asl-map-control-toggle').off('.aslCustomizeMap').on('change.aslCustomizeMap', function() {
        var controlKey = $(this).data('control');
        if (this.checked && controlKey === 'cameracontrol') {
          $('#asl-zoomcontrol').prop('checked', false);
        } else if (this.checked && controlKey === 'zoomcontrol') {
          $('#asl-cameracontrol').prop('checked', false);
        }
        applyMapControls();
        markDirty();
      });

      $(document).off('asl:map-customization-change.aslCustomizeMap').on('asl:map-customization-change.aslCustomizeMap', markDirty);

      $('#asl-save-map, .asl-save-map-secondary').off('.aslCustomizeMap').on('click.aslCustomizeMap', function() {
        saveCustomization($(this));
      });

      $(window).off('beforeunload.aslCustomizeMap').on('beforeunload.aslCustomizeMap', function() {
        if (isDirty) {
          return (window.asl_customize_map_l10n && asl_customize_map_l10n.unsaved) || 'Unsaved changes';
        }
      });


      // Add the KML Files Uploader
      var url_to_upload = ASL_REMOTE.URL,
        $form           = $('#sl-frm-kml');

      app_engine.uploader($form, url_to_upload + '?action=asl_ajax_handler&asl-nounce=' + ASL_REMOTE.nounce + '&sl-action=add_kml', function(e, data) {

        var data = data.result;

        toastIt(data);

        if(data.success) {
          window.location.reload();
        }
        
      });

      //Validate
      $('#btn-asl-upload-kml').off('.aslCustomizeMap').on('click.aslCustomizeMap', function(e) {

        if ($('#sl-frm-kml ul li').length == 0) {

          atoastr.error((window.asl_customize_map_l10n && asl_customize_map_l10n.no_kml) || 'Choose a KML or KMZ file to upload.');

          e.preventDefault();
          return;
        }
      });

      $('#asl-kml-search').off('.aslCustomizeMap').on('input.aslCustomizeMap', function() {
        var query = $.trim($(this).val()).toLowerCase();
        var visibleRows = 0;

        $('.asl-kml-file-row').each(function() {
          var visible = !query || $(this).data('search').indexOf(query) !== -1;
          $(this).toggle(visible);
          if (visible) {
            visibleRows++;
          }
        });

        $('#asl-kml-no-results').prop('hidden', visibleRows !== 0 || !query);
      });

      $('.asl-kml-preview').off('.aslCustomizeMap').on('click.aslCustomizeMap', function() {
        var $button = $(this);
        var labels = window.asl_customize_map_l10n || {};

        if ($activeKmlPreview && $activeKmlPreview[0] === $button[0]) {
          kmlPreviewLayer.setMap(null);
          kmlPreviewLayer = null;
          $activeKmlPreview.removeClass('active').attr('aria-pressed', 'false');
          $activeKmlPreview.closest('tr').find('.asl-kml-status').removeClass('is-previewing').text(labels.kml_available || 'Available');
          $activeKmlPreview = null;
          return;
        }

        if (kmlPreviewLayer) {
          kmlPreviewLayer.setMap(null);
        }
        if ($activeKmlPreview) {
          $activeKmlPreview.removeClass('active').attr('aria-pressed', 'false');
          $activeKmlPreview.closest('tr').find('.asl-kml-status').removeClass('is-previewing').text(labels.kml_available || 'Available');
        }

        kmlPreviewLayer = new google.maps.KmlLayer($button.data('url'), {
          preserveViewport: false,
          map: map_object.map_instance
        });
        var previewLayer = kmlPreviewLayer;
        $activeKmlPreview = $button.addClass('active').attr('aria-pressed', 'true');
        $button.closest('tr').find('.asl-kml-status').addClass('is-previewing').text(labels.kml_previewing || 'Previewing');

        google.maps.event.addListenerOnce(previewLayer, 'status_changed', function() {
          if (previewLayer.getStatus() !== google.maps.KmlLayerStatus.OK) {
            atoastr.error(labels.kml_preview_error || 'The KML file could not be previewed.');
            previewLayer.setMap(null);
            if (kmlPreviewLayer === previewLayer) {
              $button.removeClass('active').attr('aria-pressed', 'false');
              $button.closest('tr').find('.asl-kml-status').removeClass('is-previewing').text(labels.kml_available || 'Available');
              kmlPreviewLayer = null;
              $activeKmlPreview = null;
            }
          }
        });
      });

      //  Remove KML file event
      $('.asl-kml-list .asl-trash-icon').off('.aslCustomizeMap').on('click.aslCustomizeMap', function(e) {

        var message = window.asl_customize_map_l10n && asl_customize_map_l10n.confirm_delete_kml;
        if (!window.confirm(message || 'Delete this KML file?')) {
          return;
        }

        //  Remove the KML File
        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=delete_kml', { data_: $(this).attr('data-file') }, function(_response) {

          toastIt(_response);

          if (_response.success) {
            window.location.reload();
            return;
          }
        }, 'json');
      });
    },
    /**
     * [InfoBox_maker description]
     * @param {[type]} _inbox_id [description]
     */
    InfoBox_maker: function(_inbox_id) {

    },


    // ===========================================================================================================================================
    /**
     * [edit_store description]
     * @param  {[type]} _store [description]
     * @return {[type]}        [description]
     */
    edit_store: function(_store) {

      this.add_store(true, _store);
      
      if(asl_configs.branches != '0')
        this.branches_dt(_store);
    },
    /**
     * [branches_dt Create the Branches DT]
     * @return {[type]} [description]
     */
    branches_dt: function(_store) {

      var table          = null;
      var parent_id      = _store.id;

      var urlSearchParams = new URLSearchParams(window.location.search);
      var params          = Object.fromEntries(urlSearchParams.entries());
    

      var columnDefs = [
        {"targets": 0},
        {"targets": 1 },
        {"targets": 2, 
        render: function (data, type, full, meta) {
          return '<a href="'+ASL_Instance.manage_stores_url + full.id +'">' + data + "</a>";
        }},
        
        {"targets": 3 },
        {"targets": 4 },
        {"targets": 5 },
      ];

      var invalid_rows  = 0;

      var asInitVals = {};
      table = $('#tbl_stores').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bFilter": false,
        "bServerSide": true,
        "scrollX": true,
        "bAutoWidth": false,
        "columnDefs": columnDefs,
        "iDisplayLength": 10,
        "sAjaxSource": ASL_REMOTE.URL + "?action=asl_ajax_handler&asl-nounce=" + ASL_REMOTE.nounce + "&sl-action=get_store_list_edit&parent_id="+parent_id,
        "columns": [
          { "data": "check" },
          { "data": "id" },
          { "data": "title" },
          { "data": "state" },
          { "data": "city" },
          { "data": "postal_code" },
        ],
      "fnServerParams": function(aoData) {

          $("#tbl_stores_wrapper .dataTables_scrollHead thead input").each(function(i) {

            if (this.value != "") {
              aoData.push({
                "name": 'filter[' + $(this).attr('data-id') + ']',
                "value": this.value

              });
            }

          });

           $("#tbl_stores_wrapper .dataTables_scrollHead thead select").each(function(i) {
                  
                  
                if (this.value != "") {

                    var attr = $("#tbl_stores_wrapper .dataTables_scrollHead #select_branch option:selected").attr('data-id');
                    aoData.push({
                    "name": 'select_filter',
                    "value": this.value

              });
            }

           });
        },

        "order": [[2, 'desc']]
      });

      // console.log(aoData);

      // filter

      $("thead input").keyup(function(e) {
        if (e.keyCode == 13) {
          table.fnDraw();
        }
      });


      $("thead select").on('change',function(e) {
          table.fnDraw();
      });


      // Crud Ajax function for branch
      $('#tbl_stores tbody').on('click', '.custom-checkbox input', function(e) {

        var toggle    = ($(this).is(':checked')) ? '1' : '0',
            parent_id = _store.id,
            store_id  = $(this).attr("data-id");
        

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=add_store_into_branch', { parent_id: parent_id , store_id:store_id , toggle:toggle }, function(_response) {
          toastIt(_response);
          
          if (!_response.success) {
            
            //  revert the check due to error
            e.currentTarget.checked = false;
          }

        }, 'json');
      });

    },
    // ===========================================================================================================================================
    /**
     * [add_store description]
     * @param {[type]} _is_edit [description]
     * @param {[type]} _store   [description]
     */
    add_store: function(_is_edit, _store) {

      //  Make sure correct language is selected
      if(lang_ctrl && lang_ctrl.value != ASL_REMOTE.sl_lang) {
        window.location.search += '&asl-lang=' + lang_ctrl.value;
        return;
      }


      var $form = $('#frm-addstore'),
          hdlr  = this;

      var no_logo_selected = true;

      //  Loop over logos JSON
      for(var l in asl_logos) {

        if (!asl_logos.hasOwnProperty(l)) continue;
        asl_logos[l]['imageSrc'] = ASL_Instance.url + 'Logo/' + asl_logos[l]['imageSrc'];

        //  is-Selected?
        if (_store && _store.logo_id) {

          if(String(asl_logos[l]['value']) == String(_store.logo_id)) {
            asl_logos[l]['selected'] = true; 
            no_logo_selected = false;
          }
        }
      }
      
      asl_logos.unshift({
        value: 0,
        text: ASL_REMOTE.LANG.no_logo,
        selected: no_logo_selected
      });

      //  Logo DDL
      $('#ddl-asl-logos').ddslick({
        data: asl_logos,
        imagePosition: "right",
        selectText: ASL_REMOTE.LANG.select_logo,
        truncateDescription: true
      });

      //  Store Marker ID DDL Set value
      if (_store && _store.marker_id)
        $('#ddl-asl-markers').val(String(_store.marker_id));

      //  Marker DDL
      $('#ddl-asl-markers').ddslick({
        imagePosition: "right",
        selectText: ASL_REMOTE.LANG.select_marker,
        truncateDescription: true
      });

      //  The Current Date
      var current_date      = new Date(),
          open_time_tmpl    = '9:30 AM',
          close_time_tmpl   = '6:30 PM';


      /**
       * [timeChangeEvent Event that is fired when the time is changed]
       * @param  {[type]} e [description]
       * @return {[type]}   [description]
       */
      function timeChangeEvent(e) {

        if($(e.currentTarget).hasClass('asl-start-time')) {
          open_time_tmpl =  e.time.value;
        }
        else
          close_time_tmpl   =  e.time.value; 
      };

      //  Add/Remove DateTime Picker
      $('.asl-time-details tbody').on('click', '.add-k-add', function(e) {

        var $new_slot = $('<div class="form-group">\
                    <div class="input-group bootstrap-asltimepicker">\
                          <input type="text" class="form-control asltimepicker asl-start-time validate[required,funcCall[ASLmatchTime]]" placeholder="' + ASL_REMOTE.LANG.start_time + '"  value="'+open_time_tmpl+'">\
                          <span class="input-group-append add-on"><span class="input-group-text"><svg width="20" height="20"><use xlink:href="#i-clock"></use></svg></span></span>\
                        </div>\
                        <div class="input-append input-group bootstrap-asltimepicker">\
                          <input type="text" class="form-control asltimepicker asl-end-time validate[required]" placeholder="' + ASL_REMOTE.LANG.end_time + '" value="'+close_time_tmpl+'">\
                          <span class="input-group-append add-on"><span class="input-group-text"><svg width="20" height="20"><use xlink:href="#i-clock"></use></svg></span></span>\
                        </div>\
                        <span class="add-k-delete glyp-trash text-danger">\
                          <svg width="16" height="16"><use xlink:href="#i-trash"></use></svg>\
                        </span>\
                    </div>');


        var $cur_slot = $(this).parent().prev().find('.asl-all-day-times .asl-closed-lbl');
        // var $cur_td = $(this).closest('tr');
        // var $cur_slot = $cur_td.find('.asl-all-day-times .asl-closed-lbl');
  
        $cur_slot.before($new_slot);

        //  Add the Time slot timepicker
        $new_slot.find('input.asltimepicker').removeAttr('id').attr('class', 'form-control asltimepicker validate[required]').asltimepicker({
          //defaultTime: current_date,
          //orientation: 'auto',
          showMeridian: (asl_configs && asl_configs.time_format == '1') ? false : true,
          appendWidgetTo: '.asl-p-cont'
        })
        .on('changeTime.asltimepicker', timeChangeEvent);
      });


      //  Delete the Time Row
      $('.asl-time-details tbody').on('click', '.add-k-delete', function(e) {
        var $this_tr = $(this).parent().remove();
      });


      //  Add the time Picker
      $('.asl-p-cont .asl-time-details .asltimepicker').asltimepicker({
        showMeridian: (asl_configs && asl_configs.time_format == '1') ? false : true,
        appendWidgetTo: '.asl-p-cont',
      })
      .on('changeTime.asltimepicker', timeChangeEvent);

      //Convert the time for validation
      function asl_timeConvert(_str) {

        if (!_str) return 0;

        var time = $.trim(_str).toUpperCase();

        //when 24 hours
        if (asl_configs && asl_configs.time_format == '1') {

          var regex = /(1[012]|[0-9]):[0-5][0-9]/;

          if (!regex.test(time))
            return 0;

          var hours = Number(time.match(/^(\d+)/)[1]);
          var minutes = Number(time.match(/:(\d+)/)[1]);

          return hours + (minutes / 100);
        } else {

          var regex = /(1[012]|[1-9]):[0-5][0-9][ ]?(AM|PM)/;

          if (!regex.test(time))
            return 0;

          var hours = Number(time.match(/^(\d+)/)[1]);
          var minutes = Number(time.match(/:(\d+)/)[1]);
          var AMPM = (time.indexOf('PM') != -1) ? 'PM' : 'AM';

          if (AMPM == "PM" && hours < 12) hours = hours + 12;
          if (AMPM == "AM" && hours == 12) hours = hours - 12;

          return hours + (minutes / 100);
        }
      };

    

      //  Match the time :: validation
      window['ASLmatchTime'] = function(field, rules, i, options) {};


      //  Copy the Monday time to rest of the days
      $('#asl-time-cp').on('click', function(e) {
          var $monday    = $('.asl-p-cont .asl-time-details .asl-all-day-times').eq(0),
              $rest_days = $('.asl-p-cont .asl-time-details .asl-all-day-times:not(:first)');

          //  Clone Everyday
          $rest_days.each(function(e) {
            var day_index = parseInt(e) + 1;
            $(this).html($monday.children().clone());
            $(this).find('.asl-day-label').val('');
            $(this).find('.a-swith').find('label').attr('for', 'cmn-toggle-' + day_index);
            $(this).find('.a-swith').find('input').attr('id', 'cmn-toggle-' + day_index);
          });
        
          //  Add the Picker
          $('.asl-p-cont .asl-time-details .asltimepicker').asltimepicker({
            showMeridian: (asl_configs && asl_configs.time_format == '1') ? false : true,
            appendWidgetTo: '.asl-p-cont',
          })
          .on('changeTime.asltimepicker', timeChangeEvent);
      });

      // Initialize Google Places autocomplete for the add-store Place ID field.
      function initializePlaceIdAutocomplete() {

        var place_id_input = document.getElementById('txt_placed_id');

        if (!place_id_input || !window.google || !google.maps ||
            !google.maps.places || !google.maps.places.Autocomplete) {
          return;
        }

        var autocomplete_options = {},
            country_restrict     = place_id_input.getAttribute('data-country-restrict');

        if (country_restrict) {
          var country_codes = country_restrict.split(',').map(function(country_code) {
            return country_code.trim().toLowerCase();
          }).filter(function(country_code) {
            return country_code.length === 2;
          });

          if (country_codes.length) {
            autocomplete_options.componentRestrictions = {country: country_codes};
          }
        }

        var place_id_autocomplete = new google.maps.places.Autocomplete(place_id_input, autocomplete_options);
        place_id_autocomplete.setFields(['place_id']);
        place_id_autocomplete.addListener('place_changed', function() {

          var place = place_id_autocomplete.getPlace();

          // Preserve manually entered or pasted Place IDs unless Google returns
          // a valid Place ID from an autocomplete selection.
          if (place && place.place_id) {
            place_id_input.value = place.place_id;
          }
        });
      }

      // Initialize the Google Maps
      window['asl_map_intialized'] = function() {
        if (_store)
          map_object.render_a_map(_store.lat, _store.lng);
        else
          map_object.render_a_map(parseFloat(asl_configs.default_lat), parseFloat(asl_configs.default_lng));

        initializePlaceIdAutocomplete();
      };

      if (!(window['google'] && google.maps)) {
        map_object.intialize();
      } else
        asl_map_intialized();



      //  for the asl-wc
      $('.sl-chosen select').each(function(item) {

        var $ddl_chosen = $(this);

        $ddl_chosen.chosen({
          width: "100%",
          placeholder_text_multiple: $ddl_chosen.data('ph') || 'Select',
          no_results_text: $ddl_chosen.data('none') || 'None'
        });
      });

      
      //  Category ddl
      $('#ddl_categories').chosen({
        width: "100%",
        placeholder_text_multiple: ASL_REMOTE.LANG.select_category,
        no_results_text: ASL_REMOTE.LANG.no_category
      });


      // Debounced error display function
      const debouncedError = ASLDebounce(function (field) {

        // Get the label for the invalid field
        const fieldId   = field.attr('id');        
        const $label    = $form.find(`label[for="${fieldId}"]`);
        const labelText = $label.text() || 'a required field';

        // Show the error message using atoastr
        atoastr.error(`${ASL_REMOTE.LANG.required_field}: ${labelText}`);
      }, 300); // Adjust delay as needed


      //  Form Submit
      $form.validationEngine({
        binded: false,
        scroll: false,
        showArrow: false,
        showOneMessage: false,
        validateNonVisibleFields: true,
        onFieldFailure: debouncedError
      });

      //  To get Lat/lng
      $('#txt_city,#txt_state,#txt_postal_code').on('blur', function(e) {

        if (!isEmpty($form[0].elements["data[city]"].value)) {

          var address = [$form[0].elements["data[street]"].value, $form[0].elements["data[city]"].value, $form[0].elements["data[postal_code]"].value, $form[0].elements["data[state]"].value];

          var q_address = [];

          for (var i = 0; i < address.length; i++) {

            if (address[i])
              q_address.push(address[i]);
          }

          var $selected_country = jQuery('#txt_country option:selected'),
              country_id = $selected_country.val(),
              country_name = jQuery.trim($selected_country.text());

          //Add country if available
          if (country_id && country_name) {
            q_address.push(country_name);
          }

          address = q_address.join(', ');

          codeAddress(address, function(_geometry) {

            var s_location = [_geometry.location.lat(), _geometry.location.lng()];
            map_object.map_marker.setPosition(_geometry.location);
            map.panTo(_geometry.location);
            map.setZoom(14);
            app_engine.pages.store_changed(s_location);

          });
        }
      });

      $form.find('.asl-rich-text-editor').each(function () {
        const id = $(this).attr('id');
        const hasWPEditor = window.wp && wp.editor && typeof wp.editor.initialize === 'function';

        if (!id) {
          return;
        }

        const editorConfig = {
          height: 200,
          menubar: false,
          plugins: 'paste link lists',
          paste_as_text: true,
          branding: false,
          // Keep links exactly as entered (absolute or relative).
          relative_urls: false,
          remove_script_host: false,
          convert_urls: false,
          urlconverter_callback: function (url) {
            return url;
          },
          toolbar: 'bold italic underline | bullist numlist | link',
          setup: function (editor) {
            editor.on('change input keyup', function () {
              editor.save(); // Sync content to textarea
            });
          }
        };

        if (hasWPEditor) {
          // Re-init safely if this form is opened multiple times.
          if (typeof wp.editor.remove === 'function') {
            wp.editor.remove(id);
          } else if (window.tinymce && tinymce.get(id)) {
            tinymce.get(id).remove();
          }

          wp.editor.initialize(id, {
            tinymce: editorConfig,
            quicktags: true,
            mediaButtons: true
          });

          return;
        }

        // Fallback when WP editor APIs are unavailable.
        if (window.tinymce && tinymce.get(id)) {
          tinymce.get(id).remove();
        }

        tinymce.init({
          selector: '#' + id,
          height: editorConfig.height,
          menubar: editorConfig.menubar,
          plugins: editorConfig.plugins,
          paste_as_text: editorConfig.paste_as_text,
          branding: editorConfig.branding,
          relative_urls: editorConfig.relative_urls,
          remove_script_host: editorConfig.remove_script_host,
          convert_urls: editorConfig.convert_urls,
          urlconverter_callback: editorConfig.urlconverter_callback,
          toolbar: editorConfig.toolbar,
          setup: editorConfig.setup
        });
      });


      //  Coordinates Fixes
      var _coords = {
        lat: '',
        lng: ''
      };

      //  Click the Edit Coordinates
      $('#lnk-edit-coord').on('click', function(e) {

        _coords.lat = $('#asl_txt_lat').val();
        _coords.lng = $('#asl_txt_lng').val();

        $('#asl_txt_lat,#asl_txt_lng').val('').removeAttr('readonly');
      });


      //  Change Event Coordinates
      var $coord = $('#asl_txt_lat,#asl_txt_lng');
      $coord.on('change', function(e) {

        if ($coord[0].value && $coord[1].value && !isNaN($coord[0].value) && !isNaN($coord[1].value)) {

          var loc = {lat: parseFloat($('#asl_txt_lat').val()), lng: parseFloat($('#asl_txt_lng').val())};
          map_object.map_marker.setPosition(loc);
          map.panTo(loc);
        }
      });

      // Get Working Hours
      function getOpenHours() {

        var open_hours = {};

        $('.asl-time-details .asl-all-day-times').each(function(e) {

          var $day = $(this),
            day_index = String($day.data('day'));
          open_hours[day_index] = null;

          var day_label = $.trim($day.closest('tr').find('.asl-day-label').val() || '');
          if (day_label) {
            open_hours[day_index + '_label'] = day_label;
          }

          if ($day.find('.form-group').length > 0) {

            open_hours[day_index] = [];
          } else {

            open_hours[day_index] = ($day.find('.asl-closed-lbl input')[0].checked) ? '1' : '0';
          }

          $day.find('.form-group').each(function() {

            var $hours = $(this).find('input');
            open_hours[day_index].push($hours.eq(0).val() + ' - ' + $hours.eq(1).val());
          });

        });

        return JSON.stringify(open_hours);
      }

      function updateGalleryPreview($input) {
        var url = $.trim($input.val());
        var $control = $input.closest(".asl-gallery-field-control");
        var $preview = $control.find(".asl-gallery-preview");
        var $img = $preview.find("img");

        if (url) {
          $img.attr("src", url);
          $preview.removeClass("is-empty");
        } else {
          $img.attr("src", "");
          $preview.addClass("is-empty");
        }
      }

      $(".asl-gallery-field").each(function() {
        updateGalleryPreview($(this));
      });

      $form.on("input change", ".asl-gallery-field", function() {
        updateGalleryPreview($(this));
      });

      $form.on("click", ".asl-gallery-clear", function(e) {
        e.preventDefault();
        var $control = $(this).closest(".asl-gallery-field-control");
        var $input = $control.find(".asl-gallery-field");
        $input.val("");
        updateGalleryPreview($input);
      });

      // Gallery button
      $(".asl-gallery-field-button").on("click", function(e) {
        e.preventDefault();
        var button = $(this);
        var input = button.siblings(".asl-gallery-field");
        var mediaUploader = wp.media({
            title: ASL_REMOTE.LANG.select_media,
            button: {
                text: ASL_REMOTE.LANG.use_media
            },
            multiple: false
        }).on("select", function() {
            var attachment = mediaUploader.state().get("selection").first().toJSON();
            input.val(attachment.url);
            updateGalleryPreview(input);
        }).open();
      });

      // A plugin-owned picker avoids CSS conflicts with WordPress's editor modal.
      var activePageLinkInput = null,
          pageLinkSearchTimer = null;

      function closePageLinkPicker() {
        $(".asl-page-link-dialog").remove();
        activePageLinkInput = null;
      }

      function loadPageLinkResults(search) {
        var $results = $(".asl-page-link-results");
        $results.html('<div class="asl-page-link-message">Searching&hellip;</div>');

        $.get(ASL_REMOTE.URL, {
          action: "asl_search_internal_pages",
          nonce: ASL_REMOTE.nounce,
          search: search || ""
        }).done(function(response) {
          var items = response && response.success && response.data ? response.data.items : [];
          $results.empty();

          if (!items || !items.length) {
            $results.html('<div class="asl-page-link-message">No matching pages found.</div>');
            return;
          }

          $.each(items, function(index, item) {
            var $button = $('<button type="button" class="asl-page-link-result"></button>');
            $button.append($("<strong></strong>").text(item.title || "(no title)"));
            $button.append($("<span></span>").text((item.type || "Page") + " · " + item.url));
            $button.data("url", item.url);
            $results.append($button);
          });
        }).fail(function() {
          $results.html('<div class="asl-page-link-message asl-page-link-error">Unable to load pages. Please try again.</div>');
        });
      }

      $form.on("click", ".asl-page-link-button", function(e) {
        e.preventDefault();

        activePageLinkInput = document.getElementById($(this).data("target"));
        if (!activePageLinkInput) {
          return;
        }

        $("body").append(
          '<div class="asl-page-link-dialog" role="dialog" aria-modal="true" aria-labelledby="asl-page-link-title">' +
            '<div class="asl-page-link-backdrop"></div>' +
            '<div class="asl-page-link-panel">' +
              '<div class="asl-page-link-header"><h2 id="asl-page-link-title">Select a page</h2><button type="button" class="asl-page-link-close" aria-label="Close">&times;</button></div>' +
              '<div class="asl-page-link-search"><label for="asl-page-link-search-input">Search pages and posts</label><input id="asl-page-link-search-input" type="search" autocomplete="off" placeholder="Start typing a page title&hellip;"></div>' +
              '<div class="asl-page-link-results"></div>' +
            '</div>' +
          '</div>'
        );

        loadPageLinkResults("");
        $("#asl-page-link-search-input").trigger("focus");
      });

      $(document).on("input", "#asl-page-link-search-input", function() {
        var search = this.value;
        clearTimeout(pageLinkSearchTimer);
        pageLinkSearchTimer = setTimeout(function() { loadPageLinkResults(search); }, 250);
      });

      $(document).on("click", ".asl-page-link-result", function() {
        if (activePageLinkInput) {
          activePageLinkInput.value = $(this).data("url");
          $(activePageLinkInput).trigger("change");
        }
        closePageLinkPicker();
      });

      $(document).on("click", ".asl-page-link-close, .asl-page-link-backdrop", closePageLinkPicker);

      $(document).on("keydown", function(e) {
        if (e.key === "Escape" && $(".asl-page-link-dialog").length) {
          closePageLinkPicker();
        }
      });

      $form.on("click", ".asl-page-link-clear", function(e) {
        e.preventDefault();
        $("#" + $(this).data("target")).val("").trigger("change");
      });


    
      
      //  Add store button
      $('#btn-asl-add').on('click', function(e) {

        if (!$form.validationEngine('validate')) return;

        var $btn = $(this),
          formData = $form.ASLSerializeObject();

        formData['action']       = 'asl_ajax_handler';
        formData['sl-action']    = (_is_edit) ? 'update_store' : 'add_store';
        formData['sl-category']  = $('#ddl_categories').val();

        if (_is_edit) { formData['updateid'] = $('#update_id').val(); }

        formData['data[marker_id]'] = ($('#ddl-asl-markers').data('ddslick').selectedData) ? $('#ddl-asl-markers').data('ddslick').selectedData.value : jQuery('#ddl-asl-markers .dd-selected-value').val();
        formData['data[logo_id]'] = ($('#ddl-asl-logos').data('ddslick').selectedData) ? $('#ddl-asl-logos').data('ddslick').selectedData.value : jQuery('#ddl-asl-logos .dd-selected-value').val();

        //Ordering
        if (formData['ordr'] && isNaN(formData['ordr']))
          formData['ordr'] = '0';

  
        formData['data[open_hours]'] = getOpenHours();


        $btn.bootButton('loading');

        ServerCall(ASL_REMOTE.URL, formData, function(_response) {

          $btn.bootButton('reset');
            
          toastIt(_response);

          if (_response.success) {

            if (_is_edit) {
              _response.msg += " Redirect...";
              //window.location.replace(ASL_REMOTE.URL.replace('-ajax', '') + "?page=manage-agile-store");
            }
            //  Create New Reset
            else
              $form[0].reset();

            return;
          }


        }, 'json');
      });


      //  UPLOAD LOGO FILE IMAGE
      var url_to_upload = ASL_REMOTE.URL,
          $form_upload  = $('#frm-upload-logo');

      //  Add the logo uploader
      this._logo_media_uploader(function(data) {

        var _HTML = '';
        for (var k in data.list)
          _HTML += '<option data-imagesrc="' + ASL_Instance.url + 'Logo/' + data.list[k].path + '" data-description="&nbsp;" value="' + data.list[k].id + '">' + data.list[k].name + '</option>';


        $('#ddl-asl-logos').empty().ddslick('destroy');
        $('#ddl-asl-logos').html(_HTML).ddslick({
          //data: ddData,
          imagePosition: "right",
          selectText: ASL_REMOTE.LANG.select_logo,
          truncateDescription: true,
          defaultSelectedIndex: (_store) ? String(_store.logo_id) : null
        });

        $('#addimagemodel').smodal('hide');
        $form_upload.find('input:text, input:file').val('');


      });

      //  UPLOAD MARKER IMAGE FILE
      var $form_marker = $('#frm-upload-marker');

      app_engine.uploader($form_marker, url_to_upload + '?action=asl_ajax_handler&asl-nounce=' + ASL_REMOTE.nounce + '&sl-action=add_markers', function(_e, _data) {

        var data = _data.result;

        toastIt(data);

        if (data.success) {

          var _HTML = '';
          for (var k in data.list)
            _HTML += '<option data-imagesrc="' + ASL_Instance.url + 'icon/' + data.list[k].icon + '" data-description="&nbsp;" value="' + data.list[k].id + '">' + data.list[k].marker_name + '</option>';


          $('#ddl-asl-markers').empty().ddslick('destroy');

          $('#ddl-asl-markers').html(_HTML).ddslick({
            //data: ddData,
            imagePosition: "right",
            selectText: ASL_REMOTE.LANG.select_marker,
            truncateDescription: true,
            defaultSelectedIndex: (_store) ? String(_store.marker_id) : null
          });

          $('#addmarkermodel').smodal('hide');
          $form_marker.find('.progress_bar_').hide();
          $form_marker.find('input:text, input:file').val('');
        }
      });

    },
    /**
     * [user_setting User Settings]
     * @param  {[type]} _configs [description]
     * @return {[type]}          [description]
     */
    user_setting: function(_configs) {

      var $form = $('#frm-usersetting');

      var _keys = Object.keys(_configs);


      /**
       * [set_tmpl_image Current Image Template]
       */
      function set_tmpl_image() {

        var _tmpl = document.getElementById('asl-template').value,
          _lyout  = document.getElementById('asl-layout').value;

        //  Category accordion
        if(_lyout == '2')
          _lyout = '1';

        
        var tmpl_name = (_tmpl == 'list' || _tmpl == 'list-2' || _tmpl == '4' || _tmpl == '5')? _tmpl: _tmpl + '-' + _lyout;
        $(document.getElementById('asl-tmpl-img')).attr('src', ASL_Instance.plugin_url + 'admin/images/asl-tmpl-' + tmpl_name + '.png');

        //  Hide the Layout control for the List Template
        if(_tmpl == 'list' || _tmpl == '4')
          $('.asl-p-cont .layout-section').addClass('hide');
        else
          $('.asl-p-cont .layout-section').removeClass('hide');
      }

      var radio_fields = ['additional_info', 'link_type', 'distance_unit', 'geo_button', 'time_format', 'week_hours', 'distance_control', 'single_cat_select', 'map_layout', 'infobox_layout', 'color_scheme', 'color_scheme_1', 'color_scheme_2', 'color_scheme_3', 'font_color_scheme','gdpr', 'tabs_layout', 'filter_ddl'];

      for (var i in _keys) {

        if (!_keys.hasOwnProperty(i)) continue;

        if (radio_fields.indexOf(_keys[i]) != -1) {

          var $elem = $form.find('#asl-' + _keys[i] + '-' + _configs[_keys[i]]);
          
          if($elem && $elem[0])
            $elem[0].checked = true;
          
          continue;
        }


        var $elem = $form.find('#asl-' + _keys[i]);

        if($elem[0]) {

          if ($elem[0].type == 'checkbox')
            $elem[0].checked = (_configs[_keys[i]] == '0') ? false : true;
          else
            $elem.val(_configs[_keys[i]]);
        }
      }

      // Present the two legacy search settings as one simple admin control.
      // The hidden fields keep saved data and frontend/shortcode behavior compatible.
      var $searchMode = $('#asl-search_mode'),
          $searchProvider = $('#asl-search_provider'),
          $searchType = $('#asl-search_type');

      function getSearchMode(provider, type) {
        provider = String(provider || 'automatic').toLowerCase();
        type = String(type == null ? '0' : type);

        if (type === '1' || type === '2') return 'automatic';
        if (type === '3') return 'geocode_enter';
        if (provider === 'disabled') return 'disabled';
        if (provider === 'geoapify') return 'geoapify';
        if (provider === 'mapbox') return 'mapbox';
        if (provider === 'google') return type === '4' ? 'google_new' : 'google_legacy';
        return type === '4' ? 'automatic' : 'google_legacy';
      }

      function setSearchSettings(mode) {
        var settings = {
          automatic: ['automatic', '4'],
          google_new: ['google', '4'],
          google_legacy: ['google', '0'],
          geoapify: ['geoapify', '4'],
          mapbox: ['mapbox', '4'],
          geocode_enter: ['google', '3'],
          disabled: ['disabled', '0']
        }[mode] || ['automatic', '4'];

        $searchProvider.val(settings[0]);
        $searchType.val(settings[1]);
      }

      if ($searchMode.length) {
        var initialSearchMode = getSearchMode($searchProvider.val(), $searchType.val());
        $searchMode.val(initialSearchMode);
        setSearchSettings(initialSearchMode);
        $searchMode.on('change', function() {
          setSearchSettings(this.value);
          refreshMapProviderSettings();
        });
      }

      // Keep provider credentials with the map settings and only show fields
      // that apply to the selected map/search combination.
      var savedTileProviderStyle = String(_configs.tile_provider_style || 'default');
      var tileProviderStyles = {
        geoapify: [['default', 'Default Provider Style'], ['osm-bright-smooth', 'Bright Smooth'], ['osm-carto', 'OSM Carto'], ['positron', 'Positron'], ['dark-matter', 'Dark Matter']],
        mapbox: [['default', 'Default Provider Style'], ['streets-v12', 'Streets'], ['outdoors-v12', 'Outdoors'], ['light-v11', 'Light'], ['dark-v11', 'Dark'], ['satellite-v9', 'Satellite'], ['satellite-streets-v12', 'Satellite Streets']],
        maptiler: [['default', 'Default Provider Style'], ['streets-v4', 'Streets'], ['basic-v2', 'Basic'], ['bright-v2', 'Bright'], ['outdoor-v2', 'Outdoor'], ['topo-v2', 'Topographic'], ['satellite', 'Satellite']]
      };

      function refreshTileProviderStyles(tileProvider) {
        var $style = $('#asl-tile_provider_style'),
            styles = tileProviderStyles[tileProvider] || [],
            previousProvider = String($style.attr('data-provider') || ''),
            selected = previousProvider === tileProvider ? String($style.val() || 'default') : savedTileProviderStyle;

        if (!$style.length) return;
        $style.empty();
        $.each(styles, function(index, style) {
          $('<option>').val(style[0]).text(style[1]).appendTo($style);
        });
        if (!$style.find('option').filter(function() { return this.value === selected; }).length) selected = 'default';
        $style.val(selected).attr('data-provider', tileProvider);
        savedTileProviderStyle = 'default';
      }

      function refreshMapProviderSettings() {
        var mapVendor = String($('#asl-map_vendor').val() || 'google'),
            tileProvider = String($('#asl-tile_provider').val() || 'geoapify'),
            searchMode = String($searchMode.val() || 'automatic'),
            usesMapLibre = mapVendor === 'maplibre',
            automaticUsesGoogle = searchMode === 'automatic' && !usesMapLibre,
            automaticUsesGeoapify = searchMode === 'automatic' && usesMapLibre,
            usesGoogle = mapVendor === 'google' || automaticUsesGoogle || searchMode === 'google_new' || searchMode === 'google_legacy' || searchMode === 'geocode_enter',
            usesGeoapify = (usesMapLibre && tileProvider === 'geoapify') || automaticUsesGeoapify || searchMode === 'geoapify',
            usesMapbox = (usesMapLibre && tileProvider === 'mapbox') || searchMode === 'mapbox',
            usesGenericTileKey = usesMapLibre && tileProvider === 'maptiler',
            usesProviderStyles = usesMapLibre && !!tileProviderStyles[tileProvider],
            usesCustomGoogleLayout = mapVendor === 'google' && $('input[name="data[map_layout]"]:checked').val() === '9';

        refreshTileProviderStyles(tileProvider);
        $('.asl-tile-provider-setting').toggle(usesMapLibre);
        $('.asl-tile-provider-style-setting').toggle(usesProviderStyles);
        $('.asl-osm-tile-help').toggle(usesMapLibre && tileProvider === 'osm');
        $('.asl-maplibre-style-setting').toggle(usesMapLibre && tileProvider !== 'osm');
        $('.asl-geoapify-key-setting').toggle(usesGeoapify);
        $('.asl-mapbox-key-setting').toggle(usesMapbox);
        $('.asl-tile-provider-key-setting').toggle(usesGenericTileKey);
        $('.asl-google-key-setting').toggle(usesGoogle);
        $('.asl-google-map-setting, .asl-google-advanced-marker-setting, #maps-tab .map_layout')
          .toggleClass('asl-setting-hidden', mapVendor !== 'google');
        $('.asl-map-custom-setting').toggleClass('asl-setting-hidden', !usesCustomGoogleLayout);
        $('[data-map-settings-group="appearance"]').toggle(mapVendor === 'google' || (usesMapLibre && tileProvider !== 'osm'));
        $('[data-map-settings-group="credentials"]').toggle(usesGoogle || usesGeoapify || usesMapbox || usesGenericTileKey);
        $('[data-map-settings-group="google-style"]').toggle(mapVendor === 'google');
        refreshMapVendorCards();
      }

      function refreshMapVendorCards() {
        var selectedVendor = String($('#asl-map_vendor').val() || 'google');

        $('.asl-map-vendor-card').each(function() {
          var selected = this.getAttribute('data-map-vendor') === selectedVendor;
          this.classList.toggle('is-selected', selected);
          this.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });
      }

      $('.asl-map-vendor-card').on('click', function() {
        $('#asl-map_vendor').val(this.getAttribute('data-map-vendor')).trigger('change');
      });

      $('#asl-map_vendor, #asl-tile_provider, input[name="data[map_layout]"]').on('change', refreshMapProviderSettings);
      refreshMapProviderSettings();


      ///Make layout Active
      $('.asl-p-cont .layout-box img').eq($('#asl-template')[0].selectedIndex).addClass('active');

      $('#asl-template').on('change', function(e) {

        $('.asl-p-cont .layout-box img.active').removeClass('active');
        $('.asl-p-cont .layout-box img').eq(this.selectedIndex).addClass('active');
      });

      //  Filter_ddl
      if(_configs.filter_ddl) {

        $('#asl-filter_ddl').val(_configs.filter_ddl.split(','));
      }

      // Chosen for the fitler_ddl
      $('#asl-filter_ddl').chosen({
        width: "100%",
        placeholder_text_multiple: ASL_REMOTE.LANG.select_filters,
        no_results_text: ASL_REMOTE.LANG.no_filter
      });

      // Store form fields ordering/visibility (multiple sections)
      var $storeFields = $('.asl-store-form-fields'),
          $storeFieldsInput = $('#asl-store-form-fields-input');

      function refreshStoreFieldsInput() {
        if (!$storeFields.length || !$storeFieldsInput.length) {
          return;
        }

        var fields = [];

        $storeFields.each(function() {
          var $list = $(this),
              section = $list.data('section');

          $list.find('li').each(function() {
            var $li = $(this);
            var $toggle = $li.find('.asl-store-field-toggle');
            var hasToggle = $toggle.length > 0;
            var isEnabled = hasToggle ? ($toggle.is(':checked') ? 1 : 0) : 1; // default on when toggle is hidden/locked

            fields.push({
              key: $li.data('key'),
              field: $li.data('field'),
              label: $li.data('label'),
              type: $li.data('type'),
              section: section,
              enabled: isEnabled
            });
          });
        });

        $storeFieldsInput.val(JSON.stringify(fields));
      }

      if ($storeFields.length) {

        // Initialize toggle state from data attribute
        $storeFields.find('li').each(function() {
          var $li = $(this);
          var enabled = $li.data('enabled');
          if (enabled === 0 || enabled === '0') {
            $li.find('.asl-store-field-toggle').prop('checked', false);
          }
        });

        // Enable sorting when sortable is available
        if ($.fn.sortable) {
          $storeFields.sortable({
            handle: '.asl-drag-handle',
            connectWith: '.asl-store-form-fields',
            update: refreshStoreFieldsInput
          });
        }

        // Sync when toggles change
        $storeFields.on('change', '.asl-store-field-toggle', refreshStoreFieldsInput);

        refreshStoreFieldsInput();
      }

      // Bulk edit fields selection (global setting)
      var $bulkEditFieldsInput = $('#asl-bulk-edit-fields-input');
      var $bulkEditToggles = $('.asl-bulk-edit-field-toggle');

      function refreshBulkEditFieldsInput() {
        if (!$bulkEditFieldsInput.length || !$bulkEditToggles.length) {
          return;
        }

        var selected_fields = [];
        $bulkEditToggles.each(function() {
          if ($(this).is(':checked')) {
            selected_fields.push($(this).data('field'));
          }
        });

        $bulkEditFieldsInput.val(JSON.stringify(selected_fields));
      }

      if ($bulkEditToggles.length) {
        $bulkEditToggles.on('change', refreshBulkEditFieldsInput);
        refreshBulkEditFieldsInput();
      }

      // ---------------------------------------------------------------
      //  slug_attr_ddl
      
      var $ddl_slug       = $('#asl-slug_attr_ddl'),
          ddl_slug_values = [];

      if(_configs.slug_attr_ddl) {

        ddl_slug_values = _configs.slug_attr_ddl.split(',');

        $ddl_slug.val(ddl_slug_values);
      }

      // Chosen for the fitler_ddl_store
      $ddl_slug.chosen({
        width: "100%",
        placeholder_text_multiple: ASL_REMOTE.LANG.select_slugs,
        no_results_text: ASL_REMOTE.LANG.no_filter
      });

      $ddl_slug.on('change', function(evt, params) {

        //  add the value
        if(params.selected) {
          ddl_slug_values.push(params.selected);
        }
        //  remove the value
        else if(params.deselected) {

          ddl_slug_values = ddl_slug_values.filter(function(element) {return element !== params.deselected;});
        }        
      });

      // ---------------------------------------------------------------



      /////*Validation Engine*/////
      $form.validationEngine({
        binded: true,
        scroll: false
      });


      //  Main save button
      $('.btn-asl-user_setting').on('click', function(e) {

        if (!$form.validationEngine('validate')) return;

        var $btn = $(this);

        $btn.bootButton('loading');

        var all_data = {
          data: {
            show_categories: 0,
            advance_filter: 0,
            time_switch: 0,
            category_marker: 0,
            distance_slider: 0,
            analytics: 0,
            scroll_wheel: 0,
            target_blank: 0,
            user_center: 0,
            smooth_pan: 0,
            sort_by_bound: 0,
            full_width: 0,
            //filter_result:0,
            radius_circle: 0,
            remove_maps_script: 0,
            category_bound: 0,
            locale: 0,
            geo_marker: 0,
            sort_random: 0,
            and_filter: 0,
            fit_bound: 0,
            admin_notify: 0,
            //cluster: 0,
            display_list: 0,
            hide_search: 0,
            store_schema: 0,
            store_page_show_country: 0,
            hide_hours: 0,
            slug_link: 0,
            hide_logo: 0,
            direction_btn: 0,
            zoom_btn: 0,
            additional_info: 0,
            print_btn: 0,
            address_ddl: 0,
            store_schedule: 0,
            tran_lbl: 0,
            wpfrm_store_notify: 0,
          }
        };

        var data = $form.ASLSerializeObject();


        all_data = $.extend(all_data, data);


        //  Save the custom Map
        all_data['map_style'] = document.getElementById('asl-map_layout_custom').value;

        //  filter_ddl
        var filter_ddl = $('#asl-filter_ddl').val();
        all_data['data[filter_ddl]'] = (filter_ddl && filter_ddl.length)? filter_ddl.join(','): '';

        // Store form fields ordering/visibility
        all_data['store_form_fields'] = $('#asl-store-form-fields-input').val();
        all_data['bulk_edit_fields'] = $('#asl-bulk-edit-fields-input').val();

        //  slug_attr_ddl
        all_data['slug_attr_ddl'] = (ddl_slug_values && ddl_slug_values.length)? ddl_slug_values.join(','): '';


        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=save_setting', all_data, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

        }, 'json');
      });

      //  Reset Slug button
      $('#btn-asl-slug_reset').on('click', function(e) {
        var $btn = $(this);
        //  Send an AJAX Request
        $btn.bootButton('loading');
        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=reset_all_slugs', {}, function(_response) {
          $btn.bootButton('reset');
          toastIt(_response);
        }, 'json');
      });

      /////////////////////////
      //  Create TMPL Editor //
      /////////////////////////
      
      wp.codeEditor.initialize($('#sl-custom-template-textarea'), null);

      var $section_tmpl_select = $('#asl-customize-section'),
          $template_select     = $('#asl-customize-template'),
          $load_ctemp_btn      = $('#btn-asl-load_ctemp'),
          $save_ctemp_btn      = $('#btn-asl-save_ctemp'),
          $reset_ctemp_btn     = $('#btn-asl-reset_ctemp'),
          loaded_template      = null,
          loaded_section       = null;

      function reset_customizer_loaded_state() {
        loaded_template = null;
        loaded_section  = null;
        $save_ctemp_btn.prop('disabled', true);
        $reset_ctemp_btn.prop('disabled', true);
      }

      function mark_customizer_loaded(template, section) {
        loaded_template = template;
        loaded_section  = section;
        $save_ctemp_btn.prop('disabled', false);
        $reset_ctemp_btn.prop('disabled', false);
      }

      function is_customizer_loaded(template, section) {
        return loaded_template !== null && loaded_section !== null &&
               loaded_template === template && loaded_section === section;
      }

      //  Disable save/reset until a template is loaded
      reset_customizer_loaded_state();
      
      //  Template List doesn't have Infobox
      $template_select.on('change', function(e) {

        var customizer_options = ASL_Instance.tmpls[e.target.value];
          
        //  Clear old values
        $section_tmpl_select.empty();

        if(customizer_options) {

          $.each(customizer_options.options, function(index, option) {
            
            const $optionElement = $("<option>").val(option.value).text(option.label);

            if (option.disable) {
              $optionElement.prop("disabled", true);
            }

            $section_tmpl_select.append($optionElement);
          });
        }

        reset_customizer_loaded_state();
      });

      $section_tmpl_select.on('change', function(e) {
        reset_customizer_loaded_state();
      });

      //  Load Template button Event
      $load_ctemp_btn.on('click', function(e) {

        var $btn = $(this);

        $btn.bootButton('loading');

        var template    = $('#asl-customize-template').val(),
            section     = $('#asl-customize-section').val();


        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=load_custom_template', {template: template , section: section}, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

          if (_response.success) {

            document.querySelector('.sl-custom-tpl-text-section .CodeMirror').CodeMirror.setValue(_response.html);
            mark_customizer_loaded(template, section);
            return;
          }

          reset_customizer_loaded_state();

        }, 'json');
      });

      // load Custom template
      $('#btn-asl-save_ctemp').on('click', function(e) {

        var $btn = $(this);

        $btn.bootButton('loading');

        
        var template    = $('#asl-customize-template').val(),
            section     = $('#asl-customize-section').val(),
            html        = document.querySelector('.sl-custom-tpl-text-section .CodeMirror').CodeMirror.getValue();

        if(template == undefined || section == undefined || html == '' || html == null){

          atoastr.error('please Load template');
          $btn.bootButton('reset');
          return;
        }

        if(!is_customizer_loaded(template, section)) {
          atoastr.error('Please load template first');
          $btn.bootButton('reset');
          return;
        }

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=save_custom_template', {template: template , section: section,html: html}, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

        }, 'json');
      });


      // Reset Custom template
      $('#btn-asl-reset_ctemp').on('click', function(e) {

        var $btn = $(this);

        $btn.bootButton('loading');

        var template    = $('#asl-customize-template').val(),
            section     = $('#asl-customize-section').val();

        if(template == undefined || section == undefined){

          atoastr.error('Please load template');
          $btn.bootButton('reset');
          return;
        }

        if(!is_customizer_loaded(template, section)) {
          atoastr.error('Please load template first');
          $btn.bootButton('reset');
          return;
        }

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=reset_custom_template', {template: template , section: section}, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

          if (_response.success) {
            document.querySelector('.sl-custom-tpl-text-section .CodeMirror').CodeMirror.setValue(_response.html);
            return;
          }
        }, 'json');
      });

      //  Save the save settings for the customizer
      $('.asl-tabs a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        
        if(e.target.getAttribute('href') == '#sl-customizer' || e.target.getAttribute('href') == '#sl-labels') {

          $('.asl-btn-setting-main').addClass('hide');
        }
        else if(e.relatedTarget.getAttribute('href') == '#sl-customizer' || e.relatedTarget.getAttribute('href') == '#sl-labels') {
          $('.asl-btn-setting-main').removeClass('hide');
        }
      });



      if (isEmpty(_configs['template']))
        _configs['template'] = '0';

      //  Show the option of right template
      $('.box_layout_' + _configs['template']).removeClass('hide');

      $('.asl-p-cont #asl-layout').on('change', function(e) {

        set_tmpl_image();

      });

      //  Bind Change Template
      $('.asl-p-cont #asl-template').on('change', function(e) {

        var _value = this.value;
        $('.asl-p-cont .template-box').addClass('hide');
        $('.box_layout_' + _value).removeClass('hide');

        set_tmpl_image();

      });

      set_tmpl_image();

      ////////////////////////////////////////
      // Code for the Additional attributes //
      ////////////////////////////////////////
      $('#btn-asl-add-field').on('click', function(e) {
          
        const field_uniq_id = generateUniqueId();


        var $new_slot = $('<tr class="asl-custom-field-row">\
                            <td>\
                              <div class="form-group mb-2"><input type="text" aria-label="Field Label" class="asl-attr-label form-control validate[required,funcCall[ASLValidateLabel]]"></div>\
                              <div class="asl-field-choices d-none mb-2"><label class="small font-weight-bold">Choices</label><input type="text" placeholder="Example: Small, Medium, Large" class="asl-attr-options form-control validate[funcCall[ASLValidateOptions]]"><small class="form-text text-muted">Separate each choice with a comma.</small></div>\
                              <details class="asl-field-advanced"><summary>Advanced settings</summary>\
                                <div class="form-group mt-2 mb-2"><label class="small font-weight-bold">Internal Field Name</label><input type="text" data-auto-name="1" class="asl-attr-name form-control validate[required,funcCall[ASLValidateName]]"><small class="form-text text-muted">Generated automatically from the label.</small></div>\
                                <div class="form-group mb-2"><label class="small font-weight-bold">CSS Class</label><input maxlength="50" type="text" class="asl-attr-class form-control"></div>\
                              </details>\
                            </td>\
                            <td><div class="form-group"><select class="form-control asl-attr-type"><option value="text">Text</option><option value="textarea">Textarea</option><option value="richtext">Rich Textarea</option><option value="dropdown">Dropdown</option><option value="radio">Radio List</option><option value="checkbox">Checkbox</option><option value="gallery">Gallery</option><option value="page_link">Internal Page Link</option></select></div></td>\
                            <td><div class="form-group"><select class="form-control asl-attr-section"><option value="other">Other Details tab</option><option value="address">Store Address tab</option></select><small class="form-text text-muted">Choose where this field appears when editing a store.</small></div></td>\
                            <td><div class="form-group-inner mt-2 d-flex align-items-center"><label class="switch" for="asl-cf-req-'+field_uniq_id+'"><input type="checkbox" value="1" class="asl-attr-require custom-control-input" id="asl-cf-req-'+field_uniq_id+'"><span class="slider round"></span></label><span class="asl-required-status ml-2">No</span></div></td>\
                            <td><button type="button" class="btn btn-link text-danger add-k-delete glyp-trash" title="Remove field"><svg width="16" height="16"><use xlink:href="#i-trash"></use></svg><span>Remove</span></button></td>\
                          </tr>');
        
        var $cur_slot = $('.asl-attr-manage tbody').append($new_slot);
      });


      //  Delete current field
      $('.asl-attr-manage tbody').on('click', '.add-k-delete', function(e) {
        var field_label = $(this).closest('tr').find('.asl-attr-label').val() || 'this field';
        if (window.confirm('Remove "' + field_label + '"? Existing store values for this field will no longer be available in the form.')) {
          $(this).closest('tr').remove();
        }
      });

      //  Text will have it locked
      $('.asl-attr-manage tbody').on('change', '.asl-attr-type', function(e) {


        var $this_tr       = $(this).closest('tr'),
            $option_field  = $this_tr.find('.asl-attr-options'),
            $choices_group = $this_tr.find('.asl-field-choices');


        if (['dropdown', 'radio'].includes(this.value)) {
          $option_field.removeAttr('readonly','true');
          $choices_group.removeClass('d-none');
        }
        else {
          $option_field.attr('readonly', 'true');
          $option_field.val('');
          $choices_group.addClass('d-none');
        }

      });

      // Generate an internal name once for a new field. Label edits never
      // update an internal name that already has a value.
      $('.asl-attr-manage tbody').on('change', '.asl-attr-label', function() {
        var $name = $(this).closest('tr').find('.asl-attr-name');
        if ($name.attr('data-auto-name') === '1' && !$name.val()) {
          $name.val($(this).val().toLowerCase().trim().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, ''));
          $name.attr('data-auto-name', '0');
        }
      });

      $('.asl-attr-manage tbody').on('input', '.asl-attr-name', function() {
        $(this).attr('data-auto-name', '0');
      });

      $('.asl-attr-manage tbody').on('change', '.asl-attr-require', function() {
        $(this).closest('td').find('.asl-required-status').text(this.checked ? 'Yes' : 'No');
      });


      var custom_fields = {};

      var $field_form   = $('#frm-asl-custom-fields');
      
      $field_form.validationEngine({
        binded: true,
        scroll: false
      });


      //  Save Event for the Fields
      $('#btn-asl-save-schema').on('click', function(e) {

        $('.asl-custom-field-row').each(function() {
          var $row = $(this),
              $name = $row.find('.asl-attr-name');

          if (!$name.val()) {
            $name.val($row.find('.asl-attr-label').val().toLowerCase().trim().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, ''));
            $name.attr('data-auto-name', '0');
          }
        });

        if (!$field_form.validationEngine('validate')) return;

        var $btn = $(this);

        custom_fields = {};

        //  Capture fields data
        var $fields_tr = $('.asl-attr-manage tbody tr');
        $fields_tr.each(function(i) {

            var $tr           = $(this),
                field_label   = $tr.find('.asl-attr-label').val(), 
                field_name    = $tr.find('.asl-attr-name').val(),
                field_type    = $tr.find('.asl-attr-type').val(),
                field_options = $tr.find('.asl-attr-options').val(),
                field_section = $tr.find('.asl-attr-section').val(),
                css_class     = $tr.find('.asl-attr-class').val(),
                field_require = ($tr.find('.asl-attr-require')[0].checked)? 1: 0;

            custom_fields[field_name] = {name: field_name, label: field_label, type: field_type, options: field_options, require: field_require, section: field_section, css_class: css_class };
        });

        //  Send an AJAX Request
        $btn.bootButton('loading');

        
        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=save_custom_fields', {fields: custom_fields}, function(_response) {

          $btn.bootButton('reset');

          if (_response && _response.success) {
            $('.asl-attr-name').attr('data-auto-name', '0');
          }

          toastIt(_response);

        }, 'json');
      });

      // Validate Label
      window['ASLValidateLabel'] = function(field, rules, i, options) {
      };

      window['ASLValidateOptions'] = function(field, rules, i, options) {
      };

      

      // Validate Name
      var reg   = new RegExp(/^[a-z0-9\-\_]+$/);
      window['ASLValidateName'] = function(field, rules, i, options) {

        var _value = field.val();

        if(['id','title','phone','email','street','city','state','country','postal_code','marker_id','logo_id','description','description_2','open_hours','pending','distance','target'].indexOf(_value) != -1) {
          return '* Keyword';
        }

        if(!reg.test(_value)) {
          return '* Invalid';
        }
      };


      /////////////////////////
      /// The Cache Switches //
      /////////////////////////

      var $cache_form = $('#frm-asl-cache');


      /**
       * [update_cache description]
       * @param  {[type]} _status [description]
       * @param  {[type]} _lang   [description]
       * @return {[type]}         [description]
       */
      function update_cache(_status, _lang, _callback) {

        var cache_data = $cache_form.ASLSerializeObject();
        
        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=cache_status", {status: _status, 'asl-lang': _lang, 'content': cache_data, 'stype': 'cache'}, function(_response) {

          toastIt(_response);

          if(_callback) {
            _callback(_response);
          }

        }, 'json');
      }

      //  Cache Switch Event
      $cache_form.find('input[type=checkbox]').on('change', function(e) {

        var chk_ctrl = e.target,
            lang     = chk_ctrl.dataset.lang,
            status   = (chk_ctrl.checked)? '1': '0';

        update_cache(status, lang);
      });

      //  Cache Refresh Event
      $cache_form.find('.sl-refresh-cache').on('click', function(e) {

        var $btn     = $(this),
            lang     = $btn.data('lang'),
            status   = '1';
            
        $btn.bootButton('loading');

        update_cache(status, lang, function(){

          $btn.bootButton('reset');
        });
      });



      ///////////////////
      // The Map Modal //
      ///////////////////
      
      window['asl_map_intialized'] = function() {

        map_object.render_a_map(asl_configs.default_lat, asl_configs.default_lng);
      };

      //init the maps
      if (!(window['google'] && google.maps)) {
        map_object.intialize();
        //drawing_instance.initialize();
      } 
      else
        asl_map_intialized();


      //  Add the click event to copy coordinates and Zoom
      $('#asl-setting-set-coordinates').on('click', function(e) {

          if(map_object.map_marker) {

            var markerPosition = map_object.map_marker.getPosition(),
                markerLat = (markerPosition && typeof markerPosition.lat === 'function') ? markerPosition.lat() : markerPosition.lat,
                markerLng = (markerPosition && typeof markerPosition.lng === 'function') ? markerPosition.lng() : markerPosition.lng,
                mapZoom = Number(map_object.map_instance.getZoom()),
                $zoomSelect = $('#asl-zoom'),
                selectedZoom = null,
                selectedZoomDistance = Infinity;

            // Map providers can return a fractional zoom, while the settings field
            // contains whole-number options. Select the closest available option.
            if (Number.isFinite(mapZoom)) {
              $zoomSelect.find('option').each(function() {
                var optionZoom = Number(this.value),
                    optionDistance = Math.abs(optionZoom - mapZoom);

                if (Number.isFinite(optionZoom) && optionDistance < selectedZoomDistance) {
                  selectedZoom = this.value;
                  selectedZoomDistance = optionDistance;
                }
              });
            }

            //  set coordinates
            $('#asl-default_lat').val(markerLat);
            $('#asl-default_lng').val(markerLng);

            if (selectedZoom !== null) {
              $zoomSelect.val(selectedZoom).trigger('change').trigger('chosen:updated');
            }

            //  hide the modal
            $('#asl-map-modal').smodal('hide');

            //  show the toaster
            atoastr.warning(ASL_REMOTE.LANG.warn_save_setting);
          }
      });

      ////////////////////////////
      //  Show/Hide the Columns //
      ////////////////////////////
      $('#sl-btn-sh').on('click', function(e) {

        var sh_columns = $('#ddl-fs-cntrl').val();
        var $btn       = $(this);

        $btn.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=change_options", {'content': sh_columns, 'stype': 'hidden'}, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

          if (_response.success) {

            $('#sl-fields-sh').smodal('hide');
            window.location.reload();
          }

        }, 'json');
      });


      //  FAQ
      $('#accordionfaqs .btn.btn-link').on('click', function(e) {

        var $faq_btn = $(this);

        $faq_btn.toggleClass('collapsed');
        $faq_btn.parent().parent().next().toggleClass('show');
      }); 

      // Lazy Load asl-wc videos
      $('#sl-wc video').each(function(i){

        var video = this;
        
        for (var source in video.children) {
          if(!video.children.hasOwnProperty(source)) continue;
          var videoSource = video.children[source];
          if (typeof videoSource.tagName === "string" && videoSource.tagName === "SOURCE") {
            videoSource.src = videoSource.dataset.src;
          }
        }

        video.load();
      });

      ////////////////////////////
      // Export/Import Settings //
      ////////////////////////////

      // Export Config Event
      $('#asl-btn-export-config').on('click', function(e){

        var $btn = $(this);

        $btn.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=export_configs', {}, function(_response) {

          $btn.bootButton('reset');

          var config_text = JSON.stringify(_response.configs);

          aswal({
            title: ASL_REMOTE.LANG.export_config || 'Exported Configuration',
            html: '<span class="asl-red">' + _response.export_text_content + '</span>'+ '<textarea id="asl-export-config-textarea" rows="10" style="width:100%" readonly="true">' + config_text + '</textarea>',
            showCancelButton: true,
            confirmButtonText: ASL_REMOTE.LANG.copy || 'Copy',
            cancelButtonText: ASL_REMOTE.LANG.close || 'Close',
            showLoaderOnConfirm: true,
            preConfirm: function(_value) {
  
              return new Promise(function(resolve, reject) {

                // Copy JSON text to clipboard
                var jsonTextarea = document.getElementById('asl-export-config-textarea');
                jsonTextarea.select();
                var result = document.execCommand('copy');

                if(result) {
                  toastIt({success: true, message: _response.copy_message});
                }

                resolve();

              })
            }
          })
          .catch(aswal.noop);

        }, 'json');
      });

      // Import Config Event
      $('#asl-btn-import-config').on('click', function(e){

        aswal({
          type: 'warning',
          input: "textarea",
          html: '<span class="asl-red">' + ASL_REMOTE.LANG.import_config_warn + '</span>',
          title: ASL_REMOTE.LANG.import_config || 'Import Configuration',
          inputPlaceholder: ASL_REMOTE.LANG.paste_config_ph,
          inputAttributes: {
            "aria-label": ASL_REMOTE.LANG.paste_config_ph
          },
          confirmButtonText: ASL_REMOTE.LANG.import || 'Import',
          showCancelButton: true,
          confirmButtonColor: "#dc3545",        
          showLoaderOnConfirm: true,
          preConfirm: function(_value) {
  
            return new Promise(function(resolve, reject) {

              if(!_value) {

                  aswal.showValidationError(ASL_REMOTE.LANG.error_try_again);
                  reject();
                  return false;
              }

              //  Save the configuration
              ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=import_configs', {configs: _value}, function(_response) {

                if (!_response.success) {

                  aswal.showValidationError(_response.message);
                  reject();
                  return false;
                }
                else {

                  toastIt(_response);

                  //  Refresh to reload
                  window.location.replace(ASL_REMOTE.URL.replace('-ajax', '') + "?page=asl-settings");

                  reject();
                  return true;
                }
              });

            })
          }
        })
        .catch(aswal.noop);
      
      });
    },
    /**
     * [ui_template User Settings]
     * @param  {[type]} _configs [description]
     * @return {[type]}          [description]
     */
    ui_template: function(_configs) {

      var $form      = $('#frm-asl-ui-customizer');
      var $loadBtn   = $('#btn-asl-load_uitemp');
      var $saveBtn   = $('#btn-asl-save_uitemp');
      var $resetBtn  = $('#btn-asl-reset_uitemp');

      var disableTemplateActions = function() {

        $saveBtn.bootButton('reset');
        $resetBtn.bootButton('reset');

        $saveBtn.addClass('disabled').prop('disabled', true).removeAttr('data-template-name');
        $resetBtn.addClass('disabled').prop('disabled', true).removeAttr('data-template-name');
      };

      disableTemplateActions();

      ////////////////////////////////////
      //  Load UI Template button Event //
      ////////////////////////////////////
      $loadBtn.on('click', function(e) {

        var $btn = $(this);

        $btn.bootButton('loading');

        disableTemplateActions();

        var template = $('#asl-ui-template').val();

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=load_ui_settings', {template: template}, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

          if (_response.success) {
            $($form).find('#asl-fields-section').html('');
            $($form).find('#asl-fields-section').append(_response.html);

            $('#asl-fields-section').show();

            $saveBtn.attr({'data-template-name': template}).removeClass('disabled').prop('disabled', false);
            $resetBtn.attr({'data-template-name': template}).removeClass('disabled').prop('disabled', false);

            return;
          }


        }, 'json');
      });


      //////////////////////
      // Save UI template //
      //////////////////////
      $saveBtn.on('click', function(e) {

        var $btn      = $(this);
        var formData  = $form.ASLSerializeObject();

        var template  = $(this).attr('data-template-name');

        if(template == '' || template == null){
            atoastr.error('Load Template first');
            return;
        }

        $btn.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=sl_theme_ui_save', {sl_template: template,sl_formData: formData}, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

        }, 'json');
      });


      ////////////////////////
      // Reset UI template  //
      ////////////////////////
      $resetBtn.on('click', function(e) {

        var template = $(this).attr('data-template-name');

        if(template == '' || template == null){
            atoastr.error('Load Template first');
            return;
        }

        aswal({
          title: (ASL_REMOTE.LANG.reset_template_title || 'Reset Template?'),
          text: (ASL_REMOTE.LANG.reset_template_text || 'This will delete the saved settings for the selected template.'),
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          confirmButtonText: (ASL_REMOTE.LANG.reset_template_confirm || 'Yes, reset it!')
        }).then(function(result) {

          if(result) {

            $resetBtn.bootButton('loading');

            ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=reset_ui_template', {template: template}, function(_response) {

              $resetBtn.bootButton('reset');

              toastIt(_response);

              if (_response.success) {

                disableTemplateActions();
                $('#asl-fields-section').html('').hide();

                $loadBtn.trigger('click');
              }

            }, 'json');
          }
        });
      });

      // Helper: Lighten a hex color
      function lightenColor(hex, percent) {
          hex = hex.replace('#', '');
          if (hex.length === 3) {
              hex = hex.split('').map(c => c + c).join('');
          }

          let r = parseInt(hex.substring(0, 2), 16);
          let g = parseInt(hex.substring(2, 4), 16);
          let b = parseInt(hex.substring(4, 6), 16);

          r = Math.min(255, Math.floor(r + (255 - r) * (percent / 100)));
          g = Math.min(255, Math.floor(g + (255 - g) * (percent / 100)));
          b = Math.min(255, Math.floor(b + (255 - b) * (percent / 100)));

          return "#" + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
      }

      // Change Copy Colors
      $('#frm-asl-ui-customizer').on('change','.clr-primary',function(){
        
        let primary = $(this).val();

        // Apply to all "clr-copy"
        $('.clr-copy').val(primary).change();

        console.log(`File: jscript.js, Line: 4392`, primary);
        // Apply to all "light-XX"
        $('[class*="light-"]').each(function () {
          
            let classes = $(this).attr('class').split(/\s+/);
            let lightClass = classes.find(c => c.startsWith('light-'));
            if (lightClass) {
                let percent = parseInt(lightClass.split('-')[1], 10);
                let newColor = lightenColor(primary, percent);
                console.log(`File: jscript.js, Line: newColor: `, newColor);
                $(this).val(newColor).change();
            }
        });
      }); 

      // Change Copy Colors
      $('#frm-asl-ui-customizer').on('change','.clr-primary',function(){
        var value = $(this).val();
        $('.clr-copy').val(value).change();
      }); 

      // Change Values For Color Picker
      $('#frm-asl-ui-customizer').on('change','.colorpicker',function(){
          var value = $(this).val();
          $(this).parents('.color-row').find('.hexcolor').val(value);
      });

      //  keyPress 
      $('#frm-asl-ui-customizer').on('keyup','.hexcolor',function(){
          var value = $(this).val();
          $(this).parents('.color-row').find('.colorpicker').val(value);
      });
    },
    /**
     * [import_store description]
     * @return {[type]} [description]
     */
    import_store: function() {

      //  Validate the Plugin
      this._validate_page();

      /*Validate API Key*/
      $('#btn-validate-key').on('click', function(e) {

        var $this = $(this);

        $this.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=validate_api_key', {}, function(_response) {

          $this.bootButton('reset');

          toastIt(_response);

        }, 'json');

      });


      /*Fetch the Missing Coordinates*/
      $('#btn-fetch-miss-coords').on('click', function(e) {

        var $this = $(this);
        $this.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=fill_missing_coords', {}, function(_response) {

          $this.bootButton('reset');

          toastIt(_response);

          if (_response.success) {

            // making summary           
            var warning_summary = "<ul>";

            for (var _s in _response.summary) {

              warning_summary += "<li>" + _response.summary[_s] + "</li>";
            }

            warning_summary += '</ul>';

            $('#message_complete').html("<div class='alert alert-info'>" + warning_summary + "</div>");
            return;
          }


        }, 'json');

      });

      /*Delete Stores*/
      var _delete_all_stores = function() {

        var $this = $('#asl-delete-stores');
        $this.bootButton('loading');

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=delete_all_stores', {}, function(_response) {

          $this.bootButton('reset');
          toastIt(_response);
        }, 'json');
      };


      /*Delete All stores*/
      $('#asl-delete-stores').on('click', function(e) {

        aswal({
          title: ASL_REMOTE.LANG.truncate_stores,
          text: ASL_REMOTE.LANG.truncate_stores_text,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#dc3545",
          confirmButtonText: ASL_REMOTE.LANG.delete_all
        }).then(
          function() {

            _delete_all_stores();
          }
        );
      });


      //import store form xlsx file
      $('.btn-asl-import_store').on('click', function(e) {

        var $this = $(this);
        $this.bootButton('loading');

        var _params = {data_: $(this).attr('data-id'), duplicates: $('#sl-duplicates-data').val()};

        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=import_store', _params, function(_response) {

            $this.bootButton('reset');
            
            if (_response.summary) {

              // making summary           
              var warning_summary = "<ul>";

              for (var _s in _response.summary) {

                warning_summary += "<li>" + _response.summary[_s] + "</li>";
              }

              warning_summary += '</ul>';

              var _color = (_response.success && (_response.imported_rows || _response.stores_deleted)) ? 'success': 'error';

              var import_message = _response.imported_rows + " Rows Import" + ((_response.error)? ('<br>'+ _response.error): '');

              //  Stores Deleted
              if(_response.stores_deleted) {
                import_message += '<br> ' + _response.stores_deleted + ' Rows Deleted';
              }

              atoastr[_color](import_message);
              
              if(warning_summary)
                $('#message_complete').html("<div class='alert alert-warning'>" + warning_summary + "</div>");
              
              return;
            }
          },
          'json',
          function(_error) {

            $this.bootButton('reset');
            _error = (_error && _error.responseText) ? 'Error in import, contact us at support@agilelogix.com, ' + _error.responseText : 'Error in import, contact us at support@agilelogix.com';
            atoastr['error'](_error);

          });

      });

      //delete import file
      $('.btn-asl-delete_import_file').on('click', function(e) {


        ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=delete_import_file', { data_: $(this).attr('data-id') }, function(_response) {

          toastIt(_response);

          if (_response.success) {
            window.location.replace(ASL_REMOTE.URL.replace('-ajax', '') + "?page=import-store-list");
            return;
          }
        }, 'json');

      });

      //Remove the Duplicates
      $('#asl-duplicate-remove').on('click', function(e) {

        aswal({
          title: ASL_REMOTE.LANG.remove_duplicates,
          text: ASL_REMOTE.LANG.remove_duplicates_text,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#dc3545",
          confirmButtonText: ASL_REMOTE.LANG.yes_remove
        }).then(function() {

          ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=remove_duplicates", {}, function(_response) {

            toastIt(_response);

          }, 'json');
        });
      });

      //export file


      $('#export_store_file_').on('click', function(e) {

        var with_logo = (document.getElementById('asl-logo-images').checked) ? 1 : 0,
            with_id   = (document.getElementById('asl-export-ids').checked) ? 1 : 0 ;
        
        window.location.href = ASL_Instance.admin + '&logo_image=' + with_logo + '&with_id=' + with_id;
      });

      //upload import file
      var url_to_upload = ASL_REMOTE.URL,
          $form_upload  = $('#import_store_file');

      app_engine.uploader($form_upload, url_to_upload + '?action=asl_ajax_handler&asl-nounce=' + ASL_REMOTE.nounce + '&sl-action=upload_store_import_file', function(_e, _data) {

        var data = _data.result;

        toastIt(data);

        if (data.success) {

          $('#import_store_file_emodel').smodal('hide');
          $('#progress_bar_').hide();
          $('#frm-upload-logo').find('input:text, input:file').val('');
          window.location.replace(ASL_REMOTE.URL.replace('-ajax', '') + "?page=import-store-list");
        }
      });
    },

    /**
    * [labels description]
    * @return {[type]} [description]
    */
    labels: function() {

      // Get all label elements
      var labels = $('.asl-label-section .asl-label');


      // Listen for keyup event on search input
      $('#label-search').on('keyup', function() {
        
        var searchTerm = $(this).val().toLowerCase();

        // Filter out labels that don't match search term
        labels.each(function() {

          var lbl_cont = $(this);

          var label_input   = lbl_cont.find('input').val().toLowerCase(),
              label_default = lbl_cont.find('label').text().toLowerCase();

          if (label_input.indexOf(searchTerm) === -1 && label_default.indexOf(searchTerm) === -1) {
            lbl_cont.hide();
          } 
          else {
            lbl_cont.show();
          }
        });

        // If Search input not match
        if($('.asl-label:visible').length == 0){
            $(".no_result").css("display", "block");
        } 
        else {
            $(".no_result").css("display", "none");
        }
      });


      // save labels in database
      $('.asl-label input').on('change', function(e) {
      // $('.asl-label input').blur(function(){

        var $btn = $(this);
        var _key  = $(this).attr('data-name'),
            value = $(this).val();
        
           // Empty value prevent
        if (isEmpty(value)) {

            atoastr.error('The field cannot be left empty. Please enter a label text.');
            return false;

        }

        $btn.bootButton('loading');
        

        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=set_label", { _key : _key, value : value}, function(_response) {

          $btn.bootButton('reset');

          toastIt(_response);

        }, 'json');

      });
    }
  };

  $(document).on('click', '#sl-btn-tmpl-backup', function() {
    aswal({
      title: ASL_REMOTE.LANG.backup_tmpl,
      type: 'question',
      html: '<p>' + ASL_REMOTE.LANG.backup_tmpl_msg + '</p><select class="custom-select" id="sl-tmpl-section-1"><option value="0">Template 0</option><option value="form">Store Form</option><option value="store">Store Detail</option><option value="search">Search Widget</option><option value="lead">Lead Form</option><option value="grid">Store Grid</option></select>',
      showCancelButton: true,
      confirmButtonText: ASL_REMOTE.LANG.backup,
      preConfirm: function() {
        return new Promise(function(resolve) {
          aswal.showLoading();
          ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=backup_tmpl', {template: $('#sl-tmpl-section-1').val()}, function(response) {
            aswal.close();
            toastIt(response);
            resolve(response);
            if (response.success) {
              setTimeout(function() { window.location.reload(); }, 700);
            }
          }, 'json');
        });
      }
    });
  });

  $(document).on('click', '#sl-btn-tmpl-remove', function() {
    aswal({
      title: ASL_REMOTE.LANG.remove_tmpl,
      type: 'question',
      html: '<p>' + ASL_REMOTE.LANG.remove_tmpl_msg + '</p><select class="custom-select" id="sl-tmpl-section-2"><option value="0">Template 0</option><option value="form">Store Form</option><option value="store">Store Detail</option><option value="search">Search Widget</option><option value="lead">Lead Form</option><option value="grid">Store Grid</option></select>',
      showCancelButton: true,
      confirmButtonText: ASL_REMOTE.LANG.remove,
      preConfirm: function() {
        return new Promise(function(resolve) {
          aswal.showLoading();
          ServerCall(ASL_REMOTE.URL + '?action=asl_ajax_handler&sl-action=remove_tmpl', {template: $('#sl-tmpl-section-2').val()}, function(response) {
            aswal.close();
            toastIt(response);
            resolve(response);
            if (response.success) {
              setTimeout(function() { window.location.reload(); }, 700);
            }
          }, 'json');
        });
      }
    });
  });

  //<p class="message alert alert-danger static" style="display: block;">Legal Location not found<button data-dismiss="alert" class="close" type="button"> ×</button><span class="block-arrow bottom"><span></span></span></p>
  //if jquery is defined
  if ($)
    $('.asl-p-cont').append('<div class="loading site hide">Working ...</div><div class="asl-dumper dump-message"></div>');

})(jQuery, asl_engine);
