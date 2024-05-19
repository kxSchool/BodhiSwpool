<?php

//进程数
$work_number=100;

//
$worker=[];

require_once "config.php";
require_once "function.php";
//创建进程
for ($i=0; $i < $work_number; $i++) {
    //print_r("dddd");

    //创建多线程
    $pro=new swoole_process(function(swoole_process $work) use($i,$curl,$redis){
        while (1==1){
            $t1 = microtime(true);
            echo PHP_EOL.'开始时间:'.date('H:i:s',time()).PHP_EOL;
            //获取html文件
            $content=curldeta($redis);
            $work->write($content.PHP_EOL);
            echo '结束时间:'.date('H:i:s',time()).PHP_EOL;
            $t2 = microtime(true);
            $a = round($t2 - $t1, 3);
            $b = memory_get_usage();
            echo '执行时间:'.$a.PHP_EOL;
            echo '内存耗用:'.$b.PHP_EOL;
            sleep(1);
        }


    },true);
    $pro_id=$pro->start();
    $worker[$pro_id]=$pro;
}

while (1==1){
    foreach ($worker as $v) {

        echo $v->read();

    }
    usleep(3);
}

//模拟爬虫
function curldeta($redis)
{
    $cluster = json_decode(getCluster($redis));
    if(200==$cluster->status.PHP_EOL){
        $rows = $cluster->db_master;
        $db = new mysqli($rows->hostname, $rows->username, $rows->password, $rows->database, $rows->port) or die($rows->hostname . '打开失败'.PHP_EOL); //连接数据库
        $db->set_charset($rows->charset); //设置查询结果编码
        $sql = "insert into test(user_name) values (".time().")";
        $result = $db->query($sql); //得到查询结果
        $id=mysqli_insert_id($db);
        echo $sql.PHP_EOL;
        echo "返回值:".$id.PHP_EOL;
        mysqli_close($db);
    }else{
        $id='error';
    }

    return $id;

}

//进程回收
swoole_process::wait();

