<?php

namespace Ongoo\Onyx\Providers;

/**
 * Description of OngooTwigServiceProvider
 *
 * @author paul
 */
class OnyxTwigServiceProvider implements \Silex\ServiceProviderInterface
{

    public function boot(\Silex\Application $app)
    {
        
    }

    public function register(\Silex\Application $app)
    {
        if (!$app->offsetExists('onyx.twig.load'))
        {
            $app['onyx.twig.load'] = $app->protect(function($path, $alias) use(&$app)
            {
                if ($app->offsetExists('twig') && $app['twig'])
                {
                    $bundlePath = preg_replace('#^\{apps\}#', $app['dir_apps'], $path);
                    if (is_dir($bundlePath))
                    {
                        $app['twig.loader.filesystem']->addPath($bundlePath, $alias);
                    }
                }
            });
        }

        if ($app->offsetExists('twig'))
        {
            $twig = $app['twig'];

            $twig->addFilter(new \Twig_SimpleFilter('json_decode', function ($string, $asArray = true)
            {
                return json_decode($string, $asArray);
            }));

            $twig->addFilter(new \Twig_SimpleFilter('startsWith', function ($string)
            {
                $regex = str_replace('#', '\\#', $string);
                return preg_match("#^$regex#", $string);
            }));

            $twig->addFilter(new \Twig_SimpleFilter('endsWith', function ($string)
            {
                $regex = str_replace('#', '\\#', $string);
                return preg_match("#$regex$#", $string);
            }));

            $twig->addFunction(new \Twig_SimpleFunction('ip', function() use( &$app)
            {
                return \ip();
            }, array('is_safe' => array('html'))));

            $twig->addFunction(new \Twig_SimpleFunction('me', function() use( &$app)
            {
                return \me();
            }, array('is_safe' => array('html'))));

            $twig->addFunction(new \Twig_SimpleFunction('whoami', function() use( &$app)
            {
                return \whoami();
            }, array('is_safe' => array('html'))));

            $twig->addFunction(new \Twig_SimpleFunction('now', function() use( &$app)
            {
                return \now();
            }, array('is_safe' => array('html'))));

            $twig->addFilter(new \Twig_SimpleFilter('decimal', function ($number)
            {
                return \decimal($number);
            }));

            $twig->addFilter(new \Twig_SimpleFilter('interval', function ($number)
            {
                return \Ongoo\Utils\StringUtils::secToTime($number);
            }));

            $twig->addFilter(new \Twig_SimpleFilter('slugify', function ($text, $default = 'n-a')
            {
                return \Ongoo\Utils\StringUtils::slugify($text, $default);
            }));
        }
    }
}