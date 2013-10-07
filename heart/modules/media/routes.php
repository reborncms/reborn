<?php

	\Route::get('media/image/{:target}/{int:width}?/{int:height}?',
		'Media\Media::image', 'image_preview_with_name');
