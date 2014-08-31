<?php

    // Api route for media module
    Route::group('api/v1/media', function()
    {
        $ctrl = 'Media\Api\V1';

        // Get all files or specific file
        Route::get('files/{int:id}?', $ctrl . '::files', 'media.api.files');

        // Get files by folder id
        Route::get('files-by-folder/{int:folderId}', $ctrl . '::filesByFolder',
            'media.api.files_by_folder');

        // Get all images
        Route::get('images', $ctrl . '::images', 'media.api.images');

        // Get images by folder id
        Route::get('images-by-folder/{int:folderId}', $ctrl . '::imagesByFolder',
            'media.api.images_by_folder');

        // Get all folders or specific folder
        Route::get('folders/{int:id}?', $ctrl . '::folders', 'media.api.folders');

        // Get folders by folder id
        Route::get('folders-by-folder/{int:folderId}', $ctrl . '::foldersByFolder',
            'media.api.folders_by_folder');

        // Get all files and folders
        Route::get('files-and-folders', $ctrl . '::filesAndFolders',
            'media.api.files-and-folders');

        // Get files and folders by specific folder id
        Route::get('files-and-folders-by-folder', $ctrl . '::filesAndFoldersByFolder',
            'media.api.files_and_folders_by_folder');
    });
