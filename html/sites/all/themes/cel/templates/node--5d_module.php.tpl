<?php
// Hide comments, tags, and links now so that we can render them later.
hide($content['comments']);
hide($content['links']);
hide($content['field_tags']);
hide($content['body']);
hide($content['field_body_auth_user']);

?>
<article class="<?php print $classes; ?>" id="node-<?php print $node->nid; ?>"<?php print $attributes; ?>>>
  
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

  <?php 
  // load permissions class
  module_load_include('php', 'cel_5d_course_registration', 'classes/permissions_class');     
  $permissionsClass = new PermissionsClass();
  
  if($permissionsClass->checkCurrUsrAccessModuleAuthUserBodyTxt($node->nid)){
    print render($content['field_body_auth_user']); 
  } else {
    print render($content['body']);
  }
  
  ?>
</article> <!-- /.node -->