<?php

namespace HyperfSocialiteProviders\LaravelPassport;

use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Cblink\Hyperf\Socialite\Two\User;
use GuzzleHttp\RequestOptions;
use Hyperf\Collection\Arr;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'LARAVELPASSPORT';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [''];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return [
            'host',
            'authorize_uri',
            'token_uri',
            'userinfo_uri',
            'userinfo_key',
            'user_id',
            'user_nickname',
            'user_name',
            'user_email',
            'user_avatar',
        ];
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     *
     * @return string
     */
    public function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getLaravelPassportUrl('authorize_uri'), $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return $this->getLaravelPassportUrl('token_uri');
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param string $token
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->getLaravelPassportUrl('userinfo_uri'), [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return (array) json_decode((string) $response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param array $user
     *
     * @return User
     */
    protected function mapUserToObject(array $user)
    {
        $key = $this->getConfig('userinfo_key', null);
        $data = is_null($key) === true ? $user : Arr::get($user, $key, []);

        return (new User())->setRaw($data)->map([
            'id'       => $this->getUserData($data, 'id'),
            'nickname' => $this->getUserData($data, 'nickname'),
            'name'     => $this->getUserData($data, 'name'),
            'email'    => $this->getUserData($data, 'email'),
            'avatar'   => $this->getUserData($data, 'avatar'),
        ]);
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param string $code
     *
     * @return array
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    protected function getLaravelPassportUrl($type)
    {
        return rtrim($this->getConfig('host'), '/').'/'.ltrim(($this->getConfig($type, Arr::get([
                'authorize_uri' => 'oauth/authorize',
                'token_uri'     => 'oauth/token',
                'userinfo_uri'  => 'api/user',
            ], $type))), '/');
    }

    protected function getUserData($user, $key)
    {
        return Arr::get($user, $this->getConfig('user_'.$key, $key));
    }
}