<?php

namespace Reborn\Auth\Sentry\Eloquent;

use Eloquent;

class Group extends Eloquent
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
