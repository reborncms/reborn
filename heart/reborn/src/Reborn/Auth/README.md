# Reborn Authentication Usage

Reborn CMS used Sentry 2 for default Authentication Package. You can see SentryProvider at `/heart/reborn/src/Reborn/Auth/AuthSentryProvider.php`

## Authenticating Users

** For Login **

	Auth::authenticate(array $credentials, $remember = false);

** For Register **

	Auth::register(array $credentials, $activate = false);

** Check to User is logged in or not **

	Auth::check();

** For Log out process **

	Auth::logout();

** Check User has access for given permission **

	Auth::hasAccess($permissions, $all = true);

** For loggedin user's data model **

`null` will return when user is not logged in

	// Reurn User Model
	Auth::getUser();

	// Return User ID
	Auth::getUserId();

	// Return User Name ("first_name last_name")
	Auth::getUserName();

	// Return User Name with custom format
	// Output is "Mr.Lynn Htut"
	Auth::getUserName(function($user) {
		return 'Mr.'.$user->fullname;
	});

	// Return User Email
	Auth::getUserEmail();

** Get User or Group Provider **

Reborn have extended Provider for User and Group.

	// For User Provider (\Reborn\Auth\Sentry\Eloquent\UserProvider)
	Auth::getUserProvider();
	// or
	$app->user_provider;

	// For Group Provider (\Reborn\Auth\Sentry\Eloquent\GroupProvider)
	Auth::getGroupProvider();
	// or
	$app->group_provider;

## Using User Provider and Group Provider

Reborn have `User` and `UserGroup` Facade Class for each providers. These Classes is same with *Get User or Group Provider*

	// Get all user (Same with Eloquent::all())
	User::all($columns = array('*'));

	// Get User by
	User::findBy('first_name', 'Lynn', $columns = array('*'));

	// Get all group
	UserGroup::all($columns = array('*'));

	// Get Group by
	UserGroup::findBy('name', 'User', $columns = array('*'));

	// Delete Group by ID
	// $move_to is group name to move users where deleted group
	UserGroup::delete($id, $move_to = 'User');

## User Data Attributes

	$user = User::findBy('id', $id);

	// If you need to get user's metadata
	$user->metadata;

	// Get user fullname
	$user->fullname;

	// Get user profile image
	// Default is gravatar with image width 120 and img element tag
	$user->profile_image;

	// Profile image with custom width
	$user->profileImage(200);
	// Profile image with image src url only
	$user->prdfileImage(120, true);

Sometime you need to set user's profile image with custom photo (not gravatar).
Use `get.user.profile_image` event.

Example

	// Event
	Event::on('get.user.profile_image', function($model, $width, $url_only) {
		$src = custom_profile_image($model->email, $width);

		if ($url_only) {
			return $src;
		}

		return Html::img($src);
	});
