<?php
echo '开始时间:' . date('H:i:s', time());
//进程数
$work_number = 20;

//
$worker = [];


//单线程模式
// foreach ($curl as $v) {
// 	echo curldeta($v);
// }

//创建进程
for ($i = 0; $i < $work_number; $i++) {
    //创建多线程
    $pro = new swoole_process(function (swoole_process $work) use ($i) {
        while (1 == 1) {
            //获取html文件
            $content = curldeta("http://www.test.com/demo/mysql");
            //写入管道
            $work->write($content . PHP_EOL);
            sleep(1);
        }
    }, true);
    $pro_id = $pro->start();
    $worker[$pro_id] = $pro;
}

while (1 == 1) {
    foreach ($worker as $v) {
        echo $v->read();
    }
    usleep(3);
}


//模拟爬虫
function curldeta($curl_arr)
{    //file_get_contents
    //echo $curl_arr . PHP_EOL;
    return file_get_contents($curl_arr);
}

//进程回收
swoole_process::wait();

echo '结束时间:' . date('H:i:s', time());

?>
