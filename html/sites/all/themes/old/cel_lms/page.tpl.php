<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language; ?>" xml:lang="<?php print $language->language; ?>">

<?php global $user; ?>

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


   <div id="header"><div id="header-inner" class="clear-block">

      <?php if ($logo || $site_name || $site_slogan): ?>
        <div id="logo-title">

         <?php if ($logo): ?>
          <div id="logo"><a href="<?php print $base_path; ?>" title="<?php print t('Home'); ?>" rel="home"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" id="logo-image" usemap="#imgmaplms" style="padding:0px;" /></a></div>
          <map id="imgmaplms" name="imgmaplms">
            <area shape="rect" alt="College of Education" title="College of Education Homepage" coords="0,55,96,70" href="http://education.washington.edu " target="_blank" />
            <area shape="rect" alt="University of Washington" title="University of Washington Homepage" coords="118,55,235,70" href="http://www.washington.edu " target="_blank" />
          </map>          
         <?php else:  ?>
          <div id="logo"><a href="<?php print $base_path; ?>" title="<?php print t('Home'); ?>" rel="home"><img src="<?php print $base_path . $directory; ?>/images/logo.png" alt="<?php print t('Home'); ?>" id="logo-image" /></a></div>
         <?php endif; ?>

         <?php if ($site_name): ?>
          <div id='site-name'>
            <a href="<?php print $base_path; ?>" title="<?php print t('Home'); ?>" rel="home">
              <strong><?php print $site_name; ?></strong>
            </a>
          </div>
         <?php endif; ?>

         <?php if ($site_slogan): ?>
          <div id='site-slogan'><?php print $site_slogan; ?></div>
         <?php endif; ?>

        </div> <!-- /#logo-title -->
      <?php endif; ?>
      
      <div id="header-right">

      <?php if (!$is_front && $header): ?>
        <div id="header-blocks" class="region region-header">
          <?php print $header; ?>
        </div> <!-- /#header-blocks -->      
      <?php elseif ($mission): ?>
        <div id="mission-wrapper">
          <!--<div id="mission-w"></div>-->
          <div id="mission"><?php print $mission; ?></div>
        </div>  
      <?php endif; ?>
      
      </div>


    </div></div> <!-- /#header-inner, /#header -->

    
    <div id="navbar"><div id="navbar-inner" class="region region-navbar">
    <!-- 
     <?php if (!$is_front) : ?>
      <div id="navbar-user">
       
       <?php if ($user->uid > 0) : ?>
        <?php if (variable_get('cel_training_server', 0)) print '<span id="training-notice">TRAINING</span>';?>
        <ul class="links">
          <li><?php print l('My Account', 'user/'.$user->uid ); ?></li>
          <li><?php print l('Logout '.$user->name,'logout' ); ?></li>
        </ul>
       <?php else : ?>
        <ul class="links">
          <li><?php print l('Login', 'user'); ?></li>
        </ul>
       <?php endif; ?>
      </div>
     <?php endif; ?>
    -->
      <a name="navigation" id="navigation"></a>
      
     <!--
     <?php if (FALSE) : // replaced with Nice Menus ?>
      <div id="navbar-primary-links"><?php print theme('links', menu_primary_links(), array('class' => 'links primary-links'))  ?></div>
     <?php endif; ?>
     -->

      <?php print $navbar; ?>

      <?php if ($search_box): ?>
        <div id="search-box">
          <?php print $search_box; ?>
        </div> <!-- /#search-box -->
      <?php endif; ?>


    </div></div> <!-- /#navbar-inner, /#navbar -->

    <?php
      // login / logout tabs
      if(!$is_front) {
        if(!$logged_in) {
          print l('', 'user', array('html' => true, 'attributes' => array('id' => 'login-tab')));        
        } else {
          print l('', 'logout', array('html' => true, 'attributes' => array('id' => 'login-tab', 'class' => 'logout')));
        }
      } 
    ?>

    <div id="main"><div id="main-inner" class="clear-block<?php if ($search_box || $showPrimary || $secondary_links || $navbar) { print ' with-navbar'; } ?>">


        <?php if ($is_front && $spotlight_image) : ?>
          <div id="spotlight">
            <?php print $spotlight_image; ?>
          </div>
          <!-- sibling block -->
          <div id="spotlight-sibling" class="sidebar-block">
            <?php print $spotlight_sibling; ?>
          </div>
          <div class="clear"></div>
        <?php endif; ?>

      <?php if($is_front) print $messages; ?>

      <div id="content"><div id="content-inner">

        <?php if ($content_top): ?>
          <div id="content-top" class="region region-content_top">
            <?php print $content_top; ?>
          </div> <!-- /#content-top -->
        <?php endif; ?>
	<?php if ($progress_indicator) print $progress_indicator;?>
        <?php if ($breadcrumb or $title or $tabs or $help or $messages): ?>
          <div id="content-header">
<?php include('flash-check.php'); //not sure if we need this on the home page?>
           <?php if(!$is_front) print $messages; // messesages printed above for homepage ?>
           <?php if ($title && !$is_front): ?>
             <h1 class="title"><?php print $title; ?></h1>
           <?php endif; ?>
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

      <div id="footer-message">
      <?php //print $footer_message; ?>
      
      &copy; <?php print date('Y'); ?>
      University of Washington
      <img src="<?php print $base_path . $directory; ?>/images/footer-w.gif" id="footer-w" />
      Center for Educational Leadership
      |      
      <?php print l('Privacy Policy','privacy');?>&nbsp;|&nbsp;<?php print l('Terms and Conditions','terms');?><br/>For more information or technical support, please contact <?php print l('5D Support',variable_get('report_tech_problem_url','feedback')); ?></div>
      
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