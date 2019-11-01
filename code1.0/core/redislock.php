<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-31
 * Time: 16:55
 */

class RedLock
{
    private static $_instance ;
    private static $_redis;
    private static $_server;
    private function __construct()
    {
        self::$_redis =  new Redis();
        self::$_redis ->connect(self::$_server['hostname'], self::$_server['port']);
    }
    public static function getInstance()
    {

        if(self::$_instance instanceof self)
        {
            return self::$_instance;
        }
        return self::$_instance = new  self();
    }


    static function set($key,$expTime,$count)
    {
        //print_r("加锁".$key.PHP_EOL);
        for ($i=1;$i<=$count;$i++){
            //初步加锁
            $isLock = self::$_redis->setnx('API_'.$key.'_'.$i,time()+$expTime);
            if($isLock)
            {
                return 'API_'.$key.'_'.$i;
            }
            else
            {
                //加锁失败的情况下。判断锁是否已经存在，如果锁存在切已经过期，那么删除锁。进行重新加锁
                $val = self::$_redis->get('API_'.$key.'_'.$i);
                if($val&&$val<time())
                {
                    self::$_redis->del('API_'.$key.'_'.$i);
                }
            }
        }

    }

    static function blackList($redis,$ip){
        self::$_server=$redis;
        RedLock::getInstance();
        //self::$_redis->sAdd('blackList','127.0.0.1');
        $is=self::$_redis->sismember('blackList',$ip);
        if($is==false){
            return $is;
        }else{
            print_r("blackList:".$is.PHP_EOL);
            Swoole\Coroutine::sleep(10000);
            
            
        }
        
        
    }

    static function  waitLock($redis,$key,$apiList){
        self::$_server=$redis;
        $ip=$apiList['ip'];
        RedLock::blackList($redis,$ip);
        if(self::$_redis->hExists('APIlist_'.$key[0].'_'.$key[1], $ip)){
            self::$_redis->hIncrBy('APIlist_'.$key[0].'_'.$key[1], $ip, 1);
        }else{
            self::$_redis->hSet('APIlist_'.$key[0].'_'.$key[1], $ip, '1');
        }

        while (true) {
            $lock=self::set($apiList[$key[0]][$key[1]]['key'],$apiList[$key[0]][$key[1]]['timeout'],$apiList[$key[0]][$key[1]]['count']);
            if ($lock) {
                print_r("加锁成功".time().":".$lock.PHP_EOL);
                while (true) {
                    $lockapi = self::set($apiList['api']['key'], $apiList['api']['timeout'], $apiList['api']['count']);
                    if ($lockapi) {
                        print_r("加锁成功".time().":".$lockapi.PHP_EOL);
                        return [$lock, $lockapi,$key];
                    }else{
                        Swoole\Coroutine::sleep(0.2);
                    }
                }
            } else {
                Swoole\Coroutine::sleep(0.2);
            }
        }
    }


    static function del($redis,$key,$apiList)
    {
        self::$_server=$redis;
        RedLock::getInstance();
        $ip=$apiList['ip'];
        if(self::$_redis->hExists('APIlist_'.$key[2][0].'_'.$key[2][1], $ip)){
            print_r('APIlist_'.$key[2][0].'_'.$key[2][1]."减1".PHP_EOL);
            self::$_redis->hIncrBy('APIlist_'.$key[2][0].'_'.$key[2][1], $ip, -1);
        }
        print_r("删API锁成功".time().":".$key[0].PHP_EOL);
        print_r("删主锁成功".time().":".$key[1].PHP_EOL);
        self::$_redis->del($key);
    }

}