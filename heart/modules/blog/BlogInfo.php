<?php

namespace Blog;

class BlogInfo extends \Reborn\Module\AbstractInfo
{
    protected $name = 'Blog';

    protected $displayName = array(
        'en'	=> 'Blog',
        'my'	=> 'ဘလော့ဂ်'
    );

    protected $version = '1.21';

    protected $description = array(
        'en'	=> 'Manage your blog',
        'my'	=> 'ဘလော့ဂ် ပို့စ်များစီမံရန်'
    );

    protected $author = 'Nyan Lynn Htut / Li Jia Li';

    protected $authorUrl = 'http://www.reborncms.com';

    protected $authorEmail = 'reborncms@gmail.com';

    protected $frontendSupport = true;

    protected $backendSupport = true;

    protected $uriPrefix = 'blog';

    protected $allowToChangeUriPrefix = false;

    protected $useAsDefaultModule = true;

    protected $sharedData = false;

    /**
    * Variable for Allow Custom Field.
    * If you allow custom field in your module, set true
    *
    * @var boolean
    **/
    protected $allowCustomfield = true;

    protected $roles = array(
                        'blog.create' => 'Create',
                        'blog.edit' => 'Edit',
                        'blog.delete' => 'Delete',
                        'blog_cat.create' => 'Category Create',
                        'blog_cat.edit'	  => 'Category Edit',
                        'blog_cat.delete' => 'Category Delete',
                        );

}
