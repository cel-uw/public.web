<?php
// $Id: template.php, $



/**
 * Generates leftsidebar menu for n-tier primary links
 */ 
function cel_lms_render_second_tier() {
  $output = '';
  
      // dynamically generate archive links
	$is_news = false;
	if (( arg(0) == 'taxonomy') && ( arg(1) == 'term') ) {
			$term = taxonomy_get_term(arg(2));
			if ( $term->vid == 5 ) //News Category
				$is_news = true;
	}

	if ( (arg(0) == 'node') && ($node = node_load(arg(1))) ) {
		if ( $node->type == 'news' )
			$is_news = true;
	}
	
     if($is_news or (arg(0) == 'news')) {
       $output = '<h3 class="menu-name">Blog Archives</h3>';
       $output .= cel_workflow_news_menu();  
       return $output;  
     }     
  
  
  $primary_links_tree = menu_tree_page_data('primary-links');
  // find the active branch and output
  foreach ($primary_links_tree as $menu_item) {
    if ($menu_item['link']['in_active_trail'] && $menu_item['link']['title'] != 'Video Assessment') {
      $active_branch = $menu_item['below'];
      if (is_array($active_branch)) {
        //watchdog('fusetrace', '$menu_item = '.print_r($menu_item,true));
        if($menu_children = menu_tree_output($active_branch)) {
          $output .= '<h3 class="menu-name">'.$menu_item['link']['title'].'</h3>';
          $output .= $menu_children;          
        }
      }
      
        
    }
  }  
  
  
  return $output;
}

/**
 * Remove colons
 *
 * @param unknown_type $element
 * @param unknown_type $value
 * @return unknown
 */
function phptemplate_form_element($element, $value) {
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  $output = '<div class="form-item"';
  if (!empty($element['#id'])) {
    $output .= ' id="'. $element['#id'] .'-wrapper"';
  }
  $output .= ">\n";
  $required = !empty($element['#required']) ? '<span class="form-required" title="'. $t('This field is required.') .'">*</span>' : '';

  if (!empty($element['#title'])) {
    $title = $element['#title'];
    if (!empty($element['#id'])) {
      $output .= ' <label for="'. $element['#id'] .'">'. $t('!title !required', array('!title' => filter_xss_admin($title), '!required' => $required)) ."</label>\n";
    }
    else {
      $output .= ' <label>'. $t('!title !required', array('!title' => filter_xss_admin($title), '!required' => $required)) ."</label>\n";
    }
  }

  $output .= " $value\n";

  if (!empty($element['#description'])) {
    $output .= ' <div class="description">'. $element['#description'] ."</div>\n";
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Generate the HTML representing a given menu item ID.
 *
 * An implementation of theme_menu_item_link()
 *
 * @param $link
 *   array The menu item to render.
 * @return
 *   string The rendered menu item.
 */
function phptemplate_menu_item_link($link) {
  if (empty($link['options'])) {
    $link['options'] = array();
  }

  // If an item is a LOCAL TASK, render it as a tab
  if ($link['type'] & MENU_IS_LOCAL_TASK) {
  	if ( $link['title'] == 'Children') {
  		$link['title'] = 'Documents';
  	}
  	
    $link['title'] = '<span class="tab">' . check_plain($link['title']) . '</span>';
    $link['options']['html'] = TRUE;
  }

  if (empty($link['type'])) {
    $true = TRUE;
  }

  return l($link['title'], $link['href'], $link['options']);
}

/**
 * Duplicate of theme_menu_local_tasks() but adds clear-block to tabs.
 */
function phptemplate_menu_local_tasks() {
  $output = '';

  if ($primary = menu_primary_local_tasks()) {
    $output .= '<ul class="tabs primary clear-block">' . $primary . '</ul>';
  }
  if ($secondary = menu_secondary_local_tasks()) {
    $output .= '<ul class="tabs secondary clear-block">' . $secondary . '</ul>';
  }

  return $output;
}

/**
 * Override breadcrumb trail theme.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    return 
      '<div class="breadcrumb">'
      . implode('/', array_map(create_function('$a', 'return strtolower($a);'), $breadcrumb))
      . '</div>'
    ;
  }
}

/**
 * Override or insert PHPTemplate variables into the page templates.
 *
 * @param $vars
 *   A sequential array of variables to pass to the theme template.
 * @param $hook
 *   The name of the theme function being called ("page" in this case.)
 */
function phptemplate_preprocess_page(&$vars, $hook) {
  global $theme;
  
  drupal_add_css(path_to_theme() . '/styles/typography.css', 'theme', 'all');
  drupal_add_css(path_to_theme() . '/styles/form.css', 'theme', 'all');
  drupal_add_css(path_to_theme() . '/styles/style.css', 'theme', 'all');

  // Optionally add the wireframes style.
  if (theme_get_setting('basic_wireframes')) {
    drupal_add_css(path_to_basictheme() . '/styles/wireframes.css', 'theme', 'all');
  }
  

  $vars['css'] = drupal_add_css();
//  $vars['styles'] = drupal_get_css();

  // Allow sub-themes to have an ie.css file
  $vars['basictheme_directory'] = path_to_basictheme();

  // Add an optional title to the end of the breadcrumb.
  if (theme_get_setting('zen_breadcrumb_title') && $vars['breadcrumb']) {
    $vars['breadcrumb'] = substr($vars['breadcrumb'], 0, -6) . $vars['title'] . '</div>';
  }

  // Don't display empty help from node_help().
  if ($vars['help'] == "<div class=\"help\"><p></p>\n</div>") {
    $vars['help'] = '';
  }

  //node hierarchy string replacement
  $vars['title'] = str_replace('Children of','Documents of', $vars['title']);
  $vars['head_title'] = str_replace('Children of','Documents of', $vars['head_title']);
  
  // Classes for body element. Allows advanced theming based on context
  // (home page, node of certain type, etc.)
  $body_classes = array($vars['body_classes']);
  if (!$vars['is_front']) {
    // Add unique classes for each page and website section
    $path = drupal_get_path_alias($_GET['q']);
    list($section, ) = explode('/', $path, 2);
    $body_classes[] = zen_id_safe('page-' . $path);
    $body_classes[] = zen_id_safe('section-' . $section);
    if (arg(0) == 'node') {
      if (arg(1) == 'add') {
        if ($section == 'node') {
          array_pop($body_classes); // Remove 'section-node'
        }
        $body_classes[] = 'section-node-add'; // Add 'section-node-add'
      }
      elseif (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) == 'delete')) {
        if ($section == 'node') {
          array_pop($body_classes); // Remove 'section-node'
        }
        $body_classes[] = 'section-node-' . arg(2); // Add 'section-node-edit' or 'section-node-delete'
      }
    }
    
    // setup sidebar content for page nodes
    if($vars['node']->type == 'page') {
      if($quote = $vars['node']->field_quote[0]['view']) {
        $vars['right'] = '<div class="field-quote">' . $quote . '</div>' . $vars['right'];
        $body_classes[] = 'two-sidebars';
      }      
    } 
    
  }
  else if ($vars['is_front']) {
    $NUM_SPOTLIGHT = 5;
    $vars['spotlight_image'] = '<img alt="leadership" src="' . url(path_to_theme()) . '/images/home-spotlight' . rand(1,$NUM_SPOTLIGHT) . '.jpg" />'; 
  }
  $vars['body_classes'] = implode(' ', $body_classes); // Concatenate with spaces
  
  $vars['site_by'] = "<p id=\"site-by\">Site by <a href=\"http://www.fuseiq.com\">Fuse IQ</a></p>"; // 
}

/**
 * Override or insert PHPTemplate variables into the node templates.
 *
 * @param $vars
 *   A sequential array of variables to pass to the theme template.
 * @param $hook
 *   The name of the theme function being called ("node" in this case.)
 */
function phptemplate_preprocess_node(&$vars, $hook) {
  global $user;

  // Special classes for nodes
  $node_classes = array();
  if ($vars['sticky']) {
    $node_classes[] = 'sticky';
  }
  if (!$vars['node']->status) {
    $node_classes[] = 'node-unpublished';
    $vars['unpublished'] = TRUE;
  }
  else {
    $vars['unpublished'] = FALSE;
  }
  if ($vars['node']->uid && $vars['node']->uid == $user->uid) {
    // Node is authored by current user
    $node_classes[] = 'node-mine';
  }
  if ($vars['teaser']) {
    // Node is displayed as teaser
    $node_classes[] = 'node-teaser';
  }
  // Class for node type: "node-type-page", "node-type-story", "node-type-my-custom-type", etc.
  $node_classes[] = 'node-type-' . $vars['node']->type;
  $vars['node_classes'] = implode(' ', $node_classes); // Concatenate with spaces
}

/**
 * Override or insert PHPTemplate variables into the comment templates.
 *
 * @param $vars
 *   A sequential array of variables to pass to the theme template.
 * @param $hook
 *   The name of the theme function being called ("comment" in this case.)
 */
function phptemplate_preprocess_comment(&$vars, $hook) {
  global $user;

  // We load the node object that the current comment is attached to
  $node = node_load($vars['comment']->nid);
  // If the author of this comment is equal to the author of the node, we
  // set a variable so we can theme this comment uniquely.
  $vars['author_comment'] = $vars['comment']->uid == $node->uid ? TRUE : FALSE;

  $comment_classes = array();

  // Odd/even handling
  static $comment_odd = TRUE;
  $comment_classes[] = $comment_odd ? 'odd' : 'even';
  $comment_odd = !$comment_odd;

  if ($vars['comment']->status == COMMENT_NOT_PUBLISHED) {
    $comment_classes[] = 'comment-unpublished';
    $vars['unpublished'] = TRUE;
  }
  else {
    $vars['unpublished'] = FALSE;
  }
  if ($vars['author_comment']) {
    // Comment is by the node author
    $comment_classes[] = 'comment-by-author';
  }
  if ($vars['comment']->uid == 0) {
    // Comment is by an anonymous user
    $comment_classes[] = 'comment-by-anon';
  }
  if ($user->uid && $vars['comment']->uid == $user->uid) {
    // Comment was posted by current user
    $comment_classes[] = 'comment-mine';
  }
  $vars['comment_classes'] = implode(' ', $comment_classes);

  // If comment subjects are disabled, don't display 'em
  if (variable_get('comment_subject_field', 1) == 0) {
    $vars['title'] = '';
  }
}

/**
 * Override or insert PHPTemplate variables into the block templates.
 *
 * @param $vars
 *   A sequential array of variables to pass to the theme template.
 * @param $hook
 *   The name of the theme function being called ("block" in this case.)
 */
function phptemplate_preprocess_block(&$vars, $hook) {
  $block = $vars['block'];
  
  if($vars['block']->subject == 'Welcome') {
    $vars['block']->subject = 'Welcome, ' . $vars['user']->name;  
  }  

  // Special classes for blocks
  $block_classes = array();
  $block_classes[] = 'block-' . $block->module;
  $block_classes[] = 'region-' . $vars['block_zebra'];
  $block_classes[] = $vars['zebra'];
  $block_classes[] = 'region-count-' . $vars['block_id'];
  $block_classes[] = 'count-' . $vars['id'];
  $vars['block_classes'] = implode(' ', $block_classes);

  $vars['edit_links'] = '';
  if (user_access('administer blocks')) {
    // Display 'edit block' for custom blocks
    if ($block->module == 'block') {
      $edit_links[] = l('<span>' . t('edit block') . '</span>', 'admin/build/block/configure/' . $block->module . '/' . $block->delta,
        array(
          'attributes' => array(
            'title' => t('edit the content of this block'),
            'class' => 'block-edit',
          ),
          'query' => drupal_get_destination(),
          'html' => TRUE,
        )
      );
    }
    // Display 'configure' for other blocks
    else {
      $edit_links[] = l('<span>' . t('configure') . '</span>', 'admin/build/block/configure/' . $block->module . '/' . $block->delta,
        array(
          'attributes' => array(
            'title' => t('configure this block'),
            'class' => 'block-config',
          ),
          'query' => drupal_get_destination(),
          'html' => TRUE,
        )
      );
    }

    // Display 'administer views' for views blocks
    if ($block->module == 'views' && user_access('administer views')) {
      $edit_links[] = l('<span>' . t('edit view') . '</span>', 'admin/build/views/' . $block->delta . '/edit',
        array(
          'attributes' => array(
            'title' => t('edit the view that defines this block'),
            'class' => 'block-edit-view',
          ),
          'query' => drupal_get_destination(),
          'fragment' => 'edit-block',
          'html' => TRUE,
        )
      );
    }
    // Display 'edit menu' for menu blocks
    elseif (($block->module == 'menu' || ($block->module == 'user' && $block->delta == 1)) && user_access('administer menu')) {
      $menu_name = ($block->module == 'user') ? 'navigation' : $block->delta;
      $edit_links[] = l('<span>' . t('edit menu') . '</span>', 'admin/build/menu-customize/' . $menu_name,
        array(
          'attributes' => array(
            'title' => t('edit the menu that defines this block'),
            'class' => 'block-edit-menu',
          ),
          'query' => drupal_get_destination(),
          'html' => TRUE,
        )
      );
    }
    $vars['edit_links_array'] = $edit_links;
    $vars['edit_links'] = '<div class="edit">' . implode(' ', $edit_links) . '</div>';
  }
}

/**
 * Converts a string to a suitable html ID attribute.
 *
 * http://www.w3.org/TR/html4/struct/global.html#h-7.5.2 specifies what makes a
 * valid ID attribute in HTML. This function:
 *
 * - Ensure an ID starts with an alpha character by optionally adding an 'n'.
 * - Replaces any character except A-Z, numbers, and underscores with dashes.
 * - Converts entire string to lowercase.
 *
 * @param $string
 *   The string
 * @return
 *   The converted string
 */
function zen_id_safe($string) {
  // Replace with dashes anything that isn't A-Z, numbers, dashes, or underscores.
  $string = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $string));
  // If the first character is not a-z, add 'n' in front.
  if (!ctype_lower($string{0})) { // Don't use ctype_alpha since its locale aware.
    $string = 'id' . $string;
  }
  return $string;
}

/**
 * Return the path to the main fuse theme directory.
 */
function path_to_basictheme() {
  static $theme_path;
  if (!isset($theme_path)) {
    global $theme;
    if ($theme == 'cel_basic') {
      $theme_path = path_to_theme();
    }
    else {
      $theme_path = drupal_get_path('theme', 'cel_basic');
    }
  }
  return $theme_path;
}


function cel_lms_nodehierarchy_actions($node) {
  global $user;
  drupal_add_css(drupal_get_path('module', 'nodehierarchy') .'/nodehierarchy.css');

  $destination = drupal_get_destination();
  $actions = array();
//  $actions[] = theme('nodehierarchy_action', url("node/$node->nid"), 'view', node_access('view', $node));
  if (node_access('update', $node)) 
  	$actions[] = l('edit',"node/$node->nid/edit", array('query'=> $destination)).'&nbsp;'; //theme('nodehierarchy_action', url("node/$node->nid/edit", array('query' => $destination)), 'edit', node_access('update', $node));
  if (node_access('delete', $node)) 
  	$actions[] =  l('delete',"node/$node->nid/delete", array('query'=> $destination)); //theme('nodehierarchy_action', url("node/$node->nid/delete", array('query' => $destination)), 'delete', node_access('delete', $node));
  if (user_access('reorder children')) {
    $actions[] = theme('nodehierarchy_action', url("hierarchy/$node->nid/up", array('query' => $destination)), 'up');
    $actions[] = theme('nodehierarchy_action', url("hierarchy/$node->nid/down", array('query' => $destination)), 'down');
  }

  return "<div class='nodehierarchy_actions'>". implode("", $actions) .'</div>';
}

function cel_lms_nodehierarchy_children_list($node, $children, $show_create_links = TRUE) {
  $output = "";
  if (!empty($children)) {
    $output .= '<ul class="nodehierarchy_outline menu">';
    $i = 1;
    
    $is_course_doc_list = false;
    
    foreach ($children as $nid => $item) {
      $children_list = "";
      $attributs = array();

      //special stuff for course documents
      if ( $item['node']->type == 'course_document') {
 		$is_course_doc_list = true;
      	$title = check_plain($item['node']->title);
	      if ($item['url']) {
	        $title = '<a class="'.
	                    ($item['expandable'] ? "expand_widget" : "non_expandable") .
	                    ($item['expanded'] ? " expanded" : " collapsed") .'" >'. $title .'</a>';
	        if ( isset($item['node']->field_file[0]) )
	        	$title .= '&nbsp;&nbsp;&nbsp;'.l($item['node']->field_file[0]['filename'],str_replace('sites/default','system',$item['node']->field_file[0]['filepath']));
	        $title .= '<div style="display:none;" class="tooltip-body"  ><label>What is this document?</label><br/>'.$item['node']->body.'<br/>'.(isset($item['node']->field_description_2[0]['value']) ? '<label>How is it used?</label><br/>'.($item['node']->field_description_2[0]['value']):'').'</div>';
	      }
      } else {
	      $title = check_plain($item['node']->title);
	      if ($item['url']) {
	        $title = '<a href="'. $item['url'] .'" class="'.
	                    ($item['expandable'] ? "expand_widget" : "non_expandable") .
	                    ($item['expanded'] ? " expanded" : " collapsed") .'" title="'. $item['tooltip'] .'">'. $title .'</a>';
	      }
      	
      }
      
      $attributes['id'] = "nodehierarchy_child-". $item['node']->nid;
      $attributes['class'] = "nodehierarchy_child";
      if ($item['expandable']) {
        $attributes['class'] .= " expandable";
        $attributes['class'] .= $item['expanded'] ? " expanded" : " collapsed";
      }
      if (!nodehierarchy_previous_sibling_nid($item['node'])) {
        $attributes['class'] .= " first";
      }
      if (!nodehierarchy_next_sibling_nid($item['node'])) {
        $attributes['class'] .= " last";
      }
      $actions = theme("nodehierarchy_actions", $item['node']);
      $grandchildren = "";
      if ($item['expanded']) {
        $grandchildren = theme("nodehierarchy_children_list", $item['node'], $item['children']);
      }
      $output .= '<li'. drupal_attributes($attributes) .'><div class="item has-tooltip">'. $actions . $title .'</div><div class="children">'. $grandchildren .'</div></li>';
      $i++;
    }
    $output .= "</ul>";
  }
  else {
    $output .= t("This node has no children");
  }
  if ($show_create_links) {
    $output .= theme("nodehierarchy_new_child_links", $node);
  }
  
  if ( $is_course_doc_list ) {
  	    drupal_add_js(path_to_theme().'/jquery.dimensions.js');
      	drupal_add_js(path_to_theme().'/jquery.tooltip.js');
     	drupal_add_css(path_to_theme().'/jquery.tooltip.css');
		 $js = ' $(document).ready(function() {
		
		    $(".has-tooltip").tooltip({ 
		        bodyHandler: function() { 
		        	if ( $(this).find(".tooltip-body") )
		            	return $(this).find(".tooltip-body").html();
		        }, 
		        showURL: false 
		    });
		});';
		 drupal_add_js($js,'inline');
  	
  }
  
  return $output;
}

/**
 * Display links to create new children nodes of the given node
 */
function cel_lms_nodehierarchy_new_child_links($node) {
  $out = "";
  $create_links = array();

  if (user_access('create child nodes') && node_access('update', $node)) {
  	
  	//find any og's for this node
   	
    foreach (node_get_types() as $key => $type) {
      if (node_access('create', $key) && variable_get('nh_child_'. $key, FALSE)) {
        $destination = drupal_get_destination() ."&edit[parent]=$node->nid"."&gids[]=".(reset($node->og_groups));
        $key = str_replace('_', '-', $key);
        $title = t('Add a new %s.', array('%s' => $type->name));
        $create_links[] = l($type->name, "node/add/$key", array('query' => $destination, 'attributes' => array('title' => $title)));
      }
    }
    if ($create_links) {
      $out = '<div class="newchild">'. t("create new child !s", array('!s' => implode(" | ", $create_links))) .'</div>';
    }
  }
  return $out;
}

function phptemplate_uc_cart_block_content($help_text, $items, $item_count, $item_text, $total, $summary_links) {
  $output = '';

  // Add the help text if enabled.
  if ($help_text) {
    $output .= '<span class="cart-help-text">'. $help_text .'</span>';
  }

  // Add a wrapper div for use when collapsing the block.
  $output .= '<div id="cart-block-contents">';

  // Add a table of items in the cart or the empty message.
  $output .= theme('uc_cart_block_items', $items);

  $output .= '</div>';

  $summary_links['cart-block-empty'] = array('title' => "Empty cart", 'href' => 'cart/empty');
  // Add the summary section beneath the items table.
  $output .= theme('uc_cart_block_summary', $item_count, $item_text, $total, $summary_links);

  return $output;
}

function phptemplate_uc_cart_checkout_review($panes, $form) {
  drupal_add_css(drupal_get_path('module', 'uc_cart') .'/uc_cart.css');

  $output = check_markup(variable_get('uc_checkout_review_instructions', uc_get_message('review_instructions')), variable_get('uc_checkout_review_instructions_format', FILTER_FORMAT_DEFAULT), FALSE)
           .'<table class="order-review-table">';

  foreach ($panes as $title => $data) {
  	
  	/*** DONT SHOW CALCULATE SHIPPING COSTS PAGE ***/
  	if ( $title != 'Calculate shipping cost') {
  	
    $output .= '<tr class="pane-title-row"><td colspan="2">'. $title
              .'</td></tr>';
    if (is_array($data)) {
      foreach ($data as $row) {
        if (is_array($row)) {
          if (isset($row['border'])) {
            $border = ' class="row-border-'. $row['border'] .'"';
          }
          else {
            $border = '';
          }
          $output .= '<tr valign="top"'. $border .'><td class="title-col" '
                    .'nowrap>'. $row['title'] .':</td><td class="data-col">'
                   . $row['data'] .'</td></tr>';
        }
        else {
          $output .= '<tr valign="top"><td colspan="2">'. $row .'</td></tr>';
        }
      }
    }
   
    else {
      $output .= '<tr valign="top"><td colspan="2">'. $data .'</td></tr>';
    }
  	 }
  }

  $output .= '<tr class="review-button-row"><td colspan="2">'. $form
            .'</td></tr></table>';

  return $output;
}