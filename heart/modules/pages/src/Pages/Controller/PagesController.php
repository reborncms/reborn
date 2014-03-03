<?php

namespace Pages\Controller;

use Pages\Model\Pages;

use Pages\PagesPresenter;

class PagesController extends \PublicController
{
    public function before() {}

    public function index()
    {

        $uri = implode("/", \Uri::segments());

        if (strstr($uri, '/comments')) {
            $uri = strstr($uri, '/comments', true);
        }

        if (empty($uri)) {
            
            $uri = \Setting::get('home_page');
        }

        return $this->view($uri);
    }

    public function view($uri)
    {
        $uri = urldecode($uri);

        $query = Pages::where('uri' , '=', $uri)->first();

        if ($query == null or $query->status == 'draft') {

            return $this->notFound();

        } else {
            $css = ($query->css != '') ? '<style>'.$query->css.'</style>' : '';

            $js = ($query->js != '') ? '<script>'.$query->js.'</script>' : '';

            $title = ($query->meta_title != "") ? $query->meta_title : $query->title;

            //Set Layout for Page
            if ($this->theme->hasLayout($query->page_layout)) {

                $this->template->setLayout($query->page_layout);

            } else {

                $this->template->setLayout('default');

            }
            $this->template->title($title)
                               ->set('page', PagesPresenter::make($query))
                               ->set('css', $css)
                               ->set('js', $js)
                               ->metadata('keywords', $query->meta_keyword)
                               ->metadata('description', $query->meta_description)
                               ->metadata('og:title', $title, 'og')
                               ->metadata('og:description', $query->meta_description, 'og')
                               ->metadata('og:url', rbUrl($query->uri), 'og')
                               ->setPartial('index');

            $segments = explode("/", $uri);

            foreach ($segments as $key => $val) {
                $u = Pages::where('slug', '=', $val)->select('uri', 'title')->first();

                $this->template->breadcrumb('Home', rbUrl())
                            ->breadcrumb($u->title, rbUrl($u->uri));
            }
        }

    }

    public function preview()
    {
        $uri = $this->param('slug');

        if (is_null($uri)) {

           return $this->notFound();

        }

        $uri_string = urldecode($uri);

        if (\Auth::check()) {

            $query = Pages::where('uri' , '=', $uri_string)->first();
            
            if (($query == null) or !\Auth::getUser()->hasAccess('admin')) {

                return $this->notFound();

            } else {
                $css = ($query->css != '') ? '<style>'.$query->css.'</style>' : '';
                $js = ($query->js != '') ? '<script>'.$query->js.'</script>' : '';
                $title = ($query->meta_title != "") ? $query->meta_title : $query->title;
                $this->template->title($title)
                                ->setLayout($query->page_layout)
                                   ->set('page', PagesPresenter::make($query))
                                   ->set('css', $css)
                                   ->set('js', $js)
                                   ->metadata('keywords', $query->meta_keyword)
                                   ->metadata('description', $query->meta_description)
                                   ->setPartial('index');

                $segments = explode("/", $query->uri);

                foreach ($segments as $key => $val) {

                    $u = Pages::where('slug', '=', $val)->select('uri', 'title')->first();

                    $this->template->breadcrumb($u->title, rbUrl('pages/preview/'.$u->uri));

                }
            }

        } else {

            return $this->notFound();

        }
    }

    public function after($response)
    {
        return parent::after($response);
    }
}
