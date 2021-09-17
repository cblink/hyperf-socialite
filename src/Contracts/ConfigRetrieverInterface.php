<?php

namespace Cblink\Hyperf\Socialite\Contracts;

interface ConfigRetrieverInterface
{
    /**
     * @param string $providerName
     * @param array  $additionalConfigKeys
     *
     * @return ConfigInterface
     */
    public function fromServices($providerName, array $additionalConfigKeys = []);
}