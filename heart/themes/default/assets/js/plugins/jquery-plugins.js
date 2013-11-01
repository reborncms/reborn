// External jQuery PlugIns Collection

/*! Copyright (c) 2010 Brandon Aaron (http://brandonaaron.net)
 * Dual licensed under the MIT (MIT_LICENSE.txt)
 * and GPL Version 2 (GPL_LICENSE.txt) licenses.
 *
 * Version: 1.1.1
 * Requires jQuery 1.3+
 * Docs: http://docs.jquery.com/Plugins/livequery
 */

(function($) {

$.extend($.fn, {
	livequery: function(type, fn, fn2) {
		var self = this, q;

		// Handle different call patterns
		if ($.isFunction(type))
			fn2 = fn, fn = type, type = undefined;

		// See if Live Query already exists
		$.each( $.livequery.queries, function(i, query) {
			if ( self.selector == query.selector && self.context == query.context &&
				type == query.type && (!fn || fn.$lqguid == query.fn.$lqguid) && (!fn2 || fn2.$lqguid == query.fn2.$lqguid) )
					// Found the query, exit the each loop
					return (q = query) && false;
		});

		// Create new Live Query if it wasn't found
		q = q || new $.livequery(this.selector, this.context, type, fn, fn2);

		// Make sure it is running
		q.stopped = false;

		// Run it immediately for the first time
		q.run();

		// Contnue the chain
		return this;
	},

	expire: function(type, fn, fn2) {
		var self = this;

		// Handle different call patterns
		if ($.isFunction(type))
			fn2 = fn, fn = type, type = undefined;

		// Find the Live Query based on arguments and stop it
		$.each( $.livequery.queries, function(i, query) {
			if ( self.selector == query.selector && self.context == query.context &&
				(!type || type == query.type) && (!fn || fn.$lqguid == query.fn.$lqguid) && (!fn2 || fn2.$lqguid == query.fn2.$lqguid) && !this.stopped )
					$.livequery.stop(query.id);
		});

		// Continue the chain
		return this;
	}
});

$.livequery = function(selector, context, type, fn, fn2) {
	this.selector = selector;
	this.context  = context;
	this.type     = type;
	this.fn       = fn;
	this.fn2      = fn2;
	this.elements = [];
	this.stopped  = false;

	// The id is the index of the Live Query in $.livequery.queries
	this.id = $.livequery.queries.push(this)-1;

	// Mark the functions for matching later on
	fn.$lqguid = fn.$lqguid || $.livequery.guid++;
	if (fn2) fn2.$lqguid = fn2.$lqguid || $.livequery.guid++;

	// Return the Live Query
	return this;
};

$.livequery.prototype = {
	stop: function() {
		var query = this;

		if ( this.type )
			// Unbind all bound events
			this.elements.unbind(this.type, this.fn);
		else if (this.fn2)
			// Call the second function for all matched elements
			this.elements.each(function(i, el) {
				query.fn2.apply(el);
			});

		// Clear out matched elements
		this.elements = [];

		// Stop the Live Query from running until restarted
		this.stopped = true;
	},

	run: function() {
		// Short-circuit if stopped
		if ( this.stopped ) return;
		var query = this;

		var oEls = this.elements,
			els  = $(this.selector, this.context),
			nEls = els.not(oEls);

		// Set elements to the latest set of matched elements
		this.elements = els;

		if (this.type) {
			// Bind events to newly matched elements
			nEls.bind(this.type, this.fn);

			// Unbind events to elements no longer matched
			if (oEls.length > 0)
				$.each(oEls, function(i, el) {
					if ( $.inArray(el, els) < 0 )
						$.event.remove(el, query.type, query.fn);
				});
		}
		else {
			// Call the first function for newly matched elements
			nEls.each(function() {
				query.fn.apply(this);
			});

			// Call the second function for elements no longer matched
			if ( this.fn2 && oEls.length > 0 )
				$.each(oEls, function(i, el) {
					if ( $.inArray(el, els) < 0 )
						query.fn2.apply(el);
				});
		}
	}
};

$.extend($.livequery, {
	guid: 0,
	queries: [],
	queue: [],
	running: false,
	timeout: null,

	checkQueue: function() {
		if ( $.livequery.running && $.livequery.queue.length ) {
			var length = $.livequery.queue.length;
			// Run each Live Query currently in the queue
			while ( length-- )
				$.livequery.queries[ $.livequery.queue.shift() ].run();
		}
	},

	pause: function() {
		// Don't run anymore Live Queries until restarted
		$.livequery.running = false;
	},

	play: function() {
		// Restart Live Queries
		$.livequery.running = true;
		// Request a run of the Live Queries
		$.livequery.run();
	},

	registerPlugin: function() {
		$.each( arguments, function(i,n) {
			// Short-circuit if the method doesn't exist
			if (!$.fn[n]) return;

			// Save a reference to the original method
			var old = $.fn[n];

			// Create a new method
			$.fn[n] = function() {
				// Call the original method
				var r = old.apply(this, arguments);

				// Request a run of the Live Queries
				$.livequery.run();

				// Return the original methods result
				return r;
			}
		});
	},

	run: function(id) {
		if (id != undefined) {
			// Put the particular Live Query in the queue if it doesn't already exist
			if ( $.inArray(id, $.livequery.queue) < 0 )
				$.livequery.queue.push( id );
		}
		else
			// Put each Live Query in the queue if it doesn't already exist
			$.each( $.livequery.queries, function(id) {
				if ( $.inArray(id, $.livequery.queue) < 0 )
					$.livequery.queue.push( id );
			});

		// Clear timeout if it already exists
		if ($.livequery.timeout) clearTimeout($.livequery.timeout);
		// Create a timeout to check the queue and actually run the Live Queries
		$.livequery.timeout = setTimeout($.livequery.checkQueue, 20);
	},

	stop: function(id) {
		if (id != undefined)
			// Stop are particular Live Query
			$.livequery.queries[ id ].stop();
		else
			// Stop all Live Queries
			$.each( $.livequery.queries, function(id) {
				$.livequery.queries[ id ].stop();
			});
	}
});

// Register core DOM manipulation methods
$.livequery.registerPlugin('append', 'prepend', 'after', 'before', 'wrap', 'attr', 'removeAttr', 'addClass', 'removeClass', 'toggleClass', 'empty', 'remove', 'html');

// Run Live Queries when the Document is ready
$(function() { $.livequery.play(); });

})(jQuery);

// tipsy, facebook style tooltips for jquery
// version 1.0.0a
// (c) 2008-2010 jason frame [jason@onehackoranother.com]
// released under the MIT license

(function($) {

    function maybeCall(thing, ctx) {
        return (typeof thing == 'function') ? (thing.call(ctx)) : thing;
    };

    function isElementInDOM(ele) {
      while (ele = ele.parentNode) {
        if (ele == document) return true;
      }
      return false;
    };

    function Tipsy(element, options) {
        this.$element = $(element);
        this.options = options;
        this.enabled = true;
        this.fixTitle();
    };

    Tipsy.prototype = {
        show: function() {
            var title = this.getTitle();
            if (title && this.enabled) {
                var $tip = this.tip();

                $tip.find('.tipsy-inner')[this.options.html ? 'html' : 'text'](title);
                $tip[0].className = 'tipsy'; // reset classname in case of dynamic gravity
                $tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).prependTo(document.body);

                var pos = $.extend({}, this.$element.offset(), {
                    width: this.$element[0].offsetWidth,
                    height: this.$element[0].offsetHeight
                });

                var actualWidth = $tip[0].offsetWidth,
                    actualHeight = $tip[0].offsetHeight,
                    gravity = maybeCall(this.options.gravity, this.$element[0]);

                var tp;
                switch (gravity.charAt(0)) {
                    case 'n':
                        tp = {top: pos.top + pos.height + this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
                        break;
                    case 's':
                        tp = {top: pos.top - actualHeight - this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
                        break;
                    case 'e':
                        tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth - this.options.offset};
                        break;
                    case 'w':
                        tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width + this.options.offset};
                        break;
                }

                if (gravity.length == 2) {
                    if (gravity.charAt(1) == 'w') {
                        tp.left = pos.left + pos.width / 2 - 15;
                    } else {
                        tp.left = pos.left + pos.width / 2 - actualWidth + 15;
                    }
                }

                $tip.css(tp).addClass('tipsy-' + gravity);
                $tip.find('.tipsy-arrow')[0].className = 'tipsy-arrow tipsy-arrow-' + gravity.charAt(0);
                if (this.options.className) {
                    $tip.addClass(maybeCall(this.options.className, this.$element[0]));
                }

                if (this.options.fade) {
                    $tip.stop().css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: this.options.opacity});
                } else {
                    $tip.css({visibility: 'visible', opacity: this.options.opacity});
                }
            }
        },

        hide: function() {
            if (this.options.fade) {
                this.tip().stop().fadeOut(function() { $(this).remove(); });
            } else {
                this.tip().remove();
            }
        },

        fixTitle: function() {
            var $e = this.$element;
            if ($e.attr('title') || typeof($e.attr('original-title')) != 'string') {
                $e.attr('original-title', $e.attr('title') || '').removeAttr('title');
            }
        },

        getTitle: function() {
            var title, $e = this.$element, o = this.options;
            this.fixTitle();
            var title, o = this.options;
            if (typeof o.title == 'string') {
                title = $e.attr(o.title == 'title' ? 'original-title' : o.title);
            } else if (typeof o.title == 'function') {
                title = o.title.call($e[0]);
            }
            title = ('' + title).replace(/(^\s*|\s*$)/, "");
            return title || o.fallback;
        },

        tip: function() {
            if (!this.$tip) {
                this.$tip = $('<div class="tipsy"></div>').html('<div class="tipsy-arrow"></div><div class="tipsy-inner"></div>');
                this.$tip.data('tipsy-pointee', this.$element[0]);
            }
            return this.$tip;
        },

        validate: function() {
            if (!this.$element[0].parentNode) {
                this.hide();
                this.$element = null;
                this.options = null;
            }
        },

        enable: function() { this.enabled = true; },
        disable: function() { this.enabled = false; },
        toggleEnabled: function() { this.enabled = !this.enabled; }
    };

    $.fn.tipsy = function(options) {

        if (options === true) {
            return this.data('tipsy');
        } else if (typeof options == 'string') {
            var tipsy = this.data('tipsy');
            if (tipsy) tipsy[options]();
            return this;
        }

        options = $.extend({}, $.fn.tipsy.defaults, options);

        function get(ele) {
            var tipsy = $.data(ele, 'tipsy');
            if (!tipsy) {
                tipsy = new Tipsy(ele, $.fn.tipsy.elementOptions(ele, options));
                $.data(ele, 'tipsy', tipsy);
            }
            return tipsy;
        }

        function enter() {
            var tipsy = get(this);
            tipsy.hoverState = 'in';
            if (options.delayIn == 0) {
                tipsy.show();
            } else {
                tipsy.fixTitle();
                setTimeout(function() { if (tipsy.hoverState == 'in') tipsy.show(); }, options.delayIn);
            }
        };

        function leave() {
            var tipsy = get(this);
            tipsy.hoverState = 'out';
            if (options.delayOut == 0) {
                tipsy.hide();
            } else {
                setTimeout(function() { if (tipsy.hoverState == 'out') tipsy.hide(); }, options.delayOut);
            }
        };

        if (!options.live) this.each(function() { get(this); });

        if (options.trigger != 'manual') {
            var binder   = options.live ? 'live' : 'bind',
                eventIn  = options.trigger == 'hover' ? 'mouseenter' : 'focus',
                eventOut = options.trigger == 'hover' ? 'mouseleave' : 'blur';
            this[binder](eventIn, enter)[binder](eventOut, leave);
        }

        return this;

    };

    $.fn.tipsy.defaults = {
        className: null,
        delayIn: 0,
        delayOut: 0,
        fade: false,
        fallback: '',
        gravity: 'n',
        html: false,
        live: false,
        offset: 0,
        opacity: 0.8,
        title: 'title',
        trigger: 'hover'
    };

    $.fn.tipsy.revalidate = function() {
      $('.tipsy').each(function() {
        var pointee = $.data(this, 'tipsy-pointee');
        if (!pointee || !isElementInDOM(pointee)) {
          $(this).remove();
        }
      });
    };

    // Overwrite this method to provide options on a per-element basis.
    // For example, you could store the gravity in a 'tipsy-gravity' attribute:
    // return $.extend({}, options, {gravity: $(ele).attr('tipsy-gravity') || 'n' });
    // (remember - do not modify 'options' in place!)
    $.fn.tipsy.elementOptions = function(ele, options) {
        return $.metadata ? $.extend({}, options, $(ele).metadata()) : options;
    };

    $.fn.tipsy.autoNS = function() {
        return $(this).offset().top > ($(document).scrollTop() + $(window).height() / 2) ? 's' : 'n';
    };

    $.fn.tipsy.autoWE = function() {
        return $(this).offset().left > ($(document).scrollLeft() + $(window).width() / 2) ? 'e' : 'w';
    };

    /**
     * yields a closure of the supplied parameters, producing a function that takes
     * no arguments and is suitable for use as an autogravity function like so:
     *
     * @param margin (int) - distance from the viewable region edge that an
     *        element should be before setting its tooltip's gravity to be away
     *        from that edge.
     * @param prefer (string, e.g. 'n', 'sw', 'w') - the direction to prefer
     *        if there are no viewable region edges effecting the tooltip's
     *        gravity. It will try to vary from this minimally, for example,
     *        if 'sw' is preferred and an element is near the right viewable
     *        region edge, but not the top edge, it will set the gravity for
     *        that element's tooltip to be 'se', preserving the southern
     *        component.
     */
     $.fn.tipsy.autoBounds = function(margin, prefer) {
		return function() {
			var dir = {ns: prefer[0], ew: (prefer.length > 1 ? prefer[1] : false)},
			    boundTop = $(document).scrollTop() + margin,
			    boundLeft = $(document).scrollLeft() + margin,
			    $this = $(this);

			if ($this.offset().top < boundTop) dir.ns = 'n';
			if ($this.offset().left < boundLeft) dir.ew = 'w';
			if ($(window).width() + $(document).scrollLeft() - $this.offset().left < margin) dir.ew = 'e';
			if ($(window).height() + $(document).scrollTop() - $this.offset().top < margin) dir.ns = 's';

			return dir.ns + (dir.ew ? dir.ew : '');
		}
	};

})(jQuery);

/*
* EASYDROPDOWN - A Drop-down Builder for Styleable Inputs and Menus
* Version: 2.1.2
* License: Creative Commons Attribution 3.0 Unported - CC BY 3.0
* http://creativecommons.org/licenses/by/3.0/
* This software may be used freely on commercial and non-commercial projects with attribution to the author/copyright holder.
* Author: Patrick Kunka
* Copyright 2013 Patrick Kunka, All Rights Reserved
*/

(function(d){function e(){this.isField=!0;this.keyboardMode=this.hasLabel=this.cutOff=this.disabled=this.inFocus=this.down=!1;this.nativeTouch=!0;this.wrapperClass="dropdown";this.onChange=null}e.prototype={constructor:e,instances:[],init:function(a,c){var b=this;d.extend(b,c);b.$select=d(a);b.id=a.id;b.options=[];b.$options=b.$select.find("option");b.isTouch="ontouchend"in document;b.$select.removeClass(b.wrapperClass+" dropdown");b.$select.is(":disabled")&&(b.disabled=!0);b.$options.length&&(b.$options.each(function(a){var c=
d(this);c.is(":selected")&&(b.selected={index:a,title:c.text()},b.focusIndex=a);c.hasClass("label")&&0==a?(b.hasLabel=!0,b.label=c.text(),c.attr("value","")):b.options.push({domNode:c[0],title:c.text(),value:c.val(),selected:c.is(":selected")})}),b.selected||(b.selected={index:0,title:b.$options.eq(0).text()},b.focusIndex=0),b.render())},render:function(){var a=this;a.$container=a.$select.wrap('<div class="'+a.wrapperClass+(a.isTouch&&a.nativeTouch?" touch":"")+(a.disabled?" disabled":"")+'"><span class="old"/></div>').parent().parent();
a.$active=d('<span class="selected">'+a.selected.title+"</span>").appendTo(a.$container);a.$carat=d('<span class="carat"/>').appendTo(a.$container);a.$scrollWrapper=d("<div><ul/></div>").appendTo(a.$container);a.$dropDown=a.$scrollWrapper.find("ul");a.$form=a.$container.closest("form");d.each(a.options,function(){a.$dropDown.append("<li"+(this.selected?' class="active"':"")+">"+this.title+"</li>")});a.$items=a.$dropDown.find("li");a.maxHeight=0;a.cutOff&&a.$items.length>a.cutOff&&a.$container.addClass("scrollable");
for(i=0;i<a.$items.length;i++){var c=a.$items.eq(i);a.maxHeight+=c.outerHeight();if(a.cutOff==i+1)break}a.isTouch&&a.nativeTouch?a.bindTouchHandlers():a.bindHandlers()},bindTouchHandlers:function(){var a=this;a.$container.on("click.easyDropDown",function(){a.$select.focus()});a.$select.on({change:function(){var c=d(this).find("option:selected"),b=c.text(),c=c.val();a.$active.text(b);"function"===typeof a.onChange&&a.onChange.call(a.$select[0],{title:b,value:c})},focus:function(){a.$container.addClass("focus")},
blur:function(){a.$container.removeClass("focus")}})},bindHandlers:function(){var a=this;a.query="";a.$container.on({"click.easyDropDown":function(){a.down||a.disabled?a.close():a.open()},"mousemove.easyDropDown":function(){a.keyboardMode&&(a.keyboardMode=!1)}});d("body").on("click.easyDropDown."+a.id,function(c){c=d(c.target);var b=a.wrapperClass.split(" ").join(".");!c.closest("."+b).length&&a.down&&a.close()});a.$items.on({"click.easyDropDown":function(){var c=d(this).index();a.select(c);a.$select.focus()},
"mouseover.easyDropDown":function(){if(!a.keyboardMode){var c=d(this);c.addClass("focus").siblings().removeClass("focus");a.focusIndex=c.index()}},"mouseout.easyDropDown":function(){a.keyboardMode||d(this).removeClass("focus")}});a.$select.on({"focus.easyDropDown":function(){a.$container.addClass("focus");a.inFocus=!0},"blur.easyDropDown":function(){a.$container.removeClass("focus");a.inFocus=!1},"keydown.easyDropDown":function(c){if(a.inFocus){a.keyboardMode=!0;var b=c.keyCode;if(38==b||40==b||32==
b)c.preventDefault(),38==b?(a.focusIndex--,a.focusIndex=0>a.focusIndex?a.$items.length-1:a.focusIndex):40==b&&(a.focusIndex++,a.focusIndex=a.focusIndex>a.$items.length-1?0:a.focusIndex),a.down||a.open(),a.$items.removeClass("focus").eq(a.focusIndex).addClass("focus"),a.cutOff&&a.scrollToView(),a.query="";if(a.down)if(9==b||27==b)a.close();else{if(13==b)return c.preventDefault(),a.select(a.focusIndex),a.close(),!1;if(8==b)return c.preventDefault(),a.query=a.query.slice(0,-1),a.search(),clearTimeout(a.resetQuery),
!1;38!=b&&40!=b&&(c=String.fromCharCode(b),a.query+=c,a.search(),clearTimeout(a.resetQuery))}}},"keyup.easyDropDown":function(){a.resetQuery=setTimeout(function(){a.query=""},1200)}});a.$dropDown.on("scroll.easyDropDown",function(c){a.$dropDown[0].scrollTop==a.$dropDown[0].scrollHeight-a.maxHeight?a.$container.addClass("bottom"):a.$container.removeClass("bottom")});if(a.$form.length)a.$form.on("reset.easyDropDown",function(){a.$active.text(a.hasLabel?a.label:a.options[0].title)})},unbindHandlers:function(){this.$container.add(this.$select).add(this.$items).add(this.$form).add(this.$dropDown).off(".easyDropDown");
d("body").off("."+this.id)},open:function(){var a=window.scrollY||document.documentElement.scrollTop,c=window.scrollX||document.documentElement.scrollLeft,b=this.notInViewport(a);this.closeAll();this.$select.focus();window.scrollTo(c,a+b);this.$container.addClass("open");this.$scrollWrapper.css("height",this.maxHeight+"px");this.down=!0},close:function(){this.$container.removeClass("open");this.$scrollWrapper.css("height","0px");this.focusIndex=this.selected.index;this.query="";this.down=!1},closeAll:function(){var a=
Object.getPrototypeOf(this).instances,c;for(c in a)a[c].close()},select:function(a){"string"===typeof a&&(a=this.$select.find("option[value="+a+"]").index()-1);var c=this.options[a],b=this.hasLabel?a+1:a;this.$items.removeClass("active").eq(a).addClass("active");this.$active.text(c.title);this.$select.find("option").removeAttr("selected").eq(b).prop("selected",!0).parent().trigger("change");this.selected={index:a,title:c.title};this.focusIndex=i;"function"===typeof this.onChange&&this.onChange.call(this.$select[0],
{title:c.title,value:c.value})},search:function(){var a=this,c=function(b){a.focusIndex=b;a.$items.removeClass("focus").eq(a.focusIndex).addClass("focus");a.scrollToView()};for(i=0;i<a.options.length;i++){var b=a.options[i].title.toUpperCase();if(0==b.indexOf(a.query)){c(i);return}}for(i=0;i<a.options.length;i++)if(b=a.options[i].title.toUpperCase(),-1<b.indexOf(a.query)){c(i);break}},scrollToView:function(){if(this.focusIndex>=this.cutOff){var a=this.$items.eq(this.focusIndex).outerHeight()*(this.focusIndex+
1)-this.maxHeight;this.$dropDown.scrollTop(a)}},notInViewport:function(a){var c=a+(window.innerHeight||document.documentElement.clientHeight),b=this.$dropDown.offset().top+this.maxHeight;return b>=a&&b<=c?0:b-c+5},destroy:function(){this.unbindHandlers();this.$select.unwrap().siblings().remove();this.$select.unwrap();delete Object.getPrototypeOf(this).instances[this.$select[0].id]},disable:function(){this.disabled=!0;this.$container.addClass("disabled");this.$select.attr("disabled",!0);this.down||
this.close()},enable:function(){this.disabled=!1;this.$container.removeClass("disabled");this.$select.attr("disabled",!1)}};var f=function(a,c){a.id=a.id?a.id:"EasyDropDown"+("00000"+(16777216*Math.random()<<0).toString(16)).substr(-6).toUpperCase();var b=new e;b.instances[a.id]||(b.instances[a.id]=b,b.init(a,c))};d.fn.easyDropDown=function(){var a=arguments,c=[],b;b=this.each(function(){if(a&&"string"===typeof a[0]){var b=e.prototype.instances[this.id][a[0]](a[1],a[2]);b&&c.push(b)}else f(this,a[0])});
return c.length?1<c.length?c:c[0]:b};d(function(){"function"!==typeof Object.getPrototypeOf&&(Object.getPrototypeOf="object"===typeof"test".__proto__?function(a){return a.__proto__}:function(a){return a.constructor.prototype});d("select.dropdown").each(function(){var a=d(this).attr("data-settings");settings=a?d.parseJSON(a):{};f(this,settings)})})})(jQuery);
