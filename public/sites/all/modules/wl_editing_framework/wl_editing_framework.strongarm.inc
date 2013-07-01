<?php
/**
 * @file
 * wl_editing_framework.strongarm.inc
 */

/**
 * Implements hook_strongarm().
 */
function wl_editing_framework_strongarm() {
  $export = array();

  // Preview
  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_preview_page';
  $strongarm->value = 0;
  $export['node_preview_page'] = $strongarm;

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_preview_blurb';
  $strongarm->value = 0;
  $export['node_preview_blurb'] = $strongarm;

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_preview_slideshow';
  $strongarm->value = 0;
  $export['node_preview_slideshow'] = $strongarm;

  // Node options
  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_options_page';
  $strongarm->value = array(
    'status',
    'revision',
  );
  $export['node_options_page'] = $strongarm;

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_options_blurb';
  $strongarm->value = array(
    'status',
    'revision',
  );
  $export['node_options_blurb'] = $strongarm;

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_options_slideshow';
  $strongarm->value = array(
    'status',
    'revision',
  );
  $export['node_options_slideshow'] = $strongarm;

  // Display settings
  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_submitted_page';
  $strongarm->value = FALSE;
  $export['node_submitted_page'] = $strongarm;

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_submitted_blurb';
  $strongarm->value = FALSE;
  $export['node_submitted_blurb'] = $strongarm;

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'node_submitted_slideshow';
  $strongarm->value = FALSE;
  $export['node_submitted_slideshow'] = $strongarm;

  // Comments
  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_page';
  $strongarm->value = 1;
  $export['comment_page'] = $strongarm;

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_blurb';
  $strongarm->value = 1;
  $export['comment_blurb'] = $strongarm;

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'comment_slideshow';
  $strongarm->value = 1;
  $export['comment_slideshow'] = $strongarm;

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'wl_editing_framework_allowed_layouts';
  $strongarm->value = 'O:22:"panels_allowed_layouts":4:{s:9:"allow_new";b:1;s:11:"module_name";s:20:"wl_editing_framework";s:23:"allowed_layout_settings";a:13:{s:8:"flexible";i:0;s:14:"twocol_stacked";i:0;s:13:"twocol_bricks";i:0;s:6:"twocol";i:0;s:25:"threecol_33_34_33_stacked";i:0;s:17:"threecol_33_34_33";i:0;s:25:"threecol_25_50_25_stacked";i:0;s:17:"threecol_25_50_25";i:0;s:6:"onecol";i:0;s:24:"bootstrap_twocol_stacked";i:1;s:35:"bootstrap_threecol_33_33_33_stacked";i:1;s:35:"bootstrap_threecol_25_50_25_stacked";i:1;s:16:"bootstrap_onecol";i:1;}s:10:"form_state";N;}';
  $export['wl_editing_framework_allowed_layouts'] = $strongarm;

  return $export;
}
