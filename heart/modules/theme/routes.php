<?php

Route::get('@admin/theme/', 'Theme\Admin\Theme::index', 'theme_index');
Route::get('@admin/theme/activate/{alpha:name}/', 'Theme\Admin\Theme::activate', 'theme_activate');
Route::get('@admin/theme/delete/{alpha:name}/', 'Theme\Admin\Theme::delete', 'theme_delete');
Route::add('@admin/theme/upload/', 'Theme\Admin\Theme::upload', 'theme_upload');

Route::get('@admin/theme/editor/', 'Theme\Admin\Editor::index', 'theme_editor');
Route::add('@admin/theme/editor/edit/{alpha:ext}/{alpha:file}', 'Theme\Admin\Editor::edit', 'editor_edit');