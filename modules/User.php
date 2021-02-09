<?php
/*
 * @Author: chentx
 * @Date: 2020-12-29 15:58:28
 * @LastEditTime: 2021-02-07 17:02:23
 * @LastEditors: chentx
 * @Description: 
 */
require_once('objects/user.php');

Flight::route('/user/register', function(){
    $output = new Output();
    $username = Flight::request()->query['username'];
    $email    = Flight::request()->query['email'];
    $password = Flight::request()->query['password'];

    $user = new User();
    if ($user->set_signup_info($username, $email, $password)) {
        if ($user->signup()) {
            $output->output_json(0);
        } else {
            $output->output_json($user->errCode);
        }
    } else {
        $output->output_json($user->errCode);
    }
});

Flight::route('/user/login', function(){
    $output = new Output();
    
    $account = Flight::request()->query['account'];
    $password = Flight::request()->query['password'];

    $user = new User();
    if ($data = $user->login($account, $password)) {
        $output->output_json(0, $data);
    } else {
        $output->output_json($user->errCode);
    }
});

Flight::route('/user/logout', function(){
    $output = new Output();

    $user = new User();
    if ($data = $user->logout()) {
        $output->output_json(0);
    } else {
        $output->output_json($user->errCode);
    }
});

Flight::route('/user/profile', function(){
    $output = new Output();

    $user = new User();
    if ($data = $user->get_profile()) {
        $output->output_json(0, $data);
    } else {
        $output->output_json($user->errCode);
    }
});