<?php

namespace Widgets\Lib;

use Widgets\Model\Widgets;

class Helper {

	public static function areaRender($area = 'sidebar')
	{
		$widgets = '';
		$getWidgets = Widgets::where('area', $area)->orderBy('widget_order')->get();
		if (!empty($getWidgets)) {
			foreach ($getWidgets as $widget) {
				$options = unserialize($widget->options);
				$widgets .= \Widget::call($widget->name, $options);
			}
		}

		return $widgets;
	}

}