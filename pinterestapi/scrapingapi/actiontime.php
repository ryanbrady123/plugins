<?php
if (!isset($_COOKIE['user_id'])) {
    header("Location: index.php");
}
define('SITE_PATH', dirname(__FILE__) . '/');

include("includes/header.php");
include_once 'classes/basicfunctions.php';
//error_reporting(E_ALL);
?>
<script type="text/javascript">

    function checkUnCheckAll(check){
        if(check == 'checked'){
            $('.checkb').attr("checked",check);
        }else{
            $('.checkb').attr("checked","");
        }
    }
    
    function checkUnCheckAll1(check){
        if(check == 'checked'){
            $('.checkb1').attr("checked",check);
        }else{
            $('.checkb1').attr("checked","");
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
?>

<a style="float: right;margin: 36px;font-size: 18px;" href="actiontime.php?user_id=<?php echo $_GET['user_id'] ?>">Back </a>
<?php
$user_id = $_GET['user_id'];
$con = mysql_connect('internal-db.s152367.gridserver.com', 'db152367_manager', 'dbpinterestmanager');
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("db152367_pinterest", $con);
$result_followers = mysql_query("SELECT COUNT(follower_id) as followers FROM p_followers where f_parent_user_id ='$user_id'");

$stats_followers = mysql_fetch_array($result_followers);

$result_unfollowers = mysql_query("SELECT  COUNT(unfollower_id) as unfollowers FROM unfollow where uf_parent_user_id ='$user_id'");
$stats_unfollowers = mysql_fetch_array($result_unfollowers);
?>
<br/>
<div style="float:right;"><b><span>Follow Queue : </span><?php echo $stats_followers[followers]; ?> | <span>Un Follow Queue : </span><?php echo $stats_unfollowers[unfollowers] ?></b> | <b><a href="index.php?logout=1">Logout  </a></b></div>
<br/>


<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $objPin = new Pinterest();
    $baseFunctions = new BasicFunctions();

//    $con = mysql_connect('localhost', 'tfc', 'pl@123');
//    if (!$con) {
//        die('Could not connect: ' . mysql_error());
//    }
//    mysql_select_db("tfc", $con);



    $result = mysql_query("SELECT * FROM users where user_id='$_POST[username]'");
    $user = mysql_fetch_array($result);

    $acc['email'] = $user['u_email'];
    $acc['password'] = $user['u_password'];
    $acc['username'] = $user['username'];
    $keyword = $_POST['keyword'];

    $acc['proxy'] = NULL;


    $keywords = array();
    $keywords = explode(',', $keyword);
    $admin_counts = $baseFunctions->get_counts($acc);
    $admin_following_count = $admin_counts[following_count];
    $admin_count = ceil($admin_following_count / 50);
    $followings = $baseFunctions->get_all_folowing($acc, $admin_count);

    if ($_POST['actionType'] == 'follow') {
        $user_followers = array();
        $stats = array();
        for ($i = 0; $i < sizeof($keywords); $i++) {

            $user_check = array();
            $usercounts = 0;

            $user_check['username'] = $keywords[$i];
            $user_check['proxy'] = NULL;

            $usercounts = $baseFunctions->get_counts($user_check);
            $admin_following_count = $usercounts[following_count];

            if ($admin_following_count > 0) {
                mysql_query("INSERT INTO adding_cron (parent_u_id,username, users_count, status,type)VALUES ('$user_id','$user_check[username]', '$admin_following_count','0','follow')");
            }
            $stats[$i]['user'] = $user_check[username];
            $stats[$i]['count'] = $admin_following_count;

            $user_followers[] = $baseFunctions->get_all_folower($user_check, $page_count);
        }

        $numberoffollwers = 0;
        for ($i = 0; $i < count($stats); $i++) {
            $numberoffollwers += $stats[$i]['count'];
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>User ' . $stats[$i]['user'] . ' Has ' . $stats[$i]['count'] . ' Follwers</b>' . '<br/><br/>';
        }
        ?>
        We Fatched <?php echo $numberoffollwers ?> Records from the Pinterest related to user named <?php echo $keyword; ?>
        </div>
        <?php
    } else {

        $result = array();
        $admin_counts = $baseFunctions->get_counts($acc);
        $admin_following_count = $admin_counts[following_count];
        $admin_followers_count = $admin_counts[followers_count];


        $admin_folowing_count = ceil($admin_following_count / 50);
        $admin_followers_count = ceil($admin_followers_count / 50);

        $followers = $baseFunctions->get_all_folowing($acc, $admin_folowing_count);

        $followings = $baseFunctions->get_all_folower($acc, $admin_followers_count);

//        finding common in the loged in user followers and following account
        $logedin_follower_following = array_intersect($followers, $followings);
//        finding user which followed us but we have not follwed them yet
        $logedin_follower_notfollowing = array_diff($followings, $followers);

        if ($logedin_follower_following) {
            if (!empty($keywords[0])) {
                for ($i = 0; $i < sizeof($keywords); $i++) {
                    $page_count = 1;
                    $username['username'] = $keywords[$i];
                    $username['proxy'] = NULL;
                    $user_counts = $baseFunctions->get_counts($acc);
                    $user_following_count = $user_counts[following_count];
                    $page_count = ceil($user_following_count / 50);
                    $keywordfollowers = $baseFunctions->get_all_folower($username, $page_count);
//        finding user from key word which followed us and we followed them  
                    $logedin_followed_they_followed = array_intersect($logedin_follower_following, $keywordfollowers);
//        finding user from key word whom we followed but the have not follwed us yet
                    $logedin_folowed_they_dont = array_intersect($logedin_follower_notfollowing, $keywordfollowers);


                    $result[$i]['username'] = $keywords[$i];
                    $result[$i]['match'] = $logedin_followed_they_followed;
                    $result[$i]['dont_match'] = $logedin_folowed_they_dont;
                }
            } else {
                $result[0]['username'] = $acc['username'];
                $result[0]['match'] = $logedin_follower_following;
                $result[0]['dont_match'] = $logedin_follower_notfollowing;
            }
        } else {
//            echo 'No Records Found';
        }
        ?>
        <br/>
        <br/>
        <br/>
        <br/>
       
        
        
        <style>
            .pointer{cursor: pointer;}
        </style>
        <br/>
        <br/>
        <div class="span5">
            <h3>I Followed them and they also followed back</h3>
            <form  action="save_unfollow.php" method="post" class="form-horizontal" name="match">
		<?php
                if ($result) {
                    ?>
                    <a  class="pointer" onclick="checkUnCheckAll('checked')">Check ALL </a> | <a  class="pointer" onclick="checkUnCheckAll('')"> Un Check All</a>
                    <div class="controls valtype" data-valtype="button">
                        <input type="submit" value="Submit" class="btn btn-success" />
                    </div>
                <?php } ?>
		<br />
                <input type="hidden" name="username" id="username" value="<?php echo $user_id; ?>" />
                <div style="min-height: 611px !important;" class="pre-scrollable">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Check To un-follow</th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($result as $matched) {
                            if ($matched['match']) {
                                ?>
                                <tr>
                                    <th colspan="2">Username <?php echo $matched['username'] ?> Followers and Followings Match.</th>
                                </tr>
                                <tbody>
                                    <?php
                                    $i = 0;

                                    foreach ($matched['match'] as $match) {
                                        $i++;
                                        ?>
                                        <tr>
                                            <td><?php echo $match ?></td>
                                            <td><input type="checkbox" class="checkb" value="<?php echo $match ?>" name="user_unfollow_<?php echo $i; ?>" /></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
//                                echo 'No Record Found for ' . $match['username'] . ' user';
                                }
                                ?>


                            <?php }
                            ?>
                        </tbody>
                    </table>
                </div>
                <br/>
                <?php
                if ($result) {
                    ?>
                    <a  class="pointer" onclick="checkUnCheckAll('checked')">Check ALL </a> | <a  class="pointer" onclick="checkUnCheckAll('')"> Un Check All</a>
                    <div class="controls valtype" data-valtype="button">
                        <input type="submit" value="Submit" class="btn btn-success" />
                    </div>
                <?php } ?>
            </form>
        </div>
        <div class="span5">
            <h3>I Followed them but they haven't followed back</h3>
            <form  action="save_unfollow.php" method="post" class="form-horizontal" name="dont_match">
		<?php
                if ($result) {
                    ?>
                    <a class="pointer" onclick="checkUnCheckAll1('checked')">Check ALL </a> | <a class="pointer" onclick="checkUnCheckAll1('')"> Un Check All</a>
                    <div class="controls valtype" data-valtype="button">
                        <input type="submit" value="Submit" class="btn btn-success" />
                    </div>
                <?php } ?>
		<br />
                <input type="hidden" name="username" id="username" value="<?php echo $user_id; ?>" />
                <div style="min-height: 611px !important;" class="pre-scrollable">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Check To un-follow</th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($result as $matched) {
                            if ($matched['dont_match']) {
                                ?>
                                <tr>
                                    <?php echo $i; ?>
                                </tr>
                                <tr>
                                    <th colspan="2">Username <?php echo $matched['username'] ?> Followers and Followings That Don't Match.</th>
                                </tr>
                                <tbody>
                                    <?php
                                    $i = 0;

                                    foreach ($matched['dont_match'] as $dont_match) {
                                        $i++;
                                        ?>
                                        <tr>

                                            <td><?php echo $dont_match ?></td>
                                            <td><input type="checkbox" class="checkb1" value="<?php echo $dont_match ?>" name="user_unfollow_<?php echo $i; ?>" /></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
//                            echo 'No Record Found for ' . $matched['username'] . ' user';
                                }
                                ?>


                            <?php }
                            ?>
                        </tbody>
                    </table>
                </div>
                <br/>


                <?php
                if ($result) {
                    ?>
                    <a class="pointer" onclick="checkUnCheckAll1('checked')">Check ALL </a> | <a class="pointer" onclick="checkUnCheckAll1('')"> Un Check All</a>
                    <div class="controls valtype" data-valtype="button">
                        <input type="submit" value="Submit" class="btn btn-success" />
                    </div>
                <?php } ?>
            </form>
        </div>
        <?php
    }
} else {
    ?>
    <div class="form-actions">

        <form id="target" name="target" action="" method="post" class="form-horizontal">
            <input type="hidden" name="username" id="username" value="<?php echo $user_id; ?>" />
            <br/>
            <label class="control-label valtype" data-valtype="label">Account Names</label>
            <div class="controls">
                <input class="span5" id ="keyword" name="keyword" placeholder="Account Names (comma separated)" type="text" data-valtype="placeholder" />
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

        </form>
    </div>
<?php } ?>