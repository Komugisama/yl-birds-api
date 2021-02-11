<?php
/*
 * @Author: chentx
 * @Date: 2020-10-22 17:06:09
 * @LastEditTime: 2021-02-10 17:23:28
 * @LastEditors: chentx
 * @Description: 
 */

//root
// Flight::map('error', function() {
//     $output = new Output();
//     $output->output_json(1);
// });

Flight::map('notFound', function(){
    $output = new Output();
    $output->output_json(100);
});

Flight::route('/', function(){
    $output = new Output();
    $output->output_json(0);
});

//User
require_once('modules/User.php');

//AvesChecklist
require_once('modules/AvesChecklist.php');
require_once('modules/AvesAutocomplete.php');

//Detail
require_once('modules/AvesDetail.php');

//Report
require_once('modules/Report.php');

//启动路由
Flight::start();