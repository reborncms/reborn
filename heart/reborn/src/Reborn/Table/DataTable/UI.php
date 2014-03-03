<?php

namespace Reborn\Table\DataTable;

use Reborn\Cores\Facade;
use Reborn\Table\DataTable;

/**
 * DataTable UI Generate Helper class
 *
 * @package Reborn\Table
 * @author Myanmar Links Web Development Team
 **/

class UI
{
    /**
     * View instance variable
     *
     * @var \Reborn\MVC\View\View
     **/
    protected $view;

    /**
     * Datatable template path
     *
     * @var string
     **/
    protected $template;

    /**
     * Static method for new instance
     *
     * @return \Reborn\Table\DataTable\UI
     **/
    public static function make(DataTable $datatable, $template = null)
    {
        return with(new static($datatable, $template))->render();
    }

    /**
     * Default instance method
     *
     * @param  \Reborn\Table\DataTable $datatable
     * @param  null|string             $template
     * @return void
     **/
    public function __construct(DataTable $datatable, $template = null)
    {
        $this->view = Facade::getApplication()->view;

        $this->view->set('_datatable', $datatable);

        if (!is_null($template) and is_file($template)) {
            $this->template = $template;
        } else {
            $this->template = $this->getDefaultTemplate();
        }
    }

    /**
     * Render the datatable templte view
     *
     * @return string
     */
    public function render()
    {
        return $this->view->render($this->template);
    }

    /**
     * Get default template file
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return __DIR__.DS.'resources'.DS.'template.html';
    }
}
