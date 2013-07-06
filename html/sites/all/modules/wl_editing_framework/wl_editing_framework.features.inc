<?php
/**
 * @file
 * wl_editing_framework.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function wl_editing_framework_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  
  if($module == "field_group" && $api == "field_group") {
    return array("version" => "1");
  }
  else if($module == "linkit" && $api == "linkit_profiles") {
    return array("version" => "1");
  }
  else if($module == "page_manager" && $api == "pages_default") {
    return array("version" => "1");
  }
  else if($module == "strongarm" && $api == "strongarm") {
    return array("version" => "1");
  }
}

/**
 * Implements hook_node_info().
 */
function wl_editing_framework_node_info() {
  $items = array(
    'blurb' => array(
      'name' => t('Blurb'),
      'base' => 'node_content',
      'description' => 'A small piece of static content you can place on multiple pages. For example, you might place basic contact information on several different pages.',
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),

    'slideshow' => array(
      'name' => t('Slideshow'),
      'base' => 'node_content',
      'description' => 'A slideshow of photos and/or YouTube videos.',
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
  );
  return $items;
}
