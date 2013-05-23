<?php

namespace Reborn\Cores;

/**
 * Profiler Class for Reborn
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class Profiler
{

    public static function getTime()
    {
        $time = number_format((microtime(true) - REBORN_START_TIME) * 1000, 4)."ms";

        return $time;
    }

    public static function getMemory()
    {
        $mem = memory_get_peak_usage() - REBORN_START_MEMORY;
        $mem = (round($mem / pow(1024, 2), 3)."MB");

        return $mem;
    }

} // END class Profiler
