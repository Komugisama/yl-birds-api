<?php
/*
 * @Author: chentx
 * @Date: 2020-10-22 17:06:09
 * @LastEditTime: 2020-12-29 17:05:08
 * @LastEditors: chentx
 * @Description: 
 */
//root
Flight::route('/', function(){
    $data = array('code'=>0, 'msg'=>'welcome');
    Flight::json($data);
});

//user
require_once('modules/User.php');

//checklist
require_once('modules/AvesChecklist.php');

//启动路由
Flight::start();