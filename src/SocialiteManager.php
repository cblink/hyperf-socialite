<?php

namespace Cblink\Hyperf\Socialite;

use Cblink\Hyperf\Socialite\Contracts\SocialiteInterface;
use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Context\ApplicationContext;

class SocialiteManager extends Manager implements SocialiteInterface
{
    public function __construct()
    {
        $this->setContainer(ApplicationContext::getContainer());

        $wasCalled = new SocialiteWasCalled();

        foreach ($this->getProviders() as $provider) {
            $wasCalled->extendSocialite($this, $provider);
        }
    }

    public function getDefaultDriver()
    {
        throw new SocialiteException('No Socialite driver was specified.');
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param string $provider
     * @return AbstractProvider
     */
    public function buildProvider($provider)
    {
        $session = $this->getContainer()->has(SessionInterface::class) ?
            $this->getContainer()->get(SessionInterface::class) :
            null;

        return new $provider($this->getContainer()->get(RequestInterface::class), $session);
    }

    /**
     * @return array
     */
    protected function getProviders()
    {
        return $this->getContainer()
            ->get(ConfigInterface::class)
            ->get(sprintf('socialite.providers'), []);
    }

    /**
     * Format the server configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function formatConfig(array $config): array
    {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => value($config['redirect']),
        ], $config);
    }
}