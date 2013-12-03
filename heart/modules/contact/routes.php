<?php

Route::get('@admin/contact/{p:page}?', 'Contact\Admin\Contact::index');

Route::add('@admin/contact/detail/{int:id}','Contact\Admin\Contact::detail');

Route::add('@admin/contact/delete/{int:id}','Contact\Admin\Contact::delete');

Route::add('@admin/contact/send-mail/{int:id}?','Contact\Admin\SendMail::index');

Route::get('@admin/contact/email-template/{p:page}?', 'Contact\Admin\EmailTemplate::index');

Route::add('@admin/contact/email-template/delete/{int:id}','Contact\Admin\EmailTemplate::delete');

Route::add('@admin/contact/email-template/duplicate/{int:id}','Contact\Admin\EmailTemplate::duplicate');

Route::add('@admin/contact/email-template/view/{int:id}','Contact\Admin\EmailTemplate::view');

Route::add('@admin/contact/email-template/edit/{int:id}','Contact\Admin\EmailTemplate::edit');

/* add frontend route */

Route::add('contact','Contact\Contact::index' , 'fornted_contact');
