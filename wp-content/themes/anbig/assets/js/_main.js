/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can 
 * always reference jQuery with $, even when in .noConflict() mode.
 *
 * Google CDN, Latest jQuery
 * To use the default WordPress version of jQuery, go to lib/config.php and
 * remove or comment out: add_theme_support('jquery-cdn');
 * ======================================================================== */

(function($) {

// Use this variable to set up the common and page specific functions. If you 
// rename this variable, you will also need to rename the namespace below.
var Roots = {
  // All pages
  common: {
    init: function() {
      // JavaScript to be fired on all pages
      function setMobileMenu(){
        //console.log('setMobileMenu');
        if(window.innerWidth<768){
          $(".nav-container").css({overflow:"auto", maxHeight: $(window).height() - $(".navbar-header").height() + "px" });
        }
        else{
          $(".nav-container").css({overflow:"inherit"});
        }
      }
      //fix the mobile menu scrolling problem
      $(document).ready(setMobileMenu);
      $(window).resize(setMobileMenu);
    }
  },
  // Home page
  home: {
    init: function() {
      // JavaScript to be fired on the home page
      $(document).ready(function(){
        //resizeBanner();

        $('#main-banner-container').slippry({
          // general elements & wrapper
          slippryWrapper: '<div class="sy-box news-slider" />', // wrapper to wrap everything, including pager
          elements: '.main-banner', // elments cointaining slide content
          // options
          captions: false,
          // transitions
          transition: 'horizontal', // fade, horizontal, kenburns, false
          speed: 1200,
          pause: 5000,
          pager: false,
          adaptiveHeight: false
        });
      });
    }
  },
  page_template_template_listing_text_php:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
        /*$(".video").click(function() {
            $.fancybox({
                'padding'       : 0,
                'width'         : 'auto',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
                'content'       : "<video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" controls preload=\"none\" width=\"640\" height=\"264\" data-setup='{'autoplay': true}'><source src=\""+this.href+"\" type='video/mp4' /><track kind=\"captions\" src=\"demo.captions.vtt\" srclang=\"en\" label=\"English\"></track><!-- Tracks need an ending tag thanks to IE9 --><track kind=\"subtitles\" src=\"demo.captions.vtt\" srclang=\"en\" label=\"English\"></track><!-- Tracks need an ending tag thanks to IE9 --></video>"
                });
              return false;
            });*/
        });
    }
  },
  // About us page, note the change from about-us to about_us.
  about_us: {
    init: function() {
      // JavaScript to be fired on the about us page
    }
  }
};

// The routing fires all common scripts, followed by the page specific scripts.
// Add additional events for more control over timing e.g. a finalize event
var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = Roots;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {
    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
  }
};

$(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
