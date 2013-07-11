<?php
/**
 * @file
 * wl_authoring_framework.features.wysiwyg.inc
 */

/**
 * Implements hook_wysiwyg_default_profiles().
 */
function wl_authoring_framework_wysiwyg_default_profiles() {
  $profiles = array();

  // Exported profile: filtered_html
  $profiles['filtered_html'] = array(
    'format' => 'filtered_html',
    'editor' => 'ckeditor',
    'settings' => array(
      'default' => 1,
      'user_choose' => 0,
      'show_toggle' => 1,
      'theme' => 'advanced',
      'language' => 'en',
      'buttons' => array(
        'default' => array(
          'Bold' => 1,
          'Italic' => 1,
          'BulletedList' => 1,
          'NumberedList' => 1,
          'Outdent' => 1,
          'Indent' => 1,
          'Unlink' => 1,
          'Superscript' => 1,
          'Subscript' => 1,
          'PasteFromWord' => 1,
          'Format' => 1,
          'Styles' => 1,
          'Scayt' => 1,
          'Source' => 1,
        ),
        'linkit' => array(
          'linkit' => 1,
        ),
        'drupal' => array(
          'media' => 1,
        ),
      ),
      'toolbar_loc' => 'top',
      'toolbar_align' => 'left',
      'path_loc' => 'bottom',
      'resizing' => 1,
      'default_toolbar_grouping' => 0,
      'verify_html' => 0,
      'preformatted' => 0,
      'convert_fonts_to_spans' => 1,
      'remove_linebreaks' => 1,
      'apply_source_formatting' => 0,
      'paste_auto_cleanup_on_paste' => 0,
      'block_formats' => 'h2,h3,h4,h5,h6,p,address,pre,div',
      'css_setting' => 'self',
      'css_path' => 'http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap.min.css,%bsites/all/themes/bootstrap/css/overrides.css,%bsites/all/themes/wl/css/wl-overrides.css',
      'css_classes' => '',
      'advanced__active_tab' => 'edit-css',
    ),
    'rdf_mapping' => array(),
  );

  // Exported profile: full_html
  $profiles['full_html'] = array(
    'format' => 'full_html',
    'editor' => 'ckeditor',
    'settings' => array(
      'default' => 1,
      'user_choose' => 0,
      'show_toggle' => 1,
      'theme' => 'advanced',
      'language' => 'en',
      'buttons' => array(
        'default' => array(
          'Bold' => 1,
          'Italic' => 1,
          'BulletedList' => 1,
          'NumberedList' => 1,
          'Outdent' => 1,
          'Indent' => 1,
          'Unlink' => 1,
          'Superscript' => 1,
          'Subscript' => 1,
          'PasteFromWord' => 1,
          'Format' => 1,
          'Styles' => 1,
          'Scayt' => 1,
          'Templates' => 1,
          'Source' => 1,
        ),
        'linkit' => array(
          'linkit' => 1,
        ),
        'drupal' => array(
          'media' => 1,
        ),
      ),
      'toolbar_loc' => 'top',
      'toolbar_align' => 'left',
      'path_loc' => 'bottom',
      'resizing' => 1,
      'default_toolbar_grouping' => 0,
      'verify_html' => 0,
      'preformatted' => 0,
      'convert_fonts_to_spans' => 1,
      'remove_linebreaks' => 1,
      'apply_source_formatting' => 0,
      'paste_auto_cleanup_on_paste' => 0,
      'block_formats' => 'h2,h3,h4,h5,h6,p,address,pre,div',
      'css_setting' => 'self',
      'css_path' => 'http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap.min.css,%bsites/all/themes/bootstrap/css/overrides.css,%bsites/all/themes/wl/css/wl-overrides.css',
      'css_classes' => '',
      'advanced__active_tab' => 'edit-css',
    ),
    'rdf_mapping' => array(),
  );

  return $profiles;
}
