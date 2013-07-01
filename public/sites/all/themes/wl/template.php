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
 * Add in some variables for use in page.tpl.php
 *
 * Implements hook_preprocess_page()
 *
 * @param array &$vars
 */
function wl_preprocess_page(&$vars) {
  if(!module_exists('wl_editing_framework')) {
    drupal_set_message(t('The Where’s Lucian base theme requires the Where’s Lucian Editing Framework module to be installed.'), 'warning');
  }
  
  $new_vars = array(
    'wl_show_title' => true,
    'wl_add_colon_to_title' => false,
    'wl_subtitle' => '',
  );

  $node = false;
  if(!empty($vars['node'])) {
    switch($vars['node']->type) {
      case 'page':
      case 'panel':
        $node = $vars['node'];
        break;
    }
  }

  if(!empty($node)) {
    //Get the titles
    $show_title = field_get_items('node', $node, 'field_show_title');
    $show_title = reset($show_title);
    $new_vars['wl_show_title'] = !empty($show_title['value']);

    $add_colon = field_get_items('node', $node, 'field_add_colon_to_title');
    $add_colon = reset($add_colon);
    $new_vars['wl_add_colon_to_title'] = !empty($add_colon_to_title['value']);

    $subtitle = field_get_items('node', $node, 'field_subtitle');
    if(!empty($subtitle)) {
      foreach($subtitle as $value) {
        $new_vars['wl_subtitle'] .= render(
          field_view_value('node', $node, 'field_subtitle', $value)
        );
      }
    }
    if(!empty($new_vars['wl_subtitle'])) {
      $new_vars['wl_subtitle'] = "<small>{$new_vars['wl_subtitle']}</small>";
    }
  }

  //Merge in the new vars
  $vars = array_merge($vars, $new_vars);
}