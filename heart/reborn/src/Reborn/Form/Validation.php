<?php

namespace Reborn\Form;

use Closure;
use Reborn\Connector\DB\DBManager as DB;
use Symfony\Component\HttpFoundation\File\File;

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
     * Error message list for register by user
     *
     * @var array
     **/
    protected $messages = array();

    /**
     * Validation errors array
     *
     * @var array
     **/
    protected $errors = array();

    /**
     * Failure inputs in validation
     *
     * @var array
     **/
    protected $fail_rules = array();

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
     * 11) - emptyOrNumeric [Input value empty string or Numeric(0-9)]
     * 12) - integer [Input value is Integer value]
     * 13) - float [Input value is Float value]
     * 14) - email [Input value is Valid Email]
     * 15) - url [Input value is Valid URL]
     * 16) - ip [Input value is Valid IP address]
     * 17) - between [Input value is Between first and last value eg:between:5,7]
     * 18) - equal [Input value is Equal with given value or field name eg:equal:9]
     * 19) - color [Input value is valid 6-digits color hexadecimal code eg:#efefef]
     * 20) - patterm [Input value is valid regex pattern eg:"/\d{4}-\d{2}-\d{2}/"]
     * 21) - unique [Input value is unique in mysql database. eg: unique:tablename.keyname]
     * 22) - type [Input value's type must be $type. eg: type:string [string,array]
     * 23) - boolean [Input value type must be boolean. [0, 1, on, off, true, false]
     * 24) - image [Input value must be allow Image type. (eg: image:jpg,png)]
     * 25) - fileType [Input value must be allow File Type. (eg: fileType:pdf,zip)]
     * 26) - fileSize [Input value's maximum file size limit (eg: fileSize:2MB)]
     * 27) - before [Input date must be before date (eg: before:10/22/2013)]
     * 28) - after [Input date must be after date (eg: after:10/22/2013)]
     * 29) - honeypot [Spam filter honey pot filed's validation]
     * 30) - lat [Valid rule for latitude -90 to +90]
     * 31) - long [Valid rule for longitude -180 to +180]
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
                'emptyOrNumeric',
                'integer',
                'float',
                'email',
                'url',
                'ip',
                'between',
                'equal',
                'color',
                'pattern',
                'unique',
                'type',
                'boolean',
                'image',
                'fileType',
                'fileSize',
                'before',
                'after',
                'honeypot',
                'lat',
                'long'
            );

    /**
     * New Method list (callback) added by user.
     * Extended rules
     *
     * @var array
     **/
    protected $extended = array();

    /**
     * Static Method for Validation Instance
     *
     * @param  array                   $inputs Input fields
     * @param  array                   $rules  Rules for Input fields
     * @return \Reborn\Form\Validation
     **/
    public static function create($inputs = array(), $rules)
    {
        return new static($inputs, $rules);
    }

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
     * @param  array $inputs Input fields
     * @param  array $rules  Rules for Input fields
     * @return void
     **/
    public function __construct($inputs = array(), $rules)
    {
        // Event for Validator rule Extended
        \Event::call('reborn.validator.start', array($this));

        $this->inputs = $inputs;

        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        $this->rules = $rules;
    }

    /**
     * Add validation rule.
     *
     * @param  string                  $name
     * @param  array                   $rules
     * @return \Reborn\Form\Validation
     **/
    public function rule($name, array $rules)
    {
        $this->rules[$name] = $rules;

        return $this;
    }

    /**
     * Add new rules or change base on condition.
     *
     * @param  string                  $name
     * @param  array                   $rules
     * @param  \Closure                $condition
     * @param  boolean                 $merge
     * @return \Reborn\Form\Validation
     **/
    public function when($name, array $rules, Closure $condition, $merge = true)
    {
        if (call_user_func($condition)) {
            if ($merge and isset($this->rules[$name])) {
                $this->rules[$name] = array_merge($this->rules[$name], $rules);
            } else {
                $this->rules[$name] = $rules;
            }
        }

        return $this;
    }

    /**
     * Set Custom Message for Validation Rules
     *
     * @param  string                  $for     Input key name
     * @param  string                  $message
     * @return \Reborn\Form\Validation
     **/
    public function message($for, $message)
    {
        $this->messages[$for] = $message;

        return $this;
    }

    /**
     * Extend new validation rule.
     *
     * @param  string                  $name     Validation name
     * @param  string                  $msg      Error message for this rule
     * @param  Closure                 $callback Callback function for rule
     * @return \Reborn\Form\Validation
     **/
    public function extend($name, $message, $callback)
    {
        $this->extended[$name]['call'] = $callback;
        $this->extended[$name]['msg'] = $message;
    }

    /**
     * Alias method of extend()
     *
     **/
    public function addRule($name, $msg, $callback)
    {
        return $this->extend($name, $msg, $callback);
    }

    /**
     * Get Input Value
     *
     * @param  string|null $key Key name or null for all
     * @return mixed
     **/
    public function getInput($key = null)
    {
        if (is_null($key)) {
            return $this->inputs;
        } else {
            if ($this->hasInput($key)) {
                return $this->inputs[$key];
            }
        }

        return null;
    }

    /**
     * Has Input Values
     *
     * @param  string  $key Key name
     * @return boolean
     **/
    public function hasInput($key)
    {
        if (isset($this->inputs[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Check the validation is valid or not.
     *
     * @return boolean
     **/
    public function valid()
    {
        foreach ($this->rules as $input => $rules) {
            $rules = (array) $rules;

            foreach ($rules as $rule) {
                // Exits in require fail
                if (! $this->compute($input, $rule) ) {
                    break;
                }
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
     * Get the Validation Errors.
     * If doesn't have any validation error, return null
     *
     * @return \Reborn\Form\ValidationError
     **/
    public function getErrors()
    {
        return new ValidationError($this->errors);
    }

    /**
     * Get failed inputs
     *
     * @return array
     **/
    public function failures()
    {
        return $this->fail_rules;
    }

    /**
     * Compute the Input and his rule is valid or not.
     *
     * @param  string  $input Input field name
     * @param  string  $rule  Validation rule for given input field
     * @return boolean
     **/
    protected function compute($input, $rule)
    {
        list($rule, $param) = $this->ruleParser($rule);

        // Check given input key is isset or not and have rule require
        if (!isset($this->inputs[$input]) and in_array('required', $this->rules[$input])) {
            $this->setError('required', $input, $param);

            return false;
        } else {
            if (in_array($rule, $this->methods)) {
                // First param is Input value and second is Rule's value
                $args = array($this->inputs[$input], $param);

                if (! call_user_func_array(array($this, 'valid'.ucfirst($rule)), $args)) {
                    $this->setError($rule, $input, $param);
                }

            } elseif (array_key_exists($rule, $this->extended)) {
                // First param is Input value and second is Rule's value
                $args = array($this->inputs[$input], $param, $this);
                $method = $this->extended[$rule]['call'];

                if (! call_user_func_array($method, $args)) {
                    $this->setError($rule, $input, $param, true);
                }
            }
        }

        return true;
    }

    /**
     * Parse the given rule string to rule and rule's parameter.
     * Have a char ':' at given rule, parse the rule name and param.
     * If doesn't have ':', return rule name and param is null.
     *
     * @param  string $rule
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
     * @param  string  $rule      Name of the validation rule
     * @param  string  $key       Input field name.
     * @param  string  $arg       Argumetn string for rule (eg: max,min etc:)
     * @param  boolean $addedRule This error from addedMethods or default Methods
     * @return void
     **/
    protected function setError($rule, $key, $arg, $addedRule = false)
    {
        // Check messsageFor{RuleName} method.
        $messageMethod = 'messageFor'.ucfirst($rule);

        if ( method_exists($this, $messageMethod) ) {
            $this->errors[$key] = $this->{$messageMethod}(\Str::title($key), $arg);
        } else {
            if ($addedRule) {
                $msg = $this->extended[$rule]['msg'];
                // Message is callback
                if ($msg instanceof Closure) {
                    $msg = $msg($key, $arg, $rule);
                }
            } else {
                $msg = null;
                if (isset($this->messages[$key])) {

                    $msg = $this->messages[$key];
                }

                if (is_null($msg)) {
                    \Translate::load('validation');
                    $msg = \Translate::get('validation.'.$rule);
                }
            }

            $parseMsg = str_replace('{key}', \Str::title($key), $msg);
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

    protected function messageForImage($key, $types)
    {
        $msg = \Translate::get('validation.image');

        return str_replace(array('{key}', '{types}'), array(ucfirst($key), $types), $msg);
    }

    protected function messageForFileType($key, $types)
    {
        \Translate::load('validation');
        $msg = \Translate::get('validation.fileType');

        return str_replace(array('{key}', '{types}'), array(ucfirst($key), $types), $msg);
    }

    protected function messageForFileSize($key, $size)
    {
        \Translate::load('validation');
        $msg = \Translate::get('validation.fileSize');

        return str_replace(array('{key}', '{size}'), array(ucfirst($key), $size), $msg);
    }

    protected function messageForBefore($key, $before)
    {
        list(, $date) = $this->prepareDateFormat(null , $before);

        $msg = \Translate::get('validation.before');

        return str_replace(array('{key}', '{date}'), array(ucfirst($key), $date), $msg);
    }

    protected function messageForAfter($key, $after)
    {
        $list = explode('@', $after);

        try {
            list(, $date) = $this->prepareDateFormat(null , $after);
        } catch (\InvalidArgumentException $e) {
            if (isset($this->inputs[$list[0]])) {
                $date = '"'.\Str::title($list[0]).'"';
            }
        }

        $msg = \Translate::get('validation.after');

        return str_replace(array('{key}', '{date}'), array(ucfirst($key), $date), $msg);
    }

    /* =============== Validation Method Lists =================== */

    protected function validMax($value, $max)
    {
        return ($value <= (int) $max);
    }

    protected function validMin($value, $min)
    {
        return ($value >= (int) $min);
    }

    protected function validMaxLength($value, $max)
    {
        return (strlen($value) <= (int) $max);
    }

    protected function validMinLength($value, $min)
    {
        return (strlen($value) >= (int) $min);
    }

    protected function validRequired($value)
    {
        if (is_string($value) and trim($value) == '') {
            return false;
        } elseif (is_null($value)) {
            return false;
        } elseif ($value instanceof File) {
            return (string) $value->getPath() != '';
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

    protected function validEmptyOrNumeric($value)
    {
        if ( empty($value) || $value === '' || is_null($value) ) {
            return true;
        }

        return is_numeric($value);
    }

    protected function validInteger($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validFloat($value)
    {
        return is_float($value);
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

        return (((int) $first < (int) $value) and ((int) $value < (int) $last));
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

    protected function validUnique($value, $table_key)
    {
        list($table, $key) = explode('.', $table_key);

        $result = DB::table($table)->where($key, $value)->first();

        if (is_null($result)) {
            return true;
        }

        return false;
    }

    protected function validType($value, $type)
    {
        switch ($type) {
            case 'string':
                return is_string($value);
                break;

            case 'array':
                return is_array($value);
                break;

            default:
                throw new \Exception("Validation Type {$type} is not supported type.");
                break;
        }
    }

    protected function validBoolean($value)
    {
        $bool = array('1', '0', 'true', 'false', 'on', 'off');

        return in_array(strtolower($value), $bool);
    }

    /**
     * Valid method for Honeypot
     *
     * @param  string  $value
     * @return boolean
     **/
    protected function validHoneypot($value)
    {
        return ($value === '');
    }

    /**
     * Valid method for Latitude
     *
     * @param  string  $value
     * @return boolean
     **/
    protected function validLat($value)
    {
        return (((float) $value <= (float) 90.0) and ((float) $value >= (float) -90.0));
    }

    /**
     * Valid method for Longitude
     *
     * @param  string  $value
     * @return boolean
     **/
    protected function validLong($value)
    {
        return (((float) $value <= (float) 180.0) and ((float) $value >= (float) -180.0));
    }

    /**
     * Check Image validation
     *
     * @param  File|null   $value
     * @param  string|null $types
     * @return boolean
     **/
    protected function validImage($value, $types = null)
    {
        if (is_null($types)) {
            $types = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
        } else {
            $types = explode(',', $types);
        }

        return $this->validFileType($value, $types);
    }

    /**
     * Check File MIME Types
     *
     * @param  File|null    $value
     * @param  string|array $types
     * @return boolean
     **/
    protected function validFileType($value, $types)
    {
        if (! $value instanceof File or $value->getPath() == '') {
            return true;
        }

        if (is_string($types)) {
            $types = explode(',', $types);
        }

        return in_array($value->guessExtension(), $types);
    }

    /**
     * Check File MaxSize
     *
     * @param  File|null   $value
     * @param  string|null $types
     * @return boolean
     **/
    protected function validFileSize($value, $size)
    {
        if (! $value instanceof File or $value->getPath() == '') {
            return true;
        }

        $size =formatSizeToBytes($size);

        return ($value->getClientSize() < $size);
    }

    /**
     * Check value date must be before Rule date
     *
     * @param  string  $value
     * @param  string  $before
     * @return boolean
     **/
    protected function validBefore($value, $before)
    {
        try {
            list($value, $before) = $this->prepareDateFormat($value, $before);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return (strtotime($value) < strtotime($before));
    }

    /**
     * Check value date must be after Rule date
     *
     * @param  string  $value
     * @param  string  $after
     * @return boolean
     **/
    protected function validAfter($value, $after)
    {
        try {
            list($value, $after) = $this->prepareDateFormat($value, $after);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return (strtotime($value) > strtotime($after));
    }

    /**
     * Prepare Date format for Before, After Rules
     *
     * @param  string|null $value
     * @param  string      $rules
     * @return array
     **/
    protected function prepareDateFormat($value, $rules)
    {
        $list = explode('@', $rules);
        $date = $list[0];
        $format = isset($list[1]) ? $list[1] : 'm-d-Y';
        if (isset($this->inputs[$date])) {
            $date = $this->inputs[$date];
            if ('' === $date) {
                throw new \InvalidArgumentException("Date field is Blank!");
            }
        }

        $rule_date = \DateTime::createFromFormat($format, $date);

        if (is_null($value)) {
            $value_date = null;
        } else {
            $value_date = \DateTime::createFromFormat($format, $value);
            $value_date = $value_date->format('m/d/Y');
        }

        return array($value_date, $rule_date->format('m/d/Y'));
    }

} // END class Validation
