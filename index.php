<?php
/*
 * @Author: chentx
 * @Date: 2020-10-22 17:01:08
 * @LastEditTime: 2021-02-10 16:32:16
 * @LastEditors: chentx
 * @Description: 
 */
header('Content-Type:application/json;charset=utf-8');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Origin:http://localhost:8080');
date_default_timezone_set('Asia/Shanghai');
session_start();

require_once('vendor/autoload.php');

require_once('config/output.php');
require_once('config/validator.php');
require_once('config/database.php');
require_once('config/httpRequest.php');
require_once('config/router.php');