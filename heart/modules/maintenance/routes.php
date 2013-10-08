<?php

// Route file for module Maintenance

\Route::add('@admin/maintenance', 'Maintenance\Admin\Maintenance::index', 'admin_maintenance_index');

\Route::add('@admin/maintenance/clear/{str:folder_name}/{str:child}?', 'Maintenance\Admin\Maintenance::clear', 'admin_maintenance_clear_caches');