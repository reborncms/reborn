<?php

namespace Reborn\Auth\Sentry\Eloquent;

use Cartalyst\Sentry\Groups\Eloquent\Group as Base;

class Group extends Base
{
	protected $table = 'groups';

	/**
	 * Get relation user model.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 **/
	public function users()
	{
		return $this->hasMany('Reborn\Auth\Sentry\Eloquent\User');
	}
}