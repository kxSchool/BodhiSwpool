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
}