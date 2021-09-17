<h1 align="center"> hyperf-socialite </h1>


## About
cblink/hyperf-socialite 组件衍生于 laravel/socialite 组件的，我们对它进行了一些改造，大部分功能保持了相同。在这里感谢一下 Laravel 开发组，实现了如此强大好用的社会化登陆组件。


## Installing

```shell

# 安装
composer require cblink/hyperf-socialite -vvv

# 创建配置文件
php bin/hyperf.php vendor:publish cblink/hyperf-socialite

```

## Configure

配置文件位于 `config/autoload/socialite.php`，如文件不存在可自行创建

```php
<?php

return [
    'facebook' => [
        'client_id' => '',
        'client_secret' => '',
        // 其他provider中需要使用的配置
        // ...
    ]   
    // qq,weixin...    
];

```


## Usage

组件已经提供了许多已支持的社会化登陆组件，只需要将它配置到 `config/autoload/listeners.php` 中即可。

```php

return [
    HyperfSocialiteProviders\Facebook\FacebookExtendSocialite::class,
];

```

控制器中使用
```php
<?php

use Cblink\Hyperf\Socialite\Contracts\SocialiteInterface;

class Controller 
{
    
    /**
    * @param SocialiteInterface $socialite
     * @return \Hyperf\HttpServer\Contract\ResponseInterface
     */
    public function redirectToProvider(SocialiteInterface $socialite)
    {
        // 重定向跳转
       $redirect = $socialite->driver('facebook')->redirect();
       
       // 使用新的配置跳转
       $socialite->driver('facebook')->setConfig([
            'client_id' => 'xxx',
            'client_secret' => 'xxxx',
       ])  
       
       return $redirect; 
    }
    
    /**
    * @param SocialiteInterface $socialite
    */
    public function handleProviderCallback(SocialiteInterface $socialite)
    {
        // 获取用户信息
       $user = $socialite->driver('facebook')->user();
       
       //
       // $user->token;
    }


}
```

### 支持的列表

|  支持应用   | 驱动名称  |
|  ----  | ----  |
| 微博  | weibo |
| QQ  | qq |
| Facebook  | facebook |
| Instagram  | instagram |
| YouTube | youtube |
| 飞书自建应用  | feishu |
| 微信公众号 | weixin |
| 微信PC网站登陆 | weixinweb |
| 微信开放平台代公众号授权 | wechat_service_account |


## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/cblink/hyperf-socialite/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/cblink/hyperf-socialite/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT