<?php

/**
 * Form Builder Configuration File
 *
 * @package Reborn
 * @author Myanmar Links Professional Web Development Team
 **/

return array(

		/**
		 * Default Form Template Name.
		 * This name is key name from "templates"
		 * Template locate in BASE.'heart/reborn/src/Reborn/Form/template'
		 *
		 */
		'default' => 'sample',

		/**
		 * Templates File lists.
		 *
		 */
		'templates' => array(
				'sample' => CORES.'Form'.DS.'template'.DS.'sample.php'
			),

		/**
		 * Add new form element method
		 *  key : element method name
		 *  value :
		 */
		'elements' => array(
				// example
				//'noo' => array('NoForm', THEMES.'default'.DS.'NoForm.php')
			),
	);
