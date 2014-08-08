<?php

Route::group('user',function () {
    Route::add('', 'User\User::index', 'user');
    Route::get('profile/{int:id}', 'User\User::profile', 'user_profile');
    Route::add('edit/', 'User\User::edit', 'user_profile_edit');
    Route::add('change-password', 'User\User::changePassword', 'user_change_password');
    Route::add('logout', 'User\User::logout', 'user_logout');
    Route::add('login', 'User\User::login', 'user_login');
    Route::add('register', 'User\User::register', 'user_register');
    Route::get('activate/{string:emailEncode}/{string:activationCode}', 'User\User::activate', 'user_activate');
    Route::add('reset-password', 'User\User::resetPassword', 'user_password_reset');
    Route::add('password-reset/{string:emailEncode}/{string:hash}', 'User\User::passwordReset', 'user_reset_password');
    Route::add('resend', 'User\User::resend', 'user_password_resend');
});

Route::group('@admin/user',function () {
    Route::get('{p:page}?', 'User\Admin\User::index', 'user_admin');
    Route::add('create', 'User\Admin\User::create', 'user_create');
    Route::add('edit/{int:uri}/', 'User\Admin\User::edit', 'user_edit');
    Route::get('delete/{int:uri}', 'User\Admin\User::delete', 'user_delete');
    Route::get('activate/{int:id}?', 'User\Admin\User::activate', 'user_admin_activate');
});

Route::get('@admin/user/group/', 'User\Admin\Group::index', 'group');
Route::add('@admin/user/group/create', 'User\Admin\Group::create', 'group_create');
Route::add('@admin/user/group/edit/{int:uri}/', 'User\Admin\Group::edit', 'group_edit');
Route::get('@admin/user/group/delete/{int:uri}', 'User\Admin\Group::delete', 'group_delete');

Route::get('@admin/user/permission/', 'User\Admin\Permission::index', 'psermission');
Route::add('@admin/user/permission/edit/{int:groupid}', 'User\Admin\Permission::edit', 'permission_edit');
