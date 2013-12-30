<?php

/**
 * Route Middleware Register At Here.
 * Each middleware callback is default invoked with two argument
 *  - Request
 *  - Route (Current match route)
 * If middleware have other parameter, it will pass as three argument with array
 *
 * Example :: Route::middleware($name, $callback);
 * <code>
 * 	// Register with Closure without parameters
 * 	Route::middleware('hello', function($req, $route)) {
 * 		echo 'Hello World!';
 * 	});
 *
 * 	// Register with Closure without other parameters
 * 	Route::middleware('hello_name', function($req, $route, $params)) {
 * 		echo 'Hello '.$params['name'];
 * 	});
 *  // At Route register
 *  Route::get('test', 'Test\Test::index', 'test')->before('hello_name:name=Nyan');
 *  // Set Multiple Parameter at Route
 *  Route::get('test', 'Test\Test::index', 'test')
 * 			->before('hello_name:name=Nyan,age=26,job=WebDeveloper');
 * </code>
 *
 *  // Register with Object
 *  Route::middleware('access', 'Watcher');
 *  // Watcher class must be have run() method.
 */

Route::middleware('check_access', function($request, $route, $param){

	if (isset($param['rule'])) {
		$rule = $param['rule'];
	} else {
		// Replace "\" with "." from controller
		// eg: "admin\blog" to "admin.blog"
		$ctrl = str_replace('\\', '.',strtolower($route->controller));
		$rule = $ctrl.'.'.$route->action;
	}

	if (!\Auth::hasAccess($rule)) {
		return \Response::clueless();
	}
});
