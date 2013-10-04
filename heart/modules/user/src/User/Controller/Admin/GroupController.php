<?php

namespace User\Controller\Admin;
use Reborn\Connector\Sentry\Sentry;
use User\Model\Group as Group;

class GroupController extends \AdminController
{
	public function before() 
	{
		$this->menu->activeParent(\Module::getData('user', 'uri'));
		$this->template->style('user.css', 'user');
		$this->template->header = \Translate::get('user::group.title');
		if(!Sentry::check()) return \Redirect::to('login');
	}

	public function index()
	{
		if (!user_has_access('user.group')) return $this->notFound();
		$group = Group::all();

		$this->template->title(\Translate::get('user::group.title'))
					->breadcrumb(\Translate::get('user::group.title'))
					->set('group', $group)
					->setPartial('admin/group/index');
	}

	public function create()
	{
		if (!user_has_access('user.group.create')) return $this->notFound();
		if (\Input::isPost()) {
			
			$v = $this->validate();
			$e = new \Reborn\Form\ValidationError();

			if ($v->fail()) {
				$e = $v->getErrors();
				$this->template->set('errors', $e);
			} else {
				$groupName = \Input::get('name');
				$is_admin = (int)\Input::get('is_admin', 0);

				try {
					$group = Sentry::getGroupProvider()->create(array(
				        'name'        => $groupName,
				        'permissions' => array(
				            'admin' => $is_admin,
				        )
				    ));

					\Flash::success(t('user::group.create.success'));
				    return \Redirect::toAdmin('user/group');	
				} catch (\Cartalyst\Sentry\Groups\GroupExistsException $e) {
					\Flash::error(sprintf(t('group::group.auth.exist'), $groupName));
				}
			}
		}

		$this->template->title(\Translate::get('user::group.create.title'))
			->breadcrumb(\Translate::get('user::group.create.title'))
			->setPartial('admin/group/create');
	}

	public function edit($uri)
	{
		if (!user_has_access('user.group.edit')) return $this->notFound();
		$group = Sentry::getGroupProvider()->findById($uri);
		$groupPermission = $group->getPermissions();


		if (\Input::isPost()) {
			
			$v = $this->validate();
			$e = new \Reborn\Form\ValidationError();
			if ($v->fail()) {
				$e = $v->getErrors();
				$this->template->set('errors', $e);
			} else {
				$groupName = \Input::get('name');
				$is_admin = (int)\Input::get('is_admin', 0);

				try {
				    $group->name = $groupName;
				    $group->permissions = array(
				        'admin' => $is_admin
				    );

				    if ($group->save()) {
				        \Flash::success(\Translate::get('user::group.edit.success'));
					    return \Redirect::toAdmin('user/group');
				    } else {
				        \Flash::error(\Translate::get('user::group.edit.success'));
				    }

				    return \Redirect::toAdmin('user/group');	
				} catch (\Cartalyst\Sentry\Groups\GroupExistsException $e) {
					\Flash::error(sprintf(t('user::group.auth.exist'), $groupName));
				}
			}
		}

		$adminAccess = array_key_exists('admin', $groupPermission) ?  true : false;

		$this->template->title(sprintf(t('user::group.edit.title'), $group->name))
			->breadcrumb(sprintf(t('user::group.edit.title'), $group->name))
			->set('group', $group)
			->set('permission', $adminAccess)
			->setPartial('admin/group/edit');
	}

	public function delete($uri)
	{
		if (!user_has_access('user.group.delete')) return $this->notFound();
		$group = Sentry::getGroupProvider()->findById($uri);

	    $group->delete();
	    \Flash::success(\Translate::get('user::group.delete.success'));	    
	    return \Redirect::toAdmin('user/group');
	}
	

	protected function validate()
	{
		$rule = array(
	        'name' => 'required|minLength:2',
	    );

		$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
		return $v;
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
