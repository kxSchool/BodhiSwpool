<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-06-06
 * Time: 17:19
 */


class testRoute
{
    static function url($request, $t1,$apiList)
    {
        $output = "";
        $test = new testController();
        $path_info = $request->server['request_uri'];

        //测试从库的读写分离
        if ($path_info == "/test/test")
            $output = $test->testdata(config::redis,$apiList);

        //测试redis写读性能
        if ($path_info=="/test/redis")
            $output=$test->test_redis(config::redis,$apiList);

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