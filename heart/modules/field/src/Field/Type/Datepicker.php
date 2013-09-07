<?php

namespace Field\Type;

/**
 * Datepicker Field
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Datepicker extends \Field\AbstractType
{

	public function filler($default = null, $options = null)
	{
		$f = <<<FIELD
		<label for="text-options">Date Format</label>
		<div class="form-right-block">
		<input type="text" id="datepicker-options" name="options" value="$options">
		<p class="info">
			Enter Date Format for jQueryUI Datepicker
		</p>
		</div>
FIELD;

		return $f;
	}

	public function display()
	{

	}

} // END class Datepicker extends \Field\AbstractType
