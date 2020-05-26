/* JS Document */

jQuery(document).ready(function($)
{
	"use strict";

	var header = $('.header');
	var hamb = $('.hamburger_container_outer');
	var hambActive = false;
	var ctrl = new ScrollMagic.Controller();

	setHeader();
	
	$(window).on('resize', function()
	{
		setHeader();
		initParallax();
	});

	$(document).on('scroll', function()
	{
		setHeader();
	});

	initHamburger();
	initParallax();
	initHero();
	initProgressBars();
	initTeam();
	initStats();
	initTestimonials();
	initReel();

	function setHeader()
	{
		if(window.innerWidth < 768)
		{
			if($(window).scrollTop() > 100)
			{
				header.css({'height':"60px", "marginTop":"0px", "background":"rgba(255, 255, 255, 0.9)"});
				hamb.css({'height':"60px", "marginTop":"0px"});
			}
			else
			{
				header.css({'height':"60px", "marginTop":"0px", "background":"transparent"});
				hamb.css({'height':"60px", "marginTop":"0px"});
			}
		}
		else
		{
			if($(window).scrollTop() > 100)
			{
				header.css({'height':"80px", "marginTop":"0px", "background":"rgba(255, 255, 255, 0.9)"});
				hamb.css({'height':"80px", "marginTop":"0px"});
			}
			else
			{
				header.css({'height':"100px", "background":"transparent"});
				hamb.css("height", "100px");
			}
		}

		if(window.innerWidth > 991 && hambActive)
		{
			fsMenuClose();
		}
	}

	function initHamburger()
	{
		var hamburger = $('.hamburger');

		hamburger.on('click', function()
		{
	  		if(hamburger.hasClass('active'))
	  		{
	  			/* animate menu out of view */
	  			fsMenuClose();
	  		}
	  		else
	  		{
	  			/* animate menu into view */
	  			fsMenuOpen();
	  		}
		});	
	}

	function fsMenuOpen()
	{
		var hamburger = $('.hamburger');
		var fsMenu = $('.fs_menu_overlay');
		var fsMenuLeft = $('.fs_menu_left');
		var fsMenuItems = $('.fs_nav ul li');
		var fsSocial = $('.fs_menu_social');
		var div1 = $('.hamburger div:first-child');
		var div2 = $('.hamburger div:last-child');

		hamburger.addClass('active');
		fsMenu.addClass('active');
		var hamburgerRotateIn = TweenMax.to($('.hamburger_container'), 1, {rotation:360, x:-22, ease:Power2.easeOut});

		if(window.innerWidth < 480)
		{
			var fsMenuTween1 = TweenMax.staggerTo(fsMenuItems, 0.5, {x:-125, autoAlpha:1, ease:Power2.easeOut, delay:0.5}, 0.05);
		}	
		else if(window.innerWidth < 768)
		{
			var fsMenuTween2 = TweenMax.staggerTo(fsMenuItems, 0.5, {x:-150, autoAlpha:1, ease:Power2.easeOut, delay:0.5}, 0.05);
		}
		else
		{
			var fsMenuTween3 = TweenMax.staggerTo(fsMenuItems, 0.5, {x:-150, autoAlpha:1, ease:Power2.easeOut, delay:0.5}, 0.05);
		}
		
		fsMenuLeft.addClass('active');
		fsMenu.addClass('active');
		var fsSocIn = TweenMax.to(fsSocial, 0.5, {autoAlpha:1, ease:Power2.easeOut, delay:0.7});
		hambActive = true;
	}

	function fsMenuClose()
	{
		var hamburger = $('.hamburger');
		var fsMenu = $('.fs_menu_overlay');
		var fsMenuLeft = $('.fs_menu_left');
		var fsMenuItems = $('.fs_nav ul li');
		var fsSocial = $('.fs_menu_social');
		var div1 = $('.hamburger div:first-child');
		var div2 = $('.hamburger div:last-child');

		hamburger.removeClass('active');
		var hamburgerRotateOut = TweenMax.to($('.hamburger_container'), 1, {rotation:0, x:0, ease:Power2.easeOut});

		setTimeout(function()
		{
			fsMenuLeft.removeClass('active');
		}, 200);

		setTimeout(function()
		{
			fsMenu.removeClass('active');
		}, 300);
		
		var fsMenuTween4 = TweenMax.staggerTo(fsMenuItems, 0.8, {x:150, autoAlpha:0, ease:Power4.easeOut}, 0.06);
		var fsSocOut = TweenMax.to(fsSocial, 0.5, {autoAlpha:0, ease:Power2.easeOut, delay:0.2});
		hambActive = false;
	}

	function initParallax()
	{
		/* Adding parallax effect to the slider container */
		var homeBcg = $('.about_background');
		var scene2 = document.getElementById('scene_2');

		var homeBcgScene = new ScrollMagic.Scene({
	        triggerElement: homeBcg,
	        triggerHook: 1,
	        duration: "100%"
	    })
	    .setTween(TweenMax.to(homeBcg, 1, {y: '40%', ease:Power0.easeNone}))
	    .addTo(ctrl);

	    var parallax2 = new Parallax(scene2, {
		  relativeInput: true,
		  clipRelativeInput: false,
		  hoverOnly: true,
		  calibrateX: false,
		  calibrateY: true,
		  invertX: false,
		  invertY: true,
		  limitX: false,
		  limitY: false,
		  scalarX: 5,
		  scalarY: 8,
		  frictionX: 0.2,
		  frictionY: 0.8,
		  originX: 0.0,
		  originY: 0.0,
		  precision: 1,
		  pointerEvents: false,
		  onReady: function() { alert('ready!'); }
		});

		$('.call_to_action_1_background').parallax({imageSrc: 'images/swift_background_2.jpg'});
		$('.contact_background').parallax({imageSrc: 'images/contact_background.jpg'});
	}

	function initHero()
	{
		var heroOverlay = $('.slider_overlay');
		var heroContent = $('.home_content');

		/* move hero overlay on scroll */
		var heroOverlayScene = new ScrollMagic.Scene({
			triggerElement: heroOverlay,
			triggerHook: 1,
			duration: "100%"
		})
		.setTween(TweenMax.to(heroOverlay, 1, {x: '-7.5%', ease:Power0.easeNone}))
		.addTo(ctrl);

		/* move hero content on scroll */
		var heroContentScene = new ScrollMagic.Scene({
			triggerElement: heroContent,
			triggerHook: 1,
			duration: "100%"
		})
		.setTween(TweenMax.to(heroContent, 1, {x: '-50%', y: '-100%', opacity:'0', ease:Power0.easeNone}))
		.addTo(ctrl);
	}

	function initProgressBars()
	{
		var eles = $('.skill_bars');

		eles.each(function(i)
		{
			var ele = $(this);
    		var elePerc = ele.data('perc');
    		var eleName = '#'+ele.data('name');

    		var statsScene = new ScrollMagic.Scene({
	    		triggerElement: this,
	    		triggerHook: 'onEnter',
	    		reverse:false
	    	})
	    	.on('start', function()
	    	{
	    		var pbar = new ProgressBar.Line(eleName, 
	    		{
	    			strokeWidth: 4,
					easing: 'easeInOut',
					duration: 1400,
					color: '#00bcd5',
					trailColor: '#eee',
					trailWidth: 1,
					svgStyle: {width: '100%', height: '100%'},
					text: {
						style: {
							// Text color.
							// Default: same as stroke color (options.color)
							color: '#717a85',
							position: 'absolute',
							right: '0',
							top: '-20px',
							padding: 0,
							margin: 0,
							transform: null
							},
							autoStyleContainer: false
					},
					from: {color: '#00bcd5'},
					to: {color: '#00bcd5'},
					step: function(state, bar) {
					bar.setText(Math.round(bar.value() * 100) + ' %');
					}
	    		});
	    		pbar.animate(elePerc);
	    	})
	    	.addTo(ctrl);
		});
	}

	function removeActive(eles, panels)
	{
		eles.each(function()
		{
			$(this).removeClass('active');
		});
		panels.each(function()
		{
			$(this).removeClass('active');
		});
	}

	function setPanelHeight()
	{
		var panels = $('.process_panel');
		var panel;

		panels.each(function()
		{
			if($(this).hasClass('active'))
			{
				panel = $(this);
			}
		});

		$('.process_panels').css('height', panel.innerHeight());
	}

	function initTeam()
    {
    	/* team hover effect */
    	var teamRight = $('.team_right');
    	var teamLeft = $('.team_left');

    	teamRight.each(function(i)
    	{
    		var eleR = $(this);
    		var teamImgR = eleR.find('.team_image');
    		var teamInfoR = eleR.find('.team_info');
	    	var teamSocR = eleR.find('.team_social');

	    	teamImgR.on('mouseenter', function()
	    	{
	    		TweenMax.set(teamSocR, 0, {autoAlpha:0});
	    		var teamInfoRTweenOut = TweenMax.to(teamInfoR, 0.5, {autoAlpha:0, x:-50, ease: Power2.easeOut});
	    		var teamSocRTweenOut = TweenMax.to(teamSocR, 0.5, {autoAlpha:1, ease: Power2.easeOut});
	    	});

	    	teamImgR.on('mouseleave', function()
	    	{
	    		var teamInfoRTweenIn = TweenMax.to(teamInfoR, 0.5, {autoAlpha:1, x:0, ease: Power2.easeOut});
	    		var teamSocRTweenIn = TweenMax.to(teamSocR, 0.5, {autoAlpha:0, opacity:0, ease: Power2.easeOut});
	    	});
    	});

    	teamLeft.each(function(i)
    	{
    		var eleL = $(this);
    		var teamImgL = eleL.find('.team_image');
    		var teamInfoL = eleL.find('.team_info');
	    	var teamSocL = eleL.find('.team_social');

	    	teamImgL.on('mouseenter', function()
	    	{
	    		TweenMax.set(teamSocL, 0, {autoAlpha:0});
	    		var teamInfoLTweenOut = TweenMax.to(teamInfoL, 0.5, {autoAlpha:0, x:50, ease: Power2.easeOut});
	    		var teamSocLTweenOut = TweenMax.to(teamSocL, 0.5, {autoAlpha:1, ease: Power2.easeOut});
	    	});

	    	teamImgL.on('mouseleave', function()
	    	{
	    		var teamInfoLTweenIn = TweenMax.to(teamInfoL, 0.5, {autoAlpha:1, x:0, ease: Power2.easeOut});
	    		var teamSocLTweenIn = TweenMax.to(teamSocL, 0.5, {autoAlpha:0, opacity:0, ease: Power2.easeOut});
	    	});
    	});

    	/* team background parallax */
    	$('.team_background').parallax({imageSrc: 'images/team_background.png'});
    }

    function initStats()
    {
    	var ctrl = new ScrollMagic.Controller();
    	var statsItems = $('.stats_counter');

    	statsItems.each(function(i)
    	{
    		var ele = $(this);
    		var endValue = ele.data('end-value');
    		var eleValue = ele.text();

    		var statsScene = new ScrollMagic.Scene({
	    		triggerElement: this,
	    		triggerHook: 'onEnter',
	    		reverse:false
	    	})
	    	.on('start', function()
	    	{
	    		var counter = {value:eleValue};
	    		var counterTween = TweenMax.to(counter, 4,
	    		{
	    			value: endValue,
	    			roundProps:"value", 
					ease: Circ.easeOut, 
					onUpdate:function()
					{
						document.getElementsByClassName('stats_counter')[i].innerHTML = counter.value;
					}
	    		});
	    	})
		    .addTo(ctrl);
    	});
    }

	function initTestimonials()
    {
    	var owl_2 = $('.testimonials_slider');

		owl_2.owlCarousel(
		{
			items: 1,
			loop:true,
			mouseDrag:false,
			center: true,
			animateIn: 'fadeIn',
			animateOut: 'fadeOut',
			navSpeed: 500,
			smartSpeed: 500,
			autoplaySpeed: 500,
			dots:true,
			dotsData:true
		});
    }

    function initReel()
    {
    	$('.video').magnificPopup({
          disableOn: 700,
          type: 'iframe',
          mainClass: 'mfp-fade',
          removalDelay: 160,
          preloader: false,

          fixedContentPos: false
        });
    }
});