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
                $settings = $settings_class -> get_settings($_SESSION['project_rrmd100zsyff_userid']);
                
                echo "<br><br><b>设置个人信息</b><hr style='width: 400px;'>";
                echo "如需修改密码，修改密码需确保与确认修改密码一致，<br>否则将保留原密码<br>";
                if (is_array($settings)) {

                    echo "<br><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;姓名&nbsp</span><input type='text' id='textbox' name='name' value='$settings[name]' placeholder='姓名'>";
                    echo "<br><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;性别&nbsp</span><select id='textbox' style='width: 310px; height: 35px;' name='gender'>";
                    echo "<option>$settings[gender]</option>";
                    echo "<option>男</option>";
                    echo "<option>女</option>";
                    echo "</select>";
                    echo "<br><span>&nbsp;&nbsp;&nbsp;用户名&nbsp</span><input type='text' id='textbox' name='username' value='$settings[username]' placeholder='用户名'>";
                    echo "<br><span style='font-weight: bold;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@</span><input type='text' id='textbox' name='tag_name' value='$settings[tag_name]' placeholder='标签'>";
                    echo "<br><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;密码&nbsp</span><input type='password' id='textbox' name='password' placeholder='修改密码'>";
                    echo "<br><span>确认密码&nbsp</span><input type='password' id='textbox' name='confirmpassword' placeholder='确认修改密码'>";
                    
                    echo "<br><br>关于我<br>";
                    echo "<textarea style='margin: 8px; height: 150px; width: 300px; border-radius: 4px; border: solid 1px #aaa; padding: 4px; font-size: 14px; resize: vertical; min-height: 50px;' name='about'>$settings[about]</textarea><br>";

                    if ($gender == "女" && in_array($id, get_rm_ids())) {

                        echo "<br><br><b>生理期记录</b><hr style='width: 400px;'>";
                        echo "<br>记录日期<br>";
                        echo "<input type='date' id='textbox' name='menstrual_start'>";
                        echo "<br>记录类型<br>";
                        echo "<select id='textbox' style='width: 310px; height: 35px;' name='menstrual_type' onchange='CheckMenstrualType(this.value);'>";
                        echo "<option>起始日期</option>";
                        echo "<option>终止日期</option>";
                        echo "<option>记录日期</option>";
                        echo "</select><br>";
                        echo "<span style='display: none;' id='menstrual_content_title'>记录内容</span>";
                        echo "<br><textarea style='display: none; margin: 8px; height: 150px; width: 300px; border-radius: 4px; border: solid 1px #aaa; padding: 4px; font-size: 14px; resize: vertical; min-height: 50px;' name='menstrual_content' id='menstrual_content'></textarea><br>";

                    }

                    echo "<input style='float: middle; background-color: #405d9b; border: none; color: white; padding: 4px; font-size: 14px; border-radius: 2px; width: 50px; min-width: 50px; cursor: pointer;' type='submit' value='保存'>";
                }
                
            ?>

        </form>

    </div>
</div>

<script type="text/javascript">

    function CheckMenstrualType(val){

        var element1 = document.getElementById('menstrual_content_title');
        var element2 = document.getElementById('menstrual_content');

        if (val == "记录日期") {
            element1.style.display = 'inline-block';
            element2.style.display = 'inline-block';
        }
        else {
            element1.style.display = 'none';
            element2.style.display = 'none';
        }
    }

</script> 
