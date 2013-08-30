/*! 
 * Helper functions for using views with exposed filters as JSON sources
 * 
 * @author Lucian DiPeso
 */

/**
 * @var object cel_json The object to hold the results of our view queries
 */
window.cel = window.cel || {};
window.cel.json = (function($) {
  var jsons = {};
  // Public methods

  /**
   * Update the value of a particular JSON result
   *
   * @param string key The identifier of this JSON
   * @param object value The JSON
   */
  function set(key, value) {
    jsons[key] = value;  
  }

  /**
   * Update the value of a particular JSON result
   *
   * @param string key The identifier of this JSON
   * @param object value The JSON
   */
  function get(key) {
    return jsons[key] || false;
  }

  return {
    'set': set,
    'get': get
  }
})(jQuery);