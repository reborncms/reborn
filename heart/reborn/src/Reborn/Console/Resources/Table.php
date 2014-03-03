<?php

namespace {module}\Extensions\Table;

use Reborn\Table\Builder;

class {module}Table
{
    public static function create($data)
    {
        $actions = array(
            'view' => array(
                'title' => 'View',
                'url' => url('{uri}/view/[:id]'),
                'btn-class' => '{uri}-view',
                'icon' => 'icon-view',
            ),
            'edit' => array(
                'title' => 'Edit',
                'url' => admin_url('{uri}/edit/[:id]'),
                'icon' => 'icon-edit',
            ),
            'delete' => array(
                'title' => 'Delete',
                'url' => admin_url('{uri}/delete/[:id]'),
                'btn-class' => 'confirm_delete',
                'icon' => 'icon-remove',
            )
        );

        $options = array(
            'check_all' => true,
            'actions' => $actions,
            'btn_type' => 'icons-bar'
        );

        $table = Builder::create($options);
        $table->provider($data);
        //$table->headers(array('Title','Content', array('name' => 'Actions', 'width' => '30%')));
        //$table->columns(array('title', 'content'));
        return $table;
    }
}
