<?php
/**
 * @file
 * wl_editing_framework.features.ckeditor_profile.inc
 */

/**
 * Implements hook_ckeditor_profile_defaults().
 */
function wl_editing_framework_ckeditor_profile_defaults() {
  $data = array(
    'CKEditor Global Profile' => array(
      'name' => 'CKEditor Global Profile',
      'settings' => array(
        'ckeditor_path' => '%m/ckeditor/ckeditor',
      ),
      'input_formats' => array(),
    ),
    'Standard' => array(
      'name' => 'Standard',
      'settings' => array(
        'ss' => 2,
        'default' => 't',
        'show_toggle' => 'f',
        'uicolor' => 'default',
        'uicolor_user' => 'default',
        'toolbar' => '[
    [\'Format\',\'Styles\'],
    [\'Bold\',\'Italic\'],
    [\'NumberedList\',\'BulletedList\'],
    [\'Outdent\',\'Indent\'],
    [\'-\'],
    [\'Subscript\',\'Superscript\'],
    [\'-\'],
    [\'linkit\',\'Unlink\'],
    [\'-\'],
    [\'Blockquote\',\'-\',\'Table\',\'-\',\'Media\'],
    [\'-\'],
    [\'RemoveFormat\',\'PasteFromWord\',\'Scayt\'],
    [\'-\',\'Source\']
]',
        'expand' => 't',
        'width' => '100%',
        'lang' => 'en',
        'auto_lang' => 't',
        'language_direction' => 'default',
        'enter_mode' => 'p',
        'shift_enter_mode' => 'br',
        'font_format' => 'p;div;pre;address;h1;h2;h3;h4;h5;h6',
        'custom_formatting' => 'f',
        'formatting' => array(
          'custom_formatting_options' => array(
            'indent' => 'indent',
            'breakBeforeOpen' => 'breakBeforeOpen',
            'breakAfterOpen' => 'breakAfterOpen',
            'breakAfterClose' => 'breakAfterClose',
            'breakBeforeClose' => 0,
            'pre_indent' => 0,
          ),
        ),
        'css_mode' => 'none',
        'css_path' => '',
        'css_style' => 'theme',
        'styles_path' => '',
        'filebrowser' => 'none',
        'filebrowser_image' => '',
        'filebrowser_flash' => '',
        'UserFilesPath' => '%b%f/',
        'UserFilesAbsolutePath' => '%d%b%f/',
        'forcePasteAsPlainText' => 'f',
        'html_entities' => 'f',
        'scayt_autoStartup' => 't',
        'theme_config_js' => 'f',
        'js_conf' => '',
        'loadPlugins' => array(
          'linkit' => array(
            'name' => 'linkit',
            'desc' => 'Support for Linkit module',
            'path' => '%base_path%sites/all/modules/linkit/editors/ckeditor/',
            'buttons' => array(
              'linkit' => array(
                'label' => 'Linkit',
                'icon' => 'linkit.png',
              ),
            ),
          ),
          'media' => array(
            'name' => 'media',
            'desc' => 'Plugin for inserting images from Drupal media module',
            'path' => '%plugin_dir%media/',
            'buttons' => array(
              'Media' => array(
                'label' => 'Media',
                'icon' => 'images/icon.gif',
              ),
            ),
            'default' => 'f',
          ),
        ),
      ),
      'input_formats' => array(
        'filtered_html' => 'Filtered HTML',
        'full_html' => 'Full HTML',
      ),
    ),
  );
  return $data;
}
