<?php 

namespace Blog\Extensions;

use League\Fractal\TransformerAbstract;

use Blog\Model\BlogCategory;

class CategoryTransformer extends TransformerAbstract
{

	public function transform(BlogCategory $category)
    {

        return array(
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'parent_id' => $category->parent_id,
            'level'     => $category->level,
            'post_count'    => $category->post_count
        );

    }

}
