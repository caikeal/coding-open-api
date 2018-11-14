<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Enterprise\Auth;

use CodingOpenApi\Kernel\AccessToken as BaseAccessToken;

/**
 * Class AuthorizerAccessToken.
 *
 * @author caikeal <caikeal@qq.com>
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected $endpointToGetToken = '/api/oauth/access_token';

    /**
     * @return array
     */
    protected function getCredentials(): array
    {
        return [
            'client_id' => $this->app['config']['client_id'],
            'client_secret' => $this->app['config']['client_secret'],
            'refresh_token' => $this->app['refresh_token']->getToken()['refresh_token'],
            'grant_type' => 'refresh_token',
            'scope' => 'all',
        ];
    }
}
