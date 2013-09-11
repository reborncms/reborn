$(function(){
	$('#media_upload').colorbox({
		width: "900",
		height: "400",
		href: SITEURL + ADMIN + '/media/upload/' + $('#media-wrapper').attr('data-folder-id'),
		onClosed: function() {
			window.location.reload();
		}
	});

	$('#folder_id').chosen();

	$('#folder_id').chosen().change(function() {
		if (0 != $(this).val()) {
			window.location.assign(SITEURL + ADMIN + '/media/explore/' + $(this).val());
		} else {
			window.location.assign(SITEURL + ADMIN + '/media/');
		}
	});

	$('#media_create_folder').colorbox({
		width: "50%",
		height: "400",
		scroll: false,
		onComplete: function () {
			$.colorbox.resize();
		}
	});

	var selected = [];

	$('.ff-wrapper').bind('click', function(){
		if (!$(this).hasClass('active-wrap')) {
			$(this).addClass('active-wrap');

			selected.push($(this).attr('data-id'));

			if (selected.length > 1) {
				$('#media_main_action').show();
			}
		} else {
			$(this).removeClass('active-wrap');

			var indexer = selected.indexOf($(this).attr('data-id'));

			selected.splice(indexer, 1);

			if (selected.length <= 1) {
				$('#media_main_action').hide();
			}
		}
	});

	$('#media_main_delete').bind('click', function(){
		var del = confirm("Are you sure you want to delete selected items ?");

		if (del) {

			for(var i = 0; i < selected.length; i++) {
				$.ajax({
					url: SITEURL + ADMIN + '/media/delete/file/' + selected[i]
				});
			}
		}
	});

	$('.action-option').bind('click', function(){
		$(this).parent().parent().find('.options').toggle();
	});

	$('.action_edits').colorbox({
		width: "50%",
		height: "400",
		onComplete: function() {
			$.colorbox.resize();
		}
	});

	$('.action-detail').colorbox({
		inline: true,
		width: "400",
		height: "400",
		closeButton: false,
		scroll: false,
		onOpen: function() {

			var target = $(this).attr('data-target');
			var desc = ($(this).attr('data-desc')) ? $(this).attr('data-desc') : '&nbsp;';

			if ('file' == target) {

				var alt = ($(this).attr('data-alt')) ? $(this).attr('data-alt') : '&nbsp;';

				$('#detail-file-img').css("background", "url("+$(this).attr('data-link')+"0/180/) center no-repeat");
				$('#detail-file-link').html($(this).attr('data-link'));
				$('#detail-file-alt').html(alt);
				$('#detail-file-folder').html($(this).attr('data-folder'));
				$('#detail-file-dimen').html($(this).attr('data-width')+' x '+$(this).attr('data-height'));
				$('#detail-file-size').html($(this).attr('data-size'));
			}

			$('#detail-'+target+'-name').html($(this).attr('data-name'));
			$('#detail-'+target+'-desc').html(desc);
			$('#detail-'+target+'-author').html($(this).attr('data-user'));
		},
		onComplete: function() {
			$.colorbox.resize();
		}
	});

	// tweak the jquery plugin tmpl
	$.tmpl.regexp = /([\s'\\])(?![^%]*%\})|(?:\[%(=|#)([\s\S]+?)%\])|(\[%)|(%\])/g;
});