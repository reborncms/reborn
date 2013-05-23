<?php

namespace Reborn\Translate\Loader;

/**
 * Translate Loader Interface is implement for all translate file loader
 *
 * @package Reborn\Translate
 * @author Myanmar Links Professional Web Development Team
 **/
interface LoaderInterface
{
    /**
     * File Load Method
     */
    public function load($resource);

    /**
     * Get the Translate word
     */
    public function get($key, $data, $default);

} // END interface LoaderInterface
