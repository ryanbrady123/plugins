<?php
define('SITE_PATH', dirname(__FILE__) . '/');
include_once 'classes/basicfunctions.php';
include("includes/header.php");
?>



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
    $keyword = $_POST['keyword'];
    $acc['proxy'] = NULL;




    $con = mysql_connect('internal-db.s152367.gridserver.com', 'db152367_manager', 'dbpinterestmanager');
    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("db152367_pinterest", $con);




    $result = mysql_query("SELECT * FROM users where username='$_POST[username]'");
    $user = mysql_fetch_array($result);
    $user_id = 0;
    if (empty($user)) {
        mysql_query("INSERT INTO users (username, u_email, u_password)VALUES ('$_POST[username]', '$_POST[email]','$_POST[password]')");
        $user_id = mysql_insert_id();
    } else {
        $user_id = $user['user_id'];
    }



    $html = $objPin->get_login_page($acc);
    $auth_token = $objPin->get_auth_token($html);

    if (!$objPin->is_already_loggedin($html)) {
        $result = $objPin->make_login($acc, $auth_token);
    }




    $keywords = explode(',', $keyword);

    if ($_POST['actionType'] == 'follow') {


        $user_followers = array();
        for ($i = 0; $i < sizeof($keywords); $i++) {
            $user['username'] = $keywords[$i];
            $user['proxy'] = NULL;
            $page_count = 1;
            $user_followers[] = $baseFunctions->get_all_folower($user, $page_count);
        }


        $time = time();
        $stats = array();


        for ($i = 0; $i < sizeof($user_followers); $i++) {
            $stats[$i]['user'] = $keywords[$i];
            $stats[$i]['count'] = count($user_followers[$i]);
            for ($j = 0; $j < sizeof($user_followers[$i]); $j++) {
                if ($i == 0 && $j == 0) {
                    $time = scheduler(3, $time, 1);
                } else {
                    $time = scheduler(3, $time, 0);
                }
                $user_followers[$i][$j] . ' Time ' . date('d-m-Y H:i:s', $time) . '<br/>';
                $username = $user_followers[$i][$j];
                $runtime = date('Y-m-d H:i:s', $time);
                mysql_query("INSERT INTO p_followers (f_username,f_parent_user_id, f_run_time, f_status )VALUES ('$username','$user_id', '$runtime','0')");
            }
        }



        $numberoffollwers = 0;
        for ($i = 0; $i < count($stats); $i++) {
            $numberoffollwers += $stats[$i]['count'];
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>User ' . $stats[$i]['user'] . ' Has ' . $stats[$i]['count'] . ' Follwers</b>' . '<br/><br/>';
        }
        ?>
        <div class="span8">
            <div class="control-group">
                <label for="multiSelect" class="control-label">Users</label>
                <div class="controls">
                    <select name="multiSelect" id="multiSelect" multiple="multiple">
                        <?php for ($i = 0; $i < sizeof($user_followers); $i++) { ?>
                            <?php for ($j = 0; $j < sizeof($user_followers[$i]); $j++) { ?>
                                <option><?php echo $user_followers[$i][$j]; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            We Fatched <?php echo $numberoffollwers ?> Records from the Pinterest related to user named <?php echo $keyword; ?>
        </div>
        <?php
    } else {
        $result = array();
        $followers = $baseFunctions->get_all_folowing($acc['username'], $page_count);
        $followings = $baseFunctions->get_all_folower($acc['username'], $page_count);
        $match = array_intersect($followers, $followings);
        if($match) {
            if ($keywords) {
                for ($i = 0; $i < sizeof($keywords); $i++) {
                    $page_count = 1;

                    $Userfollowers = $baseFunctions->get_all_folower($keywords[$i], $page_count);
                    $matched_from_keyword = array_intersect($followers, $match);

                    $result[$i]['username'] = $keywords[$i];
                    $result[$i]['match'] = $matched_from_keyword;
                }
            } else {
                $result = $match;
            }
        }else{
            
            echo 'No Records Found';
            
        }
    }
    ?>
    <form  action="" method="post" class="form-horizontal">
        <?php
        print_r($result);
        foreach ($result as $matched) {
            ?>
            <br/><br/><br/><br/>
            <h1>UserName <?php echo $matched['username'] ?> Followers and Following Match.</h1>

            <table class="table table-striped table-bordered table-condensed">
                <thead>

                    <tr>
                        <th>Username</th>
                        <th>Check To un-follow</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $i = 0;
                    foreach ($matched['match'] as $match) {
                        $i++;
                        ?>
                        <tr>

                            <td><?php echo $match ?></td>
                            <td><input type="checkbox" value="<?php echo $match ?>" name="user_unfollow_<?php echo $i; ?>" /></td>
                        </tr>
                    <?php }
                    ?>

                </tbody>
            </table>
            <br/>


        <?php }
        ?>
        <div class="controls valtype" data-valtype="button">
            <input type="submit" value="Submit" class="btn btn-success" />
        </div>
    </form>
    <?php
} else {
//echo phpinfo();
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<h2 class="span5"> Create Action</h2>
    <hr>
    <div id="build">
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
                    <label class="control-label valtype" data-valtype="label">Account Names</label>
                    <div class="controls">
                        <input class="span5" id ="password" name="keyword" placeholder="Account Names (comma separated)" type="text" data-valtype="placeholder" />
                    </div>
                    <br/>
                    <label class="control-label valtype" data-valtype="label">Select Option</label>
                    <div class="controls">
                        <select id="actionType" name="actionType" data-valtype="option" class="input-xlarge valtype">
                            <option value="follow">Follow</option>
                            <option value="list">List</option>
                        </select>
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
