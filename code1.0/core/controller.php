<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-06-06
 * Time: 15:25
 */

class controller{

    function getCluster($redis)
    {

        $redisd =database::redisd($redis);
        $result['db_master'] = json_decode($redisd->get('db_master'));

        $slaves = $redisd->get('db_slaves');
        $result['db_slaves'] = json_decode($slaves);

        $host = swoole_get_local_ip();
        if ($host['eth0']==$result['db_master']->hostname){
            $result['local'] = $result['db_master'];
        }
        foreach (json_decode($slaves) as $row) { //遍历结果
            if ($row->hostname == $host['eth0']) {
                $result['local'] = $row;
            }
        }
        $result['db_bad'] = json_decode($redisd->get('db_bad'));
        $result['status'] = $redisd->get('status');
        $result['alert'] = $redisd->get('alert');
        return json_encode($result);

    }

}