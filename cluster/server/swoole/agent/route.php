<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-23
 * Time: 11:30
 */

$agent=new agent();
if ($path_info== '/favicon.ico') {
    return;
}

//切换本机为从库服务器
if ($path_info=="/test"){
    $output=$agent->test($redis);
}

//切换本机为从库服务器
if ($path_info=="/setLocalSlave"){
    $output=$agent->setLocalSlave($redis);
}

//删主服务器并切换本机为主库服务器
if ($path_info=="/setLocalMaster_delOld"){
    $output=$agent->setLocalMaster_delOld($redis,$laravel_env,$laravel_port);
}

//切换本机为主库服务器并改原主服务器为从服务器
if ($path_info=="/setLocalMaster_addOld"){
    $output=$agent->setLocalMaster_addOld($redis,$laravel_env,$laravel_port);
}

//获取数据库集群配置清单
if ($path_info=="/getCluster"){
    $output=$agent->getCluster($redis);
}

//更新并配置集群服务器
if ($path_info=="/setCluster"){
    $output=$agent->setCluster($redis);

}

//检测集群是否正常
if ($path_info=="/checkCluster"){
    $output=$agent->checkCluster($redis);
}

//检测本地从库是否正常
if ($path_info=="/checkLocalSlave"){
    $output=$agent->checkLocalSlave($redis);
}

//重写laravel配置文件
if($path_info=="/env_rewrite"){
	$output=$agent->env_rewrite($redis,$env);
}


//检查服务器主从状态
if ($path_info=='/status'){
    $output=$agent->status($redis);
}

//测试env文件重写
if($path_info=='/test_env'){
    $output=$agent->test_env($redis,$laravel_env,$laravel_port);
}