<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-23
 * Time: 11:30
 */


class clusterRoute{
    static function url($request,$t1,$apiList)
    {


        $output="";
        $cluster=new clusterController();
        $path_info = $request->server['request_uri'];

        //黑名单
        if($path_info=="/cluster/blackList")
            $output=$cluster->blackList(config::redis,$apiList['ip']);

        //获取Cpu的耗用
        if($path_info=="/cluster/getCpu")
        $output=$cluster->getCpu();

        //redis初始化
        if ($path_info=="/cluster/initCluster")
            $output=$cluster->initCluster(config::redis,config::db_master,config::db_slaves);

        //redis分布式锁
        if ($path_info=="/cluster/redLock")
            $output=$cluster->redLock(config::redis,$apiList);

        //切换本机为从库服务器
        if ($path_info=="/cluster/setLocalSlave")
            $output=$cluster->setLocalSlave(config::redis);

        //删主服务器并切换本机为主库服务器
        if ($path_info=="/cluster/setLocalMaster_delOld")
            $output=$cluster->setLocalMaster_delOld(config::redis,config::laravel_env,config::laravel_port);



        //切换本机为主库服务器并改原主服务器为从服务器
        if ($path_info=="/cluster/setLocalMaster_addOld")
            $output=$cluster->setLocalMaster_addOld(config::redis,config::laravel_env,config::laravel_port);



        //获取数据库集群配置清单
        if ($path_info=="/cluster/getCluster")
            $output=$cluster->getCluster(config::redis);



        //更新并配置集群服务器
        if ($path_info=="/cluster/setCluster")
            $output=$cluster->setCluster(config::redis);



        //检测集群是否正常
        if ($path_info=="/cluster/checkCluster")
            $output=$cluster->checkCluster(config::redis);



        //检测本地从库是否正常
        if ($path_info=="/cluster/checkLocalSlave")
            $output=$cluster->checkLocalSlave(config::redis);



        //重写laravel配置文件
        if($path_info=="/cluster/env_rewrite")
            $output=$cluster->env_rewrite(config::redis,config::env);



        //检查服务器主从状态
        if ($path_info=='/cluster/status')
            $output=$cluster->status(config::redis);



        //测试env文件重写
        if($path_info=='/cluster/test_env')
            $output=$cluster->test_env(config::redis,config::laravel_env,config::laravel_port);



        if ($request->get['act'] == "debug") {
            $t2 = microtime(true);
            $a = round($t2 - $t1, 3);
            $b = memory_get_usage();
            $view = json_decode($output);
            if ($view){
                $view->runtime = $a;
                $view->mem_usage = $b;
                $output = json_encode($view);
            }else{
                $output=$output.PHP_EOL."<br>Laravel5245<br>Swoole<br>执行时间:".$a."<br>内存耗用:".$b;
            }

        }
        return $output;
    }
}


