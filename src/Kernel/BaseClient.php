<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Kernel;

use CodingOpenApi\Kernel\Contracts\AccessTokenInterface;
use CodingOpenApi\Kernel\Http\Response;
use CodingOpenApi\Kernel\Traits\HasHttpRequests;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseClient.
 *
 * @author caikeal <caikeal@qq.com>
 */
class BaseClient
{
    use HasHttpRequests { request as performRequest; }
    /**
     * @var \CodingOpenApi\Kernel\ServiceContainer
     */
    protected $app;
    /**
     * @var \CodingOpenApi\Kernel\Contracts\AccessTokenInterface
     */
    protected $accessToken;
    /**
     * @var
     */
    protected $baseUri;

    /**
     * BaseClient constructor.
     *
     * @param \CodingOpenApi\Kernel\ServiceContainer                    $app
     * @param \CodingOpenApi\Kernel\Contracts\AccessTokenInterface|null $accessToken
     */
    public function __construct(ServiceContainer $app, AccessTokenInterface $accessToken = null)
    {
        $this->app = $app;
        $this->accessToken = $accessToken ?? $this->app['access_token'];
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array  $query
     * @param bool   $auth
     *
     * @return \Psr\Http\Message\ResponseInterface|\CodingOpenApi\Kernel\Support\Collection|array|object|string
     *
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpGet(string $url, array $query = [], $auth = true)
    {
        return $this->request($url, 'GET', ['query' => $query], false, $auth);
    }

    /**
     * POST request.
     *
     * @param string $url
     * @param array  $data
     * @param bool   $auth
     *
     * @return \Psr\Http\Message\ResponseInterface|\CodingOpenApi\Kernel\Support\Collection|array|object|string
     *
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpPost(string $url, array $data = [], $auth = true)
    {
        return $this->request($url, 'POST', ['form_params' => $data], false, $auth);
    }

    /**
     * JSON request.
     *
     * @param string       $url
     * @param string|array $data
     * @param array        $query
     * @param bool         $auth
     *
     * @return \Psr\Http\Message\ResponseInterface|\CodingOpenApi\Kernel\Support\Collection|array|object|string
     *
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpPostJson(string $url, array $data = [], array $query = [], $auth = true)
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data], false, $auth);
    }

    /**
     * PUT Request.
     *
     * @param string $url
     * @param array  $data
     * @param array  $query
     * @param bool   $auth
     *
     * @return array|Support\Collection|object|ResponseInterface|string
     *
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpPutJson(string $url, array $data = [], array $query = [], $auth = true)
    {
        return $this->request($url, 'PUT', ['query' => $query, 'json' => $data], false, $auth);
    }

    /**
     * PATCH Request.
     *
     * @param string $url
     * @param array  $data
     * @param array  $query
     * @param bool   $auth
     *
     * @return array|Support\Collection|object|ResponseInterface|string
     *
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpPatchJson(string $url, array $data = [], array $query = [], $auth = true)
    {
        return $this->request($url, 'PATCH', ['query' => $query, 'json' => $data], false, $auth);
    }

    /**
     * DELETE Request.
     *
     * @param string $url
     * @param array  $query
     * @param bool   $auth
     *
     * @return array|Support\Collection|object|ResponseInterface|string
     *
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpDelete(string $url, array $query = [], $auth = true)
    {
        return $this->request($url, 'DELETE', ['query' => $query], false, $auth);
    }

    /**
     * Upload file.
     *
     * @param string $url
     * @param array  $files
     * @param array  $form
     * @param array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface|\CodingOpenApi\Kernel\Support\Collection|array|object|string
     *
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpUpload(string $url, array $files = [], array $form = [], array $query = [])
    {
        $multipart = [];
        foreach ($files as $name => $path) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path, 'r'),
            ];
        }
        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        return $this->request($url, 'POST', ['query' => $query, 'multipart' => $multipart, 'connect_timeout' => 30, 'timeout' => 30, 'read_timeout' => 30]);
    }

    /**
     * @return AccessTokenInterface
     */
    public function getAccessToken(): AccessTokenInterface
    {
        return $this->accessToken;
    }

    /**
     * @param \CodingOpenApi\Kernel\Contracts\AccessTokenInterface $accessToken
     *
     * @return $this
     */
    public function setAccessToken(AccessTokenInterface $accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     * @param bool   $returnRaw
     * @param bool   $auth
     *
     * @return \Psr\Http\Message\ResponseInterface|\CodingOpenApi\Kernel\Support\Collection|array|object|string
     *
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $url, string $method = 'GET', array $options = [], $returnRaw = false, $auth = true)
    {
        if (empty($this->middlewares)) {
            $this->registerHttpMiddlewares();
        }

        if (!$auth) {
            $this->getHandlerStack()->remove('access_token');
        }

        $response = $this->performRequest($url, $method, $options);

        return $returnRaw ? $response : $this->castResponseToType($response, $this->app->config->get('response_type'));
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     *
     * @return \CodingOpenApi\Kernel\Http\Response
     *
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestRaw(string $url, string $method = 'GET', array $options = [])
    {
        return Response::buildFromPsrResponse($this->request($url, $method, $options, true));
    }

    /**
     * Return GuzzleHttp\ClientInterface instance.
     *
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        if (!($this->httpClient instanceof ClientInterface)) {
            $this->httpClient = $this->app['http_client'] ?? new Client();
        }

        return $this->httpClient;
    }

    /**
     * Register Guzzle middlewares.
     */
    protected function registerHttpMiddlewares()
    {
        // retry
        $this->pushMiddleware($this->retryMiddleware(), 'retry');
        // access token
        $this->pushMiddleware($this->accessTokenMiddleware(), 'access_token');
        // log
        $this->pushMiddleware($this->logMiddleware(), 'log');
    }

    /**
     * Attache access token to request query.
     *
     * @return \Closure
     */
    protected function accessTokenMiddleware()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if ($this->accessToken) {
                    $request = $this->accessToken->applyToRequest($request, $options);
                }

                return $handler($request, $options);
            };
        };
    }

    /**
     * Log the request.
     *
     * @return \Closure
     */
    protected function logMiddleware()
    {
        $formatter = new MessageFormatter($this->app['config']['http.log_template'] ?? MessageFormatter::DEBUG);

        return Middleware::log($this->app['logger'], $formatter);
    }

    /**
     * Return retry middleware.
     *
     * @return \Closure
     */
    protected function retryMiddleware()
    {
        return Middleware::retry(function (
            $retries,
            RequestInterface $request,
            ResponseInterface $response = null
        ) {
            // Limit the number of retries to 2
            if ($retries < $this->app->config->get('http.max_retries', 1) && $response && $body = $response->getBody()) {
                // Retry on server errors
                $response = json_decode($body, true);
                if (!empty($response['code']) && in_array(abs($response['code']), [1], true) && !empty($response['msg']) && !empty($response['oauth_auth_expired'])) {
                    $this->accessToken->refresh();

                    return true;
                }
            }

            return false;
        }, function () {
            return abs($this->app->config->get('http.retry_delay', 500));
        });
    }
}
