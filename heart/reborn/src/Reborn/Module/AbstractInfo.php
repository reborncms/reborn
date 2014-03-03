<?php

namespace Reborn\Module;

/**
 * Module Info Abstract Class for Reborn Module
 *
 * @package Reborn\Module
 * @author Myanmar Links Professional Web Development Team
 **/
abstract class AbstractInfo
{
    /**
     * Module name variable
     *
     * @var string
     **/
    protected $name;

    /**
     * Module version variable
     *
     * @var string
     **/
    protected $version;

    /**
     * Module Display name variable.
     *
     * @var string
     **/
    protected $displayName = array();

    /**
     * Module description variable
     *
     * @var string
     **/
    protected $description = '';

    /**
     * Module author name variable
     *
     * @var string
     **/
    protected $author;

    /**
     * Module author URL variable
     *
     * @var string
     **/
    protected $authorUrl;

    /**
     * Module author Email variable
     *
     * @var string
     **/
    protected $authorEmail;

    /**
     * Module Frontend support variable
     *
     * @var boolean
     **/
    protected $frontendSupport;

    /**
     * Module Backend support variable
     *
     * @var boolean
     **/
    protected $backendSupport;

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
    protected $uriPrefix;

    /**
     * Variable for Allow to change the URI Prefix from user.
     *
     * @var boolean
     **/
    protected $allowToChangeUriPrefix = false;

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
     * Database Table is shared for multisite.
     *
     * @var boolean
     **/
    protected $sharedData = true;

    /**
     * Database table is not shared table.
     * But sometime user want to use shared table by force.
     * If you want to doesn't allow shared user by force,
     * Set "true" for this value.
     *
     * @var boolean
     **/
    protected $allowSharedByUser = false;

    /**
     * Namespace of the Module.
     *
     * @var string
     **/
    protected $namespace;

    public function getNamespace()
    {
        if (is_null($this->namespace)) {
            $ref = new \ReflectionObject($this);
            $this->namespace = $ref->getNamespaceName();
        }

        return $this->namespace;
    }

    /**
     * Get the All Module Info
     *
     * @return array
     */
    public function getAll()
    {
        $isCore = false;

        $cores = \Config::get('app.module.cores');

        $ns = $this->getNamespace();

        if (in_array($ns, $cores)) {
            $isCore = true;
        }

        // Description of Module
        if (!is_array($this->description)) {
            $desc = array('en' => $this->description);
        } else {
            $desc = $this->description;
        }

        return array(
                'ns' => $ns, // Namespace for $this module
                'uri' => $this->uriPrefix,
                'name' => $this->name, // Name of $this module
                'display_name' => $this->displayName, // DisplayName of $this module
                'roles' => $this->roles, // Module Action roles
                'isCore' => $isCore, // Module is Core Module or not
                'version' => $this->version, // Version no. string of $this module
                'description' => $desc, // Description of $this module
                'author' => $this->author, // Author of $this module
                'author_url' => $this->authorUrl, // Author URL of $this module
                'author_email' => $this->authorEmail, // Author Email of $this module
                'frontend_support' => $this->frontendSupport,
                'backend_support' => $this->backendSupport,
                'default_module_mode' => $this->useAsDefaultModule,
                'allow_uri_change' => $this->allowToChangeUriPrefix,
                'allow_custom_field' => $this->allowCustomfield,
                'shared_data' => $this->sharedData,
                'allow_shared_by_user' => $this->allowSharedByUser
            );
    }

    /**
     * Magic method __call
     *
     */
    public function __call($method, $args)
    {
        $property = lcfirst(substr($method, 3));

        if (property_exists($this, $property)) {
            return $this->{$property};
        }
    }

} // End  of class AbstractInfo
