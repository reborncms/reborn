<?php

namespace Navigation\Controller\Admin;

use Navigation\Model\Navigation;
use Navigation\Model\NavigationLinks as Links;
use Navigation\Lib\Helper;

class NavigationController extends \AdminController
{
	public function before()
	{
		$this->menu->activeParent('appearance');

		$allGroups = Navigation::all();
		$this->links = array();
		$this->groups = array();
		$items = array();
		$default = array();
		foreach ($allGroups as $g) {

			$id = $g->id;

			$obj = \Cache::solve('Navigation::navigation_group'.$id,
						function() use($id)
						{
							return Links::where('navigation_id', '=', $id)
									->orderBy('link_order', 'asc')
									->get()->toArray();
						});

			$this->links[$g->slug] = Helper::getNavTree($obj);
			$this->groups[$g->slug] = $g;
			$default[] = $g->id;
		}

		$this->groupSelect = e2s($allGroups, 'id', 'title');
		$this->moduleSelect = Helper::moduleSelect();
		$this->defaultGroup = $default[0];
		$this->pageSelects = Helper::pageSelect();

		$this->template->style('form.css')
						->style('navigation.css','navigation' )
						->script('plugins/jquery.mjs.nestedSortable.js')
						->script('navigation.js', 'navigation', 'footer');
	}

	public function index()
	{
		$this->template
				->title(\Translate::get('navigation::navigation.title'))
				->set('links', $this->links)
				->set('groups', $this->groups)
				->set('groupSelect', $this->groupSelect)
				->set('moduleSelect', $this->moduleSelect)
				->set('pageSelect', $this->pageSelects)
				->set('defaultGroup', $this->defaultGroup)
				->setPartial('admin/index');
	}

	/**
	 * Order the Menu Items
	 */
	public function order()
	{
		$order	= \Input::get('order');
		$group	= \Input::get('group');

		if (is_array($order)) {

			// Update the db result's parent are 0
			$g = Links::where('navigation_id', '=', $group)
					->update(array('parent_id' => 0));

			foreach ($order as $key => $link) {
				Links::where('id', '=', $link['id'])
							->update(array('link_order' => $key));
				if (isset($link['children'])) {
					Links::setTheChild($link);
				}
			}

			\Cache::deleteFolder('Navigation');
		}

		return $this->returnJson(array('status' => 'ok'));
	}

	/**
	 * Create the new Menu item
	 */
	public function create()
	{
		if(\Input::isPost()) {

			$v = $this->validation();

			if ($v->valid()) {

				$link = new Links();

				$link->title = \Input::get('title');
				$link->navigation_id = \Input::get('group');
				$link->link_type = \Input::get('type');
				$link->url = $this->getUrl();
				$link->parent_id = 0;
				$link->link_order = Links::getLinkOrder(\Input::get('group'));
				$link->class = \Input::get('class');
				$link->target = \Input::get('target');
				//$this->permission = \Input::get('permission');

				if ($link->save()) {
					\Cache::deleteFolder('Navigation');
					$msg = \Translate::get('navigation::navigation.message.create_success');
					\Flash::success($msg);

					return \Redirect::toAdmin('navigation');
				} else {
					$msg = \Translate::get('navigation::navigation.message.create_error');
					\Flash::error($msg);

					return \Redirect::toAdmin('navigation');
				}
			} else {
				$errors = $v->getErrors();
				\Flash::error(implode("\n\r", $errors));

				return \Redirect::toAdmin('navigation');
			}
		}
	}

	public function edit($id)
	{
		if (!user_has_access('nav.edit')) {
			return $this->template->set('make', 'editing')
								->set('type', 'link')
								->partialOnly()
								->setPartial('admin/noaccess')->render();
		}
		$link = Links::find($id);

		switch($link->type) {
			case 'module' :
				$link->module = $link->url;
			break;

			case 'url' :
				$link->urlStr = $link->url;
			break;

			case 'page' :
				$link->page = $link->url;
			break;

			default :
				$link->uri = $link->url;
			break;
		}

		if (\Input::isPost()) {
			$v = $this->validation();

			if ($v->valid()) {
				if (\Input::get('current_group') != \Input::get('group')) {
					$parent = 0;
					$group_change = true;
					$link_order = Links::getLinkOrder((int)\Input::get('group'));
				} else {
					$parent = (int)\Input::get('parent');
					$group_change = false;
					$link_order = (int)\Input::get('order');
				}

				$link->title = \Input::get('title');
				$link->navigation_id = \Input::get('group');
				$link->link_type = \Input::get('type');
				$link->url = $this->getUrl();
				$link->parent_id = $parent;
				$link->link_order = $link_order;
				$link->class = \Input::get('class');
				$link->target = \Input::get('target');

				unset($link->uri);
				unset($link->module);
				unset($link->page);
				unset($link->urlStr);

				if ($link->updateLink(\Input::get('current_group'), $group_change)) {

					\Cache::deleteFolder('Navigation');

					return $this->returnJson(array('status' => 'success', 'message' => t('navigation::navigation.message.edit_success') ));

				} else {
					return $this->returnJson(array('status' => 'fail', 'message' => t('navigation::navigation.message.edit_error') ));
				}
			}
			else
			{
				return $this->returnJson(array('status' => 'error', 'message' => 'Validation Error' ));
			}
		}

		$type = array(
				'uri'		=> t('navigation::navigation.labels.uri'),
				'page'		=> t('navigation::navigation.labels.page'),
				'module'	=> t('navigation::navigation.labels.module'),
				'url'		=> t('navigation::navigation.labels.url'),
			);

		$this->template->title(t('navigation::navigation.link.title'))
						->set('link', $link)
						->set('groups', $this->groups)
						->set('groupSelect', $this->groupSelect)
						->set('moduleSelect', $this->moduleSelect)
						->set('pageSelect', $this->pageSelects)
						->set('defaultGroup', $this->defaultGroup)
						->set('type', $type)
						->partialOnly()
						->setPartial('admin/edit');
	}

	public function delete($id)
	{
		$link = Links::find($id);

		if (is_null($link)) {
			return $this->notFound();
		}

		$link->updateParent();

		try {
			$link->delete();
			\Cache::deleteFolder('Navigation');
			$msg = \Translate::get('navigation::navigation.message.delete_success');
			\Flash::success($msg);
		} catch (\Exception $e) {
			$msg = \Translate::get('navigation::navigation.message.delete_error');
			\Flash::error($msg);
		}

		return \Redirect::toAdmin('navigation');
	}

	public function group()
	{
		$groups = Navigation::all();

		$this->template->title(t('navigation::navigation.group.title'))
						->set('groups', $groups)
						->setPartial('admin/group');
	}

	public function groupCreate()
	{
		if (! \Input::isPost()) {
			return \Redirect::toAdmin('navigation/group');
		}

		if (is_null(\Input::get('group'))) {
			\Flash::error(t('navigation::navigation.message.required'));
			return \Redirect::toAdmin('navigation/group');
		}

		if (in_array(ucfirst(\Input::get('group')), $this->groupSelect)) {
			\Flash::error(t('navigation::navigation.message.exists'));
			return \Redirect::toAdmin('navigation/group');
		}

		$group = new Navigation();
		$group->title = \Input::get('group');
		$group->slug = slug(\Input::get('group'));
		if($group->save()) {
			\Flash::success(t('navigation::navigation.message.group_success'));
		} else {
			\Flash::error(t('navigation::navigation.message.group_error'));
		}

		return \Redirect::toAdmin('navigation/group');
	}

	/**
	 * Get the URL for menu base on menu type
	 */
	protected function getUrl()
	{
		$type = \Input::get('type');

		switch ($type) {
			case 'url' :
				$url = \Input::get('url');
			break;

			case 'module':
				$url = \Input::get('module');
			break;

			case 'page':
				$url = \Input::get('page');
			break;

			default :
				$url = \Input::get('uri');
			break;
		}

		return $url;
	}

	/**
	 * Validation check for module item
	 */
	protected function validation()
	{
		$rule = array(
			        'title' => 'required|maxLength:100',
			        'type' => 'required',
			        'url' => 'navUrl',
			        'uri' => 'navUrl',
			        'module' => 'navUrl',
			        'page' => 'navUrl',
			        'group' => 'required',
			        'class' => 'maxLength:255'
			    );

		$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

		$ins = $this;
		$v->addRule('navUrl',
					'Test is fail',
					function() use ($ins){
			        	$type = \Input::get('type');
				        switch ($type) {
							case 'module':
								$value = \Input::get('module');
								return array_key_exists($value, $ins->moduleSelect);
							break;

							case 'page':
								$value = \Input::get('page');
								return array_key_exists($value, $ins->pageSelects);
							break;

							default :
								return true;
							break;
						}
			    	}
			    );

		return $v;
	}
}
