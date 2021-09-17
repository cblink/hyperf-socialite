<?php

namespace Cblink\Hyperf\Socialite;

use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use InvalidArgumentException;

class SocialiteManager extends Manager
{

    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Socialite driver was specified.');
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