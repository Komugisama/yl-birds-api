<?php
/*
 * @Author: chentx
 * @Date: 2020-12-30 17:30:30
 * @LastEditTime: 2021-02-07 17:22:18
 * @LastEditors: chentx
 * @Description: 
 */

class HttpRequest {
    public $url;
    public $header;
    public $data;

    public $return;
    public $error;
    
    public function __construct($url) {
        $this->url = $url;
    }

    //通过cUrl发起HTTP请求
    public function get_request() {
        //初使化curl
        $curl = curl_init();

        //发送数据
        if (! empty($this->data)) {
            $url = $this->url.'?'.http_build_query($this->data);
        }
        
        //指定URL
        curl_setopt($curl, CURLOPT_URL, $url);
        
        //设定请求后返回结果
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        //设置请求头
        if(! empty($this->header)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($curl, CURLOPT_HEADER, 0);
        }
        
        //忽略证书
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    
        //设置超时时间
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        
        //发送请求
        if ($return = curl_exec($curl)) {
            $this->return = $return;
            curl_close($curl);
            return true;
        } else {
            $this->error = curl_error($curl);
            curl_close($curl);
            return false;
        }
    }

    //通过cUrl发起POST请求
    public function post_request() {
        $curl = curl_init();
    
        //指定URL
        curl_setopt($curl, CURLOPT_URL, $this->url);
    
        //设定请求后返回结果
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        //设置请求头
        if(! empty($this->header)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($curl, CURLOPT_HEADER, 0);
        }
        
        //设置发送数据
        if (! empty($this->data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
             curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data);
        }
    
        //忽略证书
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    
        //设置超时时间
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    
        //发送请求
        if ($return = curl_exec($curl)) {
            $this->return = $return;
            curl_close($curl);
            return true;
        } else {
            $this->error = curl_error($curl);
            curl_close($curl);
            return false;
        }
    }

    public function json_to_array() {
        return json_decode($this->return, 1);
    }
}
