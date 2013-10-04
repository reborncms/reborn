<?php

// Route file for module Widgets

\Route::add('@admin/widgets', 'Widgets\Admin\Widgets::index', 'admin_widgets_index');

\Route::add('@admin/widgets/add', 'Widgets\Admin\Widgets::add', 'admin_widgets_add');

\Route::add('@admin/widgets/order', 'Widgets\Admin\Widgets::order', 'admin_widgets_order');

\Route::add('@admin/widgets/remove/{int:id}', 'Widgets\Admin\Widgets::remove', 'admin_widgets_remove');

\Route::add('@admin/widgets/settings/{*:name}/{int:id}', 'Widgets\Admin\Widgets::settings', 'admin_widgets_settings');

\Route::add('@admin/widgets/has-options', 'Widgets\Admin\Widgets::hasOptions', 'admin_widgets_option');

\Route::add('@admin/widgets/move-area/{int:id}', 'Widgets\Admin\Widgets::moveArea', 'admin_widgets_moveArea');

\Route::post('@admin/widgets/options-save', 'Widgets\Admin\Widgets::optionsSave', 'admin_widgets_optionsSave');
