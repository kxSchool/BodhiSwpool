<?php
/**
 * 连接池封装.
 * User: user
 * Date: 2018/9/1
 * Time: 13:36
 */
class RedisPool
{
    protected $pool;

    function __construct()
    {
        $this->pool = new SplQueue;
    }

    function put($redis)
    {
        $this->pool->push($redis);
    }

    function get()
    {
        //有空闲连接
        if (count($this->pool) > 0) {
            return $this->pool->pop();
        }

        //无空闲连接，创建新连接
        $redis = new Swoole\Coroutine\Redis();
        $res = $redis->connect(config::redis['master']['hostname'], config::redis['master']['port']);
        if ($res == false) {
            return false;
        } else {
            return $redis;
        }
    }
}
