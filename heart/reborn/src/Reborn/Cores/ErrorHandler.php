<?php

namespace Reborn\Cores;

use Closure;
use Exception;
use ReflectionFunction;
use Reborn\Exception\HttpNotFoundException;
use Reborn\Connector\Log\LogManager;
use Symfony\Component\HttpFoundation\Response;

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

        LogManager::debug($e->getMessage());

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
            $this->defaultHandling($e, $status)->send();
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
     * @param integer $status
     * @return \Reborn\Http\Response
     **/
    protected function defaultHandling($e, $status)
    {
        $request = $this->app->request;
        if ($request->isAjax() ||
            in_array('application/json', $request->getAcceptableContentTypes())
        ) {
            return $this->jsonResponseHandler($e, $status);
        }

        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $caller = get_class($e);
        $code = $this->getCodeLine($e);

        $traces = $e->getTrace();

        if ($this->app['env'] == 'production') {
            $template = $this->app['template'];
            $content = $template->renderProductionError();
        } else {
            $content = require APP.'views'.DS.'exception.php';
        }

        return new Response($content, $status);
    }

    /**
     * Exception handler for ajax request. Return json string
     *
     * @param \Exception $e
     * @param integer $status
     * @return \Reborn\Http\Response
     **/
    protected function jsonResponseHandler($e, $status)
    {
        $data = $trace_data = array();
        $data['exception'] = array(
            'stauts_code'   => $status,
            'type'          => get_class($e),
            'message'       => $e->getMessage(),
            'file'          => str_replace(BASE, '{{ CMS }} /', $e->getFile()),
            'line'          => $e->getLine()
        );

        $traces = $e->getTrace();
        foreach ($traces as $k => $t) {
            $trace_data[] = array(
                'file'      => str_replace(BASE, '{{ CMS }} /', $e->getFile()),
                'line'      => $this->getLine($t),
                'class'     => $this->getClass($t),
                'function'  => isset($t['function']) ? $t['function'] : '',
                'args'      => isset($t['args']) ? $t['args'] : null
            );

        }

        $data['exception']['trace'] = $trace_data;

        return Response::json($data, $status);
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
        return isset($t['function'])
                    ? '::'.$t['function'].'( )'
                    : null;
    }

    /**
     * Get File Name From Exception
     *
     * @param array $t Traces array
     * @param boolean $remove_basepath Remove base path form file
     * @return string|null
     */
    protected function getFile($t, $remove_basepath =false)
    {
        $file = isset($t['file']) ? $t['file'] : 'Unknown File';

        if ($remove_basepath) {
            return str_replace(BASE, '{{ CMS }} &raquo; ', $file);
        }

        return $file;
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
     * Get Error Line from source file.
     *
     * @param string $file
     * @param integer $line
     * @return string
     **/
    protected function getErrorLine($file, $line)
    {
        $content = \File::getContent($file);
        $lines = explode("\n", $content);

        $code = str_replace(array('<?php', '?>'), array('{{ ', ' }}'), $lines[$line - 1]);

        return '<pre>'.ltrim($code).'</pre>';
    }

    /**
     * Error hanlder method
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $code = $this->getErrorLine($errfile, $errline);

        $errfile = str_replace(BASE, '{{ CMS }} &raquo; ', $errfile);
        $style = <<<STYLE
        <style>
            .rb_exception_trace {
                padding: 1% 1% 1% 1% !important;
            }

            .trace_wrap {
                background: #fff !important;
                border: 1px solid #E74C3C;
                margin: 10px auto !important;
            }
            .trace_head {
                padding: 10px !important;
                color: #E74C3C !important;
                background: #fff !important;
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
                background: #E74C3C !important;
                color: #fff !important;
                font-weight: bold;
            }
            .user_err {
                background: #EA0972 !important;
            }
            .warning_err {
                background: #EA5809 !important;
            }
            .notice_err {
                background: #EAD86C !important;
            }
            .trace_body {
                padding: 15px 10px;
                color: #2D3E50 !important;
                position: relative;
                border-top: 1px solid #E74C3C;
            }
            pre {
                padding: 15px !important;
                background: #343434 !important;
                color: #fff !important;
                margin: 15px 0 0 0 !important;
            }
            .line_no {
                position: absolute;
                right: 0;
                display: block;
                padding: 9px 10px 9px;
                min-width: 35px;
                text-align: right;
                color: #7B0000;
                background: #f9f9f9;
                border-left: 1px solid #E74C3C;
                top: -37px;
                font-size: 18px;
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
                echo $code;
                echo '<span class="line_no">Line : ';
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
                echo $code;
                echo '<span class="line_no">Line : ';
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
                echo $code;
                echo '<span class="line_no">Line : ';
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
                echo $code;
                echo '<span class="line_no">Line : ';
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

