<?php

/**
 * Configuration File for Reborn Blog Module
 *
 * @package Reborn\Module\Blog
 * @since  1.2
 */

return array(

    /**
     * Default Post Type for Blog Post
     * You can make multiple post design base on post type.
     * This post type is added in version 1.2.
     * Default post type are -
     *  - Standard [Standard for normal post. This is default post type]
     *  - Image [Post type for highlight the image]
     *  - Quote [Post type for highlight the blockquote]
     *  - Video [Post type for highlight the Video]
     *  - Audio [Post type for highlight the Audio]
     *  - Gallery [Post type for gallery. example: Trip gallery]
     *  - Status [Post type for short status, like twitter feed]
     */
    'post_types' =>	array(
                        'standard'	=> 'Standard',
                        'image'		=> 'Image',
                        'quote'		=> 'Quote',
                        'video'		=> 'Video',
                        'audio'		=> 'Audio',
                        'gallery'	=> 'Gallery',
                        'status'	=> 'Status'
                    )
);
