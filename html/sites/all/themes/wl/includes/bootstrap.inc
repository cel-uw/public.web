<?php

/**
 * A wrapper function for theme_get_setting().
 * 
 * @param string $name The name of the setting that you want to retrieve. 
 * @param string|null $theme The key (machin-readable name) of a theme.
 *    Defaults to the key of the current theme if not defined.
 * @see theme_get_setting()
 * @return mixed
 */
function wl_theme_get_setting($name, $theme = NULL) {
  switch ($name) {
    case 'exclude':
      $setting = wl_theme_get_info($name, $theme);
      break;
    default:
      $setting = theme_get_setting($name, $theme);
      break;
  }

  return isset($setting) ? $setting : NULL; 
}

/**
 * Returns the settings for a particular theme
 *
 * @param string|null $theme The theme name
 * @return mixed
 */
function wl_get_settings($theme = NULL) {
  if(!isset($theme)) {
    $theme = !empty($GLOBALS['theme_key']) ? $GLOBALS['theme_key'] : '';
  }

  if($theme) {
    $themes = list_themes();
    $theme_object = $themes[$theme];
  }

  return $theme_object->info['settings'];
}

/**
 * Gets the setting for a theme (or subtheme)
 *
 * @param string $setting_name The name of the setting
 * @param string|null $theme The name of the theme
 * @return mixed
 */
function wl_theme_get_info($setting_name, $theme = NULL) {
  // If no key is given, use the current theme if we can determine it.
  if (!isset($theme)) {
    $theme = !empty($GLOBALS['theme_key']) ? $GLOBALS['theme_key'] : '';
  }

  $output = array();

  if ($theme) {
    $themes = list_themes();
    $theme_object = $themes[$theme];

    // Create a list which includes the current theme and all its base themes.
    if (isset($theme_object->base_themes)) {
      $theme_keys = array_keys($theme_object->base_themes);
      $theme_keys[] = $theme;
    }
    else {
      $theme_keys = array($theme);
    }

    foreach ($theme_keys as $theme_key) {
      if (!empty($themes[$theme_key]->info[$setting_name])) {
        $output[$setting_name] = $themes[$theme_key]->info[$setting_name];
      }
    }
  }
  
  return $output;
}

/**
 * Returns navigational links based on a menu tree
 *
 * @param array $tree
 * @param int $lvl The current menu level
 */
function wl_menu_navigation_links($tree, $lvl = 0) {
  $result = array();

  if (count($tree) > 0) {
    foreach ($tree as $id => $item) {
      // Only work with enabled links
      if (empty($item['link']['hidden'])) {
        $new_item = array(
          'title' => $item['link']['title'],
          'link_path' => $item['link']['link_path'],
          'href' => $item['link']['href'],
        );

        // Dont do drugs and don't do any levels deeper then 1
        if ($lvl < 1) {
          $new_item['below'] = wl_menu_navigation_links($item['below'], $lvl+1);
        }

        $result['menu-'. $item['link']['mlid']] = $new_item;
      }
    }
  }
  
  return $result;
}

/**
 * Implements theme_bootstrap_progress_bar()
 *
 * @param array $vars
 */
function wl_bootstrap_progress_bar($vars) {
  $vars['attributes']['class'][] = 'progress';
  $vars['type'] = (!empty($vars['type'])) ? $vars['type'] : 'info';
  $attributes = drupal_attributes($vars['attributes']);
  
  return <<<EOL
<div {$attributes}>
  <div class="progress-bar progress-bar-{$vars['type']}" style="width: {$vars['percent']}%"></div>
</div>
EOL;

  "<div". drupal_attributes($vars['attributes']) .">
  <div class=\"bar\"
       style=\"width: ". $vars['percent'] ."%;\"></div>
  </div>";
}
