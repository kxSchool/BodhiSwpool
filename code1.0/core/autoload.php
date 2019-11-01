<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-24
 * Time: 16:22
 */
include CORE_PATH . "/help.php";
include CORE_PATH . "/model.php";
include CORE_PATH . "/controller.php";
include CORE_PATH . "/view.php";
include CORE_PATH . "/db.php";
include CORE_PATH . "/route.php";
include CORE_PATH . "/MysqlPool.php";
include CORE_PATH . "/RedisPool.php";

include APP_PATH . "/config.php";
scanFile(APP_PATH.'/models');
scanFile(APP_PATH.'/controllers');
scanFile(APP_PATH.'/route');

function scanFile($path)
{
    global $result;
    $files = scandir($path);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            if (is_dir($path . '/' . $file)) {
                scanFile($path . '/' . $file);
            } else {
                $filetype = pathinfo($file);
                if ($filetype['extension'] == 'php') {
                    include $path . '/' . $file;
                }
            }
        }
    }
}
