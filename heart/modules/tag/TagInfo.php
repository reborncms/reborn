<?php

namespace Tag;

class TagInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Tag';

	protected $displayName = array(
		'en'	=> 'Tag',
		'my'	=> 'Tag',
		'tr'	=> 'Etiketler'
	);

	protected $version = '1.0';

	protected $description = array(
		'en'	=> 'Manage tags for your content',
		'my'	=> 'Tag များကို စီမံရန်',
		'tr'	=> 'İçerikleriniz için etiketleri yönetin'
	);

	protected $author = 'Nyan Lynn Htut / Li Jia Li';

	protected $authorUrl = 'http://www.reborncms.com';

	protected $authorEmail = 'reborncms@gmail.com';

	protected $frontendSupport = false;

	protected $backendSupport = true;

	protected $uriPrefix = 'tag';

	protected $allowToChangeUriPrefix = false;

	protected $useAsDefaultModule = false;

	protected $sharedData = false;

	/**
	* Variable for Allow Custom Field.
	* If you allow custom field in your module, set true
	*
	* @var boolean
	**/
	protected $allowCustomfield = false;

	protected $roles = array(
			'tag.create'	=> 'Create',
			'tag.edit'		=> 'Edit',
			'tag.delete'	=> 'Delete'
	);

}
