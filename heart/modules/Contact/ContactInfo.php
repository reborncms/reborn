<?php

namespace Contact;

class ContactInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Contact';

	protected $version = '1.0';

	protected $description = array('en' => 'Contact Module is used for contact mail, reply mail, inbox and email template.', 'my' => 'အီးမေးလ် နှင့်ပတ်သက်သော မော်ဂျူး ကို မေးလ် ပို၍ ဆက်သွယ်ခြင်း၊ မေးလ် ဖြင့် အကြောင်းပြန်ခြင်း၊ ဝင်ရောက်လာသော မေးလ်များကို ကြည့်ရှုခြင်း နှင့် အီးမေးလ် ပုံစံများကို ပြင်ခြင်းတို့ အတွက် အသုံးပြုနိုင်ပါတယ်။');

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

	protected  $displayName = array('en' => 'Contact', 'my' => 'အီးမေးလ် နှင့်ပတ်သက်သော မော်ဂျူး');

}
