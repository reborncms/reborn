<?php

namespace Reborn\MVC\Controller;

use Reborn\MVC\Controller\PublicController as PublicController;
use Reborn\Http\Response as Response;

/**
 * 404 Controller for Rebors
 *
 * @package Reborn\MVC\Controller
 * @author Myanmar Links Professional Web Development Team
 **/
class PageNotFoundController extends PublicController
{

    public function index()
    {
        $this->setHTTPstatus(404);

        return $this->template->render404();
    }

    public function after($response)
    {
    	if (! $response instanceof Response) {
            $response = new Response($response, $this->HTTPstatus);
        }

        if ($this->Etag) {
            $response->setEtag(md5($response->getContent()));
        }

        return $response;
    }

} // END class 404Controller extends PublicController
