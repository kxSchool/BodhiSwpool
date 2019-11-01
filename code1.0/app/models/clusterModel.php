<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019-06-06
 * Time: 15:35
 */

class clusterModel extends model {

    static function test($cluster,$id){
        $sql = "select * from temp where id=".$id;
        $result = database::cluster_query($cluster,$sql);
        while ($rowx = $result) {
            return $rowx;
        }
    }

}