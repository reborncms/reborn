<?php

namespace Twitter;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'Twitter Share Button Widget',
			'author' => 'Nyan Lynn Htut'
		);

	public function save() {}

	public function update() {}

	public function delete() {}

	public function form() {}

	public function render()
	{
		$data = array();
		$data['title'] = $this->get('title', 'Twitter Share Button');
		$data['type'] = $this->get('type', 'share');
		$data['url'] = $this->get('url', \Uri::current());
		$data['text'] = $this->get('text');
		return $this->show($data);
	}
}
