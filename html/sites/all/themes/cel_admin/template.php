<?php

/**
 * Rearrange some options on the node add/edit page
 *
 * We place the "Promote to front page" checkbox in with the other
 * promotion-releated fields.
 *
 * Implements hook_form_BASE_FORM_ID_alter()
 *
 * @param array &$form The form array
 * @param array &$form_state The form state array
 * @param string $form_id The ID of the form
 */
function cel_admin_form_node_form_alter(&$form, &$form_state, $form_id) {
  if(empty($form['options']['promote'])) {
    return;
  }

  if(empty($form['#groups']['group_promote'])) {
    return;
  }

  // Make it so the promote fields only show if promoted to front page is checked
  foreach($form['#groups']['group_promote']->children as $field) {
    if(!isset($form[$field]['#states'])) {
      $form[$field]['#states'] = array();
    }
    if(!isset($form[$field]['#states'])) {
      $form[$field]['#states']['visible'] = array();
    }

    $form[$field]['#states']['visible'][':input[name="promote"]'] = array('checked' => TRUE);
    
    // Set required fields to be conditionally required
    if($form[$field]['und']['#required']) {
      $form[$field]['und']['#conditionally_required'] = TRUE;
      $form[$field]['und']['#required'] = FALSE;
      $form[$field]['und'][0]['#conditionally_required'] = TRUE;
      $form[$field]['und'][0]['#required'] = FALSE;
    }
  }

  // Move the Promoted to front page checkbox to the Promote field
  array_unshift($form['#groups']['group_promote']->children, 'promote');
  array_unshift($form['#fieldgroups']['group_promote']->children, 'promote');
  $form['#group_children']['promote'] = 'group_promote';
  $form['promote'] = $form['options']['promote'];
  unset($form['options']['promote']);
  $form['promote']['#weight'] = $form['field_promotional_image']['#weight']-1;

  // Add some conditional validation
  $form['#validate'][] = 'cel_admin_node_form_validate';
}

/**
 * Rearrange some options on the news/events node add/edit page
 *
 * We place the "Sticky" checkbox in with the other
 * promotion-releated fields.
 *
 * Implements hook_form_FORM_ID_alter()
 *
 * @param array &$form The form array
 * @param array &$form_state The form state array
 * @param string $form_id The ID of the form
 */
function cel_admin_form_news_event_item_node_form_alter(&$form, &$form_state, $form_id) {
  // Move the sticky checkbox to the Promote field
  array_unshift($form['#groups']['group_promote']->children, 'sticky');
  array_unshift($form['#fieldgroups']['group_promote']->children, 'sticky');
  $form['#group_children']['sticky'] = 'group_promote';
  $form['sticky'] = $form['options']['sticky'];
  unset($form['options']['sticky']);
  $form['sticky']['#weight'] = $form['field_promotional_image']['#weight']-2;
  $form['sticky']['#title'] = t('Sticky');
  $form['sticky']['#description'] = t('Keep this news item at the top of featured news sections.');
}

/**
 * Validates conditional promote-related fields
 *
 * @param array $form
 * @param array &$form_state
 */
function cel_admin_node_form_validate($form, &$form_state) {
  if(!empty($form_state['values']['promote'])) {
    foreach($form['#groups']['group_promote']->children as $field) {

      if(empty($form[$field]['und'][0]['#conditionally_required'])) {
        continue;
      }

      $default_value = isset($form[$field]['und'][0]['#default_value']) ? $form[$field]['und'][0]['#default_value'] : "";
      $title = isset($form[$field]['und']['#title']) ? $form[$field]['und']['#title'] : "";

      if(!isset($form_state['values'][$field]['und'][0])) {
        form_set_error($field, t('!name field is required.', array('!name' => $title)));
      }

      $field_values = $form_state['values'][$field]['und'][0];

      if(isset($field_values['fid'])) {
        // For image fields
        if(empty($field_values['fid'])) {
          form_set_error($field, t('!name field is required.', array('!name' => $title)));
        }
      } else if(empty($field_values['value']) || $field_values['value'] === $default_value) {
        form_set_error($field, t('!name field is required.', array('!name' => $title)));
      }
    }
  }
}