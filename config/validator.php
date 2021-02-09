<?php
/*
 * @Author: chentx
 * @Date: 2020-10-22 17:06:57
 * @LastEditTime: 2021-02-08 15:04:43
 * @LastEditors: chentx
 * @Description: 验证器类
 */

class Validator{
    public $locality = ['北校区', '南校区及博览园', '渭河杨凌段', '湋水流域'];
    public $month = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

    public function locality($value) {
        try {
            if (in_array($value, $this->locality)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function date($value) {
        try {
            $format = 'Y-m-d';
            $unixTime = strtotime($value);
    
            if ($unixTime && $unixTime <= time()) {
                if (date($format, $unixTime) == $value) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function month($value) {
        try {
            if (in_array($value, $this->month)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function aves($id) {
        $db = new Database();
        $condition = "WHERE taxon_id = '{$id}'";
        if ($db->get_rows_count(0, 'aves_checklist', $condition)) {
            return true;
        } else {
            return false;
        }
    }
}