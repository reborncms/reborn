<?php

namespace Pages;

class PagesPresenter extends \Presenter
{
    /**
     * Get Page Content
     */
    public function attributeContent()
    {
        $cont = html_entity_decode(htmlspecialchars_decode($this->resource->content), ENT_QUOTES);

        if (\Module::get('pages', 'db_version') >= 1.1) {
        	if ($this->resource->editor_type == 'markdown') {
        		$cont = markdown_extra($cont);		
        	}
        }

        return template_parse($cont);
    }
}
