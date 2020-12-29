<?php
/*
 * @Author: chentx
 * @Date: 2020-10-29 14:52:52
 * @LastEditTime: 2020-12-29 19:37:42
 * @LastEditors: chentx
 * @Description: 
 */
class Checklist {
    private $db;

    public $locality;
    public $month;
    public $order;
    public $offset;

    public $queryParams = array();
    public $clause;
    public $errCode;

    public function __construct() {
        $this->db = new Database();
    }

    //获取区域鸟类名录
    public function get_region_checklist($locality = null, $month = null, $order = null, $offset = 0) {
        $this->locality = $locality;
        $this->month = $month;
        $this->order = $order;
        $this->offset = (int)$offset;

        $this->set_region_clause();
        $this->set_taxon_clause();

        return $this->get_checklist('aves_record');
    }

    //获取全部鸟类名录
    public function get_all_checklist($order = null, $offset = 0) {
        $this->order = $order;
        $this->offset = (int)$offset;

        $this->set_taxon_clause();

        return $this->get_checklist('aves_checklist');
    }

    public function explode_multi_clause($field, array $values) {
        if (empty($values)) {
            return false;
        } else {
            return "({$field} LIKE '%".implode("%' OR {$field} LIKE '%", $values)."%')";
        }
    }

    public function set_region_clause() {
        if ($this->locality) {
            $localityArray = ['北校区', '南校区及博览园', '渭河杨凌段', '湋水流域'];
            if (! is_array($this->locality)) {
                $this->locality = array($this->locality);
            }
            foreach ($this->locality as $key=>$value) {
                if (! in_array($value, $localityArray)) {
                    unset($this->locality[$key]);
                }
            }
            if ($localityclause = $this->explode_multi_clause('locality', $this->locality)) {
                array_push($this->queryParams, $localityclause);
            }
        }

        if ($this->month) {
            $monthArray = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            if (! is_array($this->month)) {
                $this->month = array($this->month);
            }
            foreach ($this->month as $key=>$value) {
                if (! in_array($value, $monthArray)) {
                    unset($this->month[$key]);
                }
            }
            if ($monthclause = $this->explode_multi_clause('recordMonth', $this->month)) {
                array_push($this->queryParams, $monthclause);
            }
        }
    }

    public function set_taxon_clause() {
        if ($this->order) {
            if (! is_array($this->order)) {
                $this->order = array($this->order);
            }
            foreach ($this->order as $key=>$value) {
                $this->order[$key] = $this->db->escape_string($value);
            }
            if ($orderclause = $this->explode_multi_clause('chineseOrder', $this->order)) {
                array_push($this->queryParams, $orderclause);
            }
        }
    }

    //组合SQL并返回查询结果
    public function get_checklist($table) {
        if (empty($this->queryParams)) {
            $this->clause = "";
        } else {
            $clause = implode(' AND ', $this->queryParams);
            $this->clause = "WHERE ".$clause;
        }

        if ($this->offset < 0) {
            $this->offset = 0;
        }

        $sql = "SELECT taxon_id AS taxonID, chineseName, englishName, chineseOrder, chineseFamily FROM {$table} ".$this->clause." LIMIT {$this->offset}, 20";

        $total = $this->db->get_rows_count(false, $table, $this->clause);
        $data = $this->db->get_rows($sql);

        if ($total && $data) {
            return array('total'=>$total, 'offset'=>$this->offset , 'data'=>$data);
        } else {
            $this->errCode = 102;
            return false;
        }
    }
}