# Install
`composer install caikeal/coding-open-api`

# Settings
获取配置项：
`php artisan vendor:publish --provider="CodingOpenApi\LaravelCoding\ServiceProvider"`

# Usage
```php
...
use CodingOpenApi\LaravelCoding\Facade;

...

public function test () {
    return Facade::enterprise('ksbaozi')->access_token->getToken();
}
```

当用 `refresh_token` 刷新 `access_token` 时, 会同时更新原来的 `refresh_token`, 并会发出事件`CodingOpenApi\Enterprise\Events\RefreshTokenChanged`。

**注意：首次生成 `refresh_token` 是不会触发该事件的**

# Thanks
[overtrue](https://github.com/overtrue)
