<?php

namespace Reborn\Util;

use DateTimeZone;
use Reborn\Filesystem\File;
use Reborn\Filesystem\Directory as Dir;

/**
 * Timezone Helper Class
 *
 * @package Reborn\Util
 * @author Myanmar Links Web Development Team
 **/
class Timezone
{

    protected $regions = array();

    protected $cache_path;

    public function __construct($cache_path = null)
    {
        if (is_null($cache_path)) {
            $this->cache_path = STORAGES.'timezones'.DS;
        } else {
            $this->cache_path = $cache_path;
        }

        $this->regions = array(
            'Africa' => DateTimeZone::AFRICA,
            'America' => DateTimeZone::AMERICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Asia' => DateTimeZone::ASIA,
            'Atlantic' => DateTimeZone::ATLANTIC,
            'Australia' => DateTimeZone::AUSTRALIA,
            'Europe' => DateTimeZone::EUROPE,
            'Indian' => DateTimeZone::INDIAN,
            'Pacific' => DateTimeZone::PACIFIC
        );
    }

    public function lists()
    {
        $lists = array();

        if ($caches = $this->cacheFile()) {
            $results = json_decode($caches);

            foreach ((array) $results as $k => $l) {
                $lists[$k] = (array) $l;
            }

            return $lists;
        }

        foreach ($this->regions as $name => $mask) {
            $all = DateTimeZone::listIdentifiers($mask);

            foreach ($all as $list) {
                $lists[$name][$list] = $list;
            }
        }

        $this->saveCache($lists);

        return $lists;
    }

    public function cacheFile()
    {
        if (File::is($this->cache_path.'cache') ) {
            return File::getContent($this->cache_path.'cache');
        }

        return null;
    }

    public function saveCache($data)
    {
        if (!Dir::is($this->cache_path)) {
            Dir::make($this->cache_path);
        }

        File::put($this->cache_path.'cache', json_encode($data));
    }

} // END class Timezone
