<?php

namespace Cblink\Hyperf\Socialite;

use Cblink\Hyperf\Socialite\Contracts\SocialiteInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                SocialiteInterface::class => SocialiteManager::class,
            ],
            // 组件默认配置文件，即执行命令后会把 source 的对应的文件复制为 destination 对应的的文件
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'socialite config files.', // 描述
                    // 建议默认配置放在 publish 文件夹中，文件命名和组件名称相同
                    'source' => __DIR__ . '/../publish/socialite.php',  // 对应的配置文件路径
                    'destination' => BASE_PATH . '/config/autoload/socialite.php', // 复制为这个路径下的该文件
                ],
            ],
        ];
    }
}
