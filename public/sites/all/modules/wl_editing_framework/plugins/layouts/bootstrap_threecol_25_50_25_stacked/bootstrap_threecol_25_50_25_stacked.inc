<?php

// Plugin definition
$plugin = array(
  'title' => t('Three column 25/50/25 stacked for bootstrap'),
  'category' => t('Columns: 3'),
  'icon' => 'bootstrap_threecol_25_50_25_stacked.png',
  'theme' => 'bootstrap_threecol_25_50_25_stacked',
  'css' => 'bootstrap_threecol_25_50_25_stacked.css',
  'regions' => array(
    'top' => t('Top'),
    'left' => t('Left side'),
    'middle' => t('Middle'),
    'right' => t('Right side'),
    'bottom' => t('Bottom')
  ),
);
