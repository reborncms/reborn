<?php

namespace Blog\Lib;

use Blog\Model\BlogCategory;

use Blog\Model\Blog;

use Reborn\Auth\Sentry\Eloquent\User;

use Field;

use Blog\Lib\Helper;

class DataProvider 
{

	/**
	 * Blog Model
	 *
	 **/
	protected $blog;

	/**
	 * Blog Categories
	 *
	 **/
	protected $blog_categories;


	public function __construct()
	{

		$this->blog = new Blog;
		$this->blog_categories = new BlogCategory;

	}

	/*|-- Multiple Data Retrive Session ------------------------------------------------------------
	  | 
	  | * getPostsByInstances
	  | * allPosts
	  | * allPublicPosts
	  | * getAllParentLangPosts
	  | * getPostsBy
	  | * getPostsWhereIn
	  | * getArchives
	  | * getTrashedPosts
	  | 
	  | Helper Functions
	  | ------------------
	  | + getPostsInstance (protected)
	  | + getCustomFields
	  |
	  |---------------------------------------------------------------------------------------
	 */

	/**
	 * Get Blog Public Posts Instance
	 *
	 * @return void
	 * @author 
	 **/
	protected function getPostsInstance($conditions = array('active', 'notOtherLang', 'embed_data', 'order'))
	{

		$blog = $this->blog;

		if (in_array('active', $conditions)) {

			$blog = $blog->active();

		}

		if (in_array('notOtherLang', $conditions)) {
			
			$blog = $blog->notOtherLang();

		}

		if (in_array('embed_data', $conditions)) {
			
			$blog = $blog->with(array('category','author'));

		}

		if (in_array('order', $conditions)) {
			
			$blog = $blog->orderBy('created_at', 'desc');

		}

		//dump($blog, true);

		return $blog;

	}

	/**
	 * Get Posts by Instances (active, noOtherLang, embed_data, order)
	 *
	 * active = only publish posts
	 * noOtherLang = only parent/default language posts
	 * embed_data = embedded data of category and author
	 * order = order by created_at
	 *
	 * @return void
	 **/
	public function getPostsByInstances($instances = array('active', 'notOtherLang', 'embed_data', 'order'), $limit = 10, $offset = 0)
	{

		$blog = $this->getPostsInstance($instances);

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $this->getCustomFields($blog->get());

	}

	/**
	 * Get All Posts
	 *
	 * @return void
	 * @author 
	 **/
	public function allPosts($limit = 10, $offset = 0)
	{

		return $this->getPostsByInstances(array('embed_data', 'order'), $limit, $offset);

	}

	/**
	 * Get All parent Lang Posts
	 *
	 * @return void
	 * @author 
	 **/
	public function getAllParentLangPosts($limit = 10, $offset = 0)
	{

		return $this->getPostsByInstances(array('notOtherLang', 'order'), $limit, $offset);

	}

	/**
	 * Get All active posts with default languages
	 *
	 * @return void
	 **/
	public function allPublicPosts($limit = 10, $offset = 0)
	{

		return $this->getPostsByInstances(array('active', 'notOtherLang', 'embed_data', 'order'), $limit, $offset);

	}

	public function getPostsBy($conditions = array(), $limit = 10, $offset = 0)
	{

		if (isset($conditions['instances'])) {
			
			$blog = $this->getPostsInstance($conditions['instances']);

		} else {

			$blog = $this->getPostsInstance();

		}

		if (isset($conditions['wheres'])) {

			foreach ($conditions['wheres'] as $key => $value) {

				if ($key == 'tag') {

					$blog_ids = \Tag\Lib\Helper::getObjectIds($value, 'blog');

					if(empty($blog_ids)) {
						return array();
					}

					$blog->whereIn('id', $blog_ids);

				} else {

					$blog->where($key, $value);

				}
			}
		}

		if (isset($conditions['before_after'])) {
			
			foreach ($conditions['before_after'] as $key => $value) {

				switch ($key) {
					case 'since':
						$blog->where(\DB::raw('UNIX_TIMESTAMP(created_at)'), '>', $value);
						break;

					case 'until':
						$blog->where(\DB::raw('UNIX_TIMESTAMP(created_at)'), '<', $value);
						break;
					
					case 'after_id':
						$blog->where('id', '>', $value);
						break;

					case 'before_id':
						$blog->where('id', '<', $value);
						break;
				}

			}

		}

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $this->getCustomFields($blog->get());

	}

	/**
	 * Get Archives post by year and month
	 *
	 * @return void
	 * @author 
	 **/
	public function getArchives($year, $month = null, $limit = 0, $offset = 0, $count = false)
	{
		$blog = $this->getPostsInstance(array('active', 'embed_data'));

		$blog = $blog->where(\DB::raw('YEAR(created_at)'), $year);

		if ($month) {

			$blog->where(\DB::raw('MONTH(created_at)'), $month);

		}


		if ($count) {

			return $blog->count();

		}

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $this->getCustomFields($blog->get());
	}

	/**
	 * Get Posts by Muti-values
	 *
	 * @return void
	 * @author 
	 **/
	public function getPostsWhereIn($key, $values, $limit = 10, $offset = 0)
	{

		$blog = $this->getPostsInstance();

		$blog->whereIn($key, $values);

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $this->getCustomFields($blog->get());
	}

	/**
	 * Get Trashed Posts
	 *
	 * @return void
	 * @author 
	 **/
	public function getTrashedPosts($limit = 10, $offset = 0)
	{
		$blog = $this->getPostsInstance(array('embed_data'));

		$blog->onlyTrashed()
            ->orderBy('deleted_at', 'desc');

        if ($limit > 0) {

        	$blog->take($limit);

        }

        if ($offset > 0) {

        	$blog->skip($offset);

        }

        return $blog->get();
	}

	/**
	 * Get custom Fields 
	 *
	 * @return void
	 * @author 
	 **/
	protected function getCustomFields($blog)
	{
		if ($blog instanceof \Illuminate\Database\Eloquent\Collection) {

			$blog_with_field = Field::getAll('blog', $blog, 'custom_field');

		} else {

			$blog_with_field = Field::get('blog', $blog, 'custom_field');

		}

		return $blog_with_field;
	}

	/*|- Counting Posts -----------------------------------------------------------
	  |  
	  | * countWhereIn
	  | * countBy
	  | * trashCount
	  |
	  |----------------------------------------------------------------------------
	 */

	/**
	 * Count posts by whereIn
	 *
	 * @return void
	 * @author 
	 **/
	public function countWhereIn($key, $values)
	{

		$blog = $this->getPostsInstance(array('active', 'notOtherLang'));

		$blog->whereIn($key, $values);

		return $blog->count();

	}

	/**
	 * Count posts by conditions
	 *
	 * @return int
	 **/
	public function countBy($conditions = array())
	{
		$blog = $this->getPostsInstance(array('active', 'notOtherLang'));

		foreach ($conditions as $key => $value) {

			if ($key == 'tag') {

				$blog_ids = \Tag\Lib\Helper::getObjectIds($value, 'blog');

				if(empty($blog_ids)) {

					return array();
				}

				$blog->whereIn('id', $blog_ids);

			} else {

				$blog->where($key, $value);

			}
		}

		return $blog->count();
	}

	/**
	 * Get count of Trashed Posts
	 *
	 * @return int
	 **/
	public function trashCount()
	{
	    return $this->blog->onlyTrashed()->count();
	}

	/*|-- Get Single Data Session -----------------------------------------------------
	  | 
	  | * post 
	  | * getPostBySlug
	  | 
	  |---------------------------------------------------------------------------------
	 */

	/**
	 * Get single blog post
	 * 
	 **/
	public function post($id)
	{

		$blog = $this->getPostsInstance(array('active', 'embed_data'));

		$blog = $blog->where('id', $id)
             			->first();

        return $this->getCustomFields($blog);

	}

	/**
	 * Get single blog by slug
	 *
	 * @return void
	 * @author 
	 **/
	public function getPostBySlug($slug, $active = true)
	{

		if ($active) {

			$condition = array('active', 'embed_data');

		} else {

			$condition = array('embed_data');

		}

		$blog = $this->getPostsInstance($condition);

        $blog = $blog->where('slug', $slug)
                	->first();

        return $this->getCustomFields($blog);

	}

	/*|- Data Actions -------------------------------------------------------------
	  | 
	  | * create
	  | * update
	  | * restore
	  |
	  |------------------------------------------------------------------------------
	 */

	/**
	 * Create new blog post
	 *
	 * @return void
	 * @author 
	 **/
	public function create($data = array())
	{

		$blog = $this->prepareData($data);

		if ($blog->save()) {
			
			//ToDo : TagSave function
			Helper::tagSave($blog->id, $data['blog_tag']);

			if (\Module::isEnabled('field')) {

			    \Field::save('blog', $blog);

			}

			return $blog;

		}

		return false;

	}


	/**
	 * Update Blog Post
	 *
	 * @return void
	 **/
	public function update($id, $data = array())
	{

		$blog = $this->prepareData($data, $id);

		if ($blog->save()) {

            Helper::tagSave($blog->id, $data['blog_tag']);

            if (\Module::isEnabled('field')) {

                \Field::update('blog', $blog);

            }

            return $blog;

        }

        return false;

	}

	/**
	 * Move to Trash (soft delete)
	 *
	 * @return void
	 * @author 
	 **/
	public function trash($id)
	{

		

	}

	/**
	 * Permenantly delete
	 *
	 * @return void
	 * @author 
	 **/
	public function delete()
	{

		

	}

	/**
	 * Restore trashed post
	 *
	 * @return void
	 * @author 
	 **/
	public function restore($id)
	{

		return $this->blog->withTrashed()->where('id', $id)->restore();

	}

	/**
	 * Prepare Data for create and update
	 *
	 * @return void
	 * @author 
	 **/
	protected function prepareData($data = array(), $id = null)
	{

		if ($id) {

			$blog = Blog::find($id);
			$method = 'edit';

		} else {

		    $blog = new Blog;
		    $method = 'create';

		}

		if ($data['author_id'] == 0) {

		    $author = \Auth::getUser()->id;

		} else {

		    $author = $data['author_id'];

		}

		if (isset($data['blog_save']) and $data['blog_save'] !== null) {

			$button_save = $data['blog_save'];

		    $status = ($button_save == t('global.save') || $button_save == t('global.publish')) ? 'live' : 'draft';
		    $blog->status = $status;

		}

		//if excerpt is empty get some part from body
		if ($data['excerpt'] == '') {

		    $body_text = $data['body'];

		    if ($data['editor_type'] == 'markdown') {

		        $body_text = markdown_extra($body_text);

		    } else {

		        $body_text = html_entity_decode($body_text);

		    }

		    $excerpt = \Str::words(strip_tags($body_text), \Setting::get('excerpt_length'));

		} else {

		    $excerpt = $data['excerpt'];

		}

		$slug = ($data['slug'] == '') ? 'untitled' : $data['slug'];

		$id = $data['id'];

		// ToDo : SlugDuplicateCheck in DataProvider
		$slug_check = Helper::slugDuplicateCheck($slug, $id);

		if ($slug_check) {

		    do {

		        $slug = \Str::increment($slug);
		        $check = Helper::slugDuplicateCheck($slug, $id);

		    } while ($check);

		}

		$blog->title = ($data['title'] == '') ? 'Untitled' : $data['title'];
		$blog->slug = $slug;
		$blog->category_id = $data['category_id'];
		$blog->excerpt = $excerpt;
		$blog->post_type = $data['post_type'];
		$blog->body = $data['body'];
		$blog->author_id = $author;

		if (\Module::get('blog', 'db_version') >= 1.21) {
		    $blog->editor_type = $data['editor_type'];
		}

		if (\Module::get('blog', 'db_version') >= 1.1) {

		    //Check if this lang is already exist

		    $lang = $data['lang'];

		    $lang_ref = $data['lang_ref'];

		    $blog->lang = $lang;

		    if ($lang_ref) {

		        $blog->lang_ref = $lang_ref;

		    }

		}

		$blog->comment_status = $data['comment_status'];

		if (isset($data['sch_type']) and $data['sch_type'] != null) {

		    if ($data['sch_type'] == 'manual') {

		        $blog->created_at = new \DateTime(Input::get('date'));

		    } else {

		        if ($method == 'create') {

		            $blog->created_at = new \DateTime();

		        }

		    }

		}

		if ($method == 'edit') {

		    $blog->updated_at = new \DateTime();

		}

		// Remove Base Url from Attachment
		$blog->attachment = remove_base_url($data['attachment']);
		//type
		return $blog;

	}

	/*|--- Check Data Session ---------------------------------------------------------
	  | 
	  | * isTrashed
	  |
	  |--------------------------------------------------------------------------------
	 */

	/**
	 * Check the post is trashed or not
	 *
	 * @return bool
	 **/
	public function isTrashed($id)
	{
	    return $this->blog->withTrashed()->find($id)->trashed();
	}

	/*|--- Get External Data Lists -----------------------------------------------------
	  |  
	  | * getCategories
	  | * getAuthors
	  | * getTags
	  |
	  |----------------------------------------------------------------------------------
	 */

	/**
	 * Get Categories
	 *
	 * @return void
	 * @author 
	 **/
	public function getCategories()
	{
		return BlogCategory::all();	
	}

	/**
	 * List of Authors
	 *
	 * @return void
	 * @author 
	 **/
	public function getAuthors()
	{
		$author_ids = array_values(array_unique(Blog::lists('author_id')));

		return User::whereIn('id', $author_ids)->get();

	}

	/**
	 * List of Tags
	 *
	 * @return void
	 **/
	public function getTags()
	{
		return \Tag\Lib\Helper::getObjectTags('blog');
	}

	
}