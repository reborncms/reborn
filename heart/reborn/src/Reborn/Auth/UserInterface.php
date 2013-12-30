<?php

namespace Reborn\Auth;

interface UserInterface
{
	/**
	 * Get loggedin user data model.
	 *
	 * @return mixed
	 **/
	public function getUser();

	/**
	 * Get loggedin user's "id" data value.
	 *
	 * @return integer|null
	 **/
	public function getUserId();

	/**
	 * Get loggedin user's "username" or "fullname" data value.
	 *
	 * @param \Closure|null $callback Callback function for username.
	 * @return string|null
	 **/
	public function getUsername(\Closure $callback = null);

	/**
	 * Get loggedin user's "email" data value.
	 *
	 * @return string|null
	 **/
	public function getUserEmail();
}
