<?php

namespace Reborn\Auth\Sentry\Eloquent;

use Cartalyst\Sentry\Users\Eloquent\User as Base;
use Cartalyst\Sentry\Users\UserAlreadyActivatedException;

class User extends Base
{
    protected $dates = [
        'api_login_at'
    ];
    /**
     * Get relation user metadata.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function metadata()
    {
        return $this->hasOne('Reborn\Auth\Sentry\Eloquent\UserMetadata');
    }

    /**
     * Get mutator for User "fullname" attribute.
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return $this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

    /**
     * Get mutator for User "profile_image" attribute.
     *
     * @return string
     */
    public function getProfileImageAttribute()
    {
        return $this->profileImage();
    }

    /**
     * Get Profile Image Url
     *
     * @return string
     **/
    public function getProfileImageLinkAttribute()
    {
        return $this->profileImage(120, true);
    }

    /**
     * Get user's profile image url.
     *
     * @param  integer $width    mage width. Default is 120
     * @param  boolean $url_only Return image url only(without img tag). Default is "false"
     * @return string
     */
    public function profileImage($width = 120, $url_only = false)
    {
        $img = \Event::first('get.user.profile_image', array($this, $width, $url_only));

        if (is_null($img)) {
            $name = $this->getFullnameAttribute();

            return gravatar($this->email, $width, $name, null, 'g', null, $url_only);
        }

        return $img;
    }

    /**
     * Get an api activation code for the given user.
     *
     * @return string
     */
    public function getApiActivationCode()
    {
        $this->api_activation_code = $activationCode = $this->getRandomString(6);

        $this->save();

        return $activationCode;
    }

    /**
     * Attempts to activate the given user by checking
     * the api activate code. If the user is activated already,
     * an Exception is thrown.
     *
     * @param  string  $activationCode
     * @return bool
     * @throws \Cartalyst\Sentry\Users\UserAlreadyActivatedException
     */
    public function attemptActivationForApi($activationCode)
    {
        if ($this->activated)
        {
            throw new UserAlreadyActivatedException('Cannot attempt activation on an already activated user.');
        }

        if ($activationCode == $this->api_activation_code)
        {
            $this->activation_code = null;
            $this->api_activation_code = null;
            $this->activated       = true;
            $this->activated_at    = $this->freshTimestamp();
            return $this->save();
        }

        return false;
    }

    /**
     * Get Api Authenticattion Token
     * 
     * @return string|null
     */
    public function getApiAuthenticateToken()
    {
        return $this->auth_api_token;
    }

    /**
     * Records a api login for the user.
     *
     * @return void
     */
    public function recordApiLogin()
    {
        $this->auth_api_token = $this->getRandomString();
        $this->api_login_at = $this->freshTimestamp();

        $this->recordLogin();
    }

    /**
     * Clean the api login token
     * 
     * @return void
     */
    public function cleanApiLoginToken()
    {
        $this->auth_api_token = null;

        $this->save();
    }
}
