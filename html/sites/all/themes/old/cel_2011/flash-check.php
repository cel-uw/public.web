<?php
  // check whether we're in the Video Assessment app
  $in_assessment_app = FALSE;
  $menu_tree = menu_tree_page_data('primary-links');
  foreach ($menu_tree as $menu_item) {
    if ($menu_item['link']['title'] == 'Video Assessment' && $menu_item['link']['in_active_trail']) {
      $in_assessment_app = TRUE;
    }
  }
?>
<?php if ($in_assessment_app) : ?>
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
<?php endif; ?>
