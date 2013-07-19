<?php

/**
 * Event Register for Reborn CMS.
 *
 * Reborn have 4 event calling for application.
 */

return array(
		array(
				'name' => 'reborn.app.starting',
				'callback' => ''
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
			)
	);
