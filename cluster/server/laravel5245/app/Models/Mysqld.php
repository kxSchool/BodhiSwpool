<?php


namespace App\Models;
use DB;
class Mysqld extends Model
{
    static function get($id){
        //增加
        //$sql='insert into users(username) value("jacky.zhang'.$id.'")';
        //DB::connection("game_master::write")->insert($sql);

        //修改
        //$sql='update users set username="Jacky" where id=:id';
        //$data['id']=$id;
        //DB::connection("game_master::write")->update($sql,$data);

        //读取
        $sql='select * from users where user_id=?';
        return DB::connection("mysql1")->select($sql,[$id]);

    }


}
