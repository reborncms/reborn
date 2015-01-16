<?php

namespace Contact\Lib;


use Contact\Lib\Helper;
use Contact\Model\EmailTemplate;

/**
 * Email Template Data Provider
 *
 * @author RebornCMS Developement Team
 */
class TemplateDataProvider 
{
	/**
	 * Create Template
	 *
	 * @param array $data [email template field]
	 * @return $Model
	 * @author RebornCMS Developement Team
	 **/
	public function create($data = array())
	{

		$template = $this->prepareData($data);

		if ($template->save()) {
			
			return $template;

		}

		return false;

	}

	/**
	 * Update Template
	 *
	 * @param string $id
	 * @param array $data 
	 * @return $model
	 * @author RebornCMS Developement Tema
	 **/
	public function update($id, $data = array())
	{

		$template = $this->prepareData($data, $id);

		if ($template->save()) {

            return $template;

        }

        return false;

	}

	/**
	 * PrepareData for Email Template
	 *
	 * @param  array  $data
	 * @param  string $id 
	 * @return array
	 */
	public function prepareData($data = array(), $id = null)
	{
		if ($id) {

			$template = EmailTemplate::find($id);

        } else {

            $template = new EmailTemplate;
        }

        $template->name = $data['name'];

        $slug = $data['slug'];

        $id = $data['id'];

        $slug_check = Helper::slugDuplicateCheck($slug, $id);

		if ($slug_check) {

		    do {

		        $slug = \Str::increment($slug);
		        $check = Helper::slugDuplicateCheck($slug, $id);

		    } while ($check);

		}
        
        $template->slug = $slug;
        $template->description = $data['description'];
        $template->body = $data['body'];
        
        return $template;
	}

	/**
	 * Delete Template
	 * @param  string $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$template = EmailTemplate::find($id);

		if ($template) {
			
			if ($template->detemp == 0 ) {
				
				if($template->delete()) {

					return true;
				}
			}
		}
		return false;
	}
}