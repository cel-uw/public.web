<?php

// Provide < PHP 5.3 support for the __DIR__ constant.
if (!defined('__DIR__')) {
  define('__DIR__', dirname(__FILE__));
}
require_once __DIR__ . '/includes/bootstrap.inc';
require_once __DIR__ . '/includes/theme.inc';
require_once __DIR__ . '/includes/pager.inc';
require_once __DIR__ . '/includes/form.inc';
require_once __DIR__ . '/includes/admin.inc';
require_once __DIR__ . '/includes/menu.inc';

// Load module specific files in the modules directory.
$includes = file_scan_directory(__DIR__ . '/includes/modules', '/\.inc$/');
foreach ($includes as $include) {
  if (module_exists($include->name)) {
    require_once $include->uri;
  }
}

if (theme_get_setting('wl_rebuild_registry') && !defined('MAINTENANCE_MODE')) {
  // Rebuild .info data.
  system_rebuild_theme_data();
  // Rebuild theme registry.
  drupal_theme_rebuild();
}

/**
 * Implements hook_theme().
 *
 * @param array &$existing Existing implementations
 * @param string $type The type of object being processed
 * @param string $theme The name of the object being processed
 * @param string $path The path to the object being processed
 * @return array Array of new theme information
 */
function wl_theme(&$existing, $type, $theme, $path) {
  // If we are auto-rebuilding the theme registry, warn about the feature.
  if (
    // Only display for site config admins.
    isset($GLOBALS['user']) && function_exists('user_access') && user_access('administer site configuration')
    && theme_get_setting('wl_rebuild_registry')
    // Always display in the admin section, otherwise limit to three per hour.
    && (arg(0) == 'admin' || flood_is_allowed($GLOBALS['theme'] . '_rebuild_registry_warning', 3))
  ) {
    flood_register_event($GLOBALS['theme'] . '_rebuild_registry_warning');
    drupal_set_message(t('For easier theme development, the theme registry is being rebuilt on every page request. It is <em>extremely</em> important to <a href="!link">turn off this feature</a> on production websites.', array('!link' => url('admin/appearance/settings/' . $GLOBALS['theme']))), 'warning', FALSE);
  }
  
  return array(
    'bootstrap_links' => array(
      'variables' => array(
        'links' => array(),
        'attributes' => array(),
        'heading' => NULL
      ),
    ),
    'bootstrap_btn_dropdown' => array(
      'variables' => array(
        'links' => array(),
        'attributes' => array(),
        'type' => NULL
      ),
    ),
    'bootstrap_modal' => array(
      'variables' => array(
        'heading' => '',
        'body' => '',
        'footer' => '',
        'attributes' => array(),
        'html_heading' => FALSE,
      ),
    ),
    'bootstrap_accordion' => array(
      'variables' => array(
        'id' => '',
        'elements' => array(),
      ),
    ),
    'bootstrap_search_form_wrapper' => array(
      'render element' => 'element',
    ),
    'bootstrap_append_element' => array(
      'render element' => 'element',
    ),
  );
}

/**
 * Override theme_breadrumb().
 *
 * Print breadcrumbs as a list, with separators.
 *
 * @param array $vars
 * @return string Breadcrumb HTML
 */
function wl_breadcrumb($vars) {
  $breadcrumb = $vars['breadcrumb'];

  if (!empty($breadcrumb)) {
    $breadcrumbs = '<ul class="breadcrumb">';
    
    foreach ($breadcrumb as $key => $value) {
      $breadcrumbs .= '<li>' . $value . '</li>';
    }
    $breadcrumbs .= '</ul>';
    
    return $breadcrumbs;
  }
}

/**
 * Override or insert variables in the html_tag theme function.
 *
 * @param array &$vars
 */
function wl_process_html_tag(&$vars) {
  $tag = &$vars['element'];

  if ($tag['#tag'] == 'style' || $tag['#tag'] == 'script') {
    // Remove redundant type attribute and CDATA comments.
    unset($tag['#attributes']['type'], $tag['#value_prefix'], $tag['#value_suffix']);

    // Remove media="all" but leave others unaffected.
    if (isset($tag['#attributes']['media']) && $tag['#attributes']['media'] === 'all') {
      unset($tag['#attributes']['media']);
    }
  }
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

  // Primary nav
  $vars['primary_nav'] = FALSE;
  if ($vars['main_menu']) {
    // Build links
    $vars['primary_nav'] = menu_tree(variable_get('menu_main_links_source', 'main-menu'));
    // Provide default theme wrapper function
    $vars['primary_nav']['#theme_wrappers'] = array('menu_tree__primary');
  }

  // Secondary nav
  $vars['secondary_nav'] = FALSE;
  if ($vars['secondary_menu']) {
    // Build links
    $vars['secondary_nav'] = menu_tree(variable_get('menu_secondary_links_source', 'user-menu'));
    // Provide default theme wrapper function
    $vars['secondary_nav']['#theme_wrappers'] = array('menu_tree__secondary');
  }
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

/**
 * Bootstrap theme wrapper function for the primary menu links
 *
 * @param array &$vars
 */
function wl_menu_tree__primary(&$vars) {
  return '<ul class="menu nav navbar-nav">' . $vars['tree'] . '</ul>';
}

/**
 * Bootstrap theme wrapper function for the secondary menu links
 *
 * @param array &$vars
 */
function wl_menu_tree__secondary(&$vars) {
  return '<ul class="menu nav navbar-nav pull-right">' . $vars['tree'] . '</ul>';
}

/**
 * Returns HTML for a single local action link.
 *
 * This function overrides theme_menu_local_action() to add the icons that ship
 * with Bootstrap to the action links.
 *
 * @param $vars
 *   An associative array containing:
 *   - element: A render element containing:
 *     - #link: A menu link array with "title", "href", "localized_options", and
 *       "icon" keys. If "icon" is not passed, it defaults to "plus-sign".
 *
 * @ingroup themeable
 *
 * @see theme_menu_local_action().
 */
function wl_menu_local_action($vars) {
  $link = $vars['element']['#link'];

  // Build the icon rendering element.
  if(empty($link['icon'])) {
    $link['icon'] = 'plus';
  }
  $icon = '<i class="glyphicon ' . drupal_clean_css_identifier('glyphicon-' . $link['icon']) . '"></i>';

  // Format the action link.
  $output = '<li>';
  if(isset($link['href'])) {
    $options = isset($link['localized_options']) ? $link['localized_options'] : array();

    // If the title is not HTML, sanitize it.
    if(empty($link['localized_options']['html'])) {
      $link['title'] = check_plain($link['title']);
    }

    // Force HTML so we can add the icon rendering element.
    $options['html'] = TRUE;
    $output .= l($icon . $link['title'], $link['href'], $options);
  }
  else if(!empty($link['localized_options']['html'])) {
    $output .= $icon . $link['title'];
  } else {
    $output .= $icon . check_plain($link['title']);
  }
  $output .= "</li>\n";

  return $output;
}

/**
 * Preprocess vars for region.tpl.php
 *
 * @see region.tpl.php
 * @param array &$vars
 * @param string $hook
 */
function wl_preprocess_region(&$vars, $hook) {
  if($vars['region'] == 'content') {
    $vars['theme_hook_suggestions'][] = 'region__no_wrapper';
  }
  
  if($vars['region'] == "sidebar_first") {
    $vars['classes_array'][] = 'well';
  }
}

/**
 * Preprocess variables for block.tpl.php
 *
 * @see block.tpl.php
 * @param array &$vars
 * @param string $hook
 */
function wl_preprocess_block(&$vars, $hook) {
  //$vars['classes_array'][] = 'row';
  // Use a bare template for the page's main content.
  if ($vars['block_html_id'] == 'block-system-main') {
    $vars['theme_hook_suggestions'][] = 'block__no_wrapper';
  }
  $vars['title_attributes_array']['class'][] = 'block-title';
}

/**
 * Override or insert variables into the block templates.
 *
 * @param array &$vars An array of variables to pass to the theme template.
 * @param string $hook The name of the template being rendered ("block" in this case.)
 */
function wl_process_block(&$vars, $hook) {
  // Drupal 7 should use a $title variable instead of $block->subject.
  $vars['title'] = $vars['block']->subject;
}

/**
 * Adds the search form's submit button right after the input element.
 *
 * @ingroup themable
 * @param array &$vars
 * @return string HTML for the search form
 */
function wl_bootstrap_search_form_wrapper(&$vars) {
  $search_text = t('Search');
  $output = <<<EOL
<div class="input-group">
  {$vars['element']['#children']}
  <span class="input-group-btn">
    <button type="button" class="btn btn-default">
      <span class="element-invisible">{$search_text}</span>
      <i class="glyphicon glyphicon-search"></i>
    </button>
  </span>
</div>
EOL;
  return $output;
}