<?php // $Id: page.tpl.php $ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language; ?>" xml:lang="<?php print $language->language; ?>">

<head>
  <title><?php print $head_title; ?></title>
  <?php print $head; ?>
  <?php print $styles; ?>
 <script src="<?php print $base_path;?>AC_OETags.js" type="text/javascript"></script>
 <script src="<?php print $base_path;?>flashdetect.js" type="text/javascript"></script>
  <!--[if IE]>
    <?php if (file_exists($directory . '/ie.css')): ?>
      <link rel="stylesheet" href="<?php print $base_path . $directory; ?>/styles/ie.css" type="text/css">
    <?php else: ?>
      <link rel="stylesheet" href="<?php print $base_path . $basictheme_directory; ?>/styles/ie.css" type="text/css">
    <?php endif; ?>
  <![endif]-->
  <?php print $scripts; ?>
</head>

<body class="<?php print $body_classes; ?>">
  <div id="page" class="container showgrid"><div id="page-inner">

    <a name="top" id="navigation-top"></a>
    <div id="mini-nav">
     <?php
     global $user;
     if($user->uid > 0): ?>
      <div id="mini-nav-inner">
      <?php if (variable_get('cel_training_server', 0)) print '<span id="training-notice">TRAINING</span>';?>
        <span id="mini-nav-user">
           Welcome <?php print $user->name; ?>
        </span>
        <ul class="links">
          <li><?php print l(t('My Account'),'user/'.$user->uid .'/edit' ); ?></li><!--
       --><li><?php print l(t('Logout'),'logout' ); ?></li>
        </ul>
      </div>
     <?php endif; ?>
    </div>

    <div id="header"><div id="header-inner" class="clear-block">

      <?php if ($logo || $site_name || $site_slogan): ?>
        <div id="logo-title">

          <?php if ($logo): ?>
            <div id="logo"><a href="<?php print $base_path; ?>" title="<?php print t('Home'); ?>" rel="home"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" id="logo-image" /></a></div>
          <?php else:  ?>
            <div id="logo"><a href="<?php print $base_path; ?>" title="<?php print t('Home'); ?>" rel="home"><img src="<?php print $base_path . $directory; ?>/images/logo.png" alt="<?php print t('Home'); ?>" id="logo-image" /></a></div>
          <?php endif; ?>

          <?php if ($site_name): ?>
            <?php
              // Use an H1 only on the homepage
              $tag = $is_front ? 'h1' : 'div';
            ?>
            <<?php print $tag; ?> id='site-name'>
              <a href="<?php print $base_path; ?>" title="<?php print t('Home'); ?>" rel="home">
                <strong><?php print $site_name; ?></strong>
              </a>
            </<?php print $tag; ?>>
          <?php endif; ?>

          <?php if ($site_slogan): ?>
            <div id='site-slogan'><?php print $site_slogan; ?></div>
          <?php endif; ?>

        </div> <!-- /#logo-title -->
      <?php endif; ?>

      <?php if ($header): ?>
        <div id="header-blocks" class="region region-header">
          <?php print $header; ?>
        </div> <!-- /#header-blocks -->
      <?php endif; ?>

    </div></div> <!-- /#header-inner, /#header -->

   <?php
     $showPrimary = count($primary_links) - 1;//don't show it if it only contains the Home link.
   ?>
   <?php if ($search_box || $showPrimary || $secondary_links || $navbar): ?>
    <div id="navbar"><div id="navbar-inner" class="region region-navbar">

          <a name="navigation" id="navigation"></a>



          <?php if ($showPrimary):  ?>
            <div id="primary">
              <?php print theme('links', $primary_links); ?>
            </div> <!-- /#primary -->
          <?php endif; ?>

          <?php if ($secondary_links): ?>
            <div id="secondary">
              <?php print theme('links', $secondary_links); ?>
            </div> <!-- /#secondary -->
          <?php endif; ?>

          <?php if ($search_box): ?>
            <div id="search-box">
              <?php print $search_box; ?>
            </div> <!-- /#search-box -->
          <?php endif; ?>

          <?php print $navbar; ?>

    </div></div> <!-- /#navbar-inner, /#navbar -->
   <?php endif; ?>

    <div id="main"><div id="main-inner" class="clear-block<?php if ($search_box || $showPrimary || $secondary_links || $navbar) { print ' with-navbar'; } ?>">

      <div id="content"><div id="content-inner">

        <?php if ($mission): ?>
          <div id="mission"><?php print $mission; ?></div>
        <?php endif; ?>

        <?php if ($content_top): ?>
          <div id="content-top" class="region region-content_top">
            <?php print $content_top; ?>
          </div> <!-- /#content-top -->
        <?php endif; ?>
		  <?php if ($progress_indicator) print $progress_indicator;?>
        <?php if ($breadcrumb or $title or $tabs or $help or $messages): ?>
          <div id="content-header">
             <?php print ((!$progress_indicator)?$breadcrumb:''); ?>
             <h1 class="title"><?php print $title; ?>&nbsp;</h1>
<noscript>
	<h2 class="messages">It looks like Javascript is turned off in your browser. You must enable it before you can continue. If you are unable to turn on Javascript, please contact your district's IT department. <br/><br/></h2>
</noscript>
<?php	if ( strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') ) { ?>
	<h2 class="messages">
	In order to continue with your assessment please download and install the following browser software:<br/><br/><a href="http://www.microsoft.com/windows/downloads/ie/getitnow.mspx">Internet Explorer 7+</a><br/><br/>
	If you are unable to install these items please contact your district's IT department.<br/><br/>
	</h2>
<?php	} ?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
// Check to see if the version meets the requirements for playback
if (!hasReqestedFlashVersion) {
	var alternateContent = '<h2 class="messages">In order to continue with your assessment please download and install the following browser software:<br/><br/>'
	+ '<a href="http://www.adobe.com/go/getflash/">Get Flash</a><br/><br/>You will need to have ActiveX controls enabled to run Flash.<br/> If you are unable to install these items please contact your district\'s IT department.<br/><br/></h2>';
	document.write(alternateContent);  // insert non-flash content
}
//--><!]]>
</script>
            <?php print $messages; ?>
            <?php if ($tabs): ?>
              <div class="tabs"><?php print $tabs; ?></div>
            <?php endif; ?>
            <?php print $help; ?>
          </div> <!-- /#content-header -->
        <?php endif; ?>

        <div id="content-area">
          <?php print $content; ?>
        </div>

        <?php if ($feed_icons): ?>
          <div class="feed-icons"><?php print $feed_icons; ?></div>
        <?php endif; ?>

        <?php if ($content_bottom): ?>
          <div id="content-bottom" class="region region-content_bottom">
            <?php print $content_bottom; ?>
          </div> <!-- /#content-bottom -->
        <?php endif; ?>

      </div></div> <!-- /#content-inner, /#content -->

      <?php if ($left): ?>
        <div id="sidebar-left"><div id="sidebar-left-inner" class="region region-left">
          <?php print $left; ?>
        </div></div> <!-- /#sidebar-left-inner, /#sidebar-left -->
      <?php endif; ?>

      <?php if ($right): ?>
        <div id="sidebar-right"><div id="sidebar-right-inner" class="region region-right">
          <?php print $right; ?>
        </div></div> <!-- /#sidebar-right-inner, /#sidebar-right -->
      <?php endif; ?>

    </div></div> <!-- /#main-inner, /#main -->

    <div id="footer"><div id="footer-inner" class="region region-footer">

      <div id="footer-message"><?php print $footer_message; print l('Privacy Policy','privacy');?>&nbsp;|&nbsp;<?php print l('Terms and Conditions','terms');?><br/>For more information or technical support, please contact <?php print l('5D Support',variable_get('report_tech_problem_url','feedback')); ?></div>
      <?php print $footer; ?>
    </div></div> <!-- /#footer-inner, /#footer -->
    <?php if($site_by){print $site_by; } ?>
  <?php print $closure; ?>
  </div></div> <!-- /#page-inner, /#page -->
  <?php if ($closure_region): ?>
    <div id="closure-blocks" class="region region-closure"><?php print $closure_region; ?></div>
  <?php endif; ?>

</body>
</html>