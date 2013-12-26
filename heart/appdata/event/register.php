<?php

/**
 * Event Register for Reborn CMS.
 *
 * Reborn have 8 event calling for application.
 */

return array(
		array(
				'name' => 'reborn.app.starting',
				'callback' => function(){
					// Unregister Munee's Type
					\Munee\Asset\Registry::unRegister(array('css', 'less', 'scss'));
					/**
					 * Register the CSS Asset Class with the extensions .css and .less
					 */
					\Munee\Asset\Registry::register(array('css', 'less', 'scss'),
					function (\Munee\Request $Request) {
					    return new \Reborn\Asset\Extensions\Type\Css($Request);
					});
				}
			),
		array(
				'name' => 'reborn.app.startroute',
				'callback' => ''
			),
		array(
				'name' => 'reborn.app.routeNotFound',
				'callback' => ''
			),
		array(
				'name' => 'reborn.app.ending',
				'callback' => ''
			),
		array(
				'name' => 'reborn.controller.process.starting',
				'callback' => ''
			),
		array(
				'name' => 'reborn.controller.process.ending',
				'callback' => ''
			),
		array(
				'name' => 'reborn.app.profiling',
				'callback' => function ($response) {
					return Registry::get('app')->profiler->output($response);
				}
			),
		array(
				'name' => 'reborn.app.locale_change',
				'callback' => ''
			)
	);
