<?php
/**
 * Load a modern version of jQuery and/or jQuery Migrate
 *
 * Implements hook_js_alter().
 *
 * @param array &$js Javascript files
 */
function wl_js_alter(&$js) {
  $files = array();

  // If jquery_update available, we should not add js from theme info file
  if(module_exists('jquery_update')) {
    return;
  }

  // Load excluded JS files from theme.
  $excludes = _bootstrap_alter(bootstrap_theme_get_info('exclude'), 'js');
  $theme_path = drupal_get_path('theme', 'wl_base_js_alter');

  if(theme_get_setting('cdn_jquery')) {
    // Swap out jQuery to use the CDN
    $version = theme_get_setting('cdn_jquery_version');
    $js['misc/jquery.js']['data'] = '//ajax.googleapis.com/ajax/libs/jquery/' . $version . '/jquery.min.js';;
    $js['misc/jquery.js']['type'] = 'external';
    $js['misc/jquery.js']['version'] = $version;
    $js['misc/jquery.js']['weight'] -= 2;
  }

  if(theme_get_setting('cdn_jquery_migrate')) {
    // Include the migrate plugin to account for deprecated jQuery
    $version = theme_get_setting('cdn_jquery_migrate_version');
    $migrate = '//code.jquery.com/jquery-migrate-' . $version . '.min.js';
    $js[$migrate] = $js['misc/jquery.js'];
    $js[$migrate]['data'] = $migrate;
    $js[$migrate]['version'] = $version;
    $js[$migrate]['type'] = 'external';
    $js[$migrate]['weight']++;
  }

  $js = array_diff_key($js, $excludes);
}

/**
 * Add in some variables for use in node.tpl.php
 *
 * Implements hook_preprocess_node()
 *
 * @param array &$vars
 */
function wl_preprocess_node(&$vars) {
  $node = node_load($vars['nid']);

  $vars = _wl_basic_preprocess_vars($node, $vars);
}

/**
 * Add in some variables for use in page.tpl.php
 *
 * Implements hook_preprocess_page()
 *
 * @param array &$vars
 */
function wl_preprocess_page(&$vars) {
  $node = false;
  if(!empty($vars['node'])) {
    $node = $vars['node'];
  }
  $vars = _wl_basic_preprocess_vars($node, $vars);
}

/**
 * Add in basic vars for preprocess_page() and preprocess_node()
 *
 * Implements hook_preprocess_page()
 *
 * @param mixed $node
 * @param array $vars
 * @return array The modified vars
 */
function _wl_basic_preprocess_vars($node, $vars) {
  $new_vars = array(
    'wl_show_title' => true,
    'wl_add_colon_to_title' => false,
    'wl_subtitle' => '',
  );

  if(!empty($node)) {
    //Get the titles
    $show_title_items = field_get_items('node', $node, 'field_show_titles');
    if(!empty($show_title_items)) {
      $show_title = reset($show_title_items);
      $new_vars['wl_show_title'] = !empty($show_title['value']);
    }

    $add_colon_items = field_get_items('node', $node, 'field_add_colon_to_title');
    if(!empty($add_colon_items)) {
      $add_colon = reset($add_colon_items);
      $new_vars['wl_add_colon_to_title'] = !empty($add_colon['value']);
    }

    $subtitle_items = field_get_items('node', $node, 'field_subtitle');
    if(!empty($subtitle_items)) {
      foreach($subtitle_items as $value) {
        $subtitle_item = field_view_value('node', $node, 'field_subtitle', $value);
        $new_vars['wl_subtitle'] .= render($subtitle_item);
      }
    }
    if(!empty($new_vars['wl_subtitle'])) {
      $new_vars['wl_subtitle'] = "<small>{$new_vars['wl_subtitle']}</small>";
    }
  }

  //Merge in the new vars
  $vars = array_merge($vars, $new_vars);

  return $vars;
}