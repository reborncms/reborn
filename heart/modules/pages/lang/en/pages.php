<?php
// Language file for page module

return array(
    'titles'                    => array(
        'main_title'            => 'Pages',
        'add_page'              => 'Add new Page',
        'add_page_info'         => 'Create new Page',
    ),
    'page_contents'             => 'Page Contents',
    'meta_data'                 => 'Meta Data',
    'labels'                    => array(
        'page_title'            => 'Page Title',
        'page_slug'             => 'Page Slug',
        'status'                => 'Status',
        'page_settings'         => 'Page Settings',
        'page_layout'           => 'Page Layout',
        'page_contents'         => 'Page Contents',
        'comment_status'        => 'Comment Status',
        'comment_enabled'       => 'Comments Enabled',
        'page_meta_data'        => 'Page Meta Data',
        'meta_title'            => 'Meta Title',
        'meta_keywords'         => 'Meta Keywords',
        'meta_description'      => 'Meta Description',
        'design'	            => 'Design',
        'save_as_draft'         => 'Save as Draft',
        'page_duplicate'        => 'Duplicate this page',
    ),
    'messages'                  => array(
        'success'               => array(
            'add'               => 'New Page Successfully added',
            'edit'              => "Successfully edited the page",
            'delete'            => "Successfully deleted the page",
            'status_update'     => "Successfully updated page status",
            'autosave_on'       => 'Your post is autosave at %s.',
        ),
        'error'                 => array(
            'add'               => 'Sorry, Page cannot be added. Something went wrong',
            'edit'              => 'Sorry, Page cannot be edited. Something went wrong',
            'delete'            => 'Sorry, Page cannot be delete. Something went wrong',
            'slug_duplicate'    => '*** Slug with this name already exist.Please choose another slug.',
            'status_update'     => 'Sorry, Page status cannot be updated. Something went wrong.',
            'csrf_error'        => 'This is probably a CSRF attack.',
            'not_found_head'    => '404 Not Found!!!',
            'not_found_body'    => 'What are you looking for ?',
            'delete_home_page'  => 'Sorry, You cannot delete default home page',
        ),
    ),
);

// end of pages.php
