<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Enterprise\Auth;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author caikeal <caikeal@qq.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        !isset($app['access_token']) && $app['access_token'] = function ($app) {
            return new AccessToken($app);
        };
        !isset($app['refresh_token']) && $app['refresh_token'] = function ($app) {
            return new RefreshToken($app);
        };
    }
}
