<?php
/**
 * @file
 * wl_content_layout.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function wl_content_layout_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  
  if($module == "field_group" && $api == "field_group") {
    return array("version" => "1");
  }
  else if($module == "page_manager" && $api == "pages_default") {
    return array("version" => "1");
  }
  else if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}
