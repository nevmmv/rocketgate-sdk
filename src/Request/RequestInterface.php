<?php

namespace Nevmmv\RocketGate\Request;

use Nevmmv\RocketGate\MerchantInterface;

interface RequestInterface
{
    public function getLink(): string;

    public function getParams(): array;

    public function getMerchant(): MerchantInterface;

    public function handleResponse(string $data): array;
}
