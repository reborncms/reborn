<?php

namespace Reborn\MVC\View;

/**
 * Abstract Handler Class for the Reborn View Parser
 *
 * @package Reborn\MVC\View
 * @author Myanmar Links Professional Web Development Team
 **/
abstract class AbstractHandler
{

    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    abstract public function handle($template, $data);

} // END abstract class AbstractHandler
