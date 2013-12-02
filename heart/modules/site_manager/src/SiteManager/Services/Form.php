<?php

namespace SiteManager\Services;

class Form extends \FormBuilder
{
	protected $model = '\SiteManager\Model\SiteManager';

	/**
	 * For for Bill input
	 *
	 * @access public
	 * @return void
	 **/
	public function setFields()
	{
		$this->fields = array(

			'name'	=> array(
				'type'	=> 'text',
				'label'	=> 'Name',
				'rule'	=> 'required'
				),

			'domain'	=> array(
				'type'	=> 'text',
				'label'	=> 'Domain',
				'rule'	=> 'required'
				),

			'description'	=> array(
				'type'	=> 'textarea',
				'label'	=> 'Description'
				)
			);

		$this->submit = array('submit' => array(
			'value' => t('global.save'),
			));
	}
}
