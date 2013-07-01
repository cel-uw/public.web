<?php
/**
 * @file
 * Template for a 1 column panel layout.
 *
 * This template provides a one column panel display layout.
 *
 * Variables:
 * - $id: An optional CSS id to use for the layout.
 * - $content: An array of content, each item in the array is keyed to one
 *   panel of the layout. This layout supports the following sections:
 *   - $content['middle']: Content.
 */
?>
<div class="panel-bootstrap-onecol-stacked panel-display" <?php if (!empty($css_id)) { print "id=\"$css_id\""; } ?>>
  <div class="row-fluid panel-col-middle panel-panel">
    <div class="span12"><?php print $content['middle']; ?></div>
  </div>
</div>
