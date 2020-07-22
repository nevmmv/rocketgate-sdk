<?php

namespace Nevmmv\RocketGate;

interface MerchantInterface
{
    public function getId(): string;

    public function getPassword(): string;

    public function getName(): string;
}
