<?php

namespace Blog\Model;

use Carbon\Carbon;

class Blog extends \Eloquent
{
    protected $table = 'blog';

    public $timestamps = false;

    protected $fillable = array('view_count');

    /**
     * Blog Post Tags
     * @var array|null
     */
    protected $tags_data;

    /**
     * Blog custom field
     *
     * @var string
     **/
    public $custom_field;


    /**
     * Relationship with Blog Category
     */
    public function category()
    {
        return $this->belongsTo('Blog\Model\BlogCategory');
    }

    /**
     * Relationship with Author
     */
    public function author()
    {
    	return $this->belongsTo('\User\Model\User');
    }

    /**
     * Scope for post is active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'live')->where('created_at', '<=', date('Y-m-d H:i:s'));
    }

    /**
     * Blog post Url
     */
    public function getUrlAttribute()
    {
        return rbUrl('blog/'.$this->attributes['slug']);
    }

    /**
     * Check Blog post has Feature Image or not.
     */
    public function getHasFeatureImageAttribute()
    {
        return ('' != $this->attributes['attachment']);
    }

    /**
     * Blog post Feature Image
     */
    public function getFeatureImageAttribute()
    {
        return rbUrl($this->attributes['attachment']);
    }

    /**
     * Blog post Date
     *
     * @param string $format Date format string
     * @return string
     */
    public function getPostDateAttribute($format)
    {
        $format = is_null($format) ? 'jS F Y h:i:s A' : $format;
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at']);
        return $date->format($format);
    }

    /**
     * Blog post's Category Url
     */
    public function getCategoryUrlAttribute()
    {
        return rbUrl('blog/category/'.$this->category->slug);
    }

    /**
     * Blog post's Category Name
     */
    public function getCategoryNameAttribute()
    {
        return $this->category->name;
    }

    /**
     * Blog post's Author Url
     */
    public function getAuthorUrlAttribute()
    {
        return rbUrl('blog/author/'.$this->author->id);
    }

    /**
     * Blog post's Author Name
     */
    public function getAuthorNameAttribute()
    {
        return $this->author->first_name.' '.$this->author->last_name;
    }

    /**
     * Check Blog post's has Tags?
     *
     * @return boolean
     */
    public function getHasTagsAttribute()
    {
        return count($this->getTags()) > 0;
    }

    /**
     * Blog post's Tags as string
     *
     * @param string $separator Tag string separator
     * @return string
     */
    public function getTagsAttribute($separator)
    {
        $separator = is_null($separator) ? ',' : $separator;
        return implode($separator.' ', $this->getTags());
    }

    /**
     * Blog post's Tags as Array
     *
     * @return array
     */
    public function getTagsArrayAttribute()
    {
        return $this->getTags('arr');
    }

    public function getContentAttribute()
    {
        return html_entity_decode($this->attributes['body'], ENT_QUOTES);
    }

    /**
     * Get Blog Post Tags
     *
     * @param string $type (arr|string)
     * @return array|string
     **/
    protected function getTags($type = 'string')
    {
        if ( \Module::isEnabled('Tag') ) {

            if(is_null($this->tags_data)) {
                $tags = \Tag\Lib\Helper::getTags($this->attributes['id'], 'blog', 'arr');

                foreach ($tags as &$tag) {
                    $tag = '<a href="'.rbUrl('blog/tag/'.$tag).'" class="blog-tag">'.$tag.'</a>';
                }

                $this->tags_data = $tags;
            }

        } else {
            $this->tags_data = array();
        }

        return $this->tags_data;
    }



    /**
     * Fill some of requirement in parent __call method
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     **/
    public function __call($method, $parameters)
    {
        $check_method = 'get'.studly_case($method).'Attribute';

        if(method_exists($this, $check_method)) {
            return call_user_func_array(array($this, $check_method), $parameters);
        }

        return parent::__call($method, $parameters);
    }

}
