<?php

namespace Cblink\Hyperf\Socialite\One;

use Hyperf\Contract\SessionInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Collection;
use InvalidArgumentException;
use League\OAuth1\Client\Credentials\TokenCredentials;
use Cblink\Hyperf\Socialite\Contracts\Provider as ProviderContract;
use Hyperf\HttpServer\Contract\RequestInterface;
use League\OAuth1\Client\Server\Server;

abstract class AbstractProvider implements ProviderContract
{
    /**
     * The HTTP request instance.
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * The HTTP session instance.
     *
     * @var SessionInterface|null
     */
    protected $session;

    /**
     * The OAuth server implementation.
     *
     * @var \League\OAuth1\Client\Server\Server
     */
    protected $server;

    /**
     * A hash representing the last requested user.
     *
     * @var string
     */
    protected $userHash;

    /**
     * Create a new provider instance.
     *
     * @param RequestInterface $request
     * @param \League\OAuth1\Client\Server\Server $server
     * @param SessionInterface|null $session
     */
    public function __construct(RequestInterface $request, Server $server, SessionInterface $session = null)
    {
        $this->server = $server;
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return ResponseInterface
     * @throws \League\OAuth1\Client\Credentials\CredentialsException
     */
    public function redirect()
    {
        $this->session()->put(
            'oauth.temp', $temp = $this->server->getTemporaryCredentials()
        );

        return make(ResponseInterface::class)->redirect($this->server->getAuthorizationUrl($temp));
    }

    /**
     * Get the User instance for the authenticated user.
     *
     * @return User
     *
     * @throws InvalidArgumentException
     */
    public function user()
    {
        if (! $this->hasNecessaryVerifier()) {
            throw new InvalidArgumentException('Invalid request. Missing OAuth verifier.');
        }

        $token = $this->getToken();

        $user = $this->server->getUserDetails(
            $token, $this->shouldBypassCache($token->getIdentifier(), $token->getSecret())
        );

        $instance = (new User)->setRaw($user->extra)
            ->setToken($token->getIdentifier(), $token->getSecret());

        return $instance->map([
            'id' => $user->uid,
            'nickname' => $user->nickname,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->imageUrl,
        ]);
    }

    /**
     * Get a Social User instance from a known access token and secret.
     *
     * @param  string  $token
     * @param  string  $secret
     * @return User
     */
    public function userFromTokenAndSecret($token, $secret)
    {
        $tokenCredentials = new TokenCredentials();

        $tokenCredentials->setIdentifier($token);
        $tokenCredentials->setSecret($secret);

        $user = $this->server->getUserDetails(
            $tokenCredentials, $this->shouldBypassCache($token, $secret)
        );

        $instance = (new User)->setRaw($user->extra)
            ->setToken($tokenCredentials->getIdentifier(), $tokenCredentials->getSecret());

        return $instance->map([
            'id' => $user->uid,
            'nickname' => $user->nickname,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->imageUrl,
        ]);
    }

    /**
     * Get the token credentials for the request.
     *
     * @return \League\OAuth1\Client\Credentials\TokenCredentials
     * @throws \League\OAuth1\Client\Credentials\CredentialsException
     */
    protected function getToken()
    {
        $temp = $this->session()->get('oauth.temp');

        if (! $temp) {
            throw new InvalidArgumentException('Missing temporary OAuth credentials.');
        }

        return $this->server->getTokenCredentials(
            $temp, $this->request->query('oauth_token'), $this->request->query('oauth_verifier')
        );
    }

    /**
     * Determine if the request has the necessary OAuth verifier.
     *
     * @return bool
     */
    protected function hasNecessaryVerifier()
    {
        return $this->request->has('oauth_token') && $this->request->has('oauth_verifier');
    }

    /**
     * Determine if the user information cache should be bypassed.
     *
     * @param  string  $token
     * @param  string  $secret
     * @return bool
     */
    protected function shouldBypassCache($token, $secret)
    {
        $newHash = sha1($token.'_'.$secret);

        if (! empty($this->userHash) && $newHash !== $this->userHash) {
            $this->userHash = $newHash;

            return true;
        }

        $this->userHash = $this->userHash ?: $newHash;

        return false;
    }

    /**
     * Set the request instance.
     *
     * @param  RequestInterface  $request
     * @return $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return SessionInterface|Collection
     */
    public function session()
    {
        return $this->session ?: new Collection();
    }
}