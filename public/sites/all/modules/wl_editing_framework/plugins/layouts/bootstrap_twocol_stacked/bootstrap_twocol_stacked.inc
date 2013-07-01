<?php

// Plugin definition
$plugin = array(
  'title' => t('Two column stacked for bootstrap'),
  'category' => t('Columns: 2'),
  'icon' => 'bootstrap_twocol_stacked.png',
  'theme' => 'bootstrap_panels_twocol_stacked',
  'css' => 'bootstrap_twocol_stacked.css',
  'regions' => array(
    'top' => t('Top'),
    'left' => t('Left side'),
    'right' => t('Right side'),
    'bottom' => t('Bottom')
  ),
);
