<header id="banner" role="banner">
  <div class="container">
    <div class="row logo-row">
      <div class="span12">
        <h1 id="site-name">
          <a href="<?php print $front_page; ?>">
            <span>
              <?php print $site_name; ?>
              <small><?php print $site_slogan; ?></small>
            </span>
          </a>
        </h1>
      </div>
    </div>

    <div class="row nav-row">
      <div id="navbar-wrapper" class="span12">
        <div id="navbar" role="banner" class="navbar">
          <div class="navbar-inner">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </a>

            <?php if (!empty($page['navigation'])): ?>
              <div class="nav-collapse">
                <nav role="navigation">
                  <?php print render($page['navigation']); ?>
                </nav>
              </div>
            <?php endif; ?>

            <?php if (empty($page['navigation'])): ?>
              <div class="nav-collapse">
                <nav role="navigation">
                <?php if (!empty($primary_nav)): ?>
                  <?php print render($primary_nav); ?>
                <?php endif; ?>
                <?php if (!empty($secondary_nav)): ?>
                  <?php print render($secondary_nav); ?>
                <?php endif; ?>
                </nav>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="container" id="main-container">
  <header role="banner" id="page-header">
    <?php print render($page['header']); ?>
  </header> <!-- /#header -->

  <div class="row">
    <section class="span12">  
      <a id="main-content"></a>
      <?php if($wl_show_title): ?>
        <?php print render($title_prefix); ?>
        <?php if ($title): ?>
          <h1 class="page-header">
            <?php print $title; ?><?php if($wl_add_colon_to_title): print ":"; endif; ?>
            <?php print $wl_subtitle ?>
          </h1>
        <?php endif; ?>
        <?php print render($title_suffix); ?>
      <?php endif; ?>
      <?php print $messages; ?>
      <?php if ($tabs): ?>
        <?php print render($tabs); ?>
      <?php endif; ?>
      <?php if ($page['help']): ?> 
        <div class="well"><?php print render($page['help']); ?></div>
      <?php endif; ?>
      <?php if ($action_links): ?>
        <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>
      <?php print render($page['content']); ?>
    </section>
  </div>
</div>

<footer id="footer">
  <div class="shadow"></div>
  <div class="container">
    <div class="row">
      <div class="span12">
        <?php print render($page['footer']); ?>
      </div>
    </div>
  </div>
</footer>

<?php print render($page['page-end']); ?>

<div id="modal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  </div>
  <div class="modal-body">
    <div class="loader"></div>
  </div>
</div>

<script>
//Common JS
/**
 * @var bool window_loaded Changed to true when the window has finished
 */
var window_loaded = false;

jQuery(window).load(function($) {
  window_loaded = true;
});

jQuery(document).ready(function($) {
  $('body').on('hidden', '.modal', function () {
    //Clear out our modals
    $(this).removeData('modal');
    $(this).find('.modal-body').empty().append('<div class="loader"></div>');
  });

  $('body').on('shown', '.modal', function () {
    var modal = this;

    //Spinny!
    new Spinner({
      lines: 11,
      length: 3,
      width: 2,
      radius: 5,
      corners: 1,
      color: '#fff',
      trail: 60
    }).spin($(this).find('.modal-body .loader').get(0));

    var modal_interval = window.setInterval(function() {
      if($(modal).find('.modal-body .loader').length) {
        return;
      }

      //Dynamically resize youtube iframes
      $(modal).find('.modal-body .media-youtube-video').fitVids();
      //Set the state-change settings for youtube videos
      $(modal).find('.modal-body .media-youtube-video iframe').each(function() {
        init_youtube_player.call(this);
      });

      //Carousel any carousels
      $(modal).find('.modal-body .carousel').carousel();

      window.clearInterval(modal_interval);
    }, 500);
  });

  //Dynamically resize youtube iframes
  $('.media-youtube-video').fitVids();

  //Carousel any carousels
  $('.carousel').carousel();
});

/**
 * Init player API objects for each youtube video
 */
function onYouTubeIframeAPIReady() {
  if(!window_loaded) {
    window.setTimeout(onYouTubeIframeAPIReady, 500);
    return;
  }

  jQuery('.media-youtube-video iframe').each(function() {
    init_youtube_player.call(this);
  });
}

/**
 * Inits the YouTube Player API for a specific iframe
 *
 * Used to pause/cycle carousels in which the YouTube is embedded
 */
function init_youtube_player() {
  var player = new YT.Player(this, {
    events: {
      onStateChange: function(event) {
        var iframe = event.target.getIframe() || false;

        switch(event.data) {
          case YT.PlayerState.UNSTARTED:
          case YT.PlayerState.ENDED:
            toggle_carousel(iframe, 'finished');
            break;

          case YT.PlayerState.PAUSED:
            toggle_carousel(iframe, 'paused');
            break;

          default:
            toggle_carousel(iframe, 'playing');
        }
      }
    }
  });
}

/**
 * Toggle a carousel
 *
 * Used to pause/cycle carousels in which a YouTube is embedded
 *
 * @var object iframe The YouTube iframe
 * @var string state The state of the YouTube player
 */
function toggle_carousel(iframe, state) {
  iframe = jQuery(iframe);

  if(!iframe.length) {
    return;
  }

  var carousel = iframe.closest('.carousel'),
      pane = iframe.closest('.item');

  //This video isn't in a carousel, so stop
  if(!carousel.length || !pane.length) {
    return;
  }

  var pane_index = carousel.find('.item').index(pane);

  switch(state) {
    case 'playing':
      carousel.carousel(pane_index);
      carousel.carousel('pause');
      break;

    case 'paused':
    case 'finished':
    default:
      carousel.carousel('cycle');
  }
}
</script>