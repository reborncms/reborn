<?php

namespace User\Controller;

use Auth, Field, Module, Mailer, Hash , Event;

class UserController extends \PublicController
{
    public function before()
    {
        $this->template->header = t('user::user.title.user');
    }

    public function index()
    {
        if(!Auth::check()) return \Redirect::to('user/login');

        return \Redirect::to('/');
    }

    /**
     * View profile page for every users
     *
     * @param $id int
     **/
    public function profile($id = null)
    {
        if(!Auth::check() and is_null($id)) return \Redirect::to('user/login');

        $user = \User::findBy('id', $id);

        if(is_null($user)) return \Redirect::to('/');

        $currentUser = Auth::getUser();

        $blogs = array();

        if (\Module::isEnabled('blog')) {
            $blogs = \Blog::posts(array('limit' => 10, 'author' => $id));
        }

        $this->template->title(t('user::user.title.profile'))
                    ->breadcrumb(t('user::user.title.profile'))
                    ->set('user', $user)
                    ->set('currentUser', $currentUser)
                    ->set('blogs', $blogs)
                    ->setPartial('profile');
    }

    /**
     * User Frontend Login
     *
     * @return void
     **/
    public function login()
    {
        if(Auth::check()) return \Redirect::to('user');

        if (\Input::isPost()) {
            $rule = array(
                'email' => 'required|email',
                'password' => 'required|minLength:6',
            );
            $v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
            $e = new \Reborn\Form\ValidationError();

            if ($v->fail()) {
                    $e = $v->getErrors();
                    $this->template->set('errors', $e);
            } else {
                try {
                    $email = \Input::get('email');
                    $password = \Input::get('password');
                    $remember = \Input::get('remember');
                    is_null($remember) ? $remember = false : $remember = true;

                    $login = array(
                        'email'    => $email,
                        'password' => $password
                    );

                    if ($user = Auth::authenticate($login, $remember)) {
                        $name = $user->first_name.' '.$user->last_name;
                        \Flash::success(sprintf(t('user::user.login.success'), $name));

                        return \Redirect::back();
                    } else {
                        \Flash::error(t('user::user.login.fail'));
                    }
                } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    \Flash::error(t('user::user.login.fail'));
                } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
                    \Flash::error(t('user::user.login.activate'));
                } catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
                    $throttle = new \Cartalyst\Sentry\Throttling\Eloquent\Throttle;
                    $time = $throttle->getSuspensionTime();
                    \Flash::error(sprintf(t('user::user.login.suspended'), $time));
                } catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
                    \Flash::error(t('user::user.login.banned'));
                }
            }

        }

        $this->template->title(t('user::user.title.login'))
            ->breadcrumb(t('user::user.title.login'))
            ->setPartial('login');
    }

    /**
     * Logout the user
     *
     * @return void
     */
    public function logout()
    {
        if(!Auth::check()) return \Redirect::to('login');

        Event::call('reborn.user.logout');
        Auth::logout();

        \Flash::success(t('user::user.logout'));

        return \Redirect::to('login');
    }

    /**
     * Allowed users to edit their profile
     *
     **/
    public function edit()
    {
        if(!Auth::check()) return \Redirect::to('login');

        $user = Auth::getUser();

        if (\Input::isPost()) {

            $editUser = Auth::getUserProvider()->findById(\Input::get('id'));

            if ($user->id == $editUser->id) {

                $rule = array(
                    'email' => 'required|email',
                    'first_name' =>'required|minLength:2|maxLength:40',
                    'last_name' => 'required|minLength:2|maxLength:40',
                );

                $v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
                $e = new \Reborn\Form\ValidationError();

                if ($v->fail()) {
                    $e = $v->getErrors();
                    $this->template->set('errors', $e);
                } else {
                    $email = \Input::get('email');
                    $first_name = \Input::get('first_name');
                    $last_name = \Input::get('last_name');

                    try {
                        $user->email = $email;
                        $user->first_name = $first_name;
                        $user->last_name = $last_name;

                        if ($user->save()) {
                            $usermeta = $this->saveMeta($user);

                            if (Module::isEnabled('field')) {
                                Field::update('user', $user);
                            }

                            \Flash::success(t('user::user.profile.success'));
                            Event::call('reborn.user.edit',array($user));
                            return \Redirect::to('user/profile/'.$user->id);
                        }

                    } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
                       \Flash::error(sprintf(t('user::user.auth.userexist'), $email));
                    }
                }
            }
        }

        $fields = array();

        if (Module::isEnabled('field')) {
            $fields = Field::getForm('user', $user);
        }

        $this->template->title(t('user::user.profile.title'))
            ->breadcrumb(t('user::user.profile.title'))
            ->set('user', $user)
            ->set('fields', $fields)
            ->setPartial('edit');
    }

    /**
     * Edit profile for logged in Student
     *
     */
    public function changePassword()
    {
        if(!Auth::check()) return \Redirect::to('login');

        if (\Input::isPost()) {

            $rule = array(
                'newPassword' => 'required|minLength:6',
            );
            $v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
            $e = new \Reborn\Form\ValidationError();

            if ($v->fail()) {
                    $e = $v->getErrors();
                    $this->template->set('errors', $e);
            } else {
                try {
                    $user = Auth::getUser();

                    $oldPassword = \Input::get('oldPassword');
                    $newPassword = \Input::get('newPassword');
                    $confPassword = \Input::get('confPassword');

                    if ($user->checkPassword($oldPassword)) {

                       if ($newPassword == $confPassword) {
                               $user->password = $newPassword;
                               if ($user->save()) {
                                   \Flash::success('Password successfully changed.');

                                   return \Redirect::to('user/profile/'.$user->id);
                               } else {
                                   \Flash::error('Error while changing password.');
                               }

                       } else {
                               \Flash::error('Password does not match.');
                       }
                    } else {
                        \Flash::error('Old Password does not match.');
                    }
                } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    \Flash::error('User does not exit');
                }
            }
        }

        $this->template->title('Change Password')
            ->breadcrumb('Profile', rbUrl('user/profile'))
            ->breadcrumb('Change Password')
            ->setPartial('change-password');
    }

    /**
     * User registration with activation
     *
    **/
    public function register()
    {
        if(Auth::check()) return \Redirect::to('user');

        if (\Setting::get('user_registration') == 'disable') {
             \Flash::error(sprintf(t('user::user.auth.offreg'), $email));

             return \Redirect::to('');
        }

        if (\Input::isPost()) {

            $v = $this->validate();
            $e = new \Reborn\Form\ValidationError();

            if ($v->fail()) {
                $e = $v->getErrors();
                $this->template->set('errors', $e);
            } else {
                $email = \Input::get('email');
                $first_name = \Input::get('first_name');
                $last_name = \Input::get('last_name');
                $password = \Input::get('password');
                $confpass = \Input::get('confpass');

                if ($password !== $confpass) {
                    \Flash::error(t('user::user.password.fail'));
                } else {

                    try {
                        $user = Auth::register(array(
                            'email'    => $email,
                            'password' => $password,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'permissions' => array(),
                        ));

                        $usermeta = $this->saveMeta($user);

                        if (Module::isEnabled('field')) {
                            Field::save('user', $user);
                        }

                        $groups = Auth::getGroupProvider()->findById(3);
                        $user->addGroup($groups);

                        $activationCode = $user->getActivationCode();
                        $emailEncode = $this->base64UrlEncode($email);

                        $activationLink = url().'user/activate/'.$emailEncode.'/'.$activationCode;

                        $mail = Mailer::create(array('type' => \Setting::get('transport_mail')));
                        $mail->to($email, $first_name);
                        $mail->from(\Setting::get('site_mail'), \Setting::get('site_title'));
                        $mail->subject(t('user::user.activate.subject'));
                        $mail->body('Please active your account by using following link: <br /><a href="'.$activationLink.'">'.$activationLink.'</a>');
                        $mail->send();

                        \Flash::success(t('user::user.activate.check'));

                        return \Redirect::to('/');

                    } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
                        \Flash::error(sprintf(t('user::user.auth.userexist'), $email));
                    }
                }
            }
        }

        $fields = array();

        if (Module::isEnabled('field')) {
            $fields = Field::getForm('user');
        }

        $this->template->title(t('user::user.title.registration'))
            ->breadcrumb(t('user::user.title.registration'))
            ->set('fields', $fields)
            ->setPartial('register');
    }

    /**
    * Activate user account by using email address and activation code
    *
    * @param $email string
    * @param $activationCode string
    */
    public function activate($emailEncode = null, $activationCode = null)
    {
        if(Auth::check()) return \Redirect::to('user');

        try {

            $email = $this->base64UrlDecode($emailEncode);
            $user = Auth::getUserProvider()->findByLogin($email);

            // Attempt user activation
            if ($user->attemptActivation($activationCode)) {
                   \Flash::success(t('user::user.activate.success'));
            } else {
               \Flash::error(t('user::user.activate.fail'));
            }
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            \Flash::error(t('user::user.auth.dunexist'));

            return \Redirect::to('user/register');
        } catch (\Cartalyst\Sentry\Users\UserAlreadyActivatedException $e) {
            \Flash::error(t('user::user.auth.activated'));
        }

        return \Redirect::to('user/login');
    }

    /**
    * Email password reset link ink User
    */
    public function resetPassword()
    {
        if(Auth::check()) return \Redirect::to('user');

        if (\Input::isPost()) {

            $rule = array(
                'email' => 'required|email',
            );
            $v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
            $e = new \Reborn\Form\ValidationError();

            if ($v->fail()) {
                    $e = $v->getErrors();
                    $this->template->set('errors', $e);
            } else {
                $email = \Input::get('email');
                try {
                    $user = Auth::getUserProvider()->findByLogin($email);
                    $resetCode = $user->getResetPasswordCode();
                    $emailEncode = $this->base64UrlEncode($email);

                    $link = url().'user/password-reset/'.$emailEncode.'/'.$resetCode;

                    $mail = Mailer::create(array(\Setting::get('transport_mail')));
                    $mail->to($email, $user->fullname);
                    $mail->from(\Setting::get('site_mail'), \Setting::get('site_title'));
                    $mail->subject('Password Reset Code');
                    $mail->body('Use this link to reset your password: '.$link);
                    $mail->send(); 

                    \Flash::success(t('user::user.resentPass'));

                    return \Redirect::to('/');
                } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    \Flash::error(t('user::user.auth.dunexist'));
                }
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
        if(Auth::check()) return \Redirect::to('user');

        if (\Input::isPost()) {

            $rule = array(
                'new_password' => 'required|minLength:6',
            );
            $v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
            $e = new \Reborn\Form\ValidationError();

            if ($v->fail()) {
                    $e = $v->getErrors();
                    $this->template->set('errors', $e);
            } else {

                $newPassword = \Input::get('new_password');
                $confNewPassword = \Input::get('conf_new_password');

                if ($newPassword !== $confNewPassword) {
                    \Flash::error(t('user::user.password.notMatch'));
                } else {
                    try {
                        $email = $this->base64UrlDecode(\Uri::segment(3));
                        $user = Auth::getUserProvider()->findByLogin($email);

                        if ($user->checkResetPasswordCode($hash)) {
                            // Attempt to reset the user password
                            if ($user->attemptResetPassword($hash, $newPassword)) {
                                \Flash::success(t('user::user.password.success'));

                                return \Redirect::to('user/login');
                            } else {
                                \Flash::error(t('user::user.password.fail'));
                            }
                        } else {
                            \Flash::error(t('user::user.password.invalid'));
                        }
                    } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
                        \Flash::error(t('user::user.auth.dunexist'));

                        return \Redirect::to('user/register');
                    }
                }
            }
        }

        $this->template->title('Reset Password')
            ->breadcrumb('Reset Password')
            ->set('emailEncode', $emailEncode)
            ->set('hash', $hash)
            ->setPartial('reset');
    }

    /**
    * Resend activation link to user
    *
    */
    public function resend()
    {
        if(Auth::check()) return \Redirect::to('user');

        if (\Input::isPost()) {
            $rule = array(
                'email' => 'required|email',
            );
            $v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
            $e = new \Reborn\Form\ValidationError();

            if ($v->fail()) {
                    $e = $v->getErrors();
                    $this->template->set('errors', $e);
            } else {
                $email = \Input::get('email');
                try {
                    $user = Auth::findUserByLogin($email);

                    if ($user->isActivated()) {
                        \Flash::error(sprintf(t('user::user.activate.already'), $email));

                        return \Redirect::to('login');
                    } else {
                        $activationCode = $user->getActivationCode();
                        $emailEncode = $this->base64UrlEncode($email);
                        $activationLink = url().'user/activate/'.$emailEncode.'/'.$activationCode;

                        $mail = Mailer::create(array('type' => \Setting::get('transport_mail')));
                        $mail->to($email, $user->fullname);
                        $mail->from(\Setting::get('site_mail'), \Setting::get('site_title'));
                        $mail->subject(t('user::user.activate.subject'));
                        $mail->body('Please active your account by using following link: <br /><a href="'.$activationLink.'">'.$activationLink.'</a>');
                        $mail->send();            

                        \Flash::success(t('user::user.activate.check'));
                        return \Redirect::to('user/activate');
                    }
                } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    \Flash::error(t('user::user.auth.dunexist'));
                }
            }
        }

        $this->template->title('Resend Activation Code')
            ->breadcrumb('Resend Activation Code')
            ->setPartial('resend');
    }

    /**
     * Save Form Values of Create and Edit Blog
     *
     * @return boolean
     **/
    protected function saveMeta($user)
    {
        $metadata = is_null($user->metadata) ? new \Reborn\Auth\Sentry\Eloquent\UserMetadata : $user->metadata;

        $metadata->user_id = $user->id;
        $metadata->username = \Input::get('username');
        $metadata->biography = \Input::get('biography');
        $metadata->country = \Input::get('country');
        $metadata->website = \Input::get('website');
        $metadata->facebook = \Input::get('facebook');
        $metadata->twitter = \Input::get('twitter');

        return $metadata->save();
    }

    /**
     * Change Password if the user edit password
     *
     * @param $user
     *
     **/
    protected function setPassword($user)
    {
        $password = \Input::get('password');
        $confpass = \Input::get('confpass');

        if ($password) {
            $passwordRule = array(
                'password' => 'required|minLength:6',
            );
            $validatePassword = new \Reborn\Form\Validation(\Input::get('*'), $passwordRule);
            $e = new \Reborn\Form\ValidationError();

            if ($validatePassword->fail()) {
                $e = $validatePassword->getErrors();
                \Flash::error($e);
            } else {
                if ($password) {
                    if ($password == $confpass) {
                        $user->password = $password;
                    } else {
                        \Flash::error(t('user::user.password.fail'));
                    }
                }
            }
        }
    }

    /**
     * base64url variant encoding
     *
     * @param $email string
     * @return string     
     **/
    protected function base64UrlEncode($email)
    {
        return rtrim(strtr(base64_encode($email), '+/', '-_'), '=');        
    }

    /**
     * Decode method for base64url variant encoding
     *
     * @param $encodedEmail string
     * @return string    
     **/
    protected function base64UrlDecode($encodedEmail)
    {        
        return base64_decode(str_pad(strtr($encodedEmail, '-_', '+/'), strlen($encodedEmail) % 4, '=', STR_PAD_RIGHT));
    }

    protected function validate()
    {
        $rule = array(
            'email' => 'required|email',
            'first_name' =>'required|minLength:2|maxLength:40',
            'last_name' => 'required|minLength:2|maxLength:40',
            'password' => 'required|minLength:6',
            'confpass' => 'required|equal:password'
        );

        $v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

        return $v;
    }    
}
