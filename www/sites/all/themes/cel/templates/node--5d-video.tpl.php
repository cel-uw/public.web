<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <?php if($wl_show_title): ?>
    <header>
      <?php print render($title_prefix); ?>
      <?php if (!$page && $title): ?>
        <h2<?php print $title_attributes; ?>>
          <?php print $title; ?><?php if($wl_add_colon_to_title): print ":"; endif; ?>
          <?php print $wl_subtitle ?>
        </h2>
      <?php endif; ?>
      <?php print render($title_suffix); ?>

      <?php if ($display_submitted): ?>
        <span class="submitted">
          <?php print $user_picture; ?>
          <?php print $submitted; ?>
        </span>
      <?php endif; ?>
    </header>
  <?php endif; ?>

  <div class="uwtv-video">
    <iframe id="embed" src="http://mediaamp.org/players/iframeEmbed.html?pid=U8-EDC/<?php print $cel_5d_video_code; ?>&amp;u=UserNamesGoesHere" frameborder="0"></iframe>
  </div>

  <?php if (!empty($content['field_tags']) || !empty($content['links'])): ?>
    <footer>
      <?php print render($content['field_tags']); ?>
      <?php print render($content['links']); ?>
    </footer>
  <?php endif; ?>

  <?php print render($content['comments']); ?>  
</div> <!-- /.node -->
