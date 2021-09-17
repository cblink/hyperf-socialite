<?php

namespace Cblink\Hyperf\Socialite;

use Cblink\Hyperf\Socialite\Contracts\SocialiteInterface;
use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class Socialite implements SocialiteInterface
{

    public function __invoke(ContainerInterface $container)
    {
        $container
            ->make(EventDispatcherInterface::class)
            ->dispatch(new SocialiteWasCalled($container->make(ConfigInterface::class)));
    }

    /**
     * @param string $name
     * @return AbstractProvider
     */
    public function driver(string $name)
    {
        return make(SocialiteManager::class)->drver($name);
    }
}