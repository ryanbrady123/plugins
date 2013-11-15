<?php
$unfollowers = $_POST;
function scheduler($margin = 0, $time, $firsttime = 0) {

    $random = rand(1, $margin);
    if ($firsttime == 1) {
        $random = $random + 10;
    }

    return $time = strtotime('+' . $random . ' minutes', $time);
}

$con = mysql_connect('internal-db.s152367.gridserver.com', 'db152367_manager', 'dbpinterestmanager');
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("db152367_pinterest", $con);


//  $con = mysql_connect('localhost', 'tfc', 'pl@123');
//    if (!$con) {
//        die('Could not connect: ' . mysql_error());
//    }
//    mysql_select_db("tfc", $con);
    
$i = 0;
$j = 0;
$time = time();
foreach ($unfollowers as $unfollow) {
    if ($i != 0) {
        if ($j == 0) {
            $j = 1;
            $time = scheduler(3, $time, 1);
        } else {
            $time = scheduler(3, $time, 0);
        }
        $runtime = date('Y-m-d H:i', $time);
//        echo "INSERT INTO unfollow (uf_parent_user_id, uf_username,uf_run_time,uf_status)VALUES ('$unfollowers[username]', '$unfollow','$runtime','0')";
        mysql_query("INSERT INTO unfollow (uf_parent_user_id, uf_username,uf_run_time,uf_status)VALUES ('$unfollowers[username]', '$unfollow','$runtime','0')");
        
    } else {
        $i = 1;
    }
}


header("Location: actiontime.php?user_id=$_POST[username]");

