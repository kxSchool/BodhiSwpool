<?php

/**

 * Created by PhpStorm.

 * User: zhang

 * Date: 2019-05-23

 * Time: 11:30

 */





class zajRoute{

    static function url($G)

    {





        $output="";

        $zaj=new zajController();

        $path_info = $G['request']->server['request_uri'];

        //print_r($path_info.PHP_EOL);



        //

        if($path_info=="/mobile/audit"){

            $output=$zaj->audit($G);

        }

            

        //

        if($path_info=="/mobile/audit_init")

            $output=$zaj->audit_init($G);





        if ($request->get['act'] == "debug") {

            $t2 = microtime(true);

            $a = round($t2 - $t1, 3);

            $b = memory_get_usage();

            $view = json_decode($output);

            if ($view){

                $view->runtime = $a;

                $view->mem_usage = $b;

                $output = json_encode($view);

            }else{

                $output=$output.PHP_EOL."<br>Laravel5245<br>Swoole<br>鎵ц鏃堕棿:".$a."<br>鍐呭瓨鑰楃敤:".$b;

            }



        }

        return $output;

    }

}





