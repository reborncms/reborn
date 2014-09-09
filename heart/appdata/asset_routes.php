<?php

Route::group('assets', function(){

	Route::get('less/{*:path}', function($app, $path){
		$resolver = new \Reborn\Asset\MuneeResolver($path, 'less');
		echo $resolver->run();
		exit;
	})->isAsset();

	// Route for styles
	Route::get('styles/{*:path}', function($app, $path){
		$resolver = new \Reborn\Asset\MuneeResolver($path);
		echo $resolver->run();
		exit;
	})->isAsset();

	// Route for scripts
	Route::get('scripts/{*:path}', function($app, $path){
		$resolver = new \Reborn\Asset\MuneeResolver($path, 'js');
		echo $resolver->run();
		exit;
	})->isAsset();

	// Route for image
	Route::get('images/{*:path}', function($app, $path){
		$resolver = new \Reborn\Asset\MuneeResolver($path, 'img');
		echo $resolver->run();
		exit;
	})->isAsset();

	// Group for global assets
	Route::group('global/', function(){
		Route::get('less/{*:path}', function($app, $path){
			$resolver = new \Reborn\Asset\MuneeResolver($path, 'less');
			$resolver->isGlobal();
			echo $resolver->run();
			exit;
		})->isAsset();

		Route::get('styles/{*:path}', function($app, $path){
			$resolver = new \Reborn\Asset\MuneeResolver($path);
			$resolver->isGlobal();
			echo $resolver->run();
			exit;
		})->isAsset();

		// Route for scripts
		Route::get('scripts/{*:path}', function($app, $path){
			$resolver = new \Reborn\Asset\MuneeResolver($path, 'js');
			$resolver->isGlobal();
			echo $resolver->run();
			exit;
		})->isAsset();

		// Route for image
		Route::get('images/{*:path}', function($app, $path){
			$resolver = new \Reborn\Asset\MuneeResolver($path, 'img');
			$resolver->isGlobal();
			echo $resolver->run();
			exit;
		})->isAsset();
	});
});

// Group for admin theme assets
Route::group('assets/a/', function(){
	Route::get('less/{*:path}', function($app, $path){
		$resolver = new \Reborn\Asset\MuneeResolver($path, 'less');
		$resolver->isAdmin();
		echo $resolver->run();
		exit;
	})->isAsset();

	Route::get('styles/{*:path}', function($app, $path){
		$resolver = new \Reborn\Asset\MuneeResolver($path);
		$resolver->isAdmin();
		echo $resolver->run();
		exit;
	})->isAsset();

	// Route for scripts
	Route::get('scripts/{*:path}', function($app, $path){
		$resolver = new \Reborn\Asset\MuneeResolver($path, 'js');
		$resolver->isAdmin();
		echo $resolver->run();
		exit;
	})->isAsset();

	// Route for image
	Route::get('images/{*:path}', function($app, $path){
		$resolver = new \Reborn\Asset\MuneeResolver($path, 'img');
		$resolver->isAdmin();
		echo $resolver->run();
		exit;
	})->isAsset();
});
