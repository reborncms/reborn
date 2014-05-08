<?php

namespace Blog\Controller;

use Input;
use Module;
use Response;
use League\Fractal;
use Blog\Extensions\BlogTransformer;
use Blog\Lib\DataProvider as Provider;

/**
 * Api Controller for RebornCMS Blog Module
 *
 * @package Reborn Blog Module
 * @author Li Jia Li 
 **/
class ApiController extends \Api\Controller\ApiController
{

	/**
	 * Get All Blog posts, Posts Collection by Category, Author and Tag
	 *
	 * @return json
	 **/
	public function posts()
	{
		//To do : Blog Posts by Category, Author, tags

		$wheres = $this->checkInputData(array('category_id', 'id', 'author_id', 'tag'));

		$before_after = $this->checkInputData(array('after_id', 'before_id', 'since', 'until'));

		$limit = (Input::get('limit')) ?: 0;

		$offset = (Input::get('offset')) ?: 0;

		if (empty($wheres) and empty($before_after)) {

			$blog = Provider::allPublicPosts($limit, $offset);

		} else {

			if (!empty($wheres)) {
				$conditions['wheres'] = $wheres;
			}

			if (!empty($before_after)) {
				$conditions['before_after'] = $before_after;
			}

			$blog = Provider::getPostsBy($conditions, $limit, $offset);

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