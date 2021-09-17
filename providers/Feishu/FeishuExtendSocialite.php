<?php

namespace HyperfSocialiteProviders\Feishu;

use Cblink\Hyperf\Socialite\SocialiteWasCalled;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * Class FeishuExtendSocialite
 * @package Cblink\Socialite\Feishu
 */
class FeishuExtendSocialite implements ListenerInterface
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
        $event->extendSocialite('feishu', Provider::class);
    }
}