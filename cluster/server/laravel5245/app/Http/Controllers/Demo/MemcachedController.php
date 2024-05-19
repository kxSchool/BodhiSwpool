<?php


namespace App\Http\Controllers\Demo;
use App\Http\Controllers\Controller;
use App\Models\Memcached;
use Illuminate\Http\Request;

class MemcachedController extends Controller
{
    public function Get(Request $request){
        $username=$request->input('username');
        $D['username']=$username;
        $D['user']=Memcached::get();
        dd($D);
        return view('demo/memcached/welcome',$D);
    }
}