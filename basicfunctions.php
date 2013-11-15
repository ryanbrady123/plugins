<?php

//require_once(SITE_PATH . "classes/common.class.php");
//require_once(SITE_PATH . "classes/pinterest.class.php");
//require_once(SITE_PATH . "classes/simple_html_dom.php");

class BasicFunctions {

    public $common, $bitly, $scheduleTweets, $tweets, $accounts, $twitter, $options, $lists, $db2, $simple_pie, $simple_html_dom;
    private $user_details;
    private $authnticity_token;
    private $proxy, $max_failure;
    protected $highDelay;
    protected $lowDelay;
    function __construct() {
        
//        $common = new Common();
        $this->proxy = '';
    }

    function __destruct() {
//		  	$this->db2->close();
    }

    function update_code_field_error($acc, $field_name, $current_code, $error_code) {
        $accounts = new Accounts();
        $sql = '';
        if ($acc[$field_name] >= ($current_code + MAX_FAILURE)) {
            $sql = $accounts->update_field_status($acc['username'], $field_name, $error_code);
        } else {
            $sql = $accounts->update_field_status($acc['username'], $field_name, $current_code + 1);
        }
        $this->db2->execute($sql);
    }

    function check_user_not_exists($username, $follow_name) {

        $this->common = new Common();
        $table_info = $this->common->get_follow_table_name($username);

        $sql = "SELECT id,follow_name,status,date FROM follow_table_" . $table_info['table_name'] . " WHERE username = '" . $username . "' AND follow_name = '" . $follow_name . "' LIMIT 1";
        $this->db2->query($sql);
        $user_exists_check = $this->db2->fetch_all_assoc();
        if (count($user_exists_check) == 0) {
            echo "\n User Not in DB = " . $follow_name . "    Username  = " . $username . "\n";
            return true;
        }
        echo "\n User Already in DB = " . $follow_name . "    Username  = " . $username . "\n";
        return false;
    }

    function parse_users_pin($acc, $html_result) {

        $html = new simple_html_dom();
        $html = null;
        $html = str_get_html($html_result);
        $result = array();
        $this->common = new Common();
        echo '<pre>';
        $i = 0 ;
        foreach ($html->find('div[id=ColumnContainer] div[class=pin]') as $g) {
              
            $pinId = $g->attr['data-id'];            
            $friend_name = trim($g->find('div[class=convo attribution clearfix] a', 0)->href);
            $friend_name = trim($g->find('div[class=convo attribution clearfix] a', 0)->href);
            $friend_image = trim($g->find('div[class=convo attribution clearfix] img', 0)->src);
            $friend_name = str_replace("/", "", $friend_name);

            if (!empty($friend_name)) {
                $result[$i]['name'] = $friend_name;
                $result[$i]['id'] = $pinId;
            }
            $i ++;
        }
        $html->clear();
        return $result;
    }

    function follow_single($acc, $follow_user, $auth_token) {
        global $error_email;
        $objPin = new Pinterest();
        $this->common = new Common();
        $url = "http://pinterest.com/search/people/?q=" . $this->common->make_url($acc['param']);

        $follow_result = json_decode($objPin->follow_user($acc, $follow_user, $auth_token, $url), true);

        if ($follow_result['status'] == "success") {

            return true;
        } else {

            if ($follow_result['captcha']) {
                $mail_body = ' Support Message! ';

                $mail_body.=' Captcha Needed for account ' . $acc['username'] . '\n \n ';
            }

            $mail_body.= "\n Follow Failed! ";
            $mail_body.= "\n User = " . $acc['username'];
            $mail_body.= "\n User to follow  = " . $follow_user;
            $mail_body.= "\n Message  = " . $follow_result['message'];
            $mail_body.= "\n We will attempt to follow this user again after one hour. Thanks ";
            echo $mail_body;
            if (!$error_email[$acc['email']]) {
                mail($acc['email'], "Users Following Error Recored", $mail_body);
                mail('muhammad.furqan@purelogics.net', "Users Following Error Recored", $mail_body);
                $error_email[$acc['email']] = $acc['email'];
            }
            return false;
        }
        //$error_email[$acc['email']] = $acc['email'];
        return false;
    }

    function un_follow_single($acc, $follow_user, $auth_token) {
        $objPin = new Pinterest();
        $this->common = new Common();

        $follow_result = json_decode($objPin->un_follow_user($acc, $follow_user, $auth_token), true);
//			print_r($follow_result);
        if ($follow_result['status'] == "success") {
//				echo "\n Un-Follow Success! ";
//				echo "\n User = ".$acc['username'];
//				echo "\n User to follow  = ".$follow_user;
            return true;
        } else {
            echo "\n Un-Follow Failed! ";
            echo "\n User = " . $acc['username'];
            echo "\n User to un-follow  = " . $follow_user;
            echo "\n Message  = " . $follow_result['message'];
            return false;
        }
        return false;
    }

    function post_comment($acc, $pin, $auth_token) {
        $objPin = new Pinterest();
        $loggedScreenName = "";
        //$pin_data = $objPin->get_repin_data($acc,$pin, $auth_token);
        $response = $objPin->do_comment($acc, $pin, $auth_token);
       
        if ($response['status'] == "success") {
            echo "\n Comment Posted !\n";
            echo "\n User =  " . $acc['username'] . "\n";
            echo "\n Pin =  " . $pin['pin_id'] . "\n";
            echo "\n Comment =  " . $pin['template'] . "\n";
            echo "\n URL =  http://pinterest.com/pin/" . $pin['pin_id'] . "\n";
            //echo "\n URL =  http://pinterest.com/overmixercrisp".$response['url']."\n";
            return $response;
        } else {
            echo "\n Comment Failed! \n";
            echo "\n User =  " . $acc['username'] . "\n";
            echo "\n Pin =  " . $pin['pin_id'] . "\n";
            echo "\n Error =  " . $response['message'] . "\n";
            return false;
        }
        return false;
    }

    function search_users($acc, $follow, $keyword) {
        $objPin = new Pinterest();
        $loggedScreenName = "";
        $max_users = $follow['count'];
        $html_array = $objPin->search_user($acc, $keyword);
        $reached_max = false;
        $all_users = array();
        foreach ($html_array as $html) {
            $users = $this->parse_users($acc, $html);
            if (count($all_users) < $max_users) {
                foreach ($users as $u) {
                    $all_users[] = $u;
                    if (count($all_users) >= $max_users) {
                        $reached_max = true;
                        break;
                    }
                }
            }
            if ($reached_max) {
                break;
            }
        }
        unset($html_array);
        return $all_users;
    }

    function parse_users($acc, $html) {
        $pattern = '/\<a class="ImgLink" href="(.*?)"\>\<img.*?src="(.*?)" \/\>/si';
        //<a class="ImgLink" href="/xeec83/"><img alt="Xee Chue" src="http://media-cdn.pinterest.com/avatars/xeec83_1330017852_o.jpg" /></a>
        preg_match_all($pattern, $html, $match);
        $result = array();
        if (isset($match[1]) && count($match[1]) > 0) {
            $this->common = new Common();
            foreach ($match[1] as $key => $m) {
                $friend_name = strtolower(str_replace("/", "", $m));
                $is_not_default = $this->common->checkDefaultImage($match[2][$key], $acc['username'], $friend_name);
                $db_user_not_exist = $this->check_user_not_exists($acc['username'], $friend_name);
                if ($is_not_default && $db_user_not_exist) {
                    $result[] = $friend_name;
                }
            }
        }
        return $result;
    }

    function get_all_folowing($acc, $page_count) {
        $objPin = new Pinterest();
        $html_array = $objPin->get_all_followings($acc, $page_count);
        $all_users = array();
        foreach ($html_array as $html) {
            $users = $this->parse_follow_page($html);
            foreach ($users as $u) {
                $all_users[] = $u;
            }
        }
        unset($html_array);

        return $all_users;
    }

    function get_all_folower($acc, $page_count) {
        $objPin = new Pinterest();
        $html_array = $objPin->get_all_followers($acc, $page_count);
        $all_users = array();
        foreach ($html_array as $html) {
            $users = $this->parse_follow_page($html);
            foreach ($users as $u) {
                $all_users[] = $u;
            }
        }
        unset($html_array);
        return $all_users;
    }

    function get_counts($acc) {
        $objPin = new Pinterest();
        $html_result = $objPin->get_public_profile_page($acc);
        $result = array();
        if (!empty($html_result)) {
            $html = new simple_html_dom();
            $html = null;
            $html = str_get_html($html_result);

            $result['followers_count'] = intval(trim(str_replace("followers", "", strip_tags($html->find('div[id=ContextBar] ul[class=follow] li a', 0)->innertext))));
            $result['following_count'] = intval(trim(str_replace("following", "", strip_tags($html->find('div[id=ContextBar] ul[class=follow] li a', 1)->innertext))));
            $result['total_pages'] = ceil($result['followers_count'] / 50);
            $html->clear();
        }

        return $result;
    }

    function parse_follow_page($page_html) {
        $followers_list = array();

        $html = new simple_html_dom();
        $html = null;
        $html = str_get_html($page_html);

        foreach ($html->find('div[id=PeopleList] div[class=person]') as $g) {
            $user_name = trim($g->find('div[class=PersonInfo] a', 0)->href);
            $followers_list[] = str_replace("/", "", $user_name);
        }
        $html->clear();
        unset($page_html);
        return $followers_list;
    }

    function get_curl_results_source($url, $cookie_name, $reffer, $header = array(), $currentProxy, $agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12") {
        echo $url;
        //$agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12';
        //$agent = $this->getAgent();		
        $cookie_file_path = SITE_PATH . "cookies/" . $cookie_name . '.txt';
        if (!file_exists($cookie_name)) {
            $fp = fopen($cookie_file_path, "wb");
            fclose($fp);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!is_null($header) || count($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if (is_null($header)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, REQUEST_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, REQUEST_TIMEOUT);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

        $proxy = $currentProxy;
        if (!is_null($proxy) && $proxy != '') {
            //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($ch, CURLOPT_REFERER, $reffer);
        /* if (!empty($cookie_file_path)) {
          curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
          curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
          } */


        $result = curl_exec($ch);
        if ($result == false) {
            echo curl_error($ch);
            //$curlResult = $this->common->checkCurlResult(curl_error($ch),$proxy);
        }

        $info_array = curl_getinfo($ch);
        curl_close($ch);

        return $result;
    }
 function get_curl_results($url, $postData = null, $create_cookie = false, $username, $proxy = null, $header = null, $agent = '', $reffer = 'https://pinterest.com/') {
        $cookie_file_path = SITE_PATH . "cookies/" . strtolower($username) . '.txt';
        //if ($create_cookie) {
        if ($create_cookie && !file_exists($cookie_file_path)) {
            $fp = fopen($cookie_file_path, "wb");
            fclose($fp);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!is_null($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if (is_null($header) && !empty($agent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, REQUEST_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, REQUEST_TIMEOUT);


        if (!is_null($proxy) && $proxy != '') {
            //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        if (!is_null($postData)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        /* if(!empty($httpUsername) && !empty($httpPassword)){
          echo $httpUsername . ':' . $httpPassword;
          curl_setopt($ch, CURLOPT_USERPWD, $httpUsername . ':' . $httpPassword);
          curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
          } */
        if (is_null($header) && !empty($reffer)) {
            curl_setopt($ch, CURLOPT_REFERER, $reffer);
        }
        if (!empty($cookie_file_path)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
        }

        //curl_setopt($ch, CURLOPT_PROXYUSERPWD, "harristes109:6657wBSm");
        // Add Delay
        $delay = 0;
        $delayArray = array($this->lowDelay, $this->highDelay);
        $delay = $delayArray[array_rand($delayArray, 1)];
        sleep($delay);

        $result = curl_exec($ch);
        if ($result == false) {
            $curlResult = $this->checkCurlResult(curl_error($ch), $proxy, $url);
        }

        $info_array = curl_getinfo($ch);

        $this->last_url = $info_array['url'];

        curl_close($ch);

        return $result;
    }

    function get_curl_results_login($url, $postData = null, $create_cookie = false, $username, $proxy = null, $header = null, $agent = '', $reffer = 'https://pinterest.com/') {
        $cookie_file_path = SITE_PATH . "cookies/" . strtolower($username) . '.txt';

        //if ($create_cookie) {

        if ($create_cookie && !file_exists($cookie_file_path)) {
            $fp = fopen($cookie_file_path, "wb");
            fclose($fp);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!is_null($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        if (is_null($header) && !empty($agent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, LOGIN_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, LOGIN_TIMEOUT);


        if (!is_null($proxy) && $proxy != '') {
            //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        if (!is_null($postData)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        /* if(!empty($httpUsername) && !empty($httpPassword)){
          echo $httpUsername . ':' . $httpPassword;
          curl_setopt($ch, CURLOPT_USERPWD, $httpUsername . ':' . $httpPassword);
          curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
          } */
        if (is_null($header) && !empty($reffer)) {
            curl_setopt($ch, CURLOPT_REFERER, $reffer);
        }
        if (!empty($cookie_file_path)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
        }

        //curl_setopt($ch, CURLOPT_PROXYUSERPWD, "harristes109:6657wBSm");
        // Add Delay
        $delay = 0;
        $delayArray = array($this->lowDelay, $this->highDelay);
        $delay = $delayArray[array_rand($delayArray, 1)];
        sleep($delay);

        $result = curl_exec($ch);
        if ($result == false) {
            $curlResult = $this->checkCurlResult(curl_error($ch), $proxy, $url);
        }

        $info_array = curl_getinfo($ch);

        $this->last_url = $info_array['url'];
        curl_close($ch);

        return $result;
    }
}

