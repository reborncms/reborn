<?php

namespace Blog;

class BlogInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Blog';

	protected $version = '1.0';

	protected $description = 'Manage your blog';

	protected $author = 'Nyan Lynn Htut / Li Jia Li';

	protected $authorUrl = 'http://www.reborncms.com';

	protected $authorEmail = 'reborncms@gmail.com';

	protected $frontendSupport = true;

	protected $backendSupport = true;

	protected $uriPrefix = 'blog';

	protected $allowToChangeUriPrefix = false;

	protected $useAsDefaultModule = true;

	protected $roles = array(
						'blog.create' => 'Create',
						'blog.edit' => 'Edit',
						'blog.delete' => 'Delete',
						'blog_cat.create' => 'Category Create',
						'blog_cat.edit'	  => 'Category Edit',
						'blog_cat.delete' => 'Category Delete',
						);

}
