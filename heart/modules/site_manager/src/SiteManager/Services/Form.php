<?php

namespace SiteManager\Services;

class Form extends \FormBuilder
{
	protected $model = '\SiteManager\Model\SiteManager';

	/**
	 * Set from element fields
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
				'type'	=> 'select',
				'label'	=> 'Domain',
				'rule'	=> 'required',
				'option' => $this->getRegisterDomains()
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

	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	protected function getRegisterDomains()
	{
		$sites = require BASE_CONTENT.'sites.php';

		$lists = array();

		foreach ($sites['content_path'] as $site => $path) {
			$lists[$site] = $site;
		}

		return $lists;
	}
}
