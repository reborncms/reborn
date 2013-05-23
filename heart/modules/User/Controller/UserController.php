<?php

namespace User\Controller;

use Reborn\Connector\Sentry\Sentry;
use Reborn\Util\Mailer as Mailer;
use User\Model\UserMeta as UserMeta;

class UserController extends \PublicController
{
	public function before()
	{
		$this->template->header = \Translate::get('user::user.title.user');
	}

	public function index()
	{
		if(!Sentry::check()) return \Redirect::to('user/login');

		$user = Sentry::getUser();

		$data['name'] = $user->first_name.' '.$user->last_name;
		$data['email'] = $user->email;

		$this->template->title(\Translate::get('user::user.title.profile'))
					->breadcrumb(\Translate::get('user::user.title.profile'))
					->set('user', $data)
					->setPartial('index');
	}

	public function profile($id = null)
	{
		if(!Sentry::check()) return \Redirect::to('user/login');
		if (is_null($id)) {
			$user = Sentry::getUser();
		} else {
			$user = Sentry::getUserProvider()->findById($id);
		}

		$data['name'] = $user->first_name.' '.$user->last_name;
		$data['email'] = $user->email;

		$this->template->title(\Translate::get('user::user.title.profile'))
					->breadcrumb(\Translate::get('user::user.title.profile'))
					->set('user', $data)
					->setPartial('profile');
	}

	/**
	 * User Frontend Login
	 *
	 * @return void
	 **/
	public function login()
	{
		if(Sentry::check()) return \Redirect::to('user');

		if (\Input::isPost()) {
			if (\Security::CSRFvalid('user')) {
				$redirect = \Input::server('HTTP_REFERER');
				$rule = array(
			        'email' => 'required|valid_email',
			        'password' => 'required|mixLength:6',
			    );
				$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

				if ($v->fail()) {
						$errors = $v->getErrors();
						$this->template->set('errors', $errors);
				} else {
					$email = \Input::get('email');
					$password = \Input::get('password');

			    	$login = array(
				        'email'    => $email,
				        'password' => $password
				    );

				    if ($user = Sentry::authenticate($login)) {
				    	\Flash::success(\Translate::get('user::user.login.success'));
				        return \Redirect::to($redirect);
				    } else {
				    	\Flash::error(\Translate::get('user::user.login.fail'));
				    }
				}
			} else {
				\Flash::error(\Translate::get('user::user.csrf'));
			}
		}

		

		$this->template->title(\Translate::get('user::user.title.login'))
			->breadcrumb(\Translate::get('user::user.title.login'))
			->setPartial('login');
	}

	/**
	 * Users Logout
	 * @access public
	 * @return void
	 */
	public function logout()
	{
		if(!Sentry::check()) return \Redirect::to('login');
		$redirect = \Input::server('HTTP_REFERER');
		Sentry::logout();
		\Flash::success(\Translate::get('user::user.logout'));
		return \Redirect::to($redirect);
	}

	public function edit()
	{
		if(!Sentry::check()) return \Redirect::to('login');

		$user = Sentry::getUser();

		if (\Input::isPost()) {
			if (\Security::CSRFvalid('user')) {
				$editUser = Sentry::getUserProvider()->findById(\Input::get('id'));

				if($user->id == $editUser->id ) {
					$v = $this->validate();
					if ($v->fail()) {
						$errors = $v->getErrors();
						$this->template->errors = $errors;
					} else {
						$email = \Input::get('email');
						$first_name = \Input::get('first_name');
						$last_name = \Input::get('last_name');
						$password = \Input::get('password');
						$confpass = \Input::get('confpass');

				    	$user->email = $email;
				    	$user->first_name = $first_name;
				    	$user->last_name = $last_name;

				    	if(($password != '') AND ($confpass != '')) {				
							if($password == $confpass) {
								$user->password = $password;
							} else {
								\Flash::error(\Translate::get('user::user.password.fail'));
								return \Redirect::to('user/edit');
							}
						}

					    if ($user->save()) {
					    	$usermeta = self::saveMeta('edit', $user->id);
							$usermeta->save();
							
					        \Flash::success(\Translate::get('user::user.profile.success'));
					        return \Redirect::to('user');
					    } else {
					        \Flash::error(\Translate::get('user::user.profile.fail'));
					    }	
					}
				}
			}
			\Flash::error(\Translate::get('user::user.csrf'));
		}

		$usermeta = UserMeta::where('user_id', '=', $user->id)->get();
		foreach ($usermeta as $u) {
			$usermeta = $u;
		}

		$this->template->title(\Translate::get('user::user.profile.title'))
			->breadcrumb(\Translate::get('user::user.profile.title'))
			->set('user', $user)
			->set('usermeta', $usermeta)
			->setPartial('edit');
	}

	/**
	 * User registration with activation
	 *
	**/
	public function register()
	{
		if(Sentry::check()) return \Redirect::to('user');

		if (\Input::isPost()) {		
			if (\Security::CSRFvalid('user')) {
				$v = $this->validate();
				if ($v->fail()) {
					$errors = $v->getErrors();
					$this->template->errors = $errors;
				} else {
					$email = \Input::get('email');
					$first_name = \Input::get('first_name');
					$last_name = \Input::get('last_name');
					$password = \Input::get('password');
					$confpass = \Input::get('confpass');

					if ($password !== $confpass) {
						\Flash::error(\Translate::get('user::user.password.fail'));
					} else {
						
						try 
						{
						    $user = Sentry::register(array(
						        'email'    => $email,
						        'password' => $password,
						        'first_name' => $first_name,
						        'last_name' => $last_name,
						        'permissions' => array(),
						    ));

						    $usermeta = self::saveMeta('create', $user->id);
						    $usermeta->save();

						    $groups = Sentry::getGroupProvider()->findById(3);
					    	$user->addGroup($groups);

						    $activationCode = $user->getActivationCode();
						    $emailEncode = base64_encode($email);

						    $activationLink = rbUrl().'user/activate/'.$emailEncode.'/'.$activationCode;
						    
						    // create config to mail user activation code
						    $config = array(
								'to'		=> $email,
								'from'		=> \Setting::get('site_mail'),
								'name'		=> \Setting::get('site_title'),
								'subject'	=> \Translate::get('user::user.activate.subject'),
								'body'		=> 'Please active your account by using think link: '.$activationLink,
							);

						    // sent mail to register user
						    $mail = Mailer::send($config);

						    \Flash::success(\Translate::get('user::user.activate.check'));
							return \Redirect::to('user/activate');

						}
						catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
						{
						    \Flash::error('Login field required.');
						}
						catch (Cartalyst\Sentry\Users\UserExistsException $e)
						{
						    \Flash::error('User already exists.');
						}
					}
				}
			} else {
				\Flash::error(\Translate::get('user::user.csrf'));
			}
		}

		$this->template->title(\Translate::get('user::user.title.registration'))
			->breadcrumb(\Translate::get('user::user.title.registration'))
			->setPartial('register');
	}

	/**
	* Activate user account by using email address and activation code
	*
	* @param $email string
	* @param $activationCode string
	*/
	public function activate($emailEncode, $activationCode)
	{
		if(Sentry::check()) return \Redirect::to('user');

		$email = base64_decode($emailEncode);
		$user = Sentry::getUserProvider()->findByLogin($email);

		// Attempt user activation
	    if ($user->attemptActivation($activationCode)) {
	       	\Flash::success(\Translate::get('user::user.activate.success'));
	        return \Redirect::to('user/login');
	    } else {
	       \Flash::error(\Translate::get('user::user.activate.fail'));
	    }
		
		$this->template->title(\Translate::get('user::user.title.activate'))
			->breadcrumb(\Translate::get('user::user.title.activate'))
			->setPartial('activate');
	}

	/**
	* Email password reset link ink User
	*/
	public function resetPassword()
	{
		if(Sentry::check()) return \Redirect::to('user');

		if (\Input::isPost()) {

			if (\Security::CSRFvalid('user')) {
				$rule = array(
			        'email' => 'required|valid_email',
			    );
				$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

				if ($v->fail()) {
						$errors = $v->getErrors();
						$this->template->set('errors', $errors);
				} else {
					$email = \Input::get('email');
					try
					{
					    $user = Sentry::getUserProvider()->findByLogin($email);
					    $resetCode = $user->getResetPasswordCode();
					    $emailEncode = base64_encode($email);

						$link = rbUrl().'user/passwordreset/'.$emailEncode.'/'.$resetCode;

					    // Now you can send this code to your user via email for example.
					    $config = array(
							'to'		=> $email,
							'from'		=> \Setting::get('site_mail'),
							'name'		=> \Setting::get('site_title'),
							'subject'	=> 'Password Reset Code',
							'body'		=> 'Use this link to reset your password: '.$link,
						);

					    $mail = Mailer::send($config);

					    \Flash::success(\Translate::get('Password RestCode Successfully sent!'));
						return \Redirect::to('/');
					}
					catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
					{
					    \Flash::error('User was not found.');
					}
				}
			} else {
				\Flash::error(\Translate::get('user::user.csrf'));
			}
		}

		$this->template->title('Reset Password')
			->breadcrumb('Reset Password')
			->setPartial('send');
	}

	/**
	* Email password reset link ink User
	*
	* @param $emailEncode string
	* @param $hash string
	*/
	public function passwordReset($emailEncode, $hash)
	{
		if(Sentry::check()) return \Redirect::to('user');

		if (\Input::isPost()) {
			if (\Security::CSRFvalid('user')) {
				$rule = array(
			        'new_password' => 'required|minLength:6',
			    );
				$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

				if ($v->fail()) {
						$errors = $v->getErrors();
						$this->template->set('errors', $errors);
				} else {

					$newPassword = \Input::get('new_password');
					$confNewPassword = \Input::get('conf_new_password');

					if($newPassword !== $confNewPassword) {
						\Flash::error('New Password doesn\'t matched. Pleaes try again');
					} else {
						try
						{
							$email = base64_decode(\Uri::segment(3));
						    $user = Sentry::getUserProvider()->findByLogin($email);

						    if ($user->checkResetPasswordCode($hash)) {
						        // Attempt to reset the user password
						        if ($user->attemptResetPassword($hash, $newPassword)) {
						            \Flash::success('Successfully password reset! Please login with your new pasword.');
									return \Redirect::to('user/login');
						        } else {
						            \Flash::error('Failed to reset Password');
						        }
						    } else {
						    	\Flash::error('The provided password reset code is Invalid');
						    }
						}
						catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
						{
						    \Flash::error('User was not found.');
						}
					}
				}
			} else {
				\Flash::error(\Translate::get('user::user.csrf'));
			}
		}

		$this->template->title('Reset Password')
			->breadcrumb('Reset Password')
			->set('emailEncode', $emailEncode)
			->set('hash', $hash)
			->setPartial('reset');
	}

	/**
	 * Save Form Values of Create and Edit Blog
	 *
	 * @return boolean
	 **/
	protected function saveMeta($method, $id) 
	{
		if ($method == 'create') {
			$user = new UserMeta;
		} else {
			$user = UserMeta::find($id);
		}

		$user->user_id = $id;
		$user->biography = \Input::get('biography');
		$user->country = \Input::get('country');
		$user->website = \Input::get('website');
		$user->facebook = \Input::get('facebook');
		$user->twitter = \Input::get('twitter');
		
		return $user;
	}

	protected function validate()
	{
		$rule = array(
	        'email' => 'required|valid_email',
	        'password' => 'required|minLength:6',
	        'first_name' =>'required|minLength:2|maxLength:15',
	        'last_name' => 'required|minLength:2|maxLength:15',
	        'username' => 'required|minLength:4|maxLength:15',
	    );

		$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
		
		return $v;
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
