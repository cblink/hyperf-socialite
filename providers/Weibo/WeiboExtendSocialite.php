<?php

namespace HyperfSocialiteProviders\Weibo;

use Cblink\Hyperf\Socialite\SocialiteWasCalled;
use Hyperf\Event\Contract\ListenerInterface;

class WeiboExtendSocialite implements ListenerInterface
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
        $event->extendSocialite('weibo', Provider::class);
    }
}