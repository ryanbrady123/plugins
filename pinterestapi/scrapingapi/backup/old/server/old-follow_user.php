<?php
//ini_set('open_basedir', 'none');
define('SITE_PATH', dirname(__FILE__) . '/');
include_once 'classes/basicfunctions.php';
//ini_set('open_basedir', 'none');

define('DEBUG_MODE', $debug_mode);
define('LOGIN_TIMEOUT', $login_timeout);

//$con = mysql_connect('localhost', 'tfc', 'pl@123');
$con = mysql_connect('internal-db.s152367.gridserver.com', 'db152367_manager', 'dbpinterestmanager');

if (!$con) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db("db152367_pinterest", $con);

$objPin = new Pinterest();
$baseFunctions = new BasicFunctions();

$current_time = date('Y-m-d H:i', time());
$result = mysql_query("SELECT * FROM p_followers where DATE_FORMAT(f_run_time, '%Y-%m-%d %H:%i') < '$current_time' AND f_status='0'");

while ($user_row = mysql_fetch_array($result)) {
    $user[] = $user_row;
    mysql_query("Update p_followers SET f_status='1' where follower_id ='$user_row[follower_id]'");
}
if($user) {
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
        }

        $baseFunctions->follow_single($acc, $user_row['f_username'], $auth_token);
        mysql_query("Update p_followers SET f_status='2' where follower_id = '$user_row[follower_id]'");
        sleep(10);
    }
}
?>
