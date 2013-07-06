<div class="node <?php print $node_classes; ?>" id="node-<?php print $node->nid; ?>"><div class="node-inner">

  <?php if ($page == 0): ?>
    <h2 class="title">
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
    </h2>
  <?php endif; ?>

  <?php if ($unpublished): ?>
    <div class="unpublished"><?php print t('Unpublished'); ?></div>
  <?php endif; ?>

  <?php if ($submitted): ?>
    <div class="submitted">
      <?php print $submitted; ?>
    </div>
  <?php endif; ?>

  <?php if (count($taxonomy)): ?>
    <div class="taxonomy"><?php print t(' in ') . $terms; ?></div>
  <?php endif; ?>

  <div class="content">
    <?php print $content; ?>
  </div>

  <?php if ($links): ?>
    <div class="links">
    <?php 
    $children = nodehierarchy_get_node_children_list($node->nid);
	$ogs = og_get_node_groups($node);
	if ( $ogs )
		$gid = reset(array_keys($ogs));
		
	$doc_link = '';
	if ( $children && ($page == 0)) $doc_link =  l(count($children).' document'.(count($children)>1?'s':''), 'node/'.$nid); //default
	if ( cel_access('edit any course_document content') ) {
		if ( $doc_link ) $doc_link = $doc_link.'<br/>';
		$doc_link .= l('Add new document', 'node/add/course-document', array('query'=>array('edit'=>array('parent'=>$node->nid), 'destination' => 'node/'.$node->nid.'/children', 'gids'=>array($gid))));
	}
	?>
      <span class="links inline"><?php print $doc_link;?></span>
      <?php print $links; ?>
    </div>
  <?php endif; ?>
 
</div></div> <!-- /node-inner, /node -->
