<?php

namespace Field\Facade;

/**
 * undocumented class
 *
 * @package default
 * @author MyanmarLinks Professional Web Development Team
 **/
class Field extends \Facade
{

    /**
     * Get Instance for Field\Builder
     *
     * @return \Field\Builder
     **/
    protected static function getInstance()
    {
        return static::$app['\Field\Builder'];
    }

} // END class Field
