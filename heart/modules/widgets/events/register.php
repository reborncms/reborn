<?php

$parser->addHandler('widget', function ($template) {

        $pattern = '/\{\{\swidgetArea:(.*)\s\}\}/';
        $callback = function ($matches) {
            return "<?php echo \Widgets\Lib\Helper::areaRender(\"$matches[1]\"); ?>";

        };

        return preg_replace_callback($pattern, $callback, $template);

});
