<?php

namespace Contact;

class ContactInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Contact';

	protected $version = '1.0';

	protected $description = 'Contact Module';

	protected $author = 'Thet Paing Oo';

	protected $authorUrl = 'http://www.myanmarlinks.net';

	protected $authorEmail = 'gaara.desert91@gmail.com';

	protected $frontendSupport = true;

	protected $backendSupport = true;

	protected $uriPrefix = 'contact';

	protected $allowToChangeUriPrefix = false;

	protected $useAsDefaultModule = false;

	protected $roles = array(
		'contact.view' => 'View',
		'contact.reply' => 'Reply',
		'contact.delete' => 'Delete',
		'contact.template.add' => 'Template Create',
		'contact.template.edit' => 'Template Edit',
		'contact.template.delete' => 'Template Delete',
	);

}
