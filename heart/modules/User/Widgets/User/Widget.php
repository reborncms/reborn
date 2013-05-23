<?php

namespace User;

use Reborn\Connector\Sentry\Sentry;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'User Login Block',
			'author' => 'K'
		);

	public function save() {}

	public function update() {}

	public function delete() {}

	public function form() {}

	public function nav()
	{
		if(Sentry::check()) {
			$user = Sentry::getUser();
			$name = $user->first_name.' '.$user->last_name;
			$gravy = gravatar($user->email, 42, $user->first_name);
			return $this->show(array('name' => $name, 'gravy' => $gravy), 'navdisplay');
		} else {
			return $this->show('', 'navlogin');
		}
	}

	public function render()
	{
		if(Sentry::check()) {
			$user = Sentry::getUser();
			
			$title = "User Panel";
			$name = $user->first_name.' '.$user->last_name;
			return $this->show(array('name' => $name, 'title' => $title));
		} else {
			$title = "User Login";
			return $this->show(array('title' => $title), 'login');
		}
	}
}
