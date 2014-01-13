<?php

namespace {classname};

class {classname}Info extends \Reborn\Module\AbstractInfo
{
	/**
	 * Module name variable
	 *
	 * @var string
	 **/
	protected $name = '{module}';

	/**
	 * Module version variable
	 *
	 * @var string
	 **/
	protected $version = '1.0';

	/**
	 * Module Display name variable.
	 *
	 * @var string
	 **/
	protected $displayName = array(
								'en' => '{name}'
								);

	/**
	 * Module description variable
	 *
	 * @var string
	 **/
	protected $description = array(
							'en' => '{description}'
							);

	/**
	 * Module author name variable
	 *
	 * @var string
	 **/
	protected $author = '{author}';

	/**
	 * Module author URL variable
	 *
	 * @var string
	 **/
	protected $authorUrl = '{authorUrl}';

	/**
	 * Module author Email variable
	 *
	 * @var string
	 **/
	protected $authorEmail = '{authorEmail}';

	/**
	 * Module Frontend support variable
	 *
	 * @var boolean
	 **/
	protected $frontendSupport = {frontend};

	/**
	 * Module Backend support variable
	 *
	 * @var boolean
	 **/
	protected $backendSupport = {backend};

	/**
	 * Module can be use Default Module for Frontend variable
	 *
	 * @var boolean
	 **/
	protected $useAsDefaultModule = {allowDefaultModule};

	/**
	 * Module's URI Prefix variable
	 *
	 * @var string
	 **/
	protected $uriPrefix = {prefix};

	/**
	 * Variable for Allow to change the URI Prefix from user.
	 *
	 * @var boolean
	 **/
	protected $allowToChangeUriPrefix = {allowToChangeUriPrefix};

	/**
	 * Variable for Module Actions Roles list.
	 * Module Action permission will be decided on this role.
	 *
	 * @var array
	 **/
	protected $roles = array();

	/**
	 * Variable for Allow Custom Field.
	 * If you allow custom field in your module, set true
	 *
	 * @var boolean
	 **/
	protected $allowCustomfield = false;

	/**
	 * Table is shared for multisite.
	 *
	 * @var boolean
	 **/
	protected $sharedData = false;

}
