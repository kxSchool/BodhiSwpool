<?php



/**

 * Created by PhpStorm.

 * User: zhang

 * Date: 2019-05-23

 * Time: 11:10

 */

class zajController extends controller

{

    function audit($G)

    {

        $device=$G['request']->get['device'];

        $bundleid=$G['request']->get['bundleid'];

        $ver=$G['request']->get['ver'];

        $Redispool=$G['Redispool'];

        return zaj::audit($device,$bundleid,$ver,$Redispool);

    }

    

    function audit_init($G)

    {

        $Mysqlpool=$G['Mysqlpool'];

        $Redispool=$G['Redispool'];

        return zaj::audit_init($Mysqlpool,$Redispool);

    }





}



