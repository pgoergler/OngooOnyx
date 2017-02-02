<?php

namespace Ongoo\Onyx;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

/**
 * Description of OngooProvider
 *
 * @author paul
 */
class OnyxServiceProvider implements \Silex\ServiceProviderInterface
{

    public function boot(\Silex\Application $app)
    {
    }

    public function register(\Silex\Application $app)
    {
        if (!$app->offsetExists('onyx.services'))
        {
            $app['onyx.services'] = array();
        }

        if (!$app->offsetExists('service.register'))
        {
            $app['service.register'] = $app->protect(function(\Silex\ServiceProviderInterface $service, array $values = array()) use(&$app)
            {
                $class = \get_class($service);
                $bundles = $app['onyx.services'];
                if (!isset($bundles[$class]))
                {
                    $bundles[$class] = $service;
                    $app['onyx.services'] = $bundles;
                    return $app->register($service, $values);
                }
            });
        }

        if (!$app->OffsetExists('application.mode'))
        {
            $app['application.mode'] = 'dev';
        }
        $databases = \Ongoo\Utils\ArrayUtils::merge(include(__CONFIG_DIR . '/databases.php'), $app['application.mode']);
        $globalConfig = \Ongoo\Utils\ArrayUtils::merge(include(__CONFIG_DIR . '/config.php'), $app['application.mode']);
        $loggers = \Ongoo\Utils\ArrayUtils::merge(include(__CONFIG_DIR . '/loggers.php'), $app['application.mode']);
        
        $app['configuration'] = $app->share(function()
        {
            return \Ongoo\Onyx\Configuration::getInstance();
        });

        $app['configuration']->append(array('Databases' => $databases));
        $app['configuration']->append(array('Loggers' => $loggers), true);
        $app['configuration']->append($globalConfig, true);


        if (!$app->offsetExists('session.activate'))
        {
            $app['session.activate'] = $app->protect(function() use (&$app)
            {
                $app['session']->start();
            });
        }

        if (!$app->offsetExists('request.dump'))
        {
            $app['request.dump'] = $app->protect(function(\Symfony\Component\HttpFoundation\Request $request) use (&$app)
            {
                $app['logger']->debug("request:");
                $app['logger']->debug($request->request->all());
            });
        }

        if (!$app->offsetExists('session.dump'))
        {
            $app['session.dump'] = $app->protect(function(\Symfony\Component\HttpFoundation\Request $request) use (&$app)
            {
                $app['logger']->debug($_COOKIE);
                $app['logger']->debug($request->cookies);
            });
        }

        if (!$app->offsetExists('response.dump'))
        {
            $app['response.dump'] = $app->protect(function(Request $request, Response $response) use (&$app)
            {
                $app['logger']->debug("response={}", [$response->getContent()]);
                $app['logger']->debug("---^^^^^^^^^^^^");
            });
        }

        if (!$app->OffsetExists('request.abort'))
        {
            $app['request.abort_if'] = $app->protect(function($condition, $code, $message) use (&$app)
            {
                if ($condition)
                {
                    $app['logger']->error("aborting due to {0} {1}", array($code, $message));
                    $app->abort($code, $message);
                }
            });
        }

        if (!$app->OffsetExists('request.abort_unless'))
        {
            $app['request.abort_unless'] = $app->protect(function($condition, $code, $message = null) use (&$app)
            {
                if (!$message)
                {
                    switch ($code)
                    {
                        case 401:
                            $message = "Unauthorized";
                            break;
                        case 403:
                            $message = "Unauthorized access";
                            break;
                        case 404:
                            $message = "Not found";
                            break;
                        default:
                            $message = "Unknown error";
                    }
                }
                return $app['request.abort_if'](!$condition, $code, $message);
            });
        }

        if (!$app->OffsetExists('common.on_response'))
        {
            $app['common.on_response'] = $app->protect(function($code, $callback) use (&$app)
            {
                return function(Request $request, Response $response) use (&$app, $code, $callback)
                {
                    if ($response->getStatusCode() == $code)
                    {
                        if (is_callable($callback))
                        {
                            return $callback($app, $request, $response);
                        }
                    }
                };
            });
        }

        if (!$app->OffsetExists('common.on_http_error.json'))
        {
            $app['common.on_http_error.json'] = $app->protect(function(Request $request, Response $response) use (&$app)
            {
                $code = $response->getStatusCode();
                switch ($code)
                {
                    case 401:
                        $message = "Unauthorized";
                        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
                            'success' => false,
                            'reason' => $message
                                ), $code);
                    case 403:
                        $message = "Unauthorized access";
                        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
                            'success' => false,
                            'reason' => $message
                                ), $code);
                    case 404:
                        $message = "Not found";
                        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
                            'success' => false,
                            'reason' => $message
                                ), $code);
                }
                return $response;
            });
        }

        if (!$app->OffsetExists('common.on_404.json'))
        {
            $app['common.on_404.json'] = $app->protect(function($callback = null) use (&$app)
            {
                return function(Request $request, Response $response) use (&$app, $callback)
                {
                    if ($response->getStatusCode() == 404)
                    {
                        if (is_callable($callback))
                        {
                            return $callback($app, $request, $response);
                        } else
                        {
                            return new \Symfony\Component\HttpFoundation\JsonResponse(array(
                                'success' => false
                                    ), 404);
                        }
                    }
                };
            });
        }
        
        if (!$app->OffsetExists('common.exception_to_json'))
        {
            $app['common.exception_to_json'] = $app->protect(function(\Exception $e, $code) use (&$app)
            {
                $app['logger']->error($e);
                return $app->json(array('success' => false,'reason' => $e->getMessage()), $code);
            });
        }
    }

}
require_once('Session/functions.php');
