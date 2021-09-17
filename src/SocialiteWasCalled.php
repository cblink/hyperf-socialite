<?php

namespace Cblink\Hyperf\Socialite;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use League\OAuth1\Client\Server\Server as OAuth1Server;
use Cblink\Hyperf\Socialite\One\AbstractProvider as SocialiteOAuth1AbstractProvider;
use Cblink\Hyperf\Socialite\Two\AbstractProvider as SocialiteOAuth2AbstractProvider;
use InvalidArgumentException;

class SocialiteWasCalled
{
    public const SERVICE_CONTAINER_PREFIX = 'SocialiteProviders.config.';

    /**
     * @param  string  $providerName  'meetup'
     * @param  string  $providerClass  'Your\Name\Space\ClassNameProvider' must extend
     *                              either Laravel\Socialite\Two\AbstractProvider or
     *                              Laravel\Socialite\One\AbstractProvider
     * @param  string  $oauth1Server  'Your\Name\Space\ClassNameServer' must extend League\OAuth1\Client\Server\Server
     *
     * @return void
     */
    public function extendSocialite($providerName, $providerClass, $oauth1Server = null)
    {
        $this->classExists($providerClass);

        if ($this->isOAuth1($oauth1Server)) {
            $this->classExists($oauth1Server);
            $this->classExtends($providerClass, SocialiteOAuth1AbstractProvider::class);
        }

        /* @var SocialiteManager $socialite */
        $socialite = make(SocialiteManager::class);

        $socialite->extend(
            $providerName,
            function () use ($socialite, $providerName, $providerClass, $oauth1Server) {
                $provider = $this->buildProvider($socialite, $providerName, $providerClass, $oauth1Server);
                if (defined('SOCIALITEPROVIDERS_STATELESS') && SOCIALITEPROVIDERS_STATELESS) {
                    return $provider->stateless();
                }
                return $provider;
            }
        );
    }

    /**
     * @param SocialiteManager $socialite
     * @param string                              $providerName
     * @param string                              $providerClass
     * @param null|string                         $oauth1Server
     *
     * @return SocialiteOAuth1AbstractProvider|SocialiteOAuth2AbstractProvider
     */
    protected function buildProvider(SocialiteManager $socialite, $providerName, $providerClass, $oauth1Server)
    {
        if ($this->isOAuth1($oauth1Server)) {
            return $this->buildOAuth1Provider($socialite, $providerClass, $providerName, $oauth1Server);
        }

        return $this->buildOAuth2Provider($socialite, $providerClass, $providerName);
    }

    /**
     * Build an OAuth 1 provider instance.
     *
     * @param SocialiteManager $socialite
     * @param string $providerClass must extend Laravel\Socialite\One\AbstractProvider
     * @param string $providerName
     * @param string $oauth1Server  must extend League\OAuth1\Client\Server\Server
     *
     * @return SocialiteOAuth1AbstractProvider
     */
    protected function buildOAuth1Provider(SocialiteManager $socialite, $providerClass, $providerName, $oauth1Server)
    {
        $this->classExtends($oauth1Server, OAuth1Server::class);

        $config = $this->getConfig($providerName);

        $configServer = $socialite->formatConfig($config);

        $provider = new $providerClass(
            make(RequestInterface::class), new $oauth1Server($configServer)
        );

        $provider->setConfig($config);

        return $provider;
    }


    /**
     * Build an OAuth 2 provider instance.
     *
     * @param SocialiteManager $socialite
     * @param string           $providerClass must extend Laravel\Socialite\Two\AbstractProvider
     * @param string           $providerName
     *
     * @return SocialiteOAuth2AbstractProvider
     */
    protected function buildOAuth2Provider(SocialiteManager $socialite, $providerClass, $providerName)
    {
        $this->classExtends($providerClass, SocialiteOAuth2AbstractProvider::class);

        $provider = $socialite->buildProvider($providerClass);

        $provider->setConfig($this->getConfig($providerName));

        return $provider;
    }

    /**
     * @param string $providerName
     * @return mixed|array
     */
    protected function getConfig(string $providerName)
    {
        return make(ConfigInterface::class)->get(sprintf('socialite.config.%s', $providerName), []);
    }

    /**
     * Check if a server is given, which indicates that OAuth1 is used.
     *
     * @param string $oauth1Server
     *
     * @return bool
     */
    private function isOAuth1($oauth1Server)
    {
        return ! empty($oauth1Server);
    }

    /**
     * @param string $class
     * @param string $baseClass
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    private function classExtends($class, $baseClass)
    {
        if (false === is_subclass_of($class, $baseClass)) {
            throw new InvalidArgumentException("{$class} does not extend {$baseClass}");
        }
    }

    /**
     * @param string $providerClass
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    private function classExists($providerClass)
    {
        if (! class_exists($providerClass)) {
            throw new InvalidArgumentException("{$providerClass} doesn't exist");
        }
    }

}