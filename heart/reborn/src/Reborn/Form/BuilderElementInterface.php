<?php

namespace Reborn\Form;

/**
 * FormBuilderElementInterface class for Reborn.
 *
 * @package Reborn\Form
 * @author Myanmar Links Professional Web Development Team
 **/

interface BuilderElementInterface
{

	/**
	 * Form Element Render method interface
	 *
	 * @param string $name Form element (field) name
	 * @param array $value Field's array value
	 */
	public function render($name, $value);

}
