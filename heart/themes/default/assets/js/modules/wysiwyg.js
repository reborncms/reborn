var CKEDITOR = window.parent.CKEDITOR;

function insertImage(id, alt)
{
	var where_to = $('#where_to').val();
	var width = (document.getElementById('width-'+id).value == '') ? 0 : document.getElementById('width-'+id).value;
	var height = (document.getElementById('height-'+id).value == '') ? 0 : document.getElementById('height-'+id).value;

	var path = SITEURL + 'media/thumb/' + id + '/' + width + '/' + height + '/';

	var width_tag = (width > 0 ? 'width="' + width + '"' : '');
	var height_tag = (height > 0 ? 'height="' + height + '"' : '');

	window.parent.instance.insertHtml('<img class="m_ck_images"' + width_tag + ' ' + height_tag + 'src="' + path + '" style="float: '+where_to+';" />');

	windowClose();
}
