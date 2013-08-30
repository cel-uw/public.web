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

  $lang = $form['language']['#value'];

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
    if($form[$field][$lang]['#required']) {
      $form[$field][$lang]['#conditionally_required'] = TRUE;
      $form[$field][$lang]['#required'] = FALSE;
      $form[$field][$lang][0]['#conditionally_required'] = TRUE;
      $form[$field][$lang][0]['#required'] = FALSE;
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
  $lang = $form['language']['#value'];

  if(!empty($form_state['values']['promote'])) {
    foreach($form['#groups']['group_promote']->children as $field) {

      if(empty($form[$field][$lang][0]['#conditionally_required'])) {
        continue;
      }

      $default_value = isset($form[$field][$lang][0]['#default_value']) ? $form[$field][$lang][0]['#default_value'] : "";
      $title = isset($form[$field][$lang]['#title']) ? $form[$field][$lang]['#title'] : "";

      if(!isset($form_state['values'][$field][$lang][0])) {
        form_set_error($field, t('!name field is required.', array('!name' => $title)));
      }

      $field_values = $form_state['values'][$field][$lang][0];

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

/**
 * Add in a hidden field to store county data on the Partner address field
 *
 * @see hook_form_FORM_ID_alter()
 *
 * @param array &$form
 * @param array &$form_state
 * @param string $form_id
 */
function cel_admin_form_partner_node_form_alter(&$form, &$form_state, $form_id) {
  $lang = $form['language']['#value'];

  if(isset($form['field_address']['#attributes']['class'])) {
    $form['field_address']['#attribues']['class'][] = 'field-widget-addressfield-has-sub-admin-area';
  }

  $form['field_address'][$lang][0]['sub_administrative_area'] = array(
    '#type' => 'hidden',
    '#default_value' => '',
    '#weight' => -100,
  );

  // addressfield will turn our hidden field into a textfield in addressfield_process_format_form
  // so we need to undo it
  $form['field_address'][$lang][0]['#process'][] = 'cel_admin_fix_sub_administrative_area';
  $form['#submit'][] = 'cel_admin_form_partner_node_form_submit';
}

/**
 * Force the sub_administrative_area to be hidden.
 *
 * Unfortunately, the addressfield module is super aggressive on how it renders forms. If it finds
 * any fields that it recognizes as being part of the address, it will force it to be a textfield by
 * default. So, we need to make it hidden again -_-
 *
 * @param array $element
 * @return array The modified element
 */
function cel_admin_fix_sub_administrative_area($element) {
  if(isset($element['sub_administrative_area'])) {
    $element['sub_administrative_area']['#type'] = 'hidden';
  }
  return $element;
}

/**
 * Fetch the county name from Google's geocoding API
 *
 * Uses the geocoder module to fetch Google's canonical understanding of the address,
 * which should include county information when available.
 *
 * @param array $form
 * @param array &$form_state
 */
function cel_admin_form_partner_node_form_submit($form, &$form_state) {
  $lang = $form['language']['#value'];
  $address_item = $form_state['values']['field_address'][$lang][0];
  $address = geocoder_widget_parse_addressfield($address_item);
  
  $processor = ctools_get_plugins('geocoder', 'geocoder_handler', 'google');
  $geometry = call_user_func($processor['callback'], $address, array());
  
  if(!isset($geometry->data['geocoder_address_components'])) {
    return;
  }

  $found_sub_admin = false;
  $found_admin = (!empty($form_state['values']['field_address'][$lang][0]['administrative_area']));
  
  foreach($geometry->data['geocoder_address_components'] as $comp) {
    if(in_array('administrative_area_level_2', $comp->types)) {
      $form_state['values']['field_address'][$lang][0]['sub_administrative_area'] = $comp->short_name;
      $found_sub_admin = true;
    }

    if(!$found_admin && in_array('administrative_area_level_1', $comp->types)) {
      $form_state['values']['field_address'][$lang][0]['administrative_area'] = $comp->short_name;
      $found_admin = true;
    }

    if($found_sub_admin && $found_admin) {
      break;
    }
  }
}