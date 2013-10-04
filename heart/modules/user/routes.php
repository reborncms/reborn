<?php

Route::get('user/profile/{int:id}', 'User\User::profile', 'user_profile');
Route::add('@admin/user/edit/{int:uri}/', 'User\Admin\User::edit', 'user_edit');
Route::get('@admin/user/delete/{int:uri}', 'User\Admin\User::delete', 'user_delete');
Route::get('@admin/user/{p:usrpagi}?', 'User\Admin\User::index', 'user_index');

Route::add('@admin/user/group/edit/{int:uri}/', 'User\Admin\Group::edit', 'group_edit');
Route::get('@admin/user/group/delete/{int:uri}', 'User\Admin\Group::delete', 'group_delete');

Route::add('@admin/user/permission/edit/{int:groupid}', 'User\Admin\Permission::edit', 'permission_edit');