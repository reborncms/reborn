<?php

namespace Twitter;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'Twitter Button',
			'description' => 'Twitter Button Widget. Now only support for share button',
			'author' => 'Nyan Lynn Htut'
		);

	public function save() {}

	public function update() {}

	public function delete() {}

	public function form() {}

	public function options()
	{
		return array(
	        'type' => array(
	            'label'		=> 'Twitter Button Type',
	            'type'		=> 'select',
	            'options'	=> array(
					                'share' => 'Share Button'
					            )
	        ),
	        'url' => array(
	            'label'		=> 'Url',
	            'type'		=> 'text',
	            'info'		=> 'Url for Twitter Share Widget. If this field is empty, twitter widget will be set share url is current url.',
	        ),
	    );
	}

	public function render()
	{
		$data = array();
		$data['type'] = $this->get('type', 'share');
		$data['url'] = $this->get('url', \Uri::current());
		$data['text'] = $this->get('text', '');
		return $this->show($data);
	}
}
