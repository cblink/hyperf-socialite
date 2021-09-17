<?php

namespace Cblink\Hyperf\Socialite\Contracts;

use Hyperf\HttpServer\Contract\ResponseInterface;

interface Provider
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return ResponseInterface
     */
    public function redirect();

    /**
     * Get the User instance for the authenticated user.
     *
     * @return User
     */
    public function user();
}