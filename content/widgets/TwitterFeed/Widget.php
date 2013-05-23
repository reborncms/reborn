<?php

namespace TwitterFeed;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'Twitter Timelime Feed',
			'author' => 'K'
		);

	public function save() {}

	public function update() {}

	public function delete() {}

	public function form() {}

	public function render()
	{
		$twitterUsername = 'RebornCms';
		return $this->show(array('username' => $twitterUsername));
	}
}
