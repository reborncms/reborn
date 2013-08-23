<?php

namespace Blog\Presenter;

class PostPresenter extends \Presenter
{
	protected $model_key = 'posts';

	public function posts()
	{
		// Customized View is bind in Event name 'blog.post.maker'
		if (\Event::has('blog.post.maker')) {
			return \Event::call('blog.post.maker', array($this->posts), true);
		}

		require __DIR__.DS.'template'.DS.'post.html';
	}
}
