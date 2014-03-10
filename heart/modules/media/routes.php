<?php

    /* ===== File ===== */
    // File upload
    \Route::add(
            '@admin/media/upload/{int:folderId}?/{str:key}?',
            'Media\Admin\File::upload',
            'file_upload'
        )->method(array('GET', 'POST'));

    // Edit file
    \Route::add(
            '@admin/media/file/update/{int:id}',
            'Media\Admin\File::update',
            'edit_file'
        );

    \Route::get(
            '@admin/media',
            'Media\Admin\Media::index',
            'media'
        );

    // Setting thumbnail
    \Route::get(
            '@admin/media/thumbnail/{int:folderId}?',
            'Media\Admin\Media::thumbnail',
            'thumbnail'
        )->defaults(array('folderId' => 0));

    // File Delete
    \Route::add(
            '@admin/media/delete-file/{int:id}/{int:redirect}?',
            'Media\Admin\Media::deleteFile',
            'file_delete'
        );

/* ===== Folder ===== */
    // Folder create
    \Route::add(
            '@admin/media/folder/create/{int:folderId}?',
            'Media\Admin\Folder::create',
            'folder_create'
        )->method(array('GET', 'POST'));

    // Edit Folder
    \Route::add(
            '@admin/media/folder/update/{int:id}?',
            'Media\Admin\Folder::update',
            'update_folder'
        );

    // Folder Delete
    \Route::add(
            '@admin/media/folder/delete/{int:id}',
            'Media\Admin\Folder::delete',
            'folder_delete'
        );

    // Explore folders
    \Route::get(
            '@admin/media/explore/{int:id}',
            'Media\Admin\Media::explore',
            'explorer'
        );

    // wysiwyg
    \Route::get(
            '@admin/media/wysiwyg/{int:folderId}?',
            'Media\Admin\Media::wysiwyg',
            'wysiwyg'
        );

/* ===== Frontend ===== */
    // Viewing images
    \Route::get(
            'media/image/{:target}/{int:width}?/{int:height}?',
            'Media\Media::image',
            'image_preview'
        );

    \Route::get(
            'media/download/{int:id}',
            'Media\Media::download',
            'file_download'
        );

/* ===== TESTING ===== */
\Route::add('@admin/media/file/testing', 'Media\Admin\File::testing');
