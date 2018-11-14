<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CodingOpenApi\Enterprise\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class RefreshTokenChanged.
 *
 * @author caikeal <caikeal@qq.com>
 */
class RefreshTokenChanged
{
    use SerializesModels;

    public $payload;

    /**
     * Create a new event instance.
     *
     * @param $payload
     * [
     * "access_token" => "*****",
     * "refresh_token" => "*****",
     * "team" => "***",
     * "expires_in" =>  "***"
     * ]
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
