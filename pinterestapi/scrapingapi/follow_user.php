<?php

//ini_set('open_basedir', 'none');
define('SITE_PATH', dirname(__FILE__) . '/');
include_once 'classes/basicfunctions.php';
//ini_set('open_basedir', 'none');

define('DEBUG_MODE', $debug_mode);
define('LOGIN_TIMEOUT', $login_timeout);


$con = mysql_connect('internal-db.s152367.gridserver.com', 'db152367_manager', 'dbpinterestmanager');

if (!$con) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db("db152367_pinterest", $con);

$objPin = new Pinterest();
$baseFunctions = new BasicFunctions();

$current_time = date('Y-m-d H:i', time());

$users_not_allowed = mysql_query("SELECT * FROM users where status = '1' AND  DATE_FORMAT(next_run, '%Y-%m-%d %H:%i') < '$current_time'");
while ($record = mysql_fetch_assoc($users_not_allowed)) {
    mysql_query("Update users SET status='0' where user_id ='$record[user_id]'");
}

$result = mysql_query("SELECT f.* FROM p_followers f INNER JOIN users u ON f.f_parent_user_id=u.user_id AND u.status='0' where DATE_FORMAT(f.f_run_time, '%Y-%m-%d %H:%i') < '$current_time' AND f.f_status='0'");




while ($user_row = mysql_fetch_array($result)) {
    $user[] = $user_row;
    mysql_query("Update p_followers SET f_status='1' where follower_id ='$user_row[follower_id]'");
}
if ($user) {
    foreach ($user as $user_row) {

        $parent = mysql_query("SELECT * FROM users where user_id = '$user_row[f_parent_user_id]'");
        $parentuser = mysql_fetch_array($parent);

        $acc['email'] = $parentuser['u_email'];
        $acc['password'] = $parentuser['u_password'];
        $acc['username'] = $parentuser['username'];
        $acc['proxy'] = NULL;

        $html = $objPin->get_login_page($acc);
        $auth_token = $objPin->get_auth_token($html);

        if (!$objPin->is_already_loggedin($html)) {
            $result = $objPin->make_login($acc, $auth_token);
            if ($result == false) {
                echo '<h1>Login Faild!</h1>';
                exit;
            }
        }
        $flag = false;

        $flag = $baseFunctions->follow_single($acc, $user_row['f_username'], $auth_token);

        $time = strtotime('+60 minutes', $user_row['f_run_time']);
        $runtime = date('Y-m-d H:i', $time);
        //echo "UPDATE `p_followers` SET  `f_status`=0 , `f_run_time`=$runtime  WHERE  follower_id = '$user_row[follower_id]'";
        if ($flag == false) {
            mysql_query("UPDATE `p_followers` SET  `f_status`=0 , `f_run_time`=$runtime  WHERE  follower_id = '$user_row[follower_id]'");

            mysql_query("Update users SET status='1', next_run='$runtime' where user_id ='$user_row[f_parent_user_id]'");
        } else {
            mysql_query("DELETE FROM `p_followers` WHERE follower_id = '$user_row[follower_id]'");
        }

        sleep(30);
    }
}

