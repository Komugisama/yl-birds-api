<?php
/*
 * @Author: chentx
 * @Date: 2020-12-29 12:00:13
 * @LastEditTime: 2021-02-07 16:18:42
 * @LastEditors: chentx
 * @Description: 
 */

//鸟类详细信息
Flight::route('/aves-detail/@identifier', function($identifier) {
    $output = new Output();

    require_once('objects/avesDetail.php');
    $aves = new Aves();
    if ($aves->check_aves_name($identifier)) {
        $output->output_json(0, $aves->get_local_info());
    } else {
        $output->output_json($aves->errorCode);
    }
});