<?php


namespace App\Http\Controllers\Demo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Redisd;

class RedisController extends Controller
{
    public function Get(Request $request){
        $D=Redisd::initServer();
        print_r(json_encode($D));exit;
        return view('demo/redis/welcome',$D);
    }

}