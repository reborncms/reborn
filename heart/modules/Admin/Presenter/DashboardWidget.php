<?php

namespace Admin\Presenter;

class DashboardWidget extends \Presenter
{

	public function render()
	{
		if (count($this->model) > 0) {
			return $this->renderWidgets();
		} else {
			return null;
		}
	}

	protected function renderWidgets()
	{
		$view = \Registry::get('app')->view->getView();
		$html = __DIR__.DS.'..'.DS.'views'.DS.'widgets.html';

		return $view->set('widgets', $this->model)->render($html);
	}

}
