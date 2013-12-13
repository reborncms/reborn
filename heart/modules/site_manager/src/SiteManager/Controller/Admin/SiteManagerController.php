<?php

namespace SiteManager\Controller\Admin;

use Flash, Input, Redirect, Table;
use SiteManager\Model\SiteManager;
use SiteManager\Services\SiteMaker;

class SiteManagerController extends \AdminController
{
	/**
	 * Index action
	 *
	 * @return void
	 **/
	public function index()
	{
		$all = SiteManager::all();

		$tb_actions = array(
			'edit' 			=> array(
				'title' 	=> 'Edit',
				'url'	 	=> adminUrl('site/edit/[:id]'),
				'icon' 		=> 'icon-edit'
			),
			'delete' 		=> array(
				'title' 	=> 'Delete',
				'url' 		=> adminUrl('site/delete/[:id]'),
				'icon' 		=> 'icon-remove',
				'btn-class' => 'confirm_delete'
			)
		);

		$tb_opts = array(
			'check_all' => true,
			'actions' => $tb_actions,
			'class' => 'stripe',
			'id' => 'site_manager_table',
			'btn_type' => 'icons-bar'
		);

		$table = Table::create($tb_opts);

		$table->provider(SiteManager::all());

		$table->headers(array('Name', 'Domain', 'Actions'));

		$table->columns(array('name', 'domain'));

		$this->template->title('Site Manager')
						->setPartial('index', compact('table'));
	}

	/**
	 * Create action for new site record
	 *
	 * @return void
	 **/
	public function create()
	{
		$form = \SiteManager\Services\Form::create(adminUrl('site/create'));

		if ($form->valid()) {

			$maker = new SiteMaker($this->app, Input::get('domain'), Input::get('shared_by_force', array()));

			if ($maker->make()) {
				if ($form->save()) {
					Flash::success('Successfully created new site');

					return Redirect::module();
				} else {
					Flash::error('Error while create new site!');
				}
			}
		}

		$this->template->title('Site Manager')
						->setPartial('form', compact('form'));
	}

	/**
	 * Edit action for site data.
	 *
	 * @param integer $id
	 * @return void
	 **/
	public function edit($id)
	{
		$site = SiteManager::find($id);

		if(is_null($site)) return $this->notFound();

		$form = \SiteManager\Services\Form::create(adminUrl('site/store'));
		$form->setModel($site);

		if ($form->valid()) {
			if ($form->save()) {
				Flash::success('Successfully edited site');

				return Redirect::module();
			} else {
				Flash::error('Error while edit site!');
			}
		}

		$this->template->title('Site Manager Edit')
						->setPartial('form', compact('form'));
	}

	/**
	 * Delete action for site.
	 *
	 * @param integer $id
	 * @return void
	 **/
	public function delete($id)
	{
		$site = SiteManager::find($id);

		if(is_null($site)) return $this->notFound();

		$maker = new SiteMaker($this->app, $site->domain);

		$maker->delete();

		$site->delete();

		Flash::success('Successfully deleted site');

		return Redirect::module();
	}
}
