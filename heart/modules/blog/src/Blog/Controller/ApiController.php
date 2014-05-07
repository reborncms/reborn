<?php

namespace Blog\Controller;

use Blog\Lib\DataProvider as Provider;
use League\Fractal;
use Blog\Extensions\BlogTransformer;
use Module, Response;

/**
 * Api Controller for RebornCMS Blog Module
 *
 * @package Reborn Blog Module
 * @author Li Jia Li 
 **/
class ApiController extends \Api\Controller\ApiController
{

	private $conditions = array();

	public function before()
	{

	}

	/**
	 * Get All Blog posts, Posts Collection by Category, Author and Tag
	 *
	 * @return json
	 **/
	public function posts()
	{
		//To do : Blog Posts by Category, Author, tags

		$conditions = $this->checkInputData(array('category_id', 'id', 'author_id', 'tag'));

		if (empty($conditions)) {

			$blog = Provider::allPublicPosts(\Input::get('limit'), \Input::get('offset'));

		} else {

			$blog = Provider::getPostsBy($conditions, \Input::get('limit'), \Input::get('offset'));

		}

		$data = $this->transform($blog);

		return Response::json(array(
			'total_items' => count($data),
			'type'		  => 'posts',
			'items'		  => $data
		));

	}

	/**
	 * Get single blog post
	 *
	 * @return json
	 **/
	public function post($id = null)
	{
		// Todo : Get post by slug

		if ($id == null) {
			return $this->notFound();
		}

		$blog = Provider::post($id);

		$data = array($this->transform($blog, 'item'));

		return Response::json(array(
			'total_items' => count($data),
			'type'		  => 'post',
			'items'		  => $data
		));

	}

	private function checkInputData($fields = array()) {

		$return_data = array();
		foreach ($fields as $field) {
			$data = \Input::get($field);
			if ($data != '') {
				$return_data[$field] = $data;
			}
		}

		return $return_data;

	}

	/**
	 * Transform data with Fractal 
	 *
	 * @return array
	 **/
	private function transform($blog, $type = 'collection')
	{
		$fractal = new Fractal\Manager();

		if ($type == 'collection') {

			$resource = new Fractal\Resource\Collection($blog, new BlogTransformer);

		} else {

			$resource = new Fractal\Resource\Item($blog, new BlogTransformer);

		}

		$data = $fractal->createData($resource)->toArray();

		return $data['data'];
	}

}