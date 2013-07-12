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

  }

  //Merge in the new vars
  $vars = array_merge($vars, $new_vars);

  return $vars;
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