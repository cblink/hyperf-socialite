<?php

namespace HyperfSocialiteProviders\LaravelPassport;

use Cblink\Hyperf\Socialite\SocialiteWasCalled;
use Hyperf\Event\Contract\ListenerInterface;

class LaravelPassportExtendSocialite implements ListenerInterface
{
    public function listen(): array
    {
        return [
            SocialiteWasCalled::class,
        ];
    }

    /**
     * @param SocialiteWasCalled $event
     */
    public function process(object $event)
    {
        $event->extendSocialite('laravelpassport', Provider::class);
    }
}