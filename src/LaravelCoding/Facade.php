<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\LaravelCoding;

use Illuminate\Support\Facades\Facade as LaravelFacade;

/**
 * Class Facade.
 *
 * @author caikeal <caikeal@caikeal.com>
 */
class Facade extends LaravelFacade
{
    /**
     * 默认为 Server.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'coding.enterprise';
    }

    /**
     * @return \CodingOpenApi\Enterprise\Application
     */
    public static function enterprise($team, $refreshToken = '', $name = '')
    {
        $codingEnterprise = $name ? app('coding.enterprise.'.$name) : app('coding.enterprise');

        return $codingEnterprise->initTeam($team, $refreshToken);
    }
}
