<?php

// Route file for module Blog
\Route::add('rss_feed', 'blog/rss', 'Blog\Blog::rss');

\Route::add('blog_archives', 'blog/archives/{:int}/{:?}', 'Blog\Blog::archives');

\Route::add('blog_preview', 'blog/preview/{:any}', 'Blog\Blog::preview');

\Route::add('blog_category', 'blog/category/{:any}/{:?}', 'Blog\Blog::category');

\Route::add('blog_tag', 'blog/tag/{:any}/{:?}', 'Blog\Blog::tag');

\Route::add('blog_author', 'blog/author/{:any}/{:?}', 'Blog\Blog::author');

\Route::add('blog_single', 'blog/{:any}', 'Blog\Blog::view');