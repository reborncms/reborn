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
     *
     * @param  string  $resource
     * @param  string  $locale
     * @return boolean
     */
    public function load($resource, $locale);

} // END interface LoaderInterface
