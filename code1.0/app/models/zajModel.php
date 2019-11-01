<?php

/**

 * Created by PhpStorm.

 * User: zhang

 * Date: 2019-06-06

 * Time: 15:35

 */



class zaj extends model

{



    static function audit($device, $bundleid, $ver,$Redispool)

    {

        $device = trim(strtolower($device));

        if ($device == 'iphone' || $device == 'ipad' || $device == 'ios' || $device ==

            'appstore')

            $device = 'appstore';

        if ($device == 'android' || $device == 'google')

            $device = 'google';

        if ($device == 'MyCard' || $device == 'mycard')

            $device = 'mycard';

        //
        $redis = $Redispool->get();

        //

        if ($redis === false) {

            return "ERROR";

        }

        $key=md5($device.$bundleid.$ver);

        $result = $redis->hget('game_audit',$key);

        if ($result){

            $auditSwitch=1;

        }else{

            $auditSwitch=0;

        }

        $hw_key='hw-'.md5($bundleid.$ver);

        $hw_result = $redis->hget('hw_audit',$hw_key);

        if ($hw_result){

            $hw_result=1;

        }else{

            $hw_result=0;

        }

        $result=json_encode(array("ret"=>0,"audit"=>$auditSwitch,"hw_audit"=>$hw_result));

        $Redispool->put($redis);

        return $result;

    }



    static function audit_init($Mysqlpool,$Redispool)

    {

        //浠庤繛鎺ユ睜涓幏鍙栦竴涓狹ysql鍗忕▼瀹㈡埛绔?
        $mysql = $Mysqlpool->get(); 

        //杩炴帴澶辫触

        if ($mysql === false) {

            return "ERROR";

        }

        $res = $mysql->query('select * from game_audit'); //棰勫鐞?

        $hwres = $mysql->query("select * from yly_config_list WHERE    `status` =0  and `tag` = 'hw_audit'");

        $Mysqlpool->put($mysql); 

        

        

        

        

        //浠庤繛鎺ユ睜涓幏鍙栦竴涓猂edis鍗忕▼瀹㈡埛绔?
        $redis = $Redispool->get();

        //杩炴帴澶辫触

        if ($redis === false) {

            return "ERROR";

        }

        

        //閫氱敤瀹℃牳妯″紡

        $redis->hdel('game_audit');

        foreach ($res as $k=>$v){

            $key=md5($v['platform'].$v['bundleid'].$v['version']);

            $results[]=array($key=>$v);

            $redis->hset('game_audit',$key,json_encode($v));

        }

        

        //鍗庝负瀹℃牳妯″紡

        $redis->hdel('hw_audit');

        foreach ($hwres as $k=>$v){

            $ver=$v['value'];

            $ext1=$v['ext1'];

            $package=json_decode($v['package']);

            foreach ($package as $hwk=>$hwv){

                if ($hwv==1){

                    print_r($hwk.PHP_EOL);

                    $key='hw-'.md5($hwk.$ver);

                    $redis->hset('hw_audit',$key,$ext1);

                }

            }

                

        }

        

        

        $Redispool->put($redis);

        

        $results=json_encode($results);

        return $results;

    }





}

