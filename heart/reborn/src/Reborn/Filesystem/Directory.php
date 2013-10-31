<?php

namespace Reborn\Filesystem;

/**
 * Directory Class for Reborn
 *
 * @package Reborn\File
 * @author Myanmar Links Professional Web Development Team
 **/

class Directory
{

    /**
     * Get the data form the given directory.
     * This method is equal with php's glob() function.
     *
     * @param string $path Path for the dir
     * @param int $flag Detail at php's glob().
     * @return void
     **/
    public static function get($path, $flag = 0)
    {
        $path = trim(str_replace(array('\\','/'), DS, $path));

        return glob($path, $flag);
    }

    /**
     * Check the given path is directory or not.
     * This method is equal with php's is_dir() function.
     *
     * @param string $path
     * @return boolean
     **/
    public static function is($path)
    {
        return is_dir($path);
    }

    /**
     * Make the new directory.
     * This method is equal with php's mkdir() function.
     *
     * @param string $path
     * @param int $mode Default mode is 0777
     * @param boolean $recursive Allows the creation of nested directories.
     * @return boolean
     **/
    public static function make($path, $mode = 0777, $recursive = false)
    {
        return mkdir($path, $mode, $recursive);
    }

    /**
     * Copy the Directory.
     *
     * @param string $path Original Folder path
     * @param string $dest Destination path
     * @return boolean
     **/
    public static function copy($path, $dest)
    {
        if (static::is($path)) {

            // Make Directory if doesn't exit dest folder
            if (!static::is($dest)) {
                static::make($dest);
            }

            $files = scandir($path);

            if (sizeof($files) > 0) {
                foreach ($files as $file ) {

                    // Skip file is "." or ".."
                    if ( $file == "." || $file == ".." ) {
                        continue;
                    }

                    // $file is directory, make copy for recursive
                    if ( is_dir( $path.DS.$file ) ) {
                        static::copy( $path.DS.$file, $dest.DS.$file );
                    } else {
                        copy( $path.DS.$file, $dest.DS.$file );
                    }
                }
            }
            return true;
        } elseif (is_file($path)) {
            return copy($path, $dest);
        }

        return false;
    }

    /**
     * Delete the given directory.
     * This method is equal with php's rmdir() function.
     * But this method is support recursive delete from the given folder.
     *
     * @param string $dirpath
     * @param boolean $remove_this Remove this given folder
     * @return boolean
     **/
    public static function delete($dirpath, $remove_this = true, $skips = array())
    {
        $path = rtrim(str_replace(array('\\','/'), DS, $dirpath), DS).DS;

        if (static::is($path)) {
            $iterator = new \DirectoryIterator($path);

            foreach ($iterator as $dir) {

                $name = $dir->getFilename();

                if (in_array($name, $skips)) continue;

                if (!$dir->isDot() and !$dir->isDir()) {
                    File::delete($dir->getRealPath());
                } elseif(!$dir->isDot() and $dir->isDir()) {
                    static::delete($dir->getRealPath());
                }
            }

            if ($remove_this) {
                @chmod($path, 0777);
                return @rmdir($path);
            }
            return true;
        } else {
            return false;
        }
    }

} // END class Directory
