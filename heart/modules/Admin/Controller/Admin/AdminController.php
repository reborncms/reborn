<?php

namespace Admin\Controller\Admin;

use Reborn\Connector\Sentry\Sentry;
use Admin\Model\User;
use Admin\Model\Blog;

class AdminController extends \AdminController
{
	public function before() {}

	public function index()
	{
		// Event trigger for admin panel index.
		\Event::call('reborn.admin_panel.index');

		$last_login = User::take(5)->orderBy('last_login', 'desc')->get();

		if (\Module::isEnabled('Blog')) {
			$last_post = Blog::with('author')->take(5)->orderBy('created_at', 'desc')->get();
			$this->template->set('last_post', $last_post);
		}

		$this->template->title(\Setting::get('site_title').' - '.t('label.dashboard'))
						->set('last_login', $last_login)
						->setPartial('dashboard');
	}

	public function login()
	{
		if(Sentry::check()) return \Redirect::toAdmin();

		$rule = array(
				'email' => 'required|email',
			    'password' => 'required',
			);
		$v = new \Validation(\Input::get('*'), $rule);

		if (\Input::isPost())
		{
			if (\Security::CSRFvalid('rbam')) {
				if ($v->valid()) {
					$login = array(
				        'email'    => \Input::get('email'),
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
					\Flash::error($msg);
					return \Redirect::toAdmin('login');
				}
			} else {
				\Flash::error(t('global.csrf_fail'));
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
