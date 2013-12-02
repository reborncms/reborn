<?php

namespace Maintenance;

class MaintenanceInfo extends \Reborn\Module\AbstractInfo
{
	/**
	 * Module name variable
	 *
	 * @var string
	 **/
	protected $name = 'Maintenance';

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
								'en' => 'Maintenance'
								);

	/**
	 * Module description variable
	 *
	 * @var string
	 **/
	protected $description = array(
							'en' => 'Manage Cache Files'
							);

	/**
	 * Module author name variable
	 *
	 * @var string
	 **/
	protected $author = 'Li Jia Li';

	/**
	 * Module author URL variable
	 *
	 * @var string
	 **/
	protected $authorUrl = 'http://dragonvirus.com';

	/**
	 * Module author Email variable
	 *
	 * @var string
	 **/
	protected $authorEmail = 'limonster.li@gmail.com';

	/**
	 * Module Frontend support variable
	 *
	 * @var boolean
	 **/
	protected $frontendSupport = false;

	/**
	 * Module Backend support variable
	 *
	 * @var boolean
	 **/
	protected $backendSupport = true;

	/**
	 * Module can be use Default Module for Frontend variable
	 *
	 * @var boolean
	 **/
	protected $useAsDefaultModule = false;

	/**
	 * Module's URI Prefix variable
	 *
	 * @var string
	 **/
	protected $uriPrefix = 'maintenance';

	/**
	 * Variable for Allow to change the URI Prefix from user.
	 *
	 * @var boolean
	 **/
	protected $allowToChangeUriPrefix = true;

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

	protected $sharedData = false;

}
