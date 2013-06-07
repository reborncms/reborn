<?php

namespace Reborn\Form;

use Reborn\Config\Config;
use Reborn\Connector\DB\DBManager as DB;

/**
 * Validation class for Reborn
 *
 * @package Reborn\Form
 * @author Myanmar Links Professional Web Development Team
 **/
class Validation
{
    /**
     * Validation inputs attributes
     *
     * @var array
     **/
    protected $inputs = array();

    /**
     * Validation rules array by default
     *
     * @var array
     **/
    protected $rules = array();

    /**
     * Validation errors array
     *
     * @var array
     **/
    protected $errors = array();

    /**
     * Accepted validation method list by Default
     *
     * 01) - max [Input value's Maximum is given value]
     * 02) - min [Input value's Minimum is given value]
     * 03) - maxLength [Input value's string length Maximum is given value]
     * 04) - minLength [Input value's string length Minimum is given value]
     * 05) - required [Input value is required]
     * 06) - alpha [Input value is Alpha(a-zA-Z)]
     * 07) - alphaNum [Input value is AlphaNumeric(a-zA-Z0-9)]
     * 08) - alphaDash [Input value is AlphaNumeric, Dash and Underscore(a-zA-Z0-9-_)]
     * 09) - alphaDashDot [Input value is alphaDash and Dot(a-zA-Z0-9-_.)]
     * 10) - numeric [Input value is Numeric(0-9)]
     * 11) - integer [Input value is Integer value]
     * 12) - email [Input value is Valid Email]
     * 13) - url [Input value is Valid URL]
     * 14) - ip [Input value is Valid IP address]
     * 15) - between [Input value is Between first and last value eg:between:5,7]
     * 16) - equal [Input value is Equal with given value or field name eg:equal:9]
     * 17) - color [Input value is valid 6-digits color hexadecimal code eg:#efefef]
     * 18) - patterm [Input value is valid regex pattern eg:"/\d{4}-\d{2}-\d{2}/"]
     * 19) - unique [Input value is unique in mysql database. eg: unique:tablename.keyname]
     *
     * @var array
     **/
    protected $methods = array(
                'max',
                'min',
                'maxLength',
                'minLength',
                'required',
                'alpha',
                'alphaNum',
                'alphaDash',
                'alphaDashDot',
                'numeric',
                'integer',
                'email',
                'url',
                'ip',
                'between',
                'equal',
                'color',
                'pattern',
                'unique'
            );

    /**
     * New Method list (callback) added by user
     *
     * @var array
     **/
    protected $addedMethods = array();

    /**
     * Construct the Validation Class
     * Example :
     * <code>
     *      // Input field
     *      //eg: array('name' => 'Reborn', 'age' => '25', 'address' =>'Yangon-Myanmar')
     *      $inputs = array( // Get the all input from form submit
     *              'name' => Input::get('name'),
     *              'age' => Input::get('age'),
     *              'address' => Input::get('address')
     *          );
     *      $rules = array(
     *              'name' => 'required',
     *              'age' => 'between:18,30',
     *              'address' => 'minLength:10|alphaDashDot'
     *          );
     *      $v = new Validation($inputs, $rules);
     *      if($v->valid())
     *      {
     *          echo 'Input is Valid';
     *      }
     *      else
     *      {
     *          $errs = $v->getErrors();
     *          foreach($errs as $e)
     *          {
     *              echo '$e'.'<br>';
     *          }
     *      }
     * </code>
     *
     * @param array $inputs Input fields
     * @param array $rules Rules for Input field
     * @return void
     **/
    public function __construct($inputs = array(), $rules)
    {
        $this->inputs = $inputs;

        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        $this->rules = $rules;
    }

    /**
     * Static Method for Validation Instance
     *
     * @param array $inputs Input fields
     * @param array $rules Rules for Input field
     * @return Reborn\Cores\Validation
     **/
    public static function create($inputs = array(), $rules)
    {
        return new static($inputs, $rules);
    }

    /**
     * Add new validation rule.
     *
     * @param string $name Validation name
     * @param string $msg Error message for this rule
     * @param Closure $callback Callback function for rule
     * @return void
     **/
    public function addRule($name, $msg, $callback)
    {
        $this->addedMethods[$name]['call'] = $callback;
        $this->addedMethods[$name]['msg'] = $msg;
    }

    /**
     * Check the validation is valid or not.
     *
     * @return boolean
     **/
    public function valid()
    {
        foreach ($this->rules as $input => $rules) {
            if (is_array($rules)) {
                foreach ($rules as $rule) {
                    $this->compute($input, $rule);
                }
            } else {
                $this->compute($input, $rules);
            }
        }

        if (count($this->errors) == 0) {
            return true;
        }

        return false;
    }

    /**
     * Opposite of valid method
     *
     * @return boolean
     **/
    public function fail()
    {
        return !$this->valid();
    }

    /**
     * Get the Validation Errors. If doesn't have any validation error, return null
     *
     * @return array|null
     **/
    public function getErrors()
    {
        return (count($this->errors) !== 0) ? $this->errors : null;
    }

    /**
     * Compute the Input and his rule is valid or not.
     *
     * @param string $input Input field name
     * @param string $rule Validation rule for given input field
     * @return void
     **/
    protected function compute($input, $rule)
    {
        list($rule, $param) = $this->ruleParser($rule);

        if (in_array($rule, $this->methods)) {
            // First param is Input value and second is Rule's value
            $args = array($this->inputs[$input], $param);

            if (! call_user_func_array(array($this, 'valid'.ucfirst($rule)), $args)) {
                $this->setError($rule, $input, $param);
            }

        } elseif (array_key_exists($rule, $this->addedMethods)) {
            // First param is Input value and second is Rule's value
            $args = array($this->inputs[$input], $param);
            $method = $this->addedMethods[$rule]['call'];

            if (! call_user_func_array($method, $args)) {
                $this->setError($rule, $input, $param, true);
            }
        }
    }

    /**
     * Parse the given rule string to rule and rule's parameter.
     * Have a char ':' at given rule, parse the rule name and param.
     * If doesn't have ':', return rule name and param is null.
     *
     * @param string $rule
     * @return array
     **/
    protected function ruleParser($rule)
    {
        if (false !== ($pos = strpos($rule, ':'))) {
            return array(substr($rule, 0, $pos), substr($rule, $pos +1));
        }

        return array($rule, null);
    }

    /**
     * set the validation error.
     * This method is call from $this->compute() when the validation is invalid.
     *
     * @param string $rule Name of the validation rule
     * @param string $key Input field name.
     * @param string $arg Argumetn string for rule (eg: max,min etc:)
     * @param boolean $addedRule This error from addedMethods or default Methods
     * @return void
     **/
    protected function setError($rule, $key, $arg, $addedRule = false)
    {
        // Check messsageFor{RuleName} method.
        $messageMethod = 'messageFor'.ucfirst($rule);

        if ( method_exists($this, $messageMethod) ) {
            $this->errors[$key] = $this->{$messageMethod}($key, $arg);
        } else {
            if ($addedRule) {
                $msg = $this->addedMethods[$rule]['msg'];
            } else {
                \Translate::load('validation');
                $msg = \Translate::get('validation.'.$rule);
            }
            $parseMsg = str_replace('{key}', $key, $msg);
            $parseMsg = str_replace('{'.$rule.'}', $arg, $parseMsg);
            $this->errors[$key] = $parseMsg;
        }
    }

    /* =============== Some of Message Setter Method Lists =================== */

    protected function messageForBetween($key, $args)
    {
        \Translate::load('validation');
        $msg = \Translate::get('validation.between');
        $args = explode(',', $arg);

        $parseMsg = str_replace('{key}', $key, $msg);

        return str_replace(array('{first}', '{last}'), $args, $parseMsg);
    }

    protected function messageForUnique($key, $args)
    {
        $value = $this->inputs[$key];

        \Translate::load('validation');
        $msg = \Translate::get('validation.unique');

        return str_replace(array('{key}', '{value}'), array(ucfirst($key), $value), $msg);
    }


    /* =============== Validation Method Lists =================== */

    protected function validMax($value, $max)
    {
        return ($value <= (int)$max);
    }

    protected function validMin($value, $min)
    {
        return ($value >= (int)$min);
    }

    protected function validMaxLength($value, $max)
    {
        return (strlen($value) <= (int)$max);
    }

    protected function validMinLength($value, $min)
    {
        return (strlen($value) >= (int)$min);
    }

    protected function validRequired($value)
    {
        if (is_string($value) and trim($value) == '') {
            return false;
        } elseif(is_null($value)) {
            return false;
        }

        return true;
    }

    protected function validAlpha($value)
    {
        return preg_match("#^([a-zA-Z]+)$#", $value);
    }

    protected function validAlphaNum($value)
    {
        return preg_match("#^([a-zA-Z0-9]+)$#", $value);
    }

    protected function validAlphaDash($value)
    {
        return preg_match("#^([a-zA-Z0-9_-]+)$#", $value);
    }

    protected function validAlphaDashDot($value)
    {
        return preg_match("#^([a-zA-Z0-9\.\_-]+)$#", $value);
    }

    protected function validNumeric($value)
    {
        return is_numeric($value);
    }

    protected function validInteger($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validUrl($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    protected function validIp($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    protected function validBetween($value, $bet)
    {
        list($first, $last) = explode(',', $bet);

        return (((int)$first < (int)$value) and ((int)$value < (int)$last));
    }

    /**
     * valid Equal may be two way.
     * First is Value is equal with number (eg: equal:100)
     * Second is Value is equal with input field password
     * (eg: equal:password)
     */
    protected function validEqual($value, $eq)
    {
        if (isset($this->inputs[$eq])) {
            return ($value === $this->inputs[$eq]);
        } else {
            return ($value === $eq);
        }
    }

    /**
     * Valid 6-digit hexadecimal color code (eg: #e3e3e3)
     */
    protected function validColor($value)
    {
        return preg_match('/^#[a-f0-9]{6}$/', $value);
    }

    protected function validPattern($value, $pattern)
    {
        return preg_match($pattern, $value);
    }


} // END class Validation
