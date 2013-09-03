<?php
/**
 * We need to kill media_vimeo scripts
 *
 * @param array &$js Javascript files
 */
function cel_js_alter(&$js) {
  unset($js['sites/all/modules/media_vimeo/js/media_vimeo.js']);
  unset($js['sites/all/modules/media_vimeo/js/flash_detect_min.js']);
}

/**
 * Merge content in the navigation block into the nav itself
 *
 * @param array &$vars
 */
function cel_preprocess_page(&$vars) {
  if(empty($vars['primary_nav'])) {
    $vars['primary_nav'] = array(
      '#sorted' => true,
      '#theme_wrappers' => array('menu_tree__primary'),
    );
  }

  if(empty($vars['secondary_nav'])) {
    $vars['secondary_nav'] = array(
      '#sorted' => true,
      '#theme_wrappers' => array('menu_tree__secondary'),
    );
  }

  // Primary nav
  $vars['footer_nav'] = FALSE;
  // Build links
  $vars['footer_nav'] = menu_tree('menu-footer-menu');
  // Provide default theme wrapper function
  $vars['footer_nav']['#theme_wrappers'] = array('menu_tree__footer');

  if(!empty($vars['page']['navigation'])) {
    $vars['primary_nav'] = _cel_merge_nav_and_block($vars['primary_nav'], $vars['page']['navigation'], array('#theme' => array('menu_link__main_menu')));
    unset($vars['page']['navigation']);
  }

  if(!empty($vars['page']['secondary_navigation'])) {
    $vars['secondary_nav'] = _cel_merge_nav_and_block($vars['secondary_nav'], $vars['page']['secondary_navigation'], array('#theme' => array('menu_link__secondary_menu')));
    unset($vars['page']['secondary_navigation']);
  }
}

/**
 * Add in some CEL-specific vars
 *
 * Implementes hook_preprocess_node()
 *
 * @param array &$vars
 */
function cel_preprocess_node(&$vars) {
  $node = node_load($vars['nid']);

  $new_vars = array(
    'cel_collapsible' => false,
    'cel_collapsed' => false,
    'cel_collapse_content_classes' => "",
  );

  if(!empty($node)) {
    // Get the vars
    $collapsible_items = field_get_items('node', $node, 'field_collapsible');
    if(!empty($collapsible_items)) {
      $collapsible = reset($collapsible_items);
      $new_vars['cel_collapsible'] = !empty($collapsible['value']);
    }

    $collapsed_items = field_get_items('node', $node, 'field_collapsed');
    if(!empty($collapsed_items) && $new_vars['cel_collapsible']) {
      $collapsed = reset($collapsed_items);
      $new_vars['cel_collapsed'] = !empty($collapsed['value']);
    }
    
    $cel_collapse_content_classes = array();
    if($new_vars['cel_collapsible']) {
      $cel_collapse_content_classes[] = "collapse";
    }
    if(!$new_vars['cel_collapsed']) {
      $cel_collapse_content_classes[] = "in";
    }

    $new_vars['cel_collapse_content_classes'] = implode(" ", $cel_collapse_content_classes);

    // If we have any per-content type functions
    // We should do this sparingly--even the above collapsible stuff, while only applying to blurbs right now
    // might later be extended to other content types, so we keep it generic
    $function_name = "cel_preprocess_node_{$node->type}";
    if(!empty($node->type) && function_exists($function_name)) {
      $function_name($node, $vars);
    }
  }

  //Merge in the new vars
  $vars = array_merge($vars, $new_vars);
}

/**
 * Add in vars specific for the 5D video content type
 *
 * Called by cel_preprocess_node()
 *
 * @param object $node The node object
 * @param array &$vars The vars array
 */
function cel_preprocess_node_5d_video($node, &$vars) {
  $new_vars = array(
    'cel_5d_video_code' => "",
  );

  $video_code_items = field_get_items('node', $node, 'field_uwtv_video_code');
  if(!empty($video_code_items)) {
    foreach($video_code_items as $value) {
      $video_code_item = field_view_value('node', $node, 'field_uwtv_video_code', $value);
      $new_vars['cel_5d_video_code'] .= render($video_code_item);
    }
  }

  //Merge in the new vars
  $vars = array_merge($vars, $new_vars);
}

/**
 * Strech vimeo videos to their containers full width
 *
 * Implements hook_preprocess_media_vimeo_video()
 *
 * @params array &$vars
 */
function cel_preprocess_media_vimeo_video(&$vars) {

  $vars['autoplay'] = !empty($vars['autoplay']) ? '1' : '0';
  $vars['fullscreen_attrs'] = !empty($vars['fullscreen']) ? "webkitAllowFullScreen mozallowfullscreen allowFullScreen" : "";
  $vars['output'] = <<<OUTPUT
    <iframe id="{$vars['wrapper_id']}_iframe" class="vimeo-player" src="http://player.vimeo.com/video/{$vars['video_id']}?player_id={$vars['wrapper_id']}_iframe&amp;api=1&amp;autoplay={$vars['autoplay']}" width="{$vars['width']}" height="{$vars['height']}" frameborder="0" {$vars['fullscreen_attrs']}></iframe>
OUTPUT;
}

/**
 * Merge the contents of a block into the links in a menu
 *
 * @param array $menu The menu to merge into
 * @param array $block The block to merge
 * @param array $block_settings Any settings to add to the merged in block
 * @return array The modified menu
 */
function _cel_merge_nav_and_block($menu, $block, $block_settings = array()) {
  // Find the last link
  // This seems to be the safest, if most inefficient, option
  $splice_offset = 0;
  foreach($menu as $key=>$link) {
    $splice_offset++;
    if(!isset($link['#theme'], $link['#attributes']['class'])) {
      continue;
    }

    $last = array_search('last', $link['#attributes']['class'], true);
    if($last !== false) {
      unset($menu[$key]['#attributes']['class'][$last]);
      break;
    }
  }

  // Prep our navigation content
  $last_key = NULL;
  foreach($block as $key=>$value) {
    if($key[0] === "#") {
      unset($block[$key]);
      continue;
    }

    $last_key = $key;
  }

  foreach($block as $key=>$value) {
    $classes = "leaf";
    if($key === $last_key) {
      $classes .= " last";
    }

    if(empty($block[$key]['#theme'])) {
      $block[$key]['#theme'] = array();
    }

    $block[$key]['#prefix'] = "<li class=\"{$classes}\">";
    $block[$key]['#suffix'] = '</li>';
    foreach($block_settings as $setting=>$settings) {
      if(isset($block[$key][$setting]) && is_array($block[$key][$setting])) {
        $block[$key][$setting] = array_merge($block[$key][$setting], $settings);
      }
    }
  }

  // Splice in our navigation content
  array_splice($menu, $splice_offset, 0, $block);
  return $menu;
}

/**
 * Implements hook_form_FORM_ID_alter() for search_form().
 *
 * @param array &$form
 * @param array &$form_state
 * @param string $form_id
 */
function cel_form_search_block_form_alter(&$form, &$form_state, $form_id) {
  $form['#attributes']['class'][] = 'navbar-form';
}

/**
 * Bootstrap theme wrapper function for the primary menu links
 *
 * @param array &$vars
 */
function cel_menu_tree__primary(&$vars) {
  return '<ul class="menu nav navbar-nav nav-justified">' . $vars['tree'] . '</ul>';
}

/**
 * Bootstrap theme wrapper function for the primary menu links
 *
 * @param array &$vars
 */
function cel_menu_tree__secondary(&$vars) {
  return '<ul class="menu nav navbar-nav nav-justified">' . $vars['tree'] . '</ul>';
}

/**
 * Bootstrap theme wrapper function for the primary menu links
 *
 * @param array &$vars
 */
function cel_menu_tree__footer(&$vars) {
  return '<div class="row footer-menu">' . $vars['tree'] . '</div>';
}

/**
 * Renders the HTML for the main menu
 *
 * @param array $vars
 * @return string The HTML to render
 */
function cel_menu_link__menu_block__main_menu($vars) {
  $element = $vars['element'];
  $sub_menu = '';

  // Issue #1896674 - On primary navigation menu, class 'active' is not set on active menu item.
  // @see http://drupal.org/node/1896674
  if (($element['#href'] == $_GET['q'] || ($element['#href'] == '<front>' && drupal_is_front_page())) && 
      (empty($element['#localized_options']['language']) || $element['#localized_options']['language']->language == $language_url->language)) 
  {
    $element['#attributes']['class'][] = 'active';
  }

  if(empty($element['#original_link']['depth'])) {
    // Prevent dropdown functions from being added to management menu as to not affect navbar module.
    if ($element['#below'] && $element['#original_link']['menu_name'] == 'management' && module_exists('navbar')) {
      $sub_menu = drupal_render($element['#below']);
    }

    $output = l($element['#title'], $element['#href'], $element['#localized_options']);
    return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
  }

  if($element['#below']) {
    // Add our own wrapper
    unset($element['#below']['#theme_wrappers']);
    $sub_links = drupal_render($element['#below']);
    $sub_menu = <<<EOL
<ul class="nav">
  {$sub_links}
</ul>
EOL;
  }

  $element['#localized_options']['html'] = TRUE;
  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";

  drupal_set_message(t('An error occurred when loading the menu.'));
}

/**
 * Returns HTML for the main menu links
 *
 * @param array $vars
 * @return string HTML output
 */
function cel_menu_link__main_menu($vars) {
  $element = $vars['element'];
  $sub_menu = '';

  // Issue #1896674 - On primary navigation menu, class 'active' is not set on active menu item.
  // @see http://drupal.org/node/1896674
  if (($element['#href'] == $_GET['q'] || ($element['#href'] == '<front>' && drupal_is_front_page())) && 
      (empty($element['#localized_options']['language']) || $element['#localized_options']['language']->language == $language_url->language)) 
  {
    $element['#attributes']['class'][] = 'active';
  }

  if(empty($element['#original_link']['depth'])) {
    // Prevent dropdown functions from being added to management menu as to not affect navbar module.
    if ($element['#below'] && $element['#original_link']['menu_name'] == 'management' && module_exists('navbar')) {
      $sub_menu = drupal_render($element['#below']);
    }

    $output = l($element['#title'], $element['#href'], $element['#localized_options']);
    return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
  }

  switch($element['#original_link']['depth']) {
    case 1:
      // Top level
      if($element['#below']) {
        // Add our own wrapper
        unset($element['#below']['#theme_wrappers']);
        $sub_links = drupal_render($element['#below']);
        $sub_menu = <<<EOL
<ul class="dropdown-menu">
  <li class="first">
    <div class="container">
      {$sub_links}
    </div>
  </li>
</ul>
EOL;
        $element['#localized_options']['attributes']['class'][] = 'dropdown-toggle';
        $element['#localized_options']['attributes']['data-toggle'] = 'dropdown';

        // Generate as standard dropdown
        $element['#attributes']['class'][] = 'dropdown';
        $element['#localized_options']['html'] = TRUE;
        $element['#title'] .= ' <span class="caret"></span>';

        // Set dropdown trigger element to # to prevent inadvertant page loading with submenu click
        $element['#localized_options']['attributes']['data-target'] = '#';
      }

      $output = l($element['#title'], $element['#href'], $element['#localized_options']);
      return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
      break;

    case 2:
      // First level submenu
      if($element['#below']) {
        // Add our own wrapper
        unset($element['#below']['#theme_wrappers']);
        $sub_menu = '<ul class="nav">' . drupal_render($element['#below']) . '</ul>';
      }

      $element['#localized_options']['html'] = TRUE;
      $output = l($element['#title'], $element['#href'], $element['#localized_options']);
      return '<ul class="nav col-lg-4"><li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li></ul>\n";
      break;

    default:
      // Lower levels
      if($element['#below']) {
        // Add our own wrapper
        unset($element['#below']['#theme_wrappers']);
        $sub_menu = '<ul class="nav">' . drupal_render($element['#below']) . '</ul>';
      }

      $element['#localized_options']['html'] = TRUE;
      $output = l($element['#title'], $element['#href'], $element['#localized_options']);
      return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
  }

  drupal_set_message(t('An error occurred when loading the menu.'));
}

/**
 * Returns HTML for the footer links
 *
 * @param array $vars
 * @return string HTML output
 */
function cel_menu_link__menu_footer_menu($vars) {
  $element = $vars['element'];
  $sub_menu = '';

  // Issue #1896674 - On primary navigation menu, class 'active' is not set on active menu item.
  // @see http://drupal.org/node/1896674
  if (($element['#href'] == $_GET['q'] || ($element['#href'] == '<front>' && drupal_is_front_page())) && 
      (empty($element['#localized_options']['language']) || $element['#localized_options']['language']->language == $language_url->language)) 
  {
    $element['#attributes']['class'][] = 'active';
  }

  if(empty($element['#original_link']['depth'])) {
    // Prevent dropdown functions from being added to management menu as to not affect navbar module.
    if ($element['#below'] && $element['#original_link']['menu_name'] == 'management' && module_exists('navbar')) {
      $sub_menu = drupal_render($element['#below']);
    }

    $output = l($element['#title'], $element['#href'], $element['#localized_options']);
    return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
  }

  if($element['#below']) {
    // Add our own wrapper
    unset($element['#below']['#theme_wrappers']);
    $sub_links = drupal_render($element['#below']);
    $sub_menu = <<<EOL
<ul class="nav">
  {$sub_links}
</ul>
EOL;
  }

  switch($element['#original_link']['depth']) {
    case 1:
      // Top level
      $output = l($element['#title'], $element['#href'], $element['#localized_options']);
      return '<div class="footer-menu-item col-sm-3"><h3' . drupal_attributes($element['#attributes']) . '>' . $output . '</h3>' . $sub_menu . "</div>\n";
      break;

    default:
      $element['#localized_options']['html'] = TRUE;
      $output = l($element['#title'], $element['#href'], $element['#localized_options']);
      return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
  }

  drupal_set_message(t('An error occurred when loading the menu.'));
}

/**
 * Remove the height attribute from pictures and set width to 100% to support scaling on small devices
 *
 * Implements hook_preprocess_theme()
 * Preprocesses theme_picture_source()
 *
 * @param array &$vars
 */
function cel_preprocess_picture_source(&$vars) {
  $vars['dimensions'] = array('width' => '100%');
}

/**
 * Implements hook_form_FORM_ID_alter() for exposed filters in views.
 *
 * Change the partners default select from the unhelpful "- Any -" to "All partners".
 *
 * @param array &$form
 * @param array &$form_state
 * @param string $form_id
 */
function cel_form_views_exposed_form_alter(&$form, &$form_state, $form_id) {
  if($form_state['view']->name === 'partners' && isset($form['field_current_value']['#options']['All'])) {
    $form['field_current_value']['#options']['All'] = t('All partners');
  }
}