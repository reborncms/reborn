<?php

namespace Blog\Controller;

use Blog\Model\Blog;
use Blog\Model\BlogCategory;
use Reborn\Http\Response;
use Reborn\MVC\View\Theme as Theme;
use Auth,
    Pagination,
    Field,
    Setting;

class BlogController extends \PublicController
{
    /**
     * Blog Index
     *
     * @return void
     **/
    public function index($id = null)
    {
        $options = array(
            'total_items'       => Blog::active()->notOtherLang()->count(),
            'items_per_page'    => Setting::get('blog_per_page'),
            'url'				=> url('blog').'/'
        );

        $pagination = Pagination::create($options);

        $blogs = Blog::active()
                        ->notOtherLang()
                        ->with(array('category','author'))
                        ->orderBy('created_at', 'desc')
                        ->skip(\Pagination::offset())
                        ->take(\Pagination::limit())
                        ->get();

        if (!$blogs->isEmpty()) {
            $blogs = Field::getAll('blog', $blogs, 'custom_field');
        }

        $this->template->title('Blog')
                        ->setPartial('index')
                        ->set('blogs', $blogs)
                        ->set('list_type', 'index')
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

        $view_count = (int) $blog->view_count + 1;

        $update_view_count = Blog::where('id', '=', $blog->id)->update(array('view_count' => $view_count));

        $blog = Field::get('blog', $blog, 'custom_field');

        $this->template->title($blog['title'])
                        ->setPartial('single')
                        ->set('blog', $blog)
                        ->metadata('keywords', $blog->tags_val)
                        ->metadata('description', $blog['excerpt'])
                        ->metadata('og:title', $blog['title'], 'og')
                        ->metadata('og:type', 'blog.article', 'og')
                        ->metadata('og:url', rbUrl('blog'.$blog['slug']), 'og')
                        ->metadata('og:description', $blog['excerpt'], 'og')
                        ->breadcrumb('Blog', rbUrl('blog'))
                        ->breadcrumb($blog->category_name, $blog->category_url)
                        ->breadcrumb($blog->title);
        if ($blog['attachment']) {
            $this->template->metadata('og:image', rbUrl('media/image/'.$blog['attachment']), 'og');
        }
    }

    public function preview($slug = null)
    {
        if (!Auth::check() or $slug == null) {
            return $this->notFound();
        }

        $slug = urldecode($slug);

        $blog = Blog::with(array('category', 'author'))
                        ->where('slug', $slug)
                        ->first();

        $blog = Field::get('blog', $blog, 'custom_field');

        if ($blog == null or !Auth::getUser()->hasAccess('admin')) {
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
            'items_per_page'    => Setting::get('blog_per_page'),
        );

        $pagination = Pagination::create($options);

        //To change back with relation

        $blogs = Blog::active()
                        ->notOtherLang()
                        ->with(array('category', 'author'))
                        ->whereIn('category_id', $catIds)
                        ->orderBy('created_at', 'desc')
                        ->skip(Pagination::offset())
                        ->take(Pagination::limit())
                        ->get();

        if (!$blogs->isEmpty()) {
            $blogs = Field::getAll('blog', $blogs, 'custom_field');
        }

        if (self::checkPartial('category_'.$cat_info->slug)) {

            $this->template->setPartial('category_'.$cat_info->slug);

        } elseif (self::checkPartial('category')) {

            $this->template->setPartial('category');

        } else {

            $this->template->setPartial('index');

        }

        $this->template->title('Blog - Category:'.$cat_info->name)
                        ->set('blogs', $blogs)
                        ->set('pagination', $pagination)
                        ->set('list_type', 'category')
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

        $name = urldecode($name);

        $blog_ids = \Tag\Lib\Helper::getObjectIds($name, 'blog');

       // == To separate no tag and no blog post == /
       if ($blog_ids == false) {
           return $this->notFound();
       }

        $blog_count = Blog::active()->whereIn('id', $blog_ids)->count();

        $options = array(
            'total_items'       => $blog_count,
            'items_per_page'    => Setting::get('blog_per_page'),
        );

        $pagination = Pagination::create($options);

        $blogs = Blog::active()
                        ->notOtherLang()
                        ->with(array('category', 'author'))
                        ->whereIn('id', $blog_ids)
                        ->skip(\Pagination::offset())
                        ->take(\Pagination::limit())
                        ->orderBy('created_at', 'desc')
                        ->get();

        if (!$blogs->isEmpty()) {
            $blogs = Field::getAll('blog', $blogs, 'custom_field');
        }

        if (self::checkPartial('tag_'.$name)) {

            $this->template->setPartial('tag_'.$name);

        } elseif (self::checkPartial('tag')) {

            $this->template->setPartial('tag');

        } else {

            $this->template->setPartial('index');
        }

        $this->template->title('Blog')
                        ->set('blogs', $blogs)
                        ->set('list_type', 'tag')
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
        try {
            $author_info = Auth::getUserProvider()->findById($id);
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return $this->notFound();
        }

        $options = array(
            'total_items'       => Blog::active()->where('author_id', $id)->count(),
            'items_per_page'    => Setting::get('blog_per_page'),
        );

        $pagination = Pagination::create($options);

        $blogs = Blog::active()
                        ->notOtherLang()
                        ->with(array('category','author'))
                        ->where('author_id', $id)
                        ->skip(Pagination::offset())
                        ->take(Pagination::limit())
                        ->orderBy('created_at', 'desc')
                        ->get();

        if (!$blogs->isEmpty()) {
            $blogs = Field::getAll('blog', $blogs, 'custom_field');
        }

        if (self::checkPartial('author_'.$id)) {

            $this->template->setPartial('author_'.$id);

        } elseif (self::checkPartial('author')) {

            $this->template->setPartial('author');

        } else {

            $this->template->setPartial('index');

        }

        $this->template->title('Blog')
                        ->set('blogs', $blogs)
                        ->set('list_type', 'author')
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
                'items_per_page'    => Setting::get('blog_per_page'),
            );

            $pagination = Pagination::create($options);

            $blogs = Blog::active()
                                ->notOtherLang()
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

            $pagination = Pagination::create($options);

            $blogs = Blog::active()
                                ->notOtherLang()
                                ->with(array('category', 'author'))
                                ->where(\DB::raw('YEAR(created_at)'), $year)
                                ->where(\DB::raw('MONTH(created_at)'), $month)
                                ->skip(Pagination::offset())
                                ->take(Pagination::limit())
                                ->orderBy('created_at', 'desc')
                                ->get();
        }

        if (!$blogs->isEmpty()) {
            $blogs = Field::getAll('blog', $blogs, 'custom_field');
        }

        if (self::checkPartial('archives')) {
            $this->template->setPartial('archives');
        } else {
            $this->template->setPartial('index');
        }

        $this->template->title('Blog')
                        ->set('list_type', 'archives')
                        ->set('blogs', $blogs)
                        ->set('pagination', $pagination)
                        ->breadcrumb('Blog', rbUrl('blog'))
                        ->breadcrumb($year, rbUrl('blog/archives/'.$year));

        if ($month_name) {
            $this->template->breadcrumb($month_name);
        }
    }

    /**
     * Check from frontend partial
     *
     * @return boolean
     * @author
     **/
    protected function checkPartial($file)
    {
        return $this->theme->hasFile($file, 'blog');
    }

    /**
     * Blog RSS
     *
     * @return void
     **/
    public function rss()
    {
        $blogs = Blog::active()
                            ->notOtherLang()
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
