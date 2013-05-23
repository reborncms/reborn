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
		    'total_items'       => Blog::where('status', 'live')->where('created_at', '<=', date('Y-m-d H:i:s'))->get()->count(),
		    'url'               => 'blog/index',
		    'items_per_page'    => \Setting::get('blog_per_page'),
		    'uri_segment'		=> 3
		);

		$pagination = \Pagination::create($options);

		$blogs = Blog::with(array('category','author'))
							->where('status', 'live')
							->where('created_at', '<=', date('Y-m-d H:i:s'))
							->orderBy('created_at', 'desc')
							->skip(\Pagination::offset())
							->take(\Pagination::limit())
							->get()
							->toArray();

		if (count($blogs) > 0) {
			$blogs = self::injectTags($blogs);
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
		$blog = Blog::with(array('category', 'author'))
						->where('slug', $slug)
						->where('status', 'live')
						->where('created_at', '<=', date('Y-m-d H:i:s'))
						->first();

		if ($blog == null) {

			return $this->notFound();
		} 

		$blog = $blog->toArray();

		\Module::load('Tag');
		$tags = \Tag\Lib\Helper::getTags($blog['id'], 'blog', 'arr');
		$blog['tags'] = $tags;

		$view_count = $blog['view_count'] + 1;

		$update_view_count = Blog::where('id', '=', $blog['id'])->update(array('view_count' => $view_count));

		$this->template->title($blog['title'])
						->setPartial('single')
						->set('blog', $blog)
						->breadcrumb('Blog', rbUrl('blog'))
						->breadcrumb($blog['category']['name'], rbUrl('blog/category/'.$blog['category']['slug']))
						->breadcrumb($blog['title']);
	}

	public function preview($slug = null)
	{
		if (!\Sentry::check() or $slug == null) {

			return $this->notFound();
		}

		$blog = Blog::with(array('category', 'author'))
						->where('slug', $slug)
						->first();

		if($blog == null or !\Sentry::getUser()->hasAccess('admin')) {

			return $this->notFound();
			
		} else {

			$blog = $blog->toArray();

			\Module::load('Tag');
			$tags = \Tag\Lib\Helper::getTags($blog['id'], 'blog', 'arr');
			$blog['tags'] = $tags;

			$this->template->title($blog['title'])
							->setPartial('single')
							->set('blog', $blog)
							->breadcrumb('Blog', rbUrl('blog'))
							->breadcrumb($blog['category']['name'], rbUrl('blog/category/'.$blog['category']['slug']))
							->breadcrumb($blog['title']);

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

		$cat_info = BlogCategory::where('slug', $slug)->first();

		$catIds = BlogCategory::getCatIds($slug);

		if ($catIds == false) {
			
			return $this->notFound();
		}

		$options = array(
		    'total_items'       => Blog::where('status', 'live')->where('created_at', '<=', date('Y-m-d H:i:s'))->whereIn('category_id', $catIds)->count(),
		    'url'               => 'blog/category/'.$slug,
		    'items_per_page'    => \Setting::get('blog_per_page'),
		    'uri_segment'		=> 4
		);

		$pagination = \Pagination::create($options);

		//To change back with relation
		
		$blogs = Blog::with(array('category', 'author'))
						->where('status', 'live')
						->where('created_at', '<=', date('Y-m-d H:i:s'))
						->whereIn('category_id', $catIds)
						->skip(\Pagination::offset())
						->take(\Pagination::limit())
						->get()
						->toArray();

		if (count($blogs) > 0) {

			$blogs = self::injectTags($blogs);

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
		$blog_count = \Tag\Lib\Helper::getObjectsCount($name, 'blog');
		
		$options = array(
		    'total_items'       => $blog_count,
		    'url'               => 'blog/tag/'.$name,
		    'items_per_page'    => \Setting::get('blog_per_page'),
		    'uri_segment'		=> 4
		);

		$pagination = \Pagination::create($options);

		$skip = \Pagination::offset();

		$limit = \Pagination::limit();

		$blog_ids = \Tag\Lib\Helper::getObjects($name, 'blog', $skip, $limit);

		// == To separate no tag and no blog post == /

		if ($blog_ids == false) {

			return $this->notFound();
		}
		
		foreach($blog_ids as $blog_id) {

			$blog = Blog::with(array('category', 'author'))
							->where('id', $blog_id)
							->where('status', 'live')
							->where('created_at', '<=', date('Y-m-d H:i:s'))
							->first();
							
			if ($blog != null) {

				$blogs[] = $blog->toArray();
			}
		}

		if (count($blogs) > 0) {
			$blogs = self::injectTags($blogs);
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
		    'total_items'       => Blog::where('author_id', $id)->where('status', 'live')->where('created_at', '<=', date('Y-m-d H:i:s'))->count(),
		    'url'               => 'blog/author/'.$id,
		    'items_per_page'    => \Setting::get('blog_per_page'),
		    'uri_segment'		=> 4
		);

		$pagination = \Pagination::create($options);

		$blogs = Blog::with(array('category','author'))
							->where('author_id', $id)
							->where('status', 'live')
							->where('created_at', '<=', date('Y-m-d H:i:s'))
							->skip(\Pagination::offset())
							->take(\Pagination::limit())
							->get()
							->toArray();

		if (count($blogs) > 0) {
			$blogs = self::injectTags($blogs);
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
	public function archives($year = null, $month = null)
	{
		if ($year == null) {
			return \Redirect::to(rbUrl('blog/archives/'.date("Y")));
		}

		if (stristr($month, 'page-') or $month == null) {

			$blog_count = Blog::where(\DB::raw('YEAR(created_at)'), $year)->where('status', 'live')->where('created_at', '<=', date('Y-m-d H:i:s'))->count();

			$options = array(
			    'total_items'       => $blog_count,
			    'url'               => 'blog/archives/'.$year,
			    'items_per_page'    => \Setting::get('blog_per_page'),
			    'uri_segment'		=> 4
			);

			$pagination = \Pagination::create($options);

			$blogs = Blog::with(array('category', 'author'))
								->where(\DB::raw('YEAR(created_at)'), $year)
								->where('status', 'live')
								->where('created_at', '<=', date('Y-m-d H:i:s'))
								->skip(\Pagination::offset())
								->take(\Pagination::limit())
								->get()
								->toArray();
		} else {

			$blog_count = Blog::where(\DB::raw('YEAR(created_at)'), $year)
								->where(\DB::raw('MONTH(created_at)'), $month)
								->where('status', 'live')
								->where('created_at', '<=', date('Y-m-d H:i:s'))
								->count();

			$options = array(
			    'total_items'       => $blog_count,
			    'url'               => 'blog/archives/'.$year.'/'.$month,
			    'items_per_page'    => \Setting::get('blog_per_page'),
			    'uri_segment'		=> 5
			);

			$pagination = \Pagination::create($options);

			$blogs = Blog::with(array('category', 'author'))
								->where(\DB::raw('YEAR(created_at)'), $year)
								->where(\DB::raw('MONTH(created_at)'), $month)
								->where('status', 'live')
								->skip(\Pagination::offset())
								->take(\Pagination::limit())
								->get()
								->toArray();
		}

		if (count($blogs) > 0) {
			$blogs = self::injectTags($blogs);
		}
		
		$this->template->title('Blog')
						->setPartial('archives')
						->set('blogs', $blogs)
						->set('pagination', $pagination)
						->breadcrumb('Blog', rbUrl('blog'))
						->breadcrumb($year, rbUrl('blog/archives/'.$year));
						
		if ($month != null) {
			$this->template->breadcrumb($month, rbUrl('blog/archives/'.$year.'/'.$month));
		}
	}

	/**
	 * Blog RSS
	 *
	 * @return void
	 **/
	public function rss()
	{
		$blogs = Blog::with(array('category','author'))
							->where('status', 'live')
							->where('created_at', '<=', date('Y-m-d h:i:s'))
							->orderBy('created_at', 'desc')
							->take(\Setting::get('blog_rss_items'))
							->get();

		$this->template->set('blogs', $blogs);

		$content = $this->template->partialRender('rss');

		$response = new Response();

		$response->setContent(htmlspecialchars_decode($content));

		$response->headers->set('Content-Type', 'application/rss+xml');

		$response->send();

		return $response;
	}

	/**
	 * Insert related tags into blog array
	 *
	 * @return void 
	 **/
	protected function injectTags($blogs) 
	{
		foreach ($blogs as $blog) {
			\Module::load('Tag');
			$tags = \Tag\Lib\Helper::getTags($blog['id'], 'blog', 'arr');
			$blog['tags'] = $tags;
			$edited[] = $blog;
		}

		return $edited;
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
