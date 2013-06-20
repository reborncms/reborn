<?php

namespace Module;

class ModuleInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Module';

	protected $version = '1.0';

	protected $description = 'Module(Extension) Manager';

	protected $author = 'Nyan Lynn Htut';

	protected $authorUrl = 'http://www.myanmarlinks.net';

	protected $authorEmail = 'lynnhtut87@gmail.com';

	protected $frontendSupport = false;

	protected $backendSupport = true;

	protected $uriPrefix = 'module';

	protected $roles = array(
						'module.upload' => 'Module Upload',
						'module.install' => 'Module Install',
						'module.disable' => 'Module Disable',
						'module.enable' => 'Module Enable',
						);

}
