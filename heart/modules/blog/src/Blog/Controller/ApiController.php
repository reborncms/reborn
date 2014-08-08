<?php

namespace Blog\Controller;

use Input;
use Module;
use Response;
use League\Fractal;
use Blog\Extensions\BlogTransformer;
use Blog\Extensions\CategoryTransformer;
use Blog\Extensions\UserTransformer;
use Blog\Lib\DataProvider as Provider;
use Blog\Lib\Helper as BlogHelper;

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
	public function posts($wheres = array())
	{
		//To do : Blog Posts by Category, Author, tags

		//$wheres = $this->checkInputData(array('category_id', 'id', 'author_id', 'tag'));

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

		$data = $this->transform($blog, new BlogTransformer);

		$total_items = (isset($conditions['wheres'])) ? Provider::countBy($conditions['wheres']) : Provider::countBy();

		$response_data['total_items'] = $total_items;

		if ($limit) {
			$response_data['limit'] = $limit;
		}

		if ($offset) {
			$response_data['offset'] = $offset;
		}

		$response_data['type'] = 'posts';

		if (!empty($conditions)) {

			$wheres = isset($conditions['wheres']) ? $conditions['wheres'] : array();

			$before_after = isset($conditions['before_after']) ? $conditions['before_after'] : array();

			$response_data['query'] = array_merge($wheres, $conditions['before_after']);
		}
		
		$response_data['items'] = $data;

		return Response::json($response_data);

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

		$data = array($this->transform($blog, new BlogTransformer, 'item'));

		return Response::json(array(
			'total_items' => count($data),
			'type'		  => 'post',
			'items'		  => $data
		));

	}

	/**
	 * Get Posts by Post Type
	 *
	 * @return void
	 * @author 
	 **/
	public function getByPostType($type)
	{
		return $this->posts(array('post_type' => $type));
	}

	/**
	 * Get Posts by Category
	 *
	 * @return void
	 * @author 
	 **/
	public function getByCategory($category_id)
	{
		return $this->posts(array('category_id' => $category_id));
	}

	/**
	 * Get Posts by Author
	 *
	 * @return void
	 * @author 
	 **/
	public function getByAuthor($author_id)
	{
		return $this->posts(array('author_id' => $author_id));
	}

	/**
	 * Get Posts by Tag
	 *
	 * @return void
	 * @author 
	 **/
	public function getByTags($tag)
	{
		//$tag = urldecode($tag);
		return $this->posts(array('tag' => $tag));
	}

	/**
	 * Get Archives
	 *
	 * @return void
	 * @author 
	 **/
	function getArchives($year, $month = null)
	{
		$limit = (Input::get('limit')) ?: 0;

		$offset = (Input::get('offset')) ?: 0;

		$blog = Provider::getArchives($year, $month, $limit, $offset);

		$data = $this->transform($blog, new BlogTransformer);

		$response_data['total_items'] = Provider::getArchives($year, $month, $limit, $offset, true);
		$response_data['type'] = 'posts';
		$response_data['query']['year'] = $year;
		if ($month) {
			$response_data['query']['month'] = $month;
		}
		if ($limit) {
			$response_data['limit'] = $limit;
		}
		if ($offset) {
			$response_data['offset'] = $offset;
		}
		$response_data['items'] = $data;

		return Response::json($response_data);
	}

	/**
	 * Category List
	 *
	 * @return json
	 **/
	public function getCategories()
	{
		$categories = Provider::getCategories();

		foreach ($categories as &$category) {

			if ($category->parent_id == 0) {

				$category->level = 0;

			} else {

				$category->level = BlogHelper::getCatLvl($categories->toArray(), $category->toArray());

			}
		}

		$data = $this->transform($categories, new CategoryTransformer);

		return Response::json(array(
			'total_items' 	=> count($data),
			'type'			=> 'categories',
			'items'			=> $data
		));

	}

	/**
	 * Get Author Lists
	 *
	 * @return void
	 * @author 
	 **/
	public function getAuthors()
	{
		$authors = Provider::getAuthors();

		$data = $this->transform($authors, new UserTransformer);

		return Response::json(array(
			'total_items' 	=> count($data),
			'type'			=> 'authors',
			'items'			=> $data
		));
	}

	/**
	 * Get Tag List
	 *
	 * @return void
	 * @author 
	 **/
	public function getTags()
	{
		$tags = Provider::getTags()->toArray();

		return Response::json(array(
			'total_items' 	=> count($tags),
			'type'			=> 'tags',
			'items'			=> $tags 
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
	private function transform($resources, $class, $type = 'collection')
	{
		$fractal = new Fractal\Manager();

		if ($type == 'collection') {

			$resource = new Fractal\Resource\Collection($resources, $class);

		} else {

			$resource = new Fractal\Resource\Item($resources, $class);

		}

		$data = $fractal->createData($resource)->toArray();

		return $data['data'];
	}

	/**
	 * After Function
	 *
	 **/
	public function after($response)
	{
		return $response;
	}

}