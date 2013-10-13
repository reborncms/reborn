<?php

// Route file for module Field

Route::get('@admin/field', 'Field\Admin\Field::index', 'field_index');

Route::add('@admin/field/create', 'Field\Admin\Field::create', 'field_create');

Route::add('@admin/field/edit/{int:id}', 'Field\Admin\Field::edit', 'field_edit');

Route::get('@admin/field/delete/{int:id}', 'Field\Admin\Field::delete', 'field_delete');

Route::add('@admin/field/get-type-display/{str:type}', 'Field\Admin\Field::getTypeDisplay', 'field_get_type_form');

Route::get('@admin/field/group', 'Field\Admin\Field::group', 'field_group_index');

Route::add('@admin/field/group-create', 'Field\Admin\Field::groupCreate', 'field_group_create');

Route::add('@admin/field/group-edit/{int:id}', 'Field\Admin\Field::groupEdit', 'field_group_edit');

Route::get('@admin/field/group-delete/{int:id}', 'Field\Admin\Field::groupDelete', 'field_group_delete');
