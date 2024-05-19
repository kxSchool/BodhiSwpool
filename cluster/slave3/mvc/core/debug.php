<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-23
 * Time: 13:19
 */
if ($request->get['act'] == "debug") {
    $t2 = microtime(true);
    $a = round($t2 - $t1, 3);
    $b = memory_get_usage();
    $output = json_decode($output);
    $output->runtime = $a;
    $output->mem_usage = $b;
    $output = json_encode($output);
    //($request->server['remote_addr']." ".date("Y-m-d H:i:s",$request->server['request_time'])." 请求:   http://".$request->server['remote_addr'].":".$request->server['server_port'].$request->server['request_uri'].PHP_EOL);
    //print_r("get:".json_encode($request->get));
    //print_r(PHP_EOL);
    //print_r("post:".json_encode($request->post));
    //print_r(PHP_EOL);
}
