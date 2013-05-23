<?php

namespace Contact;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'Contact Form',
			'author' => 'Thet Paing Oo'
		);

	public function save() {}

	public function update() {}

	public function delete() {}

	public function form() {}

	public function render()
	{
		
			return $this->show();
		
	}
}
