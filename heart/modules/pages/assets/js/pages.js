jQuery(function(){

	$('#form_slug, #form_title').bind('blur',function(){
		var slug = $('#form_slug').val();
		var post_data = $('#page_form').serialize();
		$.post(SITEURL+ADMIN+'/pages/check-slug',post_data,function(data){
			$('#slug_error').html(data);
		});
	});

	$('#page-css').each(function(){
		CodeMirror.fromTextArea(this,{mode:"css",autoClearEmptyLines:true}).setSize(600, 395);
	});
	
	$('#page-js').each(function(){
		CodeMirror.fromTextArea(this,{mode:"javascript",autoClearEmptyLines:true}).setSize(600, 395);
	});	

	$(".design").colorbox({
		inline:true,
		width:"60%",
		height:600,
		onComplete:function(){
			
		},
		onClosed:function(){

		}
	});

	$('.parents ol').hide();

	// Post Auto Save Function
		function postAutoSave() {
			$.ajax({
				url: SITEURL+ADMIN+"/pages/autosave",
				type: 'POST',
				data:$('#page_form').serialize(),
				success: function(data){
					var result = data;
					if(result.status != 'no_save') {
						$('#post_id').val(result.post_id);
						$('#autosave-msg').html(result.time);
					}
				}
			});
		};

		var timeout;

		CKEDITOR.on('instanceCreated', function (e) {
			e.editor.on('change', function (ev) {
				if (timeout) clearTimeout(timeout);
				timeout = setTimeout(function(){
					postAutoSave();
				}, 30000);
			});
		});

		$('#page_form input, #page_form textarea, #page_form select').change(function(){
			if (timeout) clearTimeout(timeout);
			timeout = setTimeout(function () {
				postAutoSave();
			}, 30000);
		});
		
		// Autosave at 1 min
		/*if(document.getElementById('page_form'))
		{
			setInterval(function () {
				postAutoSave();
			}, 60000);
		}*/


});