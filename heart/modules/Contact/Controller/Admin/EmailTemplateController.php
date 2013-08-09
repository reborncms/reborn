<?php 

namespace Contact\Controller\Admin;

use Contact\Model\EmailTemplate as Etemplate;
use Reborn\Util\Pagination as Pagination;

class EmailTemplateController extends \AdminController
{

	public function before()
	{
		$this->menu->activeParent('email');
		$this->template->style('contact.css', 'contact');
	}

	/**
	 * Email Template
	 *
	 * @package Contact\EmailTemplate
	 * @author RebornCMS Development Team
	 **/
	public function index($id = null)
	{
		$result = Etemplate::all();

		$options = array(
			'total_items'	=> Etemplate::get()->count(),
			'url'			=> ADMIN_URL.'/contact/email-template/index',
			'items_per_page'=> 7,
			'uri_segment'	=> 5,
			);
			
		$pagination = Pagination::create($options);

		$result = Etemplate::skip(Pagination::offset())
							->take(Pagination::limit())
							->orderBy('id','asc')
							->get();
		
		$this->template->title(\Translate::get('contact::contact.e_template'))
					->breadcrumb(\Translate::get('contact::contact.p_title'))
					->set('templates', $result)
					->set('pagination',$pagination)
					->setPartial('admin\emailtemplate\index');
	}

	/**
	 * Add new Email Template
	 *
	 * @package Contact\EmailTemplate
	 * @author RebornCMS Development Team
	 **/
	public function create()
	{
		if (!user_has_access('contact.template.add')) return $this->notFound();
		$template =new \stdClass;
		if (\Input::isPost()) {
			 
			$v = $this->validate();
			if ($v->valid()) {

				$check = $this->uni_slug(\Input::get('slug'),\Input::get('name'));
				
				if ($check == true) {
					$this->fillData('create');
					\Flash::success(\Translate::get('contact::contact.success_template'));

					return \Redirect::toAdmin('contact/email-template');

				} else
				{
					\Flash::error(\Translate::get('contact::contact.error_template'));
					$template = (object)\Input::get('*');
				}
				
			} else {
				$errors = $v->getErrors();
				$this->template->errors = $errors;
				$template = (object)\Input::get('*');
			}
			
		}

		$this->template->title(\Translate::get('contact::contact.add_email_temp'))
						->useWysiwyg()
						->set('method', 'create')
						->set('template',$template)
						->setPartial('admin/emailtemplate/form');
	}


	/**
	 * view email template
	 *
	 * @package Contact\EmailTemplate
	 * @author RebornCMS Development Team
	 **/
	public function view($id, $ax = null)
	{
		$template = Etemplate::find($id);

		if (count($template) == 0) {
			return $this->notFound();
		}

		if (!is_null($ax)) {
			$this->template->partialOnly();
		}

		$this->template->title(\Translate::get('contact::contact.e_template'))
						->breadcrumb($template->name)
						->set('template',$template)
						->setPartial('admin\emailtemplate\view');
	}


	/**
	 * Add new Email Template
	 *
	 * @package Contact\EmailTemplate
	 * @author RebornCMS Development Team
	 **/
	public function edit($id = null)
	{
		if (!user_has_access('contact.template.edit')) return $this->notFound();
		$template = Etemplate::find($id);
		if (\Input::isPost()) {
			 
			$v = $this->validate();
			if ($v->valid()) {
				$check = $this->uni_slug(\Input::get('slug'),\Input::get('name'),\Input::get('id'));
				
				if ($check == true) {
					$this->fillData('edit',\Input::get('id'));
					\Flash::success(\Translate::get('contact::contact.success_template'));
					return \Redirect::toAdmin('contact/email-template');

				} else
				{
					\Flash::error(\Translate::get('contact::contact.error_template'));
					$template = (object)\Input::get('*');
				}
				
			} else {
				$errors = $v->getErrors();
				$this->template->errors = $errors;
				$template = (object)\Input::get('*');
			}
			
		}

		$this->template->title(\Translate::get('contact::contact.edit_email_temp'))
						->useWysiwyg()
						->set('method', 'edit')
						->set('template',$template)
						->setPartial('admin/emailtemplate/form');
	}

	/**
     * Email Template Duplicate
     *
     * @return void
     **/
    public function duplicate($id)
    {
        $template = Etemplate::find($id);
        $this->template->title(\Translate::get('contact::contact.duplicate_template'))
                    ->set('template', $template)
                    ->set('method', 'create')
                    ->useWysiwyg()
                    ->setPartial('admin\emailtemplate\form');
    }

	/**
	 * Delete Email Template
	 *
	 * @package Contact\EmailTemplate
	 * @author RebornCMS Development Team
	 **/
	public function delete($id = 0)
	{
		if (!user_has_access('contact.template.delete')) return $this->notFound();
		$ids = ($id) ? array($id) : \Input::get('action_to');

		$templates = array();

		foreach ($ids as $id) {
			if ($template = Etemplate::find($id)) {
				$template->delete();
				$templates[] = "success";
			}
		}
		
		if (!empty($templates)) {
			if (count($templates) == 1) {
				\Flash::success(\Translate::get('contact::contact.template_delete'));
			} else {
				\Flash::success(\Translate::get('contact::contact.templates_delete'));
			}
		} else {
			\Flash::error(\Translate::get('contact::contact.template_error'));
		}
		\Event::call('template_delete',array(true));
		return \Redirect::toAdmin('contact/email-template');
		
	}

	/**
	 * Save Data
	 *
	 * @return void
	 * @author RebornCMS Development Team
	 **/
	public function fillData($method ,$id = null)
	{
		if ($method == 'create') {
			$get = new Etemplate;
		} else {
			$get = Etemplate::find($id);
		}
		$get->name = \Input::get('name');
		$get->slug = \Input::get('slug');
		$get->description = \Input::get('description');
		$get->body = \Input::get('body');
		$get->save();

		\Event::call('template_'.$method.'_success',array($get));
	}

	/**
	 * Checking valid slug
	 *
	 * @return boolean
	 * @author RebornCMS Development Team
	 **/
	public function uni_slug($slug ,$name ,$id = null)
	{
		$data = null;

		$data = Etemplate::where('slug', '=', $slug)->first();
		$data2 = Etemplate::where('name', '=' ,$name)->first();

		if ($data == null && $data2 == null) {
			return true;
		} elseif ($data2->id == $id) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Validation Email
	 *
	 * @package Contact\EmailTemplate
	 * @author RebornCMS Development Team
	 **/
	public function validate()
	{
		$rule = array(
			        'name'  => 'required',
			        'slug'=> 'required',
			        'description'=> 'required|maxLength:50',
			        'body'	=> 'required'
			    );

		$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

		return $v;
	}
	
}