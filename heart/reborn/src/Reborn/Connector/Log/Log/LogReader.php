<?php

namespace Reborn\Connector\Log\Log;

use Reborn\Filesystem\File as FileSystem;

/**
 * Log Reader Class.
 * This class is helper for read the log file from browser.
 *
 *
 * @package Reborn\Connector\Log
 * @author Myanmar Links Professional Web Development Team
 **/
class LogReader
{

    protected $lineSperator = '[] []'; // Default of monolog

    protected $typePrefix = 'rebornCMSLog';

    protected $dateFormat = '/\[(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)\]/';

    protected $type = array('ERROR', 'DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ALERT');

    /**
     * Get the File Lists form given log dir path
     *
     * @param  string $path Log Folder Path
     * @return array
     */
    public function getFileLists($path)
    {
        $fileLists = array();

        $iterator = new \DirectoryIterator($path);

        foreach ($iterator as $i) {
            if (!$i->isDot() and !$i->isDir()) {
                $fileLists[] = $i->getFileName();
            }
        }

        return $fileLists;
    }

    /**
     * Get the Log file data
     *
     * @param  string      $name Name od the log file(with extension)
     * @param  string      $path Log File Path
     * @return array|false
     */
    public function getLogData($name, $path)
    {
        if (is_file($path.$name)) {
            $content = FileSystem::getContent($path.$name);

            $lines = explode($this->lineSperator, $content);

            $data = array();

            foreach ($lines as $line) {
                if (preg_match($this->dateFormat, $line, $matches)) {
                    $date = (substr($line, 0, strlen($matches[0])));
                    $date = str_replace(array('[', ']'), '', $date);

                    $str = trim(substr($line, strlen($matches[0])), ']');
                    $str = str_replace($this->typePrefix.'.', '', $str);

                    if (preg_match('/(\w)+:/', $str, $m)) {
                        $str = substr($str, strlen($m[0])+2);
                        $type = substr($m[0], 0, -1);
                    }

                    $data[] = array('time' => $date, 'message' => $str, 'type' => $type);
                }
            }

            return $data;
        }

        return false;
    }

} // END class LogReader
