<?php

	// Viewing images
	\Route::get(
			'media/image/{:target}/{int:width}?/{int:height}?',
			'Media\Media::image', 
			'image_preview_with_name'
		);

	// Setting thumbnail
	\Route::get(
			'@admin/media/thumbnail/{int:folderId}',
			'Media\Admin\Media::thumbnail', 
			'thumbnail'
		);

	// File upload
	\Route::add(
			'@admin/media/upload/{int:folderId}?/{str:key}?',
			'Media\Admin\Media::upload',
			'file_upload'
		)->method(array('GET', 'POST'));

	// Folder Delete
	\Route::delete(
			'@admin/media/delete-folder/{int:id}',
			'Media\Admin\Media::deleteFolder', 
			'folder_delete'
		);

	// File Delete
	\Route::delete(
			'@admin/media/delete-file/{int:id}/{int:redirect}?',
			'Media\Admin\Media::deleteFile',
			'file_delete'
		);

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

	// Edit Folder
	\Route::add(
			'@admin/media/edit-folder/{int:id}',
			'Media\Admin\Media::editFolder',
			'edit_folder'
		);

	// Edit file
	\Route::add(
			'@admin/media/edit-file/{int:id}',
			'Media\Admin\Media::editFile',
			'edit_file'
		);

	// wysiwyg
	\Route::get(
			'@admin/media/wysiwyg/{int:folderId}?',
			'Media\Admin\Media::wysiwyg',
			'wysiwyg'
		);
