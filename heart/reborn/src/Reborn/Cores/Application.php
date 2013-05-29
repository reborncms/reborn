<?php

namespace Reborn\Cores;

use Reborn\Http\Request;
use Reborn\Http\Response;
use Reborn\Http\Uri;
use Reborn\Route\Router;
use Reborn\MVC\View\ViewManager;
use Reborn\Config\Config;
use Reborn\Filesystem\File;
use Reborn\Widget\Widget;
use Reborn\Event\EventManager as Event;
use Reborn\Cache\CacheManager as Cache;
use Reborn\Connector\Log\LogManager as Log;
use Reborn\Connector\DB\DBManager as DB;
use Reborn\Module\ModuleManager as Module;
use Reborn\Exception\RbException as RbException;
use Reborn\Exception\HttpNotFoundException as HttpNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Session\Session as Session;

/**
 * Main Application Class for Reborn.
 * This class is extend the Pimple DIC.
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class Application extends \Pimple
{

    /**
     * Marking for application started or not
     *
     * @var bool
     **/
    protected $started = false;

    /**
     * Constructor Method
     * Create new object for Reborn Application
     *
     * @return void
     **/
    public function __construct()
    {
        $this['request'] = Request::createFromGlobals();

        $this['response'] = Response::create(null);

        $this['router'] =  $this->share(function ($this) {
            return new Router();
        });

        $this['map'] = $this->share(function ($this) {
            return new \Reborn\Route\Map();
        });

        $this['log'] = $this->share(function () {
            return new Log();
        });

        $this['view'] = $this->share( function() {
            return new ViewManager();
        });

        $this['session'] = $this->share( function() {
            return new Session();
        });

        $this['cache'] = $this->share( function() {
            return new Cache();
        });

        $this['widget'] = $this->share( function() {
            return new Widget();
        });

        // Set the Application Object into the Registry
        Registry::set('app', $this);
    }

    /**
     * Check Reborn is already installed
     *
     * @return boolean
     **/
    public function installed()
    {
        if(! File::is(APP.'config'.DS.'db.php'))
        {
            if (!\Reborn\Filesystem\Directory::is(BASE.'installer')) {
                throw new RbException("Can't find db.php at config folder");
            }

            return false;
        }

        return true;
    }

    /**
     * Installation for Reborn CMS
     *
     * @return void
     **/
    public function install()
    {
        if(File::is(APP.'config'.DS.'db.php')) {
            throw new RbException("Reborn CMS is already installed!");
        }

        require BASE.'installer'.DS.'Installer.php';

        \Installer::init();

        \Installer::start();

        exit;
    }

    /**
     * Start the application
     *
     * @return void
     **/
    public function start()
    {
        if ($this->started) {
            throw new RbException("Reborn CMS Application is already started!");
        }

        try {
            // Set Exception and Error Handler
            $this->setErrorHandler();

            // call the appInitialize method
            $this->appInitialize();

            // Call the Event Name App Start
            Event::call('reborn.app.starting');

            // Set Timezone for Application
            $this->setTimezone(\Setting::get('timezone'));

            // Check the Site is Maintainance Stage or not
            // If site is maintainance stage, give the maintain page and exit
            $this->siteIsMaintain();

            $response = $this['router']->dispatch();

             // Check Response is JsonResponse
            if ($response instanceof JsonResponse) {
                // Call the Event Name App Ending
                Event::call('reborn.app.ending');

                // Send the Json Content with Headers
                return $response->send();
            }

            if ($response instanceof StreamedResponse) {
                // Call the Event Name App Ending
                Event::call('reborn.app.ending');

                // Send the Stream Content with Headers
                return $response->send();
            }

            if(! $response instanceof Response)
            {
                $response = new Response($response);
            }

            $this->started = true;

            // Send response to the end method
            $this->end($response);
        } catch(HttpNotFoundException $e) {
            $view = new \Reborn\MVC\View\View(Config::get('template.cache_path'));
            $content = $view->render(APP.'views'.DS.'404.php');
            $response = new Response($content, 404);
            $this->end($response);

            exit(1);
        } catch(\Exception $e) {

            if (ENV == 'production') {
                $handler = new ErrorHandler();
                $response = new Response($handler->exceptionHandler($e), 503);
            } else {
                $view = new \Reborn\MVC\View\View(Config::get('template.cache_path'));
                $content = $view->render(APP.'views'.DS.'production-error.php');
                $response = new Response($content, 503);
            }

            $this->end($response);
        }
    }

    /**
     * Start the Initialize method from require classes.
     * But this method is call from application start method only.
     * Don't call more than once.
     *
     */
    public function appInitialize()
    {
        if ($this->started) {
            return true;
        }

        // Start the Session
        if (isset($this['session'])) {
            $this['session']->start();
        }

        // Start the Database initialize
        DB::initialize();

        // Start the Event initialize
        Event::initialize();

        // Start the Setting initialize
        Setting::initialize();

        // Start the Module initialize
        Module::initialize();

        // Load Event from the Enable Modules
        Event::loadFromModules();

        // Start the Uri initialize
        Uri::initialize($this->request);

        // Start the Widget initialize
        $this['widget']->initialize();
    }

    /**
     * End point of application
     *
     * @return void
     **/
    public function end(Response $response)
    {
        $response->prepare($this['request']);

        if(PROFILER) {
            $time = Profiler::getTime();
            $mem = Profiler::getMemory();

            $profiler = "<div id=\"rb_profiler\">";
            $profiler .= "Reborn CSM is rendered in $time and Memory $mem useage";
            $profiler .= "</div>\n</body>";

            $content = $response->getContent();

            $content = str_replace('</body>', $profiler, $content);
        } else {
            $content = $response->getContent();
        }

        $response->setContent($content);

        // Call the Event Name App Ending
        Event::call('reborn.app.ending');

        return $response->send();
    }

    /**
     * Set Locale for application
     * Default locale is en
     *
     * @param string $locale
     * @return void
     **/
    public function setLocale($locale = 'en')
    {
        $this['locale'] = $locale;
    }

    /**
     * Set Locale for application
     * Default locale is en
     *
     * @param string $locale
     * @return void
     **/
    public function setTimezone($tz = 'UTC')
    {
        date_default_timezone_set($tz);
    }

    /**
     * Set the Error Handler
     *
     * @return void
     */
    public function setErrorHandler()
    {
        $handler = new ErrorHandler();
        $handler->register();
    }

    /**
     * Check the site is maintainance stage ot not.
     * If site is maintainance stage, response the maintainance mode.
     *
     * @return void
     **/
    protected function siteIsMaintain()
    {
        $maintain = Setting::get('site_is_maintain');

        if (! $maintain) {
            return false;
        } else {
            $theme = Setting::get('public_theme');
            $file = THEMES.$theme.DS.'views'.DS.'layout'.DS.'maintain'.EXT;
            if (file_exists($file)) {
                $content = File::getContent($file);
            } else {
               $content = File::getContent(APP.'views'.DS.'404.php');
            }

            $response = new Response($content, 503);
            $this->end($response);

            exit(1);
        }
    }

    /**
     * Magic setter method
     *
     * @param string $key
     * @param mixed $value
     * @return void
     **/
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }

    /**
     * Magic getter method
     *
     * @param string $key
     * @return mixed
     **/
    public function __get($key)
    {
        return isset($this[$key]) ? $this[$key] : null;
    }

} // END class Application
