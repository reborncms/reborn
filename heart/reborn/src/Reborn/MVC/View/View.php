<?php

namespace Reborn\MVC\View;

use ArrayAccess;
use Reborn\Exception\FileNotFoundException;
use Reborn\Config\Config;
use Reborn\Filesystem\File;

/**
 * View Class for Reborn
 *
 * @package Reborn\MVC\View
 * @author Myanmar Links Professional Web Development Team
 **/
class View implements ArrayAccess
{

    /**
     * Data variable for View Class
     *
     * @var array
     **/
    protected $data = array();

    protected $block = array();

    /**
     * Parser Object for the file parsing
     *
     * @var \Reborn\MVC\View\Parser
     */
    protected $parser;

    /**
     * Template Object for the file include
     *
     * @var \Reborn\MVC\View\Template
     */
    protected $template;

    /**
     * Path for the template cache file
     * @var string
     */
    protected $cache;

    /**
     * Constructor Method
     *
     * @return void
     **/
    public function __construct($cachePath = null)
    {
        $configCache = Config::get('template.cache_path');
        $this->cache = is_null($cachePath) ? $configCache : $cachePath;
    }

    /**
     * Set the View Parser.
     *
     * @param Reborn\MVC\View\Parser $parser
     * @return void
     */
    public function setObject(Parser $parser, Template $template)
    {
        $this->parser = $parser;
        $this->template = $template;
    }

    /**
     * Get the Parser Object
     *
     * @return \Reborn\MVC\View\Parser
     **/
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Render the given view file
     *
     * @param string $file
     * @return string
     */
    public function render($file)
    {
        if (file_exists($file)) {
            $data = $this->data;

            if ('html' == pathinfo($file, PATHINFO_EXTENSION)) {
                $file = $this->fileParse($file, $data);
            }

            ob_start();

            extract($data, EXTR_SKIP);

            try {
                include $file;
            } catch (\Exception $e) {
                ob_get_clean(); throw $e;
            }

            $result = ob_get_clean();
            return $result;
        } else {
            throw new FileNotFoundException($file, $this->theme);
        }
    }

    /**
     * Render the String Template
     *
     * @param string $template
     * @param array $data
     * @return string
     **/
    public function renderAsStr($template, $data = array())
    {
        $data = $this->data = array_merge($this->data, $data);

        $contents = $this->parser->parseString($template);

        ob_start();

        extract($data, EXTR_SKIP);

        try {

            eval('?>'.$contents);

        } catch (\Exception $e) {

            ob_get_clean(); throw $e;

        }

        $content = ob_get_clean();

        return $content;
    }

    /**
     * Set the data for the view.
     * If geiven $key is data array, you can set $value is null
     * example:
     * <code>
     *      $data = array(
     *              'name'  => 'Reborn CMS',
     *              'license' => 'MIT',
     *              'team'  => 'Reborn Development Team'
     *          );
     *      $this->view->set($data);
     * </code>
     *
     * @param string $key Key name for the data
     * @param mixed $value Data value for given key name
     * @return \Reborn\Cores\View
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Include the partial file
     *
     * @param string $file Partial file name
     * @return string|null
     **/
    protected function includeFile($file)
    {
        // Trim the space
        $file = trim($file, ' ');
        $file = $this->template->findFile($file);
        if ($file) {
            return $this->render($file);
        } else {
            return null;
        }
    }

    /**
     * Render the Module partial file
     *
     * @param string $file Partial file name
     * @return string|null
     **/
    protected function partialFile($file)
    {
        // Trim the space
        $file = trim($file, ' ');
        return $this->template->partialRender($file);
    }

    /**
     * This method will make inner call the view from the module action.
     *
     * example : View file is blog single view.
     * We need to call comment list action from the Comment Moduel
     * to show comments of this post.
     *
     * {{ action:comment/list/$post->id }}
     * // will return the comment lists for this post id
     *
     * @param string $uri Uri for the request action
     * @return string|false
     **/
    protected function callAction($uri)
    {
        $uri = rtrim($uri, ' ');
        $module = explode('/', $uri);

        $request = \Registry::get('app')->request;
        $request->setInner();

        \Uri::initialize(\Request::create($uri));

        $response = \Registry::get('app')->router->dispatch();

        return $response->getContent();
    }

    /**
     * Parser the given file to php file and cache this file.
     *
     * @param string $file View file with full path
     * @param array $data
     * @return string
     */
    protected function fileParse($file, $data)
    {
        $cacheFile = $this->cache.md5($file).'.php';

        if ($cfile = $this->cacheFileCheck($cacheFile, $file)) {
            return $cfile;
        }

        $fileData = File::getContent($file);
        $header = "<?php if(!class_exists('Reborn\MVC\View\View')) exit(); ?>\n";
        $filename = '<?php /* '.str_replace(BASE, '..'.DS, $file)." */ ?>\n";
        $content = $this->parser->parse($fileData, $data);

        File::write($this->cache, md5($file).'.php', $header.$filename.$content);

        return $cacheFile;
    }

    /**
     * Check the cache file is valid.
     * 1) Check the file is exists or not.
     * 2) Check the original file is modified after caching or not.
     *
     * @param string $cacheFile Full path of cache file
     * @param string $file Full path of original template file
     * @return string|boolean
     */
    protected function cacheFileCheck($cacheFile, $file)
    {
        if (file_exists($cacheFile)) {
            if($this->isModified($cacheFile, $file)) {
                File::delete($cacheFile);
                return false;
            }
            return $cacheFile;
        }

        return false;
    }

    /**
     * Check origianl template file is modified or not.
     *
     * @param string $cacheFile Full path of cache file
     * @param string $file Full path of original template file
     * @return boolean
     */
    protected function isModified($cacheFile, $file)
    {
        $time = filemtime($file);
        $cacheTime = filemtime($cacheFile);

        return $time > $cacheTime;
    }

    /**
     * Magic method for setter
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Magic method for getter
     */
    public function __get($key)
    {
        return $this->data[$key];
    }

    /**
     * Magic method toString
     */
    public function __toString()
    {
        $this->render();
    }

    /**
     * Set the value to the data array with given key name
     *
     * @param string $key
     * @param mixed $value
     * @return void
     **/
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Get the value from the data array with given key
     *
     * @param string $key
     * @return mixed
     **/
    public function offsetGet($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Check key is exists or not in the data array
     *
     * @param string $key
     * @return boolean
     **/
    public function offsetExists($key)
    {
        return isset($this->data[$key]) ? true : false;
    }

    /**
     * Unset the key from the data array
     *
     * @param string $key
     * @return void
     **/
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

} // END class View
