<?php

namespace Cblink\Hyperf\Socialite\Contracts;

use Cblink\Hyperf\Socialite\Two\AbstractProvider;

interface SocialiteInterface
{
    /**
     * @param string $name
     * @return AbstractProvider
     */
    public function driver(string $name);
}