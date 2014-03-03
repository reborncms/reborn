<?php

namespace Reborn\Pagination;

/**
 * Pagination Builder Interface
 *
 * @package Reborn\Pagination
 * @author MyanmarLinks Professional Web Development Team
 **/
interface BuilderInterface
{

    /**
     * Render the pagination user interface.
     *
     * @return string
     **/
    public function render();

} // END interface BuilderInterface
