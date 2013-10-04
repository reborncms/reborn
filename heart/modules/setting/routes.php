<?php

// Route file for module Setting

Route::get('@admin/setting/system', 'Setting\Admin\Setting::system', 'setting_system');

Route::get('@admin/setting/module/{str:name}', 'Setting\Admin\Setting::module', 'setting_module');

Route::post('@admin/setting/save/{str:type}', 'Setting\Admin\Setting::save', 'setting_save')
		->defaults(array('type' => 'system'));
