<?php

namespace SiteManager\Services;

class Form extends \FormBuilder
{
	protected $model = '\SiteManager\Model\SiteManager';

	protected $skipFields = array('shared_by_force');

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
				),

			'shared_by_force' => array(
				'type' => 'checkboxGroup',
				'label' => 'Shared By Force',
				'checkbox_label' => $this->getModules()
				),
			);

		$this->submit = array('submit' => array(
			'value' => t('global.save'),
			));
	}

	/**
	 * Get registered domain lists
	 *
	 * @return array
	 **/
	protected function getRegisterDomains()
	{
		$sites = require BASE_CONTENT.'sites.php';

		$lists = array();

		$model = \SiteManager\Model\SiteManager::all(array('domain'))->lists('domain');

		foreach ($sites['content_path'] as $site => $path) {
			if (!in_array($site, $model)) {
				$lists[$site] = $site;
			}
		}

		if (empty($lists)) {
			$lists = array('' => 'Need to Register Site Domain');
		}

		return $lists;
	}

	/**
	 * Get module lists for Shared Datatable by Force
	 *
	 * @return array
	 **/
	protected function getModules()
	{
		$modules = \Module::findFrom(array(CORE_MODULES, SHARED.'modules'.DS));

		$modules = array_filter($modules, function($m) {
            return (false == $m['shared_data']);
        });

        $results = array();

        foreach ($modules as $name => $data) {
        	if ($data['allow_shared_by_user']) {
        		$results[$name] = $data['name'];
        	}
        }

        return $results;
	}
}
