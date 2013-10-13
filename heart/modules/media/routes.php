<?php

	// Viewing images
	\Route::get('media/image/{:target}/{int:width}?/{int:height}?',
		'Media\Media::image', 'image_preview_with_name');

	// Setting thumbnail
	\Route::get('admin/media/thumbnail/{int:folderId}/{alpha:target}?',
		'Media\Admin\Media::thumbnail', 'thumbnail');

	// File upload
	\Route::post('admin/media/upload/{int:folderid}?/{string:key}?',
		'Media\Admin\Media::upload', 'file_upload');

	// Folder Delete
	\Route::delete('admin/media/delete-folder/{int:id}',
		'Media\Admin\Media::deleteFolder', 'folder_delete');

	// File Delete
	\Route::delete('admin/media/delete-file/{int:id}',
		'Media\Admin\Media::deleteFile', 'file_delete');

	\Route::post('admin/media/create-folder/{int:folderId}?',
		'Media\Admin\Media::createFolder', 'folder_create');
