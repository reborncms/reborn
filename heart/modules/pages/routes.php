<?php

// Route file for module Pages

// --- Back end --- //

\Route::get('@admin/pages', 'Pages\Admin\Pages::index', 'admin_pages_index');

\Route::add('@admin/pages/create', 'Pages\Admin\Pages::create', 'admin_pages_create');

\Route::add('@admin/pages/edit/{int:id}?', 'Pages\Admin\Pages::edit', 'admin_pages_edit');

\Route::add('@admin/pages/duplicate/{int:id}?', 'Pages\Admin\Pages::duplicate', 'admin_pages_duplicate');

\Route::post('@admin/pages/autosave', 'Pages\Admin\Pages::autosave', 'admin_pages_autosave');

\Route::add('@admin/pages/delete/{int:id}', 'Pages\Admin\Pages::delete', 'admin_pages_delete');

\Route::add('@admin/pages/status/{int:id}', 'Pages\Admin\Pages::status', 'admin_pages_status');

\Route::post('@admin/pages/check-slug', 'Pages\Admin\Pages::checkSlug', 'admin_pages_checkslug');

\Route::post('@admin/pages/order', 'Pages\Admin\Pages::order', 'admin_pages_order');

// --- Front End -- //

\Route::add('pages/preview/{*:slug}', 'Pages\Pages::preview', 'pages_preview');

\Route::add('{*:slug}', 'Pages\Pages::index', 'pages_view');
