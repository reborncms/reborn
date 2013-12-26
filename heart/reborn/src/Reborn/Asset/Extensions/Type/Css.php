<?php

namespace Reborn\Asset\Extensions\Type;

class Css extends \Munee\Asset\Type\Css
{
	/**
     * Fixes relative paths to absolute paths
     * Update at line 25 (Add new rule),
     * line28 (replace '/' with DS)
     *
     * @param $content
     * @param $originalFile
     *
     * @return string
     *
     * @throws CompilationException
     */
    protected function fixRelativeImagePaths($content, $originalFile)
    {
        $regEx = '%(url[\\s]*\\()[\\s\'"]*([^\\)\'"]*)[\\s\'"]*(\\))%';

        $webroot = $this->request->webroot;
        $changedContent = preg_replace_callback($regEx, function ($match) use ($originalFile, $webroot) {
            $filePath = trim($match[2]);
            // Skip conversion if the first character is a '/' since it's already an absolute path
            if ($filePath[0] !== '/' && false === strpos($filePath, '://')) {
                $basePath = SUB_FOLDER  . str_replace($webroot, '', dirname($originalFile));
                $basePathParts = array_reverse(array_filter(explode(DS, $basePath)));

                $numOfRecursiveDirs = substr_count($filePath, '../');

                if ($numOfRecursiveDirs > count($basePathParts)) {
                    throw new CompilationException(
                        'Error in stylesheet <strong>' . $originalFile .
                        '</strong>. The following URL goes above webroot: <strong>' . $filePath .
                        '</strong>'
                    );
                }

                $basePathParts = array_slice($basePathParts, $numOfRecursiveDirs);
                $basePath = implode('/', array_reverse($basePathParts));

                if (! empty($basePath) && $basePath[0] != '/') {
                    $basePath = '/' . $basePath;
                }

                $filePath = $basePath . '/' . $filePath;
                $filePath = str_replace(array('../', './'), '', $filePath);
            }

            return $match[1] . $filePath . $match[3];
        }, $content);

        if (null !== $changedContent) {
            $content = $changedContent;
        }

        return $content;
    }
}
