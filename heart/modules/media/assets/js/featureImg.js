$(function(){
	/*var removeBtn = "<a onclick='javascript:void(0);' class='remove_img button button-red' id='featured_remove_btn' style='display: none;'>Remove</a>";
	$(document.getElementById('f_image_wrap')).prepend(removeBtn);*/

	$('#folder_id').chosen({width: "100%"});

	$('#m_tab_media').bind('click', function(){
		if (! $(this).parent().hasClass('ui-state-active')) {
			$('div#media_body').load($(this).attr('m-data-url') + ' #ajax_wrap');
			container = [];
		}
	});

	$('#folder_id').chosen().change(function(){
		$('div#media_body').load(ADMIN + '/media/feature-image/' + $(this).val() + ' #ajax_wrap');
	});
});

function insert()
{
	var target = window.parent.document;

	var targetImg = target.getElementById('featured_preview'),
		targetInput = target.getElementById('featured_id'),
		targetRmBtn = target.getElementById('featured_remove_btn');
		targetAddBtn = target.getElementById('featured_add_btn');

	var width = ($('#width').val()) ? $('#width').val() : 0,
		height = ($('#height').val()) ? $('#height').val() : 0,
		alt = $('#alt_text').val();

	var img = "<img id='featured_image' src='"+$('#image_url').val()+width+"/"+height+"/' alt='"+alt+"'>";
	var value = $('#image_url').val()+width+"/"+height+"/";

	$($(target.getElementById('featured_image'))).remove();
	$(targetAddBtn).hide();
	$(targetRmBtn).show();

	$(targetImg).append(img);
	$(targetInput).val(value);

	$.colorbox.close();
}

/*function removeFeature()
{
	$(document.getElementById('featured_remove_btn')).hide();
	$(document.getElementById('featured_add_btn')).show();

	$('#f_image_wrap img').remove();
}*/

function imgPreview(id, name, alt, width, height)
{
	$('#m_preview_area').show('fast');

	var src = SITEURL + "media/thumb/"+id+"/185/";
	var current = $('#img_set_preview img').attr('src');
	var hiddenField = "<input type='hidden' id='image_url' value='"+SITEURL+"media/thumb/"+id+"/'>";

	if (current) {
		if (src != current) {
			$('#img_set_preview img').remove();
			$('#img_set_preview #image_url').remove();
			$('#img_set_preview p').remove();
		} else { return; }
	}

	$('#img_set_preview').append("<img src='"+src+"'>");
	$('#img_set_preview').append('<p>'+name+'</p>');
	$('#img_set_preview').append(hiddenField);
	$('#width').val(width);
	$('#height').val(height);
	$('#fileId').val(id);
}