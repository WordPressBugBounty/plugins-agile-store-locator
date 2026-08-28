var asl_drawing = {};

(function($) {
  'use strict';

  var defaultColor = '#CC3333';

  function latLngEquals(first, second) {
    return first && second && Math.abs(first.lat() - second.lat()) < 0.0000001 && Math.abs(first.lng() - second.lng()) < 0.0000001;
  }

  function distanceBetween(first, second) {
    var earthRadius = 6371000;
    var lat1 = first.lat() * Math.PI / 180;
    var lat2 = second.lat() * Math.PI / 180;
    var deltaLat = (second.lat() - first.lat()) * Math.PI / 180;
    var deltaLng = (second.lng() - first.lng()) * Math.PI / 180;
    var value = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) +
      Math.cos(lat1) * Math.cos(lat2) * Math.sin(deltaLng / 2) * Math.sin(deltaLng / 2);

    return earthRadius * 2 * Math.atan2(Math.sqrt(value), Math.sqrt(1 - value));
  }

  asl_drawing = {
    shapes: [],
    markers: [],
    current_map: null,
    selectedShape: null,
    selectedColor: defaultColor,
    fillEnabled: true,
    activeMode: null,
    draftOverlay: null,
    draftPoints: [],
    startPoint: null,
    mapListeners: [],
    shapeId: 0,

    initialize: function(map, noDrawing) {
      this.current_map = map;

      if (noDrawing) {
        return;
      }

      this.bindControls();
      this.bindMapEvents();
    },

    bindControls: function() {
      var that = this;

      $('.asl-drawing-tool').off('.aslDrawing').on('click.aslDrawing', function() {
        var mode = $(this).data('drawing-mode');
        that.setMode(that.activeMode === mode ? null : mode);
      });

      $('.asl-p-cont .color_scheme input').off('.aslDrawing').on('change.aslDrawing', function() {
        that.selectColor(this.value);
        that.setSelectedShapeColor(this.value);
        that.notifyChange();
      });

      $('#asl-fill-option button').off('.aslDrawing').on('click.aslDrawing', function() {
        that.setFill($(this).data('value') === 1);
        that.notifyChange();
      });

      $('#asl-delete-shape').off('.aslDrawing').on('click.aslDrawing', function() {
        that.deleteSelectedShape();
      });

      $('#asl-clear-all').off('.aslDrawing').on('click.aslDrawing', function() {
        var message = window.asl_customize_map_l10n && asl_customize_map_l10n.confirm_clear;
        if (!that.shapes.length || window.confirm(message || 'Remove all drawn shapes?')) {
          that.clearAll();
        }
      });

      $('#asl-finish-drawing').off('.aslDrawing').on('click.aslDrawing', function() {
        that.finishPath();
      });

      $('#asl-cancel-drawing').off('.aslDrawing').on('click.aslDrawing', function() {
        that.setMode(null);
      });
    },

    bindMapEvents: function() {
      var that = this;

      this.removeMapListeners();
      this.mapListeners.push(google.maps.event.addListener(this.current_map, 'click', function(event) {
        that.handleMapClick(event.latLng);
      }));
      this.mapListeners.push(google.maps.event.addListener(this.current_map, 'mousemove', function(event) {
        that.handlePointerMove(event.latLng);
      }));
      this.mapListeners.push(google.maps.event.addListener(this.current_map, 'dblclick', function(event) {
        if (event.domEvent && event.domEvent.preventDefault) {
          event.domEvent.preventDefault();
        }
        that.finishPath();
      }));
    },

    removeMapListeners: function() {
      this.mapListeners.forEach(function(listener) {
        google.maps.event.removeListener(listener);
      });
      this.mapListeners = [];
    },

    setMode: function(mode) {
      this.cancelDraft();
      if (mode) {
        this.clearSelection();
      }
      this.activeMode = mode;

      $('.asl-drawing-tool').each(function() {
        var active = $(this).data('drawing-mode') === mode;
        $(this).toggleClass('active', active).attr('aria-pressed', active ? 'true' : 'false');
      });

      if (this.current_map) {
        this.current_map.setOptions({
          draggableCursor: mode ? 'crosshair' : null,
          disableDoubleClickZoom: mode === 'polygon' || mode === 'polyline'
        });
      }

      var instructions = window.asl_customize_map_l10n || {};
      $('#asl-drawing-instructions').text(mode ? instructions[mode] : instructions.select_tool);
      this.updateDrawingActions();
    },

    handleMapClick: function(latLng) {
      if (!this.activeMode) {
        this.clearSelection();
        return;
      }

      if (this.activeMode === 'polygon' || this.activeMode === 'polyline') {
        this.addPathPoint(latLng);
        return;
      }

      if (!this.startPoint) {
        this.startPoint = latLng;
        this.createDraftShape();
        return;
      }

      this.handlePointerMove(latLng);
      this.completeDraftShape();
    },

    handlePointerMove: function(latLng) {
      if (!this.startPoint || !this.draftOverlay) {
        return;
      }

      if (this.activeMode === 'rectangle') {
        this.draftOverlay.setBounds(new google.maps.LatLngBounds(this.startPoint, latLng));
      } else if (this.activeMode === 'circle') {
        this.draftOverlay.setRadius(distanceBetween(this.startPoint, latLng));
      }
    },

    addPathPoint: function(latLng) {
      this.draftPoints.push(latLng);

      if (!this.draftOverlay) {
        var options = this.getShapeOptions();
        options.map = this.current_map;

        if (this.activeMode === 'polygon') {
          options.paths = this.draftPoints;
          this.draftOverlay = new google.maps.Polygon(options);
        } else {
          this.draftOverlay = new google.maps.Polyline({
              map: options.map,
              path: this.draftPoints,
              strokeColor: options.strokeColor,
              strokeOpacity: options.strokeOpacity,
              strokeWeight: 3,
              clickable: false
            });
        }
      } else {
        this.draftOverlay.setPath(this.draftPoints);
      }

      this.updateDrawingActions();
    },

    finishPath: function() {
      if (!this.draftOverlay || (this.activeMode !== 'polygon' && this.activeMode !== 'polyline')) {
        return;
      }

      while (this.draftPoints.length > 1 && latLngEquals(this.draftPoints[this.draftPoints.length - 1], this.draftPoints[this.draftPoints.length - 2])) {
        this.draftPoints.pop();
      }

      var minimumPoints = this.activeMode === 'polygon' ? 3 : 2;
      if (this.draftPoints.length < minimumPoints) {
        return;
      }

      this.draftOverlay.setPath(this.draftPoints);
      this.registerShape(this.draftOverlay, this.activeMode);
      this.draftOverlay = null;
      this.draftPoints = [];
      this.setMode(null);
      this.notifyChange();
    },

    createDraftShape: function() {
      var options = this.getShapeOptions();
      options.map = this.current_map;

      if (this.activeMode === 'rectangle') {
        options.bounds = new google.maps.LatLngBounds(this.startPoint, this.startPoint);
        this.draftOverlay = new google.maps.Rectangle(options);
      } else if (this.activeMode === 'circle') {
        options.center = this.startPoint;
        options.radius = 0;
        this.draftOverlay = new google.maps.Circle(options);
      }
    },

    completeDraftShape: function() {
      if (!this.draftOverlay) {
        return;
      }

      if (this.activeMode === 'circle' && this.draftOverlay.getRadius() <= 0) {
        return;
      }

      this.registerShape(this.draftOverlay, this.activeMode);
      this.draftOverlay = null;
      this.startPoint = null;
      this.setMode(null);
      this.notifyChange();
    },

    cancelDraft: function() {
      if (this.draftOverlay) {
        this.draftOverlay.setMap(null);
      }
      this.draftOverlay = null;
      this.draftPoints = [];
      this.startPoint = null;
    },

    getShapeOptions: function() {
      return {
        strokeWeight: 2,
        strokeOpacity: 0.85,
        strokeColor: this.selectedColor,
        fillColor: this.fillEnabled ? this.selectedColor : 'transparent',
        fillOpacity: this.fillEnabled ? 0.35 : 0,
        clickable: false,
        editable: false
      };
    },

    updateDrawingActions: function() {
      var pathMode = this.activeMode === 'polygon' || this.activeMode === 'polyline';
      var minimumPoints = this.activeMode === 'polygon' ? 3 : 2;

      $('#asl-drawing-actions').prop('hidden', !this.activeMode);
      $('#asl-finish-drawing')
        .toggle(pathMode)
        .prop('disabled', !pathMode || this.draftPoints.length < minimumPoints);
    },

    registerShape: function(shape, type) {
      var that = this;
      shape.type = type;
      shape._id_ = ++this.shapeId;
      shape.setOptions({clickable: true});
      this.shapes.push(shape);

      google.maps.event.addListener(shape, 'click', function() {
        that.setSelection(shape);
      });
      this.bindGeometryChange(shape);
      this.setSelection(shape);
    },

    bindGeometryChange: function(shape) {
      var that = this;
      var events = shape.type === 'circle'
        ? ['center_changed', 'radius_changed']
        : shape.type === 'rectangle'
          ? ['bounds_changed']
          : [];

      events.forEach(function(eventName) {
        google.maps.event.addListener(shape, eventName, function() {
          if (that.selectedShape === shape) {
            that.notifyChange();
          }
        });
      });

      if (shape.getPath) {
        var path = shape.getPath();
        ['insert_at', 'remove_at', 'set_at'].forEach(function(eventName) {
          google.maps.event.addListener(path, eventName, function() {
            if (that.selectedShape === shape) {
              that.notifyChange();
            }
          });
        });
      }
    },

    loadData: function(saved) {
      var that = this;
      var shapes = saved && Array.isArray(saved.shapes) ? saved.shapes : [];

      shapes.forEach(function(shapeData) {
        if (!shapeData || !shapeData.type) {
          return;
        }

        var shape = null;
        if (shapeData.type === 'polygon') {
          shape = that.create_polygon(shapeData.coord, that.current_map, shapeData);
        } else if (shapeData.type === 'polyline') {
          shape = that.create_polyline(shapeData.coord, that.current_map, shapeData);
        } else if (shapeData.type === 'circle') {
          shape = that.create_circle(shapeData, that.current_map);
        } else if (shapeData.type === 'rectangle') {
          shape = that.create_rectangle(shapeData, that.current_map);
        }

        if (shape) {
          that.registerShape(shape, shapeData.type);
        }
      });

      this.clearSelection();
    },

    create_rectangle: function(data, map) {
      if (!data.ne || !data.sw) {
        return null;
      }
      return new google.maps.Rectangle({
        map: map || this.current_map,
        bounds: new google.maps.LatLngBounds(
          new google.maps.LatLng(data.sw[0], data.sw[1]),
          new google.maps.LatLng(data.ne[0], data.ne[1])
        ),
        strokeColor: data.strokeColor || defaultColor,
        strokeOpacity: 0.85,
        strokeWeight: 2,
        fillColor: data.color || defaultColor,
        fillOpacity: data.color === 'transparent' ? 0 : 0.35,
        editable: false
      });
    },

    create_circle: function(data, map) {
      if (!data.center || !data.radius) {
        return null;
      }
      return new google.maps.Circle({
        map: map || this.current_map,
        center: new google.maps.LatLng(data.center[0], data.center[1]),
        radius: parseFloat(data.radius),
        strokeColor: data.strokeColor || defaultColor,
        strokeOpacity: 0.85,
        strokeWeight: 2,
        fillColor: data.color || defaultColor,
        fillOpacity: data.color === 'transparent' ? 0 : 0.35,
        editable: false
      });
    },

    create_polyline: function(points, map, shapeData) {
      if (!Array.isArray(points) || points.length < 2) {
        return null;
      }
      return new google.maps.Polyline({
        map: map || this.current_map,
        path: points.map(function(point) { return {lat: parseFloat(point[0]), lng: parseFloat(point[1])}; }),
        strokeColor: shapeData.strokeColor || defaultColor,
        strokeOpacity: 0.85,
        strokeWeight: 3,
        editable: false
      });
    },

    create_polygon: function(points, map, shapeData) {
      if (!Array.isArray(points) || points.length < 3) {
        return null;
      }
      return new google.maps.Polygon({
        map: map || this.current_map,
        paths: points.map(function(point) { return {lat: parseFloat(point[0]), lng: parseFloat(point[1])}; }),
        strokeColor: shapeData.strokeColor || defaultColor,
        strokeOpacity: 0.85,
        strokeWeight: 2,
        fillColor: shapeData.color || defaultColor,
        fillOpacity: shapeData.color === 'transparent' ? 0 : 0.35,
        editable: false
      });
    },

    selectColor: function(color) {
      this.selectedColor = color;
    },

    setSelectedShapeColor: function(color) {
      if (!this.selectedShape) {
        return;
      }

      this.selectedShape.set('strokeColor', color);
      if (this.selectedShape.type !== 'polyline' && this.fillEnabled) {
        this.selectedShape.set('fillColor', color);
      }
    },

    setFill: function(enabled) {
      this.fillEnabled = enabled;
      $('#asl-fill-option button').each(function() {
        var active = ($(this).data('value') === 1) === enabled;
        $(this).toggleClass('active', active).attr('aria-pressed', active ? 'true' : 'false');
      });

      if (this.selectedShape && this.selectedShape.type !== 'polyline') {
        this.selectedShape.set('fillColor', enabled ? this.selectedShape.get('strokeColor') : 'transparent');
        this.selectedShape.set('fillOpacity', enabled ? 0.35 : 0);
      }
    },

    setSelection: function(shape) {
      this.clearSelection();
      this.selectedShape = shape;
      shape.setEditable(true);

      var color = shape.get('strokeColor') || defaultColor;
      this.selectedColor = color;
      $('.asl-p-cont .color_scheme input').filter(function() {
        return this.value.toLowerCase() === color.toLowerCase();
      }).prop('checked', true);

      this.setFill(shape.type === 'polyline' || shape.get('fillColor') !== 'transparent');
      $('#asl-delete-shape').prop('disabled', false);
      $('#asl-selected-shape-label').text(shape.type.charAt(0).toUpperCase() + shape.type.slice(1));
    },

    clearSelection: function() {
      if (this.selectedShape) {
        this.selectedShape.setEditable(false);
      }
      this.selectedShape = null;
      $('#asl-delete-shape').prop('disabled', true);
      $('#asl-selected-shape-label').text((window.asl_customize_map_l10n && asl_customize_map_l10n.none_selected) || 'None selected');
    },

    deleteSelectedShape: function() {
      if (!this.selectedShape) {
        return;
      }

      var selectedId = this.selectedShape._id_;
      this.selectedShape.setMap(null);
      this.shapes = this.shapes.filter(function(shape) {
        return shape._id_ !== selectedId;
      });
      this.clearSelection();
      this.notifyChange();
    },

    clearAll: function() {
      this.cancelDraft();
      this.shapes.forEach(function(shape) {
        shape.setMap(null);
      });
      this.markers.forEach(function(marker) {
        marker.setMap(null);
      });
      this.shapes = [];
      this.markers = [];
      this.clearSelection();
      this.notifyChange();
    },

    get_data: function(map) {
      var currentMap = map || this.current_map;
      var serializedShapes = this.shapes.map(function(shape) {
        var serialized = {
          type: shape.type,
          color: shape.type === 'polyline' ? 'transparent' : (shape.get('fillColor') || 'transparent'),
          strokeColor: shape.get('strokeColor') || defaultColor
        };

        if (shape.type === 'polygon' || shape.type === 'polyline') {
          serialized.coord = shape.getPath().getArray().map(function(point) {
            return [point.lat(), point.lng()];
          });
        } else if (shape.type === 'circle') {
          serialized.center = [shape.getCenter().lat(), shape.getCenter().lng()];
          serialized.radius = shape.getRadius();
        } else if (shape.type === 'rectangle') {
          var bounds = shape.getBounds();
          serialized.ne = [bounds.getNorthEast().lat(), bounds.getNorthEast().lng()];
          serialized.sw = [bounds.getSouthWest().lat(), bounds.getSouthWest().lng()];
        }

        return serialized;
      });

      return {
        zoom: currentMap.getZoom(),
        center: [currentMap.getCenter().lat(), currentMap.getCenter().lng()],
        shapes: serializedShapes,
        markers: []
      };
    },

    notifyChange: function() {
      $(document).trigger('asl:map-customization-change');
    }
  };
})(jQuery);
