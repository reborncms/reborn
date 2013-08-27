## Documentation for Blog Module

This documentation is focus for Theme Developer. They need to get blog posts in their theme's some of pages. Reborn Blog module have Blog Facade Class for custom query. `Blog::posts()` allow to generate a list of post on any pages.

### How to use

```
{{ loop(Blog::posts() as $post) }}
	<h2>{{ $post->title }}</h2>
	<p>{{ $post->excerpt }}</p>
{{ endloop }}
```

### Available blog post attributes

| Attribute Key       | Description           | Example  |
| ------------- |:-------------| :----- |
| $post->title     | Blog post Title |  |
| $post->url      | Blog post Url      |  `http://example.com/blog/i-am-blog-post`  |
| $post->excerpt | Blog post Excerpt      |     |
| $post->body | Blog post Full Content      |     |
| $post->category_name | Blog category name      |     |
| $post->category_url | Blog post category url      |   `http://example.com/blog/category/category-slug`  |
| $post->author_name | Blog post author name      |   *"First_Name Last_Name"*  |
| $post->author_url | Blog post author url      |  `http://example.com/blog/author/author_id`   |
| $post->has_tag | Check blog post have tags or not      |  *return boolean*   |
| $post->tags | Get blog post's tags string      |  *hello, sample, something*   |
| $post->tags_array | Get blog post's tags with array format      |  `array('hello, 'sample, 'something')`   |
| $post->has_feature_image | Check blog post have feature image or not      |  *return boolean*   |
| $post->feature_image | Get blog post's feature image url      |  `http://example.com/media/thumb/200/100`   |
| $post->post_date | Check blog posted date      |  *12th August 2013 06:37:49 AM*   |

### How to make custom query in Blog::posts()

If you need to custom query posts for your theme, passing some of option parameters in Blog::posts().

Default query will return last active 5 posts.

#### How to make query for Category

```
Blog::posts(array('category' => 'news'));
```

If you need blog posts for multiple category lists, use comma letter. *(Note: use comma only, not allow to use comma + space)*

```
Blog::posts(array('category' => 'news,article,release'))
```

#### How to limit and offset for post query

```
// Make blog post limit is 3
Blog::posts(array('limit' => 3));

// Make blog post offset is 2
Blog::posts(array('ofset' => 2));
```

#### How to set order for blog posts

```
// Make blog post order with title
Blog::posts(array('order' => 'title'))
// Make blog post order with title and order direction with asc
Blog::posts(array('order' => 'title', 'order_dir' => 'asc'))
```

### Customize Blog post date format

Blog::posts() will return $post->post_date format with `'jS F Y h:i:s A'`

*eg: 12th August 2013 06:37:49 AM*

If you need to use custom date format, use follow.

```
// output:: 12/08/2013
$post->post_date('d/m/Y');
```

### Customize separator with tags string
Blog::posts() will return $post->tags format with comma separated value.
If you need to use custom separator, use follow.

```
// output:: tag1| tag2| tag3
$post->tags('|');
```

