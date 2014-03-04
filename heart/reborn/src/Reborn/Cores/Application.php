<?php

namespace Reborn\Cores;

use Reborn\Http\Request;
use Reborn\Http\Response;
use Reborn\Http\Uri;
use Reborn\Routing\Router;
use Reborn\Routing\RouteCollection;
use Reborn\MVC\View\ViewManager;
use Reborn\Parser\InfoParser;
use Reborn\Config\Config;
use Reborn\Filesystem\File;
use Reborn\Widget\Widget;
use Reborn\Util\Security;
use Reborn\Translate\Loader\PHPFileLoader;
use Reborn\Event\EventManager as Event;
use Reborn\Cache\CacheManager;
use Reborn\Connector\Log\LogManager;
use Reborn\Connector\DB\DBManager as DB;
use Reborn\Module\ModuleManager as Module;
use Reborn\Exception\RbException;
use Reborn\Exception\TokenNotMatchException;
use Reborn\Exception\MaintainanceModeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

/**
 * Main Application Class for Reborn.
 * This class is extend the Illuminate\Container (IOC Container).
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class Application extends \Illuminate\Container\Container
{

    /**
     * Marking for application started or not
     *
     * @var bool
     **/
    protected $started = false;

    /**
     * Constructor Method.
     * Create new object for Reborn Application
     *
     * @return void
     **/
    public function __construct()
    {
        $this['error_handler'] = new ErrorHandler($this);

        $this['request'] = $this->solveRequest();

        // Enable Http Method Override for (_method)
        Request::enableHttpMethodParameterOverride();

        // Set the Application Object into the Registry
        Registry::set('app', $this);
    }

    /**
     * Solve Request Instance for Application
     *
     * @return \Reborn\Http\Request
     **/
    public function solveRequest()
    {
        if ($this->runInCli()) {
            return Request::create('http://localhost/', 'GET', array(), array(), array(), $_SERVER);
        }

        return Request::createFromGlobals();
    }

    /**
     * Solve Session Instance for Application
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session
     **/
    public function solveSession()
    {
        if ($this->runInCli()) {
            return new Session(new MockArraySessionStorage('reborn_session'));
        } else {
            $lifetime = $this['config']->get('app.session_lifetime', 60) * 60;
            $options = array('gc_maxlifetime' => $lifetime);

            $h = new NativeFileSessionHandler($this['config']->get('app.session_path', null));

            return new Session(new NativeSessionStorage($options, $h));
        }
    }

    /**
     * Set the Reborn CMS Environment.
     * Reborn accept 3 type of environment
     * <code>
     *  (1) - dev (For Development Stage)
     *  (2) - test (For Testing Stage)
     *  (3) - production (For Production Stage)
     * </code>
     *
     * @param  string $env
     * @return void
     **/
    public function setAppEnvironment($env)
    {
        $accept_envs = array('dev', 'test', 'production');
        $this['env'] = in_array($env, $accept_envs) ? $env : 'production';
    }

    /**
     * Get current Reborn CMS Environment.
     *
     * @return string
     **/
    public function getAppEnvironment()
    {
        return $this['env'];
    }

    /**
     * Check Application Environment is "dev".
     *
     * @return boolean
     **/
    public function runInDevelopment()
    {
        return ($this['env'] === 'dev');
    }

    /**
     * Check Application Environment is "production".
     *
     * @return boolean
     **/
    public function runInProduction()
    {
        return ($this['env'] === 'production');
    }

    /**
     * Check Application Environment is "test".
     *
     * @return boolean
     **/
    public function runInTesting()
    {
        return ($this['env'] === 'test');
    }

    /**
     * Check Application run in Cli
     *
     * @return boolean
     **/
    public function runInCli()
    {
        return (php_sapi_name() === 'cli');
    }

    /**
     * Check Reborn is already installed
     *
     * @return boolean
     **/
    public function installed()
    {
        if (! File::is(APP.'config'.DS.'db.php')) {
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
        if (File::is(APP.'config'.DS.'db.php')) {
            throw new RbException("Reborn CMS is already installed!");
        }

        require BASE.'installer'.DS.'Installer.php';

        \Installer::init($this);

        \Installer::start();

        exit;
    }

    /**
     * Start the application
     *
     * @param  boolean $session_is_started
     * @return void
     **/
    public function start($session_is_started = false)
    {
        if ($this->started) {
            throw new RbException("Reborn CMS Application is already started!");
        }

        // Register to IOC contianer
        $this->registerToContainer();

        // Set Exception and Error Handler
        $this->setErrorHandler();

        // call the appInitialize method
        $this->appInitialize($session_is_started);

        $this->authProviderRegister();

        // Call the Event Name App Start
        Event::call('reborn.app.starting');

        // Set Timezone for Application
        $this->setTimezone(\Setting::get('timezone', 'UTC'));
    }

    /**
     * Run Reborn Application.
     *
     * @return void
     **/
    public function run()
    {
        try {
            $response = $this['router']->dispatch();

            $this->started = true;

            if (!$response instanceof SymfonyResponse) {
                $response = new Response($response);
            }

            // Send response to the end method
            $this->end($response);
        } catch (TokenNotMatchException $e) {
            // For CSRF Fail
            \Translate::load('global');
            \Flash::error(t('global.csrf_fail'));

            $basepath = $this['request']->getBasePath();
            $redirect_url = str_replace($basepath, '', \Input::server('REDIRECT_URL'));

            return \Redirect::to($redirect_url);
        } catch (MaintainanceModeException $e) {
            $this->end(Response::maintain());

            exit(1);
        }
    }

    /**
     * Register core class to container
     *
     * @return void
     **/
    protected function registerToContainer()
    {
        $this['config'] = $this->share(function ($app) {
            return new Config($app);
        });

        $this['site_manager'] = $this->share(function ($app) {
            return new SiteManager($app);
        });

        $this['cache'] = $this->share( function ($app) {
            return new CacheManager($app);
        });

        $this['route_collection'] =  $this->share(function ($app) {
            return new RouteCollection();
        });

        $this['router'] =  $this->share(function ($app) {
            return new Router($app);
        });

        $this['log'] = $this->share(function ($app) {
            return new LogManager($app);
        });

        $this['view_manager'] = $this->share( function ($app) {
            return new ViewManager($app);
        });

        $this['view'] = $this->share( function ($app) {
            return $app['view_manager']->getView();
        });

        $this['info_parser'] = $this->share( function ($app) {
            return new InfoParser();
        });

        $this['theme'] = $this->share( function ($app) {
            return $app['view_manager']->getTheme();
        });

        $this['template'] = $this->share( function ($app) {
            return $app['view_manager']->getTemplate();
        });

        $this['session'] = $this->solveSession();

        $this['widget'] = $this->share( function ($app) {
            return new Widget($app);
        });

        $this['profiler'] = $this->share( function () {
            return new Profiler();
        });

        $this['translate_loader'] = $this->share( function ($app) {
            return new PHPFileLoader($app);
        });

    }

    /**
     * Start the Initialize method from require classes.
     * But this method is call from application start method only.
     * Don't call more than once.
     *
     * @param boolean $session_is_started
     */
    public function appInitialize($session_is_started)
    {
        if ($this->started) {
            return true;
        }

        // Start the Session
        if (isset($this['session'])) {

            if (! $session_is_started) {
                $this['session']->start();
            }

            \Security::setApplication($this);

            // Check and Make CSRF Token
            $csrf = $this['config']->get('app.security.csrf_key');

            if ( ! $this['session']->has($csrf)) {
                \Security::makeCSRFToken();
            }
        }

        // Start the Profiler
        if (('dev' == $this['env']) and $this['config']->get('app.profiler')) {
            $this['profiler']->start();
        }

        // Start the Event initialize
        Event::initialize($this);

        // Start the Database initialize
        DB::initialize($this);

        // Start the Setting initialize
        Setting::initialize($this);

        // Start the Uri initialize
        Uri::initialize($this->request);

        // Start the Module initialize
        Module::initialize($this);

        // Start the Widget initialize
        $this['widget']->initialize();
    }

    /**
     * End point of application
     *
     * @return void
     **/
    public function end(SymfonyResponse $response)
    {
        // Stop the Profiler
        if (('dev' == $this['env']) and $this['config']->get('app.profiler')) {
            $this['profiler']->stop();

            if (!$response instanceof JsonResponse ||
                !$response instanceof StreamedResponse) {
                // Call the Event Name App Profiling
                if (Event::has('reborn.app.profiling')) {
                    $result = Event::call('reborn.app.profiling', $response->getContent());
                    $response->setContent($result[0]);
                }
            }
        }

        // Check event for Response content finalize.
        // You can do html content compressing, minify with this step
        // This event will be work for Normal Content Response
        if (!$response instanceof JsonResponse ||
                !$response instanceof StreamedResponse) {

            if (Event::has('reborn.responsecontent.final')) {
                $content = $response->getContent();
                $content = Event::first('reborn.responsecontent.final', array($content));

                $response->setContent($content);
            }
        }

        // Call the Event Name App Ending
        Event::call('reborn.app.ending', array($response));

        return $response->send();
    }

    /**
     * Register the AuthProvider for Reborn CMS.
     *
     * @param  \Closure|null                     $callback Custom AuthProvider regsiter function.
     * @return \Reborn\Auth\AuthProviderInstance
     **/
    public function authProviderRegister(\Closure $callback = null)
    {
        $this['auth_provider'] = $this->share( function ($app) use ($callback) {
            $provider = null;

            if (! is_null($callback) ) {
                $provider = $callback($app);
            }

            if (! $provider instanceof \Reborn\Auth\AuthProviderInterface) {
                $provider = new \Reborn\Auth\AuthSentryProvider($app);
            }

            return $provider;
        });

        // Register for User Provider
        $this['user_provider'] = $this->share( function ($app) {
            return $app['auth_provider']->getUserProvider();
        });

        // Register for UserGroup Provider
        $this['usergroup_provider'] = $this->share( function ($app) {
            return $app['auth_provider']->getGroupProvider();
        });
    }

    /**
     * Set Locale for application.
     * Default locale is en
     *
     * @param  string $locale
     * @return void
     **/
    public function setLocale($locale = 'en')
    {
        $this['locale'] = $locale;

        \Translate::setLocale($locale);

        Event::call('reborn.app.locale_change', array($locale));
    }

    /**
     * Set Timezone for application.
     * Default timezone is UTC
     *
     * @param  string $tz
     * @return void
     **/
    public function setTimezone($tz = 'UTC')
    {
        date_default_timezone_set($tz);
    }

    /**
     * Set the Error Handler for Reborn CMS
     *
     * @return void
     */
    public function setErrorHandler()
    {
        $this['error_handler']->register();
    }

    /**
     * Magic setter method
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     **/
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }

    /**
     * Magic getter method
     *
     * @param  string $key
     * @return mixed
     **/
    public function __get($key)
    {
        return isset($this[$key]) ? $this[$key] : null;
    }

    /**
     * Inject CSRF Token
     *
     * @param  \Reborn\Http\Response $response Response Object
     * @return string
     **/
    public function injectCSRFToken($response)
    {
        $token = Security::CSRField();

        preg_match('/(value\s*=\s*"(.*)")/', $token, $m);

        $meta = \Html::meta('csrf-token', $m[2]);

        $body = $response->getContent();

        preg_match('/(<\/(head|HEAD)>)/', $body, $m);

        $body = preg_replace('/(<\/(head|HEAD)>)/', $meta."\n".'$0', $body);

        $pattern = '/(<(form|FORM)[^>]*(method|METHOD)="(post|POST)"[^>]*>)/';

        preg_match_all($pattern, $body, $matches, PREG_SET_ORDER);

        if (is_array($matches)) {
            foreach ($matches as $match) {
                if (false == strpos($match[0], 'nocsrf')) {
                    $body = str_replace($match[0], $match[0]."\n\t" .$token, $body);
                }
            }
        }

        $response->setContent($body);

        return $response;
    }

} // END class Application
