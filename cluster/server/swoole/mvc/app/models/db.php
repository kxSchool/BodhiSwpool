<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-31
 * Time: 16:55
 */

class database{
    function __construct()
    {
    }

    static function redisd($redis){
        $redisd = new redis();
        $redisd->connect($redis['hostname'], $redis['port']);
        return $redisd;
    }

    static function con($config){
        $db=new mysqli($config->hostname, $config->username, $config->password, $config->database, $config->port); //连接数据库
        //设置查询结果编码
        $db->set_charset($config->charset);
        return $db;
    }

    static  function cluster_slave($cluster){
        $i=0;
        $slaveId=floor(rand(0, ceil($cluster->db_slaves)+1));
        foreach ($cluster->db_slaves as $db_slave) {
            if ($slaveId==$i){
                $slave=$db_slave;
            }
            $i=$i+1;
        }
        if($slave){
            $db=new mysqli($slave->hostname, $slave->username, $slave->password, $slave->database, $slave->port); //连接数据库
            //设置查询结果编码
            $db->set_charset($slave->charset);
        }
        return $db;
    }

    static function cluster_master($cluster){
        $master = $cluster->db_master;
        $db=new mysqli($master->hostname, $master->username, $master->password, $master->database, $master->port); //连接数据库
        //设置查询结果编码
        $db->set_charset($master->charset);
        return $db;
    }

    static function query($cluster,$sql){
        if (strpos($sql,"select ")==0){
            $db=database::cluster_slave($cluster);
        }else{
            $db=database::cluster_master($cluster);
        }
        $result=$db->query($sql);
        $rs=$result->fetch_assoc();
        return $rs;
    }

    static function  close($db){
        //关闭连接
        mysqli_close($db);
    }
}