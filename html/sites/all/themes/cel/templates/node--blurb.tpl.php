<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <header>
    <?php print render($title_prefix); ?>
    
    <?php if (!$page && $title && $wl_show_title): ?>
      <h4<?php print $title_attributes; ?>>
        <?php if($cel_collapsible): ?>
          <a href="#" class="collapse-toggle" data-toggle="collapse" data-target="#node-<?php print $node->nid; ?>-content">
        <?php endif; ?>

        <?php print $title; ?><?php if($wl_add_colon_to_title): print ":"; endif; ?>
        <?php print $wl_subtitle ?>

        <?php if($cel_collapsible): ?>
          <span class="caret"></span></a>
        <?php endif; ?>
      </h4>
    <?php elseif ($cel_collapsible): ?>
      <button type="button" class="btn" data-toggle="collapse" data-target="#node-<?php print $node->nid; ?>-content">
        <?php if($cel_collapsed): ?>
          Expand
        <?php else: ?>
          Collapse
        <?php endif; ?>
      </button>
    <?php endif; ?>
    
    <?php print render($title_suffix); ?>

    <?php if ($display_submitted): ?>
      <span class="submitted">
        <?php print $user_picture; ?>
        <?php print $submitted; ?>
      </span>
    <?php endif; ?>
  </header>

  <div id="node-<?php print $node->nid; ?>-content" class="<?php print $cel_collapse_content_classes; ?>">
    <?php
      // Hide comments, tags, and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      hide($content['field_tags']);
      print render($content);
    ?>

    <?php if (!empty($content['field_tags']) || !empty($content['links'])): ?>
      <footer>
        <?php print render($content['field_tags']); ?>
        <?php print render($content['links']); ?>
      </footer>
    <?php endif; ?>

    <?php print render($content['comments']); ?>
  </div>

</article> <!-- /.node -->
