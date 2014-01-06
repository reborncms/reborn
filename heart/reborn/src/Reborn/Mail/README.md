# Reborn Mail with Swift Mailer

## Usage with PHP Mail

	$mail = Mailer::create();
	$mail->to('gaara.desert91@gmail.com', 'Thet Paing Oo');
	$mail->cc('tannaing@gmail.com', 'Yan Naing');
	$mail->from('lynnhtut87@gmail.com', 'Lynn Htut');
	$mail->subject('Email Testing Subject');
	$mail->body('Email testing message body');
	$mail->send();

## Usage with sendmail

	$mail = Mailer::create(array('type' => 'sendmail'));
	$mail->to('gaara.desert91@gmail.com', 'Thet Paing Oo');
	$mail->cc('tannaing@gmail.com', 'Yan Naing');
	$mail->from('lynnhtut87@gmail.com', 'Lynn Htut');
	$mail->subject('Email Testing Subject');
	$mail->body('Email testing message body');
	$mail->send();

## Usage with SMTP

	$smtp = array(
		'type' => 'smtp',
		'host' => 'smtp.mandrillapp.com',
		'port' => '587',
		'username' => 'lynnhtut87@gmail.com',
		'password' => '3wtX38YdRBlK4XPur6CYWQ'
	);
	$mail = Mailer::create($smtp);
	$mail->to('gaara.desert91@gmail.com', 'Thet Paing Oo');
	$mail->cc('tannaing@gmail.com', 'Yan Naing');
	$mail->from('lynnhtut87@gmail.com', 'Lynn Htut');
	$mail->subject('Email Testing Subject');
	$mail->body('Email testing message body');
	$mail->send();

## Mail testing at local

	If you want to test mail at local, you can use `Mailer::send(true)`

	This message will save at `/storages/tmp/Y-m-d-H-i-{Slug-of-subject}.mail`

	$mail = Mailer::create();
	$mail->to('gaara.desert91@gmail.com', 'Thet Paing Oo');
	$mail->from('lynnhtut87@gmail.com', 'Lynn Htut');
	$mail->subject('Email Testing Subject');
	$mail->body('Email testing message body');
	$mail->send(true);
