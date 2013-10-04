<?php

// Route file for module Module

Route::get('@admin/module', 'Module\Admin\Module::index', 'module_index');

Route::get('@admin/module/install/{alnum:name}/{alnum:uri}',
			'Module\Admin\Module::install', 'module_install');

Route::get('@admin/module/uninstall/{alnum:name}/{alnum:uri}',
			'Module\Admin\Module::uninstall', 'module_uninstall');

Route::get('@admin/module/upgrade/{alnum:name}/{alnum:uri}',
			'Module\Admin\Module::upgrade', 'module_upgrade');

Route::get('@admin/module/enable/{alnum:name}/{alnum:uri}',
			'Module\Admin\Module::enable', 'module_enable');

Route::get('@admin/module/disable/{alnum:name}/{alnum:uri}',
			'Module\Admin\Module::disable', 'module_disable');

Route::get('@admin/module/delete/{alnum:name}/{alnum:uri}',
			'Module\Admin\Module::delete', 'module_delete');

Route::add('@admin/module/upload', 'Module\Admin\Module::upload', 'module_upload');
