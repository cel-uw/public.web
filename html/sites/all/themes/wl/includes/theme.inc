<?php
/*
 * Custom theme functions
 *
 */

/**
 * Returns HTML for a set of links.
 *
 * @param array $vars
 * @return string
 */
function theme_bootstrap_links($vars) {
  $links = $vars['links'];
  $attributes = $vars['attributes'];
  $heading = $vars['heading'];

  global $language_url;
  $output = '';

  if (count($links) > 0) {
    $output = '';
    $output .= '<ul' . drupal_attributes($attributes) . '>';

    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = array(
          'text' => $heading,
          // Set the default level of the heading.
          'level' => 'li',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      $children = array();

      if (isset($link['below'])) {
        $children = $link['below'];
      }

      $attributes = array('class' => array($key));

      // Add first, last and active classes to the list of links to help out themers.
      if ($i == 1) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_links) {
        $attributes['class'][] = 'last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
        && (empty($link['language']) || $link['language']->language == $language_url->language)) {
        $attributes['class'][] = 'active';
      }

      if (count($children) > 0) {
        $attributes['class'][] = 'dropdown';
        $link['attributes']['data-toggle'] = 'dropdown';
        $link['attributes']['class'][] = 'dropdown-toggle';
      }

      if (!isset($link['attributes'])) {
        $link['attributes'] = array();
      }

      $link['attributes'] = array_merge($link['attributes'], $attributes);

      if (count($children) > 0) {
        $link['attributes']['class'][] = 'dropdown';
      }

      $output .= '<li' . drupal_attributes($attributes) . '>';

      if (isset($link['href'])) {
        if (count($children) > 0) {
          $link['html'] = TRUE;
          $link['title'] .= ' <span class="caret"></span>';
          $output .=  '<a' . drupal_attributes($link['attributes']) . ' href="#">'. $link['title'] .'</a>';
        }
        else {
          // Pass in $link as $options, they share the same keys.
          $output .= l($link['title'], $link['href'], $link);
        }
      }
      elseif (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }

        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $i++;

      if(count($children) > 0) {
        $attributes = array();
        $attributes['class'] = array('dropdown-menu');

        $output .= theme('bootstrap_links', array('links' => $children, 'attributes' => $attributes));
      }

      $output .= "</li>\n";
    }

    $output .= '</ul>';
  }

  return $output;
}

/**
 * theme_bootstrap_btn_dropdown
 *
 * @param array $vars
 * @return string
 */
function theme_bootstrap_btn_dropdown($vars) {
  $type_class = '';
  $sub_links ='';

  $vars['attributes']['class'][] = 'btn-group';
  // Type class
  if (isset($vars['type'])) {
    $type_class = ' btn-'. $vars['type'];
  }

  // Start markup
  $output = '<div'. drupal_attributes($vars['attributes']) .'>';

  // Ad as string if its not a link
  if (is_array($vars['label'])) {
    $output .= l($vars['label']['title'], $$vars['label']['href'], $vars['label']);
  }

  $output .= '<button type="button" class="btn'. $type_class .' dropdown-toggle" data-toggle="dropdown">';

  // Its a link so create one
  if (is_string($vars['label'])) {
    $output .= check_plain($vars['label']);
  }

  if (is_array($vars['links'])) {
    $sub_links = theme('links', array('links' => $vars['links'],'attributes' => array('class' => array('dropdown-menu'))));
  }

  // Finish markup
  $output .= '<span class="caret"></span></button>' . $sub_links . '</div>';

  return $output;
}

/**
 * Overwrites the default item list to make it prettier
 *
 * @param array $vars
 * @return string
 */
function wl_item_list($vars) {
  $items = $vars['items'];
  $title = $vars['title'];
  $type = $vars['type'];
  $attributes = $vars['attributes'];
  $output = '';

  if (isset($title)) {
    $output .= '<h3>' . $title . '</h3>';
  }

  if (!empty($items)) {
    $output .= "<{$type}" . drupal_attributes($attributes) . '>';
    $num_items = count($items);
    $i = 0;
    foreach ($items as $item) {
      $attributes = array();
      $children = array();
      $data = '';
      $i++;
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {
        // Render nested list.
        $data .= theme_item_list(array('items' => $children, 'title' => NULL, 'type' => $type, 'attributes' => $attributes));
      }
      if ($i == 1) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_items) {
        $attributes['class'][] = 'last';
      }
      $output .= '<li' . drupal_attributes($attributes) . '>' . $data . "</li>\n";
    }
    $output .= "</{$type}>";
  }
 
  return $output;
}

/**
 * Returns HTML for status and/or error messages, grouped by type.
 *
 * @param array $vars
 * @return string
 */
function wl_status_messages($vars) {
  $display = $vars['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
    'info' => t('Informative message'),
  );

  // Map Drupal message types to their corresponding Bootstrap classes.
  // @see http://twitter.github.com/bootstrap/components.html#alerts
  $status_class = array(
    'status' => 'success',
    'error' => 'error',
    'warning' => 'warning',
    // Not supported, but in theory a module could send any type of message.
    // @see drupal_set_message()
    // @see theme_status_messages()
    'info' => 'info',
  );

  foreach (drupal_get_messages($display) as $type => $messages) {
    $class = (isset($status_class[$type])) ? ' alert-' . $status_class[$type] : '';
    $output .= "<div class=\"alert alert-block{$class}\">\n";
    $output .= "  <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>\n";

    if (!empty($status_heading[$type])) {
      $output .= '<h4 class="element-invisible">' . $status_heading[$type] . "</h4>\n";
    }

    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];
    }

    $output .= "</div>\n";
  }
  return $output;
}

/**
 * Implements hook_css_alter().
 *
 * @param array &$css
 */
function wl_css_alter(&$css) {
  $theme_path = drupal_get_path('theme', 'wl');
  // Load excluded CSS files from theme.
  $excludes = _wl_alter(wl_theme_get_info('exclude'), 'css');
  // Add Bootstrap CDN file and overrides.
  if (theme_get_setting('cdn_bootstrap')){
    
    $bootstrap_version = theme_get_setting('cdn_bootstrap_version');
    $cdn = "//netdna.bootstrapcdn.com/twitter-bootstrap/{$bootstrap_version}/css/bootstrap-combined.min.css";
    if(substr($bootstrap_version, 0, 2) === "3.") {
      $cdn = "//netdna.bootstrapcdn.com/bootstrap/{$bootstrap_version}/css/bootstrap.min.css";
    }

    $css[$cdn] = array(
      'data' => $cdn,
      'type' => 'external',
      'every_page' => TRUE,
      'media' => 'all',
      'preprocess' => TRUE,
      'group' => CSS_THEME,
      'browsers' => array('IE' => TRUE, '!IE' => TRUE),
      'weight' => -2,
    );

    // Add overrides.
    $override = $theme_path . '/css/overrides.css';
    $css[$override] = array(
      'data' => $override,
      'type' => 'file',
      'every_page' => TRUE,
      'media' => 'all',
      'preprocess' => TRUE,
      'group' => CSS_THEME,
      'browsers' => array('IE' => TRUE, '!IE' => TRUE),
      'weight' => -1,
    );
  }
  $css = array_diff_key($css, $excludes);
}

/**
 * Implements hook_js_alter().
 *
 * @param array &$js
 */
function wl_js_alter(&$js) {
  $files = array();

  // Load excluded JS files from theme.
  $excludes = _wl_alter(wl_theme_get_info('exclude'), 'js');

  // If bootstrap_ui available, we should not add js from theme info file.
  if (module_exists('bootstrap_ui')) {
    libraries_load('bootstrap', 'minified');
  }

  $theme_path = drupal_get_path('theme', 'wl');

  // Replace core progress bar JS with the Bootstrap equivalent.
  if (isset($js['misc/progress.js']) && !empty($js['misc/progress.js'])) {
    unset($js['misc/progress.js']);
    $progress = $theme_path . '/js/progress.js';
    $js[$progress] = drupal_js_defaults($progress);
  }

  // Fix broken Views AJAX pagers.
  if (module_exists('views') && !empty($js[drupal_get_path('module', 'views') . '/js/ajax_view.js'])) {
    // Override core ajax prototype function to append throbber inside links,
    // instead of after it (floating issues).
    $ajax = $theme_path . '/js/bootstrap_ajax.js';
    $js[$ajax] = drupal_js_defaults($ajax);
    $js[$ajax]['group'] = JS_THEME;
    // Override views prototype function to bind to Bootstrap pagination.
    $ajax_view = $theme_path . '/js/bootstrap_ajax_view.js';
    $js[$ajax_view] = drupal_js_defaults($ajax_view);
    $js[$ajax_view]['group'] = JS_THEME;
  }

  if(theme_get_setting('cdn_bootstrap')) {
    $bootstrap_version = theme_get_setting('cdn_bootstrap_version');
    $cdn = "//netdna.bootstrapcdn.com/twitter-bootstrap/{$bootstrap_version}/js/bootstrap.min.js";

    if(substr($bootstrap_version, 0, 2) === "3.") {
      $cdn = "//netdna.bootstrapcdn.com/bootstrap/{$bootstrap_version}/js/bootstrap.min.js";
    }

    $files[] = $cdn;
  }

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

  // Rearrange / Add JS
  $group = -50;
  $weight = -100;
  foreach ($files as $file) {
    if (!isset($js[$file])) {
      $js[$file] = drupal_js_defaults();
      $js[$file]['data'] = $file;
      $js[$file]['group'] = $group;
      $js[$file]['weight'] = $weight;
      $weight++;
    }
  }
}

/**
 * Get a list of keys in a file array
 *
 * Used to properly exclude files
 *
 * @param array $files
 * @param string $type
 * @return array
 */
function _wl_alter($files, $type) {
  $output = array();
  
  foreach($files as $key => $value) {
    if (isset($files[$key][$type])) {
      foreach ($files[$key][$type] as $file => $name) {
        $output[$name] = FALSE;
      }
    }
  }
  return $output;
}

/**
 * Returns HTML for Bootstrap's progress bar.
 *
 * @param array $vars
 * @return string HTML
 */
function wl_progress_bar($vars) {
  $output .= '<div id="progress" class="progress-wrapper">';
  $output .= '  <div class="progress progress-striped active">';
  $output .= '    <div class="progress-bar progress-bar-info" style="width: ' . $vars['percent'] . '%"></div>';
  $output .= '  </div>';
  $output .= '  <div class="percentage pull-right">' . $vars['percent'] . '%</div>';
  $output .= '  <div class="message">' . $vars['message'] . '</div>';
  $output .= '</div>';
  return $output;
}

/**
 * Returns HTML for Bootstrap's modal.
 *
 * @param array $vars
 * @return string HTML
 */
function wl_bootstrap_modal($vars) {
  $body = $vars['body'];
  $links = $vars['footer'];
  $heading = $vars['heading'];
  $attributes = $vars['attributes'];

  if (isset($vars['attributes']['class']) && is_string($vars['attributes']['class'])) { // Convert classes to an array.
    $vars['attributes']['class'] = explode(' ', $vars['attributes']['class']);
  }

  $vars['attributes']['class'][] = 'modal';
  $vars['attributes']['class'][] = 'fade';

  $output = '<div'. drupal_attributes($vars['attributes']) .'>';
  $output .= '<div class="modal-dialog">';
  $output .= '<div class="modal-content">';  
  $output .= '<div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
  $output .= '<h4>'. $vars['html_heading']? $vars['heading'] : check_plain($vars['heading']) .'</h4>';
  $output .= '</div>';
  $output .= '<div class="modal-body">'. render($vars['body']) .'</div>';
  $output .= '<div class="modal-footer">'. render($vars['footer']) .'</div>';
  $output .= '</div>';
  $output .= '</div>';
  $output .= '</div>';

  return $output;
}

/**
 * Returns HTML for Bootstrap's Collapse/Accordion.
 *
 * @param array $vars
 * @return string HTML
 */
function wl_bootstrap_accordion($vars) {
  $elements = $vars['elements'];

  if(empty($vars['id'])) {
    $accordion_id = 'accordion-'. md5($elements);
  }
  else {
    $accordion_id = check_plain($vars['id']);
  }

  $output = '<div class="accordion" id="'.$accordion_id.'">';

  foreach ($elements as $id => $item) {
    $output .= '<div class="accordion-group"><div class="accordion-heading">';
    $output .= '<a class="accordion-toggle" data-toggle="collapse" data-parent="#'. $accordion_id .'" href="#'. $id .'">'. check_plain($item['header']) .'</a></div>';
    $output .= '<div id="'. $id .'" class="accordion-body collapse in"><div class="accordion-inner">';
    $output .= render($item['content']);
    $output .= '</div></div></div>';
  }

  $output .= '</div>';

  return $output;
}

/**
 * Add stripping to tables (in case they don't have .table-no-striping class)
 *
 * @param array $vars
 * @return string HTML
 */
function wl_preprocess_table(&$vars) {

  if (isset($vars['attributes']['class']) && is_string($vars['attributes']['class'])) {
    // Convert classes to an array.
    $vars['attributes']['class'] = explode(' ', $vars['attributes']['class']);
  }

  $vars['attributes']['class'][] = 'table';
  if(!in_array('table-no-striping', $vars['attributes']['class'])) {
    $vars['attributes']['class'][] = 'table-striped';
  }
  
}
