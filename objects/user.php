<?php
/*
 * @Author: chentx
 * @Date: 2020-10-29 14:23:40
 * @LastEditTime: 2020-12-29 15:34:16
 * @LastEditors: chentx
 * @Description: 
 */
class User {

    private $db;

    public $id;
    public $username;
    public $email;
    public $account;
    public $password;
    public $logined;
    public $token;
    public $errCode;

    public function __construct() {
        $this->db = new Database;
    }

    public function set_signup_info($username, $email, $password) {
        $reg = "/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:\"\"\d\w]{6,20}$/";

        if (! preg_match($reg, $username)) {
            $this->errCode = 201;
            return false;
        } else if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errCode = 202;
            return false;
        } else if (! preg_match($reg, $password)) {
            $this->errCode = 203;
            return false;
        } else {
            $this->username = $this->db->escape_string($username);
            $this->email    = $this->db->escape_string($email);
            $this->password = password_hash($password, PASSWORD_DEFAULT);
            return true;
        }
    }

    public function signup() {
        if ($this->is_already_exist('username')) {
            $this->errCode = 204;
            return false;
        } else if ($this->is_already_exist('email')) {
            $this->errCode = 205;
            return false;
        } else {
            $sql = "INSERT INTO user(`username`, `email`, `password`) VALUE('{$this->username}', '{$this->email}', '{$this->password}')";
            $result = $this->db->execute_dql($sql);
            if ($this->db->conn->affected_rows == 1) {
                return true;
            } else {
                $this->errCode = 1;
                return false;
            }
        }
    }

    public function login($account, $password) {
        if ($this->is_already_login()) {
            $this->errCode = 211;
            return false;
        } else {
            $account = $this->db->escape_string($account);
            $sql = "SELECT `id`, `username`, `email`, `password`, `created` FROM user WHERE `username` = '{$account}' OR email = '{$account}'";
            $result = $this->db->execute_dml($sql);
            if ($result->num_rows != 1) {
                $this->errCode = 212;
                return false;
            } else {
                $row = $result->fetch_assoc();
                if(password_verify($password, $row['password'])) {
                    $this->id       = $row['id'];
                    $this->username = $row['username'];
                    $this->email    = $row['email'];
                    $this->password = $row['password'];
    
                    $this->set_session();
                    return array('token' => $this->token);
                } else {
                    $this->errCode = 212;
                    return false;
                }
            }
        }
    }

    public function logout() {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            return true;
        } else {
            $this->errCode = 231;
            return false;
        }
    }

    public function is_already_exist($field = 'username') {
        //拼接对应场景验证语句
        switch ($field) {
            case 'email':
                $sql = "SELECT 1 FROM user WHERE  `email` = '{$this->email}'";
                break;

            default:
                $sql = "SELECT 1 FROM user WHERE `username` = '{$this->username}'";
        }
        
        $result = $this->db->execute_dml($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function is_already_login() {
        if (isset($_SESSION['user'])) {
            return true;
        } else {
            return false;
        }
    }

    public function set_session() {
        $this->logined = time();
        $this->token = md5($this->id.$this->logined);
        $_SESSION['user'] = array(
            'id'       => $this->id,
            'username' => $this->username,
            'email'    => $this->email,
            'logined'  => $this->logined,
            'token'    => $this->token
        );
    }

}