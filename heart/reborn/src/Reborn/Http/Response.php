<?php

namespace Reborn\Http;

use Reborn\Cores\Facade;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * HTTP Response Class
 *
 * @package Reborn\Http
 * @author Myanmar Links Professional Web Development Team
 **/
class Response extends SymfonyResponse
{

	/**
	 * Make Normal Response
	 *
	 * @param mixed   $data    The response data
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 **/
	public static function make($content = '', $status = 200, $headers = array())
	{
		return new Response($content, $status, $headers);
	}

	/**
	 * Make JsonResponse for Json Data Return
	 *
	 * @param mixed   $data    The response data
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 **/
	public static function json($content = '', $status = 200, $headers = array())
	{
		$org_headers = array('Content-Type' => 'application/json');
        $headers = array_merge($org_headers, $headers);

		return new JsonResponse($content, $status, $headers);
	}

	/**
	 * Make StreamedResponse for Stream Data Return
	 *
	 * @param mixed   $callback A valid PHP callback
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
	 * @return \Symfony\Component\HttpFoundation\StreamedResponse
	 **/
	public static function stream($callback = null, $status = 200, $headers = array())
	{
		return new StreamedResponse($callback, $status, $headers);
	}

	/**
	 * Make BinaryFileResponse for File Download.
	 *
	 * @param string $file The file to stream
	 * @param string $name Name of the file
	 * @param array $headers An array of response headers
	 * @param boolean $public Files are public by default
	 * @param boolean $prepare Response::prepare() by default
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 **/
	public static function binary($file, $name, $headers = array(), $public = true, $prepare = true)
	{
		$response = new BinaryFileResponse($file, 200, $headers, $public);

		$response->trustXSendfileTypeHeader();

		$response->setContentDisposition(
						ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name
					);

		if ($prepare) {
			$response->prepare(Facade::getApplication()->request);
		}

		return $response;
	}

	/**
	 * Response 404 Page Not Found (Clueless) with Template.
	 * ## Clueless is Slang usage for Page Not Found.
	 *
	 * @param string|null $message Message for 404 template
	 * @return \Reborn\Http\Response
	 **/
	public static function clueless($message = null)
	{
		return Response::make(Facade::getApplication()->template->render404($message), 404);
	}

	/**
	 * Response 503 for Site Is Maintain Mode with Template.
	 *
	 * @param string|null $message Message for maintain template
	 * @return \Reborn\Http\Response
	 **/
	public static function maintain($message = null)
	{
		return Response::make(Facade::getApplication()->template->renderMaintain($message), 503);
	}

} // END Request class
