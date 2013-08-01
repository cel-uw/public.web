/* 
  If older versions of jQuery UI call .outerWidth() or outerHeight() methods, 
  an object or array is sometimes returned. By passing in the default value
  for the first argument (false), this should remedy that.
*/

(function(){
  // Store a reference to the original remove method.
  var originalOuterHeight = jQuery.fn.outerHeight,
      originalOuterWidth  = jQuery.fn.outerWidth;
   
  // Define overriding method.
  jQuery.fn.outerHeight = function() {
    if(!arguments || !arguments.length) {
      arguments = [ false ];
    }
    // Execute the original method.
    return originalOuterHeight.apply( this, arguments );
  }

  // Define overriding method.
  jQuery.fn.outerWidth = function() {
    if(!arguments || !arguments.length) {
      arguments = [ false ];
    }
    // Execute the original method.
    return originalOuterWidth.apply( this, arguments );
  }
})();