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
	  
	  function setMediaMenu(){
		if($('.mobileMediaNav').length > 0){
			$('.mobileMediaToggle').click(function(){
				$('.mobileMediaNav').toggleClass('open');			
			});
		}
		if($('.mobileMediaSubNav').length > 0){
			$('.mobileMediaSubToggle').click(function(){
				$('.mobileMediaSubNav').toggleClass('open');			
			});
		}
	  }
      //fix the mobile menu scrolling problem
      $(document).ready(setMobileMenu);
	  $(document).ready(setMediaMenu);
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
  page_template_template_video_listing_text_php:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
        $(".btnVideoDetail").click(function() {
			
            $.fancybox({
                'padding'       : 0,
                'width'         : '50%',
                'height'        : '70%',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:100% !important; height:auto !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>"
                });
              return false;
            });
        });
    }
  },
  page_template_template_video_listing_image_php:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
        $(".videoLink").click(function() {
            $.fancybox({
                'padding'       : 0,
                'width'         : '50%',
                'height'        : '70%',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:100% !important; height:auto !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>"
                });
              return false;
            });
        });
		$(".lightboxContentContainer").jScrollPane({
		});
    }
  },
  page_template_template_listing_img_php:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
		  
		function setImageFilter(){
			$('.lightBoxImg').children('div').each(function(){
				$(this).css('display','none');
				$('.originalImgContainer').css('display','block');
				$('.btnOriginalImage').addClass("active");
			});
			
			$('.btnOriginalImage').click(function(){
				$('.imageFilterContainer a').each(function(){
					$(this).removeClass("active");	
				});
				$('.lightBoxImg').children('div').each(function(){
					$(this).css('display','none');
					$('.originalImgContainer').css('display','block');
					$('.btnOriginalImage').addClass("active");
				});
			});
			
			$('.btnNBI').click(function(){
				$('.imageFilterContainer a').each(function(){
					$(this).removeClass("active");	
				});
				$('.lightBoxImg').children('div').each(function(){
					$(this).css('display','none');
					$('.nbiImgContainer').css('display','block');
					$('.btnNBI').addClass("active");
				});
			});
		}
		  
        $(".imgLink").click(function() {
            $.fancybox({
                'padding'       : 0,
                'width'         : '50%',
                'height'        : '70%',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
                'content'       : "<div class=\"lightboxVideoContainer\"><div class=\"lightBoxImg\"><div class=\"originalImgContainer\"><img id=\"orginal_image\" class=\"fullImgWidth\" src=\""+this.href+"\" data-big=\""+$(this).attr("nbi_image")+"\" data-big2x=\""+$(this).attr("nbi_image")+"\" data-overlay="+$(this).attr("nbi_image")+"\" /></div><div class=\"nbiImgContainer\"><img id=\"nbi_image\" class=\"fullImgWidth\" src=\""+$(this).attr("nbi_image")+"\" data-big=\""+this.href+"\" data-big2x=\""+this.href+"\" data-overlay="+this.href+"\" /></div></div><div class=\"imageFilterContainer\"><a href=\"javascript:;\" class=\"btnOriginalImage\">Original image</a><a href=\"javascript:;\" class=\"btnNBI\">NBI image</a></div><div class=\"lightboxImgContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div></div>",
				 afterShow : function() {
					 $.when(
					 	$("#orginal_image").mlens({
							imgSrc: $("#orginal_image").attr("data-big"),	    // path of the hi-res version of the image
							imgSrc2x: $("#orginal_image").attr("data-big2x"),  // path of the hi-res @2x version of the image
																					   //for retina displays (optional)
							lensShape: "circle",				// shape of the lens (circle/square)
							lensSize: 180,					// size of the lens (in px)
							borderSize: 1,					// size of the lens border (in px)
							borderColor: "#fff",				// color of the lens border (#hex)
							borderRadius: 0,				// border radius (optional, only if the shape is square)
							imgOverlay: $("#orginal_image").attr("data-overlay"), // path of the overlay image (optional)
							overlayAdapt: true, // true if the overlay image has to adapt to the lens size (true/false)
							zoomLevel: 0                                  // zoom level multiplicator (number)
						}),
						$("#nbi_image").mlens({
							imgSrc: $("#nbi_image").attr("data-big"),	    // path of the hi-res version of the image
							imgSrc2x: $("#nbi_image").attr("data-big2x"),  // path of the hi-res @2x version of the image
																					   //for retina displays (optional)
							lensShape: "circle",				// shape of the lens (circle/square)
							lensSize: 180,					// size of the lens (in px)
							borderSize: 1,					// size of the lens border (in px)
							borderColor: "#fff",				// color of the lens border (#hex)
							borderRadius: 0,				// border radius (optional, only if the shape is square)
							imgOverlay: $("#nbi_image").attr("data-overlay"), // path of the overlay image (optional)
							overlayAdapt: true, // true if the overlay image has to adapt to the lens size (true/false)
							zoomLevel: 0                                  // zoom level multiplicator (number)
						})
					).then(setImageFilter());
				}
                });
              return false;
            });
        });
		$(".lightboxContentContainer").jScrollPane();
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
