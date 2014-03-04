<?php

namespace Field\Util;

use Reborn\Table\Builder as Table;

/**
 * Field Table Generator
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class FieldTable
{

    protected static $actions = array(
                'edit' => array(
                        'title' => 'Table Edit',
                        'icon' => 'icon-edit',
                        'btn-class' => 'btn btn-green'
                    ),
                'delete' => array(
                        'title' => 'Table Delete',
                        'icon' => 'icon-remove',
                        'btn-class' => 'btn btn-red confirm_delete'
                    )
            );

    protected static $opts = array(
                'check_all' => true,
                'class' => 'stripe',
                'btn_type' => 'icons-bar'
            );

    public static function make($obj)
    {
        static::$actions['edit']['url'] = adminUrl('field/edit/[:id]');
        static::$actions['delete']['url'] = adminUrl('field/delete/[:id]');
        static::$opts['actions'] = static::$actions;
        $table = new Table(static::$opts);
        $table->provider($obj);
        $table->headers(array('Name', 'Slug', 'Type', array('name' => 'Actions', 'width' => '14%')));
        $table->columns(array('field_name', 'field_slug', 'field_type'));

        return $table;
    }

} // END class FieldTable
