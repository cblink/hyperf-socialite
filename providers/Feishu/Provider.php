<?php

namespace HyperfSocialiteProviders\Feishu;


use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Cblink\Hyperf\Socialite\Two\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'FEISHU';

    /**
     * {@inheritdoc}.
     */
    public function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.feishu.cn/open-apis/authen/v1/index', $state);
    }

    /**
     * {@inheritdoc}.
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        $query = http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);

        return $url.'?'.$query;
    }

    /**
     * {@inheritdoc}.
     */
    protected function getCodeFields($state = null)
    {
        return [
            'app_id'         => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUrl(),
        ];
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenUrl()
    {
        return 'https://open.feishu.cn/open-apis/auth/v3/app_access_token/internal';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->post('https://open.feishu.cn/open-apis/authen/v1/access_token', [
            'json' => [
                'grant_type' => 'authorization_code',
                'code'       => $this->getCode(),
            ],
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $token)
            ]
        ]);

        $user = json_decode($response->getBody(), true);

        $this->logger()->info('request feishu', [
            'code' => $this->getCode(),
            'response' => $user
        ]);

        return $user['data'];
    }

    /**
     * {@inheritdoc}.
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['open_id'],
            'unionid'  => $user['union_id'] ?? null,
            'nickname' => $user['name'] ?? null,
            'avatar'   => $user['avatar_big'] ?? null,
            'name'     => $user['name'] ?? null,
            'email'    => $user['email'] ?? null,
            'mobile'   => $user['mobile'] ?? null,
            'token'   => $user['access_token'] ?? null,
            'refreshToken'   => $user['refresh_token'] ?? null,
            'expiresIn'   => $user['refresh_expires_in'] ?? null,
            'tenant_key'   => $user['tenant_key'] ?? null,
        ]);
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenFields($code)
    {
        return [
            'app_id' => $this->getClientId(),
            'app_secret' => $this->getClientSecret(),
        ];
    }

    /**
     * {@inheritdoc}.
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'json' => $this->getTokenFields($code),
        ]);

        $body = json_decode($response->getBody(), true);

        return ['access_token' => $body['app_access_token']];
    }
}