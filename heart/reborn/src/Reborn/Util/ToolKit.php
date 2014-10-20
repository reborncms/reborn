<?php

namespace Reborn\Util;

/**
 * Utilities ToolKit Suite Class.
 *
 * @package Reborn
 * @author Myanmar Links Professional Web Development
 **/
class ToolKit
{
    /**
     * Encode php value (mixed) to javascript value.
     *
     * @param  mixed  $value
     * @return string
     **/
    public static function jsEncode($value)
    {
        $js = array();

        if (is_string($value)) {
            return "'".static::jsquote($value)."'";
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_numeric($value)) {
            return $value;
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_array($value)) {
            if (empty($value)) return '';

            $vals = array();
            // For associate array
            if ( ($n = count($value)) > 0 && array_keys($value) !== range(0,$n-1) ) {
                foreach($value as $k => $v)
                    $vals[] = static::jsquote($k).":".static::jsEncode($v);

                return '{'.implode(',',$vals).'}';
            } else {
                foreach($value as $v)
                    $vals[] = static::jsEncode($v);

                return '['.implode(',',$vals).']';
            }
        } elseif (is_object($value)) {
            return static::jsEncode(get_object_vars($value));
        }

        return '';
    }

    /**
     * Quote a string for javascript.
     *
     * @param  sting  $string
     * @return string
     **/
    protected static function jsquote($string)
    {
        return strtr($string,array("\n"=>'\n',"\r"=>'\r', "\t"=>'\t','"'=>'\"','\''=>'\\\'','\\'=>'\\\\','</'=>'<\/'));
    }

    /**
     * Array Sorting by key's value.
     * <code>
     * 		$data = [
     *		 	['name' => 'Nyan', 'age' => 26],
     *		 	['name' => 'Lynn', 'age' => 24],
     *		 	['name' => 'Htut', 'age' => 27],
     *		 	['name' => 'John', 'age' => 18],
     *	 	];
     *   	dump(ToolKit::sortBy($data, 'name'));
     *    	// Output result
     *     [
     *     		['name' => 'Htut', 'age' => 27],
     *		 	['name' => 'John', 'age' => 18],
     *    		['name' => 'Lynn', 'age' => 24],
     *    		['name' => 'Nyan', 'age' => 26],
     *	 	];
     * </code>
     *
     * @param  array  $data
     * @param  string $by
     * @param  string $dir  Sorting direction. Default is "asc".
     * @return array
     **/
    public static function sortBy(array $data, $by, $dir = 'asc')
    {
        // "sort_lists" is use if given sorting key "by" isset data.
        // "appends" is use for given sorting key is not isset.
        // First we sorting by "sort_lists" data by "dir" and then
        // add "appends" data lists at ending of sorting lists.
        $results = $sort_lists = $appends = array();

        foreach ($data as $k => $v) {
            if (! is_null($get = array_get($v, $by)) ) {
                $sort_lists[$k] = $get;
            } else {
                $appends[] = $data[$k];
            }
        }

        ('asc' === $dir) ? asort($sort_lists) : arsort($sort_lists);

        foreach ($sort_lists as $k => $v) {
            $results[] = $data[$k];
        }

        foreach ($appends as $d) {
            $results[] = $d;
        }

        return $results;
    }

    /**
     * Ger month list array from language file
     * 
     * @return  array
     */
    public static function months()
    {
        return \Translate::get('datetimes.months');
    }
}
