<?php

namespace Contact;

class ContactInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Contact';

	protected $version = '1.2';

	protected $description = array(
		'en' => 'Contact Module is used for contact mail, reply mail, inbox and email template.',
		'my' => 'အီးမေးလ်နှင့်ပတ်သတ်သော ကိစ္စများကို လုပ်ဆောင်ရန်နေရာ',
		'tr' => 'İletişim Modülü, ziyaretçilerle dirsek teması halinde olmanızı sağlar.'
	);

	protected $author = 'Thet Paing Oo';

	protected $authorUrl = 'http://www.reborncms.com';

	protected $authorEmail = 'reborncms@gmail.com';

	protected $frontendSupport = true;

	protected $backendSupport = true;

	protected $uriPrefix = 'contact';

	protected $allowToChangeUriPrefix = false;

	protected $useAsDefaultModule = false;

	protected $allowCustomfield = true;

	protected $sharedData = false;

	protected $roles = array(
		'contact.view' => 'View',
		'contact.reply' => 'Reply',
		'contact.delete' => 'Delete',
		'contact.template.add' => 'Template Create',
		'contact.template.edit' => 'Template Edit',
		'contact.template.delete' => 'Template Delete',
	);

	protected  $displayName = array('en' => 'Contact', 'my' => 'အီးမေးလ် နှင့်ပတ်သက်သော မော်ဂျူး');

}
