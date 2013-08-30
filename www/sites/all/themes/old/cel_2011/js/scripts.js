/* SCRIPTS FOR CEL REDESIGN 2011 
   CREATIVE COMMUNICATIONS */

var anim; 

function featureShuffle(num,iter){
	
	var tNum = num;
	var featCnt = $("body.front .view-homepage-features .views-row").length;
	
	if(iter == "new"){
		$("body.front #block-views-homepage_features-block_1 .intro-fade").animate({opacity:1},4000).fadeOut(1000,function(){
		$("body.front .view-homepage-features .views-row .views-field-field-feature-image-fid:eq(" + tNum + ")").fadeIn(4000);
		$("body.front .view-homepage-features .views-row .views-field-body:eq(" + tNum + ")").fadeIn(4000).fadeTo(200,.85).css("z-index","5000");
		$("body.front .view-homepage-features .views-row .views-field-title:eq(" + tNum + ")").css("background-color","#2a0057");
		featureShuffle((tNum+1),"reit");
		});
		}
	
	else{
		if(tNum == 0){ var prevNum = (featCnt-1); }
		else{ var prevNum = (tNum-1); }
		$("body.front .view-homepage-features .views-row .views-field-field-feature-image-fid:eq(" + prevNum + ")")
		.animate({opacity:1},4000,function(){ $("body.front .view-homepage-features .views-row .views-field-body")
		.fadeOut(1500);}).fadeOut(2000,function(){
				$("body.front .view-homepage-features .views-row .views-field-body").fadeOut(1000);
				$("body.front .view-homepage-features .views-row .views-field-field-feature-image-fid:eq(" + tNum + ")").fadeIn(4000);
				$("body.front .view-homepage-features .views-row .views-field-body:eq(" + tNum + ")").fadeIn(4000).fadeTo(200,.85).css("z-index","5000");
				$("body.front .view-homepage-features .views-row .views-field-title").css("background-color","#42167e");
				$("body.front .view-homepage-features .views-row .views-field-title:eq(" + tNum + ")").css("background-color","#2a0057");
				if((tNum+1) == featCnt){tNum = 0;}
				else{tNum = (tNum+1);}
				featureShuffle(tNum,"reit");
			});
		}
	return false;
	}


function homePageIntro(){
	$("body.front #block-views-homepage_features-block_1 .intro-fade .intro-fade-img").fadeOut(1000);
	$("body.front #block-views-homepage_features-block_1 .intro-fade .intro-fade-img").each(function(index){
	if(index == 0){ // 5 FOR NOW!
		$(this).fadeIn(5000,function(){
			$(this).parent(".intro-fade").children(".intro-fade-img:eq(" + (index+1) + ")").fadeIn(5000,function(){
				$(this).parent(".intro-fade").children(".intro-fade-img:eq(" + (index+2) + ")").fadeIn(5000,function(){
					$(this).parent(".intro-fade").children(".intro-fade-img:eq(" + (index+3) + ")").fadeIn(5000,function(){
						$(this).parent(".intro-fade").children(".intro-fade-img:eq(" + (index+4) + ")").fadeIn(5000,function(){
							anim = setTimeout("homePageIntro()",5000);
						});
					});
				});
			});
		});
	  }
   });
}
 
//function reCycle(){
//	$("body.front #block-views-homepage_features-block_1 .intro-fade .intro-fade-img").fadeOut(1000,function(){
//		homePageIntro();
//		});
//	}


// SETTING HEIGHT AND POSITION OF THESE DROPDOWN BOXES
function setHomeColHeight(){
	  var homeBlHeight = 0;
	  var pPosition = 0;
	  $("body.front #main-inner #content #content-inner #content-area .block").each(function(){
		var tHeight = $(this).height();
		if(tHeight > homeBlHeight){
			homeBlHeight = tHeight;
			}
		});
	  $("body.front #main-inner #content #content-inner #content-area .block").css("height",homeBlHeight + "px");
	  
	  $("body.front .plus").each(function(){
	  	var pPos = $(this).position();
	  	if(pPos.top > pPosition){
	  		pPosition = pPos.top;
	  		}
	  	});
	  $("body.front .plus").css({"top":pPosition + "px","position":"absolute"});
	}
	


$(document).ready(function() {
  
  //$('head').append('<link rel="stylesheet" href="/sites/all/themes/cel_2011/js/testing.css" type="text/css" />');
  $('#cel-assessment-assessee-response-form').find('.textarea-identifier').hide();
  if($("body.front").length > 0){  /*** EXECUTE IF ON HOME PAGE ***/
	  
	 //$("#content-area #block-block-34 .block-inner h2.title,#content-area #block-block-36 .block-inner h2.title").wrapInner('<a href="/professional-development">');
	 //$("#content-area #block-views-News_List_2011-block_1 .block-inner h2.title").wrapInner('<a href="/news">');
	 //$("#content-area #block-views-7ff332e7a668ab4abe3bd184c474d472 .block-inner h2.title").wrapInner('<a href="/products/33">');
	  
	  $("body.front .view-homepage-features .views-field-field-feature-image-fid").css("display","none");
	  
	  
	  /****************** NEWS COLUMN ******************/
	  var newsCnt = $("body.front .view-News-List-2011 .views-row").length;
	  $("body.front .view-News-List-2011 .views-row").each(function(index){
		if(index < 2){
			$(this).css("display","block");
			}
		});
	  if(newsCnt > 2){
		$("body.front #content-area #block-views-News_List_2011-block_1 .view-content").append('<div class="plus">&nbsp;</div>');
		}
	
		$("body.front #content-area #block-views-News_List_2011-block_1 .view-content .plus").click(function(){
			if($(this).attr("class") == "plus"){
				$(this).css({"position":"relative","top":"auto"});
				$("body.front #content-area #block-views-News_List_2011-block_1").css("height","auto");
				$("body.front .view-News-List-2011 .views-row").slideDown(400);
				$(this).addClass("minus");
				}
			else{
				$("body.front #content-area #block-views-News_List_2011-block_1").css("height","auto");
				$("body.front .view-homepage-store-announcement .views-row:gt(1)").slideUp(400); // BOTH!
				$("body.front .view-News-List-2011 .views-row:gt(1)").slideUp(400); // BOTH!
				$("body.front .plus").removeClass("minus"); // BOTH!
				$(this).animate({opacity: 1.0},500,function(){setHomeColHeight();});
				}
			});
		
		
		
		/****************** STORE COLUMN ******************/
		
		var storeCnt = $("body.front .view-homepage-store-announcement .views-row").length;
		  $("body.front .view-homepage-store-announcement .views-row").each(function(index){
			if(index < 2){
				$(this).css("display","block");
				}
			});
		  if(storeCnt > 2){
			$("body.front #content-area #block-views-7ff332e7a668ab4abe3bd184c474d472 .view-content").append('<div class="plus">&nbsp;</div>');
			}
		
		$("body.front #content-area #block-views-7ff332e7a668ab4abe3bd184c474d472 .view-content .plus").click(function(){
			if($(this).attr("class") == "plus"){
				$(this).css({"position":"relative","top":"auto"});
				$("body.front #content-area #block-views-7ff332e7a668ab4abe3bd184c474d472").css("height","auto");
				$("body.front .view-homepage-store-announcement .views-row").slideDown(400);
				$("body.front #content-area #block-views-7ff332e7a668ab4abe3bd184c474d472 .view-content .plus").addClass("minus");
				}
			else{
				$("body.front #content-area #block-views-7ff332e7a668ab4abe3bd184c474d472").css("height","auto");
				$("body.front .view-homepage-store-announcement .views-row:gt(1)").slideUp(400); // BOTH!
				$("body.front .view-News-List-2011 .views-row:gt(1)").slideUp(400); // BOTH!
				$("body.front .plus").removeClass("minus"); // BOTH!
				$(this).animate({opacity: 1.0},500,function(){setHomeColHeight();});
				}
			});
	  
	  
	  
	  // ADDS INTRO FADER TO HOME FEATURE CONTENT TYPE
	  $("body.front #block-views-homepage_features-block_1").append('<div class="intro-fade"><img class="intro-fade-img" src="/sites/all/themes/cel_2011/images/2011-redesign/home-intro-a.jpg" /><img class="intro-fade-img" src="/sites/all/themes/cel_2011/images/2011-redesign/home-intro-b.jpg" /><img class="intro-fade-img" src="/sites/all/themes/cel_2011/images/2011-redesign/home-intro-c.jpg" /><img class="intro-fade-img" src="/sites/all/themes/cel_2011/images/2011-redesign/home-intro-d.jpg" /><img class="intro-fade-img" src="/sites/all/themes/cel_2011/images/2011-redesign/home-intro-e.png" /></div>');
	  
	  var introImgCnt = $("body.front #block-views-homepage_features-block_1 .intro-fade .intro-fade-img").length;
	  

	}
	
  $("#navbar .block-search #search-block-form #edit-search-block-form-1").focus(function(){	
  	$(this).animate({width: '160px'}).css({"background-image":"none","padding-left":"5px","background-color":"#ffffff"});
  	});
  
  $("#navbar .block-search #search-block-form #edit-search-block-form-1").blur(function(){	
  	$(this).animate({width: '110px'}).css({"background":"#000000 url('/sites/all/themes/cel_2011/images/2011-redesign/magglass-off.png') no-repeat 2px 4px","padding-left":"15px"});
  	});
  
  //$("body.not-front #page #content-area .node-inner .content p:eq(0) b:eq(0), body.not-front #page #content-area .node-inner .content p:eq(0) strong:eq(0)").css({"color":"#bd6a2d","font-size":"1.1em"});
  // ADDS ITEM TO MENU BAR GIVING ACCESS TO LOGIN WIDGET
  
  
  $("body.not-front #page #content-area .node-inner .content h2,body.not-front #page #content-area .node-inner .content h3,body.not-front #page #content-area .node-inner .content h4,body.not-front #page #content-area .node-inner .content h5,body.not-front #page #content-area .node-inner .content h6").next("p,ul,ol").css("margin-top","12px");

  $('#navbar ul.nice-menu li a[href~="contact-us"]').parent("li").children("ul").remove();
  
  /*
  $('#sidebar-left ul.menu li.expanded.first ul li.active-trail').parents('li.expanded.first').css({
  		"margin-left": "-14px",
		"padding-left": "14px",
		"background": "url(/sites/all/themes/cel_2011/images/2011-redesign/navarrow.png) no-repeat 0 4px"
  		});
  */
  		
  	$('#sidebar-left ul.menu li.leaf a.active, #sidebar-left ul.menu li.expanded a.active').parent('li').css({
  		"margin-left": "-12px",
		"padding-left": "12px",
		"background": "url(/sites/all/themes/cel_2011/images/2011-redesign/navarrow.png) no-repeat 0 4px"
  		});
  		
  	$("#sidebar-left ul.menu li.active-trail").css({
  		"margin-left": "-12px",
		"padding-left": "12px",
		"background": "url(/sites/all/themes/cel_2011/images/2011-redesign/navarrow.png) no-repeat 0 4px"
  		});

  
  
  /*
  $("#navbar #login-tab").click(function(){
  	$('#spotlight-sibling').animate({opacity: 'toggle'}, 500);
  	});
  $("#navbar #my-cel").click(function(){
    $('#spotlight-sibling #block-block-7').remove();
  	$('#spotlight-sibling').animate({opacity: 'toggle'}, 500);
  	});
  */
  
  // FACULTY PAGE STUFF
  /*$("body.page-about-cel-staff #content-area .content strong").click(function(){
  	$(".fac-text").slideUp("500");
  	$(".fac-text").parents("p").removeClass("fac-text-bg");
  	$(this).parent("p").children(".fac-text").slideToggle("500");
  	$(this).parent("p").toggleClass("fac-text-bg");  	
  	});*/
  
  
  /*** EXECUTE IF ON HOME PAGE ***/
if($("body.front").length > 0){
	setHomeColHeight(); // FIRST TIME PAGE LOAD
	homePageIntro();
	}


$("body.front .view-homepage-features .views-field-title").mouseover(function(){
	$("body.front .intro-fade").hide();
	$("body.front .view-homepage-features .views-field-body, body.front .view-homepage-features .views-field-field-feature-image-fid").hide();
	$(this).parent(".views-row")
	.children(".views-field-body").show()
	.parent(".views-row")
	.children(".views-field-field-feature-image-fid").show()
	});
	

/**
 * 5D Course Trial User Registration
 **/
$("body.page-5d-course-trial-register #user-register fieldset legend:contains('Account information')").replaceWith('<legend>Set up a new account</legend>');
$('body.page-5d-course-trial-register #user-register input#edit-submit').before('<p class="submit-instructions"><b>After you click on "Create new account" button, please look for a confirmation email at the email address you provided to continue.</b></p>');


/**
 * 5D Course User Registration
 **/
$('body.page-5d-course-473-course_registration #user-register input#edit-submit').before('<p class="submit-instructions"><b>After you click on "Create new account" button, please look for a confirmation email at the email address you provided to continue.</b></p>');



});