<?php
/*
 * @Author: chentx
 * @Date: 2020-12-29 12:00:13
 * @LastEditTime: 2020-12-29 15:59:53
 * @LastEditors: chentx
 * @Description: 
 */
Flight::route('/checklist', function(){
    $output = new Output();
    
    $locality = Flight::request()->query['locality'];
    $month    = Flight::request()->query['month'];
    $order    = Flight::request()->query['order'];
    $offset   = Flight::request()->query['offset'];

    require_once('objects/checklist.php');
    $checklist = new Checklist($locality, $month, $order, $offset);
    $data = $checklist->campus_checklist();
    if ($data) {
        $output->output_json(0, $data);
    } else {
        $output->output_json(102);
    }
});
