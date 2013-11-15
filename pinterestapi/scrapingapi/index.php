<?php
echo '<pre>';
define('SITE_PATH', dirname(__FILE__) . '/');
error_reporting(0);

include_once 'classes/basicfunctions.php';
include("includes/header.php");


$objPin = new Pinterest();
$BasicFunctions = new BasicFunctions();

$con = mysql_connect('localhost', 'instagram', 'pl@123');
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("instagram", $con);

$result = mysql_query("SELECT * FROM `users` ORDER BY `count_used`");
$user = mysql_fetch_array($result);

$acc['email'] = $user['u_email'];
$acc['password'] = $user['u_password'];
$acc['username'] = $user['username'];
$acc['proxy'] = NULL;

$html = $objPin->get_login_page($acc);
echo $auth_token = $objPin->get_auth_token($html);
var_dump($objPin->is_already_loggedin($html));

if (!$objPin->is_already_loggedin($html)) {
    $result = $objPin->make_login($acc, $auth_token);
}

$response_search = $objPin->search($acc, "etsy");
$pins = $BasicFunctions->parse_users_pin($acc, $response_search);
print_r($pins);
foreach ($pins as $pinn) {
    $Check = mysql_query("SELECT * FROM `post_comment` Where `pin_id` = '$pinn[id]'");
    $Checked = mysql_fetch_array($Check);

    if (!$Checked) {

        $records = mysql_query("SELECT * FROM `comments` ORDER BY `c_count_used`");
        $record = mysql_fetch_array($records);

        $pin['pin_id'] = $pinn['id'];
        $pin['template'] = $record['comment'];

        $records = mysql_query("INSERT INTO `post_comment`  (`t_parent_user_id` ,`username` ,`pin_id` ,`status` ,`have_follow` ,`t_type`)VALUES ('$user_id','$acc[username]','$pinn[id]','0','0','0')");
        $res = $BasicFunctions->post_comment($acc, $pin, $auth_token);
        $pos = strpos($res, 'Failed');
        $pos1 = strpos($res, 'Error');

// Note our use of ===.  Simply == would not work as expected
// because the position of 'a' was the 0th (first) character.
        if ($pos === false || $pos1 === false) {
            $count = (int) $record['c_count_used'] + 1;
            $update_comment = mysql_query("UPDATE `comments` SET `c_count_used`='$count' WHERE `comment_id` = '$record[comment_id]'");
        }
        break;
    }
}
$countuser = (int) $user['count_used'] + 1;
$update_comment = mysql_query("UPDATE `users` SET `count_used`='$countuser' WHERE `user_id` = '$user[user_id]'");
exit;
