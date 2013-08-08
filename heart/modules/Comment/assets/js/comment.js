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
		btn.attr('class', 'btn');

		if( $('input[name="action_to[]"]:checked, .check-all:checked').length >= 1 && $('#sel_multi_action').val() != 'none'){
			$(".button-wrapper .btn").removeAttr('disabled');
		} else {
			$(".button-wrapper .btn").attr('disabled', 'disabled');
		}

		switch (sel_val) {
			case "approved" :
				btn.addClass('btn-green');
			break;

			case "pending" :
				btn.addClass('btn-orange');
			break;

			case "spam" :
				btn.addClass('btn-dark');
			break;

			case "delete" :
				btn.addClass('btn-red');
				btn.addClass('confirm_delete_comment');
			break;

			default :
				btn.attr('class', 'btn');
				btn.attr('disabled', 'disabled');
		}
	});

	$('input[name="action_to[]"], .check-all').on('click', function () {

		if( $('input[name="action_to[]"]:checked, .check-all:checked').length >= 1 && $('#sel_multi_action').val() != 'none'){
			$(".button-wrapper .button").removeAttr('disabled');
		} else {
			$(".button-wrapper .button").attr('disabled', 'disabled');
		}

	});

	$('.comment_message').each(function(){
		var height = $(this).height();
		if (height > 106) {
			$(this).height(106).css('overflow', 'hidden').after('<a href="#" class="com_read_more">( Read More )</a>');
		};
	});

	$('.com_read_more').click(function(e){
		e.preventDefault();
		$(this).toggleClass('hide');
		if ($(this).hasClass('hide')) {
			$(this).siblings('.comment_message').css('height', 'auto');	
			$(this).text('( Hide )');
		} else {
			$(this).siblings('.comment_message').css('height', '106px');	
			$(this).text('( Read More )');
		}
		
	});

	$('.confirm_delete_comment').livequery('click', function(){
		return confirm("This will also delete child comments.Are you sure you want to delete ?");
	});
	
});