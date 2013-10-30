<?php

// This is default route for Reborn
// Don't delete this.
$defaultModule = \Setting::get('default_module');
if ('pages' == strtolower($defaultModule)) {
	Route::add('/', 'Pages\Pages::index', 'default');
} else {
	Route::add('/', ucfirst($defaultModule).'\\'.ucfirst($defaultModule).'::index', 'default');
}

Route::add('login', 'User\User::login', 'login');
Route::add('register', 'User\User::register', 'register');

// Viewing images
Route::get('image/{:target}/{int:width}?/{int:height}?',
			'Media\Media::image',
			'image_preview'
		);

// File Download Route
Route::get('download/{int:id}', 'Media\Media::download', 'file_download');

// Admin Panel Login, Logout, Dashboard Route
Route::add('@admin/login', 'Admin\Admin\Admin::login', 'admin_login');
Route::add('@admin/logout', 'Admin\Admin\Admin::logout', 'admin_logout');
Route::add('@admin', 'Admin\Admin\Admin::index', 'admin_dashboard');
Route::add('@admin/language', 'Admin\Admin\Admin::language', 'admin_language');

// Add Missing Control Route
Route::missing('Pages\Pages::view', 'missing', 'ALL', '{*:uri}');
