<?php
/*
 * @Author: chentx
 * @Date: 2020-10-22 17:06:57
 * @LastEditTime: 2020-12-30 16:28:22
 * @LastEditors: chentx
 * @Description: 
 */

class Database{
    private $host;
    private $port;
    private $user;
    private $password;
    private $db;
    public $errCode;

    public $conn;

    public function __construct($config = array()) {
        //定义数据库连接参数
        $this->host = @$config['host'] ? $config['host'] : 'localhost';
        $this->port = @$config['port'] ? $config['port'] : '3306';
        $this->user = @$config['user'] ? $config['user'] : 'root';
        $this->password = @$config['password'] ? $config['password'] : '';
        $this->db = @$config['db'] ? $config['db'] : 'birds';

        $this->db_connect();
    }

    public function db_connect() {
        @$this->conn = new mysqli($this->host.':'.$this->port, $this->user, $this->password, $this->db);

        $conn_err = $this->conn->connect_errno;
        if ($conn_err) {
            return false;
            exit;
        } else {
            $this->conn->set_charset('utf8');
        }
    }

    //使用mysqli_real_escape_string过滤字符串
    public function escape_string($value) {
        $output = $this->conn->real_escape_string($value);
        return $output;
    }

    public function get_rows_count($customSQL, $table, $condition) {
        if ($customSQL) {
            $sql = $customSQL;
        } else {
            $sql = "SELECT COUNT(1) FROM {$table} {$condition}";
        }
        $result = $this->execute_dml($sql);
        return (int)$result->fetch_array()[0];
    }

    public function get_rows($sql) {
        $result = $this->execute_dml($sql);
        if (! $result) {
            return false;
        } else {
            $rows = array();
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            if (empty($rows)) {
                return false;
            } else {
                return $rows;
            }
        }
    }

    public function execute_dml($sql) {
        $result = $this->conn->query($sql);
        if (! $result) {
            return false;
        } else {
            return $result;
        }
    }

    public function execute_dql($sql) {
        $result = $this->conn->query($sql);
        if (! $result) {
            return false;
        } else {
            if ($this->conn->affected_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
}