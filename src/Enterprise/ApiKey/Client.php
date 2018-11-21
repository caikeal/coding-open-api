<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Enterprise\ApiKey;

use CodingOpenApi\Kernel\BaseClient;

class Client extends BaseClient
{
    protected $prefixUri = '/api/enterprise/feie/apikey';

    /**
     * 获取当前用户的信息.
     *
     * @param array $params
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $params)
    {
        return $this->httpPost($this->prefixUri.'/insert', $params);
    }

    /**
     * @param array $params
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(array $params)
    {
        return $this->httpPutJson($this->prefixUri.'/update', $params);
    }

    /**
     * @param array $params
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(array $params)
    {
        return $this->httpDelete($this->prefixUri.'/delete', $params);
    }

    /**
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function check()
    {
        return $this->httpGet($this->prefixUri.'/check');
    }
}
