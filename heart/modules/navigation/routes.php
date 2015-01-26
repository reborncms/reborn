<?php

// Route file for module Navigation

Route::get('@admin/navigation', 'Navigation\Admin\Navigation::index', 'nav');

Route::add('@admin/navigation/create', 'Navigation\Admin\Navigation::create', 'nav_create');

Route::post('@admin/navigation/order',
			'Navigation\Admin\Navigation::order', 'nav_order');

Route::add('@admin/navigation/edit/{int:id}',
			'Navigation\Admin\Navigation::edit', 'nav_edit');

Route::add('@admin/navigation/delete/{int:id}',
			'Navigation\Admin\Navigation::delete', 'nav_delete');

Route::add('@admin/navigation/group',
			'Navigation\Admin\Navigation::group', 'nav_group');

Route::add('@admin/navigation/group-create',
			'Navigation\Admin\Navigation::groupCreate', 'nav_group_create');
