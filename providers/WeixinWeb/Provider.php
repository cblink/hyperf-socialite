<?php

namespace HyperfSocialiteProviders\WeixinWeb;

use GuzzleHttp\RequestOptions;
use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Cblink\Hyperf\Socialite\Two\User;
use Hyperf\Collection\Arr;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'WEIXINWEB';

    /**
     * @var string
     */
    protected $openId;

    /**
     * {@inheritdoc}.
     */
    protected $scopes = ['snsapi_login'];

    /**
     * set Open Id.
     *
     * @param string $openId
     */
    public function setOpenId($openId)
    {
        $this->openId = $openId;
    }

    /**
     * {@inheritdoc}.
     */
    public function getAuthUrl($state)
    {
        //return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/qrconnect', $state);
        return $this->buildAuthUrlFromBase($this->getConfig(
            'auth_base_uri',
            'https://open.weixin.qq.com/connect/qrconnect'
        ), $state);
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
            'redirect_uri'  => $this->getRedirectUrl(),
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
        return 'https://api.weixin.qq.com/sns/oauth2/access_token';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.weixin.qq.com/sns/userinfo', [
            RequestOptions::QUERY => [
                'access_token' => $token,
                'openid'       => $this->openId,
                'lang'         => 'zh_CN',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}.
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => Arr::get($user, 'openid'),
            'unionid'  => Arr::get($user, 'unionid'),
            'nickname' => $user['nickname'],
            'avatar'   => $user['headimgurl'],
            'name'     => null,
            'email' => null,
        ]);
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenFields($code)
    {
        return [
            'appid' => $this->getClientId(),
            'secret' => $this->getClientSecret(),
            'code'  => $code, 'grant_type' => 'authorization_code',
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

        $this->credentialsResponseBody = json_decode((string) $response->getBody(), true);
        $this->openId = $this->credentialsResponseBody['openid'];

        //return $this->parseAccessToken($response->getBody());
        return $this->credentialsResponseBody;
    }

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['auth_base_uri'];
    }
}