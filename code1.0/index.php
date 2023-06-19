<?php
/**
 * Created by PhpStorm.
 * User: zhang abcdefgh123666
 * Date: 2019-05-14
 * Time: 13:54
 */
define('APP_PATH', realpath(dirname(__file__) . "/app/"));
define('CORE_PATH', realpath(dirname(__file__) . "/core/"));
include ("core/autoload.php");

$Redispool = new RedisPool();
$Mysqlpool = new Mysqlpool();
$http = new Swoole\Http\Server("0.0.0.0", 8088);
$http->set(array(
    'worker_num' => 20,
    'daemonize' => 0,
    ));

$http->on('Start', function ()
{
    swoole_set_process_name("cluster"); }
);

$http->on('request', function ($request, $response)use ($Redispool, $Mysqlpool)
{
    $G['t1'] = microtime(true); 
    $G['path_info'] = $request->server['request_uri']; 
    $G['ip'] = $request->server['remote_addr'];
    $G['request']=$request;
    $G['response']=$response;        
    $G['Redispool']=$Redispool;
    $G['Mysqlpool']=$Mysqlpool;            
    include APP_PATH . "/init.php"; 
    $response->header('Content-Type','text/html'); 
    $response->header('Charset', 'utf-8'); 
    if (strstr($G['path_info'], "/mobile/")){
        $output=ZAJRoute::url($G);
    }
        

    if (strstr($path_info, "/test/"))
        $output = testRoute::url($G);
                
    $response->end($output); 
    }
);

$http->start();
