<?php

// Admin Inbox 
Route::group('@admin/contact/', function() {

	Route::get('{p:page}?', 'Contact\Admin\Contact::index', 'inboxAdminView');
	Route::add('detail/{int:id}', 'Contact\Admin\Contact::detail', 'detailAdminView');
	Route::get('download/{int:id}', 'Contact\Admin\Contact::download', 'attachentAdminDownload');
    Route::add('delete/{int:id}?', 'Contact\Admin\Contact::delete', 'inboxAdminDelete');

});

// Admin Send Mail
Route::add('@admin/contact/send-mail/{int:id}?', 'Contact\Admin\SendMail::index', 'mailAdminSend');

// Admin Email Template
Route::group('@admin/contact/email-template/', function() {

	Route::get('{p:page}?', 'Contact\Admin\EmailTemplate::index', 'emailtemplateAdmin');
	Route::add('create', 'Contact\Admin\EmailTemplate::create', 'emailtemplateAdminCreate');
	Route::add('check-name', 'Contact\Admin\EmailTemplate::checkName', 'emailtemplateAdminCheckName');
	Route::add('view/{int:id}', 'Contact\Admin\EmailTemplate::view', 'emailtemplateAdminView');
	Route::add('edit/{int:id}', 'Contact\Admin\EmailTemplate::edit', 'emailtemplateAdminEdit');
	Route::add('duplicate/{int:id}', 'Contact\Admin\EmailTemplate::duplicate', 'emailtemplateAdminDuplicate');
	Route::add('delete/{int:id}?', 'Contact\Admin\EmailTemplate::delete', 'emailtemplateAdminDelete');

});

/* Contact FrontEnd */

Route::add('contact', 'Contact\Contact::index' , 'contactFrontEnd');
