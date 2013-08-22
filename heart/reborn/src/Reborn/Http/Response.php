<?php

namespace Reborn\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
		$instance = new Response($content, $status, $headers);

		return $instance;
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

		$instance = new JsonResponse($content, $status, $headers);

		return $instance;
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
		$instance = new StreamedResponse($callback, $status, $headers);

		return $instance;
	}

} // END Request class
