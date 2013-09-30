<?php

namespace Reborn\Cores;

use Reborn\MVC\View\View;

/**
 * Profiler Class for Reborn
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class Profiler
{

    /**
     * Profiler lists array
     *
     * @var array
     **/
    protected $profilers = array();

    /**
     * Start the Profiling.
     *
     * @param string $name Profiler name
     * @return void
     **/
    public function start($name = 'Reborn CMS Application')
    {
        $this->profilers[$name]['time']['start'] = $this->startTimer($name);
        $this->profilers[$name]['memory']['start'] = $this->startMemory($name);
    }

    /**
     * Stop the Profiling
     *
     * @param string $name Profiler name
     * @return void
     **/
    public function stop($name = 'Reborn CMS Application')
    {
        $this->profilers[$name]['time']['end'] = $this->stopTimer();
        $this->profilers[$name]['memory']['end'] = $this->stopMemory($name);
    }

    /**
     * Output the Profiler View
     *
     * @param string $name Profiler name
     * @return string
     **/
    public function output($content)
    {
        $app = Registry::get('app');
        if (('dev' == $app['env']) and \Config::get('dev.profiler')) {
            $data = array();

            foreach ($this->profilers as $name => $p) {
                $data['time'][$name] = $this->totalTime($name);
                $data['memory'][$name] = $this->totalMemory($name);
            }

            $data['querys'] = \DB::getQueryLog();
            $data['total_querys'] = count($data['querys']);
            //$data['log'] = $this->getLog();
            $data['files'] = get_included_files();
            $data['total_files'] = count($data['files']);
            $data['get'] = $_GET;
            $data['post'] = $_POST;

            $request = $app->request;
            $data['request']['URL'] = \Uri::current();
            $data['request']['URI'] = \Uri::uriString();
            $data['request']['Module'] = $request->module;
            $data['request']['Controller'] = $request->controller;
            $data['request']['Action'] = $request->action;
            $data['request']['Params'] = implode(', ', $request->params);

            $view = new View();
            $view->set($data);

            $profile = $view->render(APP.'views'.DS.'profiler'.DS.'index.php', $data);

            $content = str_replace('</body>', $profile, $content);
        }

        return $content;
    }

    /**
     * Get the Total Time for profiler name
     *
     * @param string $name Profiler name
     * @return string
     **/
    public function totalTime($name = 'Reborn CMS Application')
    {
        if (isset($this->profilers[$name])) {
            $diff = $this->profilers[$name]['time']['end'] - $this->profilers[$name]['time']['start'];
            $time = number_format($diff * 1000, 4)."ms";
        } else {
            $time = 'We have not timer for '.$name;
        }

        return $time;
    }

    /**
     * Get total memory for profiler name
     *
     * @param string $name Profiler name
     * @return string
     **/
    public function totalMemory($name = 'Reborn CMS Application')
    {
        if (isset($this->profilers[$name])) {
            $diff = $this->profilers[$name]['memory']['end'] - $this->profilers[$name]['memory']['start'];
            $mem = (round($diff / pow(1024, 2), 3)."MB");
        } else {
            $mem = 'We have not memory watcher for '.$name;
        }

        return $mem;
    }

    /**
     * Get Start time from profiler name
     *
     * @param string $name Profiler name
     * @return string
     **/
    protected function startTimer($name)
    {
        if ('Reborn CMS Application' == $name) {
            return REBORN_START_TIME;
        }

        return microtime(true);
    }

    /**
     * Get Stop time from profiler name
     *
     * @return string
     **/
    protected function stopTimer()
    {
        return microtime(true);
    }

    /**
     * Get Start memory from profiler name
     *
     * @param string $name Profiler name
     * @return string
     **/
    protected function startMemory($name)
    {
        if ('Reborn CMS Application' == $name) {
            return REBORN_START_MEMORY;
        }

        return memory_get_usage();
    }

    /**
     * Get Stop memory from profiler name
     *
     * @return string
     **/
    protected function stopMemory()
    {
        return memory_get_peak_usage();
    }

} // END class Profiler
