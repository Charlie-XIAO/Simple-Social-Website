<?php

class Settings {

    public function get_settings($id) {

        $DB = new Database();

        $sql = "select * from users where userid = '$id' limit 1";
        $row = $DB -> read($sql);

        if (is_array($row)) {
            return $row[0];
        }
    }

    public function save_settings($data, $id) {

        // json cannot recognize chinese characters so convert type and encode content
        if (isset($data['menstrual_type'])) {
            if ($data['menstrual_type'] == "起始日期") {
                $data['menstrual_type'] = "start";
            }
            else if ($data['menstrual_type'] == "终止日期") {
                $data['menstrual_type'] = "end";
            }
            else if ($data['menstrual_type'] == "记录日期") {
                $data['menstrual_type'] = "record";
            }
            $data['menstrual_content'] = unicode_encode($data['menstrual_content']);
        }

        // check whether to update password
        $password = $data['password'];
        if (strlen($password) < 30) {
            if ($data['password'] == $data['confirmpassword'] && $data['password'] != "") {
                $data['password'] = hash("sha1", $password);
                $data['url_address'] = $data['name'] . "_U:" . $data['username'] . "_P:" . $password;
            }
            else {
                unset($data['password']);
            }
        }
        unset($data['confirmpassword']);

        // check whether to update menstruation data
        if (isset($data['menstrual_type'])) {
            if ($data['menstrual_start'] == "") {
                unset($data['menstrual_start']);
                unset($data['menstrual_type']);
                unset($data['menstrual_content']);
            }
        }

        $no_menstrual_data = $data;
        if (isset($no_menstrual_data['menstrual_start'])) {
            unset($no_menstrual_data['menstrual_start']);
        }
        if (isset($no_menstrual_data['menstrual_type'])) {
            unset($no_menstrual_data['menstrual_type']);
        }
        if (isset($no_menstrual_data['menstrual_content'])) {
            unset($no_menstrual_data['menstrual_content']);
        }

        $sql = "update users set ";
        foreach ($no_menstrual_data as $key => $value) {
            $sql .= $key . " = '" . $value . "', ";
        }
        $sql = trim($sql, ", ");
        $sql .= " where userid = '$id' limit 1";

        $DB = new Database();
        $DB -> save($sql);

        // menstruation settings
        if (isset($data['menstrual_start']) && isset($data['menstrual_type'])) {

            $sql = "select menstruation from users where userid = '$id' limit 1";
            $result = $DB -> read($sql);

            if (is_array($result[0])) {

                $curmenstruations = json_decode($result[0]['menstruation'], true);

                $arr["MPdate"] = $data['menstrual_start'];
                $arr["MPtype"] = $data['menstrual_type'];
                if (isset($data['menstrual_content'])) {
                    $arr["MPnote"] = $data['menstrual_content'];
                }
                else {
                    $arr["MPnote"] = "";
                }
                $curmenstruations[] = $arr;

                // sort by date
                $reference_array = Array();
                foreach ($curmenstruations as $key => $row) {
                    $reference_array[$key] = $row['MPdate'];
                }
                array_multisort($reference_array, SORT_ASC, $curmenstruations);

                // make start at first and end at last
                foreach (array_slice($curmenstruations, 0, -1) as $key => $row) {
                    if ($curmenstruations[$key]['MPdate'] == $curmenstruations[$key + 1]['MPdate']) {
                        if ($curmenstruations[$key]['MPtype'] == "end" || $curmenstruations[$key + 1]['MPtype'] == "start") {
                            $temp = $curmenstruations[$key];
                            $curmenstruations[$key] = $curmenstruations[$key + 1];
                            $curmenstruations[$key + 1] = $temp;
                        }
                    }
                }

                $menstruations = json_encode($curmenstruations);
                $sql = "update users set menstruation = '$menstruations' where userid = '$id' limit 1";
                $DB -> save($sql);

            }
            else {
                $arr["MPdate"] = $data['menstrual_start'];
                $arr["MPtype"] = $data['menstrual_type'];
                if (isset($data['menstrual_content'])) {
                    $arr["MPnote"] = $data['menstrual_content'];
                }
                else {
                    $arr["MPnote"] = "";
                }
                $array[] = $arr;
    
                $menstruations = json_encode($array);
                $sql = "update users set menstruation = '$menstruations' where userid = '$id' limit 1";
                $DB -> save($sql);
            }

        }
    }

}