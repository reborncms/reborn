<?php

namespace Comment\Facade;

use Comment\Model\Comments as Model;

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class Comment extends \Facade
{
    /**
     * Get Comments.
     *
     *
     * @param  int    $content_id
     * @param  string $module     Module name for Comments
     * @param  int    $status     Comment status
     * @return string
     **/
    protected function get($content_id, $module, $status, $cmt_uri_segment = 3)
    {
        $app = static::$app;

        if ($status === 1) {
            $status = 'open';
        } elseif ($status === 0) {
            $status = 'close';
        }

        $comment_form = '';

        $comments = array();

        $total_comments = Model::where('content_id', $content_id)
                                ->where('module', $module)
                                ->where('status', 'approved')
                                ->count();

        if ($total_comments > 0) {

            $total_parent_comments = Model::where('parent_id', 0)
                                ->where('content_id', $content_id)
                                ->where('module', $module)
                                ->where('status', 'approved')
                                ->count();

            $options = array(
                'total_items'       => $total_parent_comments,
                'items_per_page'    => \Setting::get('comment_per_page', 10),
                //'pagi_numbers'		=> false,
                'template'			=> array(
                    'start_container' => '<div class="pagination" id="comment-pagination">',
                ),
            );

            if (!\Uri::segment($cmt_uri_segment)) {
                $options['url'] = \Uri::current().'/comments/';
            }

            $pagination = \Pagination::create($options);

            if ($pagination->isInvalid()) {
                return \Response::clueless();
            }

            $comments = Model::with('author')
                                ->where('parent_id', 0)
                                ->where('content_id', $content_id)
                                ->where('module', $module)
                                ->where('status', 'approved')
                                ->skip(\Pagination::offset())
                                ->take(\Pagination::limit())
                                ->get();

            $app->template->set('pagination', $pagination);

        }

        $app->template->set('comments', $comments)
                        ->set('total_comments', $total_comments)
                        ->set('status', $status)
                        ->set('module', $module)
                        ->set('content_id', $content_id);

        if (\Setting::get('use_default_style') == 1) {
            $app->template->style('front-comment.css', 'comment');
        }

        if ($status == 'open') {

            $comment_form = $app->template->partialRender('comment::commentForm');

        } elseif ($status == 'close' and $total_comments > 0) {

            $comment_form = "Comment closed.";

        }

        $app->template->set('comment_form', $comment_form);

        return $app->template->partialRender('comment::index');
    }

    protected static function getInstance()
    {
        return new static();
    }

} // END class Comment extends \Facade
