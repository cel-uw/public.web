<?php

// Plugin definition
$plugin = array(
  'title' => t('Three column 33/33/33 stacked for bootstrap'),
  'category' => t('Columns: 3'),
  'icon' => 'bootstrap_threecol_33_33_33_stacked.png',
  'theme' => 'bootstrap_threecol_33_33_33_stacked',
  'css' => 'bootstrap_threecol_33_33_33_stacked.css',
  'regions' => array(
    'top' => t('Top'),
    'left' => t('Left side'),
    'middle' => t('Middle'),
    'right' => t('Right side'),
    'bottom' => t('Bottom')
  ),
);
