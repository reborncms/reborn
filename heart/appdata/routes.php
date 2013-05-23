<?php

$adminUrl = \Config::get('app.adminpanel');

// This is default route for Reborn
// Don't delete this.
$defaultModule = \Setting::get('default_module');
if ('pages' == strtolower($defaultModule)) {
	Route::add('default', '/', 'Pages\Pages::index');
} else {
	Route::add('default', '/', $defaultModule.'\\'.$defaultModule.'::index');
}

// Add Page Not Found Route
// Don't change this route
// Route::addNotFound(module, controller, action)
/*Route::addNotFound(function($params){
	echo '<h1>Hello Closure 404</h1>';
	echo '<h3>Sorry! Request URL "'.$params.'" not found in this site.</h3>';
});*/
Route::addNotFound('Pages', 'Pages', 'index');

Route::add('login', 'login', 'User\User::login');
Route::add('register', 'register', 'User\User::register');

// Admin Panel Login, Logout, Dashboard Route
Route::add('admin_login', $adminUrl.'/login', 'Admin\Admin\Admin::login');
Route::add('admin_logout', $adminUrl.'/logout', 'Admin\Admin\Admin::logout');
Route::add('admin_dashboard', $adminUrl, 'Admin\Admin\Admin::index');
