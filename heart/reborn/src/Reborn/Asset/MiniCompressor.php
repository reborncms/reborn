<?php

namespace Reborn\Asset;

/**
 * CSS Mini Compressor class for the Reborn
 *
 * @package Reborn\Asset
 * @author Myanmar Links Professional Web Development Team
 **/
class MiniCompressor
{
	public function __construct() {}

	public function make($content)
	{
		$result = str_replace("\r\n", "\n", $content);

		// remove spaces from semicolons
		$result = preg_replace('/\\s*;\\s*/', ';', $result);

		// Remove spaces from {}
		$result = preg_replace('/\\s*{\\s*/', '{', $result);
		$result = preg_replace('/;?\\s*}\\s*/', '}', $result);

		// Replace new lines with single new lines
		$result = preg_replace('/[ \\t]*\\n+\\s*/', "\n", $result);

		// Replace comma + new line with comma only
		$result = preg_replace('/,\\n+/', ',', $result);

		return $result;
	}


} // End of MiniCompressor
