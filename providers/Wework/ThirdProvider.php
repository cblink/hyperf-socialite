<?php

namespace HyperfSocialiteProviders\Wework;

use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Cblink\Hyperf\Socialite\Two\User;
use GuzzleHttp\RequestOptions;

class ThirdProvider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'THIRD_WEWORK';

    /**
     * {@inheritdoc}.
     */
    protected $scopes = ['snsapi_base'];

    /**
     * @var string ticket
     */
    protected $ticket;

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
        return 'https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://qyapi.weixin.qq.com/cgi-bin/service/getuserinfo3rd', [
            RequestOptions::QUERY => [
                'suite_access_token' => $token,
                'code'       => $this->getCode(),
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
        return (new User())->setRaw($user)->map([
            'id'       => $user['UserId'] ?? $user['OpenId'] ?? null,
            'unionid'  => $user['open_userid'] ?? null,
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
            'suite_id' => $this->getClientId(),
            'suite_secret' => $this->getClientSecret(),
            'suite_ticket' => $this->getTicket(),
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

    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * @return array|\ArrayAccess|mixed
     */
    protected function getTicket()
    {
        return $this->ticket;
    }
}
