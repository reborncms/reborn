<?php

namespace Admin\Presenter;

class DashboardWidget extends \Presenter
{

	public function attributeRender()
	{
		if (count($this->resource) > 0) {
			return $this->renderWidgets();
		} else {
			return null;
		}
	}

	protected function renderWidgets()
	{
		$view = \Registry::get('app')->view;
		$html = DASHBOARD_PATH.DS.'views'.DS.'widgets.html';

		return $view->set('widgets', $this->resource)->render($html);
	}

}
