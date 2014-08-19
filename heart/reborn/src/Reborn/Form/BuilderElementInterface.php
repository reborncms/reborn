<?php

namespace Reborn\Form;

/**
 * FormBuilderElementInterface class for Reborn.
 *
 * @package Reborn\Form
 * @author Myanmar Links Professional Web Development Team
 **/

interface BuilderElementInterface
{

    /**
     * Form Element Render method interface
     *
     * @param string $name  Form element (field) name
     * @param array  $param Field's array param value
     */
    public function render($name, $param);

}
