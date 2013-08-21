<?php

namespace User;

use Reborn\Connector\Sentry\Sentry;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'User Login Block',
			'author' => 'K',
			'sub' 			=> array(
				'header' 	=> array(
					'title' => 'User Header Navigation Login',
					'description' => 'User login and action panel for header',
				),
				'sidebar' 	=> array(
					'title' =>'User Sidebar Login',
					'description' => 'Sidebar Userpanel with Login',
				),
			),
		);

	public function options() 
	{
		return array(
			'header' => array(
				'title' => array(
					'label' 	=> 'Title',
					'type'		=> 'text',
					'info'		=> 'Leave it blank if you don\'t want to show your widget title',
				),
			),
			'sidebar' => array(
				'title' => array(
					'label' 	=> 'Title',
					'type'		=> 'text',
					'info'		=> 'Leave it blank if you don\'t want to show your widget title',
				),
			),
		);
	}

	public function header()
	{
		if(Sentry::check()) {
			$user = Sentry::getUser();
			$title = $this->get('title', '');

			$name = $user->first_name.' '.$user->last_name;
			$gravy = gravatar($user->email, 42, $user->first_name);
			return $this->show(array('name' => $name, 'gravy' => $gravy, 'title' => $title), 'navdisplay');
		} else {
			$title = $this->get('title', '');
			return $this->show(array('title' => $title), 'navlogin');
		}
	}

	public function sidebar()
	{
		if(Sentry::check()) {
			$user = Sentry::getUser();
			
			$title = $this->get('title', 'User Panel');
			$name = $user->first_name.' '.$user->last_name;
			return $this->show(array('name' => $name, 'title' => $title));
		} else {
			$title = $this->get('title', 'User Login');
			return $this->show(array('title' => $title), 'login');
		}
	}
}
