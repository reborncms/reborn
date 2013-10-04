<?php

namespace Admin\Controller\Admin;

use Reborn\Connector\Sentry\Sentry;
use Admin\Presenter\DashboardWidget;

class AdminController extends \AdminController
{
	public function index()
	{
		$widgets['fullcolumn'] = new DashboardWidget(\Event::call('reborn.dashboard.widgets.fullcolumn'));
		$widgets['leftcolumn'] = new DashboardWidget(\Event::call('reborn.dashboard.widgets.leftcolumn'));
		$widgets['rightcolumn'] = new DashboardWidget(\Event::call('reborn.dashboard.widgets.rightcolumn'));

		$this->template->title(\Setting::get('site_title').' - '.t('label.dashboard'))
						->set('widgets', $widgets)
						->setPartial('dashboard');
	}

	public function language()
	{
		if (! \Input::isPost()) {
			return $this->notFound();
		}

		$lang = \Input::get('lang', 'en');
		$this->app->session->set('reborn_dashboard_language', $lang);

        return \Redirect::to(\Input::server('HTTP_REFERER'));
	}

	public function login()
	{
		if(Sentry::check()) return \Redirect::toAdmin();

		if (\Input::isPost())
		{
			$login = array(
			        'email'    => rtrim(\Input::get('email'), ' '),
			        'password' => \Input::get('password')
			    );

			$rule = array(
				'email' => 'required|email',
			    'password' => 'required',
			);

			$v = new \Validation($login, $rule);

			if ($v->valid()) {
				$login = array(
			        'email'    => rtrim(\Input::get('email'), ' '),
			        'password' => \Input::get('password')
			    );

				try {
					if ($user = Sentry::authenticate($login)) {
				    	$username = $user->first_name.' '.$user->last_name;
				        \Flash::success(sprintf(t('global.welcome_ap'), $username));
				        return \Redirect::toAdmin();
				    } else {
				    	\Flash::error(t('global.login_fail'));
						return \Redirect::toAdmin('login');
				    }
				} catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
					\Flash::error(t('global.login_fail'));
					return \Redirect::toAdmin('login');
				}

			} else {
				$err = $v->getErrors();
				\Flash::error($err->toArray());
				return \Redirect::toAdmin('login');
			}
		}

		$this->setLayout('login');

		$this->template->title(t('label.login'))
						->setPartial('login');
	}

	public function logout()
	{
		if(!Sentry::check()) return \Redirect::toAdmin('login');
		Sentry::logout();
		return \Redirect::toAdmin('login');
	}
}
