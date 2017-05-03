<?php

namespace Ongoo\Onyx\Controllers;

/**
 * Description of TwigWebController
 *
 * @author paul
 */
class TwigWebController extends WebController
{

    protected $twigData = array();
    protected $twigName;
    protected $twigTemplatePath;

    public function __construct(&$app, $twigName, $twigTemplatePath)
    {
        parent::__construct($app);
        $this->twigName = $twigName;
        $this->twigTemplatePath = $twigTemplatePath;
    }

    public function getTwigName()
    {
        return $this->twigName;
    }
    
    public function getTwigTemplatePath()
    {
        return $this->twigTemplatePath;
    }

    public function setTwigData($name, $value)
    {
        $this->twigData[$name] = $value;
        return $this;
    }

    public function getTwigData($name, $defaultValue = null)
    {
        return isset($this->twigData[$name]) ? $this->twigData[$name] : $defaultValue;
    }
    
    /**
     *
     * @param url $to
     */
    public function redirect($to, $params = array())
    {
        if (preg_match('#^http(s)?://#', $to))
        {
            return $this->app()->redirect($to);
        }
        return $this->app()->redirect(url_for($to, $params));
    }

    public function render($twigTemplate)
    {
        $this->app['onyx.twig.load']($this->getTwigTemplatePath(), 'self');
        
        if (!$this->app['twig.loader']->exists($twigTemplate))
        {
            throw new \InvalidArgumentException("twig template $twigTemplate not found");
        }
        
        return $this->app['twig']->render($twigTemplate, $this->twigData);
    }

}
