<?php

namespace Announcement;

class Widget extends \Reborn\Widget\AbstractWidget
{
	protected $properties = array(
		'name'			=> 'Announcement',
		'description'	=> 'Text Announcement Widget.',
		'author'		=> 'Li Jia Li'
	);

	public function options()
	{
		return array(
			'title'	=> array(
				'label'	=> 'Title',
				'type'	=> 'text',
				'info'	=> 'Announcement Title. Leave it blank if you don\'t want to show title.'
			),
			'text'	=> array(
				'label'	=> 'Text',
				'type'	=> 'textarea',
				'info'	=> 'Announcement text to show'
			),
			'text_wrapper_class' => array(
				'label'	=> 'Text Wrapper Class',
				'type'	=> 'text'
			),
		);
	}

	public function render()
	{
		$data = array();
		$data['title'] = $this->get('title', '');
		$data['text'] = $this->get('text', '');
		$data['text_wrapper_class'] = $this->get('text_wrapper_class', '');
		return $this->show($data);
	}
}