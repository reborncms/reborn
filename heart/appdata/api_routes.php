<?php

if (\Module::has('api') and \Module::isEnabled('api')) {

    Route::group('api/v1/blog', function(){

        Route::get('posts', 'Blog\Api::posts', 'blog_api_posts');

        Route::get('posts/{int:id}', 'Blog\Api::post', 'blog_api_single_post');

        //By Post Types
        Route::get('type/{alpha:type}/posts', 'Blog\Api::getByPostType', 'blog.api.type.posts');

        //By Category
        Route::get('category/{int:category_id}/posts', 'Blog\Api::getByCategory', 'blog.api.category.posts');

        //By Author
        Route::get('author/{int:author_id}/posts', 'Blog\Api::getByAuthor', 'blog.api.author.posts');

        //By Tags
        Route::get('tag/{*:tag}/posts', 'Blog\Api::getByTags', 'blog.api.tags.posts');

        //By Years and Months
        Route::get('archives/{int:year}/{int:month}?/posts', 'Blog\Api::getArchives', 'blog.api.archives.posts');

        //Categories
        Route::get('categories', 'Blog\Api::getCategories', 'blog.categories');

        //Authors
        Route::get('authors', 'Blog\Api::getAuthors', 'blog.authors');

        //Tags
        Route::get('tags', 'Blog\Api::getTags', 'blog.tags');

    });

    require SYSTEM . 'modules/media/api_routes.php';

    require SYSTEM . 'modules/user/api_routes.php';

}
