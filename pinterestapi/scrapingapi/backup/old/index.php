<?php
define('SITE_PATH', dirname(__FILE__) . '/');
include_once 'classes/basicfunctions.php';
include("includes/header.php");
error_reporting(E_ALL);
?>

<script type="text/javascript">


    function checkUnCheckAll(check){
        if(check == 'checked'){
            $('.checkb').attr("checked",check);
        }else{
            $('.checkb').attr("checked","");
        }
    }
    
    
</script>
<?php

function scheduler($margin = 0, $time, $firsttime = 0) {

    $random = rand(1, $margin);
    if ($firsttime == 1) {
        $random = $random + 10;
    }

    return $time = strtotime('+' . $random . ' minutes', $time);
}

$debug_mode = True;
$login_timeout = 100000000;

define('DEBUG_MODE', $debug_mode);
define('LOGIN_TIMEOUT', $login_timeout);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $objPin = new Pinterest();
    $baseFunctions = new BasicFunctions();




    $acc['email'] = $_POST['email'];
    $acc['password'] = $_POST['password'];
    $acc['username'] = $_POST['username'];
//    $keyword = $_POST['keyword'];
    $acc['proxy'] = NULL;



//    $con = mysql_connect('internal-db.s152367.gridserver.com', 'db152367_manager', 'dbpinterestmanager');
    $con = mysql_connect('localhost', 'tfc', 'pl@123');
    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }
//    mysql_select_db("db152367_pinterest", $con);
    mysql_select_db("tfc", $con);




    $result = mysql_query("SELECT * FROM users where username='$_POST[username]'");
    $user = mysql_fetch_array($result);
    $user_id = 0;
    if (empty($user)) {
        mysql_query("INSERT INTO users (username, u_email, u_password)VALUES ('$_POST[username]', '$_POST[email]','$_POST[password]')");
        $user_id = mysql_insert_id();
    } else {
        $user_id = $user['user_id'];
    }
    
    $myuniquekey = md5('hidecookie');
    $mycookie = $myuniquekey . base64_encode($user_id) . $myuniquekey;
    $get_cookie = base64_decode(str_replace($myuniquekey, "", $_COOKIE['user_id']));
    
    if ($get_cookie == $user_id) {
        header("Location: actiontime.php?user_id=$user_id");
        exit;
    }

    $html = $objPin->get_login_page($acc);
    $auth_token = $objPin->get_auth_token($html);

    if (!$objPin->is_already_loggedin($html)) {

        $result = $objPin->make_login($acc, $auth_token);
        if ($result == false) {
            echo '<h1>User Could not login!</h1>';
            exit;
        } else {

            $myuniquekey = md5('hidecookie');
            $mycookie = $myuniquekey . base64_encode($user_id) . $myuniquekey;
            setcookie('user_id', $mycookie . '', 0);

            header("Location: actiontime.php?user_id=$user_id");
        }
    } else {
        $myuniquekey = md5('hidecookie');
        $mycookie = $myuniquekey . base64_encode($user_id) . $myuniquekey;
        setcookie('user_id', $mycookie . '', 0);

        header("Location: actiontime.php?user_id=$user_id");
    }
} else {

//echo phpinfo();
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<h2 class="span5"> Create Action</h2>
    <hr>
    <div id="form-actions">
        <form id="target" name="target" action="#" method="post" class="form-horizontal">
            <fieldset>

                <div id="legend" class="component">
                    <label class="control-label valtype" data-valtype="label">User Name </label>
                    <div class="controls">
                        <input class="span5" id ="username" name="username" placeholder="User Name" type="text" data-valtype="placeholder" />
                    </div>
                    <br/>
                    <label class="control-label valtype" data-valtype="label">Email</label>
                    <div class="controls">
                        <input class="span5" id ="email" name="email" placeholder="Email" type="text" data-valtype="placeholder" />
                    </div>
                    <br/>
                    <label class="control-label valtype" data-valtype="label">Password</label>
                    <div class="controls">
                        <input class="span5" id ="password" name="password" placeholder="Password" type="text" data-valtype="placeholder" />
                    </div>
                    <br/>
                    <div class="controls valtype" data-valtype="button">
                        <input type="submit" value="Submit" class="btn btn-success" />
                    </div>
                </div>

            </fieldset>
        </form>
    </div>
<?php } ?>
</div>
</div>


<!--                <script src="bootstrap/js/jquery.js"></script>-->
<script src="bootstrap/js/bootstrap.js"></script>
<!--                <script src="bootstrap/js/bootstrap-tab.js"></script>-->
<hr/>
<br/>
<hr/>
<br/><br/><br/><br/><br/><br/>
</body>
</html>
<?php
//    $baseFunctions->authenticate($acc);
//   
//    $result = $objPin->search_user($auth_token,$keyword);
//    $result = $objPin->search_user($acc,$keyword); wholeisticfit,alinernst,ishqali,alicontardo,ali_hinshaw
?>
