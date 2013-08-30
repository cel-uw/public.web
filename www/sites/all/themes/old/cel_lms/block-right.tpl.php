<?php if ($is_front)
	include('block.tpl.php');
	else if ($block->content) {
		
	drupal_add_js('misc/drupal.js');
	drupal_add_js('misc/collapse.js');

	?>
		<div id="block-<?php print $block->module . '-' . $block->delta; ?>" class="block <?php print $block_classes; ?>"><div class="block-inner">

		<?php 
		//swap out title for course-specific label
		if ( $course = og_get_group_context() ) {
			switch( $block->delta ) {
			case 'Group_Files_Lists-block_1':
				if ( $course->field_documents_label[0]['value'] )
					$block->subject = $course->field_documents_label[0]['value'];
				break;
			case 'Group_Files_Lists-block_2':
				if ( $course->field_assignments_label[0]['value'] )
					$block->subject = $course->field_assignments_label[0]['value'];
				break;
			case 'Group_Files_Lists-block_3':
				if ( $course->field_lessons_label[0]['value'] )
					$block->subject = $course->field_lessons_label[0]['value'];
				break;
			case 'Group_Files_Lists-block_4':
				if ( $course->field_videos_label[0]['value'] )
					$block->subject = $course->field_videos_label[0]['value'];
				break;
			case 'Group_Files_Lists-block_5':
				if ( $course->field_links_label[0]['value'] )
					$block->subject = $course->field_links_label[0]['value'];
				break;
				
			}
		}
	?>


		  <?php if ($block->subject): ?>
		    <fieldset class="<?php if ( $block->bid != 130) print 'collapsible collapsed'; ?> "><legend><?php print $block->subject; ?></legend>
		  <?php endif; ?>

		  <div class="content">
		    <?php print $block->content; ?>
		  </div>

		  <?php if ($block->subject): ?>
		    </fieldset>
		  <?php endif; ?>

		  <?php print $edit_links; ?>

		</div></div> <!-- /block-inner, /block -->
<?php } ?>