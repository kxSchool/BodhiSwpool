<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-23
 * Time: 14:13
 */

class config
{
    //集群配置缓存
    const redis = array(
        "master" => [
            'hostname' => '172.17.0.1',
            'port' => 6379
        ],
        "slaves" => [
            [
                'server' => 'slave',
                'hostname' => '172.17.0.2',
                'port' => 6379
            ],
            [
                'server' => 'slave',
                'hostname' => '172.17.0.3',
                'port' => 6379
            ],
            [
                'server' => 'slave',
                'hostname' => '172.17.0.4',
                'port' => 6379
            ]
        ]);

    //集群配置缓存
    const memcache = [
            "master" => [
                'hostname' => '172.17.0.1',
                'port' => 11211,
            ], 
            "slaves" =>[
                [
                    'server' => 'slave',
                    'hostname' => '172.17.0.2',
                    'port' => 11211,
                ],
                [
                    'server' => 'slave',
                    'hostname' => '172.17.0.3',
                    'port' => 11211,
                ],
                [
                    'server' => 'slave',
                    'hostname' => '172.17.0.4',
                    'port' => 11211,
                ]
            ]
    ];

    const mysql = [
            "master" => [
                'server' => 'master',
                'hostname' => '172.17.0.1',
                'port' => 3306,
                'database' => 'test',
                'username' => 'root',
                'password' => '123456',
                'charset' => 'utf8',
                'mPort' => 11211,
            ], 
            "slaves" => [
                [
                    'server' => 'slave',
                    'hostname' => '172.17.0.2',
                    'port' => 3306,
                    'database' => 'test',
                    'username' => 'root',
                    'password' => '123456',
                    'charset' => 'utf8',
                    'mPort' => 11211,
                ],
                [
                    'server' => 'slave',
                    'hostname' => '172.17.0.3',
                    'port' => 3306,
                    'database' => 'test',
                    'username' => 'root',
                    'password' => '123456',
                    'charset' => 'utf8',
                    'mPort' => 11211,
                ],
                [
                    'server' => 'slave',
                    'hostname' => '172.17.0.4',
                    'port' => 3306,
                    'database' => 'test',
                    'username' => 'root',
                    'password' => '123456',
                    'charset' => 'utf8',
                    'mPort' => 11211,
                ]
            ]
    ];


    //laravel项目路径
    const env = "/data/laravel5245/";
    const laravel_env = "172.17.0.2";
    const laravel_port = 11211;
    const api_lock_timeout = 60;
    const api_lock_list = 100;
}
