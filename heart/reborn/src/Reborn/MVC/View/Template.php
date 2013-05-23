<?php

namespace Reborn\MVC\View;

use Reborn\Cores\Registry;
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
     * Variable for WYSIWYG use or not at admin panel view
     *
     * @var string
     **/
    protected $wysiwyg = false;

    /**
     * Variable for the View Object
     *
     * @var \Reborn\MVC\View\View
     **/
    protected $view;

    /**
     * Variable for the View Object
     *
     * @var \Reborn\MVC\View\Theme
     **/
    protected $theme;

    /**
     * Variable for the Asset Object
     *
     * @var \Reborn\MVC\View\Asset
     **/
    protected $asset;

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
    protected $styles = array();

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
    protected $scripts = array();

    /**
     * Inline Script collection for the template
     *
     * @var array
     **/
    protected $inlineScripts = array();

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

        $this->asset = new Asset($this->theme->getThemePath());

        $this->path = $this->theme->getThemePath().'views'.DS;

        return $this;
    }

    /**
     * Use Wysiwyg editor
     *
     * @return void
     **/
    public function useWysiwyg()
    {
        $this->wysiwyg = true;
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
     * Set the partial for the template
     *
     * @param string $partial
     * @param array $data
     * @return \Reborn\MVC\View\Template
     **/
    public function setPartial($partial, $data = array())
    {
        $partial = str_replace(array('\\', '/'), DS, $partial);
        $this->partial = $partial;

        if (! empty($data)) {
            $this->set($data);
        }

        return $this;
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
            $this->styles[$place][] = $this->asset->css($file, 'all', $module);
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
            $this->scripts[$place][] = $this->asset->js($file, $module);
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

        if($this->useLayout)
        {
            $this->setLayoutVariables();

            $this->view->set($this->defaultKeys['body'], $mainContent);

            $mainContent = $this->layoutRender();
        }

        return $mainContent;
    }

    /**
     * Render the 404 Template
     *
     * @param string $file
     * @return string
     */
    public function render404()
    {
        if (file_exists($this->path.$this->layout404.$this->ext)) {
            $file = $this->path.$this->layout404.$this->ext;
        } else {
            $file = APP.'views'.DS.'404.php';
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

        $filename = $file.$this->ext;

        $modPath = $this->getModulePath($module);

        if (file_exists($this->path.strtolower($module).DS.$filename)) {
            $file = $this->path.$module.DS.$filename;
        } elseif (file_exists($modPath.$filename)) {
            $file = $modPath.$filename;
        } elseif(file_exists($this->path.$this->partialFolder.DS.$filename)) {
            $file = $this->path.$this->partialFolder.DS.$filename;
        } else {
            throw new FileNotFoundException($filename, 'Relative');
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
        $headStyle = $this->getStyleString('header');
        $headStyleInline = $this->getInlineStyleString('header');
        $footerStyle = $this->getStyleString('footer');
        $footerStyleInline = $this->getInlineStyleString('footer');
        $headScript = $this->getScriptString('header');
        $headScriptInline = $this->getInlineScriptString('header');
        $footerScript = $this->getScriptString('footer');
        $footerScriptInline = $this->getInlineScriptString('footer');
        $metadata = $this->getMetadataString();

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
                $this->defaultKeys['breadcrumb'] => $this->breadcrumb,
                'wysiwyg' => $this->wysiwyg
            );
        $this->view->set($data);
    }

    /**
     * Get the Script string from the script array from template.
     *
     * @param string $place
     * @return string
     **/
    protected function getScriptString($place)
    {
        if (isset($this->scripts[$place])) {
            if (count($this->scripts[$place]) == 1) {
                return "\n\t\t".$this->scripts[$place][0]."\n";
            }

            return "\n\t\t".implode("\n\t\t", $this->scripts[$place])."\n";
        }

        return null;
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
     * Get the Stylesheet string from the style array from template.
     *
     * @param string $place
     * @return string
     **/
    protected function getStyleString($place)
    {
        if (isset($this->styles[$place])) {
            if (count($this->styles[$place]) == 1) {
                return "\n\t\t".$this->styles[$place][0]."\n";
            }
            return "\n\t\t".implode("\n\t\t", $this->styles[$place])."\n";
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
        $request = Registry::get('app')->request;

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
        $mod = Module::getData($module);

        return $mod['path'].'views'.DS;
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
