<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Enterprise\Base;

use CodingOpenApi\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author caikeal <caikeal@qq.com>
 */
class Client extends BaseClient
{
    /**
     * Create pre-authorization code.
     *
     * @return string
     */
    public function createPreAuthorizationCode(): string
    {
        $params = [
            'client_id' => $this->app['config']['client_id'],
            'redirect_uri' => $this->app['config']['pre_auth_redirect_uri'],
            'response_type' => 'code',
            'scope' => $this->app['config']['scope'], // 用逗号分割
        ];

        return trim($this->app['config']['http']['base_uri'], '/').'/oauth_authorize.html?'.http_build_query($params);
    }
}
