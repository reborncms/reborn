<?php

namespace Admin\Presenter;

use Reborn\Cores\Facade;

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
		$view = Facade::getApplication()->view;
		$html = DASHBOARD_PATH.DS.'views'.DS.'widgets.html';

		return $view->set('widgets', $this->resource)->render($html);
	}

}
