<?php

namespace Pages\Controller;

use Pages\Model\Pages;

class PagesController extends \PublicController
{
    public function before() {}

    public function index()
    {
        $uri = implode("/", \Uri::segments());

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
            $this->template->title($title)
                            ->setLayout($query->page_layout)
                               ->set('page', $query)
                               ->set('css', $css)
                               ->set('js', $js)
                               ->metadata('keywords', $query->meta_keyword)
                               ->metadata('description', $query->meta_description)
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
        $uri = \Uri::segments();

        $uri = array_slice($uri, 2);

        if (empty($uri)) {

           return $this->notFound();

        }

        $uri_string = implode("/", $uri);

        $uri_string = urldecode($uri_string);

        if (\Sentry::check()) {

            $query = Pages::where('uri' , '=', $uri_string)->first();
            
            if (($query == null) or !\Sentry::getUser()->hasAccess('admin')) {

                return $this->notFound();

            } else {
                $css = ($query->css != '') ? '<style>'.$query->css.'</style>' : '';
                $js = ($query->js != '') ? '<script>'.$query->js.'</script>' : '';
                $title = ($query->meta_title != "") ? $query->meta_title : $query->title;
                $this->template->title($title)
                                ->setLayout($query->page_layout)
                                   ->set('page', $query)
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
