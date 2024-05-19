<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-23
 * Time: 11:30
 */


$cluster=new clusterController();
$server = $request->server;
$path_info = $request->server['request_uri'];
if ($path_info== '/favicon.ico') {
    return;
}

//切换本机为从库服务器
if ($path_info=="/test")
    $output=$cluster->test($redis);


//切换本机为从库服务器
if ($path_info=="/setLocalSlave")
    $output=$cluster->setLocalSlave($redis);


//删主服务器并切换本机为主库服务器
if ($path_info=="/setLocalMaster_delOld")
    $output=$cluster->setLocalMaster_delOld($redis,$laravel_env,$laravel_port);


//切换本机为主库服务器并改原主服务器为从服务器
if ($path_info=="/setLocalMaster_addOld")
    $output=$cluster->setLocalMaster_addOld($redis,$laravel_env,$laravel_port);


//获取数据库集群配置清单
if ($path_info=="/getCluster")
    $output=$cluster->getCluster($redis);


//更新并配置集群服务器
if ($path_info=="/setCluster")
    $output=$cluster->setCluster($redis);



//检测集群是否正常
if ($path_info=="/checkCluster")
    $output=$cluster->checkCluster($redis);


//检测本地从库是否正常
if ($path_info=="/checkLocalSlave")
    $output=$cluster->checkLocalSlave($redis);


//重写laravel配置文件
if($path_info=="/env_rewrite")
	$output=$cluster->env_rewrite($redis,$env);



//检查服务器主从状态
if ($path_info=='/status')
    $output=$cluster->status($redis);


//测试env文件重写
if($path_info=='/test_env')
    $output=$cluster->test_env($redis,$laravel_env,$laravel_port);




