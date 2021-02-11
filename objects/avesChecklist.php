<?php
/*
 * @Author: chentx
 * @Date: 2020-10-29 14:52:52
 * @LastEditTime: 2021-02-11 17:10:02
 * @LastEditors: chentx
 * @Description: 
 */
class Checklist {
    private $db;

    public $locality;
    public $month;
    public $order;
    public $name;
    public $offset;

    public $queryParams = array();
    public $clause;
    public $errCode;

    public function __construct() {
        $this->db = new Database();
    }

    //获取区域鸟类名录
    public function get_region_checklist($locality = null, $month = null, $order = null, $name = null, $offset = 0) {
        $this->locality = $locality;
        $this->month    = $month;
        $this->order    = $order;
        $this->name     = $name;
        $this->offset   = (int)$offset;

        $this->set_region_clause();
        $this->set_taxon_clause();

        return $this->get_checklist('aves_regional_checklist');
    }

    //获取全部鸟类名录
    public function get_all_checklist($order = null, $offset = 0) {
        $this->order  = $order;
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
        $validator = new Validator();
        if ($this->locality) {
            if (! is_array($this->locality)) {
                $this->locality = explode(',', $this->locality);
            }
            foreach ($this->locality as $key=>$value) {
                if (! $validator->locality($value)) {
                    unset($this->locality[$key]);
                }
            }
            if ($localityClause = $this->explode_multi_clause('locality', $this->locality)) {
                array_push($this->queryParams, $localityClause);
            }
        }

        if ($this->month) {
            if (! is_array($this->month)) {
                $this->month = explode(',', $this->month);
            }
            foreach ($this->month as $key=>$value) {
                if (! $validator->month(strtolower($value))) {
                    unset($this->month[$key]);
                }
            }
            if ($monthClause = $this->explode_multi_clause('recordMonth', $this->month)) {
                array_push($this->queryParams, $monthClause);
            }
        }
    }

    public function set_taxon_clause() {
        if ($this->order) {
            if (! is_array($this->order)) {
                $this->order = explode(',', $this->order);
            }
            foreach ($this->order as $key=>$value) {
                $this->order[$key] = $this->db->escape_string($value);
            }
            if ($orderClause = $this->explode_multi_clause('chineseOrder', $this->order)) {
                array_push($this->queryParams, $orderClause);
            }
        }

        if ($this->name) {
            $nameClause = "(chineseName LIKE '{$this->name}%' OR scientificName LIKE '{$this->name}%' OR englishName LIKE '{$this->name}%')";

            array_push($this->queryParams, $nameClause);
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
            return array('total'=>0, 'offset'=>$this->offset , 'data'=>array());
        }
    }
}

class Autocomplete {
    private $db;

    public $table = "aves_regional_checklist";
    public $input;

    public function __construct($input = "", $mode) {
        $this->db = new Database();
        $this->input = $this->db->escape_string($input);

        if ($mode == "all") {
            $this->table = "aves_checklist";
        }
    }

    public function search() {
        $sql = "SELECT `chineseName`, `scientificName`, `englishName`, `chineseOrder`, `order`, `chineseFamily`, `family`, `abbreviation` FROM `{$this->table}` WHERE `chineseName` LIKE '{$this->input}%' OR `scientificName` LIKE '{$this->input}%' OR `englishName` LIKE '{$this->input}%' OR `abbreviation` LIKE '{$this->input}%'";
        if($result = $this->db->get_rows($sql)) {
            return $result;
        } else {
            return array();
        }
    }
}

class Taxon {
    private $db;

    public $table = "aves_regional_checklist";

    public function __construct($mode) {
        $this->db = new Database();

        if ($mode == "all") {
            $this->table = "aves_checklist";
        }
    }

    public function search() {
        $sql = "SELECT DISTINCT `chineseOrder`, `order` FROM `{$this->table}`";
        if($result = $this->db->get_rows($sql)) {
            return $result;
        } else {
            return array();
        }
    }
}