<?php

if (\Module::has('api') and \Module::isEnabled('api')) {

    Route::group('api/v1/blog', function(){

        Route::get('posts', 'Blog\Api::posts', 'blog_api_posts');

        Route::get('posts/{int:id}', 'Blog\Api::post', 'blog_api_single_post');

    });

}