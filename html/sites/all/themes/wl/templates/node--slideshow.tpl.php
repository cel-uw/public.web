<?php
// Hide comments, tags, and links now so that we can render them later.
hide($content['comments']);
hide($content['links']);
hide($content['field_tags']);
hide($content['field_photos']);
hide($content['field_style']);
?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix carousel slide"<?php print $attributes; ?>>
  <?php if(!empty($content['field_photos']['#items']) && count($content['field_photos']['#items']) > 1 && count($content['field_photos']['#items']) < 10): ?>
    <ol class="carousel-indicators">
      <?php for($i = 0; $i < count($content['field_photos']['#items']); $i++): ?>
        <li data-target="#node-<?php print $node->nid; ?>" data-slide-to="<?php print $i ?>" <?php if($i === 0){ print 'class="active"'; } ?>></li>
      <?php endfor; ?>
    </ol>
  <?php endif; ?>

  <div class="carousel-inner">
    <?php if(!empty($content['field_photos']['#items'])): ?>
      <?php foreach($content['field_photos']['#items'] as $photo): ?>
        <div class="item">
          <?php if(substr($photo['filemime'], 0, 6) === 'video/'): ?>
            <?php $photo_output = field_view_value('node', $node, 'field_photos', $photo, array('type' => 'file_rendered')); ?>
          <?php else: ?>
            <?php $photo_output = field_view_value('node', $node, 'field_photos', $photo, array('type' => 'image', 'settings' => array('image_style' => 'large'))); ?>
          <?php endif; ?>
          <?php print render($photo_output); ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div> <!-- /.node -->
