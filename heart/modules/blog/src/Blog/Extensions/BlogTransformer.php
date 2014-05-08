<?php 

namespace Blog\Extensions;

use League\Fractal\TransformerAbstract;

use Blog\Model\Blog;

class BlogTransformer extends TransformerAbstract
{

	public function transform(Blog $blog)
    {

        return array(
        	'id' 			=> $blog->id,
        	'title' 		=> $blog->title,
        	'slug' 			=> $blog->slug,
        	'url'			=> $blog->url,
        	'category_id'	=> $blog->category_id,
        	'category_name'	=> $blog->category_name,
            'category_url'  => $blog->category_url,
        	'body'			=> $blog->content,
        	'excerpt'		=> $blog->excerpt,
        	'author_id'		=> $blog->author_id,
        	'author_name'	=> $blog->author_name,
            'author_url'    => $blog->author_url,
        	'post_date'		=> $blog->post_date,
	        'featured_img'  => $blog->feature_image,
	        'tags'			=> $blog->tags_arr_with_links,
	        'comment_count'	=> $blog->comment_count
        );

    }

}
