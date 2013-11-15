<?php

//ini_set('open_basedir', 'none');
define('SITE_PATH', dirname(__FILE__) . '/');
include_once 'classes/basicfunctions.php';
//ini_set('open_basedir', 'none');

define('DEBUG_MODE', $debug_mode);
define('LOGIN_TIMEOUT', $login_timeout);
//echo '<pre>';

$db = null;

function &getDBConnection() {
    return new PDO('mysql:host=localhost;dbname=instagra_db', 'instagra_user', 'dbinstauser');
}

function executeQuery($query) {
    global $db;
    if (!$db) {
        $db = getDBConnection();
    }
    $cmd = $db->prepare($query);
    $cmd->execute();
    return $cmd->fetchAll();
}

function insertData($table, $arr) {
    global $db;
    if (!$db) {
        $db = getDBConnection();
    }
    $query = "INSERT INTO `$table` ";
    foreach ($arr as $k => $a) {
        $names[] = $k;
        $values[] = "'" . $a . "'";
    }
    $query .= '(' . implode(',', $names) . ') VALUES (' . implode(',', $values) . ')';
    executeQuery($query);
    return $db->lastInsertId();
}

function updateData($table, $arr, $where = null) {
    global $db;
    $query = "UPDATE `$table` SET ";
    $i = 1;
    foreach ($arr as $key => $a) {
        if (sizeof($arr) > $i) {
            $query .= " `$key`='$a' , ";
        } else {
            $query .= " `$key`='$a' ";
        }
        $i++;
    }
    if ($where) {
        $query.='WHERE ' . $where;
    }
    return executeQuery($query);
}

$objPin = new Pinterest();
$baseFunctions = new BasicFunctions();

$current_time = date('Y-m-d H:i', time());
$sql = "SELECT u.* FROM `users_pin` u WHERE `u`.`status` ='0' ORDER BY `user_count` LIMIT 1";
$users = executeQuery($sql);
foreach ($users as $user) {
    $acc['email'] = $user['u_email'];
    $acc['password'] = $user['u_password'];
    $acc['username'] = $user['username'];
    $acc['proxy'] = NULL;

    $html = $objPin->get_login_page($acc);
    $auth_token = $objPin->get_auth_token($html);
    $objPin->is_already_loggedin($html);
    if (!$objPin->is_already_loggedin($html)) {
        $result = $objPin->make_login($acc, $auth_token);
        
        if ($result == false) {
            echo '<h1>Login Faild!</h1>';
            exit;
        }
    }

    $objPin->search($acc, "etsy");
}
exit;