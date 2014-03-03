<?php

namespace Reborn\Util;

/*use Reborn\Util\Uploader as Upload;*/
use Reborn\Fileupload\Uploader as Upload;

/**
 * Send Mail class for Reborn
 *
 * @package Reborn\Util
 * @author Myanmar Links Professional Web Development Team
 **/
class Mailer
{

    /**
     * Value for Mail Sending
     *
     * @var array
     **/
    protected static $config = array(
            'to'		=> array(),	// Email for receivers
            'from'		=> '',	// Email from sender
            'name'		=> '',	// Sender Name
            'subject'	=> '',	// Subject for sending Mail
            'body'		=> '',	// Body for sending Mail
            'part'		=> '',	// Alternative body for sending Mail
            'transport'	=> array(
                'type'		=>	'', // transoprt service for Mail ('smtp','sendmail','mail')
                'host'		=>	'', // host name for mail sever ( only for smtp mail sever )
                'port'		=>	0,	// port for mail sever ( only for smtp mail sever )
                'username'	=>	'', // username for sever auth ( only for smtp mail sever )
                'password'	=>	'',	// password for sever auth ( only for smtp mail sever )
                'mailpath'	=>	'',	// path of sendmail ( only for smtp mail sever )
                ),
            'attachment'	=>	array(
                'fieldName'	=> '',	// Input file Name
                'value'		=> '',	// attachment file value
                ),
            'attachmentConfig'=> array(
                'savePath'		=> '',	// Path for attachment file upload
                'createDir'	=> false,	// Upload directory create itself or not
                'allowedExt'=> array(),	// Upload allow file extension
                ),
        );

    /**
     * Error fro sending Mail
     *
     * @var array
     **/
    protected static $error = array(
            'notSupportType'	=> 'This Transport Type is not support',
        );

    /**
     * Success Email Sending
     *
     * @var array
     **/
    protected static $sending = array(
            'success'	=>	'Email is Successfully send',
            'fail'		=>	'Cannot send these address',
        );
    /**
     * value for attachment file name
     *
     * @var string
     **/
    private static $attName = null;

    /**
     * Emails for cann't send or doesn't exit
     *
     * @var array
     **/
    private static $sendError = array();

    /**
     * Get Attachment file name in sending mail
     *
     * @return string
     * @author RebornCMS Development Team
     **/
    public static function getAttName()
    {
        return static::$attName;
    }

    /**
     * Get email for cann't send or doesn't exit
     *
     * @return array
     * @author RebornCMS Development Team
     **/
    public static function getSendError()
    {
        return static::$sendError;
    }

    /**
     * This method is to set custom message code
     *
     * @return void
     * @author RebornCMS Development Team
     **/
    public static function setSendingCode($sendCode)
    {
       static::$sending = array_replace_recursive(static::$sending, $sendCode);
    }

    /**
     * Sending Mail function
     *
     * @param array for options
     * @return boolean
     *
     **/
    public static function send($config = array())
    {

        if (isset($config['to']) && !is_array($config['to'])) {
            $config['to'] = array($config['to']);
        }
        $config = array_replace_recursive(static::$config, $config);

        $transport = $config['transport'];
        if (empty($transport['type'])) {

            $transport['type'] = \Setting::get('transport_mail');
            $transport['host'] = \Setting::get('smtp_host');
            $transport['port'] = \Setting::get('smtp_port');
            $transport['username'] = \Setting::get('smtp_username');
            $transport['password'] = \Setting::get('smtp_password');
            $transport['mailpath'] = \Setting::get('sendmail_path');

        }

        $tran = static::transport($transport);

        if ($tran == null) {

            $result['fail'] = static::$error['notSupportType'];

            return $result;
        }

        $mailer = \Swift_Mailer::newInstance($tran);

        $message = \Swift_Message::newInstance()
                    ->setEncoder(\Swift_Encoding::get8BitEncoding())
                    ->setFrom(array($config['from'] => $config['name']))
                    ->setBody($config['body'],'text/html');

        if (isset($config['subject'])) {
            $message->setSubject($config['subject']);
        }

        if (isset($config['part'])) {
            $message->addPart($config['part'] , 'text/plain');
        }

        if ($config['attachment']['value']) {

            $uploadError = Upload::uploadInit($config['attachment']['fieldName'], $config['attachmentConfig']);

            if ($uploadError) {
                return $result['fail'] = $uploadError['errors']['0'];
            }
            $attachmentName = Upload::upload($config['attachment']['fieldName']);

            $message->attach(\Swift_Attachment::fromPath($config['attachmentConfig']['path'].DS.$attachmentName['savedName']));
            static::$attName = $attachmentName['savedName'];
        }

        foreach ($config['to'] as $key) {
            $message->setTo($key);
            try {
                $mailer->send($message, $failedRecipients);
            } catch (\Swift_TransportException $e) {
                $result['fail']	= 'Connection could not be established with Host';

                return $result;
            }

        }

        if ($failedRecipients) {
            static::$sendError = $failedRecipients;
            $v = implode(', ' , $failedRecipients);
            $result['fail'] = static::$sending['fail'].'- ' . $v;

            return $result;
        }
        $result['success'] = static::$sending['success'];

        return $result;
    }

    /**
     * Chose mail transport
     *
     * @return object
     * @author RebornCMS Development Team
     **/
    private static function transport($transport = array())
    {
        $tran = null;
        if ($transport['type'] == 'mail') {
            $tran = \Swift_MailTransport::newInstance();
        } elseif ($transport['type'] == 'smtp') {
            $tran = \Swift_SmtpTransport::newInstance($transport['host'] , $transport['port'])
                                                        ->setUsername($transport['username'])
                                                        ->setPassword($transport['password']);
        } elseif ($transport['type'] == 'sendmail') {
            $tran = \Swift_SendmailTransport::newInstance($transport['mailpath']);
        }

        return $tran;
    }

} // END class Mail
