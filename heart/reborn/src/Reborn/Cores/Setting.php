<?php

namespace Reborn\Cores;

use Reborn\Connector\DB\DBManager as DB;

/**
 * Setting Class for Reborn.
 * Setting from db_setting table.
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class Setting
{
    /**
     * Setting items array
     *
     * @var array
     **/
    protected static $items = array();

    /**
     * Setting items's system group
     *
     * @var array
     **/
    protected static $systemItems = array();

    /**
     * Settting items's module group
     *
     * @var array
     **/
    protected static $moduleItems = array();

    /**
     * Variable for Setting table
     *
     * @var string
     **/
    protected static $_table = 'settings';

    /**
     * Marker for initialize function
     *
     * @var boolean
     */
    protected static $started = false;

    /**
     * Initialize method for setting class
     *
     */
    public static function initialize()
    {
        if (static::$started) {
            return true;
        } else {
            static::findAllFromDB();
        }
    }

    /**
     * Get the setting item with key name (slug Column in db table).
     *
     * @param string $key Key(slug) name for setting item
     * @return mixed If item not found, return null.
     */
    public static function get($key)
    {
        if (isset(static::$items[$key])) {
            return static::$items[$key];
        } else {
            return null;
        }
    }

    /**
     * Check the given key is exists or not.
     *
     * @param string $key
     * @return boolean
     **/
    public static function has($key)
    {
        return isset(static::$items[$key]);
    }

    /**
     * Set the data to setting table.
     *
     * @param string $key Key name from the setting table
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        if (isset(static::$items[$key])) {
            $update = DB::table(static::$_table)
                                ->where('slug', '=', $key)
                                ->update(array('value' => $value));
            if ($update) {
                static::$items[$key] = $value;
            }
        }
    }

    /**
     * Add the new setting data to setting table.
     *
     * @param array $data Setting data array
     * @return boolean
     */
    public static function add($data)
    {
        if (DB::table(static::$_table)->insert($data)) {
            return true;
        }
        return false;
    }

    /**
     * Check given key's real value is same with given value.
     * example:
     * <code>
     *      // setting item site_title is "My Site" in the DB
     *      Setting::is('site_title', "My Site"); // return true;
     *      Setting::is('site_title', "Hello World"); // return false;
     * </code>
     *
     * @param string $key Name of the setting item
     * @param mixed $thinkValue Think value for given setting item name
     * @return boolean
     */
    public static function is($key, $thinkValue)
    {
        $realValue = static::get($key);

        return ($thinkValue == $realValue);
    }

    /**
     * Get the Setting list from Modules
     *
     * @return array
     **/
    public static function getFromModules()
    {
        $modules = \Module::getAll();

        $settings = array();
        foreach ($modules as $name => $mod) {
            $set = \Module::settings($name);
            if (! empty($set)) {
                foreach ($set as $n => $s) {
                    $val = isset(static::$moduleItems[$n])
                                ? (array)static::$moduleItems[$n]  : array();
                    if (!empty($val)) {
                        $s['require'] = isset($s['require']) ? $s['require'] : false;
                        $sv = array_merge($s, (array)$val);
                        $settings['modules'][$name][$n] = $sv;
                    }
                }
            }
        }

        $skips = \Config::get('setting.skip');

        foreach (static::$systemItems as $k => $v) {
            if (in_array($k, $skips)) {
                continue;
            }

            $settings['system'][$k] = (array)$v;
            $settings['system'][$k]['type'] = \Config::get("setting.$k.type");
            $settings['system'][$k]['class'] = \Config::get("setting.$k.class");
            $settings['system'][$k]['attrs'] = \Config::get("setting.$k.attrs", array());
            $settings['system'][$k]['require'] = \Config::get("setting.$k.require", false);
            if (($settings['system'][$k]['type'] == 'select') or
                ($settings['system'][$k]['type'] == 'radio')) {
                $settings['system'][$k]['options'] = \Config::get("setting.$k.option");
            }
        }

        return $settings;
    }

    /**
     * Find all items from DB table.
     * Andthen set systemItems if item is system module.
     *
     * @return void
     **/
    private static function findAllFromDB()
    {
        $items = DB::table(static::$_table)->get();

        foreach ($items as $i) {
            static::$items[$i->slug] = ($i->value !== '') ? $i->value : $i->default;

            if ('system' == $i->module) {
                static::$systemItems[$i->slug] = $i;
            } else {
                static::$moduleItems[$i->slug] = $i;
            }
        }
    }

} // END class Setting
