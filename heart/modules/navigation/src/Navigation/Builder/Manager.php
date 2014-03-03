<?php

namespace Navigation\Builder;

use Facade;
use Navigation\Model\Navigation as Group;

/**
 * Navigation Builder class
 *
 * @package Navigation
 * @author MyanmarLinks Professional Web Development Team
 **/
class Manager extends Facade
{
    /**
     * Choose navigation
     *
     * @param  string $group
     * @param  string $type  Navigation type.
     *                       Supported type are "bootstrap, foundation, pure, reborn"
     * @return string
     **/
    public static function choose($group = 'header', $type = 'reborn')
    {
        switch ($type) {
            case 'bootstrap':
                return static::bootstrap($group);
                break;

            case 'foundation':
                return static::foundation($group);
                break;

            case 'pure':
                return static::pure($group);
                break;

            default:
                static::reborn($group);
                break;
        }
    }

    /**
     * Static method for reborn default navigation style
     *
     * @param  string $group Navigation group name. Default is "header"
     * @return string
     **/
    public static function reborn($group = 'header')
    {
        return static::getInstance()->build($group, 'Default')->render();
    }

    /**
     * Static method for bootstrap navigation style
     *
     * @param  string $group Navigation group name. Default is "header"
     * @return string
     **/
    public static function bootstrap($group = 'header')
    {
        return static::getInstance()->build($group, 'Bootstrap')->render();
    }

    /**
     * Static method for bootstrap navigation style
     *
     * @param  string $group Navigation group name. Default is "header"
     * @return string
     **/
    public static function foundation($group = 'header')
    {
        return static::getInstance()->build($group, 'Foundation')->render();
    }

    /**
     * Static method for Pure CSS navigation style
     *
     * @param  string $group Navigation group name. Default is "header"
     * @return string
     **/
    public static function pure($group = 'header')
    {
        return static::getInstance()->build($group, 'Pure')->render();
    }

    /**
     * Build Navigation Builder Instance
     *
     * @return \Navigation\Builder\Base
     **/
    public function build($group, $type)
    {
        switch ($type) {
            case 'Bootstrap':
                return new Bootstrap(static::$app, $group);
                break;

            case 'Foundation':
                return new Foundation(static::$app, $group);
                break;

            case 'Pure':
                return new Pure(static::$app, $group);
                break;

            default:
                return new Base(static::$app, $group);
                break;
        }
    }

    /**
     * Get Manager Instance
     *
     * @return \Navigation\Builder\Manager
     **/
    protected static function getInstance()
    {
        return new static();
    }

} // END class Manager
