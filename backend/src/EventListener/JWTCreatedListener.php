<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener 
{
    // src/App/EventListener/JWTCreatedListener.php



/**
 * @var RequestStack
 */
private $requestStack;

/**
 * @param RequestStack $requestStack
 */
public function __construct(RequestStack $requestStack)
{
    $this->requestStack = $requestStack;
}

/**
 * @param JWTCreatedEvent $event
 *
 * @return void
 */
public function onJWTCreated(JWTCreatedEvent $event)
{
    $user = $event->getUser();

    

    $payload       = $event->getData();

    $payload['is_verified'] = $user->isVerified();

    $event->setData($payload);

    $header        = $event->getHeader();
    $header['cty'] = 'JWT';

    $event->setHeader($header);
}
}