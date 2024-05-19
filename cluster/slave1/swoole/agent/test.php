<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-23
 * Time: 18:58
 */

require_once "config.php";
require_once "function.php";


//模拟爬虫
function curldeta($redis)
{
    $cluster = json_decode(getCluster($redis));
    print_r($cluster);
    $rows = $cluster->db_master;
    $db = new mysqli($rows->hostname, $rows->username, $rows->password, $rows->database, $rows->port) or die($rows->hostname . '打开失败'); //连接数据库
    $db->set_charset($rows->charset); //设置查询结果编码
    $sql = "insert into test(user_name) values (".time().")";
    $result = $db->query($sql); //得到查询结果
    $id=mysqli_insert_id($db);
    echo $id.PHP_EOL;
    mysqli_close($db);
    return $id;

}


$content=curldeta($redis);
echo $content;