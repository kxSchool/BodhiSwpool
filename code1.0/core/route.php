<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-06-06
 * Time: 17:19
 */


class route
{
    static function url($request, $t1)
    {

        $path_info = $request->server['request_uri'];
        if ($path_info == '/favicon.ico') {
            return;
        }
        $output = "";
    }
}