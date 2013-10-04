<?php

namespace Widgets\Controller\Admin;

use Widgets\Model\Widgets;

use Widgets\Lib\optionsForm;

class WidgetsController extends \AdminController
{
	public function before() {

		$this->menu->activeParent('appearance');

		$this->template->style('widget.css', 'widgets');
		$this->template->script('widget.js', 'widgets');

		$ajax = $this->request->isAjax();

		if ($ajax) {
			$this->template->partialOnly();
		}
	}

	public function index() {
		$widgets = \Registry::get('app')->widget;
		$all = $widgets->all();
		$active_theme = \Setting::get('public_theme');
		$theme_info = $this->theme->info($active_theme, true);

		$area_widget = array();

		$inactive_widget = array();

		if (isset($theme_info['widget_areas'])) {

			foreach ($theme_info['widget_areas'] as $name => $title) {
				$area_widget[$name] = array(
					'title'	=> $title,
					'widgets' => array(),
				);
			}

			$areas = array_keys($theme_info['widget_areas']);

			$all_widgets = Widgets::all();

			foreach ($all_widgets as $widget) {
				if (in_array($widget->area, $areas)) {
					$area_widget[$widget->area]['widgets'][] = $widget;
				} else {
					$inactive_widget[] = $widget;
				}
			}
		}

		$this->template->title('Widgets Manager')
						->setPartial('admin/index')
						->script('plugins/jquery.colorbox.js')
						->set('all', $all)
						->set('areas', $area_widget)
						->set('inactive_widget', $inactive_widget);
	}

	public function add()
	{
		if (\Input::isPost()) {
			$widget = new Widgets;
			$widget->name = \Input::get('name');
			$widget->area = \Input::get('area');
			$save = $widget->save();
			if ($save) {
				return $this->returnJson(array('status' => 'ok', 'id' => $widget->id));
			} else {
				return $this->returnJson(array('status' => 'fail'));
			}
		}
	}

	public function order()
	{
		if (\Input::isPost()) {
			$order = \Input::get('order');
			foreach ($order as $order => $id) {
				$widget = Widgets::find($id);
				$widget->widget_order = $order;
				$save = $widget->save();
				if ($save) {
					$save_items[] = $widget->id;
				}
			}
			if (count($save_items) > 0) {
				return $this->returnJson(array('status' => 'ok', 'area' => \Input::get('area')));
			} else {
				return $this->returnJson(array('status' => 'fail'));
			}
		}
	}

	public function remove($id = null)
	{
		if ($id != null) {
			if ($widget = Widgets::find($id)) {
				$widget->delete();
				return $this->returnJson(array('status' => 'ok'));
			}
		}
	}

	public function settings($name, $id)
	{
		$name = urldecode($name);
		
		return optionsForm::render($name, $id);
	}

	public function hasOptions()
	{
		$options = \Widget::options(\Input::get('name'));

		if ($options != null) {

			return $this->returnJson(array('status' => 'ok'));

		} else {

			return $this->returnJson(array('status' => 'fail'));

		}
	}

	public function moveArea($id)
	{
		if ($widget = Widgets::find($id)) {
			$widget->area = \Input::get('area');
			$save = $widget->save();
			if ($save) {
				return $this->returnJson(array('status' => 'ok', 'id' => $widget->id));
			} else {
				return $this->returnJson(array('status' => 'fail'));
			}
		}
	}

	public function optionsSave()
	{
		$form_data = \Input::get('*');

		foreach ($form_data as $key => $value) {
			if (empty($value)) {
				unset($form_data[$key]);
			}
		}

		unset($form_data['widget_id']);

		$widget = Widgets::find(\Input::get('widget_id'));

		$widget->options = serialize($form_data);

		$save = $widget->save();

		if ($save) {
			return $this->returnJson(array('status' => 'ok'));
		} else {
			return $this->returnJson(array('status' => 'fail'));
		}

	}

	public function after($response)
	{
		return parent::after($response);
	}
}
