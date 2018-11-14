<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\LaravelCoding;

use CodingOpenApi\Enterprise\Application as Enterprise;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * Class ServiceProvider.
 *
 * @author caikeal <caikeal@qq.com>
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Boot the provider.
     */
    public function boot()
    {
    }

    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/config.php');
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('coding.php')], 'laravel-coding');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('coding');
        }
        $this->mergeConfigFrom($source, 'coding');
    }

    /**
     * Register the provider.
     */
    public function register()
    {
        $this->setupConfig();
        $apps = [
            'enterprise' => Enterprise::class,
        ];
        foreach ($apps as $name => $class) {
            if (empty(config('coding.'.$name))) {
                continue;
            }
            if (!empty(config('coding.'.$name.'.client_id'))) {
                $accounts = [
                    'default' => config('coding.'.$name),
                ];
                config(['coding.'.$name.'.default' => $accounts['default']]);
            } else {
                $accounts = config('coding.'.$name);
            }
            foreach ($accounts as $account => $config) {
                $this->app->singleton("coding.{$name}.{$account}", function ($laravelApp) use ($name, $account, $config, $class) {
                    $app = new $class(array_merge(config('coding.defaults', []), $config));
                    if (config('coding.defaults.use_laravel_cache')) {
                        $app['cache'] = new CacheBridge($laravelApp['cache.store']);
                    }
                    $app['request'] = $laravelApp['request'];

                    return $app;
                });
            }
            $this->app->alias("coding.{$name}.default", 'coding.'.$name);
            $this->app->alias('coding.'.$name, $class);
        }
    }
}
