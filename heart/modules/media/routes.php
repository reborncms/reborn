<?php

	// Viewing images
	\Route::get('media/image/{:target}/{int:width}?/{int:height}?',
		'Media\Media::image', 'image_preview_with_name');

	// Setting thumbnail
	\Route::get('@admin/media/thumbnail/{int:folderId}/{alpha:target}?',
		'Media\Admin\Media::thumbnail', 'thumbnail');

	// File upload
	\Route::post('@admin/media/upload/{int:folderid}?/{str:key}?',
		'Media\Admin\Media::upload', 'file_upload');

	// Folder Delete
	\Route::add('@admin/media/delete-folder/{int:id}',
		'Media\Admin\Media::deleteFolder', 'folder_delete');

	// File Delete
	\Route::add('@admin/media/delete-file/{int:id}/{int:redirect}?',
		'Media\Admin\Media::deleteFile', 'file_delete');

	// Folder create
	\Route::add(
			'@admin/media/create-folder/{int:folderId}?',
			'Media\Admin\Media::createFolder', 
			'folder_create'
		)->method(array('GET', 'POST'));

	// Explore folders
	\Route::get(
			'@admin/media/explore/{int:id}',
			'Media\Admin\Media::explore',
			'explorer'
		);

	\Route::add('@admin/media/edit-folder/{int:id}',
		'Media\Admin\Media::editFolder', 'edit_folder');

	\Route::add('@admin/media/edit-file/{int:id}',
		'Media\Admin\Media::editFile', 'edit_file');
