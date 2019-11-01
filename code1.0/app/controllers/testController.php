<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-06-06
 * Time: 17:07
 */

class testController extends controller{

    function testdata($redis,$apiList)
    {
        //$lock = RedLock::waitLock($redis,['test','test'],$apiList);
        $cluster = json_decode($this->getCluster($redis));
        $id=floor(rand(0, ceil(20000)+1));
        $data=clusterModel::test($cluster,$id);
        //$t=time();
        //$data['user_name']=$t;
        view::assign('name', $data);
        //RedLock::del($redis,$lock,$apiList);
        return view::display('/cluster');
    }

    function test_env($redis,$laravel_env,$laravel_port){
        $url=$laravel_env.'/cluster/env_rewrite?act=debug';
        $cli = new Swoole\Coroutine\Http\Client($laravel_env, $laravel_port);
        $cli->get('/cluster/env_rewrite?act=debug');
        $contents=$cli->body;
        $output['laravel_env']=json_decode($contents);
        return json_encode($output);
    }

    function test_redis($redis,$apiList){
        //$lock = RedLock::waitLock($redis,'test_redis',10,40);
        $redisd =database::redisd($redis);
        $redisd->set('alert','ok');
        $data['alert']=$redisd->get('alert');
        //RedLock::del($redis,$lock,$apiList);
        //$data['lock']=$lock;
        return json_encode($data);
    }
}