<?php

// === Route file for module Blog ==== //

// --- Back end --- //

	//Blog

\Route::add('@admin/blog/{p:page}?', 'Blog\Admin\Blog::index', 'admin_blog_index');

\Route::add('@admin/blog/create', 'Blog\Admin\Blog::create', 'admin_blog_create');

\Route::add('@admin/blog/edit/{int:id}?', 'Blog\Admin\Blog::edit', 'admin_blog_edit');

\Route::add('@admin/blog/multilang/{int:id}?', 'Blog\Admin\Blog::multilang', 'admin_blog_multiLang');

\Route::add('@admin/blog/change-status/{int:id}', 'Blog\Admin\Blog::changeStatus', 'admin_blog_changeStatus');

\Route::add('@admin/blog/delete/{int:id}?', 'Blog\Admin\Blog::delete', 'admin_blog_delete');

\Route::post('@admin/blog/check-slug', 'Blog\Admin\Blog::checkSlug', 'admin_blog_checkSlug');

\Route::post('@admin/blog/search', 'Blog\Admin\Blog::search', 'admin_blog_search');

\Route::post('@admin/blog/autosave', 'Blog\Admin\Blog::autosave', 'admin_blog_autosave');

\Route::post('@admin/blog/trash/{p:page}?', 'Blog\Admin\Blog::trash', 'admin_blog_trash');

\Route::add('@admin/blog/restore/{int:id}', 'Blog\Admin\Blog::restore', 'admin_blog_restore');

\Route::add('@admin/blog/post-links/{int:id}?/{p:page}?', 'Blog\Admin\Blog::postLinks', 'post_links_for_editor');

\Route::post('@admin/blog/search-links/{p:page}?', 'Blog\Admin\Blog::searchLinks', 'search_links_for_editor');

	//Blog Category

\Route::add('@admin/blog/category', 'Blog\Admin\Category::index', 'admin_blog_category_index');

\Route::add('@admin/blog/category/create', 'Blog\Admin\Category::create', 'admin_blog_category_create');

\Route::add('@admin/blog/category/edit/{int:id}?', 'Blog\Admin\Category::edit', 'admin_blog_category_edit');

\Route::add('@admin/blog/category/delete/{int:id}', 'Blog\Admin\Category::delete', 'admin_blog_category_delete');

\Route::post('@admin/blog/category/order', 'Blog\Admin\Category::order', 'admin_blog_category_order');

\Route::add('@admin/blog/category/getCategory/{int:selected}?', 'Blog\Admin\Category::getCategory', 'admin_blog_get_category');

// --- Front end --- //

\Route::add('blog/rss', 'Blog\Blog::rss', 'rss_feed');

\Route::get('blog/archives/{int:year}?/{int:month}?/{p:page}?', 'Blog\Blog::archives', 'blog_archves')
			->defaults(array('year' => date("Y")));

\Route::get('blog/preview/{*:slug}', 'Blog\Blog::preview', 'blog_preview');

\Route::get('blog/category/{*:slug}/{p:page}?', 'Blog\Blog::category', 'blog_category');

\Route::get('blog/tag/{*:name}/{p:page}?', 'Blog\Blog::tag', 'blog_tag');

\Route::get('blog/author/{int:id}/{p:page}?', 'Blog\Blog::author', 'blog_author');

\Route::get('blog/{p:page}?', 'Blog\Blog::index', 'blog_index');

// Now change {str:slug} to {*:slug} for Myanmar Font Uri.
// Thuesday, 8 October 2013
\Route::get('blog/{*:slug}', 'Blog\Blog::view', 'blog_single');
