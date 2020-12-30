<?php
/*
 * @Author: chentx
 * @Date: 2020-12-26 14:55:43
 * @LastEditTime: 2020-12-30 16:32:01
 * @LastEditors: chentx
 * @Description: 
 */
class Aves {
    public $db;
    public $errCode;

    public $identifier;
    public $scientificName;
    public $basicInfo;

    public function __construct() {
        $this->db = new Database();
    }

    //将识别符转换为鸟类学名
    public function check_aves_name($identifier) {
        $this->identifier = $this->db->escape_string($identifier);
        $sql = "SELECT * FROM aves_checklist WHERE taxon_id = '{$this->identifier}' OR chineseName = '{$this->identifier}' OR scientificName = '{$this->identifier}' OR englishName = '{$this->identifier}'";
        
        if($rows = $this->db->get_rows($sql)) {
            $this->basicInfo = $rows[0];
            $this->scientificName = $rows[0]['scientificName'];
            return true;

        } else {
            $this->errorCode = 102;
            return false;
        }
    }
}
