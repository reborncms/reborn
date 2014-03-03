<?php

namespace Admin\Controller\Admin;

use Auth, Event, Setting, Input, Flash, Redirect, Validation;
use Admin\Presenter\DashboardWidget;

class AdminController extends \AdminController
{
    /**
     * Admin Dashboard index action
     *
     * @return void
     **/
    public function index()
    {
        $widgets['fullcolumn'] = new DashboardWidget(Event::call('reborn.dashboard.widgets.fullcolumn'));
        $widgets['leftcolumn'] = new DashboardWidget(Event::call('reborn.dashboard.widgets.leftcolumn'));
        $widgets['rightcolumn'] = new DashboardWidget(Event::call('reborn.dashboard.widgets.rightcolumn'));

        $this->template->title(t('label.dashboard'))
                        ->set('widgets', $widgets)
                        ->view('dashboard');
    }

    /**
     * Language switching action
     *
     * @return \Redirect
     */
    public function language()
    {
        if (! Input::isPost() ) {
            return $this->notFound();
        }

        $lang = Input::get('lang', 'en');
        $this->app->session->set('reborn_dashboard_language', $lang);

        return Redirect::to(Input::server('HTTP_REFERER'));
    }

    /**
     * Adminpanel login process action
     *
     * @return mixed
     **/
    public function login()
    {
        if(Auth::check()) return Redirect::toAdmin();

        if (Input::isPost()) {
            $login = array(
                    'email'    => rtrim(Input::get('email'), ' '),
                    'password' => Input::get('password')
                );

            $rule = array(
                'email' => 'required|email',
                'password' => 'required',
            );

            $v = new Validation($login, $rule);

            if ($v->valid()) {
                $login = array(
                    'email'    => rtrim(Input::get('email'), ' '),
                    'password' => Input::get('password')
                );

                try {
                    if ($user = Auth::authenticate($login)) {
                        $username = $user->fullname;
                        Flash::success(sprintf(t('global.welcome_ap'), $username));

                        return Redirect::toAdmin();
                    } else {
                        Flash::error(t('global.login_fail'));

                        return Redirect::toAdmin('login');
                    }
                } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    Flash::error(t('global.invalid_user'));

                    return Redirect::toAdmin('login');
                } catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
                    Flash::error(t('global.login_suspended'));

                    return Redirect::toAdmin('login');
                } catch (\Exception $e) {
                    Flash::error(t('global.login_fail'));

                    return Redirect::toAdmin('login');
                }

            } else {
                $err = $v->getErrors();
                Flash::error($err->toArray());

                return Redirect::toAdmin('login');
            }
        }

        $this->setLayout('login');

        $this->template->title(t('label.login'))
                        ->view('login');
    }

    /**
     * Logout action for adminpanel
     *
     * @return \Redirect
     */
    public function logout()
    {
        if(!Auth::check()) return Redirect::toAdmin('login');
        Auth::logout();

        return Redirect::toAdmin('login');
    }
}
