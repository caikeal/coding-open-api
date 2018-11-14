<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Enterprise\Auth;

use CodingOpenApi\Kernel\Exceptions\HttpException;
use CodingOpenApi\Kernel\Traits\HasHttpRequests;
use CodingOpenApi\Kernel\Traits\InteractsWithCache;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

class RefreshToken
{
    use HasHttpRequests, InteractsWithCache;

    protected $requestMethod = 'GET';
    /**
     * @var string
     */
    protected $tokenKey = 'refresh_token';
    /**
     * @var string
     */
    protected $cachePrefix = 'coding_open_api.kernel.refresh_token.';
    /**
     * @var string
     */
    protected $endpointToGetToken = '/api/oauth/access_token';

    /**
     * @var int
     */
    protected $safeSeconds = 1000;

    /**
     * AccessToken constructor.
     *
     * @param \Pimple\Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @param null $code
     *
     * @return array
     *
     * @throws HttpException
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken($code = null): array
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();
        if (!$code && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }
        $token = $this->requestToken($this->getCredentials($code), true);
        $this->setToken($token[$this->tokenKey], 259200);
        $this->app['access_token']->setToken($token['access_token'], $token['expires_in'] ?? 7200);

        return $token;
    }

    /**
     * @param $token
     * @param $lifetime
     *
     * @return $this
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setToken($token, $lifetime = 259200): RefreshToken
    {
        $this->getCache()->set($this->getCacheKey(), [
            $this->tokenKey => $token,
            'expires_in' => $lifetime,
        ], $lifetime - $this->safeSeconds);

        return $this;
    }

    /**
     * @param $code
     *
     * @return array
     */
    protected function getCredentials($code): array
    {
        return [
            'client_id' => $this->app['config']['client_id'],
            'client_secret' => $this->app['config']['client_secret'],
            'grant_type' => 'authorization_code',
            'code' => $code,
        ];
    }

    /**
     * @param array $credentials
     * @param bool  $toArray
     *
     * @return \Psr\Http\Message\ResponseInterface|\CodingOpenApi\Kernel\Support\Collection|array|object|string
     *
     * @throws HttpException
     * @throws \CodingOpenApi\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestToken(array $credentials, $toArray = false)
    {
        $response = $this->sendRequest($credentials);
        $result = json_decode($response->getBody()->getContents(), true);
        $formatted = $this->castResponseToType($response, $this->app['config']->get('response_type'));
        if (empty($result[$this->tokenKey])) {
            throw new HttpException('Request access_token or refresh_token fail: '.json_encode($result, JSON_UNESCAPED_UNICODE), $response, $formatted);
        }

        return $toArray ? $result : $formatted;
    }

    /**
     * Send http request.
     *
     * @param array $credentials
     *
     * @return ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest(array $credentials): ResponseInterface
    {
        $options = [
            ('GET' === $this->requestMethod) ? 'query' : 'json' => $credentials,
            'base_uri' => $this->app['config']->get('http.base_uri'),
        ];

        return $this->setHttpClient($this->app['http_client'])->request($this->endpointToGetToken, $this->requestMethod, $options);
    }

    /**
     * @return string
     */
    protected function getCacheKey(): string
    {
        $team = $this->app['config']['team'];

        return $this->cachePrefix.$team;
    }
}
