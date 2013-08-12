<?php

namespace TwitterFeed;

class Widget extends \Reborn\Widget\AbstractWidget
{
	protected $properties = array(
			'name' => 'Twitter Timelime Feed',
			'author' => 'K',
			'sub' 			=> array(
				'feeds' 	=> array(
					'title' => 'Twitter',
					'description' => 'Show twitter feed from user timeline.',
				),
			),
		);

	public function options() 
	{
		return array(
			'feeds' => array(
				'title' => array(
					'label' 	=> 'Title',
					'type'		=> 'text',
					'info'		=> 'Leave it blank if you don\'t want to show your widget title',
				),
				'username' => array(
					'label' 	=> 'Twitter Username',
					'type'		=> 'text',
				),
				'limit' 	=> array(
					'label' 	=> 'Number of Feeds',
					'type'		=> 'text',
				),
			),
		);
	}

	public function feeds()
	{
		$title = $this->get('title', 'Twitter Feeds');
		$username = $this->get('username', 'RebornCms');
		$limit = $this->get('limit', 5);
				
		return $this->show(array('limit' => $limit, 'username' => $username, 'title' => $title));
	}
}
