'use stripe';

function imgPreview(id, name, width, height)
{
	$('#m_preview_area').show('fast');

	var src = SITEURL + "media/thumb/"+id+"/200/";
	var current = $('#m_img_preview img').attr('src');

	if (current) {
		if (src != current) {
			$('#m_img_preview img').remove();
			$('#m_img_preview p').remove();
		} else { return; }
	}

	$('#m_img_preview').append("<img src='"+src+"'>");
	$('#m_img_preview').append('<p>'+name+'</p>');
	$('#width').val(width);
	$('#height').val(height);
	$('#fileId').val(id);
}

window.parent.$('.cke_dialog_footer').hide();

$(document).ready(function(){
	/*var h = $('#m_chooser_area').height();

	h = (h < $('#m_main_area').height()) ? $('#m_main_area').height : h;

	$('#m_chooser_area').height(h);*/

	$('#insert').live('click', function(){
		var width = $('#width').val();
		var height = $('#height').val();

		width = (width == '') ? 0 : width;
		height = (height == '') ? 0 : height;

		if (isNaN(parseInt(width)) || isNaN(parseInt(height))) {
			if (width < 0 || height < 0) {
				alert('Value of width or height cannot be negative.');
				return;
			}
			alert('Value of width or height must be number.');
			return;
		}

		var floating = "float: " + $('#float').val();

		var path = SITEURL + 'media/thumb/' + $('#fileId').val() + '/' + width + '/' + height + '/';

		window.parent.instance.insertHtml("<img src='"+path+"' style='"+floating+"'>");

		window.parent.CKEDITOR.dialog.getCurrent().hide();
	});

	$('#folder_id').bind('change', function(){
		window.location.assign($(this).val());
	});

	$('#link_insert').live('click', function(){

		var width = $('#width').val();
		var height = $('#height').val();

		$('#img_preview').removeAttr();

		width = (width == '') ? $('#img_preview').innerWidth() : width;
		height = (height == '') ? $('#img_preview').innerHeight() : height;

		console.log(width);
		console.log(height);

		if (isNaN(parseInt(width)) || isNaN(parseInt(height))) {
			if (width < 0 || height < 0) {
				alert('Value of width or height cannot be negative.');
				return;
			}
			alert('Value of width or height must be number.');
			return;
		}

		var floating = "float: " + $('#float').val();

		var path = $('#link').val();

		window.parent.instance.insertHtml("<img src='"+path+"' style='"+floating+"; width: "+width+"px; height: "+height+"px;'>");

		window.parent.CKEDITOR.dialog.getCurrent().hide();
	});

	/*$('#m_ck_upload').bind('click', function(e){
		e.preventDefault();
		$('div#m_ajax_wrapper').load($(this).attr('href')+' .media_wrap');
	});*/
});
