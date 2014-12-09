<?php

namespace User;

interface ApiError
{
	const USER_BANNED_CODE = 1001;
	const USER_BANNED_MESSAGE = "User is banned";

	const USER_SUSPENDED_CODE = 1002;
	const USER_SUSPENDED_MESSAGE = "User is suspended";

	const USER_ALREADY_EXISTS_CODE = 2000;
	const USER_ALREADY_EXISTS_MESSAGE = 'User already exists';

	const USER_NOT_FOUND_CODE = 2001;
	const USER_NOT_FOUND_MESSAGE = "User not found";

	const USER_NOT_ACTIVATED_CODE = 2002;
	const USER_NOT_ACTIVATED_MESSAGE = "User is not activated";

	const USER_ALREADY_ACTIVATED_CODE = 2003;
	const USER_ALREADY_ACTIVATED_MESSAGE = "User is already activated";

	const USER_LOGIN_FIELD_REQUIRED_CODE = 3001;
	const USER_LOGIN_FIELD_REQUIRED_MESSAGE = "Login field attribute is required";

	const USER_PASSWORD_FIELD_REQUIRED_CODE = 3002;
	const USER_PASSWORD_FIELD_REQUIRED_MESSAGE = "Password attribute is required";

	const USER_VALIDATION_FAIL_CODE = 4001;
}