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
     * Create pre-authorization url.
     *
     * @param string $url
     * @return string
     */
    public function createPreAuthorizationUrl($url = ''): string
    {
        $mainUrl = explode('?', $this->app['config']['pre_auth_redirect_uri']);
        $query = explode('&', $mainUrl[1] ?? '');
        if ($url) {
            $query[] = 'redirect_uri='.$url;
        }
        $queryString = implode('&', $query);
        $fullUrl = $mainUrl[0] . '?' . $queryString;

        $params = [
            'client_id' => $this->app['config']['client_id'],
            'redirect_uri' => $fullUrl,
            'response_type' => 'code',
            'scope' => $this->app['config']['scope'], // 用逗号分割
        ];

        return trim($this->app['config']['http']['base_uri'], '/').'/oauth_authorize.html?'.http_build_query($params);
    }
}
