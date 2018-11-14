<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Kernel\Providers;

use CodingOpenApi\Kernel\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ConfigServiceProvider.
 *
 * @author caikeal <caikeal@qq.com>
 */
class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['config'] = function ($app) {
            return new Config($app->getConfig());
        };
    }
}
