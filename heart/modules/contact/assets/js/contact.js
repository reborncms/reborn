(function($) {
	$(function(){

		/* email template view */
		$('.email-template').colorbox({
			width:"60%"
		});

		$('.inbox-view').colorbox({
			width:"60%"
		});

		/* email template name check */
		$('#name').bind('blur',function(){
			var post_data = $('#etemplate').serialize();
			var p = $(this).parent().find('.info');
			$.post(SITEURL+ADMIN +  '/contact/email-template/check-name/',post_data, function(data){

				p.addClass('error');
				p.html(data);

			});
			
		});

		Reborn.slugGenerator('#name', '#slug');
	});
})(jQuery);