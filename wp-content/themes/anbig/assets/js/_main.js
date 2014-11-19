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
	  
	  function testFire(){
		console.log('fire');  
	  }
	  
	  function setPagination(){
		  $('.next').text(function(){
			return $(this).text().replace("Next", "");  
		  });
		  
		  $('.prev').text(function(){
			return $(this).text().replace("Previous", "");  
		  });
	  }
	  
	  function setbbpressSearch(){
		$("#bbp_search").val('Search...');
		$("#bbp_search").focus(function(){
			var value = $.trim($(this).val());
			if(value === "Search..."){
				$(this).val('');
			}
		});
		$("#bbp_search").blur(function(){
			var value = $.trim($(this).val());
			if(value === ""){
				$(this).val('Search...');
			}
		});
	  }
	  
	  $("a.imgLink").click(function() {
		$.fancybox({
			'padding'       : 30,
			'maxwidth'		: '90%',
			'fitToView'		: true,
			'href'          : this.href,
			'autoResize'    : true,
			'autoSize'      : true,
			'showCloseButton': true,
			'autoScale'		: true,
			'type'        : 'iframe',
			'scrolling'   : 'no',
			'content'       : "<div class=\"forumlightboxVideoContainer\"><img src=\""+this.href+"\"/></div>",
			afterShow : function() {
				//console.log('fancybox: width'+$('.fancybox-inner').css('width')+' height:'+$('.fancybox-inner').css('height')+' image: width'+$('.forumlightboxVideoContainer img').css('width')+' height:'+$('.forumlightboxVideoContainer img').css('height'));
				if(parseInt($('.forumlightboxVideoContainer img').css('width')) > parseInt($('.fancybox-inner').css('width'))){
					$(".forumlightboxVideoContainer img").removeClass('fullImgHeight').addClass('fullImgWidth');
				}else if(parseInt($('.forumlightboxVideoContainer img').css('height')) > parseInt($('.fancybox-inner').css('height'))){
					$(".forumlightboxVideoContainer img").removeClass('fullImgWidth').addClass('fullImgHeight');
				}
			},
			onUpdate : function(){
				//console.log('resized fancybox: width'+$('.fancybox-inner').css('width')+' height:'+$('.fancybox-inner').css('height')+' image: width'+$('.forumlightboxVideoContainer img').css('width')+' height:'+$('.forumlightboxVideoContainer img').css('height'));
				if(parseInt($('.forumlightboxVideoContainer img').css('width')) > parseInt($('.fancybox-inner').css('width'))){
					$(".forumlightboxVideoContainer img").removeClass('fullImgHeight').addClass('fullImgWidth');
				}else if(parseInt($('.forumlightboxVideoContainer img').css('height')) > parseInt($('.fancybox-inner').css('height'))){
					$(".forumlightboxVideoContainer img").removeClass('fullImgWidth').addClass('fullImgHeight');
				}
			}
		  });
		  return false;
	  });
	  
	  $("a.videoLink").click(function() {
		$.fancybox({
			'padding'       : 30,
			'width'         : '50%',
			'height'        : '50%',
			'href'          : this.href,
			'autoResize'    : true,
			'autoSize'      : true,
			'showCloseButton': true,
			'autoScale'   : true,
			'type'        : 'iframe',
			'scrolling'   : 'no',
			/*'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:auto; height:auto;\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",*/
			'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"100%\" height=\"auto\" style=\"width:100%;\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
			afterShow : function() {	
					$('.lightboxContentContainer').each(
						function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						});
						console.log('afterShow');
						$('.lightboxVideoContainer video').attr('style',' ');
						if(parseInt($('.lightboxVideoContainer video').css('width')) > parseInt($('.fancybox-inner').css('width'))){
							$(".lightboxVideoContainer video").removeClass('fullImgHeight').addClass('fullImgWidth');
						}else if(parseInt($('.lightboxVideoContainer video').css('height')) > parseInt($('.fancybox-inner').css('height'))){
							$(".lightboxVideoContainer video").removeClass('fullImgWidth').addClass('fullImgHeight');
						}					
					},
           	  
			  onUpdate : function(){
					if(parseInt($('.lightboxVideoContainer video').css('width')) > parseInt($('.fancybox-inner').css('width'))){
						$(".lightboxVideoContainer video").removeClass('fullImgHeight').addClass('fullImgWidth');
					}else if(parseInt($('.lightboxVideoContainer video').css('height')) > parseInt($('.fancybox-inner').css('height'))){
						$(".lightboxVideoContainer video").removeClass('fullImgWidth').addClass('fullImgHeight');
					}
				}
			});
              return false;
		});
	  
	  	  
      //fix the mobile menu scrolling problem
      $(document).ready(setMobileMenu);
	  $(document).ready(setMediaMenu);
	  $(document).ready(setPagination());
	  $(document).ready(setbbpressSearch());
      $(window).resize(setMobileMenu);
	  //$(document).ready(testShow);
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

        /*$(".videoLink").click(function() {
            $.fancybox({
                'padding'       : 30,
                'width'         : '50%',
                'height'        : '50%',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'   : true,
				'type'        : 'iframe',
				'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:auto !important; height:100% !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>"
                });
              return false;
            });*/
      });
    }
  },
  page_template_template_video_listing_text_php:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
        /*$(".btnVideoDetail").click(function() {
			
            $.fancybox({
                'padding'       : 30,
                'width'         : '50%',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:auto !important; height:100% !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
				afterShow : function() {	
					$('.lightboxContentContainer').each(
						function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						});
					}
           	  });
              return false;
        });*/
		
		$("a.btnVideoDetail").click(function() {
		$.fancybox({
			'padding'       : 30,/*
			'width'         : 'auto',
			'height'        : 'auto',*/
			'maxwidth'		: '90%',
			'fitToView'		: true,
			'href'          : this.href,
			'autoResize'    : true,
			'autoSize'      : true,
			'showCloseButton': true,
			'autoScale'   : true,
			'type'        : 'iframe',
			'scrolling'   : 'no',
			'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"100%\" height=\"auto\" style=\"width:100%;\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
			afterShow : function() {	
					$('.lightboxContentContainer').each(
						function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						});
						$('.lightboxVideoContainer video').attr('style',' ');
						if(parseInt($('.lightboxVideoContainer video').css('width')) > parseInt($('.fancybox-inner').css('width'))){
							$(".lightboxVideoContainer video").removeClass('fullImgHeight').addClass('fullImgWidth');
						}else if(parseInt($('.lightboxVideoContainer video').css('height')) > parseInt($('.fancybox-inner').css('height'))){
							$(".lightboxVideoContainer video").removeClass('fullImgWidth').addClass('fullImgHeight');
						}					
					},
           	  
			  onUpdate : function(){
					if(parseInt($('.lightboxVideoContainer video').css('width')) > parseInt($('.fancybox-inner').css('width'))){
						$(".lightboxVideoContainer video").removeClass('fullImgHeight').addClass('fullImgWidth');
					}else if(parseInt($('.lightboxVideoContainer video').css('height')) > parseInt($('.fancybox-inner').css('height'))){
						$(".lightboxVideoContainer video").removeClass('fullImgWidth').addClass('fullImgHeight');
					}
				}
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
        /*$(".videoLink").click(function() {
            $.fancybox({
                'padding'       : 30,
                'width'         : '50%',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:auto !important; height:100% !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
				afterShow : function() {	
					$('.lightboxContentContainer').each(
						function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						});
					}
           	  });
              return false;
        });*/
			
      });
    }
  },
  search_results:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
		var nbiFlag = false;
		function setImageFilter(){
			$('.lightBoxImg').children('div').each(function(){
				//$(this).css('display','none');
				$('.originalImgContainer').css('visibility','visible');
				$('.btnOriginalImage').addClass("active");
			});
			
			if(!nbiFlag){
				$('.btnNBI').css({'display':'none'});
				return;
			}
			
			$('.btnOriginalImage').click(function(){
				$('.imageFilterContainer a').each(function(){
					$(this).removeClass("active");	
				});
				$('.lightBoxImg').children('div').each(function(){
					$(this).css('display','none');
					$(this).css('visibility','hidden');
					$('.originalImgContainer').css({'display':'block','position':'relative', 'visibility':'visible'});
					$('.btnOriginalImage').addClass("active");
				});
			});
			
			$('.btnNBI').click(function(){
				$('.imageFilterContainer a').each(function(){
					$(this).removeClass("active");	
				});
				$('.lightBoxImg').children('div').each(function(){
					$(this).css('display','none');
					$(this).css('visibility','hidden');
					$('.nbiImgContainer').css({'display':'block','position':'relative', 'visibility':'visible'});
					$('.btnNBI').addClass("active");
				});
			});
		}
		
		function imageResize(container1, container2){
			var globalImgContainerWidth = 0;	
			function setImgClass(container){
				if(heightForImg < parseInt($('#'+container+' img').height())){					
					var containerWidth = 0;
					
					if(globalImgContainerWidth === 0){
						containerWidth = Math.ceil(heightForImg*(parseInt($('#'+container+' img').css('width'))/parseInt($('#'+container+' img').css('height'))));
						globalImgContainerWidth = containerWidth;
					}else{
						containerWidth = globalImgContainerWidth;
					}
					
					$('.filterImg').each(function(){
						$(this).css('width',containerWidth);	
					});
				}else{
					if(parseInt($('#'+container+' img').css('width')) > parseInt($('.fancybox-inner').css('width'))){
						$('#'+container+' img').removeClass('fullImgHeight').addClass('fullImgWidth');
					}else if(parseInt($('.'+container+' img').css('height')) > parseInt($('.fancybox-inner').css('height'))){
						$('#'+container+' img').removeClass('fullImgWidth').addClass('fullImgHeight');
					}
					if(globalImgContainerWidth === 0){
						$('#'+container).css('width','auto');
					}else{
						$('#'+container).css('width',globalImgContainerWidth);
					}
				}
			}
			if(parseInt($('.fancybox-skin').height()) > $(window).height()*0.9 ){
				$('.fancybox-skin').css('height',$(window).height()*0.9);	
				$('.fancybox-inner').css('height',$(window).height()*0.9-60);	
			}
			var heightForImg = parseInt($('.fancybox-inner').height()) - parseInt($('.imageFilterContainer').css('height')) - parseInt($('.lightboxImgContentContainer').css('height'))-45;
			$('.lightBoxImg').css({"height":heightForImg+'px'});
			
			
			setImgClass(container1);
			setImgClass(container2);
			
			globalImgContainerWidth = 0;
		}
		
		function setNBIfilter(id,nImageSrc){
			if(!nbiFlag){return;}
			var native_width = 0;
			var native_height = 0;
			var nbiImageSrc = nImageSrc;
			$("#"+id+" .large").css({"background": "url('"+nbiImageSrc+"') no-repeat"});
		
			$("#"+id).mousemove(function(e){
				if(!native_width && !native_height)
				{
					var image_object = new Image();
					image_object.src = $("#"+id+" .small").attr("src");
					
					native_width = $("#"+id+" .small").width();
					native_height = $("#"+id+" .small").height();
					$(this).css({"width":native_width});
					//console.log(native_width+' '+native_height);
				}
				else
				{
					var magnify_offset = $(this).offset();
					var mx = e.pageX - magnify_offset.left;
					var my = e.pageY - magnify_offset.top;
					
					if(mx < $(this).width() && my < $(this).height() && mx > 0 && my > 0)
					{
						$("#"+id+" .large").fadeIn(100);
					}
					else
					{
						$("#"+id+" .large").fadeOut(100);
					}
					if($("#"+id+" .large").is(":visible"))
					{
						$("#"+id+" .large").css({"background-size":native_width+'px '+native_height+'px'});
						var rx = Math.round(mx/$("#"+id+" .small").width()*native_width - $("#"+id+" .large").width()/2)*-1;
						var ry = Math.round(my/$("#"+id+" .small").height()*native_height - $("#"+id+" .large").height()/2)*-1;
						var bgp = rx + "px " + ry + "px";
						
						var px = mx - $("#"+id+" .large").width()/2;
						var py = my - $("#"+id+" .large").height()/2;
		
						$("#"+id+" .large").css({left: px, top: py, backgroundPosition: bgp});
					}
				}
			});
		}		
		
        $(".imgArchievesLink").click(function() {
			if($(this).attr('nbi_image'))
			{
				console.log('no null');
				nbiFlag = true;
			}else{
				console.log('null');
				nbiFlag = false;
			}
            $.fancybox({
                'padding'       : 30,
                'width'         : '50%',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxImgContainer\"><div class=\"lightBoxImg\"><div id=\"original_image\" class=\"originalImgContainer filterImg\"><div class=\"large\"></div><img class=\"small fullImgWidth\" src=\""+this.href+"\" data-big=\""+$(this).attr("nbi_image")+"\"/></div><div id=\"nbi_image\" class=\"nbiImgContainer filterImg\"><div class=\"large\"></div><img class=\"small fullImgWidth\" src=\""+$(this).attr("nbi_image")+"\" data-big=\""+this.href+"\"/></div></div></div><div class=\"imageFilterContainer\"><a href=\"javascript:;\" class=\"btnOriginalImage\">Original image</a><a href=\"javascript:;\" class=\"btnNBI\">NBI image</a></div><div class=\"lightboxImgContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
				 afterShow : function() {
					 $.when(
					 	imageResize(),
					 	$('.lightboxImgContentContainer').each(function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						}),
						setNBIfilter('original_image', $("#original_image .small").attr("data-big")),
						setNBIfilter('nbi_image', $("#nbi_image .small").attr("data-big"))
						//imageResize('original_image', 'nbi_image')
					).then(setImageFilter());
				},
				onUpdate : function(){
					$('.lightBoxImg').css({"height":'auto'});
					$('#original_image').css({"width":'auto'});
					$('#nbi_image').css({"width":'auto'});
					imageResize('original_image', 'nbi_image');
					setNBIfilter('original_image', $("#original_image .small").attr("data-big"));
					setNBIfilter('nbi_image', $("#nbi_image .small").attr("data-big"));			
				}
                });
              return false;
            });
        });
		
		  
        /*$(".videoLink").click(function() {
            $.fancybox({
                'padding'       : 30,
                'width'         : '50%',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:auto !important; height:100% !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
				afterShow : function() {	
					$('.lightboxContentContainer').each(
						function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						});
					}
           	  });
              return false;
			
      });*/
    }
  },
  page_template_template_listing_img_php:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
		var nbiFlag = false;
		function setImageFilter(){
			$('.lightBoxImg').children('div').each(function(){
				//$(this).css('display','none');
				$('.originalImgContainer').css('visibility','visible');
				$('.btnOriginalImage').addClass("active");
			});
			
			if(!nbiFlag){
				$('.btnNBI').css({'display':'none'});
				return;
			}
			
			$('.btnOriginalImage').click(function(){
				$('.imageFilterContainer a').each(function(){
					$(this).removeClass("active");	
				});
				$('.lightBoxImg').children('div').each(function(){
					$(this).css('display','none');
					$(this).css('visibility','hidden');
					$('.originalImgContainer').css({'display':'block','position':'relative', 'visibility':'visible'});
					$('.btnOriginalImage').addClass("active");
				});
			});
			
			$('.btnNBI').click(function(){
				$('.imageFilterContainer a').each(function(){
					$(this).removeClass("active");	
				});
				$('.lightBoxImg').children('div').each(function(){
					$(this).css('display','none');
					$(this).css('visibility','hidden');
					$('.nbiImgContainer').css({'display':'block','position':'relative', 'visibility':'visible'});
					$('.btnNBI').addClass("active");
				});
			});
		}
		
		function imageResize(container1, container2){
			var globalImgContainerWidth = 0;	
			function setImgClass(container){
				if(heightForImg < parseInt($('#'+container+' img').height())){					
					var containerWidth = 0;
					
					if(globalImgContainerWidth === 0){
						containerWidth = Math.ceil(heightForImg*(parseInt($('#'+container+' img').css('width'))/parseInt($('#'+container+' img').css('height'))));
						globalImgContainerWidth = containerWidth;
					}else{
						containerWidth = globalImgContainerWidth;
					}
					
					$('.filterImg').each(function(){
						$(this).css('width',containerWidth);	
					});
				}else{
					if(parseInt($('#'+container+' img').css('width')) > parseInt($('.fancybox-inner').css('width'))){
						$('#'+container+' img').removeClass('fullImgHeight').addClass('fullImgWidth');
					}else if(parseInt($('.'+container+' img').css('height')) > parseInt($('.fancybox-inner').css('height'))){
						$('#'+container+' img').removeClass('fullImgWidth').addClass('fullImgHeight');
					}
					if(globalImgContainerWidth === 0){
						$('#'+container).css('width','auto');
					}else{
						$('#'+container).css('width',globalImgContainerWidth);
					}
				}
			}
			if(parseInt($('.fancybox-skin').height()) > $(window).height()*0.9 ){
				$('.fancybox-skin').css('height',$(window).height()*0.9);	
				$('.fancybox-inner').css('height',$(window).height()*0.9-60);	
			}
			var heightForImg = parseInt($('.fancybox-inner').height()) - parseInt($('.imageFilterContainer').css('height')) - parseInt($('.lightboxImgContentContainer').css('height'))-45;
			$('.lightBoxImg').css({"height":heightForImg+'px'});
			
			
			setImgClass(container1);
			setImgClass(container2);
			
			globalImgContainerWidth = 0;
		}
		
		function setNBIfilter(id,nImageSrc){
			if(!nbiFlag){return;}
			var native_width = 0;
			var native_height = 0;
			var nbiImageSrc = nImageSrc;
			$("#"+id+" .large").css({"background": "url('"+nbiImageSrc+"') no-repeat"});
		
			$("#"+id).mousemove(function(e){
				if(!native_width && !native_height)
				{
					var image_object = new Image();
					image_object.src = $("#"+id+" .small").attr("src");
					
					native_width = $("#"+id+" .small").width();
					native_height = $("#"+id+" .small").height();
					//$(this).css({"width":native_width});
					//console.log(native_width+' '+native_height);
				}
				else
				{
					var magnify_offset = $(this).offset();
					var mx = e.pageX - magnify_offset.left;
					var my = e.pageY - magnify_offset.top;
					
					if(mx < $(this).width() && my < $(this).height() && mx > 0 && my > 0)
					{
						$("#"+id+" .large").fadeIn(100);
					}
					else
					{
						$("#"+id+" .large").fadeOut(100);
					}
					if($("#"+id+" .large").is(":visible"))
					{
						$("#"+id+" .large").css({"background-size":native_width+'px '+native_height+'px'});
						var rx = Math.round(mx/$("#"+id+" .small").width()*native_width - $("#"+id+" .large").width()/2)*-1;
						var ry = Math.round(my/$("#"+id+" .small").height()*native_height - $("#"+id+" .large").height()/2)*-1;
						var bgp = rx + "px " + ry + "px";
						
						var px = mx - $("#"+id+" .large").width()/2;
						var py = my - $("#"+id+" .large").height()/2;
		
						$("#"+id+" .large").css({left: px, top: py, backgroundPosition: bgp});
					}
				}
			});
		}		
		
        $(".imgArchievesLink").click(function() {
			if($(this).attr('nbi_image'))
			{
				//console.log('no null');
				nbiFlag = true;
			}else{
				//console.log('null');
				nbiFlag = false;
			}
            $.fancybox({
                'padding'       : 30,
                'width'         : '50%',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxImgContainer\"><div class=\"lightBoxImg\"><div id=\"original_image\" class=\"originalImgContainer filterImg\"><div class=\"large\"></div><img class=\"small fullImgWidth\" src=\""+this.href+"\" data-big=\""+$(this).attr("nbi_image")+"\"/></div><div id=\"nbi_image\" class=\"nbiImgContainer filterImg\"><div class=\"large\"></div><img class=\"small fullImgWidth\" src=\""+$(this).attr("nbi_image")+"\" data-big=\""+this.href+"\"/></div></div></div><div class=\"imageFilterContainer\"><a href=\"javascript:;\" class=\"btnOriginalImage\">Original image</a><a href=\"javascript:;\" class=\"btnNBI\">NBI image</a></div><div class=\"lightboxImgContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
				 afterShow : function() {
					 $.when(
					 	$('.lightboxImgContentContainer').each(function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						}),
						
						setNBIfilter('original_image', $("#original_image .small").attr("data-big")),
						setNBIfilter('nbi_image', $("#nbi_image .small").attr("data-big"))
						//imageResize('original_image', 'nbi_image')
					).then(setImageFilter());
				},
				onUpdate : function(){
					console.log('update');
					$('.lightBoxImg').css({"height":'auto'});
					$('#original_image').css({"width":'auto'});
					$('#nbi_image').css({"width":'auto'});
					imageResize('original_image', 'nbi_image');
					setNBIfilter('original_image', $('#original_image .small').attr('data-big'));
					setNBIfilter('nbi_image', $('#nbi_image .small').attr('data-big'));
				}
                });
              return false;
            });
        });
    }
  },
  // About us page, note the change from about-us to about_us.
  single_topic:{
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
        $("a.bbp-atticon-video").click(function() {
            $.fancybox({
                'padding'       : 30,
                'width'         : 'auto',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"forumlightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:auto !important; height:100% !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div>"
           	  });
              return false;
        });
		
		$("a.bbp-atthumb").click(function() {
            $.fancybox({
                'padding'       : 30,
				'maxwidth'		: '90%',
				'fitToView'		: true,
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"forumlightboxVideoContainer\"><img src=\""+this.href+"\"/></div>",
				afterShow : function() {
					if(parseInt($('.forumlightboxVideoContainer img').css('width')) > parseInt($('.fancybox-inner').css('width'))){
						$(".forumlightboxVideoContainer img").removeClass('fullImgHeight').addClass('fullImgWidth');
					}else if(parseInt($('.forumlightboxVideoContainer img').css('height')) > parseInt($('.fancybox-inner').css('height'))){
						$(".forumlightboxVideoContainer img").removeClass('fullImgWidth').addClass('fullImgHeight');
					}
				},
				onUpdate : function(){
					if(parseInt($('.forumlightboxVideoContainer img').css('width')) > parseInt($('.fancybox-inner').css('width'))){
						$(".forumlightboxVideoContainer img").removeClass('fullImgHeight').addClass('fullImgWidth');
					}else if(parseInt($('.forumlightboxVideoContainer img').css('height')) > parseInt($('.fancybox-inner').css('height'))){
						$(".forumlightboxVideoContainer img").removeClass('fullImgWidth').addClass('fullImgHeight');
					}
				}
           	  });
              return false;
        });
		
      });
    }
  },  
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
