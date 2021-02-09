<?php
/*
 * @Author: chentx
 * @Date: 2021-02-07 16:16:25
 * @LastEditTime: 2021-02-08 14:43:49
 * @LastEditors: chentx
 * @Description: 
 */
Flight::route('/report/all', function() {
    $output = new Output();

    require_once('objects/user.php');
    require_once('objects/report.php');
    $report = new Report();
    if ($data = $report->get_all_reports()) {
        $output->output_json(0, $data);
    } else {
        $output->output_json($report->errCode);
    }
    
});

Flight::route('/report/add', function() {
    $output = new Output();

    $date     = Flight::request()->query['date'];
    $locality = Flight::request()->query['locality'];
    $records  = Flight::request()->query['records'];
    $remarks  = Flight::request()->query['remarks'];

    require_once('objects/report.php');
    $report = new Report();
    if ($data = $report->add($date, $locality, $records, $remarks)) {
        $output->output_json(0, $data);
    } else {
        $output->output_json($report->errCode);
    }
    
});

Flight::route('/report/record/@id:[0-9]+', function($id) {
    $output = new Output();

    require_once('objects/report.php');
    $report = new Report();
    if ($data = $report->get_report($id)) {
        $output->output_json(0, $data);
    } else {
        $output->output_json($report->errCode);
    }
    
});