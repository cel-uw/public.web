<article id="node-<?php print $node->nid; ?>" class="media <?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <?php
    // Hide comments, tags, and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
    hide($content['field_tags']);
    hide($content['field_subtitle']);
    hide($content['field_event_date']);
    hide($content['field_photos']);
  ?>

  <div class="pull-left col-lg-4">
    <div class="media-object">
      <?php if(!empty($content['field_photos']['#items'])): ?>
        <div class="carousel slide">
          <?php if(count($content['field_photos']['#items']) > 1 && count($content['field_photos']['#items']) < 8): ?>
            <ol class="carousel-indicators">
              <?php $i=0; ?>
              <?php $class='class="active"' ?>
              <?php foreach($rows as $id => $row): ?>
                <li data-target="#node-<?php print $node->nid; ?>" data-slide-to="<?php print $i; ?>" <?php print $class; ?>></li>
                <?php $i++; ?>
                <?php $class=""; ?>
              <?php endforeach; ?>
            </ol>
          <?php endif; ?>

          <div class="carousel-inner">
            <?php if(!empty($content['field_photos']['#items'])): ?>
              <?php $active = 'active'; ?>
              <?php foreach($content['field_photos']['#items'] as $photo): ?>
                <div class="item <?php print $active; ?>">
                  <?php if(substr($photo['filemime'], 0, 6) === 'video/'): ?>
                    <?php $photo_output = field_view_value('node', $node, 'field_photos', $photo, array('type' => 'file_rendered')); ?>
                  <?php else: ?>
                    <?php $photo_output = field_view_value('node', $node, 'field_photos', $photo, array('type' => 'picture', 'settings' => array('picture_group' => 'cel', 'fallback_image_style' => 'small'))); ?>
                  <?php endif; ?>
                  <?php print render($photo_output); ?>
                </div>
                <?php $active = ''; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="media-body">
    <header>
      <?php print render($title_prefix); ?>
      
      <?php if (!$page && $title && $wl_show_title): ?>
        <div class="media-heading">
          <h4<?php print $title_attributes; ?>>
            <?php print $title; ?><?php if($wl_add_colon_to_title): print ":"; endif; ?>
            <?php print $wl_subtitle ?>
          </h4>
        </div>
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
            <?php print render(field_view_value('node', $node, 'field_event_date', reset($content['field_event_date']['#items']), array('type' => 'date_default', 'settings' => array('format_type' => 'medium', 'fromto' => 'both')))); ?>
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
  </div>

  <?php print render($content['comments']); ?>
</article> <!-- /.node -->
