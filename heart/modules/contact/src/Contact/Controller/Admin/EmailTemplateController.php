<?php

namespace Contact\Controller\Admin;

use Contact\Model\EmailTemplate as Etemplate;
use Contact\Extensions\Form\EmailTemplateForm;
use Contact\Lib\TemplateDataProvider as Provider;

use Event, Flash, Input, Pagination, Redirect, Setting, Translate;

/**
 * Email Template Controller
 * @package Contact\EmailTemplate
 * @author RebornCMS Developement Team <reborncms@gmail.com>
 */
class EmailTemplateController extends \AdminController
{

    protected $provider;
    /**
     * Before Function
     */
    public function before()
    {
        $this->menu->activeParent(\Module::get('contact', 'uri'));
        $this->template->style('contact.css','contact');
        $this->template->script('contact.js','contact');
        $this->provider = new Provider;
    }

    /**
     * Email Template
     *
     * @package Contact\EmailTemplate
     * @author RebornCMS Development Team
     **/
    public function index()
    {
        $result = Etemplate::all();

        $options = array(
            'total_items'	=> Etemplate::count(),
            'items_per_page'=> Setting::get('admin_item_per_page'),
            );

        $pagination = Pagination::create($options);

        if (Pagination::isInvalid()) {
            return $this->notFound();
        }

        $result = Etemplate::skip(Pagination::offset())
                            ->take(Pagination::limit())
                            ->orderBy('id','asc')
                            ->get();

        $this->template->title(t('contact::contact.e_template'))
                    ->breadcrumb(t('contact::contact.p_title'))
                    ->set('templates', $result)
                    ->set('pagination',$pagination)
                    ->view('admin\emailtemplate\index');
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

        $form = EmailTemplateForm::create('','etemplate');

        if ($form->valid()) {

            $data = Input::get('*');

            $form->setToSave($this->provider, $data);

            if ( $saved = $form->save()) {

                Flash::success(t('contact::contact.success_template'));

                Event::call('template_create_success',array($saved));

                return Redirect::toAdmin('contact/email-template');

            }else {

                Flash::error(t('contact::contact.error_template'));
            }
        }

        $this->template->title(t('contact::contact.add_email_temp'))
                        ->view('admin/form', compact('form'));
    }

    /**
     * view email template
     *
     * @param string $id 
     * @package Contact\EmailTemplate
     * @author RebornCMS Development Team
     **/
    public function view($id)
    {
        $template = Etemplate::find($id);

        if (count($template) == 0) {
            return $this->notFound();
        }

        if ($this->request->isAjax()) {
            $this->template->partialOnly();
        }

        $this->template->title(Translate::get('contact::contact.e_template'))
                        ->set('template',$template)
                        ->view('admin\emailtemplate\view');
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

        $form = EmailTemplateForm::create('','etemplate');

        $data = Etemplate::find($id);

        $form->provider($data);

        if ($form->valid()) {
            
            $data = Input::get('*');
            
            $form->setToSave($this->provider, $data ,'edit');

            if ($edited = $form->save()) {

                Flash::success(t('contact::contact.success_template'));

                Event::call('template_edit_success',array($edited));

                return Redirect::toAdmin('contact/email-template');

            } else {

                Flash::error(t('contact::contact.error_template'));
            }

        }

        $this->template->title(Translate::get('contact::contact.edit_email_temp'))
                        ->view('admin/form', compact('form'));
    }

    /**
     * Email Template Duplicate
     *
     * @param string $id
     * @return void
     **/
    public function duplicate($id)
    {
        if (!user_has_access('contact.template.add')) return $this->notFound();
        
        $form = EmailTemplateForm::create('admin/contact/email-template/create','etemplate');

        $data = Etemplate::find($id);

        $data['id'] = '';

        $form->provider($data);

        $this->template->title(Translate::get('contact::contact.duplicate_template'))
                        ->view('admin/form', compact('form'));
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

        $ids = ($id) ? array($id) : Input::get('action_to');

        $templates = array();

        foreach ($ids as $id) {

            if ($this->provider->delete($id)) {

                $templates[] = "success";

            }
        }

        if (!empty($templates)) {

            if (count($templates) == 1) {

                Flash::success(Translate::get('contact::contact.template_delete'));

            } else {

                Flash::success(Translate::get('contact::contact.templates_delete'));

            }

        } else {

            Flash::error(Translate::get('contact::contact.template_error'));
        }

        Event::call('template_delete',array(true));

        return Redirect::toAdmin('contact/email-template');
    }

    /**
     * Check Name for Email Template
     * @param  string $name [name of template]
     * @return string
     */
    public function checkName()
    {
        $result = '';

        $name = Input::get('name');
        
        $id = (int) Input::get('id');

        if ($id != '') {

            $data = Etemplate::where('name', '=', $name)->where('id', '!=', $id)->get();

        } else {

            $data = Etemplate::where('name', '=', $name)->get();

        }

        if (count($data) > 0) {

            $result = t('contact::contact.name_duplicate');

        }
        
        return $result;
    }

}
