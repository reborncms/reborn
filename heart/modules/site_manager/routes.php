<?php

// Route file for module Site Manager

Route::get('@admin/site', 'SiteManager\Admin\SiteManager::index');

Route::add('@admin/site/create', 'SiteManager\Admin\SiteManager::create');

Route::add('@admin/site/edit/{int:id}', 'SiteManager\Admin\SiteManager::edit');

Route::add('@admin/site/delete/{int:id}', 'SiteManager\Admin\SiteManager::delete');
