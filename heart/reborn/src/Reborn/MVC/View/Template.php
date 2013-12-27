<?php

namespace Reborn\MVC\View;

use Reborn\Cores\Facade;
use Reborn\Asset\AssetFinder;
use Reborn\Module\ModuleManager as Module;
use Reborn\Exception\FileNotFoundException;

/**
 * Template class for Reborn
 * Knowledge base on Phil Sturgeon's CI Template Class
 *
 * @package Reborn\MVC\View
 * @author Myanmar Links Professional Web Development Team
 **/
class Template
{

    /**
     * Variables for Tempalte Default Var
     *
     * @var array
     */
    protected $defaultKeys = array(
            'title'             => 'layoutTitle',
            'body'              => 'layoutBody',
            'headStyle'         => 'headerStyle',
            'headStyleInline'   => 'headerStyleInline',
            'footerStyle'       => 'footerStyle',
            'footerStyleInline' => 'footerStyleInline',
            'headScript'        => 'headerScript',
            'headScriptInline'  => 'headerScriptInline',
            'footerScript'      => 'footerScript',
            'footerScriptInline'=> 'footerScriptInline',
            'metadata'          => 'metadata',
            'breadcrumb'        => 'breadcrumbs'
        );

    /**
     * View instance
     *
     * @var \Reborn\MVC\View\View
     **/
    protected $view;

    /**
     * Theme instance
     *
     * @var \Reborn\MVC\View\Theme
     **/
    protected $theme;

    /**
     * Partial for current theme's view render
     *
     * @var string
     */
    protected $partial;

    /**
     * Layout for current theme's view render
     *
     * @var string
     */
    protected $layout = "default";

    /**
     * Variable for 404 result page
     *
     * @var string
     */
    protected $layout404 = '404';

    /**
     * Variable for maintain mode page
     *
     * @var string
     **/
    protected $maintain = 'maintain';

    /**
     * Variable for production-error page
     *
     * @var string
     **/
    protected $error = 'production-error';

    /**
     * Extension for the theme file
     *
     * @var string
     */
    protected $ext = '.php';

    /**
     * Path for the current theme's view folder
     *
     * @var string
     */
    protected $path;

    /**
     * Path for the active theme path
     *
     * @var string
     **/
    protected $themePath;

    /**
     * Folder name for the theme partial
     *
     * @var string
     */
    protected $partialFolder = 'partial';

    /**
     * Folder name for the theme layout
     *
     * @var string
     */
    protected $layoutFolder = 'layout';

    /**
     * Boolean for the layout is use ot not when template render.
     * If you use ajax request at your application, you want to reply partial only.
     * Ok. You can set $useLayout is false. Render stage will return partial only.
     *
     * @var boolean
     **/
    protected $useLayout = true;

    /**
     * Title for the layout page.(<title></title>)
     *
     * @var string
     **/
    protected $layoutTitle;

    /**
     * Stylesheet File collection for the template
     *
     * @var array
     **/
    protected $style = array();

    /**
     * Inline Stylesheet collection for the template
     *
     * @var array
     **/
    protected $inlineStyles = array();

    /**
     * Script File collection for the template
     *
     * @var array
     **/
    protected $script = array();

    /**
     * Inline Script collection for the template
     *
     * @var array
     **/
    protected $inlineScripts = array();

    /**
     * JS Variables for the template
     *
     * @var array
     **/
    protected $jsVars = array();

    /**
     * Metadata for the template <head></head>
     *
     * @var array
     **/
    protected $metadata = array();

    /**
     * Variable for the breadcrumbs
     *
     * @var array
     **/
    protected $breadcrumb = array();

    /**
     * Default constructor method.
     *
     * @param array $options Options array for the Template
     * @return \Reborn\MVC\View\Template
     */
    public function __construct(Theme $theme, View $view, $ext)
    {
        $this->view = $view;

        $this->theme = $theme;

        $this->ext = $ext;

        $this->path = $this->theme->getThemePath().'views'.DS;

        return $this;
    }

    /**
     * Get the template full path with active theme
     *
     * @return string
     **/
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the view file extension
     *
     * @return string
     **/
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Get the theme's partial path
     *
     * @return string
     **/
    public function getPartialPath()
    {
        return $this->path.$this->partialFolder.DS;
    }

    /**
     * Find template file form the given path.
     *
     * @param string $file Template file name
     * @param string $type Template type. Default is partial
     * @return string|boolean
     */
    public function findFile($file, $type = 'partial')
    {
        if ($type == 'partial') {
            $folder = $this->partialFolder;
        } elseif ($type == 'layout') {
            $folder = $this->layoutFolder;
        } else {
            $folder = $type;
        }

        $file = $file.$this->ext;

        if (file_exists($this->path.$folder.DS.$file)) {
            return $this->path.$folder.DS.$file;
        }

        return false;
    }

    /**
     * Set the layout for the template
     *
     * @param string $layout Layout name without extension
     * @return \Reborn\MVC\View\Template
     **/
    public function setLayout($layout)
    {
        $layout = str_replace(array('\\', '/'), DS, $layout);

        $this->layout = $layout;

        return $this;
    }

    /**
     * Set the partial view for the template
     *
     * @param string $partial
     * @param array $data
     * @return \Reborn\MVC\View\Template
     **/
    public function view($partial, $data = array())
    {
        $partial = str_replace(array('\\', '/'), DS, $partial);
        $this->partial = $partial;

        if (! empty($data)) {
            $this->set($data);
        }

        return $this;
    }

    /**
     * Alias of view method
     **/
    public function setPartial($partial, $data = array())
    {
        return $this->view($partial, $data);
    }

    /**
     * Set the data for the template.
     *
     * @param string $key
     * @param mixed $value
     * @return \Reborn\MVC\View\Template
     **/
    public function set($key, $value = null)
    {
        $this->view->set($key, $value);

        return $this;
    }

    /**
     * Set the Page title for the view
     *
     * @param string $title
     * @return \Reborn\MVC\View\Template
     **/
    public function title($title)
    {
        $this->layoutTitle = $title;

        return $this;
    }

    /**
     * Set the stylesheet file for the template
     *
     * @param string|array $files Stylesheet files array or single file
     * @param string $module If asset file from module, set the module name
     * @param string $place Place for the stylesheet area.(header and footer)
     * @return \Reborn\MVC\View\Template
     **/
    public function style($files = array(), $module = null, $place = 'header')
    {
        if(! is_array($files))
        {
            $files = (array) $files;
        }

        foreach($files as $file)
        {
            $this->style[$place][] = array('file' => $file, 'module' => $module);
        }

        return $this;
    }

    /**
     * Set the inline stylesheet code for the template
     *
     * @param string $styles Style Code String
     * @param string $place Place for the styles code area.(header and footer)
     * @return \Reborn\MVC\View\Template
     **/
    public function inlineStyle($styles, $place = 'header')
    {
        $this->inlineStyles[$place][] = $styles;

        return $this;
    }

    /**
     * Set the script file for the template
     *
     * @param string|array $files Script files array or single file
     * @param string $module If asset file from module, set the module name
     * @param string $place Place for the script area.(header and footer)
     * @return \Reborn\MVC\View\Template
     **/
    public function script($files = array(), $module = null, $place = 'header')
    {
        if(! is_array($files))
        {
            $files = (array) $files;
        }

        foreach($files as $file)
        {
            $this->script[$place][] = array('file' => $file, 'module' => $module);
        }

        return $this;
    }

    /**
     * Set the inline script code for the template
     *
     * @param string $scripts Scripts Code String
     * @param string $place Place for the scripts code area.(header and footer)
     * @return \Reborn\MVC\View\Template
     **/
    public function inlineScript($scripts, $place = 'header')
    {
        $this->inlineScripts[$place][] = $scripts;

        return $this;
    }

    /**
     * Set JS variables for the template
     *
     * @param string|array $key JS variable key or key value array
     * @param mixed|null $value Value for JS variable
     * @return \Reborn\MVC\View\Template
     **/
    public function jsValue($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $this->jsVars[$name] = $value;
            }
        } else {
            $this->jsVars[$key] = $value;
        }

        return $this;
    }

    /**
     * Set the metadata for the template
     * Metadata type (default is "meta", support type [og, twitter, link])
     *
     * @param string $name Metadata name
     * @param string $content Metadata content
     * @param string $type Metadata type
     * @return \Reborn\MVC\View\Template
     **/
    public function metadata($name, $content, $type = 'meta')
    {
        $name = htmlspecialchars(strip_tags($name));
        $content = htmlspecialchars(strip_tags($content));
        if ($name == 'keywords' AND ! strpos($content, ','))
        {
            $content = preg_replace('/[\s]+/', ', ', trim($content));
        }

        switch($type)
        {
            case 'meta':
            case 'twitter':
                $this->metadata[$name] = '<meta name="'.$name.'" content="'.$content.'" />';
            break;

            case 'og':
                $this->metadata[$name] = '<meta property="'.$name.'" content="'.$content.'" />';
            break;

            case 'link':
                $this->metadata[$name] = '<link rel="'.$name.'" href="'.$content.'" />';
            break;
        }

        return $this;
    }

    /**
     * Set the breadcrumb for the template.
     *
     * @param string $label Breadcrumb name
     * @param string $uri URI for the breadcrumb
     * @return \Reborn\MVC\View\Template
     **/
    public function breadcrumb($label, $uri = null)
    {
        $this->breadcrumb[$label] = $uri;
        return $this;
    }

    /**
     * This method is set the useLayout is false.
     * Return partial only when render the template.
     *
     * @return \Reborn\MVC\View\Template
     **/
    public function partialOnly()
    {
        $this->useLayout = false;
        return $this;
    }

    /**
     * Render the template
     *
     * @return string
     **/
    public function render()
    {
        if (!is_null($this->partial)) {
            $mainContent = $this->partialRender();
        } else {
            $mainContent = '';
        }

        if($this->useLayout) {

            $this->setLayoutVariables();

            $this->view->set($this->defaultKeys['body'], $mainContent);

            $mainContent = $this->layoutRender();
        }

        return $mainContent;
    }

    /**
     * Render the 404 Template
     *
     * @param string|null $message Message for 404 View
     * @return string
     */
    public function render404($message = null)
    {
        if (file_exists($this->path.$this->layout404.$this->ext)) {
            $file = $this->path.$this->layout404.$this->ext;
        } else {
            $file = APP.'views'.DS.'404.php';
        }

        $this->view->set('clueless', $message);

        return $this->view->render($file);
    }

    /**
     * Render the Maintain Mode Template
     *
     * @param string|null $message Message for maintain View
     * @return string
     */
    public function renderMaintain($message = null)
    {
        if (file_exists($this->path.$this->maintain.$this->ext)) {
            $file = $this->path.$this->maintain.$this->ext;
        } else {
            $file = APP.'views'.DS.$this->maintain.'.php';
        }

        $this->view->set('maintain', $message);

        return $this->view->render($file);
    }

    /**
     * Render the Production Error Template
     *
     * @return string
     */
    public function renderProductionError()
    {
        if (file_exists($this->path.$this->error.$this->ext)) {
            $file = $this->path.$this->error.$this->ext;
        } else {
            $file = APP.'views'.DS.$this->error.'.php';
        }

        return $this->view->render($file);
    }

    /**
     * Render the partial view
     *
     * @return string
     **/
    public function partialRender($partial = null)
    {
        if (is_null($partial)) {
            $partial = $this->partial;
        }

        if (false !== strpos($partial, "::")) {
            list($module, $file) = $this->keyParse($partial);
        } else {
            $file = $partial;
            $module = $this->getModule();
        }

        $file = str_replace(array('\\', '/', '.'), DS, $file);

        $filename = $file.$this->ext;

        $modPath = $this->getModulePath($module);

        $modFormTheme = strtolower($module);
        if (file_exists($this->path.$modFormTheme.DS.$filename)) {
            $file = $this->path.$modFormTheme.DS.$filename;
        } elseif (file_exists($modPath.$filename)) {
            $file = $modPath.$filename;
        } elseif(file_exists($this->path.$this->partialFolder.DS.$filename)) {
            $file = $this->path.$this->partialFolder.DS.$filename;
        } else {
            throw new FileNotFoundException($filename, 'Relative');
        }

        // Assign js values for partialOnly view rendering
        if (! empty($this->jsVars)) {
            if (! $this->useLayout) {
                $this->view->set('js_var', $this->compileJsVars(''));
            }
        }

        return $this->view->render($file);
    }

    /**
     * Render the layout view
     *
     * @return string
     **/
    protected function layoutRender()
    {
        $file = $this->path.$this->layoutFolder.DS.$this->layout.$this->ext;

        if (file_exists($file)) {
            return $this->view->render($file);
        } else {
            throw new FileNotFoundException($this->layout.$this->ext, $this->path.$this->layoutFolder);
        }
    }

    /**
     * Set the deafult varialbe for the template
     *
     * @return void
     **/
    protected function setLayoutVariables()
    {
        $headStyle = $this->getAssetTag('header', 'style');
        $footerStyle = $this->getAssetTag('footer', 'style');
        $headScript = $this->getAssetTag('header', 'script');
        $footerScript = $this->getAssetTag('footer', 'script');
        $headStyleInline = $this->getInlineStyleString('header');
        $footerStyleInline = $this->getInlineStyleString('footer');
        $headScriptInline = $this->getInlineScriptString('header');
        $footerScriptInline = $this->getInlineScriptString('footer');
        $metadata = $this->getMetadataString();

        // Set JS variables
        if (! empty($this->jsVars) ) {

            $headScript = $this->compileJsVars($headScript);
        }

        $data = array(
                $this->defaultKeys['title'] => $this->layoutTitle,
                $this->defaultKeys['headStyle'] => $headStyle,
                $this->defaultKeys['headStyleInline'] => $headStyleInline,
                $this->defaultKeys['headScript'] => $headScript,
                $this->defaultKeys['headScriptInline'] => $headScriptInline,
                $this->defaultKeys['footerStyle'] => $footerStyle,
                $this->defaultKeys['footerStyleInline'] => $footerStyleInline,
                $this->defaultKeys['footerScript'] => $footerScript,
                $this->defaultKeys['footerScriptInline'] => $footerScriptInline,
                $this->defaultKeys['metadata'] => $metadata,
                $this->defaultKeys['breadcrumb'] => $this->breadcrumb
            );
        $this->view->set($data);
    }

    /**
     * Complie JS variables with header scripts string.
     *
     * @param string $scripts Header Scripts Group String
     * @return string
     **/
    protected function compileJsVars($scripts)
    {
        $vars = "<script>var RB=".json_encode($this->jsVars)."</script>";

        return $vars."\n".$scripts;
    }

    /**
     * Get asset element tag
     *
     * @param string $place
     * @param string $type
     * @return string|null
     **/
    protected function getAssetTag($place, $type)
    {
        $files = isset($this->{$type}[$place]) ? $this->{$type}[$place] : array();

        // Call Event
        $res = \Event::call('reborn.template.'.$type.'.render.'.$place, array($files));

        if(! empty($res[0])) {
            $files = $res[0];
        }

        if (count($files) == 0) {
            return null;
        }

        $url = '';

        foreach ($files as $file) {
            if (is_null($file['module'])) {
                $url .= $file['file'].',';
            } else {
                $url .= $file['module'].'__'.$file['file'].',';
            }
        }

        $base = defined('ADMIN') ? url('assets/a/') : url('assets');

        switch ($type) {
            case 'style':
                $url = $base.'styles/'.rtrim($url, ',');
                return '<link rel="stylesheet" type="text/css" href="'.$url.'">'."\n";
                break;

            case 'script':
                $url = $base.'scripts/'.rtrim($url, ',');
                return '<script type="text/javascript" src="'.$url.'"></script>'."\n";
                break;

            default:
                return null;
                break;
        }
    }

    /**
     * Get the Script string from the inlineScripts array from template.
     *
     * @param string $place
     * @return string
     **/
    protected function getInlineScriptString($place)
    {
        if (isset($this->inlineScripts[$place])) {
            if (count($this->inlineScripts[$place]) == 1) {
                return "\n\t\t<script type=\"text/javascript\">"
                        .$this->inlineScripts[$place][0]."</script>\n";
            }
            $scripts = "\n\t\t";
            foreach ($this->inlineScripts[$place] as $s) {
                $scripts .= '<script type="text/javascript">';
                $scripts .= $s;
                $scripts .= '</script>'."\n";
            }
            return $scripts;
        }

        return null;
    }

    /**
     * Get the Stylesheet string from the inlineStyles array from template.
     *
     * @param string $place
     * @return string
     **/
    protected function getInlineStyleString($place)
    {
        if (isset($this->inlineStyles[$place])) {
            if (count($this->inlineStyles[$place]) == 1) {
                return "\n\t\t<style type=\"text/css\">"
                        .$this->inlineStyles[$place][0]."</style>\n";
            }
            $styles = "\n\t\t";
            foreach ($this->inlineStyles[$place] as $s) {
                $styles .= '<style type="text/css">';
                $styles .= $s;
                $styles .= '</style>'."\n";
            }
            return $styles;
        }

        return null;
    }

    /**
     * Get the Metadata string from the metadata array from template.
     *
     * @return string
     **/
    protected function getMetadataString()
    {
        if (isset($this->metadata)) {
            return "\n\t\t".implode("\n\t\t", $this->metadata)."\n";
        }

        return null;
    }

    /**
     * Parser the key (eg: blog::view)
     *
     * @param string $key
     * @return array
     **/
    protected function keyParse($key)
    {
        $arr = explode('::', $key);

        return array($arr[0], $arr[1]);
    }

    /**
     * Get the active module from the router
     *
     * @return string
     **/
    protected function getModule()
    {
        $request = Facade::getApplication()->request;

        return $request->module;
    }

    /**
     * Get the views file path for the given moduel.
     *
     * @param string $module
     * @return string
     **/
    protected function getModulePath($module)
    {
        $mod = Module::get($module);

        return $mod->path.DS.'views'.DS;
    }

    /**
     * Magic method for template data setter
     *
     * @param string $key
     * @param mixed $value
     * @return void
     **/
    public function __set($key, $value)
    {
        $this->view->set($key, $value);
    }

} // END class Template
