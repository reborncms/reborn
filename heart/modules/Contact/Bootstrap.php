<?php

namespace Contact;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		\Translate::load('contact::contact');
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('email', 'Email', '#', null,'icon-mail', 27);
		$menu->add('contact', 'Inbox',$modUri, 'email', null, 27);
		$menu->add('reply', 'Send Email',$modUri.'/send-mail', 'email', null, 27);
		$menu->add('etemplate', 'Email Templates',$modUri.'/email-template', 'email', null, 27);
		/*$menu->add('cform', 'Contact Form',$modUri.'/contact-form', 'email', null, 27);*/
	}

	public function moduleToolbar()
	{
		$uri = \Uri::segment(3);

		if ($uri == 'email-template') {
			$mod_toolbar = array(
				'ealltemplate' => array(
					'url'	=> 'contact/email-template',
					'name'	=> 'Email Templates',
					'info'	=> 'All Email Template',
					'class'	=> 'add'
					),
				'eaddtemplate' => array(
					'url'	=> 'contact/email-template/create',
					'name'	=> 'Add Template',
					'info'	=> 'Create a new Email Template',
					'class'	=> 'add'
					)
				);
		}/* elseif ($uri == 'contact-form') {
			$mod_toolbar = array(
				'contactform' => array(
					'url'  => 'contact/contact-form/edit/1',
					'name' => 'Text Edit',
					'info' => 'Edit Title & Description',
					'class'=> 'add'
					),
				'contactfield' => array(
					'url'  => 'contact/contact-form/field-edit/1',
					'name' => 'Field Eidt',
					'info' => 'Edit Field of contact form',
					'class'=> 'add'
					),
				);
		}*/ else {
			$mod_toolbar = array();
		}
		return $mod_toolbar;
	}

	public function settings()
	{
		\Module::load('Contact');

		return array(
			'sever_mail' => array(
				'type' => 'text'
				),
			'site_mail'	=> array(
				'type' => 'text'
				),
			'transport_mail' => array(
				'type'	=> 'select',
				'options' => array('mail'=>'Mail','smtp'=>'SMTPmail','sendmail'=>'Sendmail')
				),
			'smtp_host' => array(
				'type' => 'text'
				),
			'smtp_port' => array(
				'type' => 'text'
				),
			'smtp_username' => array(
				'type' => 'text'
				),
			'smtp_password' => array(
				'type' => 'password'
				),
			'sendmail_path' => array(
				'type' => 'text'
				),
			'contact_template' => array(
				'type' => 'select',
				'options' => $data = Lib\Helper::getSlug()

				),
			'reply_template' => array(
				'type' => 'select',
				'options' => $data = Lib\Helper::getSlug()
				)
			);
	}

	public function eventRegister()
	{
		
	}
}
