<div class="node <?php print $node_classes; ?>" id="node-<?php print $node->nid; ?>"><div class="node-inner">

<?php if ($page == 0): ?>
    <h2 class="title">
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
    </h2>
  <?php endif; ?>
  <?php if ($unpublished): ?>
    <div class="unpublished"><?php print t('Unpublished'); ?></div>
  <?php endif; ?>

  <?php if (count($taxonomy)): ?>
    <div class="taxonomy"><?php print t(' in ') . $terms; ?></div>
  <?php endif; ?>

  <div class="content">
	<form action="/course/469/register" method="POST">
    	<?php //print $content; ?>
    	<?php print _course_registration_chekbox_patern_replace($content) ?>
    	<input type="submit" value="Register Now">
	</form>
  </div>
  

  <?php if ($links): ?>
    <div class="links">
      <?php print $links; ?>
    </div>
  <?php endif; ?>
  
  
  
</div></div> <!-- /node-inner, /node -->
