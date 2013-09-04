//Cheat

if (typeof(Media) == 'undefined') {
	var Media = {};
}

$(function(){
	$('#media_create_folder').colorbox({
		width: '60%',
		height: '400',
		onComplete: function() { $.colorbox.resize(); },
		onClosed: function() { window.location.reload(); }
	});

	$('.m_edit').colorbox({
		width: '60%',
		height: '400',
		onComplete: function() { $.colorbox.resize(); },
		onClosed: function() { window.location.reload(); }
	});

	$('.m_confirm_delete').bind('click', function(e){
		var confirmation = window.confirm('Are you sure you want to delete?? !');
		e.preventDefault();
		if (confirmation) {
			$.ajax({
				type: 'POST',
				url: $(this).attr('href'),
				beforeSend: function(){  },
				success: function(){ window.location.reload(); }
			});
		}
	});

	$('.file_ajax_submit').livequery('click', function(e){
		e.preventDefault();

		var theUniqueId = '#'+$(this).attr('uniqueName');

		if($(theUniqueId).find('.name').val() != '') {
			if ($(theUniqueId).find('.alt_text').val() != '') {
				if ($(theUniqueId).find('.folder_id').val() != '') {
					$.ajax({
						type: 'POST',
						url: $(theUniqueId).find('.form').attr('action'),
						data: $(theUniqueId).find('.form').serialize(),
						beforeSend: function() {},
						success: function(e) {
							if (e.success) {
								$(theUniqueId).find('.m_edit_btn').removeClass('now_showing');
								$(theUniqueId).find('.m_edit_btn').text('Edit');
								$(theUniqueId).find('.m_edit_form').hide('fast');
							} else {
								$(theUniqueId).find('.m_ajax_error').append('<p>' + result.error + '</p>');
								$(theUniqueId).find('.m_ajax_error').show();
							}
						}

					});
				} else {
					$(theUniqueId).find('.folder_id').parent().find('.error').show();
				}
			} else {
				$(theUniqueId).find('.alt_text').parent().find('.error').show();
			}
		} else {
			$(theUniqueId).find('.name').parent().find('.error').show();
		}

	});

	$('#ajax_submit').bind('click', function(e){
		e.preventDefault();

		if ($('#name').val() != '') {
			if($('#slug').val() != ''){
				if ($('#folder_id')) {
					$.ajax({
						type: 'POST',
						url: $('form.form').attr('action'),
						data: $('form.form').serialize(),
						//beforeSend: function() {},
						success: function(e) {
							if (e.success) {
								$.colorbox.close();
							} else {
								$('#m_ajax_error').append('<p>' + e.error + '</p>');
								$('#m_ajax_error').show();
							}
						}
					});
				} else {
					$('#folder_id').parent().find('.error').show();
				}
			} else {
				$('#slug').parent().find('.error').show();
			}
		} else {
			$('#name').parent().find('.error').show();
		}

	});

	Reborn.slugGenerator('#name', '#alt_text');
	Reborn.slugGenerator('#name', '#slug');

	$('#media_upload').colorbox({
		width: '60%',
		height: '400',
		href: SITEURL + ADMIN + '/media/upload/' + $('.media_wrap').attr('id'),
		onClosed: function() { window.location.reload(); }
	});

	// Reinventing the wheel for only me!
	Media.nameGenerate = function (length){
		var vowels = 'aeiouy',
			consonants = 'bcdfghjklmnpqrstvwxz-_',
			key = '';
			D = new Date(),
			alt = D.getMilliseconds() % 2;

		for (var i = 0; i < length; i++) {
			if (alt == 1) {
				key += consonants.charAt(Math.floor(Math.random() * consonants.length));
				alt = 0;
			} else {
				key += vowels.charAt(Math.floor(Math.random() * vowels.length));
				alt = 1;
			}
		}

		return key;
	}

	$('a.m_view').colorbox({
		width: '500',
		height: '60%',
		onComplete: function() { $.colorbox.resize(); }
	});

	$('.dragger').draggable({
		zIndex: 20,
		revert: true
	});

});
