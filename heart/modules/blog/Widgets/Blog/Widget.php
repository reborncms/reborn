<?php

namespace Blog;

class Widget extends \Reborn\Widget\AbstractWidget
{

    protected $properties = array(
            'name' 			=> 'Blog Module Widget',
            'sub' 			=> array(
                'posts' 	=> array(
                    'title' => 'Blog Post',
                    'description' => 'Latest Blog Posts which is posted last days',
                ),
                'archive' 	=> array(
                    'title' =>'Blog Archive',
                    'description' => 'Blog by Year and month',
                ),
                'category' 	=> array(
                    'title' => 'Blog Category',
                    'description' => 'Blog Category List',
                ),
                'tagCloud'	=> array(
                    'title' => 'Blog Tag Cloud',
                    'description' => 'Blog Tag Cloud',
                ),
            ),
            'author' => 'Reborn CMS Development Team'
        );

    public function save() {}

    public function update() {}

    public function delete() {}

    public function options()
    {
        return array(
            'posts' => array(
                'title' => array(
                    'label' 	=> 'Title',
                    'type'		=> 'text',
                    'info'		=> 'Leave it blank if you don\'t want to show your widget title',
                ),
                'limit' 	=> array(
                    'label' 	=> 'Number of Posts',
                    'type'		=> 'text',
                ),
            ),
            'archive' => array(
                'title' => array(
                    'label' 	=> 'Title',
                    'type'		=> 'text',
                    'info'		=> 'Leave it blank if you don\'t want to show your widget title',
                ),
                'show_type'		=> array(
                    'label'		=> 'Yearly or Monthly',
                    'type'		=> 'select',
                    'options'	=> array(
                        'monthly'	=> 'Monthly',
                        'yearly'	=> 'Yearly',
                    ),
                ),
                'limit' 	=> array(
                    'label' 	=> 'Number of Posts',
                    'type'		=> 'text',
                ),
            ),

            'category' 	=> array(
                'title' => array(
                    'label' 	=> 'Title',
                    'type'		=> 'text',
                    'info'		=> 'Leave it blank if you don\'t want to show your widget title',
                ),
            ),

            'tagCloud' => array(
                'title'	=> array(
                    'label'		=> 'Title',
                    'type'		=> 'text',
                ),
                'maxsize'		=> array(
                    'label'		=> 'Maximum Font Size',
                    'type'		=> 'text',
                ),
                'minsize'		=> array(
                    'label'		=> 'Minimum Font Size',
                    'type'		=> 'text',
                ),
                'unit'			=> array(
                    'label'		=> 'Font Unit',
                    'type'		=> 'select',
                    'options'	=> array(
                        'pt'	=> 'pt',
                        'px'	=> 'px',
                        '%'		=> '%',
                        'em'	=> 'em'
                    ),
                ),
                'class_prefix'	=> array(
                    'label'		=> 'Class Prefix',
                    'type'		=> 'text',
                ),
                'order'			=> array(
                    'label'		=> 'Order',
                    'type'		=> 'select',
                    'options'	=> array(
                        'random'=> 'Random',
                        'name'	=> 'By Name'
                    ),
                ),
                'order_dir'		=> array(
                    'label'		=> 'Order Direction',
                    'type'		=> 'select',
                    'options'	=> array(
                        'asc'	=> 'Asending',
                        'desc'	=> 'Descending'
                    ),
                ),
            ),
        );
    }

    /**
     * Query the Blog Posts
     *
     * @return string
     **/
    public function posts()
    {
        if (!\Module::isEnabled('Blog')) {
            return null;
        }

        $title = $this->get('title', 'Latest Blog Posts');

        $limit = $this->get('limit', 5);
        $offset = $this->get('offset', 0);
        $order = $this->get('order', 'created_at');
        $order_dir = $this->get('order_dir', 'desc');

        \Module::load('Blog');
        $posts = \Blog\Model\Blog::with('category', 'author')
                            ->active()
                            ->notOtherLang()
                            ->orderBy($order, $order_dir)
                            ->take($limit)
                            ->skip($offset)
                            ->get();

        return $this->show(array('posts' => $posts, 'title' => $title), 'post');
    }

    /**
     * Not Ready Yet! (#TODO)
     *
     * @return void
     **/
    public function popular() {}

    /**
     * Get the Blog Categories
     *
     * @return string
     **/
    public function category()
    {
        if (!\Module::isEnabled('Blog')) {
            return null;
        }

        \Module::load('Blog');

        $data = array();

        $data['categories'] = \Blog\Model\BlogCategory::all();

        $data['title'] = $this->get('title', 'Blog Catagories');

        return $this->show($data, 'category');
    }

    /**
     * Blog Post Archive Widget
     *
     * @return string
     **/
    public function archive()
    {
        if (!\Module::isEnabled('Blog')) {
            return null;
        }

        \Module::load('Blog');
        $title = $this->get('title', 'Archives');
        $limit = $this->get('limit', 5);
        $data = array();

        $data['title'] = $this->get('title', 'Blog Archives');
        $data['list'] = array();

        $data['s_type'] = $this->get('show_type', 'monthly');

        if ($data['s_type'] == 'monthly') {
            $select_q = 'YEAR(created_at) as year, MONTH(created_at) as month, count(id) as post_count';
            $gp_q = 'YEAR(created_at), MONTH(created_at)';
        } else {
            $select_q = 'YEAR(created_at) as year, count(id) as post_count';
            $gp_q = 'YEAR(created_at)';
        };

        $years = \Blog\Model\Blog::orderBy('created_at','DESC')
                                    ->active()
                                    ->notOtherLang()
                                    ->select(\DB::raw($select_q))
                                    ->groupBy(\DB::raw($gp_q))
                                    ->get();
        $c = 0;
        foreach ($years as $yr) {
            $data['list'][$c]['yr'] = $yr['year'];
            if ($data['s_type'] == 'monthly') {
                $data['list'][$c]['month'] = $yr['month'];
            }
            $data['list'][$c]['post_count'] = $yr['post_count'];
            $c++;
        }

        return $this->show($data, 'archive');
    }

    /**
     * Get the Blog Tag Cloud
     *
     * @return string
     **/
    public function tagCloud()
    {
        if (!\Module::isEnabled('Tag') || !\Module::isEnabled('Blog')) {
            return null;
        }

        \Module::load('Tag');
        \Module::load('Blog');

        $arr = array(
                'maxFont' => $this->get('maxsize', 26),
                'minFont' => $this->get('minsize', 10),
                'fontUnit' => $this->get('unit', 'pt'),
                'wrap' => $this->get('wrap', ''),
                'format' => $this->get('format', 'font'),
                'classPrefix' => $this->get('class_prefix', 'tag'),
                'order' => $this->get('order', 'random'),
                'orderDir' => $this->get('order_dir', false),
                'title' => $this->get('tag_title', 'Total posts %s'),
                'url' => $this->get('url', 'blog/tag/'),
            );
        $tc = new \Reborn\Util\TagCloud($arr);

        $posts = \Blog\Model\Blog::active()
                            ->notOtherLang()
                            ->get(array('id'));
        if ($posts->isEmpty()) {
            return null;
        }

        foreach ($posts as $p) {
            $ids[] = $p->id;
        }

        $tags = \Tag\Model\TagsRelationship::where('object_name', 'blog')
                            ->whereIn('object_id', array_values($ids))->get();

        foreach ($tags as $t) {
            $tc->add($t->tag->name);
        }
        $data = array();

        $data['tag_body'] = $tc->generate();

        $data['title'] = $this->get('title', 'Tag Cloud');

        return $this->show($data, 'tagCloud');
    }

    public function render()
    {
        return $this->posts();
    }
}
