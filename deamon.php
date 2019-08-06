<?php
/**
 * Application Class For Deamon Workers
 */
class Deamon extends App
{
    const DEFAULT_PAGE = 1;

    const DEFAULT_POST_ARRAY = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Main Method For Application Test
     */
    public function run() : void
    {
        list(
            $controller,
            $action,
            $param
        ) = $this->_parseCLIOptions();

        if (!$this->_isControllerExist($controller)) {
            $this->_error();
        }

        $this->_autoLoad($controller);

        $controller = new $controller(
            $param,
            static::DEFAULT_POST_ARRAY,
            static::DEFAULT_PAGE
        );

        if (!$this->_isValidControllerAction($controller, $action)) {
            $this->_error();
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
     * Mock Method Of Performing Redirects By Rules
     */
    private function _redirect() : void
    {
        //Mock For App::_redirect()
    }

    /**
     * Mock Rewrite URI By Rules Method
     */
    private function _replaceURI() : void
    {
        //Mock For App::_replaceURI()
    }

    /**
     *  Mock Parsing URI Method
     */
    private function _parseURI() : array
    {
        //Mock For App::_parseURI()

        return [];
    }

    private function _parseCLIOptions() : array
    {
        $cliOptions = getopt('', ['controller:', 'action:', 'param:']);

        if (!array_key_exists('controller', $cliOptions)) {
            throw new Exception('Missing Controller Option');
        }

        if (!array_key_exists('action', $cliOptions)) {
            throw new Exception('Missing Action Option');
        }

        if (!array_key_exists('param', $cliOptions)) {
            $cliOptions['param'] = NULL;
        }

        return [
            mb_convert_case($cliOptions['controller'], MB_CASE_TITLE).
                'Controller',
            'action'.
                mb_convert_case($cliOptions['action'], MB_CASE_TITLE),
            (string) $cliOptions['param']
        ];
    }

    /**
     * Require All Plugins
     *
     * @param string $controller Name Of Controller Class
     */
    private function _autoLoad(string $controller = '') : void
    {
        require_once __DIR__.'/autoload.php';
        require_once __DIR__.'/../controllers/'.$controller.'.php';
    }

    /**
     *  Mock Redirect Rules
     */
    public function routeRedirect(string $uri = '') : void
    {
        //Mock For App::routeRedirect()
    }

    /**
     *  Mock Rewrite Rules
     */
    public function routeRewrite(string $uri = '') : string
    {
        //Mock For App::routeRewrite()

        return '';
    }

    /**
     * Check Is Controller Exists
     *
     * @param string $controller Var
     *
     * @return bool Is Controller Exists
     */
    private function _isControllerExist(string $controller = '') : bool
    {
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
        ControllerCore $controller,
        string         $action
    ) : bool
    {
        if (!method_exists($controller, $action)) {
            return FALSE;
        }

        $reflection = new ReflectionMethod($controller, $action);

        if (!$reflection->isPublic()) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Handler For Only App Class Errors
     */
    private function _error() : void
    {
        throw new Exception('Internal Server Error');
    }

    /**
     * Exceptions Handler
     *
     * @param Exception $exp Exception Instance
     */
    private function _exception(Exception $exp = NULL) : void
    {
        $expMessage = $exp->getMessage();

        (new ErrorPlugin)->displayException($expMessage, true);

        (new LoggerPlugin)->logError($expMessage);

        exit(0);
    }
}
?>