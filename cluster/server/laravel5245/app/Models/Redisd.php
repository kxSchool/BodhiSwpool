<?php


namespace App\Models;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
class Redisd extends Model
{
    static function initServer(){
        $db_master=array(
            'server' =>'master',
            'hostname' => '172.17.0.2',
            'port' => 3306,
            'database' => 'qcloud_ml',
            'username' => 'root',
            'password' => 'ml123456',
            'charset' => 'utf8',
            'mPort'=> 80,
        );
        $db_slaves=array(
            array(
                'server' =>'slave',
                'hostname' => '172.17.0.3',
                'port' => 3306,
                'database' => 'qcloud_ml',
                'username' => 'root',
                'password' => 'ml123456',
                'charset' => 'utf8',
                'mPort'=> 80,
            ),
            array(
                'server' =>'slave',
                'hostname' => '172.17.0.4',
                'port' => 3306,
                'database' => 'qcloud_ml',
                'username' => 'root',
                'password' => 'ml123456',
                'charset' => 'utf8',
                'mPort'=> 80,
            ),
            array(
                'server' =>'slave',
                'hostname' => '172.17.0.5',
                'port' => 3306,
                'database' => 'qcloud_ml',
                'username' => 'root',
                'password' => 'ml123456',
                'charset' => 'utf8',
                'mPort'=> 80,
            )
        );
        Redis::set('db_master', json_encode((object)$db_master));
        Redis::set('db_slaves', json_encode((object)$db_slaves));
        Redis::set('db_bad', '');
        Redis::set('status','200');
        Redis::set('alert','ok');
        $values['db_master'] = json_decode(Redis::get('db_master'));
        $values['db_slaves']=json_decode(Redis::get('db_slaves'));
        $values['db_bad']=json_decode(Redis::get('db_bad'));
        $values['status']=Redis::get('status');
        $values['alert']=Redis::get('alert');
        return $values;
    }

}