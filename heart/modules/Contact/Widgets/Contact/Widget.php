<?php

namespace Contact;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' 			=> 'Contact Form Widget',
			'sub' 			=> array(
				'contact' 	=> array(
					'title' => 'Contact Form',
					'description' => 'Contact Form Widget',
				),
			),
			'author' => 'Reborn CMS Development Team'
		);

	public function options() 
	{
		return array(
			
			'contact' => array(
				'title' => array(
					'label' 	=> 'Title',
					'type'		=> 'text',
					'info'		=> 'Leave it blank if you don\'t want to show your widget title',
				),
			),
		);
	}

	/**
	 * Query the Blog Posts
	 *
	 * @return string
	 **/
	public function contact()
	{
		if(\Module::isDisabled('Contact')) {
			return null;
		}

		$title = $this->get('title', 'Contact Us');

		\Module::load('Contact');

		return $this->show(array('title' => $title), 'display');
	}

	public function render()
	{
		
			return $this->show();
		
	}
}
