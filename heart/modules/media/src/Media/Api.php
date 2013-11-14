<?php

namespace Media;

use Media\Model\Files;

/**
 * Media File Api Class
 *
 * @package Media
 * @author MyanmarLinks Professional Web Development Team
 **/
class Api
{

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	public function images($limit = 20, $offset = null, $type = null)
	{
		$type = $this->getImageType($type);

		$files = Files::whereIn('mime_type', $type)
						->take($limit)
						->skip($offset)
						->orderBy('created_at', 'desc')
						->get();

		return $files->toArray();
	}

	/**
	 * Get Image Mine Type for Filter Query
	 *
	 * @param string|null $type
	 * @return array
	 **/
	protected function getImageType($type)
	{
		switch (strtolower($type)) {
			case 'jpg':
			case 'jpeg':
				return array('image/jpeg');
				break;

			case 'png':
				return array('image/png');
				break;

			case 'gif':
				return array('image/gif');
				break;

			case 'tiff':
				return array('image/tiff');
				break;

			default:
				return array('image/jpeg', 'image/png', 'image/gif', 'image/tiff');
				break;
		}
	}

} // END class Api
