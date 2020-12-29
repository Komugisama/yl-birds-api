<?php
/*
 * @Author: chentx
 * @Date: 2020-10-29 15:17:26
 * @LastEditTime: 2020-12-29 15:32:55
 * @LastEditors: chentx
 * @Description: 
 */
class Output {
    public $language;
    public $code;
    public $message;

    public $msgDefinition = array(

        //success
        '0' => ['en' => 'success', 'zh-cn' => '成功'],
    
        //unknown error
        '1' => ['en' => 'unknown error', 'zh-cn' => '未知错误'],
    
        //database error
        '101' => ['en' => 'invalid query params', 'zh-cn' => '无效的查询参数'],
        '102' => ['en' => 'no result found', 'zh-cn' => '无结果'],
        '103' => ['en' => 'operation failed', 'zh-cn' => '操作失败'],
    
        //user error
    
        //signup error
        '201' => ['en' => 'invalid username', 'zh-cn' => '无效的用户名'],
        '202' => ['en' => 'invalid email', 'zh-cn' => '无效的邮箱'],
        '203' => ['en' => 'invalid password', 'zh-cn' => '无效的密码'],
        '204' => ['en' => 'username already exist', 'zh-cn' => '用户名已存在'],
        '205' => ['en' => 'email already exist', 'zh-cn' => '邮箱已存在'],
    
        //login error
        '211' => ['en' => 'you have already logged in', 'zh-cn' => '您已经登录了'],
        '212' => ['en' => 'user name or password is wrong', 'zh-cn' => '用户名或密码错误'],

        //profile error

        //logout erro
        '231' => ['en' => 'you have not logged in', 'zh-cn'=>'您尚未登录']
    
        //
    );

    public function __construct($customMsgDefinition = false) {
        if (is_array($customMsgDefinition)) {
            $this->msgDefinition = $customMsgDefinition;
        }

        if (@ $_COOKIE['lang'] == 'en') {
            $this->language = 'en';
        } else {
            $this->language = 'zh-cn';
        }
    }

    public function output_json($code, $data = null) {
        $this->code = $code;

        if (array_key_exists((string)$this->code, $this->msgDefinition)) {
            $this->message = $this->msgDefinition[$code];
        } else {
            $this->code = 1;
            $this->message = ['en' => 'unknown error', 'zh-cn' => '未知错误'];
        }
        
        $output['code'] = $this->code;
        $output['msg'] = $this->message[$this->language];

        if ($data) {
            if (! is_array($data)) {
                $data = array('data'=>$data);
            }
            foreach($data as $key=>$value) {
                $output[$key] = $value;
            }
        }
        
        echo json_encode($output, JSON_UNESCAPED_UNICODE);
    }
}