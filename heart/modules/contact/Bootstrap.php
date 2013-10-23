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
		$childs = array(
			array('title' => t('contact::contact.inbox'), 'uri' => '' ),
			array('title' => t('contact::contact.s_mail'), 'uri' => 'send-mail' ),
			array('title' => t('contact::contact.e_template'), 'uri' => 'email-template'),
			);
		$menu->group($modUri,t('label.email'),'icon-mail',35,$childs);
	}

	public function moduleToolbar()
	{
		$uri = \Uri::segment(3);

		if ($uri == 'email-template') {
			$mod_toolbar = array(
				'ealltemplate' => array(
					'url'	=> 'contact/email-template',
					'name'	=> t('contact::contact.e_template'),
					'info'	=> t('contact::contact.e_all_template'),
					'class'	=> 'add'
					),
				'eaddtemplate' => array(
					'url'	=> 'contact/email-template/create',
					'name'	=> t('contact::contact.add_template'),
					'info'	=> t('contact::contact.add_des_template'),
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
		if(\Module::isEnabled('Contact')) {
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
	}

	public function register()
	{
		
	}
}
