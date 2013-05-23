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
            'action'        => 'handleAction',
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
            'Date'          => 'handleDateFormat',
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


    public function parseString($template)
    {
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
     * Handler the call the aother action.
     *
     * @param string $template
     * @return string
     **/
    public function handleAction($template)
    {
        $pattern = '/\{\{\s*(action):(.*)\s*\}\}/';

        return preg_replace($pattern, '<?php echo $this->callAction("$2"); ?>', $template);
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
        $pattern = '/\{=\s*([\s\S]*?)\s*=\}/';

        return preg_replace($pattern, "<?php\n\r $1 \n\r?>", $template);
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    protected function handleDateFormat($template)
    {
        $pattern = '/\{\{\s*(Date)(.*)\s*\}\}/';

        $callback = function($match) {
            if (true == strpos($match[2], 'is')) {
                list($var, $format) = explode(' as ', trim($match[2], ' '));
                $var = '"'.substr($var, 3).'"';
                $format = trim(trim($format, "'"), '"');
                return '<?php echo rbDate('.$var.', "'.$format.'", true); ?>';
            } else {
                list($var, $format) = explode(' as ', trim($match[2], ' '));
                $format = trim(trim($format, "'"), '"');
                return '<?php echo rbDate('.$var.', "'.$format.'"); ?>';
            }
        };

        return preg_replace_callback($pattern, $callback, $template);
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
