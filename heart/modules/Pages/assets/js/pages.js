jQuery(function(){

	$('#form_slug, #form_title').bind('blur',function(){
		var slug = $('#form_slug').val();
		var post_data = $('#page_form').serialize();
		$.post(SITEURL+ADMIN+'/pages/check_slug',post_data,function(data){
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
		height:"80%",
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
					var result = jQuery.parseJSON(data);
					if(result.status != 'no_save') {
						$('#post_id').val(result.post_id);
						$('#autosave-msg').html(result.time);
					}
				}
			});
		};
		
		// Autosave at 1 min
		if(document.getElementById('page_form'))
		{
			setInterval(function () {
				postAutoSave();
			}, 60000);
		}


});