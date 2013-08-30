<?php
// $Id: views-view.tpl.php,v 1.13 2009/06/02 19:30:44 merlinofchaos Exp $
/**
 * @file views-view.tpl.php
 * Main view template
 *
 * Variables available:
 * - $css_name: A css-safe version of the view name.
 * - $header: The view header
 * - $footer: The view footer
 * - $rows: The results of the view query, if any
 * - $empty: The empty text to display if the view is empty
 * - $pager: The pager next/prev links to display, if any
 * - $exposed: Exposed widget form/info to display
 * - $feed_icon: Feed icon to display, if any
 * - $more: A link to view more, if any
 * - $admin_links: A rendered list of administrative links
 * - $admin_links_raw: A list of administrative links suitable for theme('links')
 *
 * @ingroup views_templates
 */
?>
<div class="view view-<?php print $css_name; ?> view-id-<?php print $name; ?> view-display-id-<?php print $display_id; ?> view-dom-id-<?php print $dom_id; ?>">
  <?php if ($admin_links): ?>
    <div class="views-admin-links views-hide">
      <?php print $admin_links; ?>
    </div>
  <?php endif; ?>
  <?php if ($header): ?>
    <div class="view-header">
      <?php print $header; ?>
    </div>
  <?php endif; ?>

  <?php if ($exposed): ?>
    <div class="view-filters">
      <?php 
      // Rip out the group options and replace them with Partnerships only
      // **** YUCK *****/
      $option_start = '<select name="group_nid" class="form-select" id="edit-group-nid" >';
      $pos = strpos($exposed, $option_start);
      $start = substr($exposed,0,$pos).$option_start;
      $end = strstr(strstr($exposed,$option_start),'</select>');
      
      $ogs = db_query("SELECT node.nid, title
				FROM og INNER JOIN node ON og.nid = node.nid
	 			INNER JOIN lms_course ON node.vid = lms_course.vid AND node.nid = lms_course.nid WHERE node.status = 1 AND lms_course.course_type = 'partnership' ORDER BY title ASC");
      
      $options = '<option value="All" '.(!isset($_REQUEST['group_nid'])?'selected="selected"':'').'>&lt;Any&gt;</option>';
      while ($og = db_fetch_array($ogs)) {
      	$selected = '';
      	if ( $og['nid'] == @$_REQUEST['group_nid'])
      		$selected = 'selected="selected"';
      	$options .= '<option value="'.$og['nid'].'" '.$selected.' >'.$og['title'].'</option>';
      }
      
      $exposed = $start.$options.$end;
      
      print $exposed; ?>
    </div>
  <?php endif; ?>

  <?php if ($attachment_before): ?>
    <div class="attachment attachment-before">
      <?php print $attachment_before; ?>
    </div>
  <?php endif; ?>

  <?php if ($rows): ?>
    <div class="view-content">
      <?php print $rows; ?>
    </div>
  <?php elseif ($empty): ?>
    <div class="view-empty">
      <?php print $empty; ?>
    </div>
  <?php endif; ?>

  <?php if ($pager): ?>
    <?php print $pager; ?>
  <?php endif; ?>

  <?php if ($attachment_after): ?>
    <div class="attachment attachment-after">
      <?php print $attachment_after; ?>
    </div>
  <?php endif; ?>

  <?php if ($more): ?>
    <?php print $more; ?>
  <?php endif; ?>

  <?php if ($footer): ?>
    <div class="view-footer">
      <?php print $footer; ?>
    </div>
  <?php endif; ?>

  <?php if ($feed_icon): ?>
    <div class="feed-icon">
      <?php print $feed_icon; ?>
    </div>
  <?php endif; ?>

</div> <?php /* class view */ ?>
