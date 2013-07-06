<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language; ?>" xml:lang="<?php print $language->language; ?>">

<?php global $user; ?>
<?php //print_r($_SESSION)?>
<head>
  <title><?php print $head_title; ?></title>
  <?php print $head; ?>
  <?php print $styles; ?>
 <script src="<?php print $base_path;?>AC_OETags.js" type="text/javascript"></script>
 <script src="<?php print $base_path;?>flashdetect.js" type="text/javascript"></script>

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
          <map name="imgmaplms" id="imgmaplms">
	      <area shape="rect" coords="910,0,979,56" href="http://www.washington.edu/" alt="University of Washington Home Page" />
		  <area shape="rect" coords="364,85,570,112" href="http://education.washington.edu/" alt="UW College of Education Home Page" />
		  <area shape="rect" coords="116,85,349,112" href="http://www.washington.edu/" alt="University of Washington Home Page" />
		  <area shape="rect" coords="46,27,748,85" href="/" title="UW Center for Educational Leadership" alt="UW Center for Educational Leadership" />
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


    </div>
    <?php
    // login / logout tabs
	if(!$logged_in) 
	{
		if(cel_5d_helper_check_nav_login_visibility()){
	  		print l('', 'user', array('html' => true, 'attributes' => array('id' => 'login-tab')));
		}        
	} else {
	  print l('', 'user', array('html' => true, 'attributes' => array('id' => 'my-cel')));
	  print l('', 'logout', array('html' => true, 'attributes' => array('id' => 'logout-tab')));
	}
    ?>
    </div> <!-- /#navbar-inner, /#navbar -->

    

    <div id="main"><div id="main-inner" class="clear-block<?php if ($search_box || $showPrimary || $secondary_links || $navbar) { print ' with-navbar'; } ?>">

		<!-- sibling block - LOGIN BLOCK APPEARS HERE -->
		<div id="spotlight-sibling" class="sidebar-block">
		<?php print $spotlight_sibling; ?>
		</div>
		<div class="clear"></div>

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

      <?php print $footer; ?>
      <div id="footer-message">
      <?php //print $footer_message; ?>
      <div id="foot-copyr">&copy; <?php print date('Y'); ?> University of Washington&nbsp;|&nbsp;
      Center for Educational Leadership&nbsp;|&nbsp;  
      <?php print l('Privacy Policy','privacy');?>&nbsp;|&nbsp;<?php print l('Terms and Conditions','terms');?></div>
      <div id="foot-image"><img src="/sites/all/themes/cel_2011/images/2011-redesign/footer-w-2011-int.png" id="footer-imagemap" usemap="#m_footerw2011" /><map name="m_footerw2011" id="m_footerw2011">
	  <area shape="rect" coords="56,29,112,79" href="http://www.washington.edu/" title="University of Washington Homepage" alt="University of Washington Homepage" />
      <area shape="rect" coords="0,29,44,79" href="/" title="UW CEL Homepage" alt="UW CEL Homepage" /></map></div>
      <div id="foot-siteby"><?php if($site_by){ print $site_by; } ?></div>
      </div>
    </div></div> <!-- /#footer-inner, /#footer -->
  <?php print $closure; ?>
  </div></div> <!-- /#page-inner, /#page -->
  <?php if ($closure_region): ?>
    <div id="closure-blocks" class="region region-closure"><?php print $closure_region; ?></div>
  <?php endif; ?>

</body>
</html>