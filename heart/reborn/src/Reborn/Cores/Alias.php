<?php

namespace Reborn\Cores;

use Reborn\Exception\RbException;

/**
 * Class Alias Library for Reborn
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class Alias
{

    /**
     * Marking for Reborn's Core Class are alias or not
     *
     * @var bool
     **/
    protected static $coreIsAlias = false;

    /**
     * Array list for Reborn's Core Class
     *
     * @var array
     **/
    protected static $coreClasses = array(
            'Facade' => 'Reborn\Cores\Facade',
            'Cache' => 'Reborn\Cache\CacheManager',
            'Config' => 'Reborn\Config\Config',
            'NotAuthException' => 'Reborn\MVC\Controller\Exception\NotAuthException',
            'NotAdminAccessException' => 'Reborn\MVC\Controller\Exception\NotAdminAccessException',
            'AdminController' => 'Reborn\MVC\Controller\AdminController',
            'PublicController' => 'Reborn\MVC\Controller\PublicController',
            'PrivateController' => 'Reborn\MVC\Controller\PrivateController',
            'Controller' => 'Reborn\MVC\Controller\Controller',
            'DB' => 'Reborn\Connector\DB\DBManager',
            'Dir' => 'Reborn\Filesystem\Directory',
            'Dummy' => 'Reborn\Dummy\Generator',
            'Eloquent' => 'Reborn\MVC\Model\Model',
            'Event' => 'Reborn\Event\EventManager',
            'Error' => 'Reborn\Cores\ErrorFacade',
            'File' => 'Reborn\Filesystem\File',
            'Flash' => 'Reborn\Util\Flash',
            'Hash' => 'Reborn\Util\Hash',
            'Html' => 'Reborn\Util\Html',
            'HttpNotFoundException' => 'Reborn\Exception\HttpNotFoundException',
            'Pagination' => 'Reborn\Pagination\PaginationFacade',
            'RbException' => 'Reborn\Exception\RbException',
            'Request' => 'Reborn\Http\Request',
            'Redirect' => 'Reborn\Http\Redirect',
            'Response' => 'Reborn\Http\Response',
            'Form' => 'Reborn\Form\UIForm',
            'FormBuilder' => 'Reborn\Form\AbstractFormBuilder',
            'Input' => 'Reborn\Http\Input',
            'Log' => 'Reborn\Connector\Log\LogManager',
            'Module' => 'Reborn\Module\ModuleManager',
            'Presenter' => 'Reborn\Presenter\Presentation',
            'PresenterCollection' => 'Reborn\Presenter\Collection',
            'Registry' => 'Reborn\Cores\Registry',
            'Route' => 'Reborn\Routing\RouteFacade',
            'Router' => 'Reborn\Routing\Router',
            'Schema' => 'Reborn\Connector\DB\Schema',
            'Security' => 'Reborn\Util\Security',
            'Sentry' => 'Reborn\Connector\Sentry\Sentry',
            'Setting' => 'Reborn\Cores\Setting',
            'Str' => 'Reborn\Util\Str',
            'Table' => 'Reborn\Table\Builder',
            'DataTable' => 'Reborn\Table\DataTable',
            'DataTableBuilder' => 'Reborn\Table\DataTable\UI',
            'ToolKit' => 'Reborn\Util\ToolKit',
            'Translate' => 'Reborn\Translate\TranslateManager',
            'Uri' => 'Reborn\Http\Uri',
            'Validation' => 'Reborn\Form\Validation',
            'ViewData' => 'Reborn\MVC\View\ViewData',
            'Widget' => 'Reborn\Widget\Widget',
            'AbstractWidget' => 'Reborn\Widget\AbstractWidget'
        );

    /**
     * Construct method is final, don't allow to override these method.
     *
     */
    final function __construct() {}

    /**
     * Class Alias for Reborn's Cores Class.
     * This method is call when application started.
     * But not allow to call after one time.
     *
     * @return void
     */
    public static function coreClassAlias()
    {
        if (! static::$coreIsAlias) {
            static::aliasRegister(static::$coreClasses);
            static::$coreIsAlias = true;
        }
    }

    /**
     * Register the class alias.
     * You can use your class (external of Reborn's Cores) to alias use this method.
     * <code>
     *      // If you want to use Products\Libs\Shopper to Shopper
     *      Alias::aliasRegister(array('Shopper' => 'Products\Libs\Shopper'));
     *
     *      // Ok, Shopper class is alias now
     *      Shopper::calculate();
     * </code>
     *
     * @param array $classes Array data for class alias
     * @return void
     */
    public static function aliasRegister($classes = array())
    {
        foreach ($classes as $alias => $class) {
            if (static::$coreIsAlias) {
                if (array_key_exists($alias, static::$coreClasses) and
                $class == static::$coreClasses[$alias]) {
                    throw new RbException("$alias is already exists!");
                }
            }

            class_alias($class, $alias);
        }
    }

} // END class Alias
