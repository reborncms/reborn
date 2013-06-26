if (typeof(Media) == 'undefined') {
	var Media = {};
}

$(function(){

	/* ===== ===== Folder ===== ===== */
	$('#m_create_folder').colorbox({
		width: '800',
		height: '400',
		href: $('#m_create_folder').attr('href')+$('#media_main_wrapper').attr('m-f-data'),
		onComplete: function(){ $.colorbox.resize(); }
	});

	$('#m_jump_folder').bind('change', function(){
		if($(this).val() != 0){	window.location.assign(ADMIN+'/media/explode/'+$(this).val());}
		else{ window.location.assign(ADMIN+'/media/'); }
	});

	/* ===== ===== File ===== ===== */
	$('#m_file_upload').colorbox({
		width: '800',
		height: '500',
		onClosed: function(){
			window.location.reload();
		}
	});

	$('a.m_control_edit').live('click', function(e){
		e.preventDefault();
		$(this).parent().parent().find('.m_upload_edit').toggle('fast');
	});
	/* ===== ===== Form ===== ===== */

	/**
	 * @todo 		check form validation
	 */
	$('.ajax_submit').live('click', function(e){
		e.preventDefault();
		var requested = $(this).parent().parent().attr('action');
		var id = $(this).parent().parent().attr('id');
		var source = $('#'+id).attr('m-form-source')

		$.ajax({
			type: 'POST',
			url: requested,
			data: $('#'+id).serialize(),
			beforeSend: function(){  },
			success: function(){
				if(source){  }
				else{
					$.colorbox.remove();
					window.location.reload();
				}

			}
		});
	});

	$('.m_need_field').livequery(function(){
		$(this).focusout(function(){
			var validate = $(this).val();
			if(validate == '') {
				$(this).parent().find('span.error').show('fast');
				$(this).addClass('m_needed');
			}
		});
	});

	$('.m_form_cancle').bind('click', function(e){
		e.preventDefault();
		$.colorbox.close();
	});

	/* ===== ===== Actions ===== ===== */
	$('.m_del_btn').bind('click', function(e){
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: $(this).attr('href'),
			beforeSend: function(){  },
			success: function(){ window.location.reload(); }
		});
	});

	$('.m_edit_btn').colorbox({
		width: '700',
		height: '400',
		scrolling: false,
		onComplete: function(){ $.colorbox.resize(); }
	});

	$('.m_del_file').bind('click', function(e){
		e.preventDefault();

		$.ajax({
			url: $(this).attr('href'),
			beforeSend: function(){  },
			success: function(){ window.location.reload(); }
		});
	});

	$('.m_file_action').colorbox({
		width: '700',
		height: '400',
		scrolling: false,
		onComplete: function(){ $.colorbox.resize(); }
	});

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

	/* ===== ===== Outer ===== ===== */
	$('#m_jump_folder_outer').bind('change', function(){
		var requested = ADMIN+'/media/set_img/'+$(this).val();
		$('#set_img_body').load(requested + ' #set_img_replacer');
	});

	$('#img_set_upload').bind('click', function(e){
		e.preventDefault();

		$('#m_jump_folder_outer').hide();
		$(this).hide();
		$('#img_set_setimg').show();
		$('#set_img_body').load($(this).attr('href'));
	});

	$('#img_set_setimg').bind('click', function(e){
		e.preventDefault();

		$('#m_jump_folder_outer').show();
		$(this).hide();
		$('#img_set_upload').show();
		$('#set_img_body').load($(this).attr('href') + $('#m_jump_folder_outer').val() + ' #set_img_replacer');
	});

});
