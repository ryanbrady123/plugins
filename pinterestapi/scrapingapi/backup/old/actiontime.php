<?php
define('SITE_PATH', dirname(__FILE__) . '/');
include("includes/header.php");
include_once 'classes/basicfunctions.php';
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
?>


<?php
$user_id = $_GET['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $objPin = new Pinterest();
    $baseFunctions = new BasicFunctions();

    $con = mysql_connect('localhost', 'tfc', 'pl@123');
    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("tfc", $con);


    $result = mysql_query("SELECT * FROM users where user_id='$_POST[username]'");
    $user = mysql_fetch_array($result);

    $acc['email'] = $user['u_email'];
    $acc['password'] = $user['u_password'];
    $acc['username'] = $user['username'];
    $keyword = $_POST['keyword'];

    $acc['proxy'] = NULL;


    $keywords = array();
    $keywords = explode(',', $keyword);
    $page_count = 1;

    if ($_POST['actionType'] == 'follow') {
        $user_followers = array();
        for ($i = 0; $i < sizeof($keywords); $i++) {
            $user_check['username'] = $keywords[$i];
            $user_check['proxy'] = NULL;

            $user_followers[] = $baseFunctions->get_all_folower($user_check, $page_count);
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
        <script type="text/javascript">
            $document.ready(function(){
                alert('asdas');
            });
        </script>
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
        $followers = $baseFunctions->get_all_folowing($acc, $page_count);
        $followings = $baseFunctions->get_all_folower($acc, $page_count);

        $match = array_intersect($followers, $followings);

        if ($match) {
            if (!empty($keywords[0])) {
                for ($i = 0; $i < sizeof($keywords); $i++) {
                    $page_count = 1;
                    $username['username'] = $keywords[$i];
                    $username['proxy'] = NULL;
                    $Userfollowers = $baseFunctions->get_all_folower($username, $page_count);

                    $matched_from_keyword = array_intersect($Userfollowers, $match);


                    $result[$i]['username'] = $keywords[$i];
                    $result[$i]['match'] = $matched_from_keyword;
                }
            } else {
                $result[0]['username'] = $acc['username'];
                $result[0]['match'] = $match;
            }
        } else {
            echo 'No Records Found';
        }
        ?>
        <form  action="save_unfollow.php" method="post" class="form-horizontal">
            <input type="hidden" name="username" id="username" value="<?php echo $user_id; ?>" />
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
                            echo 'No Record Found for ' . $matched['username'] . ' user';
                        }
                        ?>

                    </tbody>
                </table>
                <br/>
                <?php
            }
            if ($result) {
                ?>
                <a onclick="checkUnCheckAll('checked')">Check ALL </a> | <a onclick="checkUnCheckAll('')"> Un Check All</a>
                <div class="controls valtype" data-valtype="button">
                    <input type="submit" value="Submit" class="btn btn-success" />
                </div>
            <?php } ?>
        </form>
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