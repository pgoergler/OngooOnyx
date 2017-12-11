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

}
