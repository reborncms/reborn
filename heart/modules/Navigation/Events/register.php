<?php

$parser->addHandler('nav', function($template) {

		$pattern = '/\{\{\snav:(.*)\s\}\}/';
		$callback = function($matches) {
            $arr = explode(' ', $matches[1]);
            $nav = array_shift($arr);
            $str = '<?php echo \Navigation\Lib\Helper::render("'.$nav.'"';
            if (empty($arr)) {
            	return $str.'); ?>';
            } else {
            	$tag = 'ul';
            	$active = 'active';
            	foreach ($arr as $a) {
            		list($key, $value) = explode('=', $a);
            		if ('tag' == $key) {
            			$tag = "$value";
            		} elseif ('active' == $key) {
            			$active = "$value";
            		}
            	}
            	return $str.', "'.$tag.'", "'.$active.'"); ?>';
            }
        };

        return preg_replace_callback($pattern, $callback, $template);
	});
