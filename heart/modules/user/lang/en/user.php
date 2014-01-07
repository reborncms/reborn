<?php

return array(
	
	'title' => array(
		'usermod' => 'User Management',
		'user' => 'User',
		'profile' => 'User Profile',
		'login' => 'User Login',
		'registration' => 'Registration',
		'activate'	=> 'Activate',
		'create' => 'User Create',
		'edit'	=> 'User Edit',
	),

	'modToolbar' => array(
		'name' => 'Create User',
		'info'	=> 'Create a new user',
	),

	//View Language
	'displayName' 	=> 'Display Name',
	'firstname'		=> 'First Name',
	'lastname'		=> 'Last Name',
	'namexample'	=> 'e.g. John Doe',
	'group'			=> 'Role',
	'email'			=> 'Email',
	'view'			=> 'View {:name} Profile',

	'lpassword' => 'Password',
	'lconfPass' => 'Confirm Password',
	'lgroup' => 'Group',
	'lusername' => 'Username',
	'lbiography' => 'Biography',
	'lcountry' => 'Country',
	'lwebsite' => 'Website',
	'lfacebook' => 'Facebook',
	'ltwitter' => 'Twitter',
	'adminpanelaccess' => 'Can access to Dashboard?',
	'gravatar' => 'Change your avatar at Gravatar',

	'temail' => 'Please enter valid Email Address',
	'tpassword' => 'Type a secure password',
	'tconfPass' => 'Confirm new password if you typed new password',
	'tusername' => 'Username must not include space, or special characters.',
	'twebsite' => 'Personal blog or webpage of this user',
	'tfacebook' => 'Enter this user\'s facebook username',
	'ttwitter' => 'Enter this user\'s twitter username',

	'menu'		=> 'Users',
	'admin' 	=> 'User Admin',
	'save'		=> 'Save',
	'delete'	=> 'Delete',
	'cancel'	=> 'Cancel',
	'Create'	=> 'Create',

	'auth' => array(
		'noequalpass' => 'Password doesn\'t match, please type again',
		'userexist' => 'Email %s is already registered, please try with another email.',
		'loginrequire' => 'Login field required',
		'activated' => 'Your account is already activated. Please try login.',
		'dunexist' => 'User was not found. Please Register to create your account.',
	),

	'login' => array(
		'success' => 'Welcome back, %s.',
		'fail' => 'Invalid Email or Password. Please try again.',
		'suspended' => 'Your account is suspended for %s minutes.',
		'banned' => 'Your account is banned.',
		'activate' => 'You need to activate your account to login.',
	),

	'logout' => 'You are successfully logout',

	'create' => array(
		'success' => 'User successfully created',
		'fail' => 'Failed to create new user',
	),

	'edit' => array(
		'success' => 'User successfully edited',
		'fail'	=> 'Failed to edit user',
	),

	'password' => array(
		'fail' => 'Password do not match',
	),

	'delete' => array(
		'success' => 'User successfully deleted.',
		'fail'	=> 'Failed to delete user.',
	),

	'profile' => array(
		'title' => 'Edit Profile',
		'success' => 'Profile successfully updated',
		'fail' => 'Failed to update your profile, please try again!',
	),

	'csrf' => 'Error occur for security reason!',

	'resentPass' => 'Password RestCode Successfully sent!',

	'activate' => array(
		'subject' => 'Activate Your Account',
		'check' => 'Check your mail for activaion code',
		'success' => 'Your account is successfully activated',
		'fail'	=> 'Fail to activate your account! Pleaes try again.',
		'already' => 'Email %s is already activated.',
		'admin' => 'User is already activated, cheating?',
	),
);