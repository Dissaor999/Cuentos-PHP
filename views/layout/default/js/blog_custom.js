/* JS Document */

jQuery(document).ready(function($)
{
	"use strict";

	var header = $('.header');
	var hamb = $('.hamburger_container_outer');
	var hambActive = false;
	var ctrl = new ScrollMagic.Controller();

	setHeader();
	initHamburger();
	initParallax();
	initHero();

	$(window).on('resize', function()
	{
		setHeader();
	});

	$(document).on('scroll', function()
	{
		setHeader();
	});

	initMasonry();

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

	function initMasonry()
	{
		$('.grid').masonry(
		{
			// options
			itemSelector: '.grid-item'
		});
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
		var homeBcg = $('.blog_background');

		var homeBcgScene = new ScrollMagic.Scene({
	        triggerElement: homeBcg,
	        triggerHook: 1,
	        duration: "100%"
	    })
	    .setTween(TweenMax.to(homeBcg, 1, {y: '40%', ease:Power0.easeNone}))
	    .addTo(ctrl);
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
});