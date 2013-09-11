<?php

namespace TwitterTimeline;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'Twitter Timeline',
			'description' => 'Twitter embedded timeline with easy-to-use options.',
			'author' => 'K'
		);

	public function options()
	{
		return array(
	        'title' => array(
	            'label'		=> 'Title',
	            'type'		=> 'text',
	        ),	        
	        'widgetid' => array(
	            'label'		=> 'Widget ID',
	            'type'		=> 'text',
	            'info'		=> 'You need to <a href="https://twitter.com/settings/widgets/new/user" target="_blank">create a widget at Twitter.com</a>, and then enter your widget id (the long number found in the URL of your widget\'s config page) in the field below. <a href="http://en.support.wordpress.com/widgets/twitter-timeline-widget/" target="_blank">Read more.</a>',
	        ),
	        'width' => array(
	            'label'		=> 'Width',
	            'type'		=> 'text',
	        ),
	        'height' => array(
	            'label'		=> 'Height',
	            'type'		=> 'text',
	        ),
	        'noheader' => array(
				'label' 	=> 'No Header',
				'type'		=> 'select',
				'options'	=> array(
					'noheader'	=> 'Yes',
					'' => 'No',
				),
			),
			'nofooter' => array(
				'label' 	=> 'No Footer',
				'type'		=> 'select',
				'options'	=> array(
					'nofooter'	=> 'Yes',
					'' => 'No',
				),
			),
			'noborders' => array(
				'label' 	=> 'No Borders',
				'type'		=> 'select',
				'options'	=> array(
					'noborders'	=> 'Yes',
					'' => 'No',
				),
			),
			'noscrollbar' => array(
				'label' 	=> 'No Scrollbar',
				'type'		=> 'select',
				'options'	=> array(
					'noborders'	=> 'Yes',
					'' => 'No',
				),
			),
			'transparent' => array(
				'label' 	=> 'Transparent Background',
				'type'		=> 'select',
				'options'	=> array(
					'transparent'	=> 'Yes',
					'' => 'No',
				),
			),
			'theme' => array(
				'label' 	=> 'Timeline Theme',
				'type'		=> 'select',
				'options'	=> array(
					'light'	=> 'Light',
					'dark'	=> 'Dark',
				),
			),
			'linkcolor' => array(
	            'label'		=> 'Link Color (in Hex)',
	            'type'		=> 'text',
	        ),
	        'bordercolor' => array(
	            'label'		=> 'Border Color (in Hex)',
	            'type'		=> 'text',
	        ),
	        'limit' => array(
	            'label'		=> 'Tweets Limit (max 20)',
	            'type'		=> 'text',
	        ),
	    );
	}

	public function render()
	{
		$data = array();
		$data['title'] = $this->get('title', 'Google Doc Viewer');

		$data['widgetid'] = $this->get('widgetid');
		$data['timeline_width'] = $this->get('width', 300);
		$data['timeline_height'] = $this->get('height', 500);
		$data['timeline_noheader'] = $this->get('noheader', '');
		$data['timeline_nofooter'] = $this->get('nofooter', '');
		$data['timeline_noborders'] = $this->get('noborders', '');
		$data['timeline_noscrollbar'] = $this->get('noscrollbar', '');
		$data['timeline_transparent'] = $this->get('transparent', '');
		$data['timeline_theme'] = $this->get('theme', 'light');
		$data['timeline_linkcolor'] = $this->get('linkcolor', '#cc0000');
		$data['timeline_bordercolor'] = $this->get('bordercolor', '#cc0000');
		$data['timeline_limit'] = $this->get('limit', '5');

		return $this->show($data);		
	}
}
