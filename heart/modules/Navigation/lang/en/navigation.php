<?php
// Language file for navigation module

return array(

	'title' => 'Navigation Manager',

	'desc' => 'Manage navigation link and group for your website.'

	'menu' => 'Navigation',

	'link'	=> array(
		'title'		=> 'Navigation',
		'save'		=> 'Save',
		'add'		=> 'Add new link',
		'edit'		=> 'Edit',
		'delete'	=> 'Delete',
		'no_links'	=> 'Navigation links does not have in this group!',
	),

	'group'	=> array(
		'title'		=> 'Navigation Group "%s"',
		'add'		=> 'Add new group',
		'edit'		=> 'Edit group',
		'delete'	=> 'Delete group',
		'no_have'	=> 'No navigation group have now!',
	),

	'labels' => array(
		'title' 		=> 'Navigation Title',
		'type' 			=> 'Navigation Type',
		'uri' 			=> 'Uri',
		'page' 			=> 'Page',
		'module' 		=> 'Module',
		'url' 			=> 'URL',
		'position'		=> 'Navigation Position',
		'group'			=> 'Navigation Group',
		'target'		=> 'Link Target',
		'class'			=> 'Class for link',
		'target_normal'	=> 'Current Window (Default)',
		'target_new_win'=> 'New Window (_blank)',
	),

	'group_labels' => array(
		'name' => 'Name',
		'slug' => 'Slug',
	),

	'message'	=> array(
		'csrf_error'		=> 'CSRF Token is invalid!',
		'create_success'	=> 'Navigation link is successfully created.',
		'create_error'		=> 'Error occur to create navigation link.',
		'edit_success'		=> 'Navigation link is successfully edited.',
		'edit_error'		=> 'Error occur to edit navigation link.',
		'delete_success'	=> 'Navigation link is successfully deleted.',
		'delete_error'		=> 'Error occur to delete navigation link.',
		'exists'			=> 'Group name is already exists!',
		'required'			=> 'Group name is required!',
		'group_create_success' => 'Group %s is succcessfully created.',
		'group_create_error' => 'Error occur to create group %s!',
	),

	'toolbar'	=> array(
			'link' => 'Links',
			'link_info' => 'View Navigation Links',
			'group' => 'Group',
			'group_info' => 'View Navigation Group'
		)
);

// end of navigation.php
