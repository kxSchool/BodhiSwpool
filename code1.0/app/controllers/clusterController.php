<?php

/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-05-23
 * Time: 11:10
 */
class clusterController extends controller
{
    function blackList($redis,$ip)
    {
        $data['is'] = RedLock::blackList(["hostname"=>"172.17.0.1","port"=>6379],$ip);
        return json_encode($data);
    }

    function getCpu()
    {
        $fp = popen('top -b -n 2 | grep -E "(Cpu|Mem)"',"r");//获取某一时刻系统cpu和内存使用情况
        $rs = "";
        while(!feof($fp)){
            $rs .= fread($fp,1024);
        }
        pclose($fp);
        $data['top']=$rs;
        preg_match_all("/%Cpu.*+/U",$rs,$cpus);
        foreach($cpus[0] as $cpu_key){
            preg_match('/%Cpu.*(\d+\.\d+) us+/', $cpu_key, $cpu); //cpu使用百分比
            $data['cpus'][]=$cpu[1];
        }

        preg_match_all("/Mem :.*+/U",$rs,$memss);
        foreach($memss[0] as $cpu_key){
            preg_match('/Mem :.* (\d+) .* (\d+) .* (\d+) .* (\d+) buff\/cache+/', $cpu_key, $mem); //cpu使用百分比
            $data['mems'][]=$mem;
        }
        return json_encode($data);
    }

    function initCluster($redis, $db_master, $db_slaves)
    {

        $redisd = database::redisd($redis);
        $redisd->set('db_master', json_encode((object)$db_master));
        $redisd->set('db_slaves', json_encode((object)$db_slaves));
        $redisd->set('db_bad', '');
        $redisd->set('status', '200');
        $redisd->set('alert', 'ok');
        $values['db_master'] = json_decode($redisd->get('db_master'));
        $values['db_slaves'] = json_decode($redisd->get('db_slaves'));
        $values['db_bad'] = json_decode($redisd->get('db_bad'));
        $values['status'] = $redisd->get('status');
        $values['alert'] = $redisd->get('alert');
        return json_encode($values);
    }

    function redLock($redis,$apiList)
    {
        $lock = RedLock::waitLock($redis,['cluster','redLock'],$apiList);
        print_r('锁号:'.$lock.PHP_EOL);

        $cluster = json_decode($this->getCluster($redis));
        $master = $cluster->db_master;
        $db = database::con($master);

        $sql = "select count(*) as c from temp";
        for ($i = 1; $i < 10; $i++) {
            $rs=database::cluster_query($cluster,$sql); //得到查询结果
        }

        $time = time();
        $sql = "insert into `temp`(user_name) values($time)";

        for ($i = 1; $i < 3; $i++){
            $db->query($sql); //创建记录
            $result1 = mysqli_insert_id($db);
            print_r('插入记录:'.$result1 . PHP_EOL);
        }







        database::close($db);

        RedLock::del($redis,$lock,$apiList);
        return '<meta charset="utf-8">锁号:['.$lock[0].']-['.$lock[1].'] 最后一条记录i：' . $result1;
    }

    function setLocalSlave($redis)
    {
        $redisd = database::redisd($redis);
        $redisd->set('status', '404');
        $host = swoole_get_local_ip();
        $redisd->set('alert', '重置服务器(' . $host['eth0'] . ')为集群的从服务器');
        $output1 = shell_exec('service mysqld stop');
        $output['stop'] = $output1;
        $output1 = shell_exec('rm -fr /etc/my.cnf');
        $output['rm_cnf'] = "ok";
        $output1 = shell_exec('cp /etc/my_slave.cnf /etc/my.cnf');
        $output['cp_cnf'] = $output1;
        $output1 = shell_exec('service mysqld start');
        $output['start'] = $output1;
        $output['status'] = '200';
        $output['alert'] = '服务器(' . $host['eth0'] . ')已重启并切换为集群的从服务器！';
        $redisd->set('status', '200');
        $redisd->set('alert', 'ok');
        return json_encode($output);
    }


    function setLocalMaster_delOld($redis, $laravel_env, $laravel_port)
    {
        $host = swoole_get_local_ip();
        $redisd = database::redisd($redis);
        $result['db_master'] = json_decode($redisd->get('db_master'));
        if ($host['eth0'] == $result['db_master']->hostname) {
            return $this->getCluster($redis);
        }
        $redisd->set('status', '404');
        $redisd->set('alert', '正在重置服务器(' . $host['eth0'] . ')为集群的主服务器... ...');
        $output1 = shell_exec('service mysqld stop');
        $output['stop'] = $output1;
        $output1 = shell_exec('rm -fr /etc/my.cnf');
        $output['rm_cnf'] = $output1;
        $output1 = shell_exec('cp /etc/my_master.cnf /etc/my.cnf');
        $output['cp_cnf'] = $output1;
        $output1 = shell_exec('service mysqld start');
        $output['start'] = $output1;
        $this->updateRedis($redis);
        $output['status'] = '200';
        $output['alert'] = '服务器(' . $host['eth0'] . ')已重启并切换为集群的主服务器！';
        $redisd->set('status', '200');
        $redisd->set('alert', 'ok');
        $cli = new Swoole\Coroutine\Http\Client($laravel_env, $laravel_port);
        $cli->get('/cluster/env_rewrite?act=debug');
        $contents = $cli->body;
        $output['laravel_env'] = json_decode($contents);
        return json_encode($output);
    }


    function updateRedis($redis)
    {
        $redisd = database::redisd($redis);
        $master = $redisd->get('db_master');
        $slaves = $redisd->get('db_slaves');
        $result['db_slaves'] = (object)json_decode($slaves);
        $host = swoole_get_local_ip();
        foreach ($result['db_slaves'] as $k => $row) { //遍历结果
            if ($row->hostname == $host['eth0']) {
                $result['local'] = $row;
                unset($result['db_slaves']->$k);
            }
        }
        $result['local']->server = "master";
        $redisd->set('db_bad', $master);
        $redisd->set('db_master', json_encode($result['local']));
        $redisd->set('db_slaves', json_encode($result['db_slaves']));
        $this->setCluster($redis);
    }

    function setLocalMaster_addOld($redis, $laravel_env, $laravel_port)
    {
        $host = swoole_get_local_ip();
        $redisd = database::redisd($redis);
        $result['db_master'] = json_decode($redisd->get('db_master'));
        $oldMaster = $result['db_master']->hostname;
        $oldPort = $result['db_master']->mPort;
        if ($host['eth0'] == $result['db_master']->hostname) {
            return $this->getCluster($redis);
        }
        $redisd->set('status', '404');
        $redisd->set('alert', '正在重置服务器(' . $host['eth0'] . ')为集群的主服务器... ...');
        $output1 = shell_exec('service mysqld stop');
        $output['stop'] = $output1;
        $output1 = shell_exec('rm -fr /etc/my.cnf');
        $output['rm_cnf'] = $output1;
        $output1 = shell_exec('cp /etc/my_master.cnf /etc/my.cnf');
        $output['cp_cnf'] = $output1;
        $output1 = shell_exec('service mysqld start');
        $output['start'] = $output1;
        $this->updateRedis_addOld($redis);
        $output['status'] = '200';
        $output['alert'] = '服务器(' . $host['eth0'] . ')已重启并切换为集群的主服务器！';
        $redisd->set('status', '200');
        $redisd->set('alert', 'ok');
        $contents = $this->http_request($laravel_env, '/cluster/env_rewrite?act=debug', $laravel_port);
        $output['laravel_env'] = json_decode($contents);

        $contents = $this->http_request($oldMaster, '/cluster/setLocalSlave?act=debug', $oldPort);
        $output['setLocalSlave'] = json_decode($contents);
        return json_encode($output);
    }

    function http_request($domain, $url, $port)
    {
        print_r("跨机调用 http://" . $domain . ":" . $port . $url . PHP_EOL);
        $cli = new Swoole\Coroutine\Http\Client($domain, $port);
        $cli->set(['timeout' => 100]);
        $cli->get($url);
        $contents = $cli->body;
        return $contents;
    }

    function updateRedis_addOld($redis)
    {
        $redisd = database::redisd($redis);
        $result['db_master'] = json_decode($redisd->get('db_master'));
        $slaves = $redisd->get('db_slaves');
        $result['db_slaves'] = (object)json_decode($slaves);
        $host = swoole_get_local_ip();
        foreach ($result['db_slaves'] as $k => $row) { //遍历结果
            if ($row->hostname == $host['eth0']) {
                $result['local'] = $row;
                unset($result['db_slaves']->$k);
            }
            $master_count = $k;
        }
        $master_count = $master_count + 1;
        $result['db_master']->server = "slave";
        $result['db_slaves']->$master_count = $result['db_master'];
        $result['local']->server = "master";
        $redisd->set('db_master', json_encode($result['local']));
        $redisd->set('db_slaves', json_encode($result['db_slaves']));
        $this->setCluster($redis);
    }


    function setCluster($redis)
    {
        $cluster = json_decode($this->getCluster($redis));
        $master = $cluster->db_master;
        $slaves = $cluster->db_slaves;
        //连接数据库
        $db = database::con($master);

        $sql = "show master status";
        $result = $db->query($sql); //得到查询结果

        while ($row = $result->fetch_assoc()) { //遍历结果
            $File = $row['File'];
            $Position = $row['Position'];
            $cluster->db_master->File = $File;
            $cluster->db_master->Position = $Position;
        }
        foreach ($slaves as $rows) { //遍历结果
            $rows->set = $this->setSlave($rows, $master, $File, $Position);
        }
        database::close($db);
        return json_encode($cluster);
    }


    function setSlave($rows, $master, $File, $Position)
    {
        //连接数据库
        $db = database::con($rows);
        $sql = "stop slave";
        $output['stop'] = $sql;
        $db->query($sql);
        $sql = "change master to master_host='$master->hostname',master_user='$master->username',master_password='$master->password',master_log_file='$File',master_log_pos=$Position";
        $output['change'] = $sql;
        $db->query($sql);
        $sql = "start slave";
        $output['start'] = $sql;
        $db->query($sql);
        $sql = "show slave status";
        $result = $db->query($sql);
        $row_slave = $result->fetch_assoc();
        $output['status'] = $row_slave;
        $Slave_IO_Running = $row_slave['Slave_IO_Running'];
        $Slave_SQL_Running = $row_slave['Slave_SQL_Running'];
        if ('Yes' == $Slave_IO_Running && 'Yes' == $Slave_SQL_Running) {
            $output['Slave_IO_Running'] = $Slave_IO_Running;
            $output['Slave_SQL_Running'] = $Slave_SQL_Running;
            $output['Connecting'] = "从数据库( $rows->hostname )连接正常";
        } else if ('Connecting' == $Slave_IO_Running) {
            $output['Connecting'] = "从数据库( $rows->hostname )连接中！";
        } else {
            $output['Connecting'] = "从数据库( $rows->hostname )挂掉了！";
        }
        database::close($db);
        return $output;
    }

    function checkDB($rows)
    {
        $output = "cluster:" . $rows->server;
        $db = database::con($rows);

        $sql = "SHOW TABLES LIKE 'temp'";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        if ($row) {
            $sql = "select user_name from `temp` where id=1";
            $result = $db->query($sql); //得到查询结果
            while ($rowx = $result->fetch_assoc()) { //遍历结果
                database::close($db);
                return $rowx['user_name'];
            }
        } else {
            return "error";
        }
        database::close($db);
    }

    function checkCluster($redis)
    {

        $cluster = json_decode($this->getCluster($redis));
        $master = $cluster->db_master;
        $slaves = $cluster->db_slaves;
        $db = database::con($master);
        $sql = "DROP TABLE IF EXISTS `temp`";
        $result = $db->query($sql); //删除表
        $sql = "CREATE TABLE `temp` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`user_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
        $result = $db->query($sql); //创建表
        $time = time();
        $sql = "insert into `temp`(user_name) values($time)";
        $result = $db->query($sql); //创建记录

        $sql = "select user_name from `temp` where id=1";
        $result = $db->query($sql); //得到查询结果
        while ($row = $result->fetch_assoc()) { //遍历结果
            $cluster->db_master->key = $row['user_name'];
        }
        $Cluster_status = 200;
        foreach ($slaves as $rows) { //遍历结果
            $id = "";
            while ($id == "") {
                $id = $this->checkDB($rows);
                if ($id <> "") {
                    $rows->key = $id;
                    if ($id == $cluster->db_master->key) {
                        $rows->status = 200;
                    } else {
                        $rows->status = 404;
                        $Cluster_status = 404;
                    }
                    if ($rows->hostname == $cluster->local->hostname) {
                        $cluster->local->key = $id;
                        if ($id == $cluster->db_master->key) {
                            $cluster->local->status = 200;
                        } else {
                            $cluster->local->status = 404;
                            $Cluster_status = 404;
                        }
                    }
                } else {
                    usleep(1);
                }
            }
        }
        if ($Cluster_status == 200) {
            $cluster->alert = "ok";
        }
        $cluster->db_slaves = $slaves;
        $cluster->status = $Cluster_status;
        database::close($db);
        return json_encode($cluster);
    }

    function checkLocalSlave($redis)
    {
        $cluster = json_decode($this->getCluster($redis));
        if ($cluster->local->server == "slave") {
            $master = $cluster->db_master;
            $slaves = $cluster->db_slaves;
            $out['db_master'] = (array)$master;
            $out['local'] = (array)$cluster->local;
            $db = database::con($master);
            $sql = "DROP TABLE IF EXISTS `temp`";
            $result = $db->query($sql); //删除表
            $sql = "CREATE TABLE `temp` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`user_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
            $result = $db->query($sql); //创建表
            $time = time();
            $sql = "insert into `temp`(user_name) values($time)";
            $result = $db->query($sql); //创建记录

            $sql = "select user_name from `temp` where id=1";
            $result = $db->query($sql); //得到查询结果
            $output = "cluster:" . $master->server . "</br>";
            $output = $output . "server:" . $master->hostname . "</br>";
            while ($row = $result->fetch_assoc()) { //遍历结果
                $output = $output . "user_name:" . $row['user_name'] . '</br></br>';
                $out['db_master']['key'] = $row['user_name'];
            }
            $rows = $cluster->local;
            $id = "";
            while ($id == "") {
                $id = $this->checkDB($rows);
                if ($id <> "") {
                    $output = $output . $id;
                    $out['local']['key'] = $id;
                } else {
                    usleep(1);
                }
            }
            if ($out['db_master']['key'] == $out['local']['key']) {
                $out['status'] = 200;
                $out['alert'] = 'ok';
            } else {
                $out['status'] = 404;
                $out['alert'] = 'error';
            }
        } else {
            $out['status'] = 404;
            $out['alert'] = 'local is Master';
        }
        database::close($db);
        return json_encode($out);
    }

    function env_rewrite($redis, $env)
    {
        $file = $env . ".env";
        $file_new = $env . "new.env";
        shell_exec('rm -fr ' . $file_new);
        $fp = file($file);
        foreach ($fp as $key => $val) {
            if (strstr($val, 'CLUSTER_')) {
                unset($fp[$key]);
            }
        }
        $redisd = database::redisd($redis);
        $result['db_master'] = json_decode($redisd->get('db_master'));
        $slaves = $redisd->get('db_slaves');
        $result['db_slaves'] = json_decode($slaves);
        foreach ($result['db_slaves'] as $key => $val) {
            if ($result['slaves']) {
                $result['slaves'] = $result['slaves'] . ',' . $val->hostname;
            } else {
                $result['slaves'] = $val->hostname;
            }
        }
        $result['status'] = $redisd->get('status');
        $result['alert'] = $redisd->get('alert');
        $redis = (object)$result;
        if ($redis->status != 200) {
            return ($redis->alert);
        }
        $fp[] = "CLUSTER_MASTER=" . $redis->db_master->hostname . "\n";
        $fp[] = "CLUSTER_SLAVES=" . $redis->slaves . "\n";

        $fp_new = fopen($file_new, 'w');
        foreach ($fp as $key => $val) {
            fwrite($fp_new, $val);
        }
        fclose($fp_new);
        shell_exec('mv ' . $file . ' ' . $file . '_' . time());
        shell_exec('mv ' . $file_new . ' ' . $file);
        return (json_encode($fp));
    }

    function status($redis)
    {
        $cluster = json_decode($this->getCluster($redis));
        if (!empty($cluster->local)) {
            $local = (object)$cluster->local;
            $db = database::con($local);
            $sql = "show master status";
            $result = $db->query($sql);
            $row_slave = $result->fetch_assoc();
            $out['master_status'] = $row_slave;
            $sql = "show slave status";
            $result = $db->query($sql);
            $row_slave = $result->fetch_assoc();
            $out['slave_status'] = $row_slave;
            database::close($db);
            return json_encode($out);
        }
    }


}

