<?php

// === Route file for module Blog ==== //

// --- Back end --- //

//Blog

\Route::group('@admin/blog', function () {

    \Route::add('{p:page}?', 'Blog\Admin\Blog::index', 'admin_blog_index');

    \Route::add('create', 'Blog\Admin\Blog::create', 'admin_blog_create');

    \Route::add('edit/{int:id}?', 'Blog\Admin\Blog::edit', 'admin_blog_edit');

    \Route::add('multilang/{int:id}?', 'Blog\Admin\Blog::multilang', 'admin_blog_multiLang');

    \Route::add('change-status/{int:id}', 'Blog\Admin\Blog::changeStatus', 'admin_blog_changeStatus');

    \Route::add('publish/{int:id}', 'Blog\Admin\Blog::publish', 'admin_blog_publish_now');

    \Route::add('delete/{int:id}?', 'Blog\Admin\Blog::delete', 'admin_blog_delete');

    \Route::add('check-slug', 'Blog\Admin\Blog::checkSlug', 'admin_blog_checkSlug');

    \Route::post('search', 'Blog\Admin\Blog::search', 'admin_blog_search');

    \Route::post('autosave', 'Blog\Admin\Blog::autosave', 'admin_blog_autosave');

    \Route::add('trash/{p:page}?', 'Blog\Admin\Blog::trash', 'admin_blog_trash');

    \Route::add('restore/{int:id}', 'Blog\Admin\Blog::restore', 'admin_blog_restore');

    \Route::add('post-links/{int:id}?/{p:page}?', 'Blog\Admin\Blog::postLinks', 'post_links_for_editor');

    \Route::post('search-links/{p:page}?', 'Blog\Admin\Blog::searchLists', 'search_links_for_editor');

    //Blog Category

    \Route::group('category', function () {

        \Route::add('', 'Blog\Admin\Category::index', 'admin_blog_category_index');

        \Route::add('create', 'Blog\Admin\Category::create', 'admin_blog_category_create');

        \Route::add('edit/{int:id}?', 'Blog\Admin\Category::edit', 'admin_blog_category_edit');

        \Route::add('delete/{int:id}', 'Blog\Admin\Category::delete', 'admin_blog_category_delete');

        \Route::post('order', 'Blog\Admin\Category::order', 'admin_blog_category_order');

        \Route::add('getCategory/{int:selected}?', 'Blog\Admin\Category::getCategory', 'admin_blog_get_category');
    });

});

// --- Front end --- //

\Route::group('blog', function () {

    \Route::add('rss', 'Blog\Blog::rss', 'rss_feed');

    \Route::get('archives/{int:year}?/{int:month}?/{p:page}?', 'Blog\Blog::archives', 'blog_archves')
                ->defaults(array('year' => date("Y")));

    \Route::get('preview/{*:slug}', 'Blog\Blog::preview', 'blog_preview');

    \Route::get('category/{*:slug}/{p:page}?', 'Blog\Blog::category', 'blog_category');

    \Route::get('tag/{*:name}/{p:page}?', 'Blog\Blog::tag', 'blog_tag');

    \Route::get('author/{int:id}/{p:page}?', 'Blog\Blog::author', 'blog_author');

    \Route::get('{p:page}?', 'Blog\Blog::index', 'blog_index');

    \Route::get('{*:slug}/comments/{p:page}?', 'Blog\Blog::view', 'blog_single_with_comment');

    // Now change {str:slug} to {*:slug} for Myanmar Font Uri.
    // Thuesday, 8 October 2013
    \Route::get('{*:slug}', 'Blog\Blog::view', 'blog_single');

});
