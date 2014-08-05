<?php 

namespace Blog\Extensions;

use League\Fractal\TransformerAbstract;

use Reborn\Auth\Sentry\Eloquent\User;

class UserTransformer extends TransformerAbstract
{

	public function transform(User $user)
    {

        return array(
            'id'        => $user->id,
            'name'      => $user->fullname,
            'email'     => $user->email,
            'url'       => url('user/profile/'.$user->id),
            'avatar'    => $user->profile_image_link,
            'biography' => $user->metadata->biography,
            'country'   => $user->metadata->country,
            'website'   => $user->metadata->website,
            'facebook'  => $user->metadata->facebook,
            'twitter'   => $user->metadata->twitter
        );

    }

}