<?php
/*
 * @Author: chentx
 * @Date: 2020-10-29 14:52:52
 * @LastEditTime: 2020-12-29 14:37:12
 * @LastEditors: chentx
 * @Description: 
 */
class Checklist {
    private $db;

    public $queryParams;
    public $offset;
    public $condition;

    public function __construct($locality = null, $month = null, $order = null, $offset = 0) {
        $this->db = new Database();
        $this->queryParams = array();
        if ($locality) {
            $localityArray = ['北校区', '南校区及博览园', '渭河杨凌段', '湋水流域'];
            if (! is_array($locality)) {
                $locality = array($locality);
            }
            foreach ($locality as $key=>$value) {
                if (! in_array($value, $localityArray)) {
                    unset($locality[$key]);
                }
            }
            if ($localityCondition = $this->explode_condition('locality', $locality)) {
                array_push($this->queryParams, $localityCondition);
            }
        }
        if ($month) {
            $monthArray = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            if (! is_array($month)) {
                $month = array($month);
            }
            foreach ($month as $key=>$value) {
                if (! in_array($value, $monthArray)) {
                    unset($month[$key]);
                }
            }
            if ($monthCondition = $this->explode_condition('recordMonth', $month)) {
                array_push($this->queryParams, $monthCondition);
            }
        }
        if ($order) {
            if (! is_array($order)) {
                $order = array($order);
            }
            foreach ($order as $key=>$value) {
                $order[$key] = $this->db->escape_string($value);
            }
            if ($orderCondition = $this->explode_condition('chineseOrder', $order)) {
                array_push($this->queryParams, $orderCondition);
            }
        }
        if ($offset <= 0) {
            $this->offset = 0;
        } else {
            $this->offset = (int)$offset;
        }
        $this->set_condition();
    }

    public function explode_condition($field, array $values) {
        if (empty($values)) {
            return false;
        } else {
            return "({$field} LIKE '%".implode("%' OR {$field} LIKE '%", $values)."%')";
        }
    }

    //生成查询条件
    public function set_condition() {
        if (empty($this->queryParams)) {
            $this->condition = "";
        } else {
            $condition = implode(' AND ', $this->queryParams);
            $this->condition = "WHERE ".$condition;
        }
    }

    //获取校园内的鸟类名录
    public function campus_checklist() {
        $sql = "SELECT taxon_id, chineseName, englishName, chineseOrder, chineseFamily FROM aves_record ".$this->condition." LIMIT {$this->offset}, 20";

        $total = $this->db->get_rows_count(false, 'aves_record', $this->condition);
        $data = $this->db->get_rows($sql);

        if ($total && $data) {
            return array('total'=>$total, 'data'=>$data);
        } else {
            return false;
        }
    }
}