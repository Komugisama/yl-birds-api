<?php
/*
 * @Author: chentx
 * @Date: 2020-10-22 17:06:09
 * @LastEditTime: 2020-12-30 16:33:13
 * @LastEditors: chentx
 * @Description: 
 */
//root
Flight::route('/', function(){
    $output = new Output();
    $output->output_json(0);
});

//user
require_once('modules/User.php');

//checklist
require_once('modules/AvesChecklist.php');

Flight::route('/aves-detail/@identifier', function($identifier) {
    $output = new Output();

    require_once('objects/avesDetail.php');
    $aves = new Aves();
    if ($data = $aves->check_aves_name($identifier)) {
        $output->output_json(0, $aves->basicInfo);
    } else {
        $output->output_json($aves->errorCode);
    }
});

//启动路由
Flight::start();