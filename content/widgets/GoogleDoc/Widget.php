<?php

namespace GoogleDoc;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'Google Doc Viewer',
			'description' => 'Google Document Viewer Widget',
			'author' => 'Nyan Lynn Htut'
		);

	public function save() {}

	public function update() {}

	public function delete() {}

	public function form() {}

	public function options()
	{
		return array(
	        'title' => array(
	            'label'		=> 'Title',
	            'type'		=> 'text',
	            'info'		=> 'Title for Google Doc Viewer',
	        ),
	        'url' => array(
	            'label'		=> 'Document Url',
	            'type'		=> 'text',
	            'info'		=> 'Document Url for Google Doc Viewer',
	        ),
	        'url' => array(
	            'label'		=> 'Document Url',
	            'type'		=> 'text',
	            'info'		=> 'Document Url for Google Doc Viewer',
	        ),
	        'width' => array(
	            'label'		=> 'Width',
	            'type'		=> 'text',
	            'info'		=> 'Width for Google Doc Viewer',
	        ),
	        'height' => array(
	            'label'		=> 'Height',
	            'type'		=> 'text',
	            'info'		=> 'Height for Google Doc Viewer',
	        ),
	        'style' => array(
	            'label'		=> 'Style',
	            'type'		=> 'text',
	            'info'		=> 'CSS Style for Google Doc Viewer. eg: border="display:none;"',
	        )
	    );
	}

	public function render()
	{
		$data = array();
		$data['title'] = $this->get('title', 'Google Doc Viewer');

		$data['gdoc_url'] = $this->get('url');
		$data['gdoc_width'] = $this->get('width', 600);
		$data['gdoc_height'] = $this->get('height', 780);
		$data['gdoc_style'] = $this->get('style', 'border="display:none;"');

		return $this->show($data);
	}
}
