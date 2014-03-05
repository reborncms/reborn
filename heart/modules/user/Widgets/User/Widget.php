<?php

namespace User;

use Auth;

class Widget extends \Reborn\Widget\AbstractWidget
{

    protected $properties = array(
            'name' => 'User Login Block',
            'author' => 'K',
            'sub' 			=> array(
                'header' 	=> array(
                    'title' => 'User Header Navigation Login',
                    'description' => 'User login and action panel for header',
                ),
                'sidebar' 	=> array(
                    'title' =>'User Sidebar Login',
                    'description' => 'Sidebar Userpanel with Login',
                ),
            ),
        );

    public function options()
    {
        return array(
            'header' => array(
                'title' => array(
                    'label' 	=> 'Title',
                    'type'		=> 'text',
                    'info'		=> 'Leave it blank if you don\'t want to show your widget title',
                ),
            ),
            'sidebar' => array(
                'title' => array(
                    'label' 	=> 'Title',
                    'type'		=> 'text',
                    'info'		=> 'Leave it blank if you don\'t want to show your widget title',
                ),
            ),
        );
    }

    public function header()
    {
        if (Auth::check()) {
            $user = Auth::getUser();
            $title = $this->get('title', '');

            return $this->show(array('user' => $user, 'title' => $title), 'navdisplay');
        } else {
            $title = $this->get('title', '');

            return $this->show(array('title' => $title), 'navlogin');
        }
    }

    public function sidebar()
    {
        if (Auth::check()) {
            $user = Auth::getUser();
            $title = $this->get('title', 'User Panel');

            return $this->show(array('user' => $user, 'title' => $title));
        } else {
            $title = $this->get('title', 'User Login');

            return $this->show(array('title' => $title), 'login');
        }
    }
}
