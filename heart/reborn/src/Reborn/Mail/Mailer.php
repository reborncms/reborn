<?php

namespace Reborn\Mail;

use Swift_Mailer;
use Swift_Message;
use Swift_Encoding;
use Swift_Attachment;

/**
 * Mailer class for Reborn CMS.
 *
 * @package default
 * @author MyanmarLinks Professional Web Development Team
 **/
class Mailer
{
	/**
	 * Transport manager instance
	 *
	 * @var \Reborn\Mail\Manager
	 **/
	protected $manager;

	/**
	 * Swift_Mailer instance
	 *
	 * @var \Swift_Mailer
	 **/
	protected $mailer;

	/**
	 * Swift_Message instance
	 *
	 * @var \Swift_Message
	 **/
	protected $message;

	/**
	 * Data array for message body's placeholder.
	 *
	 * @var array
	 **/
	protected $message_data = array();

	/**
	 * Message main layout template folder path
	 *
	 * @var string
	 **/
	protected $template_path;

	/**
	 * Variable for error message
	 *
	 * @var string
	 **/
	protected $error;

	/**
	 * Failer addresses from mail sending
	 *
	 * @var array
	 **/
	protected $failer;

	/**
	 * Create new Mailer instance with static method.
	 *
	 * @param array $config
	 * @return \Reborn\Mail\Mailer
	 **/
	public static function create($config = array())
	{
		return new static($config);
	}

	/**
	 * Default create instance method.
	 *
	 * @param array $config
	 * @return void
	 **/
	public function __construct($config = array())
	{
		$this->manager = new Manager();

		$transport = $this->manager->getTransport($config);

		$this->mailer = Swift_Mailer::newInstance($transport);

		$this->message = Swift_Message::newInstance();

		$this->message->setEncoder(Swift_Encoding::get8BitEncoding());

		$this->setTemplatePath(APP.'views'.DS.'email'.DS);
	}

	/**
	 * Set Layout template path.
	 * Template path must be end with DS.
	 * eg: BASE/heart/appdata/views/email/
	 *
	 * @return void
	 **/
	public function setTemplatePath($path)
	{
		$this->template_path = $path;
	}

	/**
	 * Set data array for message placeholder.
	 *
	 * @param array $data
	 * @param boolean $merge
	 * @return \Reborn\Mail\Mailer
	 **/
	public function data(array $data, $merge = true)
	{
		if ($merge) {
			$this->message_data = array_merge($this->message_data, $data);

			return $this;
		}

		$this->message_data = $data;

		return $this;
	}

	/**
	 * Get Manager instance
	 *
	 * @return \Reborn\Mail\Manager
	 **/
	public function getManager()
	{
		return $this->manager;
	}

	/**
	 * Get Swift_Message instance
	 *
	 * @return \Swift_Message
	 **/
	public function getMessage(Swift_Message $message)
	{
		return $this->message;
	}

	/**
	 * Set Swift_Message instance
	 *
	 * @param \Swift_Message
	 * @return void
	 **/
	public function setMessage()
	{
		$this->message = $message;

		return $this;
	}

	/**
	 * Get Swift_Mailer instance
	 *
	 * @return \Swift_Mailer
	 **/
	public function getSwiftMailer()
	{
		return $this->mailer;
	}

	/**
	 * Set Swift_Mailer instance
	 *
	 * @param \Swift_Mailer
	 * @return void
	 **/
	public function setSwiftMailer(Swift_Mailer $mailer)
	{
		$this->mailer = $mailer;

		return $this;
	}

	/**
	 * Get mail sending error
	 *
	 * @return string|null
	 **/
	public function getError()
	{
		return $this->error;
	}

	/**
	 * Get mail sending failer addresses
	 *
	 * @return array|null
	 **/
	public function getFailer()
	{
		return $this->failer;
	}

	/**
	 * Set the subject for this message
	 *
	 * @param string $subject
	 * @return \Reborn\Mail\Mailer
	 **/
	public function subject($subject)
	{
		$this->message->setSubject($subject);

		return $this;
	}

	/**
	 * Set the body for this message
	 *
	 * @param string $message
	 * @param string $template Layout template with HTML tag.
	 * @param string $content_type
	 * @return \Reborn\Mail\Mailer
	 **/
	public function body($message, $template = 'default.html', $content_type = 'text/html')
	{
		$this->message->setBody($this->getBodyData($message, $template), $content_type);

		return $this;
	}

	/**
	 * Set the body for this message
	 *
	 * @param string $message
	 * @param string $content_type
	 * @return \Reborn\Mail\Mailer
	 **/
	public function part($message, $content_type = 'text/plain')
	{
		$this->message->addPart($message, $content_type);

		return $this;
	}

	/**
	 * Set the to address of this message.
	 *
	 * @param string|array $addresses
	 * @param string|null $name
	 * @return \Reborn\Mail\Mailer
	 **/
	public function to($addresses, $name = null)
	{
		if (is_array($addresses)) {
			$this->message->setTo($addresses, $name);
		} else {
			$this->message->addTo($addresses, $name);
		}

		return $this;
	}

	/**
	 * Set the replyTo address of this message.
	 *
	 * @param string|array $addresses
	 * @param string|null $name
	 * @return \Reborn\Mail\Mailer
	 **/
	public function replyTo($addresses, $name = null)
	{
		if (is_array($addresses)) {
			$this->message->setReplyTo($addresses, $name);
		} else {
			$this->message->addReplyTo($addresses, $name);
		}

		return $this;
	}

	/**
	 * Set the from address of this message.
	 *
	 * @param string|array $addresses
	 * @param string|null $name
	 * @return \Reborn\Mail\Mailer
	 **/
	public function from($addresses, $name = null)
	{
		if (is_array($addresses)) {
			$this->message->setFrom($addresses, $name);
		} else {
			$this->message->addFrom($addresses, $name);
		}

		return $this;
	}

	/**
	 * Set the cc address of this message.
	 *
	 * @param string|array $addresses
	 * @param string|null $name
	 * @return \Reborn\Mail\Mailer
	 **/
	public function cc($addresses, $name = null)
	{
		if (is_array($addresses)) {
			$this->message->setCc($addresses, $name);
		} else {
			$this->message->addCc($addresses, $name);
		}

		return $this;
	}

	/**
	 * Set the bcc address of this message.
	 *
	 * @param string|array $addresses
	 * @param string|null $name
	 * @return \Reborn\Mail\Mailer
	 **/
	public function bcc($addresses, $name = null)
	{
		if (is_array($addresses)) {
			$this->message->setBcc($addresses, $name);
		} else {
			$this->message->addBcc($addresses, $name);
		}

		return $this;
	}

	/**
	 * Set attchment for this message.
	 *
	 * @param string $file Attach file or attach data
	 * @param string|null $name
	 * @param string|null $content_type
	 * @param boolean $data Set true for attach data
	 * @return \Reborn\Mail\Mailer
	 **/
	public function attach($file, $name = null, $content_type = null, $data = false)
	{
		if ($data) {
			$attachment = Swift_Attachment::newInstance($file);
		} else {
			$attachment = Swift_Attachment::fromPath($file);
		}

		if (! is_null($name) ) {
			$attachment->setFilename($name);
		}

		if (! is_null($content_type) ) {
			$attachment->setContentType($content_type);
		}

		$this->message->attach($attachment);

		return $this;
	}

	/**
	 * Set data attchment for this message.
	 *
	 * @param string $data Attach data
	 * @param string|null $name
	 * @param string|null $content_type
	 * @return \Reborn\Mail\Mailer
	 **/
	public function dataAttach($data, $name = null, $content_type = null)
	{
		return $this->attach($data, $name, $content_type, true);
	}

	/**
	 * Set attchment from input data for this message.
	 *
	 * @param string $input
	 * @param string|null $name
	 * @param string|null $content_type
	 * @return \Reborn\Mail\Mailer
	 * @todo Check for addded or not
	 **/
	public function inputAttach($data, $name = null, $content_type = null)
	{
		return $this->attach($data, $name, $content_type, true);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Nyan Lynn Htut
	 **/
	public function embed($file)
	{
		return $this->message->embed(\Swift_Image::fromPath($file));
	}

	/**
     * Set the priority of this message.
     *
     * The value is an integer where 1 is the highest priority and 5 is the lowest.
     *
     * @param integer $priority
     * @return \Reborn\Mail\Mailer
     */
    public function priority($priority)
    {
    	$this->message->setPriority($priority);

    	return $this;
    }

    /**
     * Send message with swift mailer.
     *
     * @param boolean $fake Use fake sending for test.
     * @return integer
     **/
    public function send($fake = false)
    {
    	$this->prepareCheckForSending();

    	if ($fake) {
    		return $this->fakeSend();
    	}

    	$result = 0;

    	try {
			$result = $this->mailer->send($this->message, $failed);
		} catch (\Swift_TransportException $e) {
			$this->error = $e->getMessage();
		}

		if ($failed) {
			$this->failer = $failed;
		}

    	return $result;
    }

    /**
     * Fake Send for mail testing.
     * You can check this mail message file at
     * STORAGES/tmp/Y-m-d-H-i-subject-title.mail
     *
     * @return boolean
     **/
    public function fakeSend()
    {
    	\File::put($this->getFakeMailPath(), $this->message->toString());

    	return true;
    }

    /**
     * Get Fake Email File Path
     *
     * @return string
     **/
    protected function getFakeMailPath()
    {
    	$file = date('Y-m-d-H-i-').\Str::slug($this->message->getSubject());

    	return STORAGES.'tmp'.DS.$file.'.mail';
    }

    /**
     * Get Message Body with replace binded data.
     *
     * @param string $message
     * @param string $template
     * @return string
     **/
    protected function getBodyData($message, $template)
    {
    	$body = '{{message_body}}';

    	if ( \File::is($this->template_path.$template) ) {
    		$body = \File::getContent($this->template_path.$template);
    	}

    	$data = array_merge(array('message_body' => $message), $this->message_data);

    	foreach ($data as $k => $v) {
    		$body = str_replace('{{'.$k.'}}', $v, $body);
    	}

    	return $body;
    }

    /**
     * Prepare check for message from address before send.
     *
     * @return void
     **/
    protected function prepareCheckForSending()
    {
    	$from = $this->message->getFrom();

    	if ( empty($from) ) {
    		$from = $this->manager->config('sender_mail');
    		$this->from($from, \Setting::get('site_title'));
    	}
    }

} // END class Mailer
