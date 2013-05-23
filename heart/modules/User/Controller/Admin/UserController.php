<?php

namespace User\Controller\Admin;
use Reborn\Connector\Sentry\Sentry;
use User\Model\User as User;
use User\Model\Group as Group;
use User\Model\UserMeta as UserMeta;

class UserController extends \AdminController
{
	public function before() {
		$this->menu->activeParent('user_management');
		$this->template->header = \Translate::get('user::user.title.usermod');
		if(!Sentry::check()) return \Redirect::to(adminUrl('login'));
	}

	public function index()
	{		
		$users = User::all();
		$currentUser = Sentry::getUser();

		foreach($users as $u) {
			$id = $u->id;
			$user = Sentry::getUserProvider()->findById($id);
			$usergroup = $user->getGroups();
			foreach ($usergroup as $g) {
				$u->group = $g->name;
			}
		}

		$this->template->title(\Translate::get('user::user.title.usermod'))
					->breadcrumb(\Translate::get('user::user.title.profile'))
					->set('users', $users)
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
		\Flash::success(\Translate::get('user::user.logout'));
		return \Redirect::to('/');
	}

	public function create()
	{
		if (\Input::isPost()) {
			$v = $this->validate();
			if (\Security::CSRFvalid('user')) {
				if ($v->fail()) {
					$errors = $v->getErrors();
					$this->template->set('errors', $errors);
				} else {
					$email = \Input::get('email');
					$first_name = \Input::get('first_name');
					$last_name = \Input::get('last_name');
					$password = \Input::get('password');
					$confpass = \Input::get('confpass');
					$groups = (int)\Input::get('group');

			    	if( $password !== $confpass ) {
						\Flash::error(\Translate::get('user::user.password.fail'));
					} else {
						$user = Sentry::getUserProvider()->create(array(
					        'email'    => $email,
					        'password' => $password,
					        'first_name' => $first_name,
					        'last_name' => $last_name,
					        'permissions' => array(),
					        'activated' => 1,
					    ));

					    $usermeta = self::saveMeta('create', $user->id);
					    $usermeta->save();

					    $groups = Sentry::getGroupProvider()->findById($groups);
					    $user->addGroup($groups);

					    \Flash::success(\Translate::get('user::user.create.success'));
					    return \Redirect::to('admin/user');
					}
					\Flash::error(\Translate::get('user::user.create.fail'));
				}
			} else {
				\Flash::error(\Translate::get('user::user.csrf'));
			}
		}

		$groups = Group::all();

		$this->template->title(\Translate::get('user::user.title.create'))
			->breadcrumb(\Translate::get('user::user.title.create'))
			->set('groups', $groups)
			->setPartial('admin/create');
	}

	public function edit($uri)
	{
		$user = Sentry::getUserProvider()->findById($uri);
		$usergroup = $user->getGroups();
		foreach ($usergroup as $group) {
			$group = $group->id;
		}

		if (\Input::isPost()) {
			$v = $this->validate();

			if (\Security::CSRFvalid('user')) {
				if ($v->fail()) {
					$errors = $v->getErrors();
					$this->template->set('errors', $errors);
				} else {
					$email = \Input::get('email');
					$first_name = \Input::get('first_name');
					$last_name = \Input::get('last_name');
					$password = \Input::get('password');
					$confpass = \Input::get('confpass');
					$groups = (int)\Input::get('group');

			    	$user->email = $email;
			    	$user->first_name = $first_name;
			    	$user->last_name = $last_name;

			    	if( ($password != '') AND ($confpass != '') ) {				
						if($password == $confpass) {
							$user->password = $password;
						} else {
							\Flash::error(\Translate::get('user::user.password.fail'));
							return \Redirect::to('admin/user/edit');
						}
					}

				    if ($user->save()) {
				    	$usermeta = self::saveMeta('edit', $user->id);
					    $usermeta->save();

				    	$group = Sentry::getGroupProvider()->findById($group);
				    	$user->removeGroup($group);
					    $groups = Sentry::getGroupProvider()->findById($groups);
					    $user->addGroup($groups);

					    \Flash::success(\Translate::get('user::user.edit.success'));
				        return \Redirect::to('admin/user');
				    } else {
				    	\Flash::error(\Translate::get('user::user.edit.fail'));
				    }	
				}
			} else {
				\Flash::error(\Translate::get('user::user.csrf'));
			}
		}

		$usermeta = UserMeta::where('user_id', '=', $user->id)->get();
		foreach ($usermeta as $u) {
			$usermeta = $u;
		}

		$groups = Group::all();

		$this->template->title(\Translate::get('user::user.title.edit'))
			->breadcrumb(\Translate::get('user::user.title.edit'))
			->set('user', $user)
			->set('group', $group)
			->set('usermeta', $usermeta)
			->set('groups', $groups)
			->setPartial('admin/edit');
	}

	/**
	 * User Delete
	 *
	 * @return void
	 */
	public function delete($uri)
	{
	    $user = Sentry::getUserProvider()->findById($uri);
	    
	    $user->delete();
	    $usermeta = UserMeta::find($uri);
	    $usermeta->delete();
	    \Flash::success(\Translate::get('user::user.delete.success'));
		return \Redirect::to('admin/user');
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

	protected function validate()
	{
		$rule = array(
	        'email' => 'required|valid_email',
	        'password' => 'required|minLength:6',
	        'first_name' =>'required|minLength:2|maxLength:15',
	        'last_name' => 'required|minLength:2|maxLength:15',
	        'username' => 'required|minLength:4|maxLength:15',
	    );

		$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);
		
		return $v;
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
