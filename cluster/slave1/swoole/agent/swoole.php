<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-14
 * Time: 13:54
 */

$http = new Swoole\Http\Server("0.0.0.0", 80);
$http->set(array(
    'worker_num' => 1,
    'daemonize' => 1,
));

$http->on('Start', function ($server) {
    swoole_set_process_name("live_master");
});
include("base.php");
include("function.php");
$http->on('request', function ($request, $response) {
    $t1 = microtime(true);
    $server = $request->server;
    $path_info = $request->server['request_uri'];
    $response->header('Content-Type', 'text/html');
    $response->header('Charset', 'utf-8');
    include("config.php");
    include("route.php");
    include("debug.php");
    $response->end($output);
});

$http->start();
