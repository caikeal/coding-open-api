<?php

/*
 * This file is part of the caikeal/coding-open-api.
 * (c) caikeal <caikeal@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    /*
     * 默认配置，将会合并到各模块中
     */
    'defaults' => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         */
        'response_type' => 'array',
        /*
         * 使用 Laravel 的缓存系统
         */
        'use_laravel_cache' => true,

        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => env('CODING_OPEN_API_LOG_LEVEL', 'debug'),
            'file' => env('CODING_OPEN_API_LOG_FILE', storage_path('logs/coding_open_api.log')),
        ],

        /*
         * 配置请求协议
         */
        'protocol' => 'https',

        /*
         * 配置请求主域名
         */
        'main_domain' => 'coding.net',
    ],
    'enterprise' => [
        'default' => [
            'client_id' => env('CODING_ENTERPRISE_CLIENT_ID', 'your-client-id'),         // AppID
            'client_secret' => env('CODING_ENTERPRISE_CLIENT_SECRET', 'your-client-secret'), // AppSecret
            'scope' => env('CODING_ENTERPRISE_SCOPE', 'all'),
            'pre_auth_redirect_uri' => env('CODING_ENTERPRISE_REDIRECT_URI', 'your-redirect-uri'),
        ],
    ],
];
