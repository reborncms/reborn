<?php

namespace Facebook;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' => 'Facebook Like Button',
			'description' => 'Facebook Like Button Widget',
			'author' => 'Nyan Lynn Htut'
		);

	public function options()
	{
		return array(
	        'fb_title' => array(
	            'label'		=> 'Widget Title',
	            'type'		=> 'text',
	            'info'		=> 'Title Text for Facebook Widget Box',
	        ),
	        'url' => array(
					'label' 	=> 'Url',
					'type'		=> 'text',
					'info'		=> 'Facebook Link for like button',
				),
	        'width' => array(
					'label' 	=> 'Width',
					'type'		=> 'text',
					'info'		=> 'Width of the facebook like.',
				),
	        'face' => array(
					'label' 	=> 'Display profile photos',
					'type'		=> 'select',
					'options'	=> array(
						'true'	=> 'Show',
						'false'	=> 'Hide',
					),
					'info'		=> 'Display profile photos below the button (standard layout only)',
				),
	        'action' => array(
					'label' 	=> 'Button Label',
					'type'		=> 'select',
					'options'	=> array(
						'like'	=> 'Like',
						'recommend'	=> 'Recommend',
					),
					'info'		=> 'The verb to display on the button',
				),
	        'layout' => array(
					'label' 	=> 'Layout',
					'type'		=> 'select',
					'options'	=> array(
						'standard'	=> 'Standard',
						'button_count'	=> 'Button Count',
						'box_count'	=> 'Box Count'
					),
					'info'		=> 'The verb to display on the button',
				),
	        'color' => array(
					'label' 	=> 'Color scheme',
					'type'		=> 'select',
					'options'	=> array(
						'light'	=> 'Light',
						'dark'	=> 'Dark',
					),
					'info'		=> 'Color scheme for the like button',
				),
	        'font' => array(
					'label' 	=> 'Button Font',
					'type'		=> 'select',
					'options'	=> array(
						'arial'	=> 'Arial',
						'lucida grande'	=> 'Lucida Grande',
						'segoe ui' => 'Segoe UI',
						'tahoma' => 'Tahoma',
						'trebuchet ms' => 'Trebuchet MS',
						'verdana' => 'Verdana'
					),
					'info'		=> 'The font to display in the button.',
				),
	        'send' => array(
					'label' 	=> 'Send Button',
					'type'		=> 'select',
					'options'	=> array(
						'true'	=> 'Yes',
						'false'	=> 'No',
					),
					'info'		=> 'Include a Send button with the Like button',
				),
	    );
	}

	public function render()
	{
		$data = array();

		$data['fb_title'] = $this->get('fb_title', null);
		$data['fb_url'] = $this->get('url', \Uri::current());
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
