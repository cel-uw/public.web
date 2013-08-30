<div class="node <?php print $node_classes; ?>" id="node-<?php print $node->nid; ?>"><div class="node-inner">

  <?php if ($page == 0): ?>
  <div class="product-image">
    <?php print $field_image_cache_rendered;?>
    <div class="product-info product display">
       <?php print theme('uc_product_price', $node->sell_price, array('field'=>'sell_price', 'class' => array('display','price')));?></div>
      </div>
    <h2 class="title">
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
    </h2>

  <?php if ($unpublished): ?>
    <div class="unpublished"><?php print t('Unpublished'); ?></div>
  <?php endif; ?>

 
  <?php if (false) : //(count($taxonomy)): ?>
    <div class="taxonomy"><?php print t(' in ') . $terms; ?></div>
  <?php endif; ?>

  <div class="content">
    <?php //
    print '<h3><em>'.$node->field_lesson_name[0]['safe'].'</em></h3>';
    //strip out everything in front of the cart form
    $content = strstr($content,'<div class="add-to-cart">');
    print $content;
    ?>
  </div>
<br />
  <?php if ($links): ?>
    <div class="links">
      <?php print l('Preview of this video', $node->path); ?>
    </div>
  <?php endif; ?>
  <?php endif; ?>
  
  <?php if ($page == 1): ?>

  <?php if ($unpublished): ?>
    <div class="unpublished"><?php print t('Unpublished'); ?></div>
  <?php endif; ?>

<h2 class="title">
      <a href="<?php print $node_url; ?>"><?php print $title; ?></a>
</h2>
       <?php print '<h3><em>'.$node->field_lesson_name[0]['safe'].'</em></h3>'; ?>
    <div class="product-image">
    <?php print $field_image_cache_rendered;?>
    <div class="product-info product display">
       <?php print theme('uc_product_price', $node->sell_price, array('field'=>'sell_price', 'class' => array('display','price')));?></div>
      </div>
    
  <?php if (count($taxonomy)):/* ?>
    <div class="taxonomy"><?php print t(' in ') . $terms; ?></div>
  <?php */ endif; ?>

  <div class="content">
    <?php $content = strstr($content,'<div class="product-info model">'); ?>
    <?php print $content; ?>
  </div>

  <?php if ($links): ?>
    <div class="links">
      <?php print $links; ?>
    </div>
  <?php endif; ?>  <?php endif; ?>
</div></div> <!-- /node-inner, /node --><br clear="all" />
