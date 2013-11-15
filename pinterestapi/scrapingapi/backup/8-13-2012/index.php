<?php
define('SITE_PATH', dirname(__FILE__) . '/');
error_reporting(0);

if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    setcookie("user_id", "", time() - 3600);
    header("Location: index.php");
}

if (isset($_COOKIE['user_id'])) {
    $myuniquekey = md5('hidecookie');
    $get_cookie = base64_decode(str_replace($myuniquekey, "", !empty($_COOKIE['user_id']) ? $_COOKIE['user_id'] : ''));

    header("Location: actiontime.php?user_id=" . $get_cookie);
}
include_once 'classes/basicfunctions.php';
include("includes/header.php");
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

if (isset($_GET['error']) && $_GET['error'] == 1) {
    echo '<b>Invalid Security Key </b>';
    
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $objPin = new Pinterest();
    
    if (isset($_POST['key']) && $_POST['key'] != 'h@ppyw@teR') {
        ?> 
        <script> window.location.href='index.php?error=1'; </script> 
        <?php
        
    }
    $acc['email'] = $_POST['email'];
    $acc['password'] = $_POST['password'];
    $acc['username'] = $_POST['username'];

    $acc['proxy'] = NULL;



    $con = mysql_connect('internal-db.s152367.gridserver.com', 'db152367_manager', 'dbpinterestmanager');
    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("db152367_pinterest", $con);



    $result = mysql_query("SELECT * FROM users where username='$_POST[username]'");
    $user = mysql_fetch_array($result);

    $user_id = 0;
    if (empty($user[0])) {
        mysql_query("INSERT INTO users (username, u_email, u_password)VALUES ('$_POST[username]', '$_POST[email]','$_POST[password]')");
        $user_id = mysql_insert_id();
    } else {
        $user_id = $user['user_id'];
    }



    $myuniquekey = md5('hidecookie');
    $mycookie = $myuniquekey . base64_encode($user_id) . $myuniquekey;
    $get_cookie = base64_decode(str_replace($myuniquekey, "", !empty($_COOKIE['user_id']) ? $_COOKIE['user_id'] : ''));

    if ($get_cookie == $user_id) {
        ?> 
        <script> window.location.href='actiontime.php?user_id=<?php echo $user_id ?>'; </script>
        <?php
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
            if (!isset($_GET['error'])) {
                $myuniquekey = md5('hidecookie');
                $mycookie = $myuniquekey . base64_encode($user_id) . $myuniquekey;
                setcookie('user_id', $mycookie . '', 0);
                ?> 

                <script> 
                    document.cookie="user_id=<?php echo $mycookie; ?>";
                    window.location.href='actiontime.php?user_id=<?php echo $user_id ?>'; </script>
                <?php
                exit;
            }
        }
    } else {
        if (!isset($_GET['error'])) {
            $myuniquekey = md5('hidecookie');
            $mycookie = $myuniquekey . base64_encode($user_id) . $myuniquekey;
            setcookie('user_id', $mycookie . '', 0);
            ?> 

            <script>
                document.cookie="user_id=<?php echo $mycookie; ?>";
                window.location.href='actiontime.php?user_id=<?php echo $user_id ?>'; </script>
            <?php
        }
    }
} else {

//echo phpinfo();
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<h2 class="span5"> Create Action</h2>
    <hr>
    <div id="form-actions">
        <form id="target" name="target" action="index.php" method="post" class="form-horizontal">
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

                    <label class="control-label valtype" data-valtype="label">Security Key</label>
                    <div class="controls">
                        <input class="span5" id ="key" name="key" placeholder="Security Key" type="text" data-valtype="placeholder" />
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
