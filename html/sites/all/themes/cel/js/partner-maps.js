/*! 
 * Library for rendering CEL partners on Google Maps
 *
 * @author Lucian DiPeso
 */

window.cel = window.cel || {};
window.cel.maps = (function($) {

  var maps = {}
      loaded_map_types = {};

  // Public methods
  /**
   * Creates a map or returns an existing map
   *
   * If a map with key already exists, it will simply return it.
   *
   * @param string key The key identifying this map
   * @param string view_id The DOM selector for the view's HTML container
   * @param object options Map options
   * @return bool|object The map object on success, false on failure
   */
  create = function(key, view_id, options) {
    options = options || {};
    options = $.extend(true, {}, options);

    if(!$(view_id).length) {
      return false;
    }

    // Clear out any existing maps
    if(maps[key] && maps[key].map) {
      maps[key].map.remove();
    }

    if($('#map-' + key).length) {
      $('#map-' + key).remove();
    }

    // Build map data
    maps[key] = {
      'map': null,
      'partners': _format_partners(key),
      'map_types': [],
      'current_filters': _get_view_filters(key)
    };

    // Create the new container
    var container = $('<div class="cel-map" id="map-' + key + '"></div>');
    container.css({ width: '100%', height: '600px' });
    $(view_id).after(container);

    // Determine which map to use
    maps[key].map_types = _get_map_types(key);
    _load_map_file(maps[key].map_types, function(map_type) {
      if(map_type) {
        maps[key].map = _load_map(key, container, map_type);
      }
    });
  }

  /**
   * Loads the best available map
   *
   * @param string map_type The type of map to try loading
   * @return string|bool The fully-qualified map type name, or false if it couldn't load the map file
   */
  _load_map_file = function(map_types, callback) {
    var format = 'mill',
        lang = 'en',
        map_type = map_types.shift();

    switch(map_type) {
      case 'CA':
      case 'US':
        format = 'lcc';
        break;
    }

    var map_file = 'jquery-jvectormap-' + map_type.toLowerCase() + '-' + format + '-' + lang + '.js',
        full_map_type = map_type.toLowerCase() + '_' + format + '_' + lang,
        that = this;

    if(full_map_type in loaded_map_types) {
      // We had already loaded this map file
      callback.call(that, loaded_map_types[full_map_type]);
      return;
    }

    // We need to try and load the map file for this
    $.getScript(Drupal.settings.basePath + 'sites/all/themes/cel/js/maps/' + map_file)
    .done(function(script, textStatus) {
      // Yay! We loaded our map
      loaded_map_types[full_map_type] = full_map_type;
      callback.call(that, full_map_type);
    })
    .fail(function(jqxhr, settings, exception) {
      // No file to be found :(
      loaded_map_types[full_map_type] = false;
      // Try loading another one...
      if(map_types.length) {
        _load_map_file(map_types, callback);
      } else {
        callback.call(that, false);
      }
    });
  }

  /**
   * Renders a map
   *
   * @param string key The key for this map
   * @param string|DOM|jQuery container The DOM element in which to place the rendered map
   * @param string map_type The map type to use
   * @return jvm.WorldMap The rendered map object
   */
  _load_map = function(key, container, map_type) {
    // Get data
    var map_options = {
      backgroundColor: 'transparent',
      map: map_type,
      container: container,
      zoomOnScroll: false,
      series: {
        regions: [{
          attribute: 'fill',
        }],
      },
      markers: maps[key].partners.pins,
      markerStyle: {
        initial: {
          fill: '#F8E23B',
          stroke: '#383f47'
        }
      },
      // Show any partners in this region
      onRegionLabelShow: function(event, label, code) {
        var partners = '';
        if(maps[key] && maps[key].partners.by_region[code] && maps[key].partners.by_region[code].partner_titles.length) {
          partners = '<ul>' +
            '<li>' + maps[key].partners.by_region[code].partner_titles.join('</li><li>') + '</li>' +
          '</ul>';
        }
        label.html(
          '<div class="inner">' +
            '<h5>' + label.html() + '</h5>' +
            partners + 
          '</div>'
        );
      },
      // Load a map with this region, if possible
      onRegionClick: function(event, code) {
        if(!maps[key]) {
          return;
        }

        // Let's see if we have a map for this specific code
        _load_map_file([ code ], function(map_type) {
          if(map_type) {
            var href = window.location.href;

            if(href.substr(href.length-1) !== "/") {
              href = href + "/";
            }

            var new_filter = code.split("-");
            new_filter.splice(0, maps[key].current_filters.length);
            new_filter.join("-");

            window.location.href = href + new_filter;
          }
        });
      }
    }

    var map = new jvm.WorldMap(map_options);

    // Build a list of pin titles and 
    var data = {},
        i;
    for(i in map.regions) {
      if(!maps[key].partners.by_region[i]) {
        // Sometimes we get the full name of a state from Google, rather than the ISO-3166-2 code
        // Let's search by full name as well
        var components = i.split("-"),
            region = map.regions[i];
        components.pop();
        var code = components.join("-") + "-" + region.config.name;

        if(maps[key].partners.by_region[code]) {
          maps[key].partners.by_region[i] = $.extend(true, {}, maps[key].partners.by_region[code]);
          maps[key].partners.by_region[code] = false;
        }
      }

      if(maps[key].partners.by_region[i] && maps[key].partners.by_region[i].partner_titles.length) {
        data[i] = 'rgb(71, 47, 146)';
      } else {
        data[i] = 'rgb(136, 137, 137)';
      }
    }

    map.series.regions[0].setValues(data);
    return map;
  }

  /**
   * Format the partner JSON object into something more usable
   *
   * @param string key The key for this map
   * @return object Partner data
   */
  _format_partners = function(key) {
    var partners = window.cel.json.get(key);
    partners = partners.partners || [];

    var pins = [],
        countries = [],
        provinces = [],
        counties = [],
        by_region = {};

    for(var i=0, length=partners.length; i<length; i++) {
      pins.push({
        latLng: [ partners[i].partner.lat, partners[i].partner['long'] ],
        name: partners[i].partner.title
      });

      var country  = partners[i].partner.country,
          province = partners[i].partner.province,
          county = partners[i].partner.county;

      if(country === 'GB') {
        // They don't use the real country code, but the fake one :-O
        country = 'UK';
      }

      if(country) {
        if($.inArray(country, countries) <= -1) {
          countries.push(country);
        }

        if(!by_region[country]) {
          by_region[country] = {
            name: country,
            filters: [ country ],
            partner_titles: []
          };
        }
        by_region[country].partner_titles.push(partners[i].partner.title);
      }

      if(province) {
        var province_key = country + "-" + province;
        if($.inArray(province_key, provinces) <= -1) {
          provinces.push(province_key);
        }

        if(!by_region[province_key]) {
          by_region[province_key] = {
            name: province,
            filters: [ country, province ],
            partner_titles: []
          };
        }
        by_region[province_key].partner_titles.push(partners[i].partner.title);
      }

      if(county) {
        var county_key = country + "-" + province + "-" + county;
        if($.inArray(county_key, counties) <= -1) {
          counties.push(county_key);
        }

        if(!by_region[county_key]) {
          by_region[county_key] = {
            name: county,
            filters: [ country, province, county ],
            partner_titles: []
          };
        }
        by_region[county_key].partner_titles.push(partners[i].partner.title);
      }
    }

    return {
      'pins': pins,
      'countries': countries,
      'provinces': provinces,
      'counties': counties,
      'by_region': by_region
    }
  }

  _get_view_filters = function(key) {
    Drupal.settings.views = Drupal.settings.views || {};
    Drupal.settings.views.ajaxViews = Drupal.settings.views.ajaxViews || {};
    Drupal.settings.views.ajaxViews['views_dom_id:' + key] = Drupal.settings.views.ajaxViews['views_dom_id:' + key] || {
      view_args: ""
    };

    if(!Drupal.settings.views.ajaxViews['views_dom_id:' + key].view_args) {
      return []
    }

    return Drupal.settings.views.ajaxViews['views_dom_id:' + key].view_args.split("/") || [];
  }

  _get_map_types = function(key, filters) {
    filters = filters || _get_view_filters(key);

    var map_types = [ 'world' ],
        country_filter = filters[0] || false,
        province_filter = filters[1] || false,
        county_filter = filters[2] || false;

    // Validate filters
    var country_validator = /^[A-Z]{2}$/,
        province_validator = /^[A-Z0-9 \-\_\.]+$/i,
        county_validator = XRegExp('^\\p{L}+$');
    country_filter = country_validator.test(country_filter) ? country_filter : false;
    province_filter = province_validator.test(province_filter) ? province_filter : false;
    county_filter = county_validator.test(county_filter) ? county_filter : false;

    if(country_filter) {
      // We should use a country
      map_types.unshift(country_filter + '_regions');
      map_types.unshift(country_filter);

      if(province_filter) {
        // We should use a province
        map_types.unshift(country_filter + '-' + province_filter);

        if(county_filter) {
          // We should use a county
          map_types.unshift(country_filter + '-' + province_filter + '-' + county_filter);
        }
      }
    }

    return map_types;
  }

  return {
    'create': create
  }

})(jQuery);
