<?php

/**
 * Field Module Helper Functions
 */

/**
 * Get Module Slelect List
 */
function module_select($with_empty_option = true)
{
	static $result;

	if(! is_null($result)) {
		return $result;
	}

	$all = \Module::getAll();

	if($with_empty_option) {
		$result = array('' => '-- Select Module --');
	} else {
		$result = array();
	}

	foreach ($all as $n => $val) {
		if ($val['enabled'] and $val['allowCustomField']) {
			$result[$n] = $val['name'];
		}
	}

	return $result;
}

/**
 * Get Supported Field Type
 */
function supported_field_types($with_empty_option = true)
{
	static $types;

	if(! is_null($types)) {
		return $types;
	}

	$lists = Field::getFieldTypes();

	if($with_empty_option) {
		$types = array('' => '-- Select Type --');
	} else {
		$types = array();
	}

	foreach ($lists as $key => $list) {
		$types[$key] = ucfirst($key);
	}

	return $types;
}
