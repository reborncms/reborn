<?php

Route::group('api/v2/user', function(){

	Route::get('', 'User\Api::getProfile', 'user.api.profile');

	Route::post('/', 'User\Api::create', 'user.api.create')->csrf();

	Route::post('verify', 'User\Api::activate', 'user.api.activate')->csrf();

    Route::post('login', 'User\Api::login', 'user.api.login')->csrf();

    Route::post('logout', 'User\Api::logout', 'user.api.logout')->csrf();

});