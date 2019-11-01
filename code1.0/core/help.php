<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-06-06
 * Time: 15:29
 */

function doConsume($url, $data)
{
    echo "doConsume:" . $url . ":?" . http_build_query($data) . "\n";
    httpGet($url, $data);
}

function httpGet($url, $data)
{
    if ($data) {
        $url .= '?' . http_build_query($data);
    }
    echo $url;
    $curlObj = curl_init(); //初始化curl，
    curl_setopt($curlObj, CURLOPT_URL, $url); //设置网址
    curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1); //将curl_exec的结果返回
    curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curlObj, CURLOPT_HEADER, 0); //是否输出返回头信息
    $response = curl_exec($curlObj); //执行
    curl_close($curlObj); //关闭会话
    return $response;
}
