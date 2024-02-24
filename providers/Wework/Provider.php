<?php

namespace HyperfSocialiteProviders\Wework;

use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Cblink\Hyperf\Socialite\Two\User;
use GuzzleHttp\RequestOptions;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'WEWORK';

    /**
     * {@inheritdoc}.
     */
    protected $scopes = ['snsapi_privateinfo'];

    /**
     * {@inheritdoc}.
     */
    public function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}.
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        $query = http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);

        return $url.'?'.$query.'#wechat_redirect';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getCodeFields($state = null)
    {
        return [
            'appid'         => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUrl(),
            'response_type' => 'code',
            'scope'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'state'         => $state,
        ];
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenUrl()
    {
        return 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo', [
            RequestOptions::QUERY => [
                'access_token' => $token,
                'code'       => $this->getCode(),
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}.
     */
    protected function mapUserToObject(array $user)
    {
        if (!array_key_exists('UserId',$user)) {
            throw new \RuntimeException('getuserinfo fail');
        }

        return (new User())->setRaw($user)->map([
            'id'       => $user['UserId'],
            'unionid'  => null,
            'nickname' => null,
            'avatar'   => null,
            'name'     => null,
            'email'    => null,
        ]);
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenFields($code)
    {
        return [
            'corpid' => $this->getClientId(),
            'corpsecret' => $this->getClientSecret(),
        ];
    }

    /**
     * {@inheritdoc}.
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            RequestOptions::QUERY => $this->getTokenFields($code),
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
