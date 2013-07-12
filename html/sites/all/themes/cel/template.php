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