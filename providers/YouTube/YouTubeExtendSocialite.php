<?php

namespace HyperfSocialiteProviders\YouTube;

use Cblink\Hyperf\Socialite\SocialiteWasCalled;
use Hyperf\Event\Contract\ListenerInterface;

class YouTubeExtendSocialite implements ListenerInterface
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
        $event->extendSocialite('youtube', Provider::class);
    }
}