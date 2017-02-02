<?php

namespace Ongoo\Onyx;

/**
 * Description of Service
 *
 * @author paul
 */
abstract class Service
{

    protected $app;

    public function __construct(\Silex\Application $app = null)
    {
        if (is_null($app))
        {
            $app = \Ongoo\Onyx\Configuration::getInstance()->get('application');
        }
        $this->app = $app;
    }

}
