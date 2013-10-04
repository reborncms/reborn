<?php

namespace User\Controller\Admin;
use Reborn\Connector\Sentry\Sentry;
use User\Model\Group as Group;

class PermissionController extends \AdminController
{
	public function before()
	{
		$this->menu->activeParent(\Module::getData('user', 'uri'));
		$this->template->header = \Translate::get('user::permission.title');
		if(!Sentry::check()) return \Redirect::to('login');
	}

	/**
	* Get groups to edit permissions for each group
	*
	*/
	public function index()
	{
		if (!user_has_access('user.permission')) return $this->notFound();
		$group = Group::all();

		$this->template->title(\Translate::get('user::permission.title'))
				->breadcrumb(\Translate::get('user::permission.title'))
				->set('group', $group)
				->setPartial('admin/permission/index');
	}

	/**
	* Edit permissions for each usergroup
	*
	* @param int $groupid
	*/
	public function edit($groupid = null)
	{
		if (!user_has_access('user.permission.edit') or is_null($groupid)) return $this->notFound();
		$group = Sentry::getGroupProvider()->findById($groupid);

		if ( !$group ) {
			return \Redirect::to('admin/user/permission');
		}

		// Get permission from the installed modules
		$permission_modules = \User\Model\PermissionModel::getall();

		if (\Input::isPost()) {
			
			$modules = \Input::get('modules');
			$actions = \Input::get('modules_actions');

			/*dump($modules);
			dump($actions, true);*/

			if (!is_null($modules)) {
				$module_lists = array();
				foreach ($modules as $k => $v) {
					$modules[$k] = 1;
					foreach ($permission_modules as $m) {
						if(empty($modules[$m->uri])) {
							$modules[$m->uri] = 0;
						}
						$module_lists[$m->uri] = $m->uri;
					}
				}

				// Add Module Actions Permission
				if (!is_null($actions)) {
					foreach ($group->permissions as $k => $v){
						if ($k == 'admin') {
							continue;
						}
						if (!array_key_exists($k, $actions)
							and !array_key_exists($k, $module_lists)) {
							$modules[$k] = 0;
						}
					}

					foreach ($actions as $k => $v) {
						$modules[$k] = (int) $v;
					}
				}

				$group->permissions = $modules;

				if ($group->save()) {
			       \Flash::success(\Translate::get('user::permission.save'));
					return \Redirect::to('admin/user/permission/');
			    } else {
			    	\Flash::success(\Translate::get('user::permission.error'));
			    	return \Redirect::to('admin/user/permission/');
			    }
			}
		}

		$groupPermissions = $group->getPermissions();

		$this->template->title(\Translate::get('user::permission.title'))
				->breadcrumb(\Translate::get('user::permission.title'))
				->set('groupPermissions', $groupPermissions)
				->set('permission_modules', $permission_modules)
				->set('group', $group)
				->script('user.js', 'user', 'footer')
				->setPartial('admin/permission/edit');
	}
}
