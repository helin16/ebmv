//** Smooth Navigational Menu- By Dynamic Drive DHTML code library: http://www.dynamicdrive.com
//** Script Download/ instructions page: http://www.dynamicdrive.com/dynamicindex1/ddlevelsmenu/
//** Menu created: Nov 12, 2008

//** Dec 12th, 08" (v1.01): Fixed Shadow issue when multiple LIs within the same UL (level) contain sub menus: http://www.dynamicdrive.com/forums/showthread.php?t=39177&highlight=smooth

//** Feb 11th, 09" (v1.02): The currently active main menu item (LI A) now gets a CSS class of ".selected", including sub menu items.

//** May 1st, 09" (v1.3):
//** 1) Now supports vertical (side bar) menu mode- set "orientation" to 'v'
//** 2) In IE6, shadows are now always disabled

//** July 27th, 09" (v1.31): Fixed bug so shadows can be disabled if desired.
//** Feb 2nd, 10" (v1.4): Adds ability to specify delay before sub menus appear and disappear, respectively. See showhidedelay variable below

//** Dec 17th, 10" (v1.5): Updated menu shadow to use CSS3 box shadows when the browser is FF3.5+, IE9+, Opera9.5+, or Safari3+/Chrome. Only .js file changed.

//** Jun 28th, 2012: Unofficial update adds optional hover images for down and right arrows in the format: filename_over.ext
//** These must be present for whichever or both of the arrow(s) are used and will preload.

//** Dec 23rd, 2012 Unofficial update to fixed configurable z-index, add method option to init "toggle" which activates on click or "hover" 
//** which activates on mouse over/out - defaults to "toggle" (click activation), with detection of touch devices to activate on click for them.
//** Add option for when there are two or more menus using "toggle" activation, whether or not all previously opened menus collapse
//** on new menu activation, or just those within that specific menu
//** See: http://www.dynamicdrive.com/forums/showthread.php?72449-PLEASE-HELP-with-Smooth-Navigational-Menu-(v1-51)&p=288466#post288466

//** Feb 7th, 2013 Unofficial update change fixed configurable z-index back to graduated for cases of main UL wrapping. Update off menu click detection in
//** ipad/iphone to touchstart because document click wasn't registering. see: http://www.dynamicdrive.com/forums/showthread.php?72825

//** Feb 14th, 2013 Add window.ontouchstart to means tests for detecting touch browsers - thanks DD!
//** Feb 15th, 2013 Add 'ontouchstart' in window and 'ontouchstart' in document.documentElement to means tests for detecting touch browsers - thanks DD!

//** Feb 20th, 2013 correct for IE 9+ sometimes adding a pixel to the offsetHeight of the top level trigger for horizontal menus
//** Feb 23rd, 2013 move CSS3 shadow adjustment for IE 9+ to the script, add resize event for all browsers to reposition open toggle 
//** menus and shadows in window if they would have gone to a different position at the new window dimensions
//** Feb 25th, 2013 (v2.0) All unofficial updates by John merged into official and now called v2.0. Changed "method" option's default value to "hover"
//** May 14th, 2013 (v2.1) Adds class 'repositioned' to menus moved due to being too close to the browser's right edge
//** May 30th, 2013 (v2.1) Change from version sniffing to means testing for jQuery versions which require added code for click toggle event handling
//** Sept 15th, 2013 add workaround for false positives for touch on Chrome
//** Sept 22nd, 2013 (v2.2) Add vertical repositioning if sub menu will not fit in the viewable vertical area. May be turned off by setting
// 	repositionv: false,
//** in the init. Sub menus that are vertically repositioned will have the class 'repositionedv' added to them.

var ddsmoothmenu = {

///////////////////////// Global Configuration Options: /////////////////////////

//Specify full URL to down and right arrow images (23 is padding-right for top level LIs with drop downs, 6 is for vertical top level items with fly outs):
arrowimages: {down:['downarrowclass', 'down.gif', 23], right:['rightarrowclass', 'right.gif', 6]},
transition: {overtime:300, outtime:300}, //duration of slide in/ out animation, in milliseconds
shadow: true, //enable shadow? (offsets now set in ddsmoothmenu.css stylesheet)
showhidedelay: {showdelay: 100, hidedelay: 200}, //set delay in milliseconds before sub menus appear and disappear, respectively
zindexvalue: 1000, //set z-index value for menus
closeonnonmenuclick: true, //when clicking outside of any "toggle" method menu, should all "toggle" menus close? 
closeonmouseout: false, //when leaving a "toggle" menu, should all "toggle" menus close? Will not work on touchscreen

/////////////////////// End Global Configuration Options ////////////////////////

overarrowre: /(?=\.(gif|jpg|jpeg|png|bmp))/i,
overarrowaddtofilename: '_over',
detecttouch: !!('ontouchstart' in window) || !!('ontouchstart' in document.documentElement) || !!window.ontouchstart || (!!window.Touch && !!window.Touch.length) || !!window.onmsgesturechange || (window.DocumentTouch && window.document instanceof window.DocumentTouch),
detectwebkit: navigator.userAgent.toLowerCase().indexOf("applewebkit") > -1, //detect WebKit browsers (Safari, Chrome etc)
idevice: /ipad|iphone/i.test(navigator.userAgent),
detectie6: (function(){var ie; return (ie = /MSIE (\d+)/.exec(navigator.userAgent)) && ie[1] < 7;})(),
detectie9: (function(){var ie; return (ie = /MSIE (\d+)/.exec(navigator.userAgent)) && ie[1] > 8;})(),
ie9shadow: function(){},
css3support: typeof document.documentElement.style.boxShadow === 'string' || (!document.all && document.querySelector), //detect browsers that support CSS3 box shadows (ie9+ or FF3.5+, Safari3+, Chrome etc)
prevobjs: [], menus: null,

executelink: function(jQuery, prevobjs, e){
	var prevscount = prevobjs.length, link = e.target;
	while(--prevscount > -1){
		if(prevobjs[prevscount] === this){
			prevobjs.splice(prevscount, 1);
			if(link.href !== ddsmoothmenu.emptyhash && link.href && jQuery(link).is('a') && !jQuery(link).children('span.' + ddsmoothmenu.arrowimages.down[0] +', span.' + ddsmoothmenu.arrowimages.right[0]).length){
				if(link.target && link.target !== '_self'){
					window.open(link.href, link.target);
				} else {
					window.location.href = link.href;
				}
				e.stopPropagation();
			}
		}
	}
},

repositionv: function(jQuerysubul, jQuerylink, newtop, winheight, doctop, method, menutop){
	menutop = menutop || 0;
	var topinc = 0, doclimit = winheight + doctop;
	jQuerysubul.css({top: newtop, display: 'block'});
	while(jQuerysubul.offset().top < doctop) {
		jQuerysubul.css({top: ++newtop});
		++topinc;
	}
	if(!topinc && jQuerylink.offset().top + jQuerylink.outerHeight() < doclimit && jQuerysubul.data('height') + jQuerysubul.offset().top > doclimit){
		jQuerysubul.css({top: doctop - jQuerylink.parents('ul').last().offset().top - jQuerylink.position().top});
	}
	method === 'toggle' && jQuerysubul.css({display: 'none'});
	if(newtop !== menutop){jQuerysubul.addClass('repositionedv');}
	return [topinc, newtop];
},

updateprev: function(jQuery, prevobjs, jQuerycurobj){
	var prevscount = prevobjs.length, prevobj, jQueryindexobj = jQuerycurobj.parents().add(this);
	while(--prevscount > -1){
		if(jQueryindexobj.index((prevobj = prevobjs[prevscount])) < 0){
			jQuery(prevobj).trigger('click', [1]);
			prevobjs.splice(prevscount, 1);
		}
	}
	prevobjs.push(this);
},

subulpreventemptyclose: function(e){
	var link = e.target;
	if(link.href === ddsmoothmenu.emptyhash && jQuery(link).parent('li').find('ul').length < 1){
		e.preventDefault();
		e.stopPropagation();
	}
},

getajaxmenu: function(jQuery, setting, nobuild){ //function to fetch external page containing the panel DIVs
	var jQuerymenucontainer=jQuery('#'+setting.contentsource[0]); //reference empty div on page that will hold menu
	jQuerymenucontainer.html("Loading Menu...");
	jQuery.ajax({
		url: setting.contentsource[1], //path to external menu file
		async: true,
		error: function(ajaxrequest){
			jQuerymenucontainer.html('Error fetching content. Server Response: '+ajaxrequest.responseText);
		},
		success: function(content){
			jQuerymenucontainer.html(content);
			!!!nobuild && ddsmoothmenu.buildmenu(jQuery, setting);
		}
	});
},

closeall: function(e){
	var smoothmenu = ddsmoothmenu, prevscount;
	if(!smoothmenu.globaltrackopen){return;}
	if(e.type === 'mouseleave' || ((e.type === 'click' || e.type === 'touchstart') && smoothmenu.menus.index(e.target) < 0)){
		prevscount = smoothmenu.prevobjs.length;
		while(--prevscount > -1){
			jQuery(smoothmenu.prevobjs[prevscount]).trigger('click');
			smoothmenu.prevobjs.splice(prevscount, 1);
		}
	}
},

emptyhash: jQuery('<a href="#"></a>').get(0).href,

buildmenu: function(jQuery, setting){
	var smoothmenu = ddsmoothmenu;
	smoothmenu.globaltrackopen = smoothmenu.closeonnonmenuclick || smoothmenu.closeonmouseout;
	var zsub = 0; //subtractor to be incremented so that each top level menu can be covered by previous one's drop downs
	var prevobjs = smoothmenu.globaltrackopen? smoothmenu.prevobjs : [];
	var jQuerymainparent = jQuery("#"+setting.mainmenuid).removeClass("ddsmoothmenu ddsmoothmenu-v").addClass(setting.classname || "ddsmoothmenu");
	setting.repositionv = setting.repositionv !== false;
	var jQuerymainmenu = jQuerymainparent.find('>ul'); //reference main menu UL
	var method = smoothmenu.detecttouch? 'toggle' : setting.method === 'toggle'? 'toggle' : 'hover';
	var jQuerytopheaders = jQuerymainmenu.find('>li>ul').parent();//has('ul');
	var orient = setting.orientation!='v'? 'down' : 'right', jQueryparentshadow = jQuery(document.body);
	jQuerymainmenu.click(function(e){e.target.href === smoothmenu.emptyhash && e.preventDefault();});
	if(method === 'toggle') {
		if(smoothmenu.globaltrackopen){
			smoothmenu.menus = smoothmenu.menus? smoothmenu.menus.add(jQuerymainmenu.add(jQuerymainmenu.find('*'))) : jQuerymainmenu.add(jQuerymainmenu.find('*'));
		}
		if(smoothmenu.closeonnonmenuclick){
			if(orient === 'down'){jQuerymainparent.click(function(e){e.stopPropagation();});}
			jQuery(document).unbind('click.smoothmenu').bind('click.smoothmenu', smoothmenu.closeall);
			if(smoothmenu.idevice){
				document.removeEventListener('touchstart', smoothmenu.closeall, false);
				document.addEventListener('touchstart', smoothmenu.closeall, false);
			}
		} else if (setting.closeonnonmenuclick){
			if(orient === 'down'){jQuerymainparent.click(function(e){e.stopPropagation();});}
			jQuery(document).bind('click.' + setting.mainmenuid, function(e){jQuerymainmenu.find('li>a.selected').parent().trigger('click');});
			if(smoothmenu.idevice){
				document.addEventListener('touchstart', function(e){jQuerymainmenu.find('li>a.selected').parent().trigger('click');}, false);
			}
		}
		if(smoothmenu.closeonmouseout){
			var jQueryleaveobj = orient === 'down'? jQuerymainparent : jQuerymainmenu;
			jQueryleaveobj.bind('mouseleave.smoothmenu', smoothmenu.closeall);
		} else if (setting.closeonmouseout){
			var jQueryleaveobj = orient === 'down'? jQuerymainparent : jQuerymainmenu;
			jQueryleaveobj.bind('mouseleave.smoothmenu', function(){jQuerymainmenu.find('li>a.selected').parent().trigger('click');});
		}
		if(!jQuery('style[title="ddsmoothmenushadowsnone"]').length){
			jQuery('head').append('<style title="ddsmoothmenushadowsnone" type="text/css">.ddsmoothmenushadowsnone{display:none!important;}</style>');
		}
		var shadowstimer;
		jQuery(window).bind('resize scroll', function(){
			clearTimeout(shadowstimer);
			var jQueryselected = jQuerymainmenu.find('li>a.selected').parent(),
			jQueryshadows = jQuery('.ddshadow').addClass('ddsmoothmenushadowsnone');
			jQueryselected.eq(0).trigger('click');
			jQueryselected.trigger('click');
			shadowstimer = setTimeout(function(){jQueryshadows.removeClass('ddsmoothmenushadowsnone');}, 100);
		});
	}

	jQuerytopheaders.each(function(){
		var jQuerycurobj=jQuery(this).css({zIndex: (setting.zindexvalue || smoothmenu.zindexvalue) + zsub--}); //reference current LI header
		var jQuerysubul=jQuerycurobj.children('ul:eq(0)').css({display:'block'}).data('timers', {});
		var jQuerylink = jQuerycurobj.children("a:eq(0)").css({paddingRight: smoothmenu.arrowimages[orient][2]}).append( //add arrow images
			'<span style="display: block;" class="' + smoothmenu.arrowimages[orient][0] + '"></span>'
		);
		var dimensions = {
			w	: jQuerylink.outerWidth(),
			h	: jQuerycurobj.innerHeight(),
			subulw	: jQuerysubul.outerWidth(),
			subulh	: jQuerysubul.outerHeight()
		};
		var menutop = orient === 'down'? dimensions.h : 0;
		jQuerysubul.css({top: menutop});
		function restore(){jQuerylink.removeClass('selected');}
		method === 'toggle' && jQuerysubul.click(smoothmenu.subulpreventemptyclose);
		jQuerycurobj[method](
			function(e){
				if(!jQuerycurobj.data('headers')){
					smoothmenu.buildsubheaders(jQuery, jQuerysubul.find('>li>ul').parent(), setting, method, prevobjs);
					jQuerycurobj.data('headers', true).find('>ul').each(function(i, ul){
						var jQueryul = jQuery(ul);
						jQueryul.data('height', jQueryul.outerHeight());
					}).css({display:'none', visibility:'visible'});
				}
				method === 'toggle' && smoothmenu.updateprev.call(this, jQuery, prevobjs, jQuerycurobj);
				clearTimeout(jQuerysubul.data('timers').hidetimer);
				jQuerylink.addClass('selected');
				jQuerysubul.data('timers').showtimer=setTimeout(function(){
					var menuleft = orient === 'down'? 0 : dimensions.w;
					var menumoved = menuleft, newtop, doctop, winheight, topinc = 0;
					menuleft=(jQuerycurobj.offset().left+menuleft+dimensions.subulw>jQuery(window).width())? (orient === 'down'? -dimensions.subulw+dimensions.w : -dimensions.w) : menuleft; //calculate this sub menu's offsets from its parent
					menumoved = menumoved !== menuleft;
					jQuerysubul.css({top: menutop}).removeClass('repositionedv');
					if(setting.repositionv && jQuerylink.offset().top + menutop + jQuerysubul.data('height') > (winheight = jQuery(window).height()) + (doctop = jQuery(document).scrollTop())){
						newtop = (orient === 'down'? 0 : jQuerylink.outerHeight()) - jQuerysubul.data('height');
						topinc = smoothmenu.repositionv(jQuerysubul, jQuerylink, newtop, winheight, doctop, method, menutop)[0];
					}
					jQuerysubul.css({left:menuleft, width:dimensions.subulw}).stop(true, true).animate({height:'show',opacity:'show'}, smoothmenu.transition.overtime, function(){this.style.removeAttribute && this.style.removeAttribute('filter');});
					if(menumoved){jQuerysubul.addClass('repositioned');} else {jQuerysubul.removeClass('repositioned');}
					if (setting.shadow){
						if(!jQuerycurobj.data('jQueryshadow')){
							jQuerycurobj.data('jQueryshadow', jQuery('<div></div>').addClass('ddshadow toplevelshadow').prependTo(jQueryparentshadow).css({zIndex: jQuerycurobj.css('zIndex')}));  //insert shadow DIV and set it to parent node for the next shadow div
						}
						smoothmenu.ie9shadow(jQuerycurobj.data('jQueryshadow'));
						var offsets = jQuerysubul.offset();
						var shadowleft = offsets.left;
						var shadowtop = offsets.top;
						jQuerycurobj.data('jQueryshadow').css({overflow: 'visible', width:dimensions.subulw, left:shadowleft, top:shadowtop}).stop(true, true).animate({height:dimensions.subulh}, smoothmenu.transition.overtime);
					}
				}, smoothmenu.showhidedelay.showdelay);
			},
			function(e, speed){
				var jQueryshadow = jQuerycurobj.data('jQueryshadow');
				if(method === 'hover'){restore();}
				else{smoothmenu.executelink.call(this, jQuery, prevobjs, e);}
				clearTimeout(jQuerysubul.data('timers').showtimer);
				jQuerysubul.data('timers').hidetimer=setTimeout(function(){
					jQuerysubul.stop(true, true).animate({height:'hide', opacity:'hide'}, speed || smoothmenu.transition.outtime, function(){method === 'toggle' && restore();});
					if (jQueryshadow){
						if (!smoothmenu.css3support && smoothmenu.detectwebkit){ //in WebKit browsers, set first child shadow's opacity to 0, as "overflow:hidden" doesn't work in them
							jQueryshadow.children('div:eq(0)').css({opacity:0});
						}
						jQueryshadow.stop(true, true).animate({height:0}, speed || smoothmenu.transition.outtime, function(){if(method === 'toggle'){this.style.overflow = 'hidden';}});
					}
				}, smoothmenu.showhidedelay.hidedelay);
			}
		); //end hover/toggle
	}); //end jQuerytopheaders.each()
},

buildsubheaders: function(jQuery, jQueryheaders, setting, method, prevobjs){
	//setting.jQuerymainparent.data('jQueryheaders').add(jQueryheaders);
	jQueryheaders.each(function(){ //loop through each LI header
		var smoothmenu = ddsmoothmenu;
		var jQuerycurobj=jQuery(this).css({zIndex: jQuery(this).parent('ul').css('z-index')}); //reference current LI header
		var jQuerysubul=jQuerycurobj.children('ul:eq(0)').css({display:'block'}).data('timers', {}), jQueryparentshadow;
		method === 'toggle' && jQuerysubul.click(smoothmenu.subulpreventemptyclose);
		var jQuerylink = jQuerycurobj.children("a:eq(0)").append( //add arrow images
			'<span style="display: block;" class="' + smoothmenu.arrowimages['right'][0] + '"></span>'
		);
		var dimensions = {
			w	: jQuerylink.outerWidth(),
			subulw	: jQuerysubul.outerWidth(),
			subulh	: jQuerysubul.outerHeight()
		};
		jQuerysubul.css({top: 0});
		function restore(){jQuerylink.removeClass('selected');}
		jQuerycurobj[method](
			function(e){
				if(!jQuerycurobj.data('headers')){
					smoothmenu.buildsubheaders(jQuery, jQuerysubul.find('>li>ul').parent(), setting, method, prevobjs);
					jQuerycurobj.data('headers', true).find('>ul').each(function(i, ul){
						var jQueryul = jQuery(ul);
						jQueryul.data('height', jQueryul.height());
					}).css({display:'none', visibility:'visible'});
				}
				method === 'toggle' && smoothmenu.updateprev.call(this, jQuery, prevobjs, jQuerycurobj);
				clearTimeout(jQuerysubul.data('timers').hidetimer);
				jQuerylink.addClass('selected');
				jQuerysubul.data('timers').showtimer=setTimeout(function(){
					var menuleft= dimensions.w;
					var menumoved = menuleft, newtop, doctop, winheight, topinc = 0;
					menuleft=(jQuerycurobj.offset().left+menuleft+dimensions.subulw>jQuery(window).width())? -dimensions.w : menuleft; //calculate this sub menu's offsets from its parent
					menumoved = menumoved !== menuleft;
					jQuerysubul.css({top: 0}).removeClass('repositionedv');
					if(setting.repositionv && jQuerylink.offset().top + jQuerysubul.data('height') > (winheight = jQuery(window).height()) + (doctop = jQuery(document).scrollTop())){
						newtop = jQuerylink.outerHeight() - jQuerysubul.data('height');
						topinc = smoothmenu.repositionv(jQuerysubul, jQuerylink, newtop, winheight, doctop, method);
						newtop = topinc[1];
						topinc = topinc[0];
					}
					jQuerysubul.css({left:menuleft, width:dimensions.subulw}).stop(true, true).animate({height:'show',opacity:'show'}, smoothmenu.transition.overtime, function(){this.style.removeAttribute && this.style.removeAttribute('filter');});
					if(menumoved){jQuerysubul.addClass('repositioned');} else {jQuerysubul.removeClass('repositioned');}
					if (setting.shadow){
						if(!jQuerycurobj.data('jQueryshadow')){
							jQueryparentshadow = jQuerycurobj.parents("li:eq(0)").data('jQueryshadow');
							jQuerycurobj.data('jQueryshadow', jQuery('<div></div>').addClass('ddshadow').prependTo(jQueryparentshadow).css({zIndex: jQueryparentshadow.css('z-index')}));  //insert shadow DIV and set it to parent node for the next shadow div
						}
						var offsets = jQuerysubul.offset();
						var shadowleft = menuleft;
						var shadowtop = jQuerycurobj.position().top - (newtop? jQuerysubul.data('height') - jQuerylink.outerHeight() - topinc : 0);
						if (smoothmenu.detectwebkit && !smoothmenu.css3support){ //in WebKit browsers, restore shadow's opacity to full
							jQuerycurobj.data('jQueryshadow').css({opacity:1});
						}
						jQuerycurobj.data('jQueryshadow').css({overflow: 'visible', width:dimensions.subulw, left:shadowleft, top:shadowtop}).stop(true, true).animate({height:dimensions.subulh}, smoothmenu.transition.overtime);
					}
				}, smoothmenu.showhidedelay.showdelay);
			},
			function(e, speed){
				var jQueryshadow = jQuerycurobj.data('jQueryshadow');
				if(method === 'hover'){restore();}
				else{smoothmenu.executelink.call(this, jQuery, prevobjs, e);}
				clearTimeout(jQuerysubul.data('timers').showtimer);
				jQuerysubul.data('timers').hidetimer=setTimeout(function(){
					jQuerysubul.stop(true, true).animate({height:'hide', opacity:'hide'}, speed || smoothmenu.transition.outtime, function(){
						method === 'toggle' && restore();
					});
					if (jQueryshadow){
						if (!smoothmenu.css3support && smoothmenu.detectwebkit){ //in WebKit browsers, set first child shadow's opacity to 0, as "overflow:hidden" doesn't work in them
							jQueryshadow.children('div:eq(0)').css({opacity:0});
						}
						jQueryshadow.stop(true, true).animate({height:0}, speed || smoothmenu.transition.outtime, function(){if(method === 'toggle'){this.style.overflow = 'hidden';}});
					}
				}, smoothmenu.showhidedelay.hidedelay);
			}
		); //end hover/toggle for subheaders
	}); //end jQueryheaders.each() for subheaders
},

init: function(setting){
	if(this.detectie6 && parseFloat(jQuery.fn.jquery) > 1.3){
		this.init = function(setting){
			if (typeof setting.contentsource=="object"){ //if external ajax menu
				jQuery(function(jQuery){ddsmoothmenu.getajaxmenu(jQuery, setting, 'nobuild');});
			}
			return false;
		};
		jQuery('link[href*="ddsmoothmenu"]').attr('disabled', true);
		jQuery(function(jQuery){
			alert('You Seriously Need to Update Your Browser!\n\nDynamic Drive Smooth Navigational Menu Showing Text Only Menu(s)\n\nDEVELOPER\'s NOTE: This script will run in IE 6 when using jQuery 1.3.2 or less,\nbut not real well.');
				jQuery('link[href*="ddsmoothmenu"]').attr('disabled', true);
		});
		return this.init(setting);
	}
	var mainmenuid = '#' + setting.mainmenuid, right, down, stylestring = ['</style>\n'], stylesleft = setting.arrowswap? 4 : 2;
	function addstyles(){
		if(stylesleft){return;}
		if (typeof setting.customtheme=="object" && setting.customtheme.length==2){ //override default menu colors (default/hover) with custom set?
			var mainselector=(setting.orientation=="v")? mainmenuid : mainmenuid+', '+mainmenuid;
			stylestring.push([mainselector,' ul li a {background:',setting.customtheme[0],';}\n',
				mainmenuid,' ul li a:hover {background:',setting.customtheme[1],';}'].join(''));
		}
		stylestring.push('\n<style type="text/css">');
		stylestring.reverse();
		jQuery('head').append(stylestring.join('\n'));
	}
	if(setting.arrowswap){
		right = ddsmoothmenu.arrowimages.right[1].replace(ddsmoothmenu.overarrowre, ddsmoothmenu.overarrowaddtofilename);
		down = ddsmoothmenu.arrowimages.down[1].replace(ddsmoothmenu.overarrowre, ddsmoothmenu.overarrowaddtofilename);
		jQuery(new Image()).bind('load error', function(e){
			setting.rightswap = e.type === 'load';
			if(setting.rightswap){
				stylestring.push([mainmenuid, ' ul li a:hover .', ddsmoothmenu.arrowimages.right[0], ', ',
				mainmenuid, ' ul li a.selected .', ddsmoothmenu.arrowimages.right[0],
				' { background-image: url(', this.src, ');}'].join(''));
			}
			--stylesleft;
			addstyles();
		}).attr('src', right);
		jQuery(new Image()).bind('load error', function(e){
			setting.downswap = e.type === 'load';
			if(setting.downswap){
				stylestring.push([mainmenuid, ' ul li a:hover .', ddsmoothmenu.arrowimages.down[0], ', ',
				mainmenuid, ' ul li a.selected .', ddsmoothmenu.arrowimages.down[0],
				' { background-image: url(', this.src, ');}'].join(''));
			}
			--stylesleft;
			addstyles();
		}).attr('src', down);
	}
	jQuery(new Image()).bind('load error', function(e){
		if(e.type === 'load'){
			stylestring.push([mainmenuid+' ul li a .', ddsmoothmenu.arrowimages.right[0],' { background: url(', this.src, ') no-repeat;width:', this.width,'px;height:', this.height, 'px;}'].join(''));
		}
		--stylesleft;
		addstyles();
	}).attr('src', ddsmoothmenu.arrowimages.right[1]);
	jQuery(new Image()).bind('load error', function(e){
		if(e.type === 'load'){
			stylestring.push([mainmenuid+' ul li a .', ddsmoothmenu.arrowimages.down[0],' { background: url(', this.src, ') no-repeat;width:', this.width,'px;height:', this.height, 'px;}'].join(''));
		}
		--stylesleft;
		addstyles();
	}).attr('src', ddsmoothmenu.arrowimages.down[1]);
	setting.shadow = this.detectie6 && (setting.method === 'hover' || setting.orientation === 'v')? false : setting.shadow || this.shadow; //in IE6, always disable shadow except for horizontal toggle menus
	jQuery(document).ready(function(jQuery){ //ajax menu?
		if (setting.shadow && ddsmoothmenu.css3support){jQuery('body').addClass('ddcss3support');}
		if (typeof setting.contentsource=="object"){ //if external ajax menu
			ddsmoothmenu.getajaxmenu(jQuery, setting);
		}
		else{ //else if markup menu
			ddsmoothmenu.buildmenu(jQuery, setting);
		}
	});
}
}; //end ddsmoothmenu variable


// Patch for jQuery 1.9+ which lack click toggle (deprecated in 1.8, removed in 1.9)
// Will not run if using another patch like jQuery Migrate, which also takes care of this
if(
	(function(jQuery){
		var clicktogglable = false;
		try {
			jQuery('<a href="#"></a>').toggle(function(){}, function(){clicktogglable = true;}).trigger('click').trigger('click');
		} catch(e){}
		return !clicktogglable;
	})(jQuery)
){
	(function(){
		var toggleDisp = jQuery.fn.toggle; // There's an animation/css method named .toggle() that toggles display. Save a reference to it.
		jQuery.extend(jQuery.fn, {
			toggle: function( fn, fn2 ) {
				// The method fired depends on the arguments passed.
				if ( !jQuery.isFunction( fn ) || !jQuery.isFunction( fn2 ) ) {
					return toggleDisp.apply(this, arguments);
				}
				// Save reference to arguments for access in closure
				var args = arguments, guid = fn.guid || jQuery.guid++,
					i = 0,
					toggler = function( event ) {
						// Figure out which function to execute
						var lastToggle = ( jQuery._data( this, "lastToggle" + fn.guid ) || 0 ) % i;
						jQuery._data( this, "lastToggle" + fn.guid, lastToggle + 1 );
	
						// Make sure that clicks stop
						event.preventDefault();
	
						// and execute the function
						return args[ lastToggle ].apply( this, arguments ) || false;
					};

				// link all the functions, so any of them can unbind this click handler
				toggler.guid = guid;
				while ( i < args.length ) {
					args[ i++ ].guid = guid;
				}

				return this.click( toggler );
			}
		});
	})();
}

/* TECHNICAL NOTE: To overcome an intermittent layout bug in IE 9+, the script will change margin top and left for the shadows to 
   1px less than their computed values, and the first two values for the box-shadow property will be changed to 1px larger than 
   computed, ex: -1px top and left margins and 6px 6px 5px #aaa box-shadow results in what appears to be a 5px box-shadow. 
   Other browsers skip this step and it shouldn't affect you in most cases. In some rare cases it will result in 
   slightly narrower (by 1px) box shadows for IE 9+ on one or more of the drop downs. Without this, sometimes 
   the shadows could be 1px beyond their drop down resulting in a gap. This is the first of the two patches below. 
   and also relates to the MS CSSOM which uses decimal fractions of pixels for layout while only reporting rounded values. 
   There appears to be no computedStyle workaround for this one. */

//Scripted CSS Patch for IE 9+ intermittent mis-rendering of box-shadow elements (see above TECHNICAL NOTE for more info)
//And jQuery Patch for IE 9+ CSSOM re: offset Width and Height and re: getBoundingClientRect(). Both run only in IE 9 and later.
//IE 9 + uses decimal fractions of pixels internally for layout but only reports rounded values using the offset and getBounding methods.
//These are sometimes rounded inconsistently. This second patch gets the decimal values directly from computedStyle.
if(ddsmoothmenu.detectie9){
	(function(jQuery){ //begin Scripted CSS Patch
		function incdec(v, how){return parseInt(v) + how + 'px';}
		ddsmoothmenu.ie9shadow = function(jQueryelem){ //runs once
			var getter = document.defaultView.getComputedStyle(jQueryelem.get(0), null),
			curshadow = getter.getPropertyValue('box-shadow').split(' '),
			curmargin = {top: getter.getPropertyValue('margin-top'), left: getter.getPropertyValue('margin-left')};
			jQuery('head').append(['\n<style title="ie9shadow" type="text/css">',
			'.ddcss3support .ddshadow {',
			'\tbox-shadow: ' + incdec(curshadow[0], 1) + ' ' + incdec(curshadow[1], 1) + ' ' + curshadow[2] + ' ' + curshadow[3] + ';',
			'}', '.ddcss3support .ddshadow.toplevelshadow {',
			'\topacity: ' + (jQuery('.ddcss3support .ddshadow').css('opacity') - 0.1) + ';',
			'\tmargin-top: ' + incdec(curmargin.top, -1) + ';',
			'\tmargin-left: ' + incdec(curmargin.left, -1) + ';', '}',
			'</style>\n'].join('\n'));
			ddsmoothmenu.ie9shadow = function(){}; //becomes empty function after running once
		}; //end Scripted CSS Patch
		var jqheight = jQuery.fn.height, jqwidth = jQuery.fn.width; //begin jQuery Patch for IE 9+ .height() and .width()
		jQuery.extend(jQuery.fn, {
			height: function(){
				var obj = this.get(0);
				if(this.length < 1 || arguments.length || obj === window || obj === document){
					return jqheight.apply(this, arguments);
				}
				return parseFloat(document.defaultView.getComputedStyle(obj, null).getPropertyValue('height'));
			},
			innerHeight: function(){
				if(this.length < 1){return null;}
				var val = this.height(), obj = this.get(0), getter = document.defaultView.getComputedStyle(obj, null);
				val += parseInt(getter.getPropertyValue('padding-top'));
				val += parseInt(getter.getPropertyValue('padding-bottom'));
				return val;
			},
			outerHeight: function(bool){
				if(this.length < 1){return null;}
				var val = this.innerHeight(), obj = this.get(0), getter = document.defaultView.getComputedStyle(obj, null);
				val += parseInt(getter.getPropertyValue('border-top-width'));
				val += parseInt(getter.getPropertyValue('border-bottom-width'));
				if(bool){
					val += parseInt(getter.getPropertyValue('margin-top'));
					val += parseInt(getter.getPropertyValue('margin-bottom'));
				}
				return val;
			},
			width: function(){
				var obj = this.get(0);
				if(this.length < 1 || arguments.length || obj === window || obj === document){
					return jqwidth.apply(this, arguments);
				}
				return parseFloat(document.defaultView.getComputedStyle(obj, null).getPropertyValue('width'));
			},
			innerWidth: function(){
				if(this.length < 1){return null;}
				var val = this.width(), obj = this.get(0), getter = document.defaultView.getComputedStyle(obj, null);
				val += parseInt(getter.getPropertyValue('padding-right'));
				val += parseInt(getter.getPropertyValue('padding-left'));
				return val;
			},
			outerWidth: function(bool){
				if(this.length < 1){return null;}
				var val = this.innerWidth(), obj = this.get(0), getter = document.defaultView.getComputedStyle(obj, null);
				val += parseInt(getter.getPropertyValue('border-right-width'));
				val += parseInt(getter.getPropertyValue('border-left-width'));
				if(bool){
					val += parseInt(getter.getPropertyValue('margin-right'));
					val += parseInt(getter.getPropertyValue('margin-left'));
				}
				return val;
			}
		}); //end jQuery Patch for IE 9+ .height() and .width()
	})(jQuery);
}