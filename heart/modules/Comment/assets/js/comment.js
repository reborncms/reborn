jQuery(function(){
	$('.comment-reply-btn, .comment-edit-btn').colorbox({
		width:"45%",
		onComplete : function(){
			$('#reply-edit-btn').on('click', function(e){
				e.preventDefault();
				var form_action = $('#re-form').attr('action');
				var form_val = $('#re-form').serialize();
				$.post(form_action, form_val, function(data){
					if(data == "true") {
						window.location.reload();
						$.colorbox.close();
					} else {
						var error_msg = "Sorry something went wrong. Please try again.";
						$('#ajax-form-error').html(error_msg);
					}
				});
			})
		}
	});

	$('#sel_multi_action').on('change', function(){
		var sel_val = $(this).val();
		var btn = $('#multi_action_btn');
		btn.attr('class', 'button');

		if( $('input[name="action_to[]"]:checked, .check-all:checked').length >= 1 && $('#sel_multi_action').val() != 'none'){
			$(".button-wrapper .button").removeAttr('disabled');
		} else {
			$(".button-wrapper .button").attr('disabled', 'disabled');
		}

		switch (sel_val) {
			case "approved" :
				btn.addClass('button-green');
			break;

			case "pending" :
				btn.addClass('button-yellow');
			break;

			case "spam" :
				btn.addClass('button-orange');
			break;

			case "delete" :
				btn.addClass('button-red');
				btn.addClass('confirm_delete_comment');
			break;

			default :
				btn.attr('class', 'button');
				btn.attr('disabled', 'disabled');
		}
	});

	$('input[name="action_to[]"], .check-all').live('click', function () {

		if( $('input[name="action_to[]"]:checked, .check-all:checked').length >= 1 && $('#sel_multi_action').val() != 'none'){
			$(".button-wrapper .button").removeAttr('disabled');
		} else {
			$(".button-wrapper .button").attr('disabled', 'disabled');
		}

	});

	$('.comment_message').each(function(){
		var height = $(this).height();
		if (height > 100) {
			$(this).height(100).css('overflow', 'hidden').after('<a href="#" class="com_read_more">( Read More )</a>');
		};
	});

	$('.com_read_more').click(function(e){
		e.preventDefault();
		$(this).toggleClass('hide');
		if ($(this).hasClass('hide')) {
			$(this).siblings('.comment_message').css('height', 'auto');	
			$(this).text('( Hide )');
		} else {
			$(this).siblings('.comment_message').css('height', '100px');	
			$(this).text('( Read More )');
		}
		
	});

	$('.confirm_delete_comment').livequery('click', function(){
		return confirm("This will also delete child comments.Are you sure you want to delete ?");
	});
	
});