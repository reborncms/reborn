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
	 * Count all User.
	 *
	 * @return integer	 
	 **/
	public function count()
	{
		return $this->createModel()->count();
	}

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

	/**
	 * Find all user by conditional.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param integer|null $limit
	 * @param integer|null $offset
	 * @param array $columns	 
	 * @return \Illuminate\Database\Eloquent\Collection	 
	 **/
	public function findAllBy($key, $value, $limit = null, $offset = null, $columns = array('*'))
	{
		$model = $this->createModel();

		if(!is_null($limit)) {
			$model->take($limit);
		}

		if(!is_null($offset)) {
			$model->skip($offset);
		}

		return $model->where($key, '='. $value)->get($columns);

	}

	/**
	 * Find all user with limit 
	 *
	 * @param integer $limit
	 * @param integer|null $offset
	 * @param array $columns	
	 * @return \Illuminate\Database\Eloquent\Collection	 
	 **/
	public function findAllWithLimit($limit, $offset = null, $columns = array('*'))
	{
		$model = $this->createModel();

		return $model->take($limit)->skip($offset)->get($columns);
	}

	/**
	 * Delete User
	 *
	 * @param integer $id	 
	 * @return void
	 **/
	public function delete($id)
	{
		$model = $this->createModel();

		$model->metadata->delete();

		$model->delete();		
	}
}
