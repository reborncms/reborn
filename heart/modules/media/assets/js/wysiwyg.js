'use stripe';

window.parent.$('.cke_dialog_footer').hide();

var floating = 'none';
var linkFloating = 'none';

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

	var align = ('center' == floating) ? 'margin: 0 auto;' : 'float: ' + floating;

	var path = SITEURL + 'media/image/' + $('#image_url').val() + '/' + width + '/' + height;

	window.parent.CKEDITOR.currentInstance.insertHtml("<img src='"+path+"' style='"+align+";' alt='"+ alt +"'>");

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
		alt = $('#link_alt').val();

	if (isNaN(parseInt(width)) || isNaN(parseInt(height))) {
		if (width < 0 || height < 0) {
			alert('Value of width or height cannot be negative.');
			return;
		}
		alert('Value of width or height must be number.');
		return;
	}

	var align = ('center' == linkFloating) ? 'margin: 0 auto;' : 'float: ' + linkFloating;

	var path = $('#external_link').val();

	window.parent.CKEDITOR.currentInstance.insertHtml("<img src='"+path+"' style='"+align+"; width: "+width+"px; height: "+height+"px;' alt='"+ alt +"'>");

	window.parent.CKEDITOR.dialog.getCurrent().hide();
}

$(document).ready(function(){

	$('#link_ok_btn').bind('click', function(){
		var value = $('#external_link').val().trim();

		if (!value) {
			alert('Please insert link');
		}

		var image = "<img id='link-preview-img' onLoad='javascript:linkImg();' src='"+value+"'/>";

		$('#link-prev-img-wrap').html(image);

	});

	$('#m-thumb-dimension .icon-link').on('click', function() {
		$(this).toggleClass('link-active');
	});

	$('.thumb-img').livequery('click', function(){

		if (! $(this).hasClass('m-thumb-active')) {
			$('.m-thumbs').removeClass('m-thumb-active');
			$(this).addClass('m-thumb-active');
		}

		$('#width').val($(this).attr('data-width'));
		$('#height').val($(this).attr('data-height'));
		$('#alt_text').val($(this).attr('data-alt'));

		var image = "<img src='"+SITEURL+"media/image/"+$(this).attr('data-filename')+"/300/180'>";

		var imageName = "<p>"+$(this).attr('data-name')+"</p>";

		var imageUrl = "<input type='hidden' value='"+$(this).attr('data-filename')+"' id='image_url'>";

		$('#m-thumb-preview-wrap').html(image+imageName+imageUrl);
	});

	$('#m-thumb-align .btns-group .btn').on('click', function () {
		if (! $(this).hasClass('align-active')) {
			$('#m-thumb-align .btns-group .btn').removeClass('align-active');
			$(this).addClass('align-active');

			floating = $(this).val();
		}
	});

	$('.link-form-wrap .btns-group .btn').on('click', function() {
		if (! $(this).hasClass('align-active')) {
			$('.link-form-wrap .btns-group .btn').removeClass('align-active');
			$(this).addClass('align-active');

			linkFloating = $(this).val();
		}
	});

	$('#nav_media_tab').on('click', function(e){
		e.preventDefault();

		if (! $(this).hasClass('action-active')) {
			$('#thumb-action-bar a').removeClass('action-active');

			$(this).addClass('action-active');
		}

		$('#m-thumb-body').load(SITEURL + ADMIN + "/media/wysiwyg #ajax_wrap");
	});

	$('#jumper').on('change', function(e){

		$('#m-thumb-body').load(SITEURL + ADMIN + "/media/wysiwyg/" + $(this).val() + ' #ajax_wrap');

	});

});
