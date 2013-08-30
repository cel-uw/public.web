<?php
// $Id: views-view-table.tpl.php,v 1.8 2009/01/28 00:43:43 merlinofchaos Exp $
/**
 * @file views-view-table.tpl.php
 * Template to display a view as a table.
 *
 * - $title : The title of this group of rows.  May be empty.
 * - $header: An array of header labels keyed by field id.
 * - $fields: An array of CSS IDs to use for each field id.
 * - $class: A class or classes to apply to the table, based on settings.
 * - $row_classes: An array of classes to apply to each row, indexed by row
 *   number. This matches the index in $rows.
 * - $rows: An array of row items. Each row is an array of content.
 *   $rows are keyed by row number, fields within rows are keyed by field ID.
 * @ingroup views_templates
 */

  	    drupal_add_js(path_to_theme().'/jquery.dimensions.js');
      	drupal_add_js(path_to_theme().'/jquery.tooltip.js');
     	drupal_add_css(path_to_theme().'/jquery.tooltip.css');
		 $js = ' $(document).ready(function() {
		
		    $(".has-tooltip").tooltip({ 
		        bodyHandler: function() { 
		        	if ( $(this).find(".tooltip-body") )
		            	return $(this).find(".tooltip-body").html();
		        }, 
		        showURL: false 
		    });
		});';
		 drupal_add_js($js,'inline');

?>
<table class="<?php print $class; ?>">
  <?php if (!empty($title)) : ?>
    <caption><?php print $title; ?></caption>
  <?php endif; ?>
  <thead>
    <tr>
      <?php foreach ($header as $field => $label): if ($field != 'body' && $field != 'field_description_2_value' ) :?>
        <th class="views-field views-field-<?php print $fields[$field]; ?>">
          <?php print $label; ?>
        </th>
        <?php endif; ?>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php 
    if (!user_access('edit any course_document content')) {
       global $user;
      $oglist = og_get_subscriptions($user->uid);
       
      $i = 0;
      foreach($rows as $count => $row) {
      		if ( isset($row['nid'])) { //this should be the course link item
				$course_parts = explode('groupdash/', $row['nid']);
				$course_parts = explode('"',$course_parts[1]);
				$course_nid = $course_parts[0];
				if ( !array_key_exists($course_nid, $oglist) ) {
					unset($rows[$count]);
				}
	      		}
	  }
       
     }
    
    foreach ($rows as $count => $row): 
 ?>
      <tr class="has-tooltip <?php print implode(' ', $row_classes[$count]); ?>">
        <?php foreach ($row as $field => $content): if ($field != 'body' && $field != 'field_description_2_value') :?>
          <td class="views-field views-field-<?php print $fields[$field]; ?>">
            <?php print str_replace('sites/default/files','system/files',$content); ?>
 	        <?php if  ($field == 'title' ) print '<div style="display:none;" class="tooltip-body"  ><label>What is this document?</label><br/>'.$row['body'].(isset($row['field_description_2_value']) ? '<label>How is it used?</label><br/>'.$row['field_description_2_value']:'').'</div>'; ?>
          </td>
         <?php endif; ?>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
