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
     * @param  array                                    $columns
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
     * @param  string                                   $key
     * @param  mixed                                    $value
     * @param  array                                    $columns
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
     * @param  string                                   $key
     * @param  mixed                                    $value
     * @param  integer|null                             $limit
     * @param  integer|null                             $offset
     * @param  array                                    $columns
     * @return \Illuminate\Database\Eloquent\Collection
     **/
    public function findAllBy($key, $value, $limit = null, $offset = null, $columns = array('*'))
    {
        $model = $this->createModel();

        if (!is_null($limit)) {
            $model->take($limit);
        }

        if (!is_null($offset)) {
            $model->skip($offset);
        }

        return $model->where($key, '='. $value)->get($columns);

    }

    /**
     * Find all user with limit
     *
     * @param  integer                                  $limit
     * @param  integer|null                             $offset
     * @param  array                                    $columns
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
     * @param  integer $id
     * @return void
     **/
    public function delete($id)
    {
        $model = $this->createModel();

        $model->metadata->delete();

        $model->delete();
    }

    /**
     * Search user with first name or last name.
     * Example
     * <code>
     * 		// Search for user's name include "nyan"
     *   	User::search('nyan');
     *
     *     	// Search user's name include nyan without ID 1
     *      User::search('nyan', array(1));
     * </code>
     *
     * @param  string                                   $name  User's name keyword to search
     * @param  array                                    $notIn User IDs for whereNotIn
     * @return \Illuminate\Database\Eloquent\Collection
     **/
    public function search($name, $notIn = array())
    {
        $model = $this->createModel();

        if ( ! empty($notIn) ) {
            return $model->whereNotIn('id', $notIn)
                        ->where(function ($q) use ($name) {
                            $q->where('first_name', 'like', '%'.$name.'%')
                                ->orWhere('last_name', 'like', '%'.$name.'%');
                        })->get();
        }

        return $model->where('first_name', 'like', '%'.$name.'%')
                ->orWhere('last_name', 'like', '%'.$name.'%')
                ->get();
    }
}
