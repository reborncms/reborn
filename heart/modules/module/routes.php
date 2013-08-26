<?php

// Route file for module Module

$adminUrl = \Config::get('app.adminpanel');

Route::add('module_index', $adminUrl.'/module', 'Module\Admin\Module::index');

Route::add('module_install',
			$adminUrl.'/module/install/{alnum:name}',
			'Module\Admin\Module::install');

Route::add('module_uninstall',
			$adminUrl.'/module/uninstall/{alnum:name}',
			'Module\Admin\Module::uninstall');

Route::add('module_enable',
			$adminUrl.'/module/enable/{alnum:name}',
			'Module\Admin\Module::enable');

Route::add('module_disable',
			$adminUrl.'/module/disable/{alnum:name}',
			'Module\Admin\Module::disable');

Route::add('module_delete',
			$adminUrl.'/module/delete/{alnum:name}',
			'Module\Admin\Module::delete');
