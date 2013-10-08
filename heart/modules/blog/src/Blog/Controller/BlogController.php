<?php

namespace Blog\Controller;

use Blog\Model\Blog;
use Blog\Model\BlogCategory;
use Reborn\Http\Response;

class BlogController extends \PublicController
{
	public function before()
	{
		\Translate::load('blog::blog');
	}

	/**
	 * Blog Index
	 *
	 * @return void
	 **/
	public function index($id = null)
	{
		$options = array(
		    'total_items'       => Blog::active()->count(),
		    'items_per_page'    => \Setting::get('blog_per_page'),
		);

		$pagination = \Pagination::create($options);

		$blogs = Blog::active()
						->with(array('category','author'))
						->orderBy('created_at', 'desc')
						->skip(\Pagination::offset())
						->take(\Pagination::limit())
						->get();

		if (!$blogs->isEmpty()) {
			$blogs = \Field::getAll('blog', $blogs, 'custom_field');
		}

		$this->template->title('Blog')
						->setPartial('index')
						->set('blogs', $blogs)
						->breadcrumb('Blog')
						->set('pagination', $pagination);
	}

	/**
	 * Blog Single
	 *
	 * @return void
	 **/
	public function view($slug)
	{
		$slug = urldecode($slug);

		$blog = Blog::active()
						->with(array('category', 'author'))
						->where('slug', $slug)
						->first();

		if ($blog == null) {
			return $this->notFound();
		}

		$view_count = (int)$blog->view_count + 1;

		$update_view_count = Blog::where('id', '=', $blog->id)->update(array('view_count' => $view_count));

		$blog = \Field::get('blog', $blog, 'custom_field');

		$this->template->title($blog['title'])
						->setPartial('single')
						->set('blog', $blog)
						->breadcrumb('Blog', rbUrl('blog'))
						->breadcrumb($blog->category_name, $blog->category_url)
						->breadcrumb($blog->title);
	}

	public function preview($slug = null)
	{
		if (!\Sentry::check() or $slug == null) {

			return $this->notFound();
		}

		$slug = urldecode($slug);

		$blog = Blog::with(array('category', 'author'))
						->where('slug', $slug)
						->first();

		$blog = \Field::get('blog', $blog, 'custom_field');

		if($blog == null or !\Sentry::getUser()->hasAccess('admin')) {

			return $this->notFound();

		} else {

			$this->template->title($blog['title'])
							->setPartial('single')
							->set('blog', $blog)
							->breadcrumb('Blog', rbUrl('blog'))
							->breadcrumb($blog->category_name, $blog->category_slug)
							->breadcrumb($blog->title);

		}


	}

	/**
	 * By Category
	 *
	 * @return void
	 **/
	public function category($slug = null)
	{
		if ($slug == null) {

			return $this->notFound();
		}

		$slug = urldecode($slug);

		$cat_info = BlogCategory::where('slug', $slug)->first();

		$catIds = BlogCategory::getCatIds($slug);

		if ($catIds == false) {

			return $this->notFound();
		}

		$options = array(
		    'total_items'       => Blog::active()->whereIn('category_id', $catIds)->count(),
		    'items_per_page'    => \Setting::get('blog_per_page'),
		);

		$pagination = \Pagination::create($options);

		//To change back with relation

		$blogs = Blog::active()
						->with(array('category', 'author'))
						->whereIn('category_id', $catIds)
						->skip(\Pagination::offset())
						->take(\Pagination::limit())
						->get();

		if (!$blogs->isEmpty()) {
			$blogs = \Field::getAll('blog', $blogs, 'custom_field');
		}

		$this->template->title('Blog - Category:'.$cat_info->name)
						->setPartial('category')
						->set('blogs', $blogs)
						->set('pagination', $pagination)
						->breadcrumb('Blog', rbUrl('blog'))
						->breadcrumb('Category - ' . $cat_info->name);
	}

	/**
	 * By Tag
	 *
	 * @return void
	 **/
	public function tag($name)
	{
		//Check tag ..
		// Fix status live
		\Module::load('Tag');

		$name = urldecode($name);

		$blog_ids = \Tag\Lib\Helper::getObjectIds($name, 'blog');

		$blog_count = Blog::active()->whereIn('id', $blog_ids)->count();

		// == To separate no tag and no blog post == /
		if ($blog_ids == false) {

			return $this->notFound();
		}

		$options = array(
		    'total_items'       => $blog_count,
		    'items_per_page'    => \Setting::get('blog_per_page'),
		);

		$pagination = \Pagination::create($options);

		$blogs = Blog::active()
						->with(array('category', 'author'))
						->whereIn('id', $blog_ids)
						->skip(\Pagination::offset())
						->take(\Pagination::limit())
						->orderBy('created_at', 'desc')
						->get();

		if (!$blogs->isEmpty()) {
			$blogs = \Field::getAll('blog', $blogs, 'custom_field');
		}

		$this->template->title('Blog')
						->setPartial('tag')
						->set('blogs', $blogs)
						->breadcrumb('Blog', rbUrl('blog'))
						->breadcrumb('Tag - '. $name)
						->set('pagination', $pagination);
	}

	/**
	 * By Author
	 *
	 * @return void
	 **/
	public function author($id)
	{
		try
		{
		    $author_info = \Sentry::getUserProvider()->findById($id);
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    return $this->notFound();
		}

		$options = array(
		    'total_items'       => Blog::active()->where('author_id', $id)->count(),
		    'items_per_page'    => \Setting::get('blog_per_page'),
		);

		$pagination = \Pagination::create($options);

		$blogs = Blog::active()
						->with(array('category','author'))
						->where('author_id', $id)
						->skip(\Pagination::offset())
						->take(\Pagination::limit())
						->orderBy('created_at', 'desc')
						->get();

		if (!$blogs->isEmpty()) {
			$blogs = \Field::getAll('blog', $blogs, 'custom_field');
		}

		$this->template->title('Blog')
						->setPartial('author')
						->set('blogs', $blogs)
						->breadcrumb('Blog', rbUrl('blog'))
						->breadcrumb('Author - ' . $author_info->first_name . ' ' . $author_info->last_name)
						->set('pagination', $pagination);
	}

	/**
	 * Blog Archive by Year and month
	 *
	 * @return void
	 **/
	public function archives()
	{
		$year = $this->param('year');
		$month = $this->param('month');
		$page = $this->param('page');

		$month_name = '';

		if (!$month) {

			$blog_count = Blog::active()->where(\DB::raw('YEAR(created_at)'), $year)->count();

			$options = array(
			    'total_items'       => $blog_count,
			    'items_per_page'    => \Setting::get('blog_per_page'),
			);

			$pagination = \Pagination::create($options);

			$blogs = Blog::active()
								->with(array('category', 'author'))
								->where(\DB::raw('YEAR(created_at)'), $year)
								->skip(\Pagination::offset())
								->take(\Pagination::limit())
								->orderBy('created_at', 'desc')
								->get();
		} else {

			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$blog_count = Blog::active()
								->where(\DB::raw('YEAR(created_at)'), $year)
								->where(\DB::raw('MONTH(created_at)'), $month)
								->count();

			$options = array(
			    'total_items'       => $blog_count,
			    'items_per_page'    => \Setting::get('blog_per_page'),
			);

			$pagination = \Pagination::create($options);

			$blogs = Blog::active()
								->with(array('category', 'author'))
								->where(\DB::raw('YEAR(created_at)'), $year)
								->where(\DB::raw('MONTH(created_at)'), $month)
								->skip(\Pagination::offset())
								->take(\Pagination::limit())
								->orderBy('created_at', 'desc')
								->get();
		}

		if (!$blogs->isEmpty()) {
			$blogs = \Field::getAll('blog', $blogs, 'custom_field');
		}

		$this->template->title('Blog')
						->setPartial('archives')
						->set('blogs', $blogs)
						->set('pagination', $pagination)
						->breadcrumb('Blog', rbUrl('blog'))
						->breadcrumb($year, rbUrl('blog/archives/'.$year));

		if ($month_name) {
			$this->template->breadcrumb($month_name);
		}
	}

	/**
	 * Blog RSS
	 *
	 * @return void
	 **/
	public function rss()
	{
		$blogs = Blog::active()
							->with(array('category','author'))
							->orderBy('created_at', 'desc')
							->take(\Setting::get('blog_rss_items'))
							->get();

		if (!$blogs->isEmpty()) {
			$blogs = \Field::getAll('blog', $blogs, 'custom_field');
		}

		$this->template->set('blogs', $blogs);

		$content = $this->template->partialRender('rss');

		$response = new Response();

		$response->setContent(htmlspecialchars_decode($content));

		$response->headers->set('Content-Type', 'application/rss+xml');

		$response->send();

		return $response;
	}
}
