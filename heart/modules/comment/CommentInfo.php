<?php

namespace Comment;

class CommentInfo extends \Reborn\Module\AbstractInfo
{
    protected $name = 'Comment';

    protected $displayName = array(
        'en'	=> 'Comment',
        'my'	=> 'မှတ်ချက်'
    );

    protected $version = '1.11';

    protected $description = array(
        'en'	=> 'Manage Comments',
        'my'	=> 'မှတ်ချက်များစီမံရန်'
    );

    protected $author = 'Naing Lin Aung / Li Jia Li';

    protected $authorUrl = 'http://www.reborncms.com';

    protected $authorEmail = 'reborncms@gmail.com';

    protected $frontendSupprot = true;

    protected $backendSupport = true;

    protected $uriPrefix = 'comment';

    protected $allowToChangeUriPrefix = true;

    protected $useAsDefaultModule = false;

    /**
    * Variable for Allow Custom Field.
    * If you allow custom field in your module, set true
    *
    * @var boolean
    **/
    protected $allowCustomfield = false;

    protected $sharedData = false;

    protected $roles = array(
        'comment.reply'		=> 'Reply',
        'comment.edit'		=> 'Edit',
        'comment.delete'	=> 'Delete'
    );

}
