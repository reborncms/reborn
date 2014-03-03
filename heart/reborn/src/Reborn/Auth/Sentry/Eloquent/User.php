<?php

namespace Reborn\Auth\Sentry\Eloquent;

use Cartalyst\Sentry\Users\Eloquent\User as Base;

class User extends Base
{
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

            return gravatar($this->email, $width, $name, 'g', null, $url_only);
        }

        return $img;
    }
}
