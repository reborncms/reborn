<?php

namespace Reborn\Auth\Sentry\Eloquent;

use Cartalyst\Sentry\Users\Eloquent\Provider;

class UserProvider extends Provider
{
	/**
	 * The Eloquent user model.
	 *
	 * @var string
	 */
	protected $model = 'Reborn\Auth\Sentry\Eloquent\User';

	/**
	 * Find All User.
	 *
	 * @param array $columns
	 * @return \Illuminate\Database\Eloquent\Collection
	 **/
	public function all($columns = array('*'))
	{
		$model = $this->createModel();

		return $model->all($columns);
	}

	/**
	 * Find user by conditional.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param array $columns
	 * @return \Illuminate\Database\Eloquent\Collection
	 **/
	public function findBy($key, $value, $columns = array('*'))
	{
		$model = $this->createModel();

		return $model->where($key, '=', $value)->get($columns)->first();
	}
}
