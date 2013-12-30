<?php

namespace Reborn\Auth\Sentry\Eloquent;

use Eloquent;

class UserMetadata extends Eloquent
{
	protected $table = 'users_metadata';

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'user_id';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Get relation user model.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 **/
	public function user()
	{
		return $this->belongsTo('Reborn\Auth\Sentry\Eloquent\User');
	}

	/**
	 * Get mutator for User "fullname" attribute.
	 *
	 * @return string
	 */
	/*public function getFullnameAttribute()
	{
		return $this->attributes['first_name'].' '.$this->attributes['last_name'];
	}*/
}
