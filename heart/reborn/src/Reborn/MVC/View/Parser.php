<?php

namespace Reborn\MVC\View;

use Reborn\MVC\View\AbstractHandler;

/**
 * Template Parser class for Reborn View
 *
 * @package Reborn\MVC\View
 * @author Myanmar Links Professional Web Development Team
 **/
class Parser
{

    /**
     * Default Parser Handler Lists
     *
     * @var array
     **/
    protected $handlers = array(
            'include'       => 'handleInclude',
            'nav'           => 'handleNavigation',
            'partial'       => 'handlePartial',
            'partial_loop'  => 'handlePartialLoop',
            'loop'          => 'handleLoop',
            'if'            => 'handleIf',
            'ifnot'         => 'handleIfNot',
            'ifempty'       => 'handleIfEmpty',
            'ifnotempty'    => 'handleIfNotEmpty',
            'ifset'         => 'handleIfIsset',
            'ifnotset'      => 'handleIfNotIsset',
            'elseif'        => 'handleElseIf',
            'else'          => 'handleElse',
            'breadcrumb'    => 'handleBreadcrumb',
            'make'          => 'handlerMaker'
        );

    /**
     * New register parser handler lists.
     *
     * @var array
     **/
    protected $addHandlers = array();

    /**
     * Data array for the parser
     *
     * @var array
     */
    protected $data = array();

    /**
     * Saving data for no parse content
     *
     * @var array
     **/
    protected $noparse = array();

    /**
     * Add (register) new parser handler for the parser.
     * example :
     * <code>
     *      $parser->addHander('while', function() {
     *              return new MyParser\Handler\While;
     *          });
     * </code>
     *
     * @param array $handlers Parse handler array
     * @return Reborn\MVC\View\Parser
     **/
    public function addHandler($name, $handler)
    {
        $this->addHandlers[$name] = $handler;

        return $this;
    }

    /**
     * Parse the given template file.
     *
     * @param string $template Template file content
     * @param mixed $data
     * @return string
     */
    public function parse($template, $data)
    {
        $this->data = $data;

        $content = $this->parseString($this->parsePHPTag($template));

        return $content;
    }

    /**
     * Parse template string.
     *
     * @param string $template Template string
     * @return string
     */
    public function parseString($template)
    {
        $template = $this->handleUnParse($template);

        // First step is handle by the add handlers (from the active theme)
        foreach ($this->addHandlers as $name => $handler) {

            if ($handler instanceof \Closure) {
                $template = $handler($template, $this->data);
            } else {
                $handler = new $handler($this);

                if($handler instanceof AbstractHandler) {
                    $template = $handler->handle($template, $this->data);
                }
            }
        }

        // Second step is Handle by the default handlers
        foreach($this->handlers as $key => $func)
        {
            $pattern = '/(?<!\w)(\s*)\{\{\s*('.$key.'.+?)\s*\}\}/';
            if (preg_match($pattern, $template))
            {
                $template = $this->{$func}($template);
            }
        }

        // Third step is handle for PHP Comment
        $template = $this->handleComment($template);

        // Fourth step is handle for Code Block varialble
        $template = $this->handleCodeBlock($template);

        // Handle for echo
        $template = $this->handleEcho($template);

        $template = $this->handleReParse($template);

        return $template;
    }

    /**
     * Split the Content string to array
     *
     * @param string $string Content String
     * @return mixed
     **/
    public function splitContent($string, $keyStr = '_main')
    {
        $split = array();

        // Example String {{ nav:header tag:ul active:active,current }} }}
        if (preg_match('/\"(.*)\"/', $string, $m)) {
            $esc = str_replace(' ', '~', $m[1]);
            $string = str_replace($m[1], $esc, $string);
        }

        $bits = explode(' ', $string);

        foreach ($bits as $bit) {

            if (strpos($bit, ":")) {
                list($key, $value) = explode(':', $bit);
                $split[$key] = str_replace('~', ' ', $value);
            } else {
                $split[$keyStr] = str_replace('~', ' ', $bit);
            }
        }

        return $split;
    }

    /**
     * Parse the PHP Tag to string.
     *
     * @param string $template
     * @return string
     */
    protected function parsePHPTag($template)
    {
        return str_replace( array("<?","?>"), array("&lt;?","?&gt;"), $template );
    }

    /**
     * Handler for noparse string replace
     *
     * @param string $template
     * @return string
     **/
    protected function handleUnParse($template)
    {
        if (preg_match_all('/<noparse>(.*)<\/noparse>/', $template, $match)) {
            $i = 1;
            foreach ($match[0] as $k => $m) {
                $key = $this->getNoParseMarker($i);
                $this->noparse[$key] = $match[1][$k];
                $template = str_replace($m, $key, $template);
                $i++;
            }
        }

        return $template;
    }

    /**
     * Handler for replace noparse key with their value
     *
     * @param string $template
     * @return string
     **/
    protected function handleReParse($template)
    {
        return str_replace(array_keys($this->noparse), array_values($this->noparse), $template);
    }

    /**
     * Get no parse key string
     *
     * @param string $prefix
     * @return string
     **/
    protected function getNoParseMarker($prefix)
    {
        return $prefix.'_noparse_'.\Reborn\Util\Str::random(6);
    }

    /**
     * Handle the Theme file include
     *
     * @param string $template
     * @return string
     **/
    protected function handleInclude($template)
    {
        $pattern = '/\{\{\s*(include):(.*)\s*\}\}/';

        $d = preg_replace($pattern, '<?php echo $this->includeFile("$2")$3; ?>', $template);

        return $d;
    }

    /**
     * Handle the Navigation
     *
     * @param string $template
     * @return string
     **/
    protected function handleNavigation($template)
    {
        $pattern = '/\{\{\s*(nav):(.*)\s*\}\}/';

        $callback = function($matches) {
            $arr = explode(' ', rtrim($matches[2], ' '));
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
    }

    /**
     * Handle the Module Parial Render
     *
     * @param string $template
     * @return string
     **/
    protected function handlePartial($template)
    {
        $pattern = '/\{\{\s*(partial):(.*)\s*\}\}/';

        $d = preg_replace($pattern, '<?php echo $this->partialFile("$2")$3; ?>', $template);

        return $d;
    }

    /**
     * Handle the Module Parial Loop Render
     *
     * @param string $template
     * @return string
     **/
    protected function handlePartialLoop($template)
    {
        $pattern = '/\{\{\spartial_loop(\s*\((.*)\))\s\}\}/';

        return preg_replace($pattern, '<?php echo $this->partialLoop$1; ?>', $template);
    }


    /**
     * Handle the Loop(foreach only) for parser.
     *
     * @param string $template
     * @return string
     */
    protected function handleLoop($template)
    {
        $pattern = '/\{\{\s*(loop)(\s*\(.*\))\s*\}\}/';

        $d =  preg_replace($pattern, '<?php foreach$2 : ?>', $template);
        $d = preg_replace('/\{\{\s*(endloop)\s*\}\}/', '<?php endforeach; ?>', $d);

        return $d;
    }

    /**
     * Handle the PHP if statement
     *
     * @param string $template
     * @return string
     **/
    protected function handleIf($template)
    {
        $pattern = '/\{\{\s*(if)(\s*\(.*\))\s*\}\}/';

        $d =  preg_replace($pattern, '<?php $1$2 : ?>', $template);
        $d = preg_replace('/\{\{\s*(endif)\s*\}\}/', '<?php endif; ?>', $d);

        return $d;
    }

    /**
     * Handle the PHP if statement with not operator
     *
     * @param string $template
     * @return string
     **/
    protected function handleIfNot($template)
    {
        $pattern = '/\{\{\s*(ifnot)(\s*\((.*)\))\s*\}\}/';

        $d =  preg_replace($pattern, '<?php if(! $3) : ?>', $template);
        $d = preg_replace('/\{\{\s*(endif)\s*\}\}/', '<?php endif; ?>', $d);

        return $d;
    }

    /**
     * Handle the PHP if statement with empty function
     *
     * @param string $template
     * @return string
     **/
    protected function handleIfEmpty($template)
    {
        $pattern = '/\{\{\s*(ifempty)(\s*\((.*)\))\s*\}\}/';

        return preg_replace($pattern, '<?php if(empty($3)) : ?>', $template);
    }

    /**
     * Handle the PHP if statement with not empty function
     *
     * @param string $template
     * @return string
     **/
    protected function handleIfNotEmpty($template)
    {
        $pattern = '/\{\{\s*(ifnotempty)(\s*\((.*)\))\s*\}\}/';

        return preg_replace($pattern, '<?php if(! empty($3)) : ?>', $template);
    }

     /**
     * Handle the PHP if statement with isset function
     *
     * @param string $template
     * @return string
     **/
    protected function handleIfIsset($template)
    {
        $pattern = '/\{\{\s*(ifset)(\s*\((.*)\))\s*\}\}/';

        return preg_replace($pattern, '<?php if(isset($3)) : ?>', $template);
    }

     /**
     * Handle the PHP if statement with not isset function
     *
     * @param string $template
     * @return string
     **/
    protected function handleIfNotIsset($template)
    {
        $pattern = '/\{\{\s*(ifnotset)(\s*\((.*)\))\s*\}\}/';

        return preg_replace($pattern, '<?php if(! isset($3)) : ?>', $template);
    }

    /**
     * Handle the PHP elsif statement
     *
     * @param string $template
     * @return string
     **/
    protected function handleElseIf($template)
    {
        $pattern = '/\{\{\s*(elseif)(\s*\(.*\))\s*\}\}/';

        return preg_replace($pattern, '<?php $1$2 : ?>', $template);
    }

    /**
     * Handle the PHP else statement
     *
     * @param string $template
     * @return string
     **/
    protected function handleElse($template)
    {
        $pattern = '/\{\{\s*(else)\s*\}\}/';

        return preg_replace($pattern, '<?php else : ?>', $template);
    }

    /**
     * Handle the Template Breadcrumb
     *
     * @param string $template
     * @return string
     **/
    protected function handleBreadcrumb($template)
    {
        $pattern = '/\{\{\sbreadcrumb\s\}\}/';
        $replace = '
            <?php foreach($breadcrumbs as $name => $url) : ?>
                <?php if(! is_null($url)) : ?>
                    <a href="<?php echo $url ?>" ><?php echo $name ?></a>
                <?php else : ?>
                    <span><?php echo $name; ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        ';

        return preg_replace($pattern, $replace, $template);
    }

    /**
     * Handle the ViewData::make()
     *
     * @param string $template
     * @return string
     **/
    protected function handlerMaker($template)
    {
        $pattern = '/\{\{\s*make:(\w+)\s(.*)\s*\}\}/';

        $callback = function($match) {
            $name = $match[1];
            $params = $match[2];

            if ('' == $params) return '<?php echo ViewData::make("'.$name.'"); ?>';

            $pattern = '/(.*?)\s*=\s*(\'|")(|&#?\w+;)(.*?)(\'|")/s';

            preg_match_all($pattern, $params, $m);
            $parameters = array();

            foreach ($m[1] as $k => $v) {
                $v = ltrim($v, ' ');
                if (isset($m[4][$k])) {
                    if (!in_array($m[4][$k], array('true', 'false', 'null', 'array()'))) {
                        $parameters[$v] = "'".$v."'=>".'"'.$m[4][$k].'"';
                    } else {
                        $parameters[$v] = "'".$v."'=>".$m[4][$k];
                    }
                } else {
                    $parameters[$v] = "'".$v."'=>"."''";
                }
            }
            $ps = implode(', ', $parameters);

            return '<?php echo ViewData::make("'.$name.'", array('.$ps.')); ?>';
        };

        return preg_replace_callback($pattern, $callback, $template);
    }

    /**
     * Handle the PHP Comments
     *
     * @param string $template
     * @return string
     **/
    protected function handleComment($template)
    {
        $template = $this->handleCommentSingleLine($template);

        return $this->handleCommentBlock($template);
    }

    /**
     * Handle the PHP comment single line
     *
     * @param string $template
     * @return string
     **/
    protected function handleCommentSingleLine($template)
    {
        $pattern = '/\{#\s*(.+?)\s#*}/';

        return preg_replace($pattern, '<?php // $1 ?>', $template);
    }

    /**
     * Handle the PHP comment block
     *
     * @param string $template
     * @return string
     **/
    protected function handleCommentBlock($template)
    {
        $pattern = '/\{##\s*((.|\s)*?)\s*##\}/';

        return preg_replace($pattern, '<?php /*$1*/ ?>', $template);
    }

    /**
     * Handle the code block variable
     *
     * @param string $template
     * @return string
     **/
    protected function handleCodeBlock($template)
    {
        $pattern[] = '/\{=\s*([\s\S]*?)\s*=\}/';
        $pattern[]= '/\{@\s*([\s\S]*?)\s*@\}/';

        $replace[] = "<?php\n\r $1 \n\r?>";
        $replace[] = "<?php\n\r $1 \n\r?>";

        return preg_replace($pattern, $replace, $template);
    }

    /**
     * Handle the variable echo
     *
     * @param string $template
     * @return string
     **/
    protected function handleEcho($template)
    {
        // Echo variable pattern for normal condition
        $normalPattern = '/\{\{\s*(.+?)\s*\}\}/';

        return preg_replace($normalPattern, '<?php echo $1; ?>', $template);
    }

} // END class Parser
