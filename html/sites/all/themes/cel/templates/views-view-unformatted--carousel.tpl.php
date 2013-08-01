<?php

/**
 * @file
 * Default simple view template to display a list of rows.
 *
 * @ingroup views_templates
 */
?>
<?php if (!empty($title)): ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>

<div class="carousel slide" id="featured_content">
  <?php if(count($rows) > 1 && count($rows) < 8): ?>
    <ol class="carousel-indicators">
      <?php $i=0; ?>
      <?php $class='class="class"' ?>
      <?php foreach($rows as $id => $row): ?>
        <li data-target="#featured_content" data-slide-to="<?php print $i; ?>" <?php print $class; ?>></li>
        <?php $i++; ?>
        <?php $class=""; ?>
      <?php endforeach; ?>
    </ol>
  <?php endif; ?>

  <div class="carousel-inner">
    <?php $first = true; ?>
    <?php foreach ($rows as $id => $row): ?>
      <?php if(!$classes_array[$id] && $first): ?>
        <?php $classes_array[$id] = ""; ?>
      <?php endif; ?>

      <?php if($first): ?>
        <?php $classes_array[$id] .= " active"; ?>
        <?php $first = false; ?>
      <?php endif; ?>

      <div<?php if ($classes_array[$id]) { print ' class="' . $classes_array[$id] .'"';  } ?>>
        <?php print $row; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
