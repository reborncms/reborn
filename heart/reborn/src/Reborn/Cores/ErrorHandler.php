<?php

namespace Reborn\Cores;

use Closure;
use Exception;
use ReflectionFunction;
use Reborn\Exception\HttpNotFoundException;
use Reborn\Http\Response;

/**
 * Error Handler class
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class ErrorHandler
{

    /**
     * Exception Handlers
     *
     * @var array
     **/
    protected $handlers = array();

    /**
     * Application (IOC) Container
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * Default instance method
     *
     * @param \Reborn\Cores\Application $app
     * @return void
     **/
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register the error handler.
     *
     */
    public function register()
    {
        set_error_handler(array($this, "errorHandler"));
        set_exception_handler(array($this, "exceptionHandler"));
        register_shutdown_function(array($this, 'shutdownHandler'));
    }

    /**
     * Bind to Exception Hanlder
     *
     * @param Closure $handler Closure function for handler
     * @param boolean $append Handler is append in $this->handlers
     * @return \Reborn\Cores\ErrorHandler
     */
    public function bind(Closure $handler, $append = false)
    {
        if ($append) {
            array_push($this->handlers, $handler);
        } else {
            array_unshift($this->handlers, $handler);
        }

        return $this;
    }

    /**
     * Handler for the Exception Error.
     *
     * @param \Exception
     * @return mixed
     */
    public function exceptionHandler(Exception $e)
    {
        // Response Status Code
        if ($e instanceof HttpNotFoundException) {
            $status = 404;
        } else {
            $status = 500;
        }

        $response = null;

        // Resolve Binding From Register Handlers
        if ($handler = $this->resolveBinding($e)) {
            try {
                $response = $handler($e);
            } catch (Exception $e) {
                $response = null;
            }
        }

        if ($response instanceof Response) {
            $response->send();
        } else {
            $this->defaultHandling($e, $status);
        }

        exit(1);
    }

    /**
     * Resolve Handler Binding
     *
     * @param \Exception $e
     * @return boolean|Closure
     **/
    protected function resolveBinding($e)
    {
        foreach ($this->handlers as $handler) {
            $reflection = new ReflectionFunction($handler);
            $parameters = $reflection->getParameters();
            $hint = $parameters[0];
            $exception = $hint->getClass();

            if (! $exception->isInstance($e)) {
                continue;
            }

            return $handler;
        }

        return false;
    }

    /**
     * Default Exception Handling
     *
     * @param \Exception $e
     * @return \Reborn\Http\Response
     **/
    protected function defaultHandling($e, $status)
    {
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $caller = get_class($e);
        $code = $this->getCodeLine($e);

        $traces = $e->getTrace();

        \Log::debug($message);

        if ($this->app['env'] == 'production') {
            $view = new \Reborn\MVC\View\View(\Config::get('template.cache_path'));
            $content = $view->render(APP.'views'.DS.'production-error.php');
        } else {
            $content = require APP.'views'.DS.'exception.php';
        }

        return new Response($content, $status);
    }

    /**
     * Get Class Name From Exception
     *
     * @param array $t Traces array
     * @return string|null
     */
    protected function getClass($t)
    {
        return isset($t['class']) ? $t['class'] : null;
    }

    /**
     * Get Function Name From Exception
     *
     * @param array $t Traces array
     * @return string|null
     */
    protected function getFunction($t)
    {
        return isset($t['function']) ? '::'.$t['function'] : null;
    }

    /**
     * Get File Name From Exception
     *
     * @param array $t Traces array
     * @return string|null
     */
    protected function getFile($t)
    {
        return isset($t['file']) ? $t['file'] : 'Unknown File';
    }

    /**
     * Get Line Number From Exception
     *
     * @param array $t Traces array
     * @return string
     */
    protected function getLine($t)
    {
        return isset($t['line']) ? $t['line'] : '#';
    }

    /**
     * Get Code Block to show at exception view
     *
     * @param array $t Traces array
     * @return string
     */
    protected function getCodeLine($t)
    {
        if ($t instanceof \Exception) {
            $line = $t->getLine();
            $file = $t->getFile();
        } else {
            $line = $this->getLine($t);
            if('#' == $line) return null;
            $file = $this->getFile($t);
        }

        $content = \File::getContent($file);
        $lines = explode("\n", $content);
        $codeLines = array_slice($lines, $line - 6, 10, true);
        $codeLines[$line - 1] = '<strong>'.$codeLines[$line -1].'</strong>';
        $codeLines = implode("\n", $codeLines);
        return '<pre>'.$codeLines.'</pre>';
    }

    /**
     * Error hanlder method
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        $style = <<<STYLE
        <style>
            .rb_exception_trace {
                padding: 1% 1% 1% 0;
            }

            .trace_wrap {
                background: #fff;
                border: 1px solid #ababab;
                margin: 10px auto;
            }
            .trace_head {
                padding: 10px;
                color: #333;
                background: #cdcdcd;
                cursor: pointer;
                position: relative;
            }
            .error_trace {
                padding-left: 45px !important;
            }
            .error_throw {
                padding: 10px 15px;
                position: absolute;
                display: block;
                top: 0;
                left: 0;
                background: #787878;
                color: #fff;
                font-weight: bold;
            }
            .user_err {
                background: #EA0972;
            }
            .warning_err {
                background: #EA5809;
            }
            .notice_err {
                background: #EAD86C;
            }
            .trace_body {
                padding: 15px 10px;
                color: #2D3E50;
                position: relative;
                border-top: 1px solid #b9b9b9;
            }
            .line_no {
                position: absolute;
                right: 0;
                display: block;
                padding: 12px 10px 13px 10px;
                min-width: 35px;
                text-align: right;
                color: #7B0000;
                background: #efefef;
                border-left: 1px solid #b9b9b9;
                bottom: 0;
                font-size: 18px;
                font-weight: bold;
            }
        </style>
STYLE;
        echo $style;

        echo '<div class="rb_main_wrap">';
        echo '<div class="rb_exception_trace">';
        echo '<div class="trace_wrap">';
        switch ($errno) {
            case E_USER_ERROR:
                echo '<div class="trace_head error_trace">';
                echo '<span class="error_throw user_err">!</span>';
                echo "My ERROR : [$errno] $errstr ";
                echo '</div>';
                echo '<div class="trace_body">';
                echo $errfile;
                echo '<span class="line_no">';
                echo $errline;
                echo '</span>';
                echo '</div>';
                exit(1);
                break;

            case E_USER_WARNING:
                echo '<div class="trace_head error_trace">';
                echo '<span class="error_throw warning_err">!</span>';
                echo "My WARNING : [$errno] $errstr ";
                echo '</div>';
                echo '<div class="trace_body">';
                echo $errfile;
                echo '<span class="line_no">';
                echo $errline;
                echo '</span>';
                echo '</div>';
                break;

            case E_USER_NOTICE:
                echo '<div class="trace_head error_trace">';
                echo '<span class="error_throw notice_err">!</span>';
                echo "My NOTICE : [$errno] $errstr ";
                echo '</div>';
                echo '<div class="trace_body">';
                echo $errfile;
                echo '<span class="line_no">';
                echo $errline;
                echo '</span>';
                echo '</div>';
                break;

            default:
                echo '<div class="trace_head error_trace">';
                echo '<span class="error_throw">!</span>';
                echo "Unknown error type: [$errno] $errstr";
                echo '</div>';
                echo '<div class="trace_body">';
                echo $errfile;
                echo '<span class="line_no">';
                echo $errline;
                echo '</span>';
                echo '</div>';
                break;
            }

        echo '</div>';
        echo '</div>';
        echo '</div>';

        return true;
    }

    /**
     * Suhtdown Handler.
     *
     */
    public function shutdownHandler()
    {
        if ( $error = error_get_last()) {
            $this->errorHandler($error['type'],
                                $error['message'],
                                $error['file'],
                                $error['line']);
        }
    }

} // END class ErrorHandler

