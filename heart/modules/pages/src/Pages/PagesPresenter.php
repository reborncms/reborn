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
        return template_parse($cont);
    }
}