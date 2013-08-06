<?php
/**
 * @file
 * Template for a 3 column 33/33/33 panel layout.
 *
 * This template provides a three column panel display layout, with
 * additional areas for the top and the bottom.
 *
 * Variables:
 * - $id: An optional CSS id to use for the layout.
 * - $content: An array of content, each item in the array is keyed to one
 *   panel of the layout. This layout supports the following sections:
 *   - $content['top']: Content in the top row.
 *   - $content['left']: Content in the left column.
 *   - $content['middle']: Content in the middle column.
 *   - $content['right']: Content in the right column.
 *   - $content['bottom']: Content in the bottom row.
 */
?>
<div class="panel-bootstrap-3col-33-33-33-stacked panel-display" <?php if (!empty($css_id)) { print "id=\"$css_id\""; } ?>>
  <?php if ($content['top']): ?>
    <div class="row panel-col-top panel-panel">
      <div class="col-lg-12"><?php print $content['top']; ?></div>
    </div>
  <?php endif; ?>

  <div class="row center-wrapper">
    <div class="panel-col-middle col-lg-4 col-lg-push-4 panel-panel">
      <?php print $content['middle']; ?>
    </div>
    <div class="panel-col-first col-lg-4 col-lg-pull-4 panel-panel">
      <?php print $content['left']; ?>
    </div>
    <div class="panel-col-last col-lg-4 panel-panel">
      <?php print $content['right']; ?>
    </div>
  </div>

  <?php if ($content['bottom']): ?>
    <div class="row panel-col-bottom panel-panel">
      <div class="col-lg-12"><?php print $content['bottom']; ?></div>
    </div>
  <?php endif; ?>
</div>
