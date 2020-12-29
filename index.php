<?php
/*
 * @Author: chentx
 * @Date: 2020-10-22 17:01:08
 * @LastEditTime: 2020-12-29 16:04:03
 * @LastEditors: chentx
 * @Description: 
 */
header('Content-Type:application/json;charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
session_start();

require_once('vendor/autoload.php');

require_once('config/output.php');
require_once('config/database.php');
require_once('config/router.php');