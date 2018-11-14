<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Kernel\Contracts;

use Psr\Http\Message\RequestInterface;

/**
 * Interface AuthorizerAccessToken.
 *
 * @author caikeal <caikeal@qq.com>
 */
interface AccessTokenInterface
{
    /**
     * @return array
     */
    public function getToken(): array;

    /**
     * @return \CodingOpenApi\Kernel\Contracts\AccessTokenInterface
     */
    public function refresh(): self;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $requestOptions
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface;
}
