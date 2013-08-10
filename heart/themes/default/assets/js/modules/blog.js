(function($) {
	$(function(){
		
		/* ========== Start of Search Function List ============== */
		
		// Ajax Search Function
		function ajaxSearch(form, url) {		
			$.ajax({
				url: url,
				type: 'POST',
				data: $(form).serialize(),
				beforeSend: function() {
					$('#filter-form').append('<span id="waiting" class="loading"></span>');
				},
				complete: function() {
					$('#waiting').remove();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$('#waiting').remove();
					$('#filter-form').append('<span id="result-error">'+errorThrown+'</span>');
				},
				success: function(data) {
					var data = jQuery.parseJSON(data);
					$('#content-wrapper').html(data.result);
					$('#filter-box').val(data.term);
					$('#filter-form').append('<span id="result-count">'+data.total+'</span>');
				}
			});
		};
		
		// Search the Post
		$('#filter-submit').live('click', function(e){
			e.preventDefault();
			var form = $('#filter-form'),
				url = $(form).attr('action');
				
			ajaxSearch(form, url);
		});
		
		// Ajax Pagination at Search Result
		$('#ajax-pagi a').live('click', function(e){
			e.preventDefault();
			var form = $('#filter-form'),
				url = $(this).attr('href');
				
			ajaxSearch(form, url);
		});
		
		/* ========== End of Search Function List ============== */
		
		// Post Auto Save Function
		function postAutoSave() {
			$.ajax({
				url: SITEURL+ADMIN+"/blog/autosave",
				type: 'POST',
				data:$('.blog').serialize(),
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
		if(document.getElementById('blog-create') || document.getElementById('blog-edit'))
		{
			setInterval(function () {
				postAutoSave();
			}, 60000);
		}
		

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

		// Add New Category Box Open
		$('#add-new-cat').on('click', function(){
			$('#new-cat-box').fadeToggle(700, "linear");
		});
		
		var originalParent = $('#new-cat-parent').val();
		
		Reborn.slugGenerator('#new-cat-name', '#new-cat-slug');
		
		// Ajax New category added
		$('#add-new-cat-ajax-bt').on('click', function(e){
			e.preventDefault();
			var catName = $('#new-cat-name').val(),
				catSlug = $('#new-cat-slug').val(),
				catParent = $('#new-cat-parent').val();
			$.ajax({
				url: SITEURL+ADMIN+'/blog/category/add_cat/',
				type: 'POST',
				data: { new_cat_name: catName, new_cat_slug: catSlug, parent_level: catParent },
				success: function(data) {
					var data = jQuery.parseJSON(data);
					console.log(data);
					var opt = '<option value="'+data.id+'" selected="selected">'+data.name+'</option>';
					$('#cat_selected').append(opt);
					var value = '';
					$('#new-cat-box').fadeToggle(700, "linear");
					$('#new-cat-name').val(value);
					$('#new-cat-slug').val(value);
					$('#new-cat-parent').val(originalParent);
				}
			});
		});
		
		// Tag Input
		if(document.getElementById('tags')) {
			$('#tags').tagsInput({
				width:'auto',
				autocomplete_url:'admin/tag/ajax'
			});
		}

		// Date Picker
		$( "#datepicker" ).datepicker({'dateFormat' : 'yy-mm-dd'});

		// Show hide Schedule Auto Manual
		var manual = $('#sch-manual');
		var auto = $('#sch-auto');
		$(manual).on('click', function(){
			if($(manual+':checked')) {
				$('div#manual-sch').slideDown(700);
			}
		});
		$(auto).on('click', function(){
			$('div#manual-sch').slideUp(700);
		});
		
		// Featured Image Function
		$('#add-f-img').colorbox({
			scrollable: false,
			innerWidth: "70%",
			opacity: 0.5,
			onComplete : function() {
				$.colorbox.resize();
			},
			onClosed : function() {
				$('.media_file_info').die('click');
			}
		});
		
		// Remove Featured Image
		$('#remove-f-img').live('click', function(){
			$('#f-img-wrap img').remove();
			var val = '';
			$('#attachemnt').val(val);
			$(this).hide();
			$('#add-f-img').show();
		});
		
		// Category Page Section ========================
		$('a.c-edit-box').colorbox({
			scrollable: false,
			innerWidth: 600,
			innerHeight: 280,
			inline: true,
			onComplete: function() {
				$.colorbox.resize();
				
				Reborn.slugGenerator('#edit_title', '#edit_slug');
				
				var id = $(this).attr('data-id'),
					name = $(this).attr('data-name'),
					slug = $(this).attr('data-slug'),
					desc = $(this).attr('data-desc'),
					parent = $(this).attr('data-parent'),
					level = $(this).attr('data-level'),
					selectedItem = document.getElementById('edit_selected');
				$('#edit_id').val(id);
				$('#edit_title').val(name);
				$('#edit_slug').val(slug);
				$('#edit_desc').val(desc);
				if(level != 0) {
					level = level - 1;
				}
				selectItemByValue(selectedItem, parent+'|'+level);
				
				$('form#blog-cat-edit').removeAttr('action');
				$('form#blog-cat-edit').live('submit', function(e) {
					
					var form_data = $(this).serialize();
					
					$.ajax({
						url: SITEURL + ADMIN + '/blog/category/edit',
						type: "POST",
					        data: form_data,
						success: function(obj) {
							var data = jQuery.parseJSON(obj);
							var msg_box = $('#edit-msg');
							if(data.status == 'ok') {							
								$(msg_box).html(data.success);
								$(msg_box).show();
								$.colorbox.resize();
								window.location.reload();
								$.colorbox.close();					
							} else {
								$(msg_box).html(data.error);
								$(msg_box).show();
								$.colorbox.resize();
							}
						}
						
						
					});
					e.preventDefault();
				});
			}
		});
		
		// Select option selected by value
		function selectItemByValue(elmnt, value){

			for(var i=0; i < elmnt.options.length; i++)
			{
				if(elmnt.options[i].value == value)
				elmnt.selectedIndex = i;
			}
		}

	});
})(jQuery);