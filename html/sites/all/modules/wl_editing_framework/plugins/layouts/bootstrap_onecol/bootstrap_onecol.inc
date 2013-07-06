<?php

// Plugin definition
$plugin = array(
  'title' => t('Single column for bootstrap'),
  'category' => t('Columns: 1'),
  'icon' => 'bootstrap_onecol.png',
  'theme' => 'bootstrap_onecol',
  'css' => 'bootstrap_onecol.css',
  'regions' => array(
    'middle' => t('Middle')
  ),
);
