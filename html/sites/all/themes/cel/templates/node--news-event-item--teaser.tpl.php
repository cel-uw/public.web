<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <?php
    // Hide comments, tags, and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
    hide($content['field_tags']);
    hide($content['field_subtitle']);
    hide($content['field_event_date']);
    hide($content['field_photos']);
  ?>

  <?php if(!empty($content['field_photos']['#items'])): ?>
    <?php $photo = reset($content['field_photos']['#items']); ?>
    <?php if(substr($photo['filemime'], 0, 6) === 'video/'): ?>
      <?php $photo_output = field_view_value('node', $node, 'field_photos', $photo, array('type' => 'file_rendered')); ?>
    <?php else: ?>
      <?php $photo_output = field_view_value(
        'node', 
        $node, 
        'field_photos', 
        $photo, 
        array(
          'type' => 'picture', 
          'settings' => array(
            'picture_group' => 'cel', 
            'fallback_image_style' => 'small',
            'image_link' => 'content',
          )
        )
      ); ?>
    <?php endif; ?>

    <div class="col-4 col-sm-4 col-lg-12">
      <?php print render($photo_output); ?>
    </div>
  <?php endif; ?>
  <header>
    <?php print render($title_prefix); ?>
    
    <?php if (!$page && $title && $wl_show_title): ?>
      <h4<?php print $title_attributes; ?>>
        <?php print $title; ?><?php if($wl_add_colon_to_title): print ":"; endif; ?>
        <?php if(!empty($wl_subtitle)): ?>
          <?php $wl_subtitle = '<br>' . $wl_subtitle; ?>
        <?php endif; ?>
        <? print $wl_subtitle ?>
      </h4>
    <?php endif; ?>
    
    <?php print render($title_suffix); ?>

    <?php if ($display_submitted): ?>
      <span class="submitted">
        <?php print $user_picture; ?>
        <?php print $submitted; ?>
      </span>
    <?php endif; ?>

    <?php if(!empty($content['field_event_date']['#items'])): ?>
      <dl class="dl-horizontal event-date">
        <dt>When:</dt> 
        <dd>
          <?php $event_date = reset($content['field_event_date']['#items']); ?>
          <?php $event_output = field_view_value('node', $node, 'field_event_date', $event_date, array('type' => 'date_default', 'settings' => array('format_type' => 'medium', 'fromto' => 'both'))); ?>
          <?php print render($event_output); ?>
        </dd>
      </dl>
    <?php endif; ?>
  </header>

  <?php print render($content); ?>

  <?php if (!empty($content['field_tags']) || !empty($content['links'])): ?>
    <footer>
      <?php print render($content['field_tags']); ?>
      <?php print render($content['links']); ?>
    </footer>
  <?php endif; ?>
</article> <!-- /.node -->
