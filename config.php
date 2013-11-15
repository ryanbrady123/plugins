<?php

$db = null;


function executeQuery($query) {
    global $db;
    if (!$db) {
        $db = new PDO('mysql:host=db.arwen.major.netbox.cz:3306;dbname=gofy.cz_job', 'gofy_n1ti6u', 'csfdcsfd');
    }
    $cmd = $db->prepare($query);
    $cmd->execute();
    return $cmd->fetchAll();
}

function insertData($table, $arr) {
    
    global $db;
    if (!$db) {
        $db = new PDO('mysql:host=db.arwen.major.netbox.cz:3306;dbname=gofy.cz_job', 'gofy_n1ti6u', 'csfdcsfd');
    }
    $query = "INSERT INTO `$table` ";
    foreach ($arr as $k => $a) {
        $names[] = '`'.$k.'`';
        $values[] = "'" . $a . "'";
    }
    $query .= '(' . implode(',', $names) . ') VALUES (' . implode(',', $values) . ')';
    $query = $query;

    executeQuery($query);
    
    return $db->lastInsertId();
}

function updateData($table, $arr, $where) {

    if ($where) {
        global $db;
        $query = "UPDATE `$table` SET ";
        $i = 1;
        foreach ($arr as $key => $a) {
            if (sizeof($arr) > $i) {
                $query .= " `$key`='" . $a . "' , ";
            } else {
                $query .= " `$key`='" . $a . "' ";
            }
            $i++;
        }
        if ($where) {
            $query.='WHERE ' . $where;
        }

        return executeQuery($query);
    } else {
        echo '<h1>Please provide the where clause </h1>';
    }
}

function TimeZoneToMin($timezone) {

    $time = explode(':', $timezone);
    if (!empty($timezone) && sizeof($time) > 1) {
        if ($time[0] > 0)
            return ($time[0] * 60) + $time[1];
        else
            return ($time[0] * 60) - $time[1];
    }else {
        return 0;
    }
}

function createDateRangeArray($strDateFrom, $strDateTo) {
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.
    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange = array();

    $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
    $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
        while ($iDateFrom < $iDateTo) {
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }
    return $aryRange;
}

function mySQLSafe($value, $quote = "'") {

// strip quotes if already in
    $value = htmlspecialchars($value, ENT_NOQUOTES);
    $value = str_replace(array("\'", "'"), "&#39;", $value);

// Stripslashes
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
// Quote value
    if (version_compare(phpversion(), "4.3.0") == "-1") {
        $value = mysql_escape_string($value);
    } else {
        $value = mysql_real_escape_string($value);
    }
    $value = $quote . $value . $quote;

    return $value;
}
