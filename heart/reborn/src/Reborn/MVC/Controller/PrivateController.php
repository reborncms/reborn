<?php

namespace Reborn\MVC\Controller;

use Reborn\Http\Redirect;
use Reborn\Connector\Sentry\Sentry;

class PrivateController extends PublicController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAuthentication();
	}

	/**
     * Check the Authentication for Private Controller
     *
     * @return boolean
     **/
    protected function checkAuthentication()
    {
        if (!Sentry::check()) {
            return Redirect::to('login');
        } else {
            $this->template->loggedin_user = Sentry::getUser();
        }

        return true;
    }
}
