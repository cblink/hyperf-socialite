<?php

namespace Cblink\Hyperf\Socialite\Contracts;

use Cblink\Hyperf\Socialite\Two\AbstractProvider;

interface SocialiteInterface
{
    /**
     * @param string|null $driver
     * @return AbstractProvider
     */
    public function driver($driver = null);
}