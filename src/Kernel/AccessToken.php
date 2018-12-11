<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Kernel;

use CodingOpenApi\Enterprise\Events\RefreshTokenChanged;
use CodingOpenApi\Kernel\Contracts\AccessTokenInterface;
use CodingOpenApi\Kernel\Exceptions\HttpException;
use CodingOpenApi\Kernel\Exceptions\InvalidArgumentException;
use CodingOpenApi\Kernel\Traits\HasHttpRequests;
use CodingOpenApi\Kernel\Traits\InteractsWithCache;
use Illuminate\Support\Facades\Event;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AccessToken.
 *
 * @author caikeal <caikeal@qq.com>
 */
abstract class AccessToken implements AccessTokenInterface
{
    use HasHttpRequests, InteractsWithCache;
    /**
     * @var \Pimple\Container
     */
    protected $app;
    /**
     * @var string
     */
    protected $requestMethod = 'GET';
    /**
     * @var string
     */
    protected $endpointToGetToken;
    /**
     * @var string
     */
    protected $queryName;
    /**
     * @var array
     */
    protected $token;
    /**
     * @var int
     */
    protected $safeSeconds = 1000;
    /**
     * @var string
     */
    protected $tokenKey = 'access_token';
    /**
     * @var string
     */
    protected $cachePrefix = 'coding_open_api.kernel.access_token.';
    /**
     * @var string
     */
    protected $refreshTokenKey = 'refresh_token';

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
     * @return array
     *
     * @throws Exceptions\InvalidConfigException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getRefreshedToken(): array
    {
        return $this->getToken(true);
    }

    /**
     * @param bool $refresh
     *
     * @return array
     *
     * @throws Exceptions\InvalidConfigException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken(bool $refresh = false): array
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();
        if (!$refresh && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }
        $token = $this->requestToken($this->getCredentials(), true);
        // 更新失效 refresh_token 的 access_token, 以防同进程中使用报错
        $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 7200);

        // 更新 refresh_token 值，并更新对应 access_token
        $this->app['refresh_token']->setToken($token[$this->refreshTokenKey]);
        $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 7200);
        Event::dispatch(new RefreshTokenChanged($token));

        return $token;
    }

    /**
     * @param string $token
     * @param int    $lifetime
     *
     * @return \CodingOpenApi\Kernel\Contracts\AccessTokenInterface
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setToken(string $token, int $lifetime = 7200): AccessTokenInterface
    {
        $this->getCache()->set($this->getCacheKey(), [
            $this->tokenKey => $token,
            'expires_in' => $lifetime,
        ], $lifetime - $this->safeSeconds);

        return $this;
    }

    /**
     * @return \CodingOpenApi\Kernel\Contracts\AccessTokenInterface
     *
     * @throws Exceptions\InvalidConfigException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function refresh(): AccessTokenInterface
    {
        $this->getToken(true);

        return $this;
    }

    /**
     * @param array $credentials
     * @param bool  $toArray
     *
     * @return \Psr\Http\Message\ResponseInterface|\CodingOpenApi\Kernel\Support\Collection|array|object|string
     *
     * @throws Exceptions\InvalidConfigException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestToken(array $credentials, $toArray = false)
    {
        $response = $this->sendRequest($credentials);
        $result = json_decode($response->getBody()->getContents(), true);
        $formatted = $this->castResponseToType($response, $this->app['config']->get('response_type'));
        if (empty($result[$this->tokenKey]) && empty($result[$this->refreshTokenKey])) {
            throw new HttpException('Request access_token or refresh_token fail: '.json_encode($result, JSON_UNESCAPED_UNICODE), $response, $formatted);
        }

        return $toArray ? $result : $formatted;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $requestOptions
     *
     * @return \Psr\Http\Message\RequestInterface
     *
     * @throws Exceptions\InvalidConfigException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface
    {
        parse_str($request->getUri()->getQuery(), $query);
        $query = http_build_query(array_merge($this->getQuery(), $query));

        return $request->withUri($request->getUri()->withQuery($query));
    }

    /**
     * Send http request.
     *
     * @param array $credentials
     *
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest(array $credentials): ResponseInterface
    {
        $options = [
            ('GET' === $this->requestMethod) ? 'query' : 'json' => $credentials,
            'base_uri' => $this->app['config']->get('http.base_uri'),
        ];

        return $this->setHttpClient($this->app['http_client'])->request($this->getEndpoint(), $this->requestMethod, $options);
    }

    /**
     * @return string
     */
    protected function getCacheKey(): string
    {
        return $this->cachePrefix.md5(json_encode($this->getCredentials()));
    }

    /**
     * The request query will be used to add to the request.
     *
     * @return array
     *
     * @throws Exceptions\InvalidConfigException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function getQuery(): array
    {
        return [$this->queryName ?? $this->tokenKey => $this->getToken()[$this->tokenKey]];
    }

    /**
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function getEndpoint(): string
    {
        if (empty($this->endpointToGetToken)) {
            throw new InvalidArgumentException('No endpoint for access token request.');
        }

        return $this->endpointToGetToken;
    }

    /**
     * @return string
     */
    public function getTokenKey(): string
    {
        return $this->tokenKey;
    }

    /**
     * Credential for get token.
     *
     * @return array
     */
    abstract protected function getCredentials(): array;
}
