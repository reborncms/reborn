<?php

namespace User\Model;

class UserMeta extends \Eloquent
{
	protected $table = 'users_metadata';

	protected $primaryKey = 'user_id';

	public $timestamps = false;

	protected $multisite = true;

}