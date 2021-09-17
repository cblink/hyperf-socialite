<?php

namespace HyperfSocialiteProviders\WeChatServiceAccount;

use Cblink\Hyperf\Socialite\SocialiteWasCalled;
use Hyperf\Event\Contract\ListenerInterface;

class WeChatServiceAccountExtendSocialite implements ListenerInterface
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
        $event->extendSocialite('wechat_service_account', Provider::class);
    }
}