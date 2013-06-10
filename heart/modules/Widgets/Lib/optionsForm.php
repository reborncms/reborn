<?php

namespace Widgets\Lib;

use Widgets\Model\Widgets;

class optionsForm {

	public static function render($name, $id) {

		$options = \Widget::options($name);

		$form = '';

		if ($options != null) {

			$data = Widgets::where('id', $id)->pluck('options');

			$options_data = unserialize($data);

			$form .= '<div class="option-form-wrapper">';

			$form .= '<div id="error-options"></div>';

			$form .= \Form::start(adminUrl('widget/options-save'), $name.'-option-form', false, array('class' => 'form'));

			$form .= \Form::hidden('widget_id', $id);

			foreach ($options as $name => $values) {

				$form .= '<div class="form-field-wrapper">';

				$form .= \Form::label($values['label'], $name);

				$type = $values['type'];

				$value = ($options_data[$name]) ? $options_data[$name] : '';

				switch ($type) {
					case 'text':
						$form .= \Form::input($name, $value);
						break;

					case 'textarea':
						$form .= \Form::textarea($name, $value);
						break;

					case 'select':
						$form .= \Form::select($name, $values['options'], $value);
						break;
					
					default:
						$form .= \Form::input($name, $value);
						break;
				}

				$form .= '</div>'; // end of form-field-wrapper

			}

			$form .= '<div class="option-form-action">';

			$form .= \Form::submit('save_option', 'Save', array('class' => 'button button-green', 'id' => 'save_option_btn'));

			$form .= \Form::button('cancel', 'Cancel', 'button', array('class' => 'button button-red', 'id' => 'option-cancel-btn'));

			$form .= '</div>'; // end of option-form-action 

			$form .= \Form::end();

			$form .= '</div>'; // end of option-form-wrapper

		}

		return $form;

	}

}