<?php

namespace Blog\Presenter;

class PostPresenter extends \Presenter
{
    protected $model_key = 'posts';

    public function posts($template)
    {
        $default = __DIR__.DS.'template'.DS.'post.html';
        // Customized View is bind in Event name 'blog.post.maker.template'
        if (\Event::has('blog.post.maker.template')) {
            $template_path = \Event::first('blog.post.maker.template', array($template));

            if (is_null($template_path) and !\File::is($template_path)) {
                require $default;
            } else {
                require $template_path;
            }
        } else {
            require $default;
        }
    }
}
