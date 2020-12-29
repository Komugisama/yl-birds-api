<?php
/*
 * @Author: chentx
 * @Date: 2020-10-22 17:06:09
 * @LastEditTime: 2020-12-29 16:00:15
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
require_once('modules/Checklist.php');

//启动路由
Flight::start();