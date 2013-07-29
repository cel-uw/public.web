<?php

/**
 * @file
 * Provides views theme override functions for Bootstrap themes.
 */

/**
 * Display a view as a table style.
 *
 * @param array &$vars
 */
function wl_preprocess_views_view_table(&$vars) {
  $vars['classes_array'][] = 'table';
}

/**
 * Display a view as a grid style.
 *
 * @param array &$vars
 */
function wl_preprocess_views_view_grid(&$vars) {
  $vars['class'] .= ' table';
}
