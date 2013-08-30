<?php
// $Id: theme-settings.php $

/**
 * Implementation of THEMEHOOK_settings() function.
 *
 * @param $saved_settings
 *   An array of saved settings for this theme.
 * @param $subtheme_defaults
 *   Allow a subtheme to override the default values.
 * @return
 *   A form array.
 */

function phptemplate_settings($saved_settings) {

  /*
   * The default values for the theme variables. Make sure $defaults exactly
   * matches the $defaults in the template.php file.
   */
$defaults = array(
   'basic_layout' => 'liquid',
   'basic_wireframes' => 0,
);
  

  // Merge the saved variables and their default values
  $settings = array_merge($defaults, $saved_settings);
  
  
   /*
   * Create the form using Forms API
   */
  $form['baisc-div-opening'] = array(
    '#value'         => '<div id="theme-settings">',
  ); 
  

  
  
  $form['themedev']['basic_wireframes'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Display borders around main layout elements'),
    '#default_value' => $settings['basic_wireframes'],
    '#description'   => t(''),
    '#prefix'        => '<div id="div-setting-wireframes"><strong>' . t('Wireframes:') . '</strong>',
    '#suffix'        => '</div>',
  );
  

  
    $form['basic-div-closing'] = array(
    '#value'         => '</div>',
  );

  // Return the additional form widgets
  return $form;
}
?>