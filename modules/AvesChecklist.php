<?php
/*
 * @Author: chentx
 * @Date: 2020-12-29 12:00:13
 * @LastEditTime: 2020-12-30 09:41:05
 * @LastEditors: chentx
 * @Description: 
 */

//区域鸟类名录
Flight::route('/aves-checklist/region', function(){
    $output = new Output();
    
    $locality = Flight::request()->query['locality'];
    $month    = Flight::request()->query['month'];
    $order    = Flight::request()->query['order'];
    $offset   = Flight::request()->query['offset'];

    require_once('objects/avesChecklist.php');
    $checklist = new Checklist();
    $data = $checklist->get_region_checklist($locality, $month, $order, $offset);
    if ($data) {
        $output->output_json(0, $data);
    } else {
        $output->output_json($checklist->errCode);
    }
});

//全部鸟类名录
Flight::route('/aves-checklist/all', function(){
    $output = new Output();
    
    $order    = Flight::request()->query['order'];
    $offset   = Flight::request()->query['offset'];

    require_once('objects/avesChecklist.php');
    $checklist = new Checklist();
    $data = $checklist->get_all_checklist($order, $offset);
    if ($data) {
        $output->output_json(0, $data);
    } else {
        $output->output_json($checklist->errCode);
    }
});