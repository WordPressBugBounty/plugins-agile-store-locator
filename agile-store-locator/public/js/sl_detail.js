(function( $ ) {

  'use strict';

  
  /**
   * [store-detail description]
   * @param  {[type]} _options [description]
   * @return {[type]}          [description]
   */
  $.fn.asl_store_detail = function(_options) {

    /**
     * [store_method The main method of the store detail widget]
     * @return {[type]} [description]
     */
    function store_method() {

      var $container = $(this),
          $map_div   = $container.find('.asl-detail-map');

      //  Map div must exist!
      if(!$map_div[0]) {
        return;
      }

      var detail_config = $container.data('config');


      if(!window.ASLCommonMap || !detail_config)return;

      var asl_lat  = (detail_config.default_lat) ? parseFloat(detail_config.default_lat) : 39.9217698526,
          asl_lng  = (detail_config.default_lng) ? parseFloat(detail_config.default_lng) : -75.5718432,
          location = {lat: asl_lat, lng: asl_lng},
          map_style = null;

      if (detail_config.map_layout && detail_config.map_vendor !== 'maplibre') {
        try { map_style = JSON.parse(detail_config.map_layout); } catch (error) { map_style = null; }
      }

      var map = ASLCommonMap.create($map_div[0], {
        config: detail_config,
        center: location,
        zoom: parseInt(detail_config.zoom),
        scrollwheel: detail_config.scroll_wheel,
        gestureHandling: detail_config.gesture_handling || 'cooperative',
        zoomControl: detail_config.zoomcontrol !== 'false',
        mapTypeControl: detail_config.maptypecontrol !== 'false',
        scaleControl: detail_config.scalecontrol !== 'false',
        rotateControl: detail_config.rotatecontrol !== 'false',
        fullscreenControl: detail_config.fullscreencontrol !== 'false',
        streetViewControl: detail_config.streetviewcontrol !== 'false',
        styles: map_style
      });

      map.addMarker({
        position: location,
        title: detail_config.store_title || '',
        icon: detail_config.URL + 'icon/' + (detail_config.icon || 'default.png')
      });
    };

    /*loop for each*/
    this.each(store_method);

    return this;
  };


  //  ASL GDPR Borlabs Callback
  window.asl_gdpr = function() {
    $('.asl-cont.asl-store-pg').asl_store_detail();
  };

  // Run the widget script
  /* aslInitializeWhenGAPIReady(function(){
    
    $('.asl-cont.asl-store-pg').asl_store_detail();
  }); */

  $('.asl-cont.asl-store-pg').asl_store_detail();

  function initStoreGallery() {
    document.querySelectorAll('.asl-store-pg .asl-gallery-toggle').forEach(function(toggle) {
      toggle.addEventListener('click', function() {
        var storePage = toggle.closest('.asl-store-pg');
        if (!storePage) {
          return;
        }

        storePage.querySelectorAll('.asl-gallery-extra[hidden]').forEach(function(photo) {
          photo.hidden = false;
        });

        toggle.setAttribute('aria-expanded', 'true');
        toggle.hidden = true;
      });
    });
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    initStoreGallery();
  } else {
    document.addEventListener('DOMContentLoaded', initStoreGallery);
  }

}( jQuery ));
