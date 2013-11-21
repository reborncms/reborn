<?php

namespace Reborn\Filesystem;

/**
 * File Class for Reborn
 *
 * @package Reborn\File
 * @author Myanmar Links Professional Web Development Team
 **/

class FileException extends \Exception {};

class IOException extends \Exception {};

class File
{

    /**
     * Check the file is exists or not.
     * This method is equal with php's file_exists() function.
     *
     * @param string $file
     * @return boolean
     **/
    public static function is($file)
    {
        return file_exists($file);
    }

    /**
     * Get Content from Given File (file_get_contents)
     *
     * @param string $path File's path
     * @return string
     */
    public static function getContent($path)
    {
        if (! file_exists($path)) {
            throw new FileException(sprintf("File not found at \"%s\"", $path));
        }

        return file_get_contents($path);
    }

    /**
     * Get content from Remote Host
     *
     * @param array $path File's path
     * @return mixed
     */
    public static function getFromRemote($path)
    {
        return file_get_contents($path);
    }

    /**
     * Write new file with given contents.
     * Note : If file is doesn't already in given path, auto create this file.
     *
     * @param string $path Path of file locate
     * @param string $filename File name to save or create.
     * @param string $content Contents for file.
     * @return void
     */
    public static function write($path, $filename, $content = null)
    {
        $path = rtrim(str_replace(array('\\','/'), DS, $path), DS);

        $file = $path.DS.$filename;

        $path = rtrim($path, DS);

        if (file_exists($file)) {
            $handle = fopen($path.DS.$filename, "w");
        } else {
            $handle = fopen($path.DS.$filename, "c");
        }

        if (! $handle) {
            throw new IOException(sprintf("Cannot open file '%s'", $file));
        }

        if (! fwrite($handle, $content)) {
            throw new IOException(sprintf("Cannot write to file '%s'", $file));
        }

        fclose($handle);
    }

    /**
     * Put the new contents to a file.
     * If you want to append th data to a file, set true the $append
     * [third parameter]. Default is false for this.
     * See details at php's file_put_contents()
     *
     * @param string $path File's path
     * @param string $data Data content to put a file
     * @param boolean $append Default is false.
     * @return mixed
     */
    public static function put($path, $data, $append = false)
    {
        if ($append) {
            return file_put_contents($path, $data, FILE_APPEND | LOCK_EX);
        }

        return file_put_contents($path, $data, LOCK_EX);
    }

    /**
     * Delete the given file.
     *
     * @param string $path File path
     * @return boolean
     */
    public static function delete($path)
    {
        if (! file_exists($path)) return false;

        @chmod($path, 0777);

        return @unlink($path);
    }

} // END class File
