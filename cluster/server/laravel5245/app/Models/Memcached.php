<?php


namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
class Memcached extends Model
{
    static function get(){
        Cache::forever('bar', 'Jacky.Zhang');
        $a=Cache::get('bar');
        return $a;
    }

}