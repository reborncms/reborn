<?php

namespace Contact\Extensions\Table;

use Reborn\Table\Builder;

class InboxTable
{
    public static function create($data)
    {
        $actions = array(
            'view' => array(
                'title' => t('contact::contact.labels.detail'),
                'url' => admin_url('contact/detail/[:id]'),
                'icon' => 'icon-view',
                'btn-class' => 'inbox-view',
            ),
            'reply' => array(
                'title' => t('contact::contact.labels.reply'),
                'url' => admin_url('contact/send-mail/[:id]'),
                'icon' => 'icon-reply',
            ),
            'delete' => array(
                'title' => t('global.delete'),
                'url' => admin_url('contact/delete/[:id]'),
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
        $table->headers(array(t('label.name'), t('label.email'), t('contact::contact.labels.subject'), array('name' => t('label.action'), 'width' => '20%')));
        $table->columns(array('name', 'email', 'check_mail'));
        return $table;
    }
}
