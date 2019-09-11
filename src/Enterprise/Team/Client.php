<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Enterprise\Team;

use CodingOpenApi\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function info()
    {
        return $this->httpGet('/api/team/'.$this->app['config']['team'].'/get');
    }

    /**
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function user()
    {
        return $this->httpGet('/api/team/'.$this->app['config']['team'].'/members', ['page' => 1, 'pageSize' => 999999]);
    }

    /**@return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function payInfo()
    {
        return $this->httpGet('/api/enterprise/'.$this->app['config']['team']);
    }
}
