/* JS Document */

jQuery(document).ready(function($)
{
	"use strict";

	var header = $('.header');
	var hamb = $('.hamburger_container_outer');
	var hambActive = false;
	var ctrl = new ScrollMagic.Controller();
	var map;

	setHeader();
	
	$(window).on('resize', function()
	{
		setHeader();
	});

	$(document).on('scroll', function()
	{
		setHeader();
	});

	initHamburger();
	initParallax();
	initHero();
	initContact();
	initGoogleMap();
	initContactInfo();

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
		var homeBcg = $('.contact_background');
		var scene2 = document.getElementById('scene_2');

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

	function initContact()
    {
    	var name = $('#form_name');
    	var email = $('#form_email');
    	var message = $('#form_message');
    	var nameFocus = $('.name_focus');
    	var emailFocus = $('.email_focus');

    	/* hide placeholder text on focus and show it again on lost focus */
    	name.on('focus', function()
		{
			name.attr("placeholder", "");
			nameFocus.css({'visibility':"visible", 'opacity':"1"});
		});

		name.on('blur', function()
		{
			name.attr("placeholder", "Name");
			nameFocus.css({'visibility':"hidden", 'opacity':"0"});
		});

		email.on('focus', function()
		{
			email.attr("placeholder", "");
			emailFocus.css({'visibility':"visible", 'opacity':"1"});
		});

		email.on('blur', function()
		{
			email.attr("placeholder", "Email");
			emailFocus.css({'visibility':"hidden", 'opacity':"0"});
		});

		message.on('focus', function()
		{
			message.attr("placeholder", "");
		});

		message.on('blur', function()
		{
			message.attr("placeholder", "Your Message");
		});
    }

    function initGoogleMap()
    {
    	var myLatlng = new google.maps.LatLng(-33.866923, 151.24951);
    	var mapOptions = 
    	{
    		center: myLatlng,
	       	zoom: 15,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			draggable: true,
			scrollwheel: false,
			zoomControl: true,
			zoomControlOptions:
			{
				position: google.maps.ControlPosition.RIGHT_CENTER
			},
			mapTypeControl: false,
			scaleControl: false,
			streetViewControl: false,
			rotateControl: false,
			fullscreenControl: true,
			styles:
			[
			  {
			    "elementType": "geometry",
			    "stylers": [
			      {
			        "color": "#f5f5f5"
			      }
			    ]
			  },
			  {
			    "elementType": "labels.icon",
			    "stylers": [
			      {
			        "visibility": "off"
			      }
			    ]
			  },
			  {
			    "elementType": "labels.text.fill",
			    "stylers": [
			      {
			        "color": "#616161"
			      }
			    ]
			  },
			  {
			    "elementType": "labels.text.stroke",
			    "stylers": [
			      {
			        "color": "#f5f5f5"
			      }
			    ]
			  },
			  {
			    "featureType": "administrative.land_parcel",
			    "elementType": "labels.text.fill",
			    "stylers": [
			      {
			        "color": "#bdbdbd"
			      }
			    ]
			  },
			  {
			    "featureType": "poi",
			    "elementType": "geometry",
			    "stylers": [
			      {
			        "color": "#eeeeee"
			      }
			    ]
			  },
			  {
			    "featureType": "poi",
			    "elementType": "labels.text.fill",
			    "stylers": [
			      {
			        "color": "#757575"
			      }
			    ]
			  },
			  {
			    "featureType": "poi.park",
			    "elementType": "geometry",
			    "stylers": [
			      {
			        "color": "#e5e5e5"
			      }
			    ]
			  },
			  {
			    "featureType": "poi.park",
			    "elementType": "labels.text.fill",
			    "stylers": [
			      {
			        "color": "#9e9e9e"
			      }
			    ]
			  },
			  {
			    "featureType": "road",
			    "elementType": "geometry",
			    "stylers": [
			      {
			        "color": "#e2e2e2"
			      }
			    ]
			  },
			  {
			    "featureType": "road.arterial",
			    "elementType": "labels.text.fill",
			    "stylers": [
			      {
			        "color": "#757575"
			      }
			    ]
			  },
			  {
			    "featureType": "road.highway",
			    "elementType": "geometry",
			    "stylers": [
			      {
			        "color": "#dadada"
			      }
			    ]
			  },
			  {
			    "featureType": "road.highway",
			    "elementType": "labels.text.fill",
			    "stylers": [
			      {
			        "color": "#616161"
			      }
			    ]
			  },
			  {
			    "featureType": "road.local",
			    "elementType": "labels.text.fill",
			    "stylers": [
			      {
			        "color": "#9e9e9e"
			      }
			    ]
			  },
			  {
			    "featureType": "transit.line",
			    "elementType": "geometry",
			    "stylers": [
			      {
			        "color": "#e5e5e5"
			      }
			    ]
			  },
			  {
			    "featureType": "transit.station",
			    "elementType": "geometry",
			    "stylers": [
			      {
			        "color": "#eeeeee"
			      }
			    ]
			  },
			  {
			    "featureType": "water",
			    "elementType": "geometry",
			    "stylers": [
			      {
			        "color": "#c9c9c9"
			      }
			    ]
			  },
			  {
			    "featureType": "water",
			    "elementType": "geometry.fill",
			    "stylers": [
			      {
			        "color": "#00bbd5"
			      }
			    ]
			  },
			  {
			    "featureType": "water",
			    "elementType": "labels.text.fill",
			    "stylers": [
			      {
			        "color": "#9e9e9e"
			      }
			    ]
			  }
			]
    	}

    	// Initialize a map with options
    	map = new google.maps.Map(document.getElementById('map'), mapOptions);
    	var q;

		var marker = new google.maps.Marker({
			position: myLatlng,
			map:map,
			title:'SWIFT'
		});

		setMap();
			
		google.maps.event.addListener(map, 'zoom_changed', function()
		{
			setMap();
		});

    	google.maps.event.addDomListener(window, 'resize', function()
    	{
    		setTimeout(function()
			{
				setMap();
			}, 1000);
    	});

    	function setMap()
    	{
    		map.setCenter(myLatlng);
    	}
    }

    function initContactInfo()
	{
		var eles = $('.contact_link');

		eles.each(function()
		{
			var ele = $(this);
			var text = "";
			var hoverText = ele.data("hover");

			ele.on('mouseenter', function()
			{
				text = ele.text();
				ele.text(hoverText);
			});

			ele.on('mouseleave', function()
			{
				ele.text(text);
			});
		});
	}
});