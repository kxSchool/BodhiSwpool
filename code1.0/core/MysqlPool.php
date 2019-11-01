<?php
/**
 * 连接池封装.
 * User: user
 * Date: 2018/9/1
 * Time: 13:36
 */
class MysqlPool
{
    protected $pool;
     private $config = [
        'max_num'   => 100,
        'mysql'     =>[
            'host'      => '20.10.1.51',
            'port'      => 3306,
            'user'      => 'root',
            'password'  => 'newlife',
            'database'  => 'qcloud_ml',
            ]
    ];

    function __construct()
    {
        $this->pool = new SplQueue;
    }

    function put($mysql)
    {
        $this->pool->push($mysql);
    }

    function get()
    {
        //有空闲连接
        if (count($this->pool) > 0) {
            return $this->pool->pop();
        }
        //无空闲连接，创建新连接
        $mysql = new Swoole\Coroutine\MySQL();
        $mysql->connect([
            'host' => $this->config['mysql']['host'],
            'port' => $this->config['mysql']['port'],
            'user' => $this->config['mysql']['user'],
            'password' => $this->config['mysql']['password'],
            'database' => $this->config['mysql']['database'],
        ]);
        if ($res == false) {
            //throw new RuntimeException("failed to connect mysql server");
        }
        
        return $mysql;
    }
}