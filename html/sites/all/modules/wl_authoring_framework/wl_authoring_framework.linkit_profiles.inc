<?php
/**
 * @file
 * wl_authoring_framework.linkit_profiles.inc
 */

/**
 * Implements hook_default_linkit_profiles().
 */
function wl_authoring_framework_default_linkit_profiles() {
  $export = array();

  $linkit_profile = new stdClass();
  $linkit_profile->disabled = FALSE; /* Edit this to true to make a default linkit_profile disabled initially */
  $linkit_profile->api_version = 1;
  $linkit_profile->name = 'default';
  $linkit_profile->admin_title = 'Default';
  $linkit_profile->data = array(
    'plugins' => array(
      'entity:taxonomy_term' => array(
        'weight' => '0',
        'enabled' => 1,
      ),
      'entity:node' => array(
        'weight' => '0',
        'enabled' => 1,
      ),
      'entity:file' => array(
        'weight' => '0',
        'enabled' => 1,
      ),
      'entity:user' => array(
        'weight' => '0',
        'enabled' => 1,
      ),
    ),
    'entity:node' => array(
      'result_description' => '[node:title] [ [node:nid] ] by [node:author:name]',
      'bundles' => array(
        'article' => 0,
        'page' => 0,
        'panel' => 0,
      ),
      'group_by_bundle' => 0,
      'reverse_menu_trail' => 1,
      'include_unpublished' => 1,
    ),
    'entity:file' => array(
      'result_description' => 'File [file:name] uploaded by [file:owner]',
      'bundles' => array(
        'image' => 0,
        'video' => 0,
        'audio' => 0,
        'document' => 0,
      ),
      'group_by_bundle' => 0,
      'reverse_menu_trail' => 0,
      'show_scheme' => 0,
      'group_by_scheme' => 0,
      'image_extra_info' => array(
        'thumbnail' => 'thumbnail',
        'dimensions' => 'dimensions',
      ),
    ),
    'entity:taxonomy_term' => array(
      'result_description' => 'Term [term:vocabulary] / [term:parents:join:/] / [term:name]',
      'reverse_menu_trail' => 0,
    ),
    'entity:user' => array(
      'result_description' => 'User [user:name] ([user:status])',
      'reverse_menu_trail' => 0,
    ),
    'autocomplete' => array(
      'charLimit' => 3,
      'wait' => 350,
      'remoteTimeout' => 10000,
    ),
    'attributes' => array(
      'rel' => array(
        'weight' => '0',
        'enabled' => 1,
      ),
      'class' => array(
        'weight' => '0',
        'enabled' => 0,
      ),
      'accesskey' => array(
        'weight' => '0',
        'enabled' => 0,
      ),
      'id' => array(
        'weight' => '0',
        'enabled' => 1,
      ),
      'title' => array(
        'weight' => '0',
        'enabled' => 1,
      ),
    ),
  );
  $linkit_profile->role_rids = array(
    2 => 1,
  );
  $linkit_profile->weight = 0;
  $export['default'] = $linkit_profile;

  return $export;
}
