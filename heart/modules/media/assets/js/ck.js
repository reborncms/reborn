'use stripe';

window.parent.$('.cke_dialog_footer').hide();

function insert() {
	var width = ($('#width').val()) ? $('#width').val() : 0,
		height = ($('#height').val()) ? $('#height').val() : 0,
		alt = $('#alt_text').val();

	if (isNaN(parseInt(width)) || isNaN(parseInt(height))) {
		if (width < 0 || height < 0) {
			alert('Value of width or height cannot be negative.');
			return;
		}
		alert('Value of width or height must be number.');
		return;
	}

	var floating = "float: " + $('#float').val();

	var path = SITEURL + 'media/image/' + $('#image_url').val() + '/' + width + '/' + height;

	window.parent.instance.insertHtml("<img src='"+path+"' style='"+floating+";' alt='"+ alt +"'>");

	window.parent.CKEDITOR.dialog.getCurrent().hide();
}

function linkImg () {
	var image = document.getElementById('link-preview-img');

	$('#link_width').val(image.naturalWidth);
	$('#link_height').val(image.naturalHeight);

	$('#link_preview').show();
}

function linkInsert () {
	var width = ($('#link_width').val()) ? $('#link_width').val() : 0,
		height = ($('#link_height').val()) ? $('#link_height').val() : 0,
		alt = $('#alt_text').val();

	if (isNaN(parseInt(width)) || isNaN(parseInt(height))) {
		if (width < 0 || height < 0) {
			alert('Value of width or height cannot be negative.');
			return;
		}
		alert('Value of width or height must be number.');
		return;
	}

	var floating = "float: " + $('#link_float').val();

	var path = $('#external_link').val();

	window.parent.instance.insertHtml("<img src='"+path+"' style='"+floating+"; width: "+width+"px; height: "+height+"px;' alt='"+ alt +"'>");

	window.parent.CKEDITOR.dialog.getCurrent().hide();
}

$(document).ready(function(){

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
		$('#m-thumb-button button').show();
	});

	$('#m-thumb-action-upload').on('click', function(e){
		e.preventDefault();

		if (! $(this).hasClass('action-active')) {
			$('#thumb-action-bar a').removeClass('action-active');

			$(this).addClass('action-active');
		}
		
		$('#m-thumb-media-wrap').load($(this).attr('href'), '#m-thumb-media-wrap');
	});

	$('#m-thumb-action-media').on('click', function(e){
		e.preventDefault();

		if (! $(this).hasClass('action-active')) {
			$('#thumb-action-bar a').removeClass('action-active');

			$(this).addClass('action-active');
		}

		$('#m-thumb-media-wrap').load($(this).attr('href') + ' #m-thumb-ajax-wrap');
	});

});
