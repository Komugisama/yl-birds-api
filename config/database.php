<?php
/*
 * @Author: chentx
 * @Date: 2020-10-22 17:06:57
 * @LastEditTime: 2021-02-08 16:23:48
 * @LastEditors: chentx
 * @Description: 
 */

class Database extends mysqli{
    private $host;
    private $port;
    private $user;
    private $password;
    private $db;

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
        $this->connect($this->host . ":" . $this->port, $this->user, $this->password, $this->db);

        $connErr = $this->connect_errno;
        if ($connErr) {
            return false;
            exit;
        } else {
            $this->set_charset('utf8');
        }
    }

    //使用mysqli_real_escape_string过滤字符串
    public function escape_string($value) {
        $output = $this->real_escape_string($value);
        return $output;
    }

    public function get_rows_count($customSQL, $table = '', $condition = '') {
        if ($customSQL) {
            $sql = $customSQL;
        } else {
            $sql = "SELECT COUNT(1) FROM {$table} {$condition}";
        }
        $result = $this->execute_dql($sql);
        return (int)$result->fetch_array()[0];
    }

    public function get_rows($sql) {
        $result = $this->execute_dql($sql);
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

    public function execute_dql($sql) {
        $result = $this->query($sql);
        if (! $result) {
            return false;
        } else {
            return $result;
        }
    }

    public function execute_dml($sql) {
        $result = $this->query($sql);
        if (! $result) {
            return false;
        } else {
            if ($this->affected_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function transaction($sqlList) {
        $this->autocommit(FALSE);
        try {
            foreach ($sqlList AS $sql) {
                $result = $this->query($sql);
                if (! $result) {
                    throw new Exception();
                }
            }
            $this->commit();
            $this->autocommit(TRUE);

            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            $this->autocommit(TRUE);

            return false;
        }
    }

    public function last_insert_id() {
        $sql = "SELECT LAST_INSERT_ID() AS id";
        $rows = $this->get_rows($sql);
        return $rows[0]['id'];
    }
}