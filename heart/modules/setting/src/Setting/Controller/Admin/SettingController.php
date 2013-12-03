<?php

namespace Setting\Controller\Admin;

class SettingController extends \AdminController
{

	public function before()
	{
		$this->menu->activeParent('settings');
		$this->settings = \Setting::getFromModules();

		$this->template->style('settings.css', 'Setting');
		$this->template->script('fancySelect.js', 'Setting');
	}

	public function index()
	{
		$this->process();
	}

	public function module($name = null)
	{
		// If Module is Disabled, return the 404 result
		if (is_null($name) || !\Module::isEnabled($name)) {
			return $this->notFound();
		}

		if (!isset($this->settings['modules'][$name])) {
			return $this->notFound();
		}

		$this->process($name);
	}

	public function save($type)
	{
		if (!\Input::isPost()) {
			return \Redirect::toAdmin('setting');
		}

		if (\Input::get('type') == 'system') {
			$fields = $this->settings['system'];
			$url = 'setting';
		} else {
			$fields = $this->settings['modules'][\Input::get('type')];
			$url = 'setting/module/'.$type;
		}

		$rules = array();
		foreach ($fields as $field) {
			if ($field['require']) {
				$rules[$field['slug']] = 'required';
			}
		}

		$v = new \Validation(\Input::get('*'), $rules);

		$checks = $this->getCheckBox($fields);

		if ($v->valid()) {
			// Set the Setting Value
			foreach (\Input::get('*') as $name => $value) {
				if ( in_array($name, $checks)) {
					\Setting::set($name, '1');
				} else {
					\Setting::set($name, $value);
				}
			}

			// Second step, save the checkbox value
			foreach($checks as $name) {
				if ( !array_key_exists($name, \Input::get('*')) ) {
					\Setting::set($name, '0');
				}
			}

			$msg = \Translate::get('setting::setting.save_success');
			\Flash::success($msg);
		} else {
			// Form Validation Error
			$errors = $v->getErrors();
			// Assign session for errors
			\Flash::error($errors->toArray());
		}

		return \Redirect::toAdmin($url);
	}

	protected function getCheckBox($fields = array())
	{
		$checks = array();

		foreach ($fields as $name => $attrs) {
			if ($attrs['type'] == 'checkbox') {
				$checks[] = $name;
			}
		}

		return $checks;
	}

	protected function process($type = 'system')
	{
		if ($type != 'system') {

			$title = sprintf(\Translate::get('setting::setting.module_title'), ucfirst($type));
			if (isset($this->settings['modules'][$type])) {
				$settings = $this->settings['modules'][$type];
			}
		} else {
			$settings = $this->settings[$type];
			$title = \Translate::get('setting::setting.system_title');
		}

		$main = array(adminUrl('setting') => t('setting::setting.system_title'));

		$navigation = $this->settings;
		$lists = array();

		if (isset($navigation['modules'])) {
			foreach ($navigation['modules'] as $mod => $val) {
				if (\Module::isEnabled($mod)) {
					$url = adminUrl('setting/module/'.strtolower($mod));
					$lists[$url] = ucfirst($mod);
				}
			}

			asort($lists);
		}

		$this->template->title('Setting')
					->set('settings', $settings)
					->set('type', $type)
					->set('title', $title)
					->set('lists', array_merge($main, $lists))
					->setPartial('index');
	}
}
