var asl_engine = window['asl_engine'] || {};

(function($, app_engine) {
  'use strict';


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

  app_engine['pages'] = {
    /**
     * [dashboard Main Dashboard page]
     * @return {[type]} [description]
     */
    dashboard: function() {

      var current_date  = 0,
        date_           = new Date();

      var day_arr = [];
      var months  = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        month     = months[date_.getMonth()],
        data_arr  = [];

      //  add dummy data
      for (var a = 1; a <= date_.getDate(); a++) {

        day_arr.push(a + ' ' + month);
        data_arr.push(0);
      }

      var lineChartData = {
        labels: day_arr,
        datasets: [{
          tension: 0.1,
          lineTension: 0.1,
          backgroundColor: "rgba(75, 192, 192, 0.4)",
          borderColor: "rgba(75, 192, 192, 1",
          borderCapStyle: 'butt',
          borderDash: [],
          borderDashOffset: 0.0,
          borderJoinStyle: 'miter',
          pointBorderColor: "rgba(75, 192, 192, 1)",
          pointBackgroundColor: "#fff",
          pointBorderWidth: 1,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: "rgba(75, 192, 192, 1)",
          pointHoverBorderColor: "rgba(220, 220, 220, 1)",
          pointHoverBorderWidth: 2,
          pointRadius: 1,
          pointHitRadius: 10,
          label: 'Searches',
          backgroundColor: "#57C8F2",
          data: data_arr
        }]

      };

      asl_initialize_chart();

      //  Datetime
      var $datepicker = $('#sl-datetimepicker');

      /**
       * [get_duration_string Return the duration  string used for the AJAX]
       * @return {[type]} [description]
       */
      function get_duration_string() {

        var dt_data = $datepicker.data('daterangepicker');

        return encodeURI('sl-start=' + dt_data.startDate.format('YYYY-MM-DD') + '&sl-end=' + dt_data.endDate.format('YYYY-MM-DD'));
      };

      /////////////////////////////////
      //  Change the expertise level //
      /////////////////////////////////
      var $sl_level = $('#asl-level-swtch');

      /**
       * [update_level description]
       * @param  {[type]} _status [description]
       */
      function update_level(_status, _callback) {
        
        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&sl-action=expertise_level", {status: $sl_level[0].checked? '1': '0'}, function(_response) {

          toastIt(_response);

          if(_callback) {
            _callback(_response);
          }

          window.setTimeout(function(){
            
            window.location.reload();

          }, 1500);


        }, 'json');
      }; 

      //  Cache Switch Event
      $sl_level.bind('change', function(e) {

        var chk_ctrl = e.target,
            status   = (chk_ctrl.checked)? '1': '0';

        update_level(status);
      });

      // Analytics is a locked preview in the Free edition.
      if (!document.getElementById('asl_search_canvas')) {
        return;
      }


      //  Export the leads
      $('#sl-btn-export-stats').bind('click', function() {

        window.location.href = ASL_REMOTE.URL + "?action=asl_ajax_handler&asl-nounce=" + ASL_REMOTE.nounce + "&sl-action=export_stats&" + get_duration_string();
      });



      ///////////bar chart
      var ctx = document.getElementById("asl_search_canvas").getContext("2d"),
        charts_option = {
          type: 'line',
          data: lineChartData,
          options: {
            bezierCurve: true,
            animation: true,
            responsive: true,
            maintainAspectRatio: false,
            title: {
              display: true,
              text: '#Searches'
            },
            scales: {
              y: {
                suggestedMin: 0,
                ticks: {
                  beginAtZero: true
                }
              }
            }
          }
        };
      var myBar = new Chart(ctx, charts_option);

      Chart.defaults.scales.linear.min = 0;

      /**
 * [updateChart Get the Stats Chart]
 * @return {[type]}   [description]
 */
function updateChart(_chart_data) {
  var searchDataRaw = _chart_data[0];
  var interactionDataRaw = _chart_data[1];

  var labels = [];
  var searchData = [];
  var interactionData = [];

  // Use Object.keys to iterate
  for (var key in searchDataRaw) {
    if (searchDataRaw.hasOwnProperty(key) && interactionDataRaw.hasOwnProperty(key)) {
      labels.push(searchDataRaw[key]['label']);
      searchData.push(parseInt(searchDataRaw[key]['data']));
      interactionData.push(parseInt(interactionDataRaw[key]['data']));
    }
  }

  // Destroy old chart
  if (myBar) {
    myBar.destroy();
  }

  // Create new chart with two datasets
  myBar = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Searches',
          data: searchData,
          borderColor: 'rgba(75,192,192,1)',
          backgroundColor: 'rgba(75,192,192,0.2)',
          tension: 0.1,
          fill: true
        },
        {
          label: 'Interactions',
          data: interactionData,
          borderColor: 'rgba(255,99,132,1)',
          backgroundColor: 'rgba(255,99,132,0.2)',
          tension: 0.1,
          fill: true
        }
      ]
    },
    options: charts_option.options
  });
}



      //
      //updateChart(m, y);

      /**
       * [getViews Get the Stores Views and Search]
       * @return {[type]}   [description]
       */
      function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
      }

      function countryFlag(countryCode) {
        var code = String(countryCode || '').toUpperCase();

        if (!/^[A-Z]{2}$/.test(code)) {
          return '<span class="asl-location-placeholder">&#128205;</span>';
        }

        return `<img class="asl-country-flag" src="https://flagcdn.com/${escapeHtml(code.toLowerCase())}.svg" alt="${escapeHtml(code)} flag" width="24" height="18" loading="lazy">`;
      }

      function changeHtml(change) {
        change = change || {label: '—', trend: 'same'};

        var icon = change.trend === 'up' ? '&#9650;' : (change.trend === 'down' ? '&#9660;' : '');
        return `<span class="asl-change asl-change-${escapeHtml(change.trend || 'same')}">${icon} ${escapeHtml(change.label || '—')}</span>`;
      }

      var analytics_data = {
        stores: [],
        searches: []
      };

      var analytics_sort = {
        stores: {key: 'views', direction: 'desc'},
        searches: {key: 'views', direction: 'desc'}
      };

      function sortAnalyticsRows(rows, listName) {
        var sort = analytics_sort[listName];

        return rows.slice().sort(function(a, b) {
          var aValue;
          var bValue;

          if (sort.key === 'change') {
            aValue = a.change && a.change.value != null ? parseFloat(a.change.value) : Number.POSITIVE_INFINITY;
            bValue = b.change && b.change.value != null ? parseFloat(b.change.value) : Number.POSITIVE_INFINITY;
          }
          else {
            aValue = parseInt(a.views, 10) || 0;
            bValue = parseInt(b.views, 10) || 0;
          }

          if (aValue === bValue) {
            return (parseInt(b.views, 10) || 0) - (parseInt(a.views, 10) || 0);
          }

          return sort.direction === 'asc' ? aValue - bValue : bValue - aValue;
        });
      }

      function getViews(stores_views, search_views) {

        if (Array.isArray(stores_views)) {
          analytics_data.stores = stores_views;
        }

        if (Array.isArray(search_views)) {
          analytics_data.searches = search_views;
        }

        stores_views = sortAnalyticsRows(analytics_data.stores, 'stores');
        search_views = sortAnalyticsRows(analytics_data.searches, 'searches');

        //  Clear old records
        jQuery('#asl-stores-views li').remove();
        jQuery('#asl-searches-views li').remove();


        ///////////////////////////////
        //  Iterate to fill the list //
        ///////////////////////////////

        var stores_views_html = '';
        if (stores_views && stores_views.length) {

          for (var s = 0; s < stores_views.length; s++) {

            var _store_view = stores_views[s];
            var store_logo = _store_view.logo || ASL_REMOTE.logo;
            stores_views_html += `<li class="list-group-item">
                          <div class="row">
                            <div class="col-7"><div class="list-items asl-analytics-name"><img src="${escapeHtml(store_logo)}" alt=""> <span>${escapeHtml(_store_view.title)}</span></div></div>
                            <div class="col-2 text-right"><div class="list-items">${escapeHtml(_store_view.views)}</div></div>
                            <div class="col-3 text-right"><div class="list-items">${changeHtml(_store_view.change)}</div></div>
                          </div>
                        </li>`;
          }
        } 
        else {

          stores_views_html += `<li class="list-group-item">
                          <div class="row">
                            <div class="col-12 text-center asl-empty-state">${escapeHtml(ASL_REMOTE.LANG.no_store_views)}</div>
                          </div>
                        </li>`;
        }

        jQuery('#asl-stores-views').append(stores_views_html);


        ///////////////////////////////
        //  Iterate to fill the list //
        ///////////////////////////////
        var searches_views_html = '';
        if (search_views && search_views.length) {

          for (var s = 0; s < search_views.length; s++) {

            var _store_view = search_views[s];
            searches_views_html += `<li class="list-group-item">
                          <div class="row">
                            <div class="col-7"><div class="list-items asl-analytics-name">${countryFlag(_store_view.country_code)} <span>${escapeHtml(_store_view.search_str)}</span></div></div>
                            <div class="col-2 text-right"><div class="list-items">${escapeHtml(_store_view.views)}</div></div>
                            <div class="col-3 text-right"><div class="list-items">${changeHtml(_store_view.change)}</div></div>
                          </div>
                        </li>`;
          }
        } 
        else {

          searches_views_html += `<li class="list-group-item">
                          <div class="row">
                            <div class="col-12 text-center asl-empty-state">${escapeHtml(ASL_REMOTE.LANG.no_search_results)}</div>
                          </div>
                        </li>`;
        }
        

        jQuery('#asl-searches-views').append(searches_views_html);
      };

      $('.asl-sort-button').bind('click', function() {
        var $button = $(this);
        var listName = $button.data('list');
        var sortKey = $button.data('sort');
        var currentSort = analytics_sort[listName];

        if (currentSort.key === sortKey) {
          currentSort.direction = currentSort.direction === 'desc' ? 'asc' : 'desc';
        }
        else {
          currentSort.key = sortKey;
          currentSort.direction = 'desc';
        }

        $('.asl-sort-button[data-list="' + listName + '"]').removeClass('is-active').attr('aria-sort', 'none').find('.asl-sort-indicator').html('');
        $button.addClass('is-active')
          .attr('data-direction', currentSort.direction)
          .attr('aria-sort', currentSort.direction === 'desc' ? 'descending' : 'ascending')
          .find('.asl-sort-indicator')
          .html(currentSort.direction === 'desc' ? '&#9660;' : '&#9650;');

        getViews(null, null);
      });


      //  Export the analytics
      $('#sl-btn-export-analytics').bind('click', function() {

        window.location.href = ASL_REMOTE.URL + "?action=asl_ajax_handler&asl-nounce=" + ASL_REMOTE.nounce + "&sl-action=export_stats&" + get_duration_string();
      });


      //getViews(temp[0], temp[1]);
      
      //  datetime options
      var date_time_options = {
        "timePicker": false,
        "parentEl": '.tab-content .form-group',
        "alwaysShowCalendars": false,
        "startDate": moment().subtract(6, 'days'),
        "endDate": moment().startOf('hour'),
         "ranges": {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      };
    
      /**
       * [refetch_data Refetch data of the stats]
       * @return {[type]} [description]
       */
      function refetch_data() {

        //  Length of data
        var rows_len = $('#asl-search-len').val();

        //  apply servercall
        ServerCall(ASL_REMOTE.URL + "?action=asl_ajax_handler&asl-nounce=" + ASL_REMOTE.nounce + "&sl-action=get_stats&" + get_duration_string(), {len: rows_len }, function(_response) {

          var stores_views = _response.stores;
          var search_views = _response.searches;
          var chart_data   = _response.chart_data;

          getViews(stores_views, search_views);

          updateChart(chart_data);
        });
      };

      $('.asl-view-all').bind('click', function() {
        $('#asl-search-len').val('0');
        $('.asl-view-all').prop('disabled', true).addClass('d-none');
        refetch_data();
      });


      //  Add datetimepicker
      $datepicker.daterangepicker(date_time_options, 
        function(start, end, label) {
          
          refetch_data();
          //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')')
      });

      //  first time
      refetch_data();

      //  For the Views
      $('#asl-search-view,#asl-search-len').bind('change', function(e) {
        refetch_data();
      });

    }
  };


  asl_engine.pages.dashboard();

})(jQuery, asl_engine);
