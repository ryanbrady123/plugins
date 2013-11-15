<?php

define('SITE_PATH', dirname(__FILE__) . '/');
include_once 'classes/basicfunctions.php';

define('DEBUG_MODE', $debug_mode);
define('LOGIN_TIMEOUT', $login_timeout);

//ini_set(display_errors,1);
//error_reporting(E_ALL);


function scheduler($margin = 0, $time, $firsttime = 0) {
    $random = rand(5, $margin);
    if ($firsttime == 1) {
        $random = $random + 10;
    }
    return $time = strtotime('+' . $random . ' minutes', $time);
}

$objPin = new Pinterest();
$baseFunctions = new BasicFunctions();

$con = mysql_connect('internal-db.s152367.gridserver.com', 'db152367_manager', 'dbpinterestmanager');
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("db152367_pinterest", $con);

$result = mysql_query("SELECT * FROM adding_cron where status = 0");

while ($user_row = mysql_fetch_assoc($result)) {

    $admin_login = mysql_query("SELECT * FROM users where user_id = '$user_row[parent_u_id]'");
    $admin = mysql_fetch_array($admin_login);

    $acc['email'] = $admin['u_email'];
    $acc['password'] = $admin['u_password'];
    $acc['username'] = $admin['username'];
    $acc['proxy'] = NULL;

    $admin_counts = $baseFunctions->get_counts($acc);
    $admin_following_count = $admin_counts['following_count'];
    $admin_page_count = ceil($admin_following_count / 50);
    $admin_followers = $baseFunctions->get_all_folowing($acc, $admin_page_count);

    for ($j = 0; $j < sizeof($admin_followers); $j++) {
        mysql_query("INSERT INTO p_followers (f_username,f_parent_user_id, f_run_time, f_status )VALUES ('$admin_followers[$j]','$user_row[parent_u_id]', '2012-07-21 19:40:10','4')");
    }


    $html = $objPin->get_login_page($acc);
    $auth_token = $objPin->get_auth_token($html);
    if (!$objPin->is_already_loggedin($html)) {
        $result = $objPin->make_login($acc, $auth_token);
        if ($result == false) {
            echo '<h1>User Could not login!</h1>';
            continue;
        }
    }

    $user_check = array();
    $user_check['username'] = $user_row['username'];
    $user_check['proxy'] = NULL;

    $user_counts = $baseFunctions->get_counts($user_check);
    $user_following_count = $user_counts['following_count'];
    $user_page_count = ceil($user_following_count / 50);

    $user_followers = $baseFunctions->get_all_folower($user_check, $user_page_count);

    $time = time();
    for ($j = 0; $j < sizeof($user_followers); $j++) {
        if ($j == 0) {
            $time = scheduler(10, $time, 1);
        } else {
            $time = scheduler(10, $time, 0);
        }
        $user_followers[$j] . ' Time ' . date('d-m-Y H:i:s', $time) . '<br/>';
        $username = $user_followers[$j];
        $runtime = date('Y-m-d H:i:s', $time);
        $user = mysql_query("SELECT * FROM p_followers where f_username = '$username' AND f_parent_user_id = '$user_row[parent_u_id]'");
        $user_result = mysql_fetch_array($user);
        if (empty($user_result['f_username'])) {
            mysql_query("INSERT INTO p_followers (f_username,f_parent_user_id, f_run_time, f_status )VALUES ('$username','$user_row[parent_u_id]', '$runtime','0')");
        }
    }
    mysql_query("DELETE FROM `p_followers` WHERE `p_followers`.`f_status` = 4");
    mysql_query("Update adding_cron SET status='1' where id = '$user_row[id]'");
    sleep(10);
}