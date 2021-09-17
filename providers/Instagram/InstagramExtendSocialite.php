<?php

namespace HyperfSocialiteProviders\Instagram;

use Cblink\Hyperf\Socialite\SocialiteWasCalled;
use Hyperf\Event\Contract\ListenerInterface;

class InstagramExtendSocialite implements ListenerInterface
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
        $event->extendSocialite('instagram', Provider::class);
    }
}