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
     * @param $team
     *
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function info($team)
    {
        return $this->httpGet('/api/team/'.$team.'/get');
    }

    /**
     * @param $team
     *
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function user($team)
    {
        return $this->httpGet('/api/team/'.$team.'/members');
    }

    /**
     * @param $team
     *
     * @return array|\CodingOpenApi\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function payInfo($team)
    {
        return $this->httpGet('/api/enterprise/'.$team);
    }
}
