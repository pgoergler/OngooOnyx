<?php

namespace Ongoo\Onyx\Session;

function client_id()
{
    $c = \Ongoo\Onyx\Configuration::getInstance()->get('application');
    if (!$c->OffsetExists('client_id'))
    {
        $c['client_id'] = \Ongoo\Onyx\ip();
    }
    return $c['client_id'];
}

function me()
{
    $me = whoami();
    return $me ? $me->getLogin() : client_id();
}

function whoami()
{
    $c = \Ongoo\Onyx\Configuration::getInstance()->get('application');
    if (!$c->OffsetExists('whoami'))
    {
        $c['whoami'] = $c->protect(function() use (&$c)
        {
            if ($c->OffsetExists('session') && $c['session'] instanceof \Quartz\QuartzGuard\Session && $c['session']->getGuardUser())
            {
                if (method_exists($c['session'], 'getSecureUser'))
                {
                    return $c['session']->getSecureUser($c['request']);
                }
            }
            return null;
        });
    }
    return $c['whoami']();
}

function getUser()
{
    $c = \Ongoo\Onyx\Configuration::getInstance()->get('application');
    if ($c->offsetExists('session') && $c['session']->isStarted())
    {
        return $c['session']->getSecureUser($c['request']);
    }
    return null;
}