<?php

namespace Reborn\MVC\View\Handler;

use Reborn\MVC\View\AbstractHandler;

/**
 * Abstract Handler Class for the Reborn View Parser
 *
 * @package Reborn\MVC\View
 * @author Myanmar Links Professional Web Development Team
 **/
class Form extends AbstractHandler
{

    // Object for Form Data
    protected $object;

    // Input Type
    protected $inputs = array('text', 'hidden',
                        'password', 'search', 'url', 'tel');

    public function getKey()
    {
        return 'form';
    }

    public function handle($temp, $data)
    {
        $pattern = '/\{\{\s(form:)(.*)\s\}\}([\s\S]*?)\{\{\s(endform)\s\}\}/m';

        $callback = array($this, 'parseForm');

        return preg_replace_callback($pattern, $callback, $temp);
    }

    protected function parseForm($matches)
    {
        $open = $this->parseOpen($matches[2]);
        $body = $this->parseBody($matches[3]);
        $close = $this->parseClose($matches[4]);

        return $open.$body.$close;
    }

    protected function parseOpen($str)
    {
        $splits = $this->parser->splitContent($str);

        // Form Name
        $name = array_shift($splits);

        // Use enctype=multipart/form-data or not?
        if (isset($splits['file']) and ('use' == $splits['file'])) {
            $file = "true";
        } else {
            $file = "false";
        }
        //$url = isset($splits['url']) ? $splits['url'] : '';
        $this->object = isset($splits['object']) ? $splits['object'] : null;
        $url = '';
        if (isset($splits['url'])) {
            $url = $splits['url'];
            $x = $this->object.'->$1';
            $url = preg_replace('/\$(\w+)/', $x, $url);
        }

        unset($splits['url']);
        unset($splits['object']);
        unset($splits['file']);
        $attrs = $this->arrStr($splits);

        $str = '<?php echo Form::start("'.$url.'", "'.$name.'", '.$file.', ';
        $str .= $attrs.'); ?>';

        return $str;
    }

    protected function parseClose($strs)
    {
        return '<?php echo Form::end(); ?>';
    }

    protected function parseBody($strs)
    {
        $pattern = '/\{\{\s(.*)\s\}\}/';
        $data = preg_replace_callback($pattern, array($this, 'parseField'), $strs);

        return $data;
    }

    protected function parseField($strs)
    {
        $strs = explode(' ', $strs[1], 2);
        list($type, $name) = explode(':', array_shift($strs));

        if (isset($strs[0]) and !empty($strs[0])) {
            $strs = $this->parser->splitContent($strs[0], '_attrs');
        }

        if (isset($strs['_attrs'])) {
            $strs['_attrs'] = '"'.trim($strs['_attrs'],'"').'"';
        }

        if (in_array($type, $this->inputs)) {
            $form = '<?php echo Form::input("'.$name.'", ';

            $val = $this->getValue($type, $name);
            $attrs = $this->arrStr($strs);
            $form .= $val.', "'.$type.'", '.$attrs.'); ?>';

        } elseif ('label' == $type) {
            $form = '<?php echo Form::label('.$strs['_attrs'].', "'.$name.'"); ?>';
        } elseif ('textarea' == $type) {
            $val = $this->getValue($type, $name);
            $attrs = $this->arrStr($strs);
            $form = '<?php echo Form::textarea("'.$name.'", '.$val.', '.$attrs.'); ?>';
        }

        return $form;
    }

    protected function getValue($type, $name)
    {
        if ('file' == $type) {
            $val = "null";
        } else {
            if (!is_null($this->object)) {
                $val = 'isset('.$this->object.'->'.$name.') ? ';
                $val .= $this->object.'->'.$name.' : null ';
            } else {
                $val = 'null';
            }
        }

        return $val;
    }

    protected function arrStr($arr)
    {
        $start = 'array(';
        $end = ')';
        $s = '';
        if (empty($arr)) {
            return $start.$end;
        }
        foreach ($arr as $k => $v) {
            $v = trim($v, '"');
            $s .= '"'.$k.'" => "'.$v.'", ';
        }
        $s = rtrim($s, ', ');

        return $start.$s.$end;
    }
}
