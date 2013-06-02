<?php

namespace Reborn\Util;

/**
 * Table Generate Helper class
 *
 * @package Reborn\Util
 * @author Myanmar Links Web Development Team
 **/

class Table
{
	/**
	 * tabel header (th) variable
	 *
	 * @var array
	 **/
	protected $header = array();

	/**
	 * Use table footer
	 *
	 * @var boolean
	 **/
	protected $useTFoot = false;

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
	protected $checkAll = false;

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
	 * table data object variable
	 *
	 * @var Object (May be Eloquent Collection)
	 **/
	protected $object;


	/**
	 * Static method for Table Instance
	 *
	 * @param array $options
	 * @return Reborn\Util\Table
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
	 *
	 * @param array $options
	 * @return Reborn\Util\Table
	 **/
	public function __construct($options = array())
	{
		if (isset($options['check_all'])) {
			$this->checkAll = (boolean) $options['check_all'];
		}

		if (isset($options['actions'])) {
			$this->setActions($options['actions']);
		}

		if (isset($options['class'])) {
			$this->setTableClass($options['class']);
		}

		if (isset($options['id'])) {
			$this->setTableId($options['id']);
		}

		if (isset($options['object'])) {
			$this->setObject($options['object']);
		}

		if (isset($options['use_tfoot'])) {
			$this->useTFoot = (boolean) $options['use_tfoot'];
		}
	}

	/**
	 * Set the Data Object.
	 *
	 * @return Reborn\Util\Table
	 **/
	public function setObject($obj)
	{
		if (is_object($obj)) {
			$this->object = $obj;
		}

		return $this;
	}

	/**
	 * Set the Table tag's class value
	 *
	 * @return Reborn\Util\Table
	 **/
	public function setTableClass($classes)
	{
		$this->table_class = $classes;
	}

	/**
	 * Set the Table tag's id value
	 *
	 * @return Reborn\Util\Table
	 **/
	public function setTableId($id)
	{
		$this->table_id = $id;
	}

	/**
	 * Set the table's action buttons for each row.
	 *
	 * <code>
	 * $actions = array(
	 * 					'edit' => array(
	 * 								'title' => 'Edit',
	 * 								'btn-color' => 'blue',
	 * 								'url' => rbUrl('blog/edit'), // adminUrl('blog/edit')
	 * 								'icon' => 'icon-edit'),
	 * 					'delete' => array(
	 * 								'title' => 'Delete',
	 * 								'btn-color' => 'red',
	 * 								'url' => rbUrl('blog/delete'), // adminUrl('blog/delete')
	 * 								'icon' => 'icon-delete')
	 * 				);
	 * $tabel->setActions($actions);
	 * </code>
	 *
	 * @return Reborn\Util\Table
	 **/
	public function setActions($actions = array())
	{
		foreach ($actions as $name => $act) {
			$this->prepareActionsLink($name, $act);
		}

		return $this;
	}

	/**
	 * Prepare the Actions Link (button) data array
	 *
	 * @param string $name
	 * @param array $options
	 * @return Reborn\Util\Table
	 **/
	protected function prepareActionsLink($name, $options)
	{
		$act = array();
		$act['title'] = isset($options['title']) ? $options['title'] : ucfirst($name);
		$act['btn_color'] = isset($options['btn-color']) ? 'button-'.$options['btn-color'] : '';
		$act['icon'] = isset($options['icon']) ? $options['icon'] : '';

		if (isset($options['url'])) {
			if (preg_match('/\[:(.*)\]/', $options['url'], $match)) {
				$act['url'] = str_replace($match[0], '[r]', $options['url']);
				$act['key'] = $match[1];
			} else {
				$act['url'] = $options['url'];
				$act['key'] = '';
			}
		}

		$act['target'] = isset($options['new_window']) ? (boolean)$options['new_window'] : false;

		$this->actions[$name] = $act;
		return $this;
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
	 * @return Reborn\Util\Table
	 **/
	public function headers( array $headers)
	{
		$this->header = $headers;

		return $this;
	}

	/**
	 * Add table columns data array
	 *
	 * @param array $cols
	 * @return Reborn\Util\Table
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
	 * Build the Table Output.
	 *
	 * @return string
	 **/
	public function build()
	{
		if(is_null($this->object)) {
			return null;
		}

		$result = $this->renderTableOpen();

		if (! empty($this->header) ) {
			$result .= $this->renderTableHeader();

			if ($this->useTFoot) {
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
		$total_head = count($this->header);
		$total_cols = count($this->cols);

		$head_row = "\t<thead>\n\t\t<tr class=\"table-head\">";

		if ($this->checkAll) {
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
		$total_head = count($this->header);
		$total_cols = count($this->cols);

		$foot_row = "\t<tfoot>\n\t\t<tr class=\"table-footer\">";

		if ($this->checkAll) {
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
		$total_head = count($this->header);
		$total_cols = count($this->cols);

		$ths = '';

		foreach ($this->header as $key => $value) {
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

		foreach ($this->object as $key => $obj) {
			$body .= $this->renderTableRow($obj, $key);
		}

		$body .= "\n\t</tbody>\n";

		return $body;
	}

	/**
	 * Render the table row (<tr>....</tr>)
	 *
	 * @param Object $obj Data object
	 * @param int $key Array key from Object Collection.
	 * 					Use for HTML Tag class attribute name only.
	 * @return string
	 **/
	protected function renderTableRow($obj, $key)
	{
		$no = $key + 1; // Because key is start from 0.
		$row = "\n\t\t<tr class=\"row-$no\">";

		if ($this->checkAll) {
			$row .= "\n\t\t\t".'<td class="checkbox">';
			$row .= "\n\t\t\t\t";
			$row .= \Form::checkbox('action_to[]', $obj->id, array('id' => 'action'.$no));
			$row .= "\n\t\t\t</td>";
		}

		$row .= $this->renderTableColumn($obj);

		if (! empty($this->actions) ) {
			$row .= $this->renderActionsColumn($obj);
		}

		$row .= "\n\t\t</tr>";

		return $row;
	}

	/**
	 * Render the data Column for table (<td>...</td>)
	 *
	 * @param Object $obj Data object
	 * @return string
	 **/
	protected function renderTableColumn($obj)
	{
		$cols = '';

		foreach ($this->cols as $k => $col) {

			$cols .= "\n\t\t\t<td>";
			$cols .= $obj->{$col['key']};
			$cols .= '</td>';
		}

		return $cols;
	}

	/**
	 * Render the Action Buttons Column for table
	 *
	 * @param Object $obj Data object
	 * @return string
	 **/
	protected function renderActionsColumn($obj)
	{
		$row = "\n\t\t\t<td class=\"td-actions\">";
		foreach ($this->actions as $action) {
			if (!is_null($obj->{$action['key']})) {
				$action['url'] = str_replace('[r]', $obj->{$action['key']}, $action['url']);
			}

			$row .= $this->getButton($action);
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
		$btn .= 'class="ico-button '.$attrs['btn_color'].' tipsy-tip">';
		$btn .= "\n\t\t\t\t\t";
		$btn .= '<i class="'.$attrs['icon'].' icon-white"></i>';
		$btn .= "\n\t\t\t\t</a>";

		return $btn;
	}

}
