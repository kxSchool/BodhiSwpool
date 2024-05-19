<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-27
 * Time: 17:13
 */

$file="/data/laravel5816/.env";
$file_new="/data/laravel5816/new.env";
shell_exec('rm -fr '.$file_new);
$fp = file($file);
foreach ($fp as $key=>$val){
	if (strstr($val,'CLUSTER_')){
		unset($fp[$key]);
	}
}


$redisd = new redis();
$redisd->connect('172.17.0.2', 6379);
$result['db_master'] = json_decode($redisd->get('db_master'));
$slaves = $redisd->get('db_slaves');
$result['db_slaves'] = json_decode($slaves);
foreach ($result['db_slaves'] as $key => $val) {
	if ($result['slaves']){
		$result['slaves']=$result['slaves'].','.$val->hostname;
	}else{
		$result['slaves']=$val->hostname;
	}
	//$result['slaves'][]=$val->hostname;
}
$result['status'] = $redisd->get('status');
$result['alert'] = $redisd->get('alert');
global $redis;
$redis = (object)$result;
if ($redis->status != 200) {
	print_r($redis->alert);
	exit;
}
//print_r($redis->slaves);exit;


$fp[]="CLUSTER_MASTER=".$redis->db_master->hostname."\n";
$fp[]="CLUSTER_SLAVES=".$redis->slaves."\n";

print_r(json_encode($fp));

$fp_new = fopen($file_new,'w');
foreach ($fp as $key=>$val){
	fwrite($fp_new, $val);
}
fclose($fp_new);
shell_exec('mv '.$file .' ' .$file.'_'.time());
shell_exec('mv '.$file_new .' '.$file);

