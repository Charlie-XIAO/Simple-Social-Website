<br>
<div style="min-height: 400px; width: 100%; background-color: white; text-align: center;">
    <div style="padding: 20px; max-width: 450px; display: inline-block;">

        <form method="post" enctype="multipart/form-data">

            <?php

                $DB = new Database();
                $sql = "select id, gender from users where userid = '$user_data[userid]' limit 1";
                $info = $DB -> read($sql)[0];
                $gender = $info['gender'];
                $id = $info['id'];

                $settings_class = new Settings();
                $settings = $settings_class -> get_settings($user_data['userid']);

                echo "<br><br><b>关于</b><hr>";
                if (is_array($settings) && $settings['about'] != "") {
                    echo "<div style='margin: 8px; width: 400px; border-radius: 4px; padding: 4px; font-size: 14px;' name='about'>$settings[about]</div><br>";
                }
                else {
                    echo "<br>未填写<br><br>";
                }

                // if female and self (or admin) login show menstrual cycle
                if ($user_data['userid'] == $_SESSION['project_rrmd100zsyff_userid'] || $_SESSION['project_rrmd100zsyff_userid'] == "9964070") {

                    if (in_array($id, get_rm_ids()) && $user_data['menstruation'] != "") {
                    // FOR TESTING PURPOSE, USE THE FOLLOWING CONDITION INSTEAD OF THE ABOVE CONDITION
                    //if ($user_data['menstruation'] != "") {
                        echo "<br><br><b>生理期记录</b><hr>";
                        
                        $MPdata = json_decode($user_data['menstruation'], true);
                        $prev_start = "START";
                        $tag = "";

                        // ========== CHANGE PREDICTION BASIS ========== //
                        $DURATION_AVERAGE = 6;  // average duration of the menstrual period
                        $CYCLE_AVERAGE = 29;  // average length of the menstrual cycle
                        // ========== END CHANGE PREDICTION BASIS ========== //
                        
                        echo "<div style='text-align: left; font-size: 14px; margin-left: 10px; margin-right: 10px;'>";
                        foreach ($MPdata as $value) {
                            
                            if ($value['MPtype'] == "start") {
                                echo "<br><span style='font-weight: bold; color: #405d9b;'>";
                                echo $value['MPdate'] . " 月经来潮";
                                echo "</span>";
                                if ($prev_start != "START") {
                                    $date1 = new DateTime($prev_start);
                                    $date2 = new DateTime($value['MPdate']);
                                    $interval = $date1 -> diff($date2);
                                    $interval_num = $interval -> format("%r%a");
                                    if ($interval_num < 20 || $interval_num > 35) {
                                        echo "<span style='color: #8b0000;'>";
                                    }
                                    else {
                                        echo "<span style='color: #888;'>";
                                    }
                                    echo "&nbsp (月经周期: 约" . $interval_num . "天)";
                                    echo "</span>";
                                }
                                echo "<br>";
                                $prev_start = $value['MPdate'];
                                $tag = "PERIOD";
                            }
                            else if ($value['MPtype'] == "end") {
                                echo "<span style='font-weight: bold; color: #405d9b;'>";
                                echo $value['MPdate'] . " 结束";
                                echo "</span>";
                                $date1 = new DateTime($prev_start);
                                $date2 = new DateTime($value['MPdate']);
                                $duration = $date1 -> diff($date2);
                                $duration_num = $duration -> format("%r%a") + 1;
                                if ($duration_num < 3 || $duration_num > 8) {
                                    echo "<span style='color: #8b0000;'>";
                                }
                                else {
                                    echo "<span style='color: #888;'>";
                                }
                                echo "&nbsp (经期时长: 约" . $duration_num . "天)";
                                echo "</span><br>";
                                $tag = "NON-PERIOD";
                            }
                            else if ($value['MPtype'] == "record") {
                                echo "[" . $value['MPdate'] . "] " . unicode_decode($value['MPnote']) . "<br>";
                            }
                        }

                        echo "<br><div>";
                        echo "<div style='font-weight: bold; color: #405d9b;'>预测数据</div>";
                        if ($tag == "PERIOD") {
                            echo "<div>本次经期结束: " . date("Y-m-d", strtotime($DURATION_AVERAGE . " days", strtotime($prev_start))) . "</div>";
                        }
                        if ($tag == "NON-PERIOD" || $tag == "PERIOD") {
                            echo "<div>易孕期: " . date("Y-m-d", strtotime($CYCLE_AVERAGE - 19 . " days", strtotime($prev_start))) . " 至 " . date("Y-m-d", strtotime($CYCLE_AVERAGE - 10 . " days", strtotime($prev_start))) . "</div>";
                            echo "<div>排卵日: " . date("Y-m-d", strtotime($CYCLE_AVERAGE - 14 . " days", strtotime($prev_start))) . "</div>";
                            echo "<div>下次月经周期: " . date("Y-m-d", strtotime($CYCLE_AVERAGE . " days", strtotime($prev_start))) . " 至 " . date("Y-m-d", strtotime($CYCLE_AVERAGE + $DURATION_AVERAGE . " days", strtotime($prev_start))) . "</div>";
                        }
                        echo "</div>";

                        echo "</div>";
                        
                    }

                }
                
            ?>

        </form>

    </div>
</div>