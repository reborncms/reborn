<?php

namespace {module};

class {module}Info extends \Reborn\Module\AbstractInfo
{
	protected $name = '{name}';

	protected $version = '{version}';

	protected $displayName = array(
								'en' => '{name}'
								);

	protected $description = array(
							'en' => '{description}'
							);

	protected $author = '{author}';

	protected $authorUrl = '{authorUrl}';

	protected $authorEmail = '{authorEmail}';

	protected $frontendSupport = {frontend};

	protected $backendSupport = {backend};

	protected $useAsDefaultModule = {allowDefaultModule};

	protected $uriPrefix = {prefix};

	protected $allowToChangeUriPrefix = {allowToChangeUriPrefix};

}
