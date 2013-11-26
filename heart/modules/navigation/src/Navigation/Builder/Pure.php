<?php

namespace Navigation\Builder;

/**
 * Pure CSS Navigation Builder Class
 *
 * @package Navigation
 * @author MyanmarLinks Professional Web Development Team
 **/
class Pure extends Base
{

	/**
	 * Get active class name.
	 *
	 * @return string
	 **/
	protected function getActiveClass()
	{
		return 'pure-menu-selected';
	}

} // END class Pure
