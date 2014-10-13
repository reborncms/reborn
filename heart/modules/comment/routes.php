<?php

// ==== Route File for Comment Module ==== //

// --- Back End --- //

\Route::add('@admin/comment/{p:page}?', 'Comment\Admin\Comment::index', 'admin_comment_index');

\Route::get('@admin/comment/filter/{alpha:status}/{p:page}?', 'Comment\Admin\Comment::filter', 'admin_comment_filter');

\Route::get('@admin/comment/change-status/{int:id}/{alpha:status}?', 'Comment\Admin\Comment::changeStatus', 'admin_comment_changeStatus');

\Route::add('@admin/comment/reply/{int:id}', 'Comment\Admin\Comment::reply', 'admin_comment_reply');

\Route::add('@admin/comment/edit/{int:id}', 'Comment\Admin\Comment::edit', 'admin_comment_edit');

\Route::post('@admin/comment/multiaction', 'Comment\Admin\Comment::multiaction', 'admin_comment_multiaction');

\Route::add('@admin/comment/delete/{int:id}?', 'Comment\Admin\Comment::delete', 'admin_comment_delete');

\Route::add('@admin/comment/restore/{int:id}', 'Comment\Admin\Comment::restore', 'admin_comment_restore');

// -- Front End -- //

\Route::add('comment/show/{int:content_id}/{alpha:module}/{alpha:status}', 'Comment\Comment::show', 'comment_show');

\Route::post('comment/post', 'Comment\Comment::post', 'comment_post');
