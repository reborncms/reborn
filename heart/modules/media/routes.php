<?php
	$adminUrl = \Config::get('app.adminpanel');

	Route::add('editFile', $adminUrl.'/media/edit/file/{:int}', 
		'Media\Admin\Media::editFile');

	Route::add('editFolder', $adminUrl.'/media/edit/folder/{:int}',
		'Media\Admin\Media::editFolder');

	Route::add('seeFile', '/file/{:alnum}', 'Media\Media::image');
