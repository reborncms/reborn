<?php

namespace Reborn\Table;

use MongoDate;
use Carbon\Carbon;
use Reborn\Form\Form;

/**
 * Table Generate Helper class
 *
 * @package Reborn\Table
 * @author Myanmar Links Web Development Team
 **/

class Builder
{
	/**
	 * tabel header (th) variable
	 *
	 * @var array
	 **/
	protected $headers = array();

	/**
	 * Use table footer
	 *
	 * @var boolean
	 **/
	protected $use_tfoot = false;

	/**
	 * table columns (td) variable
	 *
	 * @var array
	 **/
	protected $cols = array();

	/**
	 * Add check all select box variable
	 *
	 * @var boolean
	 **/
	protected $check_all = false;

	/**
	 * Add row actions buttons data variable
	 *
	 * @var array
	 **/
	protected $actions = array();

	/**
	 * CSS class name for table variable
	 *
	 * @var string
	 **/
	protected $table_class;

	/**
	 * CSS id name for table variable
	 *
	 * @var string
	 **/
	protected $table_id;

	/**
	 * table data provider
	 *
	 * @var mixed (May be Array, Object or EloquentCollection)
	 **/
	protected $provider;

	/**
	 * Action buttons type name
	 * Support Type name are
	 *  - icon [Icon only] eg: <a href="#"><i class="icon-class"></i></a>
	 *  - icons-bar [Icons group with div.icons-bar wrapping]
	 *  - icon-text [Icon and Text] eg: <a href="#"><i class="icon-class"></i>TextLabel</a>
	 *  - text-icon [Text and Icon] eg: <a href="#">TextLabel<i class="icon-class"></i></a>
	 *  - text [Text Only] eg: <a href="#">TextLabel</a>
	 *
	 * Default value is text
	 *
	 * @var string
	 **/
	protected $button_type = 'text';


	/**
	 * Static method for Table Instance
	 *
	 * @param array $options
	 * @return \Reborn\Util\Table
	 **/
	public static function create($options = array())
	{
		return new static($options);
	}

	/**
	 * Default Constructor Method for Table
	 *
	 * Avaliable options list.
	 *  - check_all [Boolean] Use Check All check box in table
	 *  - actions [Array] Same with setActions param.
	 *  - class [String] Class name for table tag
	 *  - id [String] Id name for table tag
	 *  - object [Object] Data Object
	 *  - btn_type [String] Action Button Type Name
	 *
	 * @param array $options
	 * @return void
	 **/
	public function __construct($options = array())
	{
		if (isset($options['check_all'])) {
			$this->check_all = (boolean) $options['check_all'];
		}

		if (isset($options['actions'])) {
			$this->actions($options['actions']);
		}

		if (isset($options['class'])) {
			$this->tableClass($options['class']);
		}

		if (isset($options['id'])) {
			$this->tableId($options['id']);
		}

		if (isset($options['provider'])) {
			$this->provider($options['provider']);
		}

		if (isset($options['use_tfoot'])) {
			$this->use_tfoot = (boolean) $options['use_tfoot'];
		}

		if (isset($options['btn_type'])) {
			$this->actionType($options['btn_type']);
		}
	}

	/**
	 * Set the Data Provider for table.
	 *
	 * @param mixed $provider Data provider (May be Model or array)
	 * @return \Reborn\Util\Table
	 **/
	public function provider($provider)
	{
		if (is_object($provider) || is_array($provider)) {
			$this->provider = $provider;
		}

		return $this;
	}

	/**
	 * Set the Table tag's class value
	 *
	 * @param string $classes Tabel class [css] string
	 * @return \Reborn\Util\Table
	 **/
	public function tableClass($classes)
	{
		$this->table_class = $classes;

		return $this;
	}

	/**
	 * Set the Table tag's id value
	 *
	 * @param string $id Tabel id string
	 * @return \Reborn\Util\Table
	 **/
	public function tableId($id)
	{
		$this->table_id = $id;

		return $this;
	}

	/**
	 * Set the Action Button Type
	 *
	 * @param string $type Action Button Type
	 * @return \Reborn\Util\Table
	 **/
	public function actionType($type)
	{
		if (in_array($type, array('text', 'icon-text', 'text-icon', 'icon', 'icons-bar'))) {
			$this->button_type = $type;
		}

		return $this;
	}

	/**
	 * Set the table's action buttons for each row.
	 *
	 * <code>
	 * $actions = array(
	 * 					'edit' => array(
	 * 								'title' => 'Edit',
	 * 								'btn-class' => 'blue',
	 * 								'url' => rbUrl('blog/edit'), // adminUrl('blog/edit')
	 * 								'icon' => 'icon-edit'),
	 * 					'delete' => array(
	 * 								'title' => 'Delete',
	 * 								'btn-class' => 'red',
	 * 								'url' => rbUrl('blog/delete'), // adminUrl('blog/delete')
	 * 								'icon' => 'icon-delete')
	 * 				);
	 * $tabel->setActions($actions);
	 * </code>
	 *
	 * @return \Reborn\Util\Table
	 **/
	public function actions($actions = array())
	{
		foreach ($actions as $name => $action) {
			$this->prepareActionsLink($name, $action);
		}

		return $this;
	}

	/**
	 * Set the table's single action button (or) text.
	 *
	 * @param string $name
	 * @param string $title
	 * @param string|null $url
	 * @param string|null $icon
	 * @param string|null $class
	 * @return \Reborn\Util\Table
	 **/
	public function action($name, $title, $url = null, $icon = null, $class = null)
	{
		$action = array('title' => $title);

		if (! is_null($url) ) {
			$action['url'] = $url;
		}

		if (! is_null($icon) ) {
			$action['icon'] = $icon;
		}

		if (! is_null($class) ) {
			$action['btn-class'] = $class;
		}

		$this->prepareActionsLink($name, $action);

		return $this;
	}

	/**
	 * Prepare the Actions Link (button) data array
	 *
	 * @param string $name
	 * @param array $options
	 * @return void
	 **/
	protected function prepareActionsLink($name, $options)
	{
		$act = array();
		$act['title'] = isset($options['title']) ? $options['title'] : ucfirst($name);
		$act['btn_class'] = isset($options['btn-class']) ? $options['btn-class'] : '';
		$act['icon'] = isset($options['icon']) ? $options['icon'] : '';

		if (isset($options['url'])) {
			$url = $options['url'];

			$act['url'] = $url;
			$act['key'] = array();

			if (preg_match_all('/\[:(\w+)\]/', $url, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					$act['key'][] = $match[1];
				}
			}
		}

		$act['target'] = isset($options['new_window']) ? (boolean)$options['new_window'] : false;

		$this->actions[$name] = $act;
	}

	/**
	 * Set table header field (<th> value)
	 *
	 * <code>
	 *  // Simple Setter
	 * 	$ths = array('Title', 'Category', 'Created At');
	 *  $table->headers($ths);
	 *
	 *  // But I want to set th width for some columns. How to?
	 *  // Ok. Use multideminsional array
	 *  $ths = array('Name', array('name' => 'Email', 'width' => '25%'), array('name' => 'Created'));
	 *  $table->headers($ths);
	 *  // Output is...
	 *  // <tr>
	 *  //   <th>Name</th>
	 *  //   <th width="25%">Email</th>
	 *  //   <th width="auto">Created</th>
	 *  // </tr>
	 * </code>
	 *
	 * @param array $headers
	 * @return \Reborn\Util\Table
	 **/
	public function headers( array $headers)
	{
		$this->headers = $headers;

		return $this;
	}

	/**
	 * Add table columns data array
	 *
	 * @param array $cols
	 * @return \Reborn\Util\Table
	 **/
	public function columns($cols = array())
	{
		foreach ($cols as $name => $col) {
			if (is_array($cols[$name])) {
				$this->cols[$name]['key'] = isset($col['key']) ? $col['key'] : $name;
				$this->cols[$name]['type'] = $col['type'];
				if ($col['type'] == 'date') {
					$format = isset($col['format']) ? $col['format'] : 'Y-m-d';
					$this->cols[$name]['format'] = $format;
				}
			} else {
				$this->cols[$col] = array('key' => $col);
			}
		}

		return $this;
	}

	/**
	 * Check table data is empty or not.
	 *
	 * @return boolean
	 **/
	public function isEmpty()
	{
		if (is_object($this->provider) and method_exists($this->provider, 'isEmpty')) {
			return $this->provider->isEmpty();
		}

		return empty($this->provider);
	}

	/**
	 * Build the Table Output.
	 *
	 * @return string
	 **/
	public function build()
	{
		if($this->isEmpty()) {
			return null;
		}

		$result = $this->renderTableOpen();

		if (! empty($this->headers) ) {
			$result .= $this->renderTableHeader();

			if ($this->use_tfoot) {
				$result .= $this->renderTableFooter();
			}
		}

		$result .= $this->renderTableBody();

		$result .= $this->renderTableClose();

		return $result;
	}

	/**
	 * Render Table Tag's Open tag (<table>).
	 *
	 * @return string
	 **/
	protected function renderTableOpen()
	{
		$id = !is_null($this->table_id) ? ' id="'.$this->table_id.'" ' : '';
		$class = !is_null($this->table_class) ? 'class="'.$this->table_class.'"' : '';

		return '<table '.$class.$id.'>'."\n";
	}

	/**
	 * Render Table Tag's Close tag (</table>).
	 *
	 * @return string
	 **/
	protected function renderTableClose()
	{
		return '</table>'."\n";
	}

	/**
	 * Render the table header (<thead>....</thead>)
	 *
	 * @return string
	 **/
	protected function renderTableHeader()
	{
		$head_row = "\t<thead>\n\t\t<tr class=\"table-head\">";

		if ($this->check_all) {
			$head_row .= "\n\t\t\t<th width=\"5%\">";
			$head_row .= "\n\t\t\t\t";
			$head_row .= '<input id="action_to_all" class="check-all" type="checkbox" name="action_to_all">';
			$head_row .= "\n\t\t\t</th>";
		}

		$head_row .= $this->renderTableTh();

		$head_row .="\n\t\t</tr>\n\t</thead>";

		return $head_row;
	}

	/**
	 * Render the table footer (<thead>....</thead>)
	 *
	 * @return string
	 **/
	protected function renderTableFooter()
	{
		$foot_row = "\t<tfoot>\n\t\t<tr class=\"table-footer\">";

		if ($this->check_all) {
			$foot_row .= "\n\t\t\t<th width=\"5%\">";
			$foot_row .= "\n\t\t\t\t";
			$foot_row .= '<input id="action_to_all_foot" class="check-all" type="checkbox" name="action_to_all">';
			$foot_row .= "\n\t\t\t</th>";
		}

		$foot_row .= $this->renderTableTh();

		$foot_row .="\n\t\t</tr>\n\t</tfoot>";

		return $foot_row;
	}

	/**
	 * Render the table th.
	 *
	 * @return string
	 **/
	protected function renderTableTh()
	{
		$total_head = count($this->headers);
		$total_cols = count($this->cols);

		$ths = '';

		foreach ($this->headers as $key => $value) {
			if (is_array($value)) {
				$width = isset($value['width']) ? $value['width'] : 'auto';
				$name = isset($value['name']) ? $value['name'] : '';
				$ths .= "\n\t\t\t<th width=\"$width\">".$name."</th>";
			} else {
				$ths .= "\n\t\t\t<th>".$value."</th>";
			}
		}

		if (! empty($this->actions) ) {
			if ($total_head <= $total_cols) {
				$multi = $total_cols - $total_head;
				if ($multi == 0) {
					$multi = 1;
				}

				$string = "\n\t\t\t<th></th>";

				$ths .= str_repeat($string, $multi);
			}
		}

		return $ths;
	}

	/**
	 * Render the table body (<tbody>...</tbody>)
	 *
	 * @return string
	 **/
	protected function renderTableBody()
	{
		$body = "\n\t<tbody class=\"table-body\">";

		foreach ($this->provider as $key => $data) {
			$body .= $this->renderTableRow($data, $key);
		}

		$body .= "\n\t</tbody>\n";

		return $body;
	}

	/**
	 * Render the table row (<tr>....</tr>)
	 *
	 * @param mixed $data
	 * @param int $key Array key from Object Collection.
	 * 					Use for HTML Tag class attribute name only.
	 * @return string
	 **/
	protected function renderTableRow($data, $key)
	{
		$no = $key + 1; // Because key is start from 0.
		$row = "\n\t\t<tr class=\"row-$no\">";

		if ($this->check_all) {
			$id = is_array($data) ? $data['id'] : $data->id;
			$row .= "\n\t\t\t".'<td class="checkbox">';
			$row .= "\n\t\t\t\t";
			$row .= Form::checkbox('action_to[]', $id, false, array('id' => 'action'.$no));
			$row .= "\n\t\t\t</td>";
		}

		$row .= $this->renderTableColumn($data);

		if (! empty($this->actions) ) {
			$row .= $this->renderActionsColumn($data);
		}

		$row .= "\n\t\t</tr>";

		return $row;
	}

	/**
	 * Render the data Column for table (<td>...</td>)
	 *
	 * @param mixed $data
	 * @return string
	 **/
	protected function renderTableColumn($data)
	{
		$cols = '';

		foreach ($this->cols as $k => $col) {
			$cols .= "\n\t\t\t<td>";

			if (isset($col['type']) and ('date' == $col['type'])) {
				$cols .= $this->getObjectAttributeDateTime($data, $col['key'], $col['format']);
			} else {
				$cols .= $this->getObjectAttribute($data, $col['key']);
			}

			$cols .= '</td>';
		}

		return $cols;
	}

	/**
	 * Get the Object's attribute value
	 *
	 * @param mixed $data
	 * @param string $key Object's key name
	 * @return string
	 **/
	protected function getObjectAttribute($data, $key)
	{
		if (false === strpos($key, '.')) {
			if(is_object($data)) {
				return $data->{$key};
			} elseif(is_array($data)) {
				return $data[$key];
			} else {
				return $data;
			}
		}

		$keys = explode('.', $key, 2);

		return $this->getObjectAttribute($data->{$keys[0]}, $keys[1]);
	}

	/**
	 * Get the Object's datetime attribute value
	 *
	 * @param Object $obj ModelObject
	 * @param string $key Object's key name
	 * @param string $format Datetime format string
	 * @return string
	 **/
	protected function getObjectAttributeDateTime($data, $key, $format)
	{
		$date = is_array($data) ? $data[$key] : $data->{$key};

		if ($data instanceof \Eloquent) {
			return date_format($date, $format);
		} elseif ($date instanceof MongoDate) {
			return date($format, $date->sec);
		}

		return Carbon::createFromFormat($format, $date)->toDateTimeString();
	}

	/**
	 * Render the Action Buttons Column for table
	 *
	 * @param mixed $data
	 * @return string
	 **/
	protected function renderActionsColumn($data)
	{
		$row = "\n\t\t\t<td class=\"td-actions\">";

		if('icons-bar' == $this->button_type) {
			$row .= '<div class="icons-bar">';
		}

		foreach ($this->actions as $action) {

			if (isset($action['url'])) {
				foreach ($action['key'] as $attr) {

					if (is_array($data)) {
						$replace = isset($data[$attr]) ? $data[$attr] : null;
					} else {
						$replace = $data->{$attr};
					}

					$action['url'] = str_replace(
										'[:'.$attr.']',
										$replace,
										$action['url']
									);
				}
			}

			$row .= $this->getButton($action);
		}

		if('icons-bar' == $this->button_type) {
			$row .= '</div>';
		}

		$row .= "\n\t\t\t</td>";

		return $row;
	}

	/**
	 * Get the Anchor HTML Code for action button
	 *
	 * @param array $attrs Button Attribute
	 * @return string
	 **/
	protected function getButton($attrs)
	{
		$btn = "\n\t\t\t\t";
		$btn .= '<a href="'.$attrs['url'].'" ';

		if ($attrs['target']) {
			$btn .= 'target="_blank" ';
		}

		$btn .= 'title="'.$attrs['title'].'" ';
		$btn .= 'class="'.$attrs['btn_class'].' tipsy-tip">';
		$btn .= "\n\t\t\t\t\t";
		$btn .= $this->getButtonData($attrs);
		$btn .= "\n\t\t\t\t</a>";

		return $btn;
	}

	/**
	 * Get Action Button Inner Data
	 *
	 * @param array $attrs Button attributes array
	 * @return string
	 **/
	protected function getButtonData($attrs)
	{
		switch ($this->button_type) {
			case 'text':
				return $attrs['title'];
				break;

			case 'icon-text':
				return '<i class="'.$attrs['icon'].'"></i>'.$attrs['title'];

			case 'text-icon':
				return $attrs['title'].'<i class="'.$attrs['icon'].'"></i>';

			case 'icon':
			default:
				return '<i class="'.$attrs['icon'].'"></i>';
				break;
		}
	}

}
