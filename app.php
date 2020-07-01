<?php
/**
 * Main Application Class
 */
class App
{
    use Router;

    public function __construct()
    {
        $this->_redirect();
        $this->_replaceURI();
    }

    /**
     * Main Method For Application
     */
    public function run(): void
    {
        list(
            $controller,
            $action,
            $param,
            $page,
            $language
        ) = $this->_parseURI();

        if (!$this->_isControllerExist($controller)) {
            $errorMessage = sprintf('Controller %s Is Not Exist', $controller);

            throw new AppException(
                $errorMessage,
                AppException::CONTROLLER_IS_NOT_EXIST
            );
        }

        $this->_autoLoad($controller);

        $controller = new $controller(
            $param,
            $_POST,
            $page,
            $language
        );

        if (!$this->_isValidControllerAction($controller, $action)) {
            $errorMessage = sprintf(
                'Invalid Action %s For Controller %s',
                $action,
                $controller
            );

            throw new AppException(
                $errorMessage,
                AppException::INVALID_CONTROLLER_ACTION
            );
        }

        try {
            set_error_handler([$this, 'errorHandler']);
            $controller->$action();
        } catch (\Exception $exp) {
            $this->_exception($exp);
        }

        exit(0);
    }

    /**
     * Errors Handler
     *
     * @param int    $errCode    HTTP Response Code
     * @param string $errMessage Error Message
     * @param string $errFile    File With Error
     * @param int    $errLine    Line In File With Error
     */
    public function errorHandler(
        int    $errCode,
        string $errMessage,
        string $errFile,
        int    $errLine
    ): void
    {
        $debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($debugBacktrace as $idx => $debugBacktraceStep) {
            $debugBacktrace[$idx] = '…';

            if (array_key_exists('file', $debugBacktraceStep)) {
                $debugBacktrace[$idx] = $debugBacktraceStep['file'];
            }

            if (array_key_exists('line', $debugBacktraceStep)) {
                $debugBacktrace[$idx] = $debugBacktrace[$idx].
                                        ' ('.$debugBacktraceStep['line'].')';
            }
        }

        $debugBacktraceStr = implode(' -> ', array_reverse($debugBacktrace));

        $logMessage = "Error [$errCode]: $errMessage. ".
                      "File: $errFile ($errLine). ".
                      "Trace: $errFile ($debugBacktraceStr)";

        (new ErrorPlugin)->displayError(
            $errCode,
            $errMessage,
            $errFile,
            $errLine,
            $debugBacktrace,
            OUTPUT_FORMAT_JSON
        );

        (new LoggerPlugin)->logError($logMessage);

        exit(0);
    }

    /**
     * Perform Redirects By Rules
     */
    private function _redirect(): void
    {
        $uri = $_SERVER['REQUEST_URI'];

        $this->routeRedirect($uri);
    }

    /**
     * Rewrite URI By Rules
     */
    private function _replaceURI(): void
    {
        $_SERVER['REAL_REQUEST_URI'] = $_SERVER['REQUEST_URI'];

        $uri = $_SERVER['REQUEST_URI'];

        if ('' === $uri) {
            $uri = '/';
        }

        $uri = $this->routeRewrite($uri);

        $_SERVER['REQUEST_URI'] = $uri;
    }

    /**
     * Parse Controller, Method Of Cotroller And Params From URI
     */
    private function _parseURI(): ?array
    {
        $language   = null;
        $controller = null;
        $action     = null;
        $param      = null;
        $page       = null;

        $uri = $_SERVER['REQUEST_URI'];

        if (preg_match('/^\/(.*?)\/page-([0-9]+)\/$/su', $uri)) {
            $page = preg_replace('/^\/(.*?)\/page-(.*?)\/$/su', '$2', $uri);
            $uri  = preg_replace('/^\/(.*?)\/page-(.*?)\/$/su', '$1', $uri);
        }

        $uri = preg_replace('/((^\/)|(\/$))/su', '', $uri);

        $uriData = explode('/', $uri);

        if (!empty($uriData)) {
            $language = array_shift($uriData);
        }

        if (!empty($uriData)) {
            $controller = array_shift($uriData);
        }

        if (!empty($uriData)) {
            $action = array_shift($uriData);
        }

        if (!empty($uriData)) {
            $param = array_shift($uriData);
        }

        if (null === $controller) {
            throw new AppException(
                'Controller Is Not Set',
                AppException::CONTROLLER_IS_NOT_SET
            );
        }

        if (null === $action) {
            throw new AppException(
                'Action For Controller Is Not Set',
                AppException::CONTROLLER_ACTION_IS_NOT_SET
            );
        }

        $language   = (string) $language;
        $controller = mb_convert_case($controller, MB_CASE_TITLE).'Controller';
        $action     = 'action'.mb_convert_case($action, MB_CASE_TITLE);
        $param      = (string) $param;
        $page       = (int) $page;

        return [
            $controller,
            $action,
            $param,
            $page,
            $language
        ];
    }

    /**
     * Check Is Controller Exists
     *
     * @param string|null $controller Var
     *
     * @return bool Is Controller Exists
     */
    private function _isControllerExist(?string $controller = null): bool
    {
        if (empty($controller)) {
            return false;
        }

        return file_exists(__DIR__.'/../controllers/'.$controller.'.php');
    }

    /**
     * Check Is Method Public And Exists In Controller
     *
     * @param ControllerCore $controller ControllerCore Instance
     * @param string         $action     Name Of Method
     *
     * @return bool Is Method Public And Exists In Controller
     */
    private function _isValidControllerAction(
        ?ControllerCore $controller = null,
        ?string         $action     = null
    ): bool
    {
        if (empty($controller) || empty($action)) {
            return false;
        }

        if (!method_exists($controller, $action)) {
            return false;
        }

        $reflection = new ReflectionMethod($controller, $action);

        if (!$reflection->isPublic()) {
            return false;
        }

        return true;
    }

    /**
     * Require All Plugins And Controller Classes
     *
     * @param string $controller Name Of Controller Class
     */
    private function _autoLoad(?string $controller = null): void
    {
        if (empty($controller)) {
            throw new AppException(
                'Controller Is Not Set',
                AppException::CONTROLLER_IS_NOT_SET
            );
        }

        require_once __DIR__.'/autoload.php';
        require_once __DIR__.'/../controllers/'.$controller.'.php';
    }

    /**
     * Exceptions Handler
     *
     * @param Exception|null $exception Exception Instance
     */
    private function _exception(?Exception $exception = null): void
    {
        if (empty($exception)) {
            exit(0);
        }

        $expMessage = $exception->getMessage();

        (new ErrorPlugin)->displayException($expMessage, OUTPUT_FORMAT_JSON);

        (new LoggerPlugin)->logError($expMessage);

        exit(0);
    }
}
