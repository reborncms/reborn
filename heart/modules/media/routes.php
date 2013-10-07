<?php

	// Viewing images
	\Route::get('media/image/{:target}/{int:width}?/{int:height}?',
		'Media\Media::image', 'image_preview_with_name');

	// Setting thumbnail
	\Route::get('admin/media/thumbnail/{int:folderId}/{alpha:target}?',
		'Media\Admin\Media::thumbnail', 'thumbnail');
