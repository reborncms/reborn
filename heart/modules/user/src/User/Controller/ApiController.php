<?php

namespace User\Controller;

use Auth;
use Config;
use Input;
use Response;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserAlreadyActivatedException;

class ApiController extends \Api\Controller\ApiController
{
	/**
	 * Create new user by api
	 * @return Response
	 */
	public function create()
	{
		if (\Setting::get('user_registration') == 'disable') {
            throw new \Exception("User creation is disable");
        }

        $rules = Config::get('user::user.validation.api.create');

        $v = new \Validation(Input::get(), $rules);

        if ( $v->fail()) {
        	return Response::json([
				'status' => 'fail',
				'code' => \User\ApiError::USER_VALIDATION_FAIL_CODE,
				'messages' => $v->getErrors()->toArray()
			]);
        }

        try {
            $user = Auth::register(array(
                'email'    => Input::get('email'),
                'password' => Input::get('password'),
                'first_name' => Input::get('first_name'),
                'last_name' => Input::get('last_name'),
                'permissions' => array(),
            ));

            $groups = Auth::getGroupProvider()->findById(3);
            $user->addGroup($groups);

            $activationCode = $user->getApiActivationCode();

            $data = array(
            	'status' => 'success',
            	'email' => $user->email,
            	'varification_code' => $activationCode
            );

            return Response::json($data);

        } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
            
            return Response::json([
				'status' => 'fail',
				'code' => \User\ApiError::USER_ALREADY_EXISTS_CODE,
				'message' => \User\ApiError::USER_ALREADY_EXISTS_MESSAGE,
			]);
        }
	}

	/**
	 * Activate user account by api
	 * @return Response
	 */
	public function activate()
	{
		$email = Input::get('email');
		$activate_code = Input::get('varification_code');

		$user = Auth::getUserProvider()->findBy('email', $email);

		if ( is_null($user)) {
			return Response::json([
				'status' => 'fail',
				'code' => \User\ApiError::USER_NOT_FOUND_CODE,
				'message' => \User\ApiError::USER_NOT_FOUND_MESSAGE,
			]);
		}

		try {
			if ( $user->attemptActivationForApi($activate_code)) {
				
				$data = array(
					'status' => 'success'
				);
			} else {
				$data = array(
					'status' => 'fail',
					'message' => 'Activation fail'
				);
			}

			return Response::json($data);
		} catch (UserAlreadyActivatedException $e) {
			
			return Response::json([
				'status' => 'fail',
				'code' => \User\ApiError::USER_ALREADY_ACTIVATED_CODE,
				'message' => \User\ApiError::USER_ALREADY_ACTIVATED_MESSAGE,
			]);
		}
	}

	/**
	 * Login user by api
	 * @return Response
	 */
	public function login()
	{
		$credentials['email'] = Input::get('email');
		$credentials['password'] = Input::get('password');

		$v = new \Validation($credentials, Config::get('user::user.validation.api.login'));

		if ( $v->fail()) {

			return Response::json([
				'status' => 'fail',
				'code' => \User\ApiError::USER_VALIDATION_FAIL_CODE,
				'messages' => $v->getErrors()->toArray()
			]);
		}

		$data = [
			'status' => 'fail',
			'message' => 'Login fail',
		];

		try {
			
			if( $user = Auth::loginForApi($credentials)) {
				return Response::json([
					'status' => 'success',
					'user'	=> $user->toArray()
				]);
			}

		} catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
			
			$data = [
				'code' => \User\ApiError::USER_NOT_FOUND_CODE,
				'message' => \User\ApiError::USER_NOT_FOUND_MESSAGE,
			];

        } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            
            $data = [
				'code' => \User\ApiError::USER_NOT_ACTIVATED_CODE,
				'message' => \User\ApiError::USER_NOT_ACTIVATED_MESSAGE,
			];

        } catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            
            $data = [
				'code' => \User\ApiError::USER_SUSPENDED_CODE,
				'message' => \User\ApiError::USER_SUSPENDED_MESSAGE,
			];

        } catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
            
            $data = [
				'code' => \User\ApiError::USER_BANNED_CODE,
				'message' => \User\ApiError::USER_BANNED_MESSAGE,
			];
        }

		return Response::json($data);
	}

	/**
	 * Logout user account by api
	 * @return Response
	 */
	public function logout()
	{
		\Event::call('reborn.user.api.logout');
        Auth::logout();

        return Response::json(['status' => 'success']);
	}

	/**
	 * Get user profile for current api login user
	 * @return Response
	 */
	public function getProfile()
	{
		if (! Auth::check()) {
			return Response::json(['message' => 'Unauthorized'], 401);
		}

		$user = Auth::getUser();

		return Response::json([
			'status' => 'success',
			'user' => $user->toArray()
		]);
	}
}