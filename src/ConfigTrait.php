<?php

namespace Cblink\Hyperf\Socialite;

use Hyperf\Utils\Arr;

trait ConfigTrait
{
    /**
     * @var array
     */
    protected array $config = [];

    /**
     * @param array $config
     * @param bool $cover
     * @return $this
     */
    public function setConfig(array $config = [], bool $cover = true)
    {
        if (!$cover) {
            $config = array_merge($this->config, $config);
        }

        $this->config = $config;

        return $this;
    }

    /**
     * @param $key
     * @param null $default
     * @return array|\ArrayAccess|mixed
     */
    public function getConfig($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * @return array|\ArrayAccess|mixed
     */
    public function getClientId()
    {
        return $this->getConfig('client_id');
    }

    /**
     * @return array|\ArrayAccess|mixed
     */
    public function getClientSecret()
    {
        return $this->getConfig('client_secret');
    }

    /**
     * @return array|\ArrayAccess|mixed
     */
    public function getRedirectUrl()
    {
        return $this->getConfig('redirect_url');
    }

}