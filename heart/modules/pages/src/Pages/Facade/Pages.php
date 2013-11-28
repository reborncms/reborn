<?php 

namespace Pages\Facade;

use Pages\Model\Pages as Model;

/**
 * Facade Class for Pages
 *
 * @package Page Module of reborncms
 * @author Li Jia Li
 **/
class Pages extends \Facade
{

	protected static function getInstance()
	{
		return new static();
	}

	protected function get($id)
	{
		$page = Model::find($id);

		return $page;
	}

	protected function titleList($limit = null, $order = 'desc')
	{
		if ($limit == null) {
			$pages = Model::orderBy('created_at', $order)->get(array('title', 'uri'));
		} else {
			$pages = Model::limit($limit)->orderBy('created_at', $order)->get(array('title', 'uri'));
		}

		return $this->makeList($pages);
	}

	protected function childrenList($id)
	{
		$pages = Model::where('parent_id', $id)->get();

		return $this->makeList($pages);
	}

	private function makeList($pages)
	{
		$list = '';

		if (!is_null($pages)) {
			$list .= '<ul class="page_list">';

			foreach ($pages as $page) {

				if (is_array($page)) {
					$page = arr_to_object($page);
				}

				$list .= '<li>';

				$list .= '<a href="'.rbUrl($page->uri).'">'.$page->title.'</a>';

				if (isset($page->children)) {

					$list .= $this->getChild($page->children);

				}

				$list .= '</li>';
			}

			$list .= '</ul>';
		}

		return $list;
	}

	private function getChild($children)
	{
		$child = '';

		$child .= '<ul>';

		foreach ($children as $page) {

			$child .= '<li>';

			$child .= '<a href="'.rbUrl($page->uri).'">'.$page->title.'</a>';

			if(isset($page->children)) {

				$child .= $this->getChild($page->children);

			}

			$child .= '</li>';
		}

		$child .= '</ul>';

		return $child;
	}

	protected function hierarchicalList()
	{
		$pages = Model::page_structure(true);

		return $this->makeList($pages);
	}

}