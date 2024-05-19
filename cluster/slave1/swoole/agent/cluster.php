<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-10
 * Time: 17:44
 */
header("Content-Type: text/html; charset=utf-8");
set_time_limit(0);
$slave_db = array(
    'db0'=>array(
        'server' =>'master',
        'hostname' => '172.17.0.2',
        'port' => 3306,
        'database' => 'ecshop4',
        'username' => 'root',
        'password' => 'cj781124',
        'charset' => 'utf8',
    ),
    'db1'=>array(
        'server' =>'slave',
        'hostname' => '172.17.0.3',
        'port' => 3306,
        'database' => 'ecshop4',
        'username' => 'root',
        'password' => 'cj781124',
        'charset' => 'utf8',
    ),
    'db2'=>array(
        'server' =>'slave',
        'hostname' => '172.17.0.4',
        'port' => 3306,
        'database' => 'ecshop4',
        'username' => 'root',
        'password' => 'cj781124',
        'charset' => 'utf8',
    ),
    'db3'=>array(
        'server' =>'slave',
        'hostname' => '172.17.0.5',
        'port' => 3306,
        'database' => 'ecshop4',
        'username' => 'root',
        'password' => 'cj781124',
        'charset' => 'utf8',
    )

);
foreach ($slave_db as $db_key) {
    echo "<meta charset='UTF-8'>";
    $server=$db_key['server'];
    $host = $db_key['hostname'];
    $port = $db_key['port'];
    $db_user = $db_key['username'];
    $db_pass = $db_key['password'];
    $database = $db_key['database'];
    $charset=$db_key['charset'];

    $db = new mysqli($host,$db_user,$db_pass,$database,$port) or die($host.'打开失败'); //连接数据库
    //或者这样也可以
    //$db = mysqli_connect($host,$db_user,$db_pass,$database,$port) or die($host.'打开失败'); //连接数据库

    $db->set_charset($charset); //设置查询结果编码
    echo $server.":<b>".$host."</b>   ";
    if ($server=='master'){
        $master=$host;
        $sql="DROP TABLE IF EXISTS `temp`";
        $result = $db->query($sql); //删除表
        $sql="CREATE TABLE `temp` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`user_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
        $result = $db->query($sql); //创建表
        $time=time();
        $sql="insert into `temp`(user_name) values($time)";
        $result = $db->query($sql); //创建记录
        $sql="show master status";
        $result = $db->query($sql); //得到查询结果
        while($row = $result->fetch_assoc()){ //遍历结果
            $File=$row['File'];
            $Position=$row['Position'];
            print_r("File:".$row['File']."  ");print_r("Position:".$row['Position']."<br/>");
        }
    }else{
        $sql="stop slave";
        print_r($sql."<br/>");
        $db->query($sql);
        $sql="change master to master_host='$master',master_user='root',master_password='cj781124',master_log_file='$File',master_log_pos=$Position";
        print_r($sql."<br/>");
        $db->query($sql);
        $sql="start slave";
        print_r($sql."<br/>");
        $db->query($sql);

        $sql = "show slave status";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        print_r($row);
        $Slave_IO_Running = $row['Slave_IO_Running'];
        $Slave_SQL_Running = $row['Slave_SQL_Running'];
        if ('Yes' == $Slave_IO_Running && 'Yes' == $Slave_SQL_Running) {
            print_r("<br/>");
            print_r('Slave_IO_Running:'.$Slave_IO_Running."<br/>");
            print_r('Slave_SQL_Running:'.$Slave_SQL_Running."<br/>");
        } else {
            $content .= "从数据库( $host )挂掉了！ <br/>";
        }
    }

    $sql="select user_name from `temp` where id=1";
    $result = $db->query($sql); //得到查询结果
    while($row = $result->fetch_assoc()){ //遍历结果
        print_r("user_name:".$row['user_name'].'</br>');
    }

    $db->close(); //关闭连接
    $db=nothing;
    print_r("<br/>");
}
