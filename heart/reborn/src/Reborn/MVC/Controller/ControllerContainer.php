<?php

namespace Reborn\MVC\Controller;

use Reborn\Cores\Registry;
use Reborn\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Container class for Controller
 *
 * @package Reborn\MVC\Controller
 * @author Myanmar Links Professional Web Development Team
 **/
class ControllerContainer
{
    /**
     * request object variable for controller
     *
     * @var Reborn\Cores\Request
     **/
    protected $request = null;

    /**
     * template object variable for controller
     *
     * @var Reborn\MVC\View\Template
     **/
    protected $template = null;

    /**
     * theme object variable for controller
     *
     * @var Reborn\MVC\View\Theme
     **/
    protected $theme = null;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $session = null;

    /**
     * Variavle for HTTP status code for response
     *
     * @var int
     */
    protected $HTTPstatus = 200;

    /**
     * Variable for ETag use or not. Default is false.
     *
     * @var boolean
     */
    protected $Etag = false;

    public function __construct()
    {
        $this->creator();
    }

    public function before() {}

    /**
     * After Method for Controller
     * This method return the Response Object.
     *
     * @param mixed $response
     */
    public function after($response)
    {
        // If request in inner call, return the partialRender Only
        if ($this->request->inner and !$response) {
            $this->request->inner = false;

            return $this->template->partialRender();
        } elseif ($this->request->inner and $response) {
            $this->request->inner = false;

            return $response;
        }

        // Check Response is JsonResponse
        if ($response instanceof JsonResponse) {
            return $response;
        }

        if (! $response instanceof Response) {
            if (is_null($response)) {
                $response = $this->template->render();
            }
            $response = new Response($response, $this->HTTPstatus);
        }

        if ($this->Etag) {
            $response->setEtag(md5($response->getContent()));
        }

        return $response;
    }

    /**
     * Return the Json from Controller
     *
     * @param mixed   $data    The response data
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
     * @return Symfony\Component\HttpFoundation\JsonResponse
     **/
    protected function returnJson($data, $status = 200, $headers = array())
    {
        $org_headers = array('Content-Type' => 'application/json');
        $headers = array_merge($org_headers, $headers);
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Create the Controller with Application Object
     * This method is call from Application start only.
     *
     * @return void
     */
    final function creator()
    {
        $reg = Registry::get('app');
        $this->request = $reg['request'];
        $this->session = $reg['session'];
        $this->template = $reg['view']->getTemplate();
        $this->theme = $reg['view']->getTheme();
    }

    /**
     * Parse the template string.
     *
     * @param string $template
     * @param array $data
     * @return string
     **/
    protected function parse($template, $data = array())
    {
        $reg = Registry::get('app');
        $view = $reg['view']->getView();

        return $view->renderAsStr($template, $data);
    }

    /**
     * Set the HTTP status code to use at response.
     *
     * @param int $code HTTP status code.
     * @return void
     */
    protected function setHTTPstatus($code)
    {
        $realCode = Response::$statusTexts;

        if (isset($realCode[$code])) {
            $this->HTTPstatus = $code;
        }
    }

    /**
     * Call the Action Method
     *
     * @return void
     **/
    public function callByMethod($method, $params)
    {
        if (! method_exists($this, $method)) {
            return $this->notFound();
        }

        return call_user_func_array(array($this, $method), (array)$params);
    }

} // END class ControllerContainer
