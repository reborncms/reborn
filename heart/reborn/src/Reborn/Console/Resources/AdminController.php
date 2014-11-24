<?php

namespace {module}\Controller\Admin;

use Flash;
use Input;
use Setting;
use Redirect;
use Pagination;
use Validation;
use {module}\Model\{module};
use {module}\Extensions\Form\{module}Form;
use {module}\Extensions\Table\{module}Table;

class {module}Controller extends \AdminController
{
    /**
     * Before Method.
     * This method will be call before call the request action.
     *
     * @return void
     */
    public function before()
    {
        // Set active parent menu
        //$this->menu->activeParent('menu_name');
    }

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        // Set Pagination options
        $options = array(
            'total_items'       => {module}::count(),
            'items_per_page'    => Setting::get('admin_item_per_page'),
        );

        $pagination = Pagination::create($options);

        // Get from Model
        $lists = {module}::orderBy('created_at', 'desc')
                            ->take(Pagination::limit())
                            ->skip(Pagination::offset())
                            ->get();

        $table = {module}Table::create($lists);

        $this->template->title('{module} Index')
                        ->set('table', $table)
                        ->set('pagination', $pagination)
                        ->view('admin/index');
    }

    /**
     * Show the form for creating a new resource
     * and create new resource when form submit.
     *
     * @return mixed
     */
    public function create()
    {
        $form = {module}Form::create();

        // Check form submit with POST method
        if ($form->valid()) {
            $model = new {module}();

            // Set input values to model at here
            // eg: $model->title = Input::get('title');

            if ($model->save()) {
                Flash::success('Successfully created');

                return Redirect::module();
            } else {
                Flash::error('Error ouucred to create new content!');
            }
        }

        $this->template->title('{module} Create')
                        ->view('admin/form', compact('form'));
    }

    /**
     * Show the form for updating the requested resource
     * and update resource when form submit.
     *
     * @param  integer $id
     * @return mixed
     */
    public function edit($id)
    {
        // Get data from model
        $model = {module}::find($id);

        // Return 404 for wrong ID
        if ( is_null($model) ) return $this->notFound();

        $form = {module}Form::create();

        $form->provider($model);

        // Check form submit with POST method
        if ($form->valid()) {

            // Set input values to model at here
            // eg: $model->title = Input::get('title');

            if ($model->save()) {
                Flash::success('Successfully edited');

                return Redirect::module();
            } else {
                Flash::error('Error ouucred to edit content!');
            }
        }

        $this->template->title('{module} Edit')
                        ->view('admin/form', compact('form'));
    }

    /**
     * Remove the requested resource from storage.
     *
     * @param  integer               $id
     * @return \Reborn\Http\Redirect
     */
    public function delete($id = 0)
    {
        $ids = ($id) ? array($id) : Input::get('action_to');

        // Delete records from table
        {module}::whereIn('id', $ids)->delete();

        Flash::success('Successfully deleted.');

        // Redirect to model admin index page
        return Redirect::module();
    }

    /**
     * After Method for Controller
     * This method return the Response Object.
     *
     * @param \Symfony\Component\HttpFoundation\Response
     */
    public function after($response)
    {
        return parent::after($response);
    }
}
