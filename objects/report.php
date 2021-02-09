<?php
/*
 * @Author: chentx
 * @Date: 2021-01-02 21:16:42
 * @LastEditTime: 2021-02-09 10:20:43
 * @LastEditors: chentx
 * @Description: 
 */
class Report {
    public $db;
    public $user;
    public $errCode;

    public $id;
    public $date;
    public $locality;
    public $remarks;
    public $records;

    public function __construct() {
        $this->db   = new Database();
        $this->user = new User();
    }

    public function get_all_reports() {
        if ($this->user->is_already_login()) {
            $sql = "SELECT * FROM aves_event WHERE user_id = '{$this->user->id}'";
            if ($result = $this->db->execute_dql($sql)) {
                $total = $result->num_rows;
                $data = array();
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                return array('total'=>$total, 'data'=>$data);
            } else {
                return false;
            }
        } else {
            $this->errCode = 301;
            return false;
        }
    }

    public function get_report($id) {
        if ($this->user->is_already_login()) {
            if ($reportInfo = $this->report_exist($id)) {
                $sql = "SELECT occurrence_id, taxon_id, chineseName, occurrenceRemarks FROM aves_occurrence WHERE user_id = '{$this->user->id}' AND event_id = '{$id}'";
                if ($result = $this->db->execute_dql($sql)) {
                    $total = $result->num_rows;
                    $data = $reportInfo;
                    while ($row = $result->fetch_assoc()) {
                        $data['records'][] = $row;
                    }
                    return array('total'=>$total, 'data'=>$data);
                } else {
                    $this->errCode = 1;
                    return false;
                }
            } else {
                $this->errCode = 311;
                return false;
            }
        } else {
            $this->errCode = 301;
            return false;
        }
    }

    public function add($date, $locality, $records, $remarks) {
        if ($this->user->is_already_login()) {
            $validator = new Validator();
            if (! $validator->date($date)) {
                $this->errCode = 321;
                return false;
            }

            if (! $validator->locality($locality)) {
                $this->errCode = 322;
                return false;
            }

            try {
                $recordsArray = json_decode($records, 1);
                if (empty($recordsArray)) {
                    $this->errCode = 331;
                    return false;
                } else {
                    $avesList=array();
                    foreach ($recordsArray AS $key=>$record) {
                        if (empty($record['id'])) {
                            unset($recordsArray[$key]);
                        } else {
                            if (! in_array($record['id'], $avesList) && $validator->aves($record['id'])) {
                                array_push($avesList, $record['id']);
                                if (isset($record['remarks'])) {
                                    $recordsArray[$key]['remarks']=$this->db->escape_string($record['remarks']);
                                } else {
                                    $recordsArray[$key]['remarks']='';
                                }
                            } else {
                                unset($recordsArray[$key]);
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $this->errCode = 331;
                return false;
            }

            try {
                if (isset($remarks)) {
                    $remarks = $this->db->escape_string($remarks);
                } else {
                    $remarks = '';
                }
            } catch (Exception $e) {
                $this->errCode = 323;
                return false;
            }

            try {
                $this->db->autocommit(FALSE);
                $result = $this->db->query("INSERT INTO aves_event_raw(user_id, eventDate, locality, eventRemarks) VALUE('{$this->user->id}', '{$date}', '{$locality}', '{$remarks}')");
                $eventID = $this->db->last_insert_id();
                foreach ($recordsArray AS $key=>$record) {
                    $result = $this->db->query("INSERT INTO aves_occurrence_raw(event_id, taxon_id, occurrenceRemarks) VALUE('{$eventID}', '{$record['id']}', '{$record['remarks']}')");
                    if (! $result) {
                        throw new Exception();
                    }
                }
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollback();
                return false;
            }
            
            return true;
        } else {
            $this->errCode = 301;
            return false;
        }
    }

    public function delete_report() {
        
    }

    public function report_exist($id) {
        $id = $this->db->escape_string($id);
        $sql = "SELECT event_id, eventDate, locality, eventRemarks FROM aves_event WHERE event_id = '{$id}' AND user_id = '{$this->user->id}'";
        if ($result = $this->db->execute_dql($sql)) {
            $row = $result->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}