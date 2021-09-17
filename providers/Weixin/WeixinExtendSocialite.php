<?php

namespace HyperfSocialiteProviders\Weixin;

use Cblink\Hyperf\Socialite\SocialiteWasCalled;
use Hyperf\Event\Contract\ListenerInterface;

class WeixinExtendSocialite implements ListenerInterface
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
        $event->extendSocialite('weixin', Provider::class);
    }
}