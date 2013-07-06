<?php // $Id: nakedpage.tpl.php $
//for bare report pages and such ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language; ?>" xml:lang="<?php print $language->language; ?>">

<head>
  <title><?php print $head_title; ?></title>
  <?php print $head; ?>
  <?php print $styles; ?>
  <!--[if IE]>
    <?php if (file_exists($directory . '/ie.css')): ?>
      <link rel="stylesheet" href="<?php print $base_path . $directory; ?>/styles/ie.css" type="text/css">
    <?php else: ?>
      <link rel="stylesheet" href="<?php print $base_path . $basictheme_directory; ?>/styles/ie.css" type="text/css">
    <?php endif; ?>
  <![endif]-->
  <?php print $scripts; ?>
</head>

<body class="<?php print $body_classes; ?>" style="background-color: #FFFFFF">
  <div id="page" class="container showgrid"  style="background-color: #FFFFFF"><div id="page-inner">
        <div id="content-area">
          <?php print $content; ?>
        </div>
  </div></div> <!-- /#page-inner, /#page -->
 
</body>
</html>