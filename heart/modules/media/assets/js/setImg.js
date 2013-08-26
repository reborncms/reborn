var container = [];

function setImage(elem, id, alt)
{
	var uniqueName = keyGenerate(5);

	var src = "<input class='image_id' type='hidden' name='imageId[]' value='"+ id +"'>";
	var image = "<img src='" + $(elem).attr('data-url') + "90/' alt='"+ alt +"' />"; //for only sym
	var rm_link = '<a href="javascript:void(0);" class="remove_img">x</a>';

	container[uniqueName] = "<div id='"+ uniqueName +"' class='m_images'>"+ rm_link + image + src + "</div>";

	$(elem).parent().parent().addClass('m_bg_blue');

	$(elem).attr({
		onclick: "javascript:remove(this, "+ id +", '"+ alt +"');",
		selector: uniqueName
	});
}

function remove(elem, id, alt)
{
	delete container[$(elem).attr('selector')];

	$(elem).parent().parent().removeClass('m_bg_blue');

	$(elem).attr("onclick", "javascript:setImage(this, "+ id +", '"+ alt +"')");
	$(elem).removeAttr("selector");
}

function insert()
{
	var theParent = window.parent.document;

	for (var j in container)
	{
		$(theParent.getElementById('thumb_wrap')).append(container[j]);
		$.colorbox.close();
	}
}

// Reinventing the wheel only for image set
function keyGenerate(length)
{
	 var vowels = 'aeiouy',
	consonants = 'bcdfghjklmnpqrstvwxz1234567890',
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

$(function(){
	$('#m_tab_media').bind('click', function(){
		if (! $(this).parent().hasClass('ui-state-active')) {
			$('div#media_body').load($(this).attr('m-data-url') + ' #ajax_wrap');
			container = [];
		}
	});

	$('#tabs').tabs({
		ajaxOptions: {
			beforeSend: function(){
				$('#m_calling_upload').text('Waiting....');
			},
			success: function(){
				$('#m_calling_upload').text('Upload');
			}
		}
	});
});
