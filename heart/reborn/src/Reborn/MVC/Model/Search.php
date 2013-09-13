<?php

namespace Reborn\MVC\Model;


/**
 * Search Model
 *
 * @package Reborn\MVC\Model
 * @author Myanmar Links Professional Web Development
 **/
class Search extends Model
{
	/**
	 * skip variable
	 *
	 * @var integer
	 **/
	protected $skip;

	/**
	 * limit variable
	 *
	 * @var integer
	 **/
	protected $limit = 20;

	/**
	 * wheres variable
	 *
	 * @var array
	 **/
	protected $wheres = array();

	/**
	 * Joint keyword (or, and) for Multiple columns
	 *
	 * @var string
	 **/
	protected $joint = 'or';

	/**
	 * Supported Joint keyword lists
	 *
	 * @var array
	 **/
	protected $support_joints = array('on', 'and');

	/**
	 * Order list
	 *
	 * @var array
	 **/
	protected $order_by = array();

	/**
	 * Static method for Search
	 *
	 * @param string $table Table name
	 * @return void
	 **/
	public static function make($table = '')
	{
		$ins = new static();

		return $ins;
	}

	/**
	 * Setter for joint
	 *
	 * @param string $joint
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function joint($joint = 'or')
	{
		$joint = strtolower($joint);

		if (in_array($joint, $this->support_joints)) {
			$this->joint = $joint;
		}

		return $this;
	}

	/**
	 * Text must be this value
	 *
	 * @param string $field Field column name
	 * @param string $text field value
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function must($field, $text)
	{
		$this->wheres[$field] = array('=', $text);

		return $this;
	}

	/**
	 * Text start with this value
	 *
	 * @param string $field Field column name
	 * @param string $text field value
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function startWith($field, $text)
	{
		$this->wheres[$field] = array('like', $text.'%');

		return $this;
	}

	/**
	 * Text end with this value
	 *
	 * @param string $field Field column name
	 * @param string $text field value
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function endWith($field, $text)
	{
		$this->wheres[$field] = array('like', '%'.$text);

		return $this;
	}

	/**
	 * Text contain this value
	 *
	 * @param string $field Field column name
	 * @param string $text field value
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function contain($field, $text)
	{
		$this->wheres[$field] = array('like', '%'.$text.'%');

		return $this;
	}

	/**
	 * Text contain this value
	 *
	 * @param string $field Field column name
	 * @param string $text field value
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function charLength($field, $length)
	{
		if (is_string($length) and !is_numeric($length)) {
			$length = strlen($length);
		}

		$text = str_repeat('_', (int)$length);
		$this->wheres[$field] = array('like', $text);

		return $this;
	}

	/**
	 * Set order by DESC
	 *
	 * @param string|array $columns
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function desc($columns)
	{
		$columns = array($columns);

		$this->order_by['desc'] = $columns;

		return $this;
	}

	/**
	 * Set order by ASC
	 *
	 * @param string|array $columns
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function asc($columns)
	{
		$columns = array($columns);

		$this->order_by['asc'] = $columns;

		return $this;
	}

	/**
	 * Skip limit for query result
	 *
	 * @param integer $skip
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function skip($skip)
	{
		$this->skip = $skip;

		return $this;
	}

	/**
	 * Limit for query result
	 *
	 * @param integer $skip
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function take($limit)
	{
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Get search Query Result
	 *
	 * @param  array  $columns
	 * @return array|static[]
	 **/
	public function get($columns = array('*'))
	{
		$builder = $this->prepare();

		$builder->take($this->limit);

		if(! is_null($this->skip) ) {
			$builder->skip($this->skip);
		}

		return $builder->get($columns);
	}

	/**
	 * Set table name
	 *
	 * @return \Reborn\MVC\Model\Search
	 **/
	public function setTable($table)
	{
		$this->table = $table;

		return $this;
	}

	/**
	 * Prepare Query
	 *
	 * @return void
	 **/
	protected function prepare()
	{
		$builder = $this->newQuery();

		$i = 0;

		foreach ($this->wheres as $column => $w) {
			if ($i > 0) {
				$builder->where($column, $w[0], $w[1], $this->joint);
			} else {
				$builder->where($column, $w[0], $w[1]);
			}
			$i++;
		}

		// Make Order
		$order_by = $this->order_by;
		if (isset($order_by['desc'])) {
			foreach ($order_by['desc'] as $col) {
				$builder->orderBy($col, 'desc');
			}
		}

		if (isset($order_by['asc'])) {
			foreach ($order_by['asc'] as $col) {
				$builder->orderBy($col, 'asc');
			}
		}

		return $builder;
	}

} // END class Search
