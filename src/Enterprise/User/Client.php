<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Enterprise\User;

use CodingOpenApi\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * 获取当前用户的信息.
     *
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function current()
    {
        return $this->httpGet('/api/account/current_user');
    }

    /**
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function currentMobile()
    {
        return $this->httpGet('/api/account/phone/info');
    }

    /**
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function currentEmail()
    {
        return $this->httpGet('/api/account/email');
    }

    /**
     * @param $username
     *
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function info($username)
    {
        return $this->httpGet('/api/account/key/'.$username);
    }
}
