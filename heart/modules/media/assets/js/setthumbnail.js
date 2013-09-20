$(function(){

	$('#m-thumb-choose-folder').chosen({'width': '60%'});

	//$('#m-thumb-option-pane').perfectScrollbar();

	$('.m-thumbs').livequery('click', function(){

		if (! $(this).hasClass('m-thumb-active')) {
			$('.m-thumbs').removeClass('m-thumb-active');
			$(this).addClass('m-thumb-active');
		}

		$('#width').val($(this).attr('data-width'));
		$('#height').val($(this).attr('data-height'));
		$('#alt_text').val($(this).attr('data-alt'));

		var image = "<img src='"+SITEURL+"media/image/"+$(this).attr('data-filename')+"/300/200'>";

		var imageName = "<p>"+$(this).attr('data-name')+"</p>";

		var imageUrl = "<input type='hidden' value='"+$(this).attr('data-filename')+"' id='image_url'>";

		$('#m-thumb-preview-wrap').html(image+imageName+imageUrl);

		$('#m-thumb-preview-wrap').show();
	});

	$('#m-thumb-choose-folder').chosen().change(function(){
		$('div#m-thumb-body').load(SITEURL + ADMIN + '/media/thumbnail/' + $(this).val() + ' #ajax_wrap');
	});

	$('#m-thumb-action-upload').bind('click', function(e){
		e.preventDefault();

		if (! $(this).hasClass('action-active')) {
			$('#thumb-action-bar a').removeClass('action-active');

			$(this).addClass('action-active');
		}

		$('#m-thumb-media-wrap').load($(this).attr('href')/* + ' #ajax_wrap'*/);
		$(window).unload();
	});

	$('#m-thumb-action-media').livequery('click', function(e){
		e.preventDefault();
		$('#cboxLoadedContent').load($(this).attr('href'));
	})
});

function insert()
{
	var target = window.parent.document;
	console.log(target);
	var targetImg = $('.thumbnail_preview', target),
		targetInput = target.getElementById(THUMB_TARGET),
		targetRmBtn = $('.thumbnail_remove_btn', target);
		targetAddBtn = $('.thumbnail_add_btn', target);

	var width = ($('#width').val()) ? $('#width').val() : 0,
		height = ($('#height').val()) ? $('#height').val() : 0,
		alt = $('#alt_text').val();

	var img = "<img class='thumbnail_image' src='"+SITEURL+'media/image/'+$('#image_url').val() +"/"+ THUMB_WIDTH + "' alt='"+alt+"'>";
	var value = $('#image_url').val()+"/"+width+"/"+height+"/";

	$('.thumbnail_image', target).remove();
	$(targetAddBtn).hide();
	$(targetRmBtn).show();

	$(targetImg).append(img);
	$(targetInput).val(value);

	$.colorbox.close();
}