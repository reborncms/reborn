<?php

namespace {module}\Controller;

use Pagination;
use {module}\Model\{module};

class {module}Controller extends \PublicController
{
    /**
     * Before Method.
     * This method will be call before call the request action.
     *
     * @return void
     */
    public function before() {}

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
            'items_per_page'    => 10,
        );

        $pagination = Pagination::create($options);

        // Get from Model
        $lists = {module}::orderBy('created_at', 'desc')
                            ->take(Pagination::limit())
                            ->skip(Pagination::offset())
                            ->get();

        $this->template->title('{module} Index')
                        ->set('lists', $lists)
                        ->set('pagination', $pagination)
                        ->view('index');
    }

    /**
     * Display the requested resource.
     *
     * @param  integer $id
     * @return void
     */
    public function view($id)
    {
        // Get data from model
        $data = {module}::find($id);

        if ( is_null($data) ) return $this->notFound();

        $this->template->title('{module} - '.$data->title)
                        ->set('data', $data)
                        ->view('view');
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
