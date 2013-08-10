(function($) {
	$(function(){

		// URL Auto Generate
		Reborn.slugGenerator('#form_title', '#form_slug');

		// Right-Side Panel Open
		$('#option-control > a').on('click', function(){
			$('#option-control').toggleClass('selected');
			var oOrc = $('#option-control').hasClass('selected');
			if(oOrc) {
				$('.r-container').slideDown(700);
			} else {
				$('.r-container').slideUp(700);
			}
		});

		// Right-Side Block open
		$('a.r-i-b').on('click', function(){
			var wrapper =  $(this).parent();
			var box = $(wrapper).find('.r-i-b-h');
			$(wrapper).toggleClass('select-box');
			$(box).slideToggle(700);
		});
	});
})(jQuery);