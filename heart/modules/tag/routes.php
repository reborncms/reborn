<?php

// Route file for module Tag

\Route::get('@admin/tag/{p:page}?', 'Tag\Admin\Tag::index', 'admin_tag_index');

\Route::add('@admin/tag/create', 'Tag\Admin\Tag::create', 'admin_tag_create');

\Route::add('@admin/tag/edit/{int:id}?', 'Tag\Admin\Tag::edit', 'admin_tag_edit');

\Route::add('@admin/tag/delete/{int:id}?', 'Tag\Admin\Tag::delete', 'admin_tag_delete');

\Route::get('@admin/tag/autocomplete', 'Tag\Admin\Tag::autocomplete', 'admin_tag_autocomplete');

