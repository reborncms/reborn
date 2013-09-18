<?php

namespace Tag\Controller\Admin;

use Tag\Model\Tag;
use Tag\Model\TagsRelationship;

class TagController extends \AdminController
{
	public function before() 
	{
		$this->menu->activeParent('content');
		
		\Translate::load('tag::tag');
	}

	public function index($id = null) 
	{
		$options = array(
		    'total_items'       => Tag::all()->count(),
		    'url'               => ADMIN_URL.'/tag/index',
		    'items_per_page'    => \Setting::get('admin_item_per_page'),
		    'uri_segment'		=> 4,
		);

		$pagination = \Pagination::create($options);

		$tag = Tag::orderBy('name', 'asc')->skip(\Pagination::offset())->take(\Pagination::limit())->get();

		$form = $this->template->partialRender('admin/form');
		
		$this->template->title('Manage Tags')
					   ->set('tags', $tag)
					   ->set('pagination', $pagination)
					   ->set('form', $form)
					   ->setPartial('admin/index')
					   ->style('form.css')
					   ->script('form.js')
					   ->script('plugins/jquery.colorbox.js');
	}

	public function create() 
	{
		if (!user_has_access('tag.create')) {
             return $this->notFound();
        }
		if (\Input::isPost()) {

			$validation = self::validate();

			if ($validation->valid()) {

				$save_tag = self::saveValues('create');

				if ($save_tag == 'duplicate') {

					$this->flash('error', sprintf(\Translate::get('tag::tag.already_exit'), \Input::get('name')));

				} elseif ($save_tag == "success") {

					$this->flash('success', sprintf(\Translate::get('tag::tag.create_success'),\Input::get('name')));

				} else {

					$this->flash('error', sprintf(\Translate::get('tag::tag.create_error'),\Input::get('name')));

				}

			} else {

				$errors = $validation->getErrors();
				$this->flash('error', $errors['name']);
			}
			return \Redirect::to('admin/tag');

		}

		$this->template->setPartial('admin/form')
						->set('method', 'create');
	}

	public function edit($id = null) 
	{
		if (!user_has_access('tag.edit')) {
             return $this->notFound();
        }

		$ajax = $this->request->isAjax();

		if (\Input::isPost()) {

			$validation = self::validate();

			if ($validation->valid()) {

				$save_tag = self::saveValues('edit', \Input::get('id'));

				if ($save_tag == 'duplicate') {

					if ($ajax) {
						return json_encode(array('status' => 'fail', 'msg' => sprintf(\Translate::get('tag::tag.already_exit'), \Input::get('name'))));
					} else {
						$this->flash('error', sprintf(\Translate::get('tag::tag.already_exit'), \Input::get('name')));
					}

				} elseif ($save_tag == 'success') {

					if ($ajax) {
						return json_encode(array('status' => 'ok', 'msg' => sprintf(\Translate::get('tag::tag.edit_success'),\Input::get('name'))));
					} else {
						$this->flash('success', sprintf(\Translate::get('tag::tag.edit_success'),\Input::get('name')));
					}		

				} else {
					if ($ajax) {
						return json_encode(array('status' => 'fail', 'msg' => sprintf(\Translate::get('tag::tag.edit_error'), \Input::get('name'))));
					} else {
						$this->flash('error', sprintf(\Translate::get('tag::tag.edit_error'), \Input::get('name')));	
					}		
		
				}
				
			} else {

				$errors = $validation->getErrors();
				if ($ajax) {
					return json_encode(array('status' => 'fail', 'msg' => $errors['name']));
				} else {
					$this->flash('error', $errors['name']);
					$tag = (object)\Input::get('*');
				}
			}
			return \Redirect::to('admin/tag');

		} else {
			$tag = Tag::find($id);
		}

		if ($ajax) {
			$this->template->partialOnly();
		}
		$this->template->setPartial('admin/form')
					   ->set('method', 'edit')
					   ->set('tag', $tag);
	}

	protected function saveValues($method, $id = null)
	{

		if ($method == 'create') {
			$tag = new Tag;
		} else {
			$tag = Tag::find($id);
		}

		$slug = \Input::get('name');

		if (strlen($slug) == strlen(utf8_decode($slug))) {

			$slug = strtolower(preg_replace("/[^a-zA-Z 0-9-]/", "", \Input::get('name')));
			
		}
		
		$final_slug = str_replace(" ", "-", $slug);

		$check_duplicate = (int)Tag::where('name', '=', $final_slug)->count();

		if ($check_duplicate > 0) {

			return "duplicate";

		} else {
			$tag->name = $final_slug;
			$save_tag = $tag->save();

			\Cache::deleteFolder('Tag');

			if ($save_tag) {
				return "success";
			} else {
				return "fail";
			}

		}

	}

	public function delete($id = 0) 
	{
		if (!user_has_access('tag.delete')) {
             return $this->notFound();
        }

		$ids = ($id) ? array($id) : \Input::get('action_to');

		$tags = array();

		foreach ($ids as $id) {
			if ($tag = Tag::find($id)) {
				if ($tag->delete()) {
					$tags[] = $tag->name;
				}
			}
		}

		if (!empty($tags)) {

			\Cache::deleteFolder('Tag');
			
			if (count($tags) == 1) {
				$this->flash('success', sprintf(\Translate::get('tag::tag.delete_success'), $tags[0]));
			} else {
				$this->flash('success', sprintf(\Translate::get('tag::tag.delete_success_many'), implode(", ", $tags)));
			}
		} else {
			$this->flash('error', \Translate::get('tag::tag.delete_error'));
		}
		return \Redirect::to(adminUrl('tag'));
	}

	public function autocomplete()
	{
		$term = \Input::get('term');
		$tags = Tag::where('name', 'LIKE', "%$term%")->select('name as value')->get()->toArray();
		return json_encode($tags);
	}

	/**
	 * Import Tags
	 *
	 * @return void
	 **/
	public static function import($object_id, $object_name, $tags = '')
	{
		if (!empty($tags)) {
			$prev_tags = TagsRelationship::with('tag')
							->where('object_id', '=', $object_id)
							->where('object_name', '=', $object_name)
							->get();
			$i = 0;
			foreach ($prev_tags as $tag) {
				$pre_tag_name[] = $tag->tag->name;
				$i++;
			}

			$tags = explode(',', $tags);

			if (isset($pre_tag_name)) {
				$deleted = array_diff($pre_tag_name, $tags);

				if (!empty($deleted)) {
					foreach ($prev_tags as $tag) {
						if (in_array($tag->tag->name, $deleted)) {
							TagsRelationship::where('tag_id', '=', $tag->tag->id)
												->where('object_id', '=', $object_id)
												->where('object_name', '=', $object_name)
												->delete();
						}
					}
				}
			}

			foreach ($tags as $tag) {

				if (strlen($tag) == strlen(utf8_decode($tag))) {		
					$tag = strtolower(preg_replace("/[^a-zA-Z 0-9-]/", "", $tag));
				}

				$tag = str_replace(" ", "-", $tag);

				$result = Tag::where('name', '=', $tag)->first();

				if(count($result) > 0) {
					$tag_id = $result->id;
				} else {
					$tag_insert = Tag::create(array('name' => $tag));
					$tag_id = $tag_insert->id;
				}
				$has_tag = TagsRelationship::where('tag_id', '=', $tag_id)
												->where('object_id', '=', $object_id)
												->where('object_name', '=', $object_name)
												->count();
				
				if ($has_tag == 0) {
					TagsRelationship::create(array(
							'tag_id' => $tag_id,
							'object_id' => $object_id,
							'object_name' => $object_name
					));
				}
			}
		} else {
			return '';
		}

	}

	/**
	 * Form Validate
	 *
	 * @return bool
	 **/
	protected function validate()
	{
		$rule = array(
		    'name' => 'required|maxLength:50',
		);

		$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

		return $v;
	}


	public function after($response)
	{
		return parent::after($response);
	}
}
