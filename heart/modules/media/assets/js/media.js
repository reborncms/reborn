$(function(){
	$('.ff-mix-container').perfectScrollbar();

	$('#media_upload').colorbox({
		width: "900",
		height: "600",
		href: SITEURL + ADMIN + '/media/upload/' + $('#media-wrapper').attr('data-folder-id'),
		onClosed: function() {
			window.location.reload();
		}
	});

	$('.ff-wrapper').bind('mouseleave', function() {
		$(this).find('.options').hide();
	});

	$('#folder_id').chosen({ 'width' : '100%' });

	$('#folder_id').chosen().change(function() {
		if (0 != $(this).val()) {
			window.location.assign(SITEURL + ADMIN + '/media/explore/' + $(this).val());
		} else {
			window.location.assign(SITEURL + ADMIN + '/media/');
		}
	});

	$('.folder-thumb').dblclick(function(){
		window.location.assign(SITEURL + ADMIN + '/media/explore/' + $(this).attr('data-id'));
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

	$('.thumb-body').bind('click', function(e){

		var parent = $(this).parent();

		if (! $(parent).hasClass('active-wrap')) {
			$(parent).addClass('active-wrap');

			selected.push($(parent).attr('data-id'));

			if (selected.length > 1) {
				$('#media_main_action').show();
			}
		} else {
			$(parent).removeClass('active-wrap');

			var indexer = selected.indexOf($(parent).attr('data-id'));

			selected.splice(indexer, 1);

			if (selected.length <= 1) {
				$('#media_main_action').hide();
			}
		}

		$(parent).parent().addClass('clicked');
	});

	$(document).on('click', function(e) {

		var clicked = $(e.target);
		if (! clicked.parents().hasClass("clicked")){
			$('.thumb-body').parent().removeClass('active-wrap');
			selected = [];
		}

	});

	$('#media_main_delete').bind('click', function(){
		var del = confirm("Are you sure you want to delete selected items ?");

		if (del) {

			for(var i = 0; i < selected.length; i++) {
				$.ajax({
					url: SITEURL + ADMIN + '/media/delete-file/' + selected[i]
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

	/* ===== Drag and Drop ===== */
	$('.draggable').draggable({
		revert: true,
		zIndex: 100,
		start: function(event, ui) {
			var mPosX = event.pageX - $(this).offset().left;
			var mPosY = event.pageY - $(this).offset().top;

			$(this).css('margin-top', mPosY);
			$(this).css('margin-left', mPosX);
			$(this).switchClass('ff-wrapper', 'small');
			
			$(this).bind('mouseleave', function(){ $(this).switchClass('small', 'ff-wrapper') });
		}
	});

	$('.droppable').droppable({
		drop: function(event, ui) {
			ui.draggable.hide();
		}
	});

	/* ===== Upload ===== */
	$('#uploaded-files').livequery(function(){
		$(this).perfectScrollbar();
	});
});