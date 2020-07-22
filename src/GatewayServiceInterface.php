<?php


namespace Nevmmv\RocketGate;

use Nevmmv\RocketGate\Request\RequestInterface;

interface GatewayServiceInterface
{
    public function request(RequestInterface $request);
}
