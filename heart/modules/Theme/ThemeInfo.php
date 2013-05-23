<?php

namespace Theme;

class ThemeInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Theme';

	protected $version = '1.0';

	protected $description = 'Allows admins and staff to switch themes, upload new themes, and manage theme options.';

	protected $author = 'K';

	protected $authorUrl = 'http://khaynote.com';

	protected $authorEmail = 'khayusaki@gmail.com';

	protected $frontendSupprot = true;

	protected $backendSupport = true;

	protected $uriPrefix = 'theme';

	protected $allowToChangeUriPrefix = false;

	protected $useAsDefaultModule = true;
}