<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Enterprise;

use CodingOpenApi\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author overtrue <i@overtrue.me>
 *
 * @property \CodingOpenApi\Enterprise\Auth\AccessToken  $access_token
 * @property \CodingOpenApi\Enterprise\Auth\RefreshToken $refresh_token
 * @property \CodingOpenApi\Enterprise\Base\Client       $base
 * @property \CodingOpenApi\Enterprise\User\Client       $user
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Auth\ServiceProvider::class,
        Base\ServiceProvider::class,
        User\ServiceProvider::class,
    ];

    /**
     * @param $team
     * @param string $refreshToken
     *
     * @return Application
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function initTeam($team, $refreshToken = ''): Application
    {
        $this->defaultConfig = [
            'team' => $team,
        ];

        $refreshToken && $this->refresh_token->setToken($team, $refreshToken);

        return $this;
    }
}
