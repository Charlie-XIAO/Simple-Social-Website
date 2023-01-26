<?php

class Time {

    public function get_time($pasttime, $today=0) {

        // set timezone
        // access time string with $dt -> format("Y-m-d H:i:s")
        $timezone = "Asia/Hong_Kong";
        $timestamp = time();
        $dt = new DateTime("now", new DateTimeZone($timezone));
        $dt -> setTimestamp($timestamp);

        $today = $dt -> format("Y-m-d H:i:s");
        $datetime1 = date_create($pasttime, timezone_open($timezone));
        $datetime2 = date_create($today, timezone_open($timezone));

        $interval = date_diff($datetime1, $datetime2);

        $answerY = $interval -> format('%y');
        $answerM = $interval -> format('%m');
        $answerD = $interval -> format('%d');

        // check for how much time passed

        if ($answerY >= 1) {
            return $answerY . "年前";
        }
        else if ($answerM >= 1) {
            return $answerM . "月前";
        }
        else if ($answerD >= 1) {
            return $answerD . "天前";
        }
        else {

            $answerH = $interval -> format('%h');
            $answerI = $interval -> format('%i');
            $answerS = $interval -> format('%s');

            if ($answerH >= 1) {
                return $answerH . "小时前";
            }
            else if ($answerI >= 1) {
                return $answerI . "分钟前";
            }
            else if ($answerS > 10) {
                return $answerS . "秒前";
            }
            else {
                return "刚才";
            }
        }

    }

}