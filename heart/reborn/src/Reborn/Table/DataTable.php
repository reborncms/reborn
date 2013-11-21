<?php

namespace Reborn\Table;

use Closure;
use Reborn\Util\Str;
use Reborn\Util\Html;
use Reborn\Http\Input;

/**
 * jQuery DataTable Class
 *
 * @package Reborn\Table
 * @author Myanmar Links Web Development Team
 **/

class DataTable
{
	/**
	 * Options for Datatable
	 *
	 * @var array
	 **/
	protected $options = array();

	/**
	 * Actions data list for Datatable
	 *
	 * @var array
	 **/
	protected $actions = array();

	/**
	 * Action links url prefix
	 *
	 * @var string
	 **/
	protected $action_prefix;

	/**
	 * Eloquent Model for Query Processing
	 *
	 * @var string|\Illuminate\Database\Eloquent\Model
	 **/
	protected $model;

	/**
	 * Bind custom query for datatable.
	 *
	 * @var array
	 **/
	protected $custom_query = array();

	/**
	 * Key value columns data for datatable
	 *
	 * @var array
	 **/
	protected $columns = array();

	/**
	 * Column width variable
	 *
	 * @var array
	 **/
	protected $column_width = array();

	/**
	 * Column lists for view render
	 *
	 * @var array
	 **/
	protected $column_lists = array();

	/**
	 * Total record form Database
	 *
	 * @var integer|null
	 **/
	protected $total;

	/**
	 * Total record by filtering form Database
	 *
	 * @var integer
	 **/
	protected $filter_total = 0;

	/**
	 * Need to load the jquery.datatable.js
	 *
	 * @var boolean
	 **/
	protected $need_js = true;

	/**
	 * Custom CSS file path
	 *
	 * @var string
	 **/
	protected $css_file;

	/**
	 * Transformer callback list for show value
	 *
	 * @var array
	 **/
	protected $transformers = array();

	/**
	 * undocumented class variable
	 *
	 * @var array
	 **/
	protected $sort_disable = array();

	/**
	 * Static Method to create new instance.
	 *
	 * @param string|\Illuminate\Database\Eloquent\Model $model
	 * @param array $columns
	 * @return \Reborn\Table\DataTable
	 */
	public static function make($model, $columns = array())
	{
		return new static($model, $columns);
	}

	/**
	 * Default instance method.
	 *
	 * @param string|\Illuminate\Database\Eloquent\Model $model
	 * @param array $columns
	 * @return void
	 **/
	public function __construct($model, $columns = array())
	{
		if (is_string($model)) {
			$model = new $model();
		}

		$this->model = $model;

		if (! empty($columns) ) {
			$this->columns($columns);
		}
	}

	/**
	 * Bind custom query for datatable
	 *
	 * @param \Closure $callback
	 * @return \Reborn\Table\DataTable
	 **/
	public function query(Closure $callback)
	{
		$this->custom_query[] = $callback;

		return $this;
	}

	/**
	 * Check to load js file.
	 *
	 * @return boolean
	 **/
	public function needDatatableJs()
	{
		return $this->need_js;
	}

	/**
	 * Skip JS file loading process.
	 * Becaus already loaded by manually.
	 *
	 * @return \Reborn\Table\DataTable
	 **/
	public function skipJsLoader()
	{
		$this->need_js = false;

		return $this;
	}

	/**
	 * Set custom css file for datatable
	 *
	 * @param string $url CSS file url
	 * @return \Reborn\Table\DataTable
	 **/
	public function customCss($url)
	{
		$this->css_file = $url;

		return $this;
	}

	/**
	 * Get custom css file url string
	 *
	 * @return string
	 **/
	public function getCustomCss()
	{
		return $this->css_file;
	}

	/**
	 * Check custom css use or not
	 *
	 * @return boolean
	 **/
	public function useCustomCss()
	{
		return (!is_null($this->css_file));
	}

	/**
	 * Set Columns for Datatable
	 *
	 * @param array $columns
	 * @return \Reborn\Table\DataTable
	 */
	public function columns(array $columns)
	{
		foreach ($columns as $name => $label) {
			$column = array();
			$column['name'] = $name;
			$column['label'] = $label;
			$this->columns[] = $column;
			$this->column_lists[$name] = $label;
		}

		return $this;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function notSortable(array $columns)
	{
		$this->sort_disable = $columns;

		return $this;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function getNotSortable()
	{
		$results = array();

		// for actions columns
		if (! empty($this->actions) ) {
			$results[] = count($this->columns);
		}

		foreach ($this->sort_disable as $name) {

			foreach ($this->columns as $k => $col) {
				if ($name == $col['name']) {
					$results[] = $k;
					break;
				}
			}
		}

		return json_encode($results);
	}

	/**
	 * Set columns width
	 *
	 * @param array $width
	 * @return \Reborn\Table\DataTable
	 **/
	public function columnWidth(array $width)
	{
		$this->column_width = $width;

		return $this;
	}

	/**
	 * Get column (th) width by key or all
	 *
	 * @param string|null $key
	 * @param string $default
	 * @return array|string
	 **/
	public function getColumnWidth($key = null, $default = 'auto')
	{
		if (! is_null($key) and isset($this->column_width[$key])) {
			return $this->column_width[$key];
		}

		if (is_null($key)) {
			return $this->column_width;
		}

		return $default;
	}

	/**
	 * Set transformer callback for column.
	 * example :: want to transform {id} to #{id}
	 * <code>
	 * 		$table->transformer('id', function($value){
	 * 			return '#'.$value;
	 * 		});
	 * </code>
	 *
	 * @param string $for
	 * @param \Closure $callback
	 * @return \Reborn\Table\DataTable
	 **/
	public function transformer($for, Closure $callback)
	{
		$this->transformers[$for] = $callback;

		return $this;
	}

	/**
	 * Set options for datatable script
	 *
	 * @param array $options
	 * @return \Reborn\Table\DataTable
	 **/
	public function options(array $options = array())
	{
		if (! empty($options) ) {
			$this->options = array_merge($this->options, $options);
		}

		return $this;
	}

	/**
	 * Get options by key or all options
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return string
	 **/
	public function getOption($key = null, $default = null)
	{
		if (is_null($key)) {
			return $this->options;
		}

		return array_get($this->options, $key, $default);
	}

	/**
	 * Set actions column at datatable
	 *
	 * @param string $prefix url prefix (module's url)
	 * @param array $actions
	 * @return \Reborn\Table\DataTable
	 **/
	public function actions($prefix, $actions)
	{
		$this->actions = $actions;
		$this->action_prefix = $prefix;

		return $this;
	}

	/**
	 * Check has action column for datatable
	 *
	 * @return boolean
	 **/
	public function hasActions()
	{
		return (!empty($this->actions));
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 **/
	public function getSortingDisable()
	{

	}

	/**
	 * Get all column lists for column iteration
	 *
	 * @return array
	 **/
	public function getColumns()
	{
		return $this->column_lists;
	}

	/**
	 * Check data is empty or not
	 *
	 * @return boolean
	 **/
	public function isEmpty()
	{
		if (is_null($this->total)) {
			$this->resolveCustomQuery();

			$this->total = $this->model->count();
		}

		return ($this->total < 1);
	}

	/**
	 * Build datatable final result.
	 *
	 * @return array
	 **/
	public function build()
	{
		// Make Custom Query Binding
		if (! empty($this->custom_query) ) {
			$this->resolveCustomQuery();
		}

		$this->makeFiltering();

		$this->makeCounting();

		$this->makePaging();

		$this->makeOrdering();

		return $this->makeRendering();
	}

	/**
	 * Build Datatable result with Json format
	 *
	 * @return string
	 **/
	public function buildJson()
	{
		return json_encode($this->build());
	}

	/**
	 * Make Filtering Process
	 *
	 * @return void
	 **/
	protected function makeFiltering()
	{
		$count = $this->getColumnCount();

		if (Input::get('sSearch','') != '') {
			$this-> makeSearchFiltering($count);
		}

		// Individual column filtering
		$this->makeColumnFiltering($count);
	}

	/**
	 * Make Filter by Search Keyword
	 *
	 * @param integer $count Total columns
	 * @return void
	 **/
	protected function makeSearchFiltering($count)
	{
		$keyword = '%'.Input::get('sSearch').'%';

		for ($i=0; $i < $count; $i++) {
			// Make Filter Query if column[$i] is searchable
			if (Input::get('bSearchable_'.$i) == "true") {
				$column = $this->getColumnKeyName($i);

				if (!is_null($column)) {
					if ($i === 0) {
						$this->model = $this->model->where($column, 'like', $keyword);
					} else {
						$this->model = $this->model->orWhere($column, 'like', $keyword);
					}
				}
			}
		}
	}

	/**
	 * Make Individual column filtering
	 *
	 * @param integer $count Total columns
	 * @return void
	 **/
	protected function makeColumnFiltering($count)
	{
		for ($i=0; $i < $count; $i++) {
			// Make Filter Query if column[$i] is searchable
			if (Input::get('bSearchable_'.$i) == "true"
				&& Input::get('sSearch_'.$i, '') != '') {

				$column = $this->getColumnKeyName($i);

				if (!is_null($column)) {
					if ($i === 0) {
						$this->model = $this->model->where($column, 'like', $keyword);
					} else {
						$this->model = $this->model->andWhere($column, 'like', $keyword);
					}
				}
			}
		}
	}

	/**
	 * Count filtering result and total result
	 *
	 * @return void
	 */
	protected function makeCounting()
	{
		$this->total = $this->model->count();

		$model = $this->model;

		$this->filter_total = (int) $model->count();
	}

	/**
	 * Make Paging for datatable result
	 *
	 * @return void
	 */
	protected function makePaging()
	{
		if(!is_null(Input::get('iDisplayStart')) && Input::get('iDisplayLength') != -1)
		{
			$this->model = $this->model->skip(Input::get('iDisplayStart'))
									->take(Input::get('iDisplayLength',10));
		}
	}

	/**
	 * Make Ordering for datatable result
	 *
	 * @return void
	 */
	protected function makeOrdering()
	{
		// Last we make ordering
		if (! is_null(Input::get('iSortCol_0')) ) {
			$total = intval(Input::get('iSortingCols'));

			for ($i = 0; $i < $total; $i++) {
				if (Input::get('bSortable_'.intval(Input::get('iSortCol_'.$i))) == "true") {
					$column = $this->getColumnKeyName(Input::get('iSortCol_'.$i));

					$sort_dir = Input::get('sSortDir_'.$i);

					if (false == strpos($column, '.')) {
						$this->model = $this->model
									->orderBy($column, $sort_dir);
					}
				}
			}

		}
	}

	/**
	 * Make rendering with data result and format of datatable
	 *
	 * @return array
	 */
	protected function makeRendering()
	{
		// Final step for DataTable
		$results = $this->getResult();

		$output = array(
			"sEcho" => intval(Input::get('sEcho')),
			"iTotalRecords" => $this->total,
			"iTotalDisplayRecords" => $this->filter_total,
			"aaData" => $results
		);

		return $output;
	}

	/**
	 * Get data result from Model
	 *
	 * @return array
	 **/
	protected function getResult()
	{
		$results = $this->model->get();

		$data = array();

		$columns = $this->getAllColumnName();

		foreach ($results as $result) {
			$tmp = array();
			foreach ($columns as $col) {
				$tmp[] = $this->getTransformValue($col, $result);
			}

			if (! empty($this->actions) ) {
				$tmp[] = $this->makeActionsColumn($result);
			}

			$data[] = $tmp;
			unset($tmp);
		}

		return $data;
	}

	/**
	 * Resolve the custom query binding
	 * and reset custom_query value
	 *
	 * @return void
	 **/
	protected function resolveCustomQuery()
	{
		foreach ($this->custom_query as $query) {
			$this->model = $query($this->model);
		}

		$this->custom_query = array();
	}

	/**
	 * Transform value if have transformer callback
	 *
	 * @param string $for
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return string
	 **/
	protected function getTransformValue($for, $model)
	{
		$list = explode('.', $for);

		$value = (count($list) == 2) ? $model->{$list[0]}->{$list[1]} : $model->{$list[0]};

		if (isset($this->transformers[$for])) {
			$callback = $this->transformers[$for];
			return $callback($value, $model);
		}

		return $value;
	}

	/**
	 * Make Action column data
	 *
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return string
	 **/
	protected function makeActionsColumn($model)
	{
		$view = '<div class="icons-bar">';
		foreach ($this->actions as $action) {
			list($url, $icon, $attrs) = $this->parserActionData($action, $model);

			$view .= Html::a($url, $icon, $attrs);
		}

		return $view.'</div>';
	}

	/**
	 * Parse action data string to data array
	 *
	 * @param string $action
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return array
	 **/
	protected function parserActionData($action, $model)
	{
		// Check first char is "!". This sign is not include admin url
		$first = substr($action, 0, 1);

		// Explode action string by ":"
		// [0] is action,
		// [1] is model's key
		// [2] is icon-class,
		// [3] is Tooltip text
		$explode = explode(':', $action);

		if ('!' === $first) {
			$explode[0] = substr($explode[0], 1);
			$url = rbUrl($this->action_prefix.'/'.$explode[0]).$model->$explode[1];
		} else {
			$url = adminUrl($this->action_prefix.'/'.$explode[0]).$model->$explode[1];
		}

		$icon = isset($explode[2]) ? $explode[2] : $explode[0];

		$attrs = array();
		$attrs['class'] = ('delete' === $explode[0]) ? 'confirm_delete tipsy-tip'
													: 'tipsy-tip';

		$attrs['title'] = isset($explode[3]) ? $explode[3] : Str::title($explode[0]);

		return array($url, '<i class="icon-'.$icon.'"></i>', $attrs);
	}

	/**
	 * Get Column count value for iteration
	 *
	 * @return integer
	 **/
	protected function getColumnCount()
	{
		return count($this->columns);
	}

	/**
	 * Get Column Name by Key
	 *
	 * @param integer $key
	 * @return null|string
	 **/
	protected function getColumnKeyName($key)
	{
		if (isset($this->columns[$key])) {
			return $this->columns[$key]['name'];
		}

		return null;
	}

	/**
	 * Get All Column Names
	 *
	 * @param integer $key
	 * @return null|string
	 **/
	protected function getAllColumnName()
	{
		$names = array_map(function($column)
		{
			return $column['name'];
		}, $this->columns);

		return $names;
	}

	/**
	 * PHP's magic method __toString.
	 *
	 * @return array
	 **/
	public function __toString()
	{
		return $this->build();
	}

}
