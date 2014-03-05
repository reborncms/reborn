<?php

namespace User\Lib;

use \Reborn\Auth\Sentry\Eloquent\User;

class Helper
{
    public static function dashboardWidget()
    {
        $widget = array();
        $widget['title'] = t('label.user_last_login');
        $widget['icon'] = 'icon-users';
        $widget['id'] = 'user';
        $widget['body'] = '';
        $last_login = User::take(5)->orderBy('last_login', 'desc')->get();
        $widget['body'] .= '<ul>';

        foreach ($last_login as $user) {
            $url = rbUrl('user/profile/'.$user->id);
            $fullname = $user->first_name.' '.$user->last_name;
            $widget['body'] .= '<li>'.sprintf(t('label.last_login_text'), $url, $fullname, num($user->last_login)).'</li>';
        }

        $widget['body'] .= '</ul>';

        return $widget;
    }
}
