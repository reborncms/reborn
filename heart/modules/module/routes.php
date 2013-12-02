<?php

// Route file for module Module

Route::get('@admin/module', 'Module\Admin\Module::index', 'module_index');

Route::get('@admin/module/install/{str:name}',
			'Module\Admin\Module::install', 'module_install');

Route::get('@admin/module/uninstall/{str:name}',
			'Module\Admin\Module::uninstall', 'module_uninstall');

Route::get('@admin/module/upgrade/{str:name}',
			'Module\Admin\Module::upgrade', 'module_upgrade');

Route::get('@admin/module/enable/{str:name}',
			'Module\Admin\Module::enable', 'module_enable');

Route::get('@admin/module/disable/{str:name}',
			'Module\Admin\Module::disable', 'module_disable');

Route::get('@admin/module/delete/{str:name}',
			'Module\Admin\Module::delete', 'module_delete');

Route::add('@admin/module/upload', 'Module\Admin\Module::upload', 'module_upload');
