/**
	Solid CSS Framework. (Flat UI Design)
	jQuery PlugIn Helper Javascript Function

	Supprot List -
		** sameHeight(element, options)
			// Adjust element base on max height
		** navHiddder (element, options)
			// For Mobile drop down navigation

**/

(function($) {

	$.fn.sameHeight = function ( options) {

		var screenSize = $(window).width(),
			limitSize = ((typeof options == 'undefined') ? 0 : options);

		if (screenSize < limitSize) {
			return true;
		}

		return this.each(function() {

				var childElements = $(this).children();
					max = 0;

				childElements.each(function() {
					var element = $(this),
						h = $(element).height();
					max = (h > max ?  h : max );
				});
				$(childElements).css({height: max})
								.addClass('sd-height-adjust');
			});
	};

	$.fn.navHidder = function (ele, options) {

		if ($(ele).hasClass('sd-show')) {
			$(ele).hide().removeClass('sd-show');
		} else {
			$(ele).show().addClass('sd-show');
		}
	};

	$.fn.slideSearch = function (options) {

		var btn = $(this),
			direction = 'left',
			speed = 900,
			effect = "linear",
			par = $(this).parent().parent('.search-box'),
			search = par.find('input[type=text], input[type=search]'),
			s_width = $(search).width();

		if (typeof $(btn).data('dir') != 'undefined') {
			direction = $(btn).data('dir');
		}

		if (typeof $(btn).data('speed') != 'undefined') {
			speed = $(btn).data('speed');
		}

		if (typeof $(btn).data('effect') != 'undefined') {
			effect = $(btn).data('effect');
		}

		$(search).animate({
			width: ['toggle'],
			left: "-="+s_width,
			}, speed, effect);
	};

	window.Solid = {
		sameHeight : function () {
			var ele = $('.same-height');

			$(ele).each(function(){
				options = $(this).data('screen');
				$(this).sameHeight(options);
			});
		}

	}

	$(document).on('click', '.nav-hidder-btn', function(e) {
		var btn = $(this),
			target = $(btn).data('targets');

		if (typeof target == 'undefined') {
			var par = $(this).parent('.nav-hidder'),
				target = $(par).find('.nav-hidder-body');
		}
		$(btn).navHidder(target);
	});

	// Bind the SameHeight
	$(document).on('load', window.Solid.sameHeight());

	$(document).on('click', '[data-search=slide]', function(e) {
		var btn = $(this),
			direction = 'left',
			speed = 900,
			effect = "linear";



		$(btn).slideSearch(direction);

		/*if (typeof target == 'undefined') {
			var par = $(this).parent('.nav-hidder'),
				target = $(par).find('.nav-hidder-body');
		}
		$(btn).navHidder(target);*/
	});

})(window.jQuery);

//jQuery(window).on('load', window.Solid.sameHeight());

$('.badge').click(function() {
	alert($(this).data('sd-org-height'));
})

// Prettify PlugIn
$('pre, table code').addClass('prettyprint');
prettyPrint();

