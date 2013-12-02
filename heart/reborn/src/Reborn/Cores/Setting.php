<?php

namespace Reborn\Cores;

use Reborn\Config\Config;
use Reborn\Cores\Application;
use Reborn\Connector\DB\Schema;
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
    public static function initialize(Application $app)
    {
        if (static::$started) {
            return true;
        }

        if($app->site_manager->isMulti()) {
            $prefix = $app->site_manager->tablePrefix();
            static::$_table = $prefix.static::$_table;
        }

        static::findAllFromDB();
    }

    /**
     * Get the setting item with key name (slug Column in db table).
     *
     * @param string $key Key(slug) name for setting item
     * @param mixed $default
     * @return mixed If item not found, return default value.
     */
    public static function get($key, $default = null)
    {
        if (isset(static::$items[$key])) {
            return static::$items[$key];
        }

        return value($default);
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
     * Remove the data from setting table.
     *
     * @param string $key Slug name from the setting table
     * @param mixed $value
     * @return void
     */
    public static function remove($key)
    {
        DB::table(static::$_table)
                ->where('slug', '=', $key)
                ->delete();
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
     * Set table name and prepare for MultiSite
     *
     * @param string $prefix
     * @return void
     **/
    public static function setTableForMultisite($prefix)
    {
        static::$_table = $prefix.'settings';

        if (! Schema::hasTable(static::$_table)) {
            static::createNewSettingTable();
        }
    }

    /**
     * Create new setting table and fill default data
     *
     * @param string|null $table
     * @return void
     **/
    protected static function createNewSettingTable($table = null)
    {
        $table_name = is_null($table) ? static::$_table : $table;

        Schema::table($table_name, function($table)
        {
            $table->create();
            $table->string('slug', 255);
            $table->string('name', 255);
            $table->text('desc');
            $table->text('value');
            $table->text('default');
            $table->string('module', 50);
        });

        $data = static::getDefaultSettings();

        foreach ($data as $setting) {
            static::add($setting);
        }
    }

    /**
     * Get Default setting values.
     *
     * @return array
     **/
    protected static function getDefaultSettings()
    {
        $data = array();
        $data[] = array(
            'slug'      => 'default_module',
            'name'      => 'Default Module',
            'desc'      => 'Default Module for Reborn CMS',
            'value'     => '',
            'default'   => 'Pages',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'home_page',
            'name'      => 'Home Page',
            'desc'      => 'Home Page for your site',
            'value'     => '',
            'default'   => 'home',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'site_title',
            'name'      => 'Site Title',
            'desc'      => 'Site name for your site',
            'value'     => '',
            'default'   => 'Reborn CMS',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'site_slogan',
            'name'      => 'Site Slogan',
            'desc'      => 'Slogan for your site',
            'value'     => '',
            'default'   => 'Your slogan here',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'admin_theme',
            'name'      => 'Admin Panel Theme',
            'desc'      => 'Theme for the site backend (Admin Panel)',
            'value'     => '',
            'default'   => 'default',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'public_theme',
            'name'      => 'Public Theme',
            'desc'      => 'Theme for the site frontend (Public)',
            'value'     => 'default',
            'default'   => 'default',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'adminpanel_url',
            'name'      => 'Admin Panel URI',
            'desc'      => 'URI for the admin panel.',
            'value'     => '',
            'default'   => 'admin',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'default_language',
            'name'      => 'Default Language',
            'desc'      => 'Default Language for Reborn CMS',
            'value'     => '',
            'default'   => 'en',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'timezone',
            'name'      => 'Select your Timezone',
            'desc'      => 'Set timezone for your server',
            'value'     => '',
            'default'   => 'UTC',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'admin_item_per_page',
            'name'      => 'Items to show in one page (Admin Panel)',
            'desc'      => 'Item limit to show in admin Data Tables',
            'value'     => '',
            'default'   => '10',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'frontend_enabled',
            'name'      => 'Frontend Status',
            'desc'      => 'If your site in maintenance condition, you can closed your site.',
            'value'     => '',
            'default'   => 'enable',
            'module'    => 'system'
        );
        $data[] = array(
            'slug'      => 'spam_filter',
            'name'      => 'Spam Filter Key',
            'desc'      => 'Use this key for spam filter from bot',
            'value'     => '',
            'default'   => 'D0ntFillINthI$FielD',
            'module'    => 'system'
        );

        return $data;
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
