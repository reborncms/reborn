<?php

namespace Module;

class NotifyWidget
{
	/**
	 * Helper method for Adminpanel dashboard widget view.
	 *
	 * @return string|null
	 * @author Nyan Lynn Htut
	 **/
	public static function dashboardWidget()
	{
		$output = '';
		$results = array();

		$all = \Module::getAll();

		foreach ($all as $module) {
			if ($module->needToUpdate()) {
				$results[$module->uri] = $module->displayName();
			}
		}

		if ( empty($results) ) {
			return null;
		}

		$text = t('module::module.dashboard_widget_text');
		foreach ($results as $uri => $mod) {
			$output .= '<li>';
			$output .= sprintf($text, $mod);
			$output .= '<a href="'.admin_url('module/upgrade/'.$uri).'" class="btn btn-green">Upgrade</a>';
			$output .= '</li>';
		}

		$widget = array();
		$widget['title'] = t('module::module.dashboard_widget_title');
		$widget['icon'] = 'icon-cloudup';
		$widget['id'] = 'module';
		$widget['block-color'] = 'red';
		$widget['body'] = '<ul>'.$output.'<ul>';

		return $widget;
	}
}
