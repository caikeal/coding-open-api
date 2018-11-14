# Install
`composer install caikeal/coding-open-api`

# Usage
```php
...
use CodingOpenApi\LaravelCoding\Facade;

...

public function test () {
    return Facade::enterprise('ksbaozi')->access_token->getToken();
}
```