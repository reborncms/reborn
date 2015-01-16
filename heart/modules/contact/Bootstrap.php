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
		} else {
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
				'options' => \Config::get('contact::contact.transport_mail')
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
				),
			'attach_field' => array(
				'type' => 'select',
				'options' => \Config::get('contact::contact.attachment_opt')
				)
			);
		}
	}

	public function register()
	{
		
	}
}
