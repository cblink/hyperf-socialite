<?php

namespace Cblink\Hyperf\Socialite;

use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Contract\RequestInterface;

class SocialiteManager extends Manager
{
    public function __construct()
    {
        $this->init();
    }

    public function getDefaultDriver()
    {
        throw new SocialiteException('No Socialite driver was specified.');
    }

    /**
     *
     */
    public function init()
    {
        $wasCalled = new SocialiteWasCalled();

        foreach ($this->getProviders() as $key => $provider) {
            $wasCalled->extendSocialite($key, $provider);
        }
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param string $provider
     * @return AbstractProvider
     */
    public function buildProvider($provider)
    {
        return new $provider(
            make(RequestInterface::class),
            make(SessionInterface::class)
        );
    }

    /**
     * @return array
     */
    protected function getProviders()
    {
        return make(ConfigInterface::class)->get(sprintf('socialite.providers'), []);
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