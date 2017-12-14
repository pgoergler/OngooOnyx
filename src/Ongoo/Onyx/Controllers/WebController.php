<?php

namespace Ongoo\Onyx\Controllers;

/**
 * Description of WebController
 *
 * @author paul
 */
class WebController
{

    protected $app = null;

    public function __construct(&$app)
    {
        $this->app = $app;
    }

    /**
     *
     * @return \Silex\Application
     */
    public function app()
    {
        return $this->app;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->app['request_stack']->getCurrentRequest();
    }

    /**
     *
     * @return \Ongoo\Session\Session
     */
    public function getSession()
    {
        return $this->app['session'];
    }

    public function abortUnless($condition, $httpCode, $message = '')
    {
        return $this->app['request.abort_unless']($condition, $httpCode, $message);
    }

    public function abortIf($condition, $httpCode, $message = '')
    {
        return $this->app['request.abort_if']($condition, $httpCode, $message);
    }

    public function abort404Unless($condition, $message = '')
    {
        return $this->app['request.abort_unless']($condition, 404, $message);
    }

    public function abort404If($condition, $message = '')
    {
        return $this->app['request.abort_if']($condition, 404, $message);
    }

    public function abort403Unless($condition, $message = '')
    {
        return $this->app['request.abort_unless']($condition, 403, $message);
    }

    public function abort403If($condition, $message = '')
    {
        return $this->app['request.abort_if']($condition, 403, $message);
    }

    public function abort401Unless($condition, $message = '')
    {
        return $this->app['request.abort_unless']($condition, 401, $message);
    }

    public function abort401If($condition, $message = '')
    {
        return $this->app['request.abort_if']($condition, 401, $message);
    }

    public function forwardCodeUnless($code, $condition, $message = "error")
    {
        trigger_error('forwardCodeUnless is deprecated', E_USER_DEPRECATED);
        $this->abortUnless($condition, $code, $message);
    }

    public function forwardCode($code, $condition, $message = "error")
    {
        trigger_error('forwardCode is deprecated', E_USER_DEPRECATED);
        $this->abortIf(!$condition, $code, $message);
    }

    public function forward404Unless($condition)
    {
        trigger_error('forward404Unless is deprecated', E_USER_DEPRECATED);
        $this->abort404Unless($condition);
    }

    public function forward404($condition)
    {
        trigger_error('forward404 is deprecated', E_USER_DEPRECATED);
        $this->abort404If($condition);
    }

    public function forward401Unless($condition)
    {
        trigger_error('forward401Unless is deprecated', E_USER_DEPRECATED);
        $this->abort401Unless($condition);
    }

    public function forward401($condition)
    {
        trigger_error('forward401 is deprecated', E_USER_DEPRECATED);
        $this->abort401If($condition);
    }

    public function execute($action = 'index', $args = array())
    {
        $fn = sprintf('execute%s', ucfirst($action));
        return call_user_func_array(array($this, $fn), $args);
    }

}
