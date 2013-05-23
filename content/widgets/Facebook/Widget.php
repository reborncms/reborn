<?php

namespace Facebook;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'Facebook Like Button Widget',
			'author' => 'Nyan Lynn Htut'
		);

	public function save() {}

	public function update() {}

	public function delete() {}

	public function form() {}

	public function render()
	{
		$data = array();

		$data['title'] = $this->get('', 'Facebook Like Button');

		$data['fb_url'] = $this->get('url');
		$data['fb_send'] = $this->get('send', "true");
		$data['fb_font'] = $this->get('font', "arial");
		// support - arial, lucida grande, segoe ui, tahoma, trebuchet ms, verdana
		$data['fb_show_faces'] = $this->get('faces', "true");
		$data['fb_width'] = $this->get('width', "450");
		$data['fb_colorscheme'] = $this->get('color', 'light');
		// support light, dark
		$data['fb_action'] = $this->get('action', 'like');
		// support like, recommend
		$data['fb_layout'] = $this->get('layout', 'standard');
		// support - standard, button_count, box_count

		return $this->show($data);
	}
}
