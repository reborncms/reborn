// Accordion
$("#accordion").accordion({ header: "h3", active: 0, collapsible: true, autoHeight: false, icons:{header:"sc-arrow-r",headerSelected:"sc-arrow-d"} });

// Link Sortable
$('ol.sortable').nestedSortable({
	disableNesting: 'no-nest',
	forcePlaceholderSize: true,
	handle: 'div',
	helper:	'clone',
	items: 'li',
	opacity: .6,
	placeholder: 'placeholder',
	revert: 250,
	tabSize: 25,
	tolerance: 'pointer',
	toleranceElement: '> div',
	stop:function(){
		post = {};
		post.order = $('ol.sortable').nestedSortable("toHierarchy");
		post.group = $(this).parent('div').attr('id');
		$.post(SITEURL+ADMIN+'/navigation/order',post);
	}
});

//Link Type Change Event
$('#type_selected').on('change', function(){

	var box = $(this).val(),
		showBox = $('#' + box);
	$('.nav-type-wrap').hide();

	$(showBox).fadeIn(700);

});

// Add new link box open @ close
$('#option-control > a').on('click', function(){
	$('#option-control').toggleClass('selected');
	var oOrc = $('#option-control').hasClass('selected');
	if(oOrc) {
		$('.r-container').slideDown(700);
	} else {
		$('.r-container').slideUp(700);
	}
});

// Nav Link Edit Function
$('.link-edit').colorbox({
	width:"50%",
	height:"80%",
	title: "Navigation Link Edit",
	onComplete:function(){
		$.colorbox.resize();

		//Link Type Change Event in Edit
		$('#type_select_edit').live('change', function(){

			var boxe = $(this).val();

			$('.nav-type-wrap-e').hide();

			$('#' + boxe + 'e').fadeIn(700);

		});

		$('#edit-link-btn').click(function() {
			var form = $('#link-edit-form');
			$.ajax({
				url: $(form).attr('action'),
				type: 'POST',
				data:$(form).serialize(),
				success: function(result){
					if(result.status == 'success') {
						$('#message-wrap').removeClass('success fail error');
						$('#message-wrap').addClass('success');
						$('#message-wrap').html(result.message);
					} else if(result.status == 'fail') {
						$('#message-wrap').removeClass('success fail error');
						$('#message-wrap').addClass('fail');
						$('#message-wrap').html(result.message);
					} else {
						$('#message-wrap').removeClass('success fail error');
						$('#message-wrap').addClass('error');
						$('#message-wrap').html(result.message);
					}

					$.colorbox.resize();
				}
			});

			return false;
		});
	},
	onClosed:function(){
		window.location.reload();
	}
});
