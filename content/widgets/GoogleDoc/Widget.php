<?php

namespace GoogleDoc;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'Google Doc Viewer Widget',
			'author' => 'Nyan Lynn Htut'
		);

	public function save() {}

	public function update() {}

	public function delete() {}

	public function form() {}

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
