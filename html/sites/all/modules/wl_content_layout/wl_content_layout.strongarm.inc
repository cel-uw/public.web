<?php
/**
 * @file
 * wl_content_layout.strongarm.inc
 */

/**
 * Implements hook_strongarm().
 */
function wl_content_layout_strongarm() {
  $export = array();

  $strongarm = new stdClass();
  $strongarm->disabled = FALSE; /* Edit this to true to make a default strongarm disabled initially */
  $strongarm->api_version = 1;
  $strongarm->name = 'wl_content_layout_allowed_layouts';
  $strongarm->value = 'O:22:"panels_allowed_layouts":4:{s:9:"allow_new";b:1;s:11:"module_name";s:17:"wl_content_layout";s:23:"allowed_layout_settings";a:13:{s:8:"flexible";b:0;s:14:"twocol_stacked";b:0;s:13:"twocol_bricks";b:0;s:6:"twocol";b:0;s:25:"threecol_33_34_33_stacked";b:0;s:17:"threecol_33_34_33";b:0;s:25:"threecol_25_50_25_stacked";b:0;s:17:"threecol_25_50_25";b:0;s:6:"onecol";b:0;s:24:"bootstrap_twocol_stacked";b:1;s:35:"bootstrap_threecol_33_33_33_stacked";b:1;s:35:"bootstrap_threecol_25_50_25_stacked";b:1;s:16:"bootstrap_onecol";b:1;}s:10:"form_state";N;}';
  $export['wl_content_layout_allowed_layouts'] = $strongarm;

  return $export;
}
