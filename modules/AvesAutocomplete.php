<?php
/*
 * @Author: chentx
 * @Date: 2020-12-29 12:00:13
 * @LastEditTime: 2021-02-11 17:11:48
 * @LastEditors: chentx
 * @Description: 
 */

//区域鸟类自动补全
Flight::route('/aves-autocomplete/region/@input', function($input){
    $output = new Output();

    require_once('objects/avesChecklist.php');
    $autocomplete = new Autocomplete($input, 'region');
    $data = $autocomplete->search();
    $output->output_json(0, array("data"=>$data));
});

//全部鸟类自动补全
Flight::route('/aves-autocomplete/all/@input', function($input){
    $output = new Output();

    require_once('objects/avesChecklist.php');
    $autocomplete = new Autocomplete($input, 'all');
    $data = $autocomplete->search();
    $output->output_json(0, array("data"=>$data));
});

Flight::route('/aves-taxon/all', function(){
    $output = new Output();

    require_once('objects/avesChecklist.php');
    $taxon = new Taxon('all');
    $data = $taxon->search();
    $output->output_json(0, array("data"=>$data));
});

Flight::route('/aves-taxon/region', function(){
    $output = new Output();

    require_once('objects/avesChecklist.php');
    $taxon = new Taxon('region');
    $data = $taxon->search();
    $output->output_json(0, array("data"=>$data));
});