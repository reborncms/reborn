<?php

// Route file for module Blog
\Route::add('rss_feed', 'blog/rss', 'Blog\Blog::rss');

\Route::add('blog_archives', 'blog/archives', 'Blog\Blog::archives');

\Route::add('blog_single', 'blog/{:alnum}', 'Blog\Blog::view');