<?php

namespace User\Controller\Admin;
use Reborn\Connector\Sentry\Sentry;
use User\Model\User as User;
use User\Model\Group as Group;
use User\Model\UserMeta as UserMeta;

class UserController extends \AdminController
{
	public function before() 
	{
		$this->menu->activeParent('user_management');
		$this->template->style('user.css', 'user');
		$this->template->header = t('user::user.title.usermod');

		if(!Sentry::check()) return \Redirect::to(adminUrl('login'));
	}

	public function index()
	{
		$options = array(
		    'total_items'       => User::all()->count(),
		    'url'               => ADMIN_URL.'/user/index',
		    'items_per_page'    => 10,
		    'uri_segment'		=> 4
		);

		$pagination = \Pagination::create($options);

		$users = User::skip(\Pagination::offset())
						->take(\Pagination::limit())
						->get();

		$currentUser = Sentry::getUser();

		foreach($users as $u) {
			$id = $u->id;
			$user = Sentry::getUserProvider()->findById($id);
			$usergroup = $user->getGroups();
			foreach ($usergroup as $g) {
				$u->group = $g->name;
			}
		}

		$this->template->title(t('user::user.title.usermod'))
					->breadcrumb(t('user::user.title.profile'))
					->set('users', $users)
					->set('pagination', $pagination)
					->set('currentUser', $currentUser)
					->setPartial('admin/index');
	}

	/**
	 * Users Logout
	 * @access public
	 * @return void
	 */
	public function logout()
	{
		Sentry::logout();
		\Flash::success(t('user::user.logout'));
		return \Redirect::to('/');
	}

	public function create()
	{
		if (!user_has_access('user.create')) return $this->notFound();

		if (\Input::isPost()) {
			
			$rule = array(
		        'email' => 'required|email',
		        'password' => 'required|minLength:6',
		        'first_name' =>'required|minLength:2|maxLength:40',
		        'last_name' => 'required|minLength:2|maxLength:40',
		    );

			$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
			$e = new \Reborn\Form\ValidationError();

			if ($v->fail()) {
				$e = $v->getErrors();
				$this->template->set('errors', $e);
			} else {
				$email = \Input::get('email');
				$first_name = \Input::get('first_name');
				$last_name = \Input::get('last_name');
				$password = \Input::get('password');
				$confpass = \Input::get('confpass');
				$groups = (int)\Input::get('group');
				$adminPanelAccess = (int)\Input::get('admin_access', 0);

		    	if( $password !== $confpass ) {
					\Flash::error(t('user::user.password.fail'));
				} else {

					try {
						$user = Sentry::getUserProvider()->create(array(
					        'email'    => $email,
					        'password' => $password,
					        'first_name' => $first_name,
					        'last_name' => $last_name,
					        'permissions' => array(),
					        'activated' => 1,
					        'permissions' => array(
					            'admin' => $adminPanelAccess,
					        )
					    ));

					    $usermeta = self::saveMeta('create', $user->id);
					    $usermeta->save();

					    $groups = Sentry::getGroupProvider()->findById($groups);
					    $user->addGroup($groups);

					    \Flash::success(t('user::user.create.success'));
					    return \Redirect::toAdmin('user');
					} catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
					    \Flash::error(sprintf(t('user::user.auth.userexist'), $email));
					}
				}
			}
		}

		$groups = Group::all();

		$this->template->title(t('user::user.title.create'))
			->breadcrumb(t('user::user.title.create'))
			->set('groups', $groups)
			->setPartial('admin/create');
	}

	public function edit($uri)
	{
		if (!user_has_access('user.edit')) return $this->notFound();

		$user = Sentry::getUserProvider()->findById($uri);
		$usergroup = $user->getGroups();
		foreach ($usergroup as $group) {
			$group = $group->id;
		}

		if (\Input::isPost()) {
			
			$rule = array(
		        'email' => 'required|email',
		        'first_name' =>'required|minLength:2|maxLength:40',
		        'last_name' => 'required|minLength:2|maxLength:40',
		    );

			$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
			$e = new \Reborn\Form\ValidationError();

			if ($v->fail()) {
				$e = $v->getErrors();
				$this->template->set('errors', $e);
			} else {
				$email = \Input::get('email');
				$first_name = \Input::get('first_name');
				$last_name = \Input::get('last_name');
				$groups = (int)\Input::get('group');
				$adminPanelAccess = (int)\Input::get('admin_access', 0);

				try {
					$user->email = $email;
			    	$user->first_name = $first_name;
			    	$user->last_name = $last_name;
			    	$user->permissions = array(
					    'admin' => $adminPanelAccess,
					);

					self::setPassword($user);

				    if ($user->save()) {
				    	$usermeta = self::saveMeta('edit', $user->id);
					    $usermeta->save();

					    if ((int)$group !== $groups) {
					    	$group = Sentry::getGroupProvider()->findById($group);
					    	$user->removeGroup($group);
					    	$groups = Sentry::getGroupProvider()->findById($groups);
					    	$user->addGroup($groups);
					    }

					    \Event::call('user_edited',array($user));
					    \Flash::success(t('user::user.edit.success'));
					    return \Redirect::toAdmin('user');

				    } else {
				    	\Flash::error(t('user::user.edit.fail'));
				    }
				} catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
				   \Flash::error(sprintf(t('user::user.auth.userexist'), $email));
				}
			}			
		}

		$usermeta = UserMeta::where('user_id', '=', $user->id)->get();
		foreach ($usermeta as $u) {
			$usermeta = $u;
		}

		$groups = Group::all();

		$adminAccess['dashboard'] = array_key_exists('admin', $user->getPermissions()) ?  true : false;
		$adminAccess['superUser'] = array_key_exists('superuser', $user->getPermissions()) ?  true : false;

		$this->template->title(t('user::user.title.edit'))
			->breadcrumb(t('user::user.title.edit'))
			->set('user', $user)
			->set('group', $group)
			->set('usermeta', $usermeta)
			->set('groups', $groups)
			->set('adminAccess', $adminAccess)
			->setPartial('admin/edit');
	}

	/**
	 * User Delete
	 *
	 * @return void
	 */
	public function delete($uri)
	{
		if (!user_has_access('user.delete')) return $this->notFound();

	    $user = Sentry::getUserProvider()->findById($uri);

	    \Event::call('user_deleted',array($user));

	    $user->delete();
	    $usermeta = UserMeta::find($uri);
	    $usermeta->delete();
	    \Flash::success(t('user::user.delete.success'));
		return \Redirect::toAdmin('user');
	}

	/**
	 * Save Form Values of Create and Edit Blog
	 *
	 * @return boolean
	 **/
	protected function saveMeta($method, $id) {
		if ($method == 'create') {
			$user = new UserMeta;
		} else {
			$user = UserMeta::find($id);
		}

		$user->user_id = $id;
		$user->username = \Input::get('username');
		$user->biography = \Input::get('biography');
		$user->country = \Input::get('country');
		$user->website = \Input::get('website');
		$user->facebook = \Input::get('facebook');
		$user->twitter = \Input::get('twitter');

		return $user;
	}

	/**
	 * Change Password if the user edit password
	 *
	 * @param $user
	 *
	 **/
	protected function setPassword($user)
	{
		$password = \Input::get('password');
		$confpass = \Input::get('confpass');

		if ($password) {
			$passwordRule = array(
				'password' => 'required|minLength:6',
			);
			$validatePassword = new \Reborn\Form\Validation(\Input::get('*'), $passwordRule);

			if ($validatePassword->fail()) {
				$errors = $validatePassword->getErrors();
				\Flash::error($errors);
				return \Redirect::toAdmin('user/edit/'.$user->id);
			} else {
				if ($password) {
					if($password == $confpass) {
						$user->password = $password;
					} else {
						\Flash::error(t('user::user.password.fail'));
						return \Redirect::toAdmin('user/edit/'.$user->id);
					}
				}
			}
		}
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
