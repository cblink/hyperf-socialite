<?php

namespace HyperfSocialiteProviders\Wework;

use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Cblink\Hyperf\Socialite\Two\User;
use GuzzleHttp\RequestOptions;
use Hyperf\Collection\Arr;

class ThirdQrProvider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'THIRD_WEWORKQR';

    /**
     * @var string ticket
     */
    protected $ticket;

    /**
     * {@inheritdoc}.
     */
    public function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.work.weixin.qq.com/wwopen/sso/3rd_qrConnect', $state);
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
            'appid'         => $this->getClientId(),
            'redirect_uri'  => $this->getRedirectUrl(),
            'usertype'      => 'member',
            'state'         => $state,
            'lang'          => 'zh',
        ];
    }

    /**
     * {@inheritdoc}.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->request('POST','https://qyapi.weixin.qq.com/cgi-bin/service/get_login_info', [
            RequestOptions::JSON => [
                'auth_code'    => $this->getCode(),
            ],
            RequestOptions::QUERY => [
                'access_token' => $token,
            ],
        ]);

        $user = json_decode((string) $response->getBody(), true);

        return $user;
    }

    /**
     * {@inheritdoc}.
     */
    protected function mapUserToObject(array $user)
    {
        if (!array_key_exists('user_info', $user) || !array_key_exists('userid',$user['user_info'])) {
            throw new \RuntimeException('getuserinfo fail:' . json_encode($user));
        }

        return (new User())->setRaw($user)->map([
            'id'       => $user['user_info']['userid'],
            'unionid'  => $user['user_info']['open_userid'] ?? null,
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
            'provider_secret' => $this->getClientSecret(),
        ];
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenUrl()
    {
        return 'https://qyapi.weixin.qq.com/cgi-bin/service/get_provider_token';
    }

    /**
     * {@inheritdoc}.
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::JSON => $this->getTokenFields($code),
        ]);

        $body = json_decode((string) $response->getBody(), true);

        if (!array_key_exists('provider_access_token', $body)) {
            throw new \RuntimeException('get token fail:' . json_encode($body));
        }

        return $body;
    }

    /**
     * Get the access token from the token response body.
     *
     * @param array $body
     *
     * @return string
     */
    protected function parseAccessToken($body)
    {
        return Arr::get($body, 'provider_access_token');
    }
}
