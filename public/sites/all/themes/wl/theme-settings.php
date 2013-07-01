<?php

include_once(realpath(dirname(__FILE__) . '/../bootstrap/includes/bootstrap.inc'));

/**
 * Add some fields to the theme admin settings
 *
 * Implements hook_form_FORM_ID_alter()
 *
 * @param array &$form The form
 * @param array $form_state The form state
 * @param string|null $form_id
 */
function wl_form_system_theme_settings_alter(&$form, $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  $form['cdn']['cdn_jquery'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Use CDN to load in a modern version of jQuery'),
    '#default_value' => theme_get_setting('cdn_jquery'),
    '#description'   => t('Use CDN (a third party hosting server) to host the jQuery files. This theme will not use the built-in jQuery file anymore and instead the visitor will download them from ') . l('code.jquery.com', 'http://code.jquery.com')
                        .'<div class="alert alert-error">' . t('WARNING: this technique will give you a performance boost but will also make you dependant on a third party who has no obligations towards you concerning uptime and service quality.') . '</div>',
  );

  $form['cdn']['cdn_jquery_version_container'] = array(
    '#type' => 'container',
    '#states' => array(
      'invisible' => array(
       ':input[name="cdn_jquery"]' => array('checked' => FALSE),
      ),
    ),
  );

  $form['cdn']['cdn_jquery_version_container']['cdn_jquery_version'] = array(
    '#type' => 'select',
    '#title' => t('jQuery version'),
    '#options' => array(
      '2.0.2'  => 'v2.0.2',
      '1.10.1' => 'v1.10.1',
      '1.9.1'  => 'v1.9.1',
      '1.8.3'  => 'v1.8.3',
      '1.7.2'  => 'v1.7.2',
    ),
    '#default_value' => theme_get_setting('cdn_jquery_version'),
  );

  $form['cdn']['cdn_jquery_migrate'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Use CDN to load in the jQuery Migrate plugin'),
    '#default_value' => theme_get_setting('cdn_jquery_migrate'),
    '#description'   => t('Use CDN (a third party hosting server) to host the jQuery Migrate plugin. This theme will not use the built-in jQuery file anymore and instead the visitor will download them from ') . l('code.jquery.com', 'http://code.jquery.com')
                        .'<div class="alert alert-error">' . t('WARNING: this technique will give you a performance boost but will also make you dependant on a third party who has no obligations towards you concerning uptime and service quality.') . '</div>',
  );

  $form['cdn']['cdn_jquery_migrate_version_container'] = array(
    '#type' => 'container',
    '#states' => array(
      'invisible' => array(
       ':input[name="cdn_jquery_migrate"]' => array('checked' => FALSE),
      ),
    ),
  );

  $form['cdn']['cdn_jquery_migrate_version_container']['cdn_jquery_migrate_version'] = array(
    '#type' => 'select',
    '#title' => t('jQuery Migrate version'),
    '#options' => array(
      '1.2.1' => 'v1.2.1',
      '1.1.1' => 'v1.1.1',
      '1.0.0' => 'v1.0.0',
    ),
    '#default_value' => theme_get_setting('cdn_jquery_migrate_version'),
  );

}

