<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Kernel;

use CodingOpenApi\Kernel\Providers\ConfigServiceProvider;
use CodingOpenApi\Kernel\Providers\HttpClientServiceProvider;
use CodingOpenApi\Kernel\Providers\LogServiceProvider;
use CodingOpenApi\Kernel\Providers\RequestServiceProvider;
use Pimple\Container;

/**
 * Class ServiceContainer.
 *
 * @author caikeal <caikeal@qq.com>
 *
 * @property \CodingOpenApi\Kernel\Config              $config
 * @property \Symfony\Component\HttpFoundation\Request $request
 * @property \GuzzleHttp\Client                        $http_client
 * @property \Monolog\Logger                           $logger
 */
class ServiceContainer extends Container
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var array
     */
    protected $providers = [];
    /**
     * @var array
     */
    protected $defaultConfig = [];
    /**
     * @var array
     */
    protected $userConfig = [];

    /**
     * Constructor.
     *
     * @param array       $config
     * @param array       $prepends
     * @param string|null $id
     */
    public function __construct(array $config = [], array $prepends = [], string $id = null)
    {
        $this->registerProviders($this->getProviders());
        parent::__construct($prepends);
        $this->userConfig = $config;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id ?? $this->id = md5(json_encode($this->userConfig));
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $base = [
            // http://docs.guzzlephp.org/en/stable/request-options.html
            'http' => [
                'timeout' => 30.0,
                'base_uri' => $this->defaultConfig['team'] ? "{$this->userConfig['protocol']}://{$this->defaultConfig['team']}.{$this->userConfig['main_domain']}/" : "{$this->userConfig['protocol']}://{$this->userConfig['main_domain']}/",
            ],
        ];

        return array_replace_recursive($base, $this->defaultConfig, $this->userConfig);
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return array_merge([
            LogServiceProvider::class,
            ConfigServiceProvider::class,
            RequestServiceProvider::class,
            HttpClientServiceProvider::class,
        ], $this->providers);
    }

    /**
     * @param string $id
     * @param mixed  $value
     */
    public function rebind($id, $value)
    {
        $this->offsetUnset($id);
        $this->offsetSet($id, $value);
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

    /**
     * @param array $providers
     */
    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }
}
