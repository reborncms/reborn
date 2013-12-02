<?php

namespace Reborn\Asset;

use Reborn\Cores\Facade;
use Reborn\Filesystem\Directory as Dir;
use Reborn\Module\ModuleManager as Module;
use Reborn\Exception\FileNotFoundException;

/**
 * Asset Management class for the Reborn
 *
 * @package Reborn\Asset
 * @author Myanmar Links Professional Web Development Team
 **/
class Asset
{
	/**
	 * Variable for the Request object
	 *
	 * @var \Reborn\Cores\Request
	 **/
	protected $request;

	/**
	 * Asset file path for the URL
	 *
	 * @var string
	 **/
	protected $path;

	/**
	 * Asset file path for the File locate
	 *
	 * @var string
	 **/
	protected $realPath;

	/**
	 * Folder name (or) path of the Asset files container
	 *
	 * @var string
	 **/
	protected $assetPath = 'assets';

	/**
	 * Default constructor method
	 *
	 * @param string $path File path for before Asset folder path
	 * @return void
	 **/
	public function __construct($path)
	{
		$this->request = Facade::getApplication()->request;

		$this->realPath = $path.$this->assetPath.DS;

		$path = str_replace(BASE, '', $path);
		$path = str_replace(DS, '/', $path);

		$this->path = $this->request->baseUrl().$path.$this->assetPath.'/';
	}

	/**
	 * Set Asset Folder Name.
	 *
	 * @param string $foldername Asset folder name
	 * @return \Reborn\Asset\Asset
	 **/
	public function setAssetFolder($foldername = 'assets')
	{
		$this->assetPath = $foldername;
		return $this;
	}

	/**
	 * Get the assets folder's URL path by given module or active theme.
	 * example:
	 * <code>
	 * 		// return the active theme's css path
	 * 		// eg: active theme = default
	 * 		// output = http://localhost/reborn/content/themes/default/assets/
	 * 		$this->asset->getAssetPath();
	 * </code>
	 *
	 * @param string|null $module Module name
	 * @return string
	 **/
	public function getAssetPath($module = null)
	{
		return $this->findPath(null, $module);
	}

	/**
	 * Get the css folder's URL path by given module or active theme.
	 * example:
	 * <code>
	 * 		// return the active theme's css path
	 * 		// eg: active theme = default
	 * 		// output = http://localhost/reborn/content/themes/default/assets/css/
	 * 		$this->asset->getCssPath();
	 * </code>
	 *
	 * @param string|null $module Module name
	 * @return string
	 **/
	public function getCssPath($module = null)
	{
		return $this->findPath('css', $module);
	}

	/**
	 * Get the less folder's URL path by given module or active theme.
	 * example:
	 * <code>
	 * 		// return the active theme's less path
	 * 		// eg: active theme = default
	 * 		// output = http://localhost/reborn/content/themes/default/assets/less/
	 * 		$this->asset->getLessPath();
	 * </code>
	 *
	 * @param string|null $module Module name
	 * @return string
	 **/
	public function getLessPath($module = null)
	{
		return $this->findPath('less', $module);
	}

	/**
	 * Get the js folder's URL path by given module or active theme.
	 * example:
	 * <code>
	 * 		// return the active theme's js path
	 * 		// eg: active theme = default
	 * 		// output = http://localhost/reborn/content/themes/default/assets/js/
	 * 		$this->asset->getJsPath();
	 * </code>
	 *
	 * @param string|null $module Module name
	 * @return string
	 **/
	public function getJsPath($module = null)
	{
		return $this->findPath('js', $module);
	}

	/**
	 * Get the img folder's URL path by given module or active theme.
	 * example:
	 * <code>
	 * 		// return the active theme's img path
	 * 		// eg: active theme = default
	 * 		// output = http://localhost/reborn/content/themes/default/assets/img/
	 * 		$this->asset->getImgPath();
	 * </code>
	 *
	 * @param string|null $module Module name
	 * @return string
	 **/
	public function getImgPath($module = null)
	{
		return $this->findPath('img', $module);
	}

	/**
	 * Get the CSS(stylesheet) file by given filename from module or active theme.
	 *
	 * @param string $file Stylesheet file name with extension
	 * @param string $media Media type for stylesheet tag
	 * @param string|null $module If you want file from module, set the module name.
	 * @return string
	 **/
	public function css($file, $media = "all", $module = null)
	{
		$url = $this->find('css', $file, $module);
		if (is_null($url)) return false;

		return '<link rel="stylesheet" type="text/css" href="'.$url.'" media="'.$media.'">';
	}

	/**
	 * Get the LeSS(stylesheet) file by given filename from module or active theme.
	 *
	 * @param string $file Stylesheet file name with extension
	 * @param string $media Media type for stylesheet tag
	 * @param string|null $module If you want file from module, set the module name.
	 * @return string
	 **/
	public function less($file, $media = "all", $module = null)
	{
		if (!is_null($module)) {
			list($real, $url) = $this->getModuelAssetPath($module);

			$filePath = $real.'less'.DS;
		} else {
			$filePath = $this->realPath.'less'.DS;
		}

		if (!file_exists($lessfile = $filePath.$file)) {
			return null;
		}

		$resolver = new LessResolver();

		$solvedUrl = $resolver->solve($lessfile);

		return '<link rel="stylesheet" type="text/css" href="'.$solvedUrl.'" media="'.$media.'">';
	}

	/**
	 * Get the JS(script) file by given filename from module or active theme.
	 *
	 * @param string $file Script file name with extension
	 * @param string|null $module If you want file from module, set the module name.
	 * @return string
	 **/
	public function js($file, $module = null)
	{
		$url = $this->find('js', $file, $module);
		if (is_null($url)) return false;

		return '<script type="text/javascript" src="'.$url.'"></script>';
	}

	/**
	 * Get the image file with <img> tag by given filename from module or active theme.
	 * example:
	 * <code>
	 * 		$attr = array('id' => 'pic', 'title' => 'Img Title', 'class' => 'sample');
	 * 		$this->asset->img('test.jpg', 'Test', $attr);
	 * </code>
	 *
	 * @param string $file Image file name with extension
	 * @param string|null $alt Text for the ALT.
	 * @param array $attr Other attribute for img tag (eg: title, id, etc.)
	 * @param string|null $module If you want file from module, set the module name.
	 * @return string
	 **/
	public function img($file, $alt = null, $attr = array(), $module = null)
	{
		return $this->imgRender($file, $alt, $attr, $module, true);
	}

	/**
	 * Get the image file src.
	 *
	 * @param string $file Image file name with extension
	 * @return string
	 **/
	public function imgFile($file)
	{
		return $this->imgRender($file, null, array(), null, false);
	}

	/**
	 * Render the image tag or image src is base on param.
	 * This method is call fromthe $this->img() and $this->imgFile()
	 *
	 * @param string $file Image file name with extension
	 * @param string|null $alt Text for the ALT.
	 * @param string|null $module If you want file from module, set the module name.
	 * @param boolean $useTag Boolean key for the img tag or src only at return.
	 * @return string
	 **/
	protected function imgRender($file, $alt, $attr, $module, $useTag)
	{
		$url = $this->find('img', $file, $module);
		if (is_null($url)) return false;

		if (is_null($alt)) {
			$altArr = explode('.', $file);
			$alt = $altArr[0];
		}

		if ($useTag) {
			$tag = '<img src="'.$url.'" alt="'.$alt.'" ';

			foreach ($attr as $name => $value) {
				$tag .= $name.'="'.$value.'" ';
			}
			$tag .= "/>";

			return $tag;
		}

		return $url;
	}

	/**
	 * Find the asset file in the given path.
	 * If file is cannot find in given path, return the
	 * Throw FileNotFoundException at the 'dev' Environment,
	 * In the other environment (eg: production) return the null.
	 *
	 * @param string $type Asset Type (css, js, img)
	 * @param string $file Asset file name with extension
	 * @param string|null $module Module name for asset file from module
	 * @param boolean $noTime Without file modify time
	 * @return mixed
	 **/
	protected function find($type, $file, $module = null, $noTime = false)
	{
		if (preg_match('/^http/', $file)) {
			return $file;
		}

		if (!is_null($module)) {
			list($real, $url) = $this->getModuelAssetPath($module);

			$path = $real.$type.DS;
			$filePath = $path.$file;
			$urlPath = $url.$type.'/'.$file;
		} else {
			$path = $this->realPath.$type.DS;
			$filePath = $path.$file;
			$urlPath = $this->path.$type.'/'.$file;
		}

		if (file_exists($filePath)) {
			if ($noTime) {
				return $urlPath;
			}

			$mTime = filemtime($filePath);
			return $urlPath.'?'.$mTime;
		}

		// File Not Found Exception Only in Development Stage
		$app = Facade::getApplication();
		if ($app['env'] == 'dev') {
			throw new FileNotFoundException($file, $path);
		}

		return null;
	}

	/**
	 * Get the asset folder path by asset type (css, js, img).
	 *
	 * @param string|null $type Asset type
	 * @param string|null $module Module name
	 * @return string
	 **/
	protected function findPath($type = null, $module = null)
	{
		if (!is_null($module)) {
			list($real, $url) = $this->getModuelAssetPath($module);

			$urlPath = $url;
		} else {
			$urlPath = $this->path;
		}

		if (!is_null($type)) {
			$urlPath = $urlPath.$type.'/';
		}

		return $urlPath;
	}

	/**
	 * Get the asset file path for the given module.
	 *
	 * @param string $module Module name
	 * @return array
	 **/
	protected function getModuelAssetPath($module)
	{
		$mod = Module::get($module);

		$path = is_null($mod) ? '' : $mod->path;

		$urlpath = str_replace(BASE, '', $path);
		$urlpath = str_replace(DS, '/', $urlpath);

		return array(
					$path.DS.$this->assetPath.DS,
					$this->request->baseUrl().$urlpath.'/'.$this->assetPath.'/'
				);
	}

} // END class Asset
