<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-14
 * Time: 13:54
 */
define('APP_PATH', realpath(dirname(__FILE__)."/app/"));
$http = new Swoole\Http\Server("0.0.0.0", 11211);
$http->set(array(
    'worker_num' => 20,
    'daemonize' => 0,
));

$http->on('Start', function () {
    swoole_set_process_name("cluster");

});
include("core/base.php");
$http->on('request', function ($request, $response) {
    $t1 = microtime(true);
    $response->header('Content-Type', 'text/html');
    $response->header('Charset', 'utf-8');
    include("app/config.php");
    include("app/route.php");
    include("core/debug.php");
    $response->end($output);
});

$http->start();
