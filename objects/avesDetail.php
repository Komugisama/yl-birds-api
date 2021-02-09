<?php
/*
 * @Author: chentx
 * @Date: 2020-12-26 14:55:43
 * @LastEditTime: 2020-12-31 17:32:21
 * @LastEditors: chentx
 * @Description: 
 */
class Aves {
    public $db;
    public $errCode;

    public $identifier;
    public $taxonID;
    public $scientificName;

    public $recorded = false;
    public $description;
    public $records;
    public $images;

    public function __construct() {
        $this->db = new Database();
    }

    //将识别符转换为鸟类学名
    public function check_aves_name($identifier) {
        $this->identifier = $this->db->escape_string($identifier);
        
        $sql = "SELECT `taxon_id` AS `taxonID`, `scientificName`, `chineseName`, `englishName`, `family`, `chineseFamily`, `order`, `chineseOrder`, `description`, `habitat`, `distribution`, `iucn` FROM aves_checklist WHERE taxon_id = '{$this->identifier}' OR chineseName = '{$this->identifier}' OR scientificName = '{$this->identifier}' OR englishName = '{$this->identifier}'";
        
        if($rows = $this->db->get_rows($sql)) {
            $row = $rows[0];
            $row['taxonID'] = (int)$row['taxonID'];

            $this->description      = $row;
            $this->taxonID        = $row['taxonID'];
            $this->scientificName = $row['scientificName'];

            return true;
        } else {
            $this->errorCode = 102;
            return false;
        }
    }

    //输出数据
    public function get_local_info() {
        $info = $this->description;

        $this->get_records();
        if ($this->recorded) {
            $info += $this->records; 
        }
        if (! empty($this->images)) {
            $info += $this->images;
        }

        return array("data" => $info);
    }

    //获取鸟类在区域内的记录
    public function get_records() {
        $sql = "SELECT `recordMonth`, `locality` FROM aves_regional_checklist WHERE `taxon_id` = '{$this->taxonID}'";
        if($rows = $this->db->get_rows($sql)) {
            $row = $rows[0];
            $this->recorded   = true;
            $this->records = array('localities'=>explode(",", $row['locality']), 'months'=>explode(",", $row['recordMonth']));
            return true;
        } else {
            return false;
        }
    }

    public function get_media() {
        $avesMedia = new AvesMedia($this->scientificName);
        if ($images = $avesMedia->get_images()) {
            $this->images = $images;
        }
    }
}

class AvesMedia {
    public $scientificName;

    public $nameMatch;
    public $speciesKey;
    public $taxon;
    public $images = array();
    public $sounds = array();

    public function __construct($scientificName) {
        $this->scientificName = $scientificName;
        $this->get_species_key();
    }

    public function get_species_key() {
        $nameMatch = new HttpRequest('https://api.gbif.org/v1/species/match');
        $nameMatch->data = array('class' => 'aves', 'name' => $this->scientificName);
        if ($nameMatch->get_request()) {
            $this->taxon = $nameMatch->json_to_array();
            if ($this->taxon['rank'] == 'SPECIES') {
                $this->speciesKey = $this->taxon['speciesKey'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function get_images() {
        $imagesRequest = new HttpRequest('https://api.gbif.org/v1/occurrence/search');
        $imagesRequest->data = array('mediaType' => 'StillImage', 'speciesKey' => $this->speciesKey);
        
        if ($imagesRequest->get_request()) {
            $results = $imagesRequest->json_to_array()['results'];
            foreach ($results as $result) {
                foreach ($result['media'] as $media) {
                    if ($media['type'] == 'StillImage' && ! strstr($media['creator'], 'Xeno-canto')) {
                        array_push($this->images, $this->simplify_media_info($media, $result['eventDate'], 'StillImage'));
                    }
                }
            }
            if ($this->taxon['rank'] == 'SPECIES') {
                $this->speciesKey = $this->taxon['speciesKey'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function simplify_media_info(array $media, $eventDate, $mediaType = 'StillImage') {
        $output = array();
        $output['rightsHolder'] = $media['rightsHolder'];
        $output['eventDate'] = $eventDate;
        $output['license'] = $media['license'];

        switch ($mediaType) {
            case 'Sound':
                $output['identifier'] = $media['identifier'];
                break;
            
            default:
                $output['identifier'] = str_replace('original', 'large', $media['identifier']);
        }
        $output['identifier'] = str_replace('original', 'large', $media['identifier']);

        return $output;
    }
}
