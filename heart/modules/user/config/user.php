<?php

return array(

	'validation' => array(
		'api' => array(
			'create' => array(
				'email' => 'required|email',
		        'first_name' =>'required|minLength:2|maxLength:40',
		        'last_name' => 'required|minLength:2|maxLength:40',
		        'password' => 'required|minLength:6'
			),

			'login'	=> array(
				'email' => 'required|email',
				'password' => 'required',
			),
		),
		'website' => array(
			'create' => array(
				'email' => 'required|email',
		        'first_name' =>'required|minLength:2|maxLength:40',
		        'last_name' => 'required|minLength:2|maxLength:40',
		        'password' => 'required|minLength:6',
		        'confpass' => 'required|equal:password'
			),
		)
	),
);