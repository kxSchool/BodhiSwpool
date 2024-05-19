<?php


namespace App\Http\Controllers\Demo;
use App\Http\Controllers\Controller;
use App\Models\Mysqld;
use Illuminate\Http\Request;

class MysqlController extends  Controller
{
    public function Get(Request $request){
        $username=$request->input('username');
        $D['username']=$username;
        $D['users']=Mysqld::get(2);


        return view('demo/mysql/welcome',$D);
    }

}
