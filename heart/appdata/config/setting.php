<?php

return array(

	/**
	 * Skip Setting
	 * Don't show at setting panel
	 */
	'skip' => array('admin_theme', 'public_theme'),

	/**
	 * Default Module Setting
	 */
	'default_module' => array(
		'type' => 'select',
		'require' => true,
		'option' => function() {
				$mods = \Module::getAll();
				$results = array();
				foreach ($mods as $mod => $v) {
					if (($v['enabled'] === true) and ($v['frontendSupport'] == true)) {
						$results[$mod] = $v['name'];
					}
				}

				return $results;
			},
		),
	/**
	 * Home Page
	 */
	'home_page' => array(
        'type' => 'select',
        'option' => function() {
        	\Module::load('Pages');
			$page_opt = \Pages\Lib\Helper::pageList();
			return $page_opt;
        },
    ),

    /**
	 * Item per page to show in admin panel
	 */
    'admin_item_per_page' => array(
    	'type' => 'text',
    ),

	/**
	 * Site Title Setting
	 */
	'site_title' => array(
			'type' => 'text',
			'require' => true,
		),

	/**
	 * Site Slogan Setting
	 */
	'site_slogan' => array(
			'type' => 'text'
		),

	/**
	 * Public Theme Setting
	 */
	/*'public_theme' => array(
			'type' => 'select',
			'require' => false,
			'class' => 'full',
			'option' => function() {
				$themes = \Dir::get(THEMES.'*');
				foreach ($themes as $t) {
					$th = str_replace(THEMES, '', $t);
					$theme[$th] = $th;
				}

				return $theme;
			}
		),*/

	/**
	 * Admin Panel Url Setting
	 */
	'adminpanel_url' => array(
			'type' => 'text',
			'require' => true,
		),

	/**
	 * Default Language Setting
	 */
	'default_language' => array(
			'type' => 'select',
			'require' => false,
			'option' => function() {
				$langs = \Config::get('app.support_langs');

				return $langs;
			}
		),

	/**
	 * Timezone Setting
	 */
	'timezone' => array(
			'type' => 'select',
			'require' => false,
			'option' => function() {
				$tz = new \Reborn\Util\Timezone();
				return $tz->lists();
			}
		),

	/**
	 * Frontend Enabled Setting
	 */
	'frontend_enabled' => array(
			'type' => 'select',
			'option' => array('1' => 'Enable', '0' => 'Disable')
		),

	/**
	 * Span Filter Key Setting
	 */
	'spam_filter' => array(
			'type' => 'text'
		),


	/*'callback_sample' => array(
			'type' => 'callback:hellow', // callback:(eventName);
			'require' => false,
			'option' => function() {
				$langs = \Config::get('app.support_langs');

				return $langs;
			}
		),*/


);
