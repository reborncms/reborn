<?php

namespace Reborn\Auth\Sentry\Eloquent;

use Cartalyst\Sentry\Groups\Eloquent\Provider;

class GroupProvider extends Provider
{
	/**
	 * The Eloquent group model.
	 *
	 * @var string
	 */
	protected $model = 'Reborn\Auth\Sentry\Eloquent\Group';

	/**
	 * Find All Group.
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
	 * Find Group by conditional.
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
	 * Delete Group by group id.
	 *
	 * @param integer $id
	 * @param string $move_to Group name to move deleted group's users. Default is "User"
	 * @return void
	 **/
	public function delete($id, $move_to = 'User')
	{
		$delete_model = $model = $this->createModel();

		$move = $model->where('name', '=', $move_to)->first();

		// If "$move_to" group is exists,
		// update delete group's user to "$move_to" group.
		if (! is_null($move) ) {
			\DB::table('users_groups')->where('group_id', $id)
            					->update(array('group_id' => (int) $move->id));
		}

		$delete_model->where('id', '=', $id)->delete();
	}
}
