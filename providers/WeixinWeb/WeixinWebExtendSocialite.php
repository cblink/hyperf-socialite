<?php

namespace HyperfSocialiteProviders\WeixinWeb;

use Cblink\Hyperf\Socialite\SocialiteWasCalled;
use Hyperf\Event\Contract\ListenerInterface;

class WeixinWebExtendSocialite implements ListenerInterface
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
        $event->extendSocialite('weixinweb', Provider::class);
    }
}