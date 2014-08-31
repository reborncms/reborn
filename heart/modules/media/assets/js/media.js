'use stripe';

$(function(){
	$('.ff-mix-container').perfectScrollbar();

	if (!RB.hasFolder) {
		$('#media_upload').addClass('btn-disabled');
		$('#media_upload').on('click', function(e) { e.preventDefault(); });
	} else {
		$('#media_upload').colorbox({
			width: "900",
			innerHeight: "360",
			closeButton: false,
			href: RB.adminMedia + '/upload/' + RB.currentFolder,
			onComplete: function() {
				$('#cboxClose').hide();
			},
			onClosed: function() {
				window.location.reload();
			}
		});
	}

	$('#media_create_folder').on('click', function(e){
		e.preventDefault();

		if ($(this).hasClass('btn-disabled')) { return; }

		var value = {
			'name': 'RbFolder', 
			'folder_id': RB.currentFolder,
			'description': ''
		};

		var that = $(this);

		var text = that.text();

		$.ajax({
			type: 'POST',
			url: SITEURL + ADMIN + '/media/folder/create/' + RB.currentFolder,
			data: value,
			beforeSend: function() {
				that.addClass('btn-disabled');
				that.text('Loading ...');
			},
			success: function(result) {

				if ('fail' == result.status) {

				} else {
					var data = result.data;

					var folder = "<div class='ff-wrapper droppable f-left folder' id='folder_'"+ result.data['id'] +" data-id='"+result.data['id']+"'>";

						folder += "<div class='ff-actions p-relative'>";
						folder += "<div class='btns-group p-absolute'>";
						folder += "<a href='#folder-detail' class='action-detail btn btn-ico btn-dark' title='detail' data-target='folder' data-name='"+data['name']+"' data-desc='"+data['description']+"' data-folder='"+data['parent']+"' data-user='"+data['user']+"'>";
						folder += "<i class='icon-view'></i></a>";
						folder += "<a class='action-option btn btn-ico btn-dark' title='options'><i class='icon-arrow-down'></i></a></div>";
						folder += "<ul class='options p-absolute'>";
						folder += "<li><a class='action_edits' href='"+SITEURL + ADMIN + '/media/folder/update/'+ data['id'] + "'>Edit</a></li>";
						folder += "<li><a href='#'>Move</a></li>";
						folder += "<li><a class='confirm_delete' href='"+SITEURL+ADMIN+'/media/folder/delete/'+data['id']+"'>Delete</a></li>";
						folder += "</ul></div>";

						folder += "<div class='thumb-body' data-target='folder'>";
						folder += "<div data-id='"+data['id']+"' class='thumbs folder-thumb' title='"+data['name']+"'></div>";
						folder += "</div><div class='ff-name'><p>"+data['name']+"</p></div></div>";

					$('#folder-wrapper').append(folder);
				}

				that.text(text);
				that.removeClass('btn-disabled');
			}
		});

	});

	$('#folder_submit').livequery('click', function(e){
		e.preventDefault();

		var name = $('#name').val();
		var folder = $('#folder_id').val();

		$.ajax({
			type: 'POST',
			url: $('.form').attr('action'),
			data: $('.form').serialize(),
			success: function(result) {
				if ('success' == result.status) {
					$.colorbox.close();
					window.location.reload();
				}
			}
		});
	});
/* ===== Files and Folders actions ===== */

	$('.ff-wrapper').on('mouseleave', function() {
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

	var selected = {'file':[], 'folder':[]};

	$('.thumb-body').on('click', function(e){

		var parent = $(this).parent();

		if (! $(parent).hasClass('active-wrap')) {
			parent.addClass('active-wrap');

			('file' == $(this).attr('data-target')) ? selected.file.push($(this)) : selected.folder.push($(this));

			if (selected.file.length+selected.folder.length > 1) {
				$('#media_main_action').show();
			}

		} else {
			parent.removeClass('active-wrap');

			if ('file' == parent.attr('data-target')) {
				var indexer = selected.file.indexOf($(this));
				selected.file.splice(indexer, 1);
			} else {
				var indexer = selected.folder.indexOf($(this));
				selected.folder.splice(indexer, 1);
			}

			if (selected.file.length+selected.folder.length <= 1) {
				$('#media_main_action').hide();
			}
		}

		

		$(parent).parent().addClass('clicked');

		var datas = $(this).parent().find('.action-detail');

		status(datas);
	});

	function status(data) {

		if (0 != selected.file.length && 0 != selected.folder.length) {
			$('#m-statuses p').text(selected.folder.length+' folders and '+selected.file.length+' files have been selected.');
		} else if (0 != selected.file.length) {
			$('#m-statuses p').text(selected.file.length+' files have been selected.');
		} else if (0 != selected.folder.length) {
			$('#m-statuses p').text(selected.folder.length+' folders have been selected.');
		}

		if ('file' == data.attr('data-target')) {
			$('#status_file_name td:last-child').text(data.attr('data-name'));
			$('#status_file_name').show();
			$('#status_folder_name').hide();

			$('#status_desc td:last-child').text(data.attr('data-desc'));

			$('#status_dimension td:last-child').text(data.attr('data-width')+' X '+data.attr('data-height'));
			$('#status_dimension').show();

			$('#status_size td:last-child').text(data.attr('data-size'));
			$('#status_size').show();

			$('#status_folder_auth').hide();
			$('#status_file_auth td:last-child').text(data.attr('data-user'));
			$('#status_file_auth').show();
		} else {
			$('#m-status-desc table tr').hide();

			$('#status_folder_name td:last-child').text(data.attr('data-name'));
			$('#status_folder_name').show();

			$('#status_desc td:last-child').text(data.attr('data-desc'));
			$('#status_desc').show();

			$('#status_folder_auth td:last-child').text(data.attr('data-user'));
			$('#status_folder_auth').show();
		}
	}

	$(document).on('click', function(e) {

		var clicked = $(e.target);
		if (! clicked.parents().hasClass("clicked")){
			$('.thumb-body').parent().removeClass('active-wrap');
			$('#media_main_action').hide();
			selected = { 'file':[], 'folder':[] };

			$('#m-statuses p').text($('#m-statuses').attr('data-origin'));

			$('#m-status-desc table tr').hide();

			$('#status_folder_name td:last-child').text($('#status_folder_name').attr('data-origin'));
			$('#status_folder_name').show();

			$('#status_desc td:last-child').text($('#status_desc').attr('data-origin'));
			$('#status_desc').show();

			$('#status_folder_auth td:last-child').text($('#status_file_auth').attr('data-origin'));
			$('#status_folder_auth').show();

		}

	});

	$('#media_main_delete').on('click', function(){

		var del = confirm("Are you sure you want to delete selected items ?");

		if (del) {

			for (var i = 0; i < selected.file.length; i++) {
				var theFile = selected.file[i].parent();

				$('#file_'+theFile.attr('data-id')).remove();

				$.ajax({
					url: SITEURL + ADMIN + '/media/delete-file/' + theFile.attr('data-id')
				});
			}

			for(var j = 0; j < selected.folder.length; j++) {
				var theFolder = selected.folder[j].parent();

				$('#folder_'+theFolder.attr('data-id')).remove();

				$.ajax({
					url: SITEURL + ADMIN + '/media/delete-folder/' + theFolder.attr('data-id')
				});
			}
		}
	});

	$('.action-option').livequery('click', function(){
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

				$('#detail-file-img').css("background", "url("+$(this).attr('data-link')+"/0/180/) center no-repeat");
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
	/*$('.draggable').draggable({
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
	});*/

	/* ===== Upload ===== */
	$('#uploaded-files').livequery(function(){
		$(this).perfectScrollbar();
	});
	

	/* ===== Main Actions ===== */
	$('#media_search_btn').on('click', function(e){
		e.preventDefault();

		$('#jump-box').fadeOut();

		$('#search-box').fadeToggle();
	});

	$('#media_jump_btn').on('click', function(e){
		e.preventDefault();

		$('#search-box').fadeOut();

		$('#jump-box').fadeToggle();
	});

	/* ===== Feature Image ===== */
	

	$('#extra-li a').removeClass('ui-tabs-anchor');

	$('#jumper').chosen({ "width" : '90%' }).change(function() {
		$('#m-thumb-body').load(SITEURL + ADMIN + '/media/thumbnail/' + $(this).val() + 'wysiwyg #ajax_wrap');
	});

	$('#link_ok_btn').on('click', function(){
		var value = $('#external_link').val().trim();

		if (!value) {
			alert('Please insert link');
		}

		var image = "<img id='link-preview-img' onLoad='javascript:linkImg();' src='"+value+"'/>";

		$('#link-preview-img').remove();

		$(image).insertBefore($('#link-prev-img-wrap .btn'));
	});
});
