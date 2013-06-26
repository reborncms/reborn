	$('.media_set_img').live('click', function(e){
		e.preventDefault();
		var orgWidth = 0,
			width = jQuery(this).parent().parent().find('.m_set_img_width').val();

		if(width == ''){
			width = orgWidth;
		}

		var orgHeight = 0,
			height = jQuery(this).parent().parent().find('.m_set_img_height').val();
		if(height == ''){
			height = orgHeight;
		}

		var imgId = $(this).attr('data-id');

		var main = window.parent.document;

		var wrap = main.getElementById('f-img-wrap'),
			fImg = main.getElementById('f-img'),
			field = main.getElementById('attachemnt'),
			showText = main.getElementById('add-f-img'),
			removeText = main.getElementById('remove-f-img');

		var img = main.createElement('img');
		img.src = SITEURL+'media/thumb/'+imgId+'/'+width+'/'+height;
		img.id = 'f-img';
		if(fImg == null)
		{
			wrap.appendChild(img);
		} else {
			wrap.removeChild(fImg);
			wrap.appendChild(img);
		}

		field.value = '{{ url:site }}media/thumb/'+imgId+'/'+width+'/'+height;

		wrap.style.display = "block";

		showText.style.display = "none";

		removeText.style.display = "block";
		return false;
	});
//}

