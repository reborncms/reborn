<?php

namespace Setting\Controller\Admin;

class SettingController extends \AdminController
{
	public function before()
	{
		$this->menu->activeParent('settings');
		$this->settings = \Setting::getFromModules();
	}

	public function index()
	{
		return \Redirect::toAdmin('setting/system');
	}

	public function system()
	{
		$this->process();
	}

	public function module($name = null)
	{
		// If Module is Disabled, return the 404 result
		if (is_null($name) || \Module::isDisabled($name)) {
			return $this->notFound();
		}

		$name = ucfirst($name);

		if (!isset($this->settings['modules'][$name])) {
			return $this->notFound();
		}

		$this->process($name);
	}

	public function save()
	{
		if (!\Input::isPost()) {
			return \Redirect::toAdmin('setting/system');
		}

		if (!\Security::CSRFValid('rbset')) {
			\Flash::error(t('global.csrf_fail'));
			return \Redirect::toAdmin('setting/system');
		}

		if (\Input::get('type') == 'system') {
			$fields = $this->settings['system'];
		} else {
			$fields = $this->settings['modules'][ucfirst(\Input::get('type'))];
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
			\Flash::error($errors);
		}

		return \Redirect::to(\Input::server('HTTP_REFERER'));
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

			$title = sprintf(\Translate::get('setting::setting.module_title'), $type);
			if (isset($this->settings['modules'][$type])) {
				$settings = $this->settings['modules'][$type];
			}
		} else {
			$settings = $this->settings[$type];
			$title = \Translate::get('setting::setting.system_title');
		}

		$this->template->title('Setting')
					->set('settings', $settings)
					->set('type', $type)
					->set('title', $title)
					->setPartial('index');
	}
}
