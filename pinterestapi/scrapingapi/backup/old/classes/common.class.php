<?php

class Common {

    private $globalProxy = '';
    private $globalProxyArray = array();
    private $maxProxyTry = 3;
    public $globalCount = 0;
    private $lowDelay = 0.2;
    private $highDelay = 0.3;
    public $sitePath = '/var/www/html/virtual/fs-manager/twitter-manager.fs-manager.duribl.com/';
    private $threadPerAccount = 0;
    private $cookieName;
    public $last_url = '';

    function Common() {
        $this->cookieName = '';
    }

    function setMaxProxyTry($val) {
        $this->maxProxyTry = $val;
    }

    function getMaxProxyTry() {
        return $this->maxProxyTry;
    }

    function getSitePath() {
        return $this->sitePath;
    }

    function setGlobalProxy($val) {
        $this->globalProxy = $val;
    }

    function getGlobalProxy() {
        return $this->globalProxy;
    }

    function setGlobalProxyArray($val) {
        $this->globalProxyArray = $val;
    }

    function getGlobalProxyArray() {
        return $this->globalProxyArray;
    }

    function setThreadPerAccount($val) {
        return $this->threadPerAccount;
    }

    function getThreadPerAccount() {
        return $this->threadPerAccount;
    }

    function getCookieName() {
        return $this->cookieName;
    }

    function setCookieName($val) {
        $this->cookieName = $val;
    }

    function get_last_url() {
        return $this->last_url;
    }

    function is_email($email) {
        if (!eregi("^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
            return false;
        }
        return true;
    }

    function is_empty($val) {
        if (empty($val)) {
            return false;
        }
        return true;
    }

    function getProxy() {
        $proxy_list = array('67.222.147.199:3128', '67.222.147.200:3128', '67.222.147.201:3128', '208.82.100.81:3128');
        $rand_key = array_rand($proxy_list, 1);
        $proxy = $proxy_list[$rand_key];
        return $proxy;
    }

    function getSingleProxy() {
        $backup_proxies = BACKUP_PROXIES;
        $proxy_list = explode(',', $backup_proxies);
        $rand_key = array_rand($proxy_list, 1);
        $proxy = $proxy_list[$rand_key];
        echo "\n Backup Proxy = " . $proxy . "\n";
        return $proxy;
    }

    function removeCommaFromCount($str) {
        $str = trim($str);
        $str = str_replace(',', '', $str);
        return $str;
    }

    function is_suspeneded($username, $suspended) {
        if (!empty($username) && $suspended) {
            $objAccounts->setUsername($username);
            $sql = $objAccounts->updateSuspended(-1); // account is suspended
            $db->execute($sql);
            $objAccounts->setUsername($username);
            $db->execute($objAccounts->updateLoginActive(0));
            return true;
        }

        return false;
    }

    function getAgent() {

        /* 	Mozilla/5.0 (Windows NT 6.0) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.60 Safari/534.24
          Mozilla/5.0 (Windows NT 5.1) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.57 Safari/534.24
          Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.205 Safari/534.16
          Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.204 Safari/534.16
          Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1
          Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)
          Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E; InfoPath.3; Creative AutoUpdate v1.40.02)
          Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.10
          Opera/9.80 (Windows NT 6.0; U; en) Presto/2.7.62 Version/11.00
          Opera/9.80 (Macintosh; Intel Mac OS X; U; en) Presto/2.6.30 Version/10.61
          Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.11) Gecko/20101023 Firefox/3.6.11 (Palemoon/3.6.11) ( .NET CLR 3.5.30729; .NET4.0E) */


        $agent_list = array('Mozilla/5.0 (Windows NT 5.2; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Mozilla/5.0 (WindowsCE 6.0; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Mozilla/5.0 (Windows NT 6.0; rv:2.0b6pre) Gecko/20100907 Firefox/4.0b6pre',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.2; Trident/4.0; Media Center PC 4.0; SLCC1; .NET CLR 3.0.04320)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 1.1.4322)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.0; Trident/4.0; InfoPath.1; SV1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 3.0.04506.30)',
            'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.0; Trident/4.0; FBSMTWB; .NET CLR 2.0.34861; .NET CLR 3.0.3746.3218; .NET CLR 3.5.33652; msn OptimizedIE8;ENUS)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; Media Center PC 6.0; InfoPath.2; MS-RTC LM 8)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; Media Center PC 6.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; InfoPath.3; .NET4.0C; .NET4.0E; .NET CLR 3.5.30729; .NET CLR 3.0.30729; MS-RTC LM 8)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; InfoPath.2)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 3.0)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; msn OptimizedIE8;ZHCN)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8; InfoPath.3; .NET4.0C; .NET4.0E) chromeframe/8.0.552.224',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8; .NET4.0C; .NET4.0E; Zune 4.7; InfoPath.3)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8; .NET4.0C; .NET4.0E; Zune 4.7)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; Zune 4.0)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; OfficeLiveConnector.1.4; OfficeLivePatch.1.3; yie8)'
        );
        $rand_key = array_rand($agent_list, 1);
        $agent = $agent_list[$rand_key];
        return $agent;
    }

    function checkCurlResult($ch_error, $proxy, $url = '') {
        if ($ch_error != '') {
            $base_path = SITE_PATH . 'curlerrorlog';
            if (!file_exists($base_path)) {
                mkdir($base_path, 0777);
            }
            $file = $base_path . "/" . date('Ymd') . '.txt';
            echo "\nCurl error: " . $ch_error . " for url = " . $url . "\n";
            //Proxy CONNECT aborted due to timeout		
            $logText = $proxy . ' ' . $ch_error . "\n";
            if (!empty($logText)) {
                file_put_contents($file, $logText, FILE_APPEND);
            }
            return false;
        } else {
            return true;
        }
    }

    function get_range($ch) {
        $range = null;

        switch ($ch) {
            case ($ch >= 'a' && $ch <= 'e') :
                $range = 1;
                break;
            case ($ch >= 'f' && $ch <= 'j') :
                $range = 2;
                break;
            case ($ch >= 'k' && $ch <= 'o') :
                $range = 3;
                break;
            case ($ch >= 'p' && $ch <= 't') :
                $range = 4;
                break;
            case ($ch >= 'u' && $ch <= 'y') :
                $range = 5;
                break;
            default :
                $range = 6;
                break;
        }
        return $range;
    }

    function get_follow_table_name($username) {
        $str_lower_username = strtolower($username);
        $result = array();
        $result['first_ch'] = strtolower(substr($str_lower_username, 0, 1));
        $result['second_ch'] = strtolower(substr($str_lower_username, 1, 1));

        $result['table_name'] = $result['first_ch'] . $this->get_range($result['second_ch']);

        return $result;
    }

    function formatKeyword($rawKeyword = null) {
        $keyword = '';
        //keyword modification for the url
        $keyword = trim($keyword);
        $keyword = str_replace(' ', '+', $rawKeyword);
        $keyword = strtolower($keyword);
        return $keyword;
    }

    function getFileContent($path) {
        $fileContent = '';
        if (file_exists($path)) {
            $fileContent = file_get_contents($path);
        }
        return $fileContent;
    }

    function get_plain($str) {
        return strip_tags($str);
    }

    function shortString($string, $charactersNumber) {
        if (strlen($string) > ($charactersNumber - 3)) {
            $string = substr($string, 0, $charactersNumber);
            $string .= '..';
            return $string;
        } else {
            return $string;
        }
    }

    function saveDebugContent($username, $text) {
        if (DEBUG_MODE && ($text != '')) {
            $username = strtolower($username);
            $debug_sir = "debug";
            $base_path = SITE_PATH . $debug_sir . '/';
            if (!file_exists($base_path)) {
                mkdir(SITE_PATH . $debug_sir, 0777);
            }
            $user_dir_path = $base_path . $username;
            $file_name = date('Ymd') . '.txt';
            $file_full_path = $user_dir_path . '/' . $file_name;

            if (!file_exists($user_dir_path)) {
                mkdir($user_dir_path, 0777);
            }
            $text .= "\n ---------------------------------------------------------------------- \n";
            error_log($text . " \n", 3, $file_full_path);
            //file_put_contents($path,$text." \n",FILE_APPEND);
        }
    }

    function saveUserDeatil($username, $content) {
        if (!empty($content)) {
            $username = strtolower($username);
            $detail_dir = "users_detail";
            $base_path = SITE_PATH . $detail_dir . '/';
            if (!file_exists($base_path)) {
                mkdir(SITE_PATH . $detail_dir, 0777);
            }
            $file_name = $username . '.txt';
            $file_full_path = $base_path . '/' . $file_name;
            echo "\n\n===== Detail Stored In File ======\n\n";
            file_put_contents($file_full_path, $content);
        }
    }

    function getUserDeatil($username) {
        $content = null;
        $username = strtolower($username);
        $detail_dir = "users_detail";
        $base_path = SITE_PATH . $detail_dir . '/';
        $file_name = $username . '.txt';
        $file_full_path = $base_path . '/' . $file_name;
        if (file_exists($file_full_path)) {
            echo "\n\n===== Detail From File ======\n\n";
            return json_decode(file_get_contents($file_full_path), true);
        }

        return null;
    }

    /* function saveHTMLDebugContent($text){
      if(HTML_DEBUG_MODE){
      $path = SITE_PATH.'debug/'.date('Ymd').'_HTML.txt';
      $text .= "\n ---------------------------------------------------------------------- \n" ;
      error_log($text." \n", 3, $path);
      }
      } */

    function saveLog($base_path, $username, $text) {
        if (LOG_ENABLED && $text && $text != '') {
            $username = strtolower($username);
            $user_dir_path = $base_path . $username;
            $file_name = date('Ymd') . '.txt';
            $file_full_path = $user_dir_path . '/' . $file_name;

            if (!file_exists($user_dir_path)) {
                mkdir($user_dir_path, 0777);
            }
            $text .= "\n ---------------------------------------------------------------------- \n";
            error_log($text . " \n", 3, $file_full_path);
            //file_put_contents($path,$text." \n",FILE_APPEND);
        }
    }

    function saveCapchaCalls($text = '') {
        $path = SITE_PATH . 'capcha_log/' . date('Ymd') . '.txt';
        if (!file_exists(SITE_PATH . 'capcha_log')) {
            mkdir(SITE_PATH . 'capcha_log', 0777);
        }
        file_put_contents($path, $text . " \n", FILE_APPEND);
    }

    function getListContent($user_name) {
        $path = SITE_PATH . 'lists/' . strtolower($user_name) . '_lists.txt';
        if (!file_exists($path)) {
            file_put_contents($path, '');
        } else {
            $content = file_get_contents($path);
            $content_array = explode("\n", $content);
            $final_array = array();
            foreach ($content_array as $con) {
                $token_colon = explode(':', $con);
                $list_name = $token_colon[0];
                $temp_con = str_replace($list_name . ':', '', $con);
                $temp_con = trim($temp_con);
                if (!empty($list_name)) {
                    $final_array[$list_name] = array();
                }
                if (!empty($temp_con)) {
                    $users = explode(',', $temp_con);
                    $final_array[$list_name] = $users;
                }
            }
            if (count($final_array) > 0) {
                return $final_array;
            }
        }
        return null;
    }

    function listExist($username, $list_name) {
        $content = $this->getListContent($username);
        $list_name = strtolower($list_name);

        if (isset($content[$list_name])) {
            return $content[$list_name];
        }
        return null;
    }

    function userExistInList($username, $list_name, $user_to_check) {
        $content = $this->getListContent($username);
        if (isset($content[$list_name])) {
            $array = $content[$list_name];
            if (in_array($user_to_check, $array)) {
                return $array;
            }
        }
        return null;
    }

    function addList($user_name, $list_name) {
        $file_path = SITE_PATH . 'lists/' . strtolower($user_name) . '_lists.txt';
        file_put_contents($file_path, $list_name . ":\n", FILE_APPEND);
    }

    function addUserInList($user_name, $list_name, $user_to_add) {
        $file_path = SITE_PATH . 'lists/' . strtolower($user_name) . '_lists.txt';
        $content = $this->getListContent($user_name);
        $content[$list_name][] = $user_to_add;


        $str = '';
        foreach ($content as $list => $con) {
            if (!empty($list)) {
                $str .= $list . ":" . implode(",", $con) . "\n";
            }
        }
        echo $str;
        file_put_contents($file_path, $str);
    }

    function updateListContent($username, $list_name, $user_to_check) {
        if ($this->listExist($username, $list_name)) {
            if (!$this->userExistInList($username, $list_name, $user_to_check)) {
                
            }
        }
        $content = $this->getListContent($username);
        $array = $content[$list_name];
        if (in_array($user_to_check, $array)) {
            return true;
        } else {
            return false;
        }
        return false;
    }

    function recursiveSplit($string, $layer) {
        //$pattern = '/\$choose\{(([^\$choose\{endchoose]*|(?R))*)endchoose\}\$/';
        ///\((([^()]*|(?R))*)\)/

        $pattern = "/\$choose\{(([^\$choose\{endchoose\}\$]*|(?R))*)endchoose\}\$/";

        //$pattern = "/\((([^()]*|(?R))*)\)/"
        preg_match_all($pattern, $string, $matches);
        // iterate thru matches and continue recursive split
        if (count($matches) > 1) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                if (is_string($matches[1][$i])) {
                    if (strlen($matches[1][$i]) > 0) {
                        echo "<pre>Layer " . $layer . ":   " . $matches[1][$i] . "</pre><br />";
                        $this->recursiveSplit($matches[1][$i], $layer + 1);
                    }
                }
            }
        }
    }

    function replaceChoose_inner(&$template) {

        $match_array = array();
        //$pattern = '/\$choose\{(([^\$choose\{endchoose\}\$]*|(?R))*)endchoose\}\$/';
        $pattern = '/\$choose\{(.*?) endchoose\}\$/si';
        preg_match_all($pattern, $template, $match);
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $tokens = explode('|??|', $val);
                shuffle($tokens);
                $selected = $tokens[array_rand($tokens, 1)];
                $template = str_replace($match[0][$key], $selected, $template);
            }
        }
        return $template;
    }

    /* function replaceChoose_inner(&$template){

      $match_array = array();
      //$pattern = '/\$choose\{(([^\$choose\{endchoose\}\$]*|(?R))*)endchoose\}\$/';
      $pattern = '/\$choose\{(.*?) endchoose\}\$/si';
      preg_match_all($pattern,$template,$match);
      print_r($match);
      if(isset($match[1][0])){
      foreach($match[1] as $key=>$val){
      $tokens = explode('|??|',$val);
      shuffle($tokens);
      $selected = $tokens[array_rand($tokens,1)];
      $template = str_replace($match[0][$key],$selected,$template);
      }
      }

      echo '<br> Template';
      echo $template;
      return $template;
      } */

    function replaceChoose(&$template) {



        $startPos = array();
        $endPost = array();


        $i = 0;
        $n = 0;
        while (strpos($template, '$choose{', $n) !== false) {
            $startPos[$i] = $n = strpos($template, '$choose{', $n);
            $n++;
            $i++;
        }


        $i = 0;
        $n = 0;
        while (strpos($template, 'endchoose}$', $n) !== false) {
            $endPost[$i] = $n = strpos($template, 'endchoose}$', $n);
            $n++;
            $i++;
        }


        //print_r($startPos); 
        //print_r($endPost); 

        $innerPostion = 0;

        if (count($endPost) > 0) {

            for ($c = count($endPost) - 1; $c >= 0; $c--) {

                //echo $startPos[$c];
                if ($startPos[$c] < $endPost[0]) {
                    $innerPostion = $startPos[$c];
                    break;
                }
            }

            $limit = ($endPost[0]) - $innerPostion;

            $str_found = $forinnerChoose = substr($template, $innerPostion, $limit + 11);

            $this->replaceChoose_inner($forinnerChoose);

            $template = str_replace($str_found, $forinnerChoose, $template);

            if (strpos($template, '$choose{') !== false)
                $template = $this->replaceChoose($template);
        }

        return $template;
    }

    function replaceTitle(&$template, $titleResult = array()) {
        preg_match_all('/\$title(.*?)\$/si', $template, $match);
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $titleX = $val;
                $current_title = addslashes($titleResult[$titleX]['title']);
                $template = str_replace($match[0][$key], $current_title, $template);
            }
        }

        return $template;
    }

    function replaceDescription(&$template, $titleResult = array()) {
        preg_match_all('/\$desc(.*?)\$/si', $template, $match);
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $descX = $val;
                $current_desc = addslashes($titleResult[$descX]['description']);
                $template = str_replace($match[0][$key], $current_desc, $template);
            }
        }
        return $template;
    }

    function replaceBitlyURL(&$template, $titleResult = array(), $proxy, $acc) {
        $current_url = '';
        $objBitly = new Bitly();
        preg_match_all('/\$convert.bitly\{(.*?)\}\$/si', $template, $match);
        //print_r($match);
        if (isset($match[1][0])) {
            $rawBitly = $match[1][0];
            echo $rawBitly . '   ' . $proxy;
            $bitlyResult = $objBitly->get_bitly_short_url($rawBitly, $acc['bitly_api_login'], $acc['bitly_api_key'], 'json', $proxy);
            //print_r($bitlyResult);
            $current_url = $bitlyResult['data']['url'];
            $template = str_replace($match[0][0], $current_url, $template);
        }
        return $current_url;
    }

    function replacePrice(&$template, $titleResult = array()) {
        preg_match_all('/\$price(.*?)\$/si', $template, $match);
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $titleX = $val;
                $current_title = addslashes($titleResult[$titleX]['price']);
                $template = str_replace($match[0][$key], $current_title, $template);
            }
        }

        return $template;
    }

    function replaceReviews(&$template, $titleResult = array()) {
        preg_match_all('/\$reviews(.*?)\$/si', $template, $match);
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $titleX = $val;
                $current_title = addslashes($titleResult[$titleX]['reviews']);
                $template = str_replace($match[0][$key], $current_title, $template);
            }
        }

        return $template;
    }

    function replaceRating(&$template, $titleResult = array()) {
        preg_match_all('/\$rating(.*?)\$/si', $template, $match);
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $titleX = $val;
                $current_title = addslashes($titleResult[$titleX]['rating']);
                $template = str_replace($match[0][$key], $current_title, $template);
            }
        }

        return $template;
    }

    function replaceImage(&$template, $titleResult = array()) {
        preg_match_all('/\$image(.*?)\$/si', $template, $match);
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $titleX = $val;
                $current_title = addslashes($titleResult[$titleX]['image']);
                $template = str_replace($match[0][$key], $current_title, $template);
            }
        }

        return $template;
    }

    function replaceURL(&$template, $titleResult = array(), $type = '', $url_array = null) {
        if ($type == 'multi') {
            $template = $this->replaceAllURL($template, $titleResult, $url_array);
        } else {
            preg_match_all('/\$link(.*?)\$/si', $template, $match);
        }
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $urlX = $val;
                $current_url = $titleResult[$urlX]['link'];
                $template = str_replace($match[0][$key], $current_url, $template);
            }
        }
        return $template;
    }

    function replaceTrim(&$template, $current_url) {
        preg_match_all('/\$trim\{(.*?)\}\$/si', $template, $match);
        if (isset($match[1][0])) {
            $titleToReplace = $match[0];
            $rawTrim = $match[1];
            foreach ($rawTrim as $key => $raw) {
                $str_wn_trim = str_replace($titleToReplace[$key], '', $template);
                if (strlen($raw . $str_wn_trim) > 140) {
                    $newTitle = substr($raw, 0, (140 - (strlen($str_wn_trim))));
                    $template = str_replace($titleToReplace[$key], $newTitle, $template);
                } else {
                    $template = str_replace($titleToReplace[$key], $raw, $template);
                }
            }
        }
        return $template;
    }

    function replaceEmptyTrim(&$template) {
        preg_match_all('/\$trim\{(.*?)\}\$/si', $template, $match);
        if (isset($match[1][0])) {
            if (isset($match[1][0]) && trim($match[1][0]) == "") {
                $template = str_replace($match[0][0], "", $template);
            }
        }
        if (strpos($template, '$trim{}$') !== false) {
            $template = str_replace('$trim{}$', '', $template);
        }
        return $template;
    }

    function replaceEmptyBilty(&$template) {
        if (strpos($template, '$convert.bitly{}$') !== false) {
            preg_match_all('/\$convert.bitly\{(.*?)\}\$/si', $template, $match);
            if (isset($match[1][0])) {
                $template = str_replace($match[0][0], $match[1][0], $template);
            }
        }
        if (strpos($template, '$convert.bitly{}$') !== false) {
            $template = str_replace('$convert.bitly{}$', '', $template);
        }
        return $template;
    }

    function replaceTrimForRetweet(&$template, $current_url, $retweet) {
        preg_match_all('/\$trim\{(.*?)\}\$/si', $template, $match);
        if (isset($match[1][0])) {
            $titleToReplace = $match[0];
            $rawTrim = $match[1];
            foreach ($rawTrim as $key => $raw) {
                $tempStr = $raw . ' ' . $current_url;
                $tempStrLength = strlen($tempStr);
                if ($tempStrLength > 140) {
                    $newTitle = substr($raw, 0, (140 - (strlen($current_url) + 1)));
                    //$newTitle = substr($raw,0,$strCount-1);
                    $template = str_replace($titleToReplace[$key], $newTitle, $template);
                } else {
                    $template = str_replace($titleToReplace[$key], $raw, $template);
                }
            }
        }
        return $template;
    }

    function replaceTags(&$template, $titleResult = array()) {
        preg_match_all('/\$tags(.*?)\$/si', $template, $match);
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $descX = $val;
                $current_desc = addslashes($titleResult[$descX]['tags']);
                $template = str_replace($match[0][$key], $current_desc, $template);
            }
        }
        return $template;
    }

    function tagsStrToArry($arr) {

        $tags = array();
        if (count($arr))
            foreach ($arr as $tag) {
                $tstr = str_replace('<![CDATA[', '', $tag['data']);
                $tstr = str_replace(']', '', $tag['data']);
                $tags[] = $tstr;
            }
        $tagsStr = implode(',', $tags);
        return $tagsStr;
    }

    function replace_content(&$template, $proxy) {
        preg_match_all('/\$get-content\{(.*?)endgetcontent\}\$/si', $template, $match);
        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $url = $match[1][$key];
                $content = "";
                if (!empty($url)) {
                    $api_array = $this->get_api_key();
                    $api_url = "http://access.alchemyapi.com/calls/url/URLGetText?apikey=" . $api_array['key'] . "&url=" . rawurlencode($url) . "&template=%24ENTITY&outputMode=json";
                    $content = $this->content_api($api_url, $api_array['proxy']);
                }
                $template = str_replace($match[0][$key], $content['text'], $template);
            }
        }
        return $template;
    }

    function get_api_key() {
        if (defined('API_KEYS')) {
            $key_array = explode("\n", API_KEYS);
            $key_rand = explode(",", $key_array[array_rand($key_array, 1)]);
            if (count($key_rand) > 0) {
                $return_array = array('proxy' => $key_rand[0], 'key' => $key_rand[1]);
                return $return_array;
            }
        }
        return null;
    }

    function content_api($url, $proxy = '') {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        if (!empty($proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        //return $result;
        return json_decode($result, true);
    }

    function replaceTranslation(&$template, $proxy) {



        preg_match_all('/\$translate-(.*?)\{(.*?)endtranslate\}\$/si', $template, $match);
        if (isset($match[1][0]) && !empty($match[2][0])) {

            foreach ($match[1] as $key => $val) {
                $target_lang = $match[1][$key];
                $src_lang = "";
                if (strpos($match[1][$key], "-") !== false) {
                    $lang_array = explode("-", $match[1][$key]);
                    $src_lang = $lang_array[0];
                    $target_lang = $lang_array[1];
                }
                $content = trim($match[2][0]);
                if (empty($src_lang)) {
                    $content = $this->doTranslate($content, 'en', $target_lang, $proxy); // translate to given languages
                    /* 				echo "<br>=======================Given==========================<br>";
                      echo $content;
                      echo "<br>===================================================<br>"; */
                    if (!empty($content)) {
                        $content = $this->doTranslate($content, $target_lang, 'en', $proxy); // retranslate to english to change the content
                        /* 					echo "<br>=======================English==========================<br>";
                          echo $content;
                          echo "<br>===================================================<br>"; */
                    }
                } else {
                    $content = $this->doTranslate($content, $src_lang, $target_lang, $proxy); // translate to given languages
                }
                $content = stripslashes($content);
                $content = addslashes($content);

                $template = str_replace($match[0][$key], $content, $template);
            }
        }
        /* 	echo "<br>===================================================<br>";
          echo $template;
          echo "<br>===================================================<br>"; */
        return $template;
    }

    function doTranslate($str, $src_lang, $target_lang = 'en', $proxy) {


        $url = "http://translate.google.com/translate_a/t";

        $data['url'] = $url;

        $header_array[] = 'Host: translate.google.com';
        $header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
        $header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        //$header_array[] = 'Accept-Language: en-us,en;q=0.5';
        //$header_array[] = 'Accept-Encoding: gzip,deflate';
        //$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
        //$header_array[] = 'Keep-Alive: 115';
        //$header_array[] = 'Connection: keep-alive';
        //$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
        //$header_array[] = 'X-Requested-With: XMLHttpRequest';
        //$header_array[] = 'X-PHX: true';
        $header_array[] = 'Referer: http://translate.google.com/';
        //$header_array[] = 'Pragma: no-cache';
        $header_array[] = 'Cache-Control: no-cache';


        //$url = 'http://translate.google.com/translate_t?text='.urlencode($str).'&langpair='.$src_lang.'|'.$target_lang;
        //http://translate.google.com/translate_t?text=Hello,my%20friend!&langpair=en|es


        $post_data = 'client=t&text=' . rawurlencode($str) . '&hl=en&sl=' . $src_lang . '&tl=' . $target_lang . '&multires=1&otf=1&pc=1&ssel=0&tsel=0';


        $res = $this->get_curl_results($url, $post_data, true, null, $header_array, null, $proxy);
        while (strpos($res, ',,')) {
            $res = str_replace(',,', ',"",', $res);
        }

        $result = json_decode($res, true);

        $string = "";
        foreach ($result[0] as $r) {
            //var_dump(trim($r[0]));
            $string .= $r[0];
            //echo $r[0].'<br/>';
        }
        return $string;
    }

    function replaceEmptyTranslation(&$template) {
        preg_match_all('/\$translate-(.*?)\{(.*?)endtranslate\}\$/si', $template, $match);
        if (isset($match[1][0]) && empty($match[2][0])) {
            foreach ($match[1] as $key => $val) {
                $template = str_replace($match[0][$key], '', $template);
            }
        }
        return $template;
    }

    function replaceEmptyEndPoint(&$template) {
        preg_match_all('/\$get-content\{(.*?)endgetcontent\}\$/si', $template, $match);
        if (isset($match[1][0]) && empty($match[2][0])) {
            foreach ($match[1] as $key => $val) {
                $template = str_replace($match[0][$key], '', $template);
            }
        }
        return $template;
    }

    function googleBlogSearchContentFilter($Feeds, $howMany = 10, $userrname = "", $cache = 0) {
        $howMany = GOOGLE_BLOG_FEED_COUNT;
        $response = array();
        $html = new simple_html_dom();
        $html = null;
        $html = str_get_html($Feeds);
        $result = array();
        foreach ($html->find('li.g') as $g) {
            $link = $g->find('h3.r a', 0)->href;
            $title = $g->find('h3.r a', 0)->innertext;
            $s = $g->find('div.s', 0);
            $desc = $this->strip_tags_content($s->innertext);
            if (empty($desc)) {
                $s = $g->find('div.s div.st', 0);
                $desc = $this->strip_tags_content($s->innertext);
            }
            $result[] = array('title' => strip_tags($title),
                'link' => $link,
                'description' => $this->strip_tags_content($s->innertext));
        }
        //Cleans up the memory 
        $html->clear();

        foreach ($result as $counter => $Feed) {
            $link = urldecode($Feed['link']);
            if (strpos($link, 'url?q=') !== false) {
                $token = explode('url?q=', $link);
                $linkToken = explode('&amp;sa=U', $token[1]);
                $link = urldecode($linkToken[0]);
            }
            if (strpos($link, '?utm_source=') !== false) {
                $token = explode('?utm_source=', $link);
                $link = urldecode($token[0]);
            }
            $description = strip_tags($Feed['description']);
            if (!empty($link) && strpos($link, '/search?q') === false) {
                $response[$counter]['title'] = $Feed['title'];
                $response[$counter]['description'] = $description;
                $response[$counter]['link'] = $link;

                //$response[] = array('title' => $Feed['title'], 'description' => $description,'link' => $link);
            }
            if ($counter == $howMany - 1)
                break;
        }

        /* if($Feeds){
          foreach ($Feeds as $counter => $item) {
          $href  			= $item->get_link();
          $title 			= strip_tags($item->get_title());
          $unique_id		=	md5($title.rand(1000, 100000));
          $description 	= $this->get_plain($item->get_description());
          $source  		= $item->get_item_tags('http://purl.org/dc/elements/1.1/','publisher');
          $source	 		= ($source[0]['data']=='Untitled')?'':($source[0]['data']);

          $response[$counter]['title']		= $title;
          $response[$counter]['description']	= $description;
          $response[$counter]['source']		= $source;
          $response[$counter]['link']			= $href;

          if($counter == $howMany-1)break;
          }
          } */
        $response = array_merge(array(), $response);
        $debug_text = "\n ------- Google Blog Search (After Parsing) ------- \n";
        $debug_text .= "\n Result= " . serialize($response);
        $this->saveDebugContent($userrname, $debug_text);

        return $response;
    }

    function clean_amazon_url($url) {
        $url_array = explode("ref=", $url);
        return $url_array[0];
    }

    function clean_text($str = '') {
        $str = strip_tags($str);
        $str = trim($str);
        $str = str_replace('&quot;', "", $str);
        $str = str_replace('+', ' ', $str);
        $str = preg_replace('([^0-9a-zA-Z- ])', '', $str);
        return $str;
    }

    function amazonSearchContentFilter($Feeds, $howMany = 10, $userrname = "", $cache = 0, $link_param = "") {
        $howMany = AMAZON_FEED_COUNT;
        $response = array();
        $html = new simple_html_dom();
        $html = null;
        if (!empty($Feeds)) {
            //echo "</pre>";
            //echo "</pre>";
            //echo "</pre>";
            //echo $Feeds;
            $html = str_get_html($Feeds);
            $result = array();
            $counter = 0;
            foreach ($html->find('div[id=atfResults] div[class=result firstRow product]') as $g) {
                $rating = "";
                $reviews = 0;
                $price = "";
                $image = $g->find('div[class=image] img', 0)->src;
                $link = $g->find('div[class=data] h3[class=title] a', 0)->href;
                $title = $g->find('div[class=data] h3[class=title] a', 0)->innertext;
                $price = $g->find('div[class=data] div[class=newPrice] span[class=price]', 0)->innertext;
                if (empty($price)) {
                    $price = $g->find('div[class=data] div[class=usedNewPrice] span[class=subPrice] span[class=price]', 0)->innertext;
                }
                if (empty($price)) {
                    $price = $g->find('div[class=data] div[class=usedNewPrice] span[class=subPrice] span[class=price]', 1)->innertext;
                }
                $description = $g->find('div[class=data] div[class=fastTrack]', 0)->innertext;

                if (empty($description)) {
                    $description = $g->find('div[class=data] div[class=lowStock]', 0)->innertext;
                }
                $description = $this->strip_tags_content(strip_tags(trim($description)));
                if (!empty($g->find('div[class=data] div[class=starsAndPrime]', 0)->innertext)) {
                    if (!empty($g->find('div[class=data] div[class=starsAndPrime] div[class=stars]', 0)->innertext)) {
                        $rating = str_replace(" out of 5 stars", "", $g->find('div[class=data] div[class=starsAndPrime] div[class=stars] div[class=asinReviewsSummary] a', 0)->getAttribute('alt'));
                    }
                    if (!empty($g->find('div[class=data] div[class=starsAndPrime] div[class=reviewsCount]', 0)->innertext)) {
                        $reviews = $g->find('div[class=data] div[class=starsAndPrime] div[class=reviewsCount] a', 0)->innertext;
                    }
                }

                if (!empty($title)) {
                    $link_array = explode("?", $link);
                    $link = $link_array[0];
                    $response[] = array('title' => strip_tags($title),
                        'link' => $link . $link_param,
                        'price' => $price,
                        'description' => $description,
                        'rating' => $rating,
                        'reviews' => $reviews,
                        'image' => str_replace("_AA115_", "_AA300_", $image));
                    $counter++;
                }
                //echo "<br> Counter = ".$counter;
                if ($counter >= $howMany)
                    break;
            }
            if ($counter < $howMany) {
                foreach ($html->find('div[id=btfResults] div[class=result product]') as $g) {
                    $rating = "";
                    $reviews = 0;
                    $price = "";
                    $image = $g->find('div[class=image] img', 0)->src;
                    $link = $g->find('div[class=data] h3[class=title] a', 0)->href;
                    $title = $g->find('div[class=data] h3[class=title] a', 0)->innertext;
                    $price = $g->find('div[class=data] div[class=newPrice] span[class=price]', 0)->innertext;
                    if (empty($price)) {
                        $price = $g->find('div[class=data] div[class=usedNewPrice] span[class=subPrice] span[class=price]', 0)->innertext;
                    }
                    if (empty($price)) {
                        $price = $g->find('div[class=data] div[class=usedNewPrice] span[class=subPrice] span[class=price]', 1)->innertext;
                    }
                    $description = $g->find('div[class=data] div[class=fastTrack]', 0)->innertext;
                    if (empty($description)) {
                        $description = $g->find('div[class=data] div[class=lowStock]', 0)->innertext;
                    }
                    $description = $this->strip_tags_content(strip_tags(trim($description)));

                    if (!empty($g->find('div[class=data] div[class=starsAndPrime]', 0)->innertext)) {
                        if (!empty($g->find('div[class=data] div[class=starsAndPrime] div[class=stars]', 0)->innertext)) {
                            $rating = str_replace(" out of 5 stars", "", $g->find('div[class=data] div[class=starsAndPrime] div[class=stars] div[class=asinReviewsSummary] a', 0)->getAttribute('alt'));
                        }
                        if (!empty($g->find('div[class=data] div[class=starsAndPrime] div[class=reviewsCount]', 0)->innertext)) {
                            $reviews = $g->find('div[class=data] div[class=starsAndPrime] div[class=reviewsCount] a', 0)->innertext;
                        }
                    }

                    if (!empty($title)) {
                        $link_array = explode("?", $link);
                        $link = $link_array[0];
                        $response[] = array('title' => strip_tags($title),
                            'link' => $link . $link_param,
                            'price' => $price,
                            'description' => $description,
                            'rating' => $rating,
                            'reviews' => $reviews,
                            'image' => str_replace("_AA115_", "_AA300_", $image));
                        $counter++;
                    }
                    //echo "<br> Counter = ".$counter;
                    if ($counter >= $howMany)
                        break;
                }
            }

            //echo '<pre> REsult of Amazon <br><br>';
            //print_r($response);
            //echo "</pre>";
            //exit;
            //Cleans up the memory 
            $html->clear();
            $response = array_merge(array(), $response);
            $debug_text = "\n ------- Amazon (After Parsing) ------- \n";
            $debug_text .= "\n Result= " . serialize($response);
            $this->saveDebugContent($userrname, $debug_text);
        }
        return $response;
    }

    function etsySearchContentFilter($Feeds, $howMany = 10, $userrname = "", $cache = 0, $link_param = "") {
        $howMany = ETSY_FEED_COUNT;
        $response = array();
        $html = new simple_html_dom();
        $html = null;
        if (!empty($Feeds)) {
            //echo "</pre>";
            //echo "</pre>";
            //echo "</pre>";
            //echo $Feeds;
            $html = str_get_html($Feeds);
            $result = array();
            $counter = 0;
            foreach ($html->find('div[id=primary] div[id=recent_showcase] ul[class=listings scroller] ul[classs=listings] li[class=listing-card]') as $g) {
                $rating = "";
                $reviews = 0;
                $price = "";
                $link = $g->find('a[class=listing-thumb]', 0)->href;
                $image = $g->find('a[class=listing-thumb] img', 0)->src;
                $title = $g->find('div[class=listing-detail] div[class=listing-title] a', 0)->innertext;
                $maker = $g->find('div[class=listing-detail] div[class=listing-title] div[class=listing-maker] a', 0)->innertext;
                $price = $g->find('div[class=listing-detail] div[class=listing-price]', 0)->innertext;

                $price = trim(str_replace(array('$', 'USD'), "", $price));

                if (!empty($title)) {
                    $link_array = explode("?", $link);
                    $link = $link_array[0];
                    $response[] = array('title' => trim(strip_tags($title)),
                        'link' => $link . $link_param,
                        'price' => $price,
                        'image' => str_replace("il_170x135", "il_570xN", $image));
                    $counter++;
                }
                //echo "<br> Counter = ".$counter;
                if ($counter >= $howMany)
                    break;
            }

            if (count($counter) < $howMany) {

                foreach ($html->find('div[id=primary] ul[class=listings] li[class=listing-card]') as $g) {
                    $rating = "";
                    $reviews = 0;
                    $price = "";
                    $link = $g->find('a[class=listing-thumb]', 0)->href;
                    $image = $g->find('a[class=listing-thumb] img', 0)->src;
                    $title = $g->find('div[class=listing-detail] div[class=listing-title] a', 0)->innertext;
                    $maker = $g->find('div[class=listing-detail] div[class=listing-title] div[class=listing-maker] a', 0)->innertext;
                    $price = $g->find('div[class=listing-detail] div[class=listing-price]', 0)->innertext;

                    $price = trim(str_replace(array('$', 'USD'), "", $price));

                    if (!empty($title)) {
                        $link_array = explode("?", $link);
                        $link = $link_array[0];
                        $response[] = array('title' => trim(strip_tags($title)),
                            'link' => $link . $link_param,
                            'price' => $price,
                            'image' => str_replace("il_170x135", "il_570xN", $image));
                        $counter++;
                    }
                    //echo "<br> Counter = ".$counter;
                    if ($counter >= $howMany)
                        break;
                }
            }


            //echo '<pre> REsult of Amazon <br><br>';
            //print_r($response);
            //echo "</pre>";
            //exit;
            //Cleans up the memory 
            $html->clear();
            $response = array_merge(array(), $response);

            $debug_text = "\n ------- Etsy (After Parsing) ------- \n";
            $debug_text .= "\n Result= " . serialize($response);
            $this->saveDebugContent($userrname, $debug_text);
        }
        return $response;
    }

    function amazonSearchContentFilter_old($Feeds, $howMany = 10, $userrname = "", $cache = 0) {
        $howMany = AMAZON_FEED_COUNT;
        $response = array();
        $html = new simple_html_dom();
        $html = null;
        if (!empty($Feeds)) {
            //echo "</pre>";
            //echo "</pre>";
            //echo "</pre>";
            //echo $Feeds;
            $html = str_get_html($Feeds);
            $result = array();
            $counter = 0;
            foreach ($html->find('div[id=atfResults] div[class=result firstRow product]') as $g) {
                $rating = "";
                $reviews = 0;
                $price = "";
                $image = $g->find('div[class=image] img', 0)->src;
                $link = $g->find('div[class=data] div[class=title] a', 0)->href;
                $title = $g->find('div[class=data] div[class=title] a', 0)->innertext;
                $price = $g->find('div[class=data] div[class=newPrice] span[class=price]', 0)->innertext;
                if (empty($price)) {
                    $price = $g->find('div[class=data] div[class=usedNewPrice] span[class=subPrice] span[class=price]', 0)->innertext;
                }
                if (empty($price)) {
                    $price = $g->find('div[class=data] div[class=usedNewPrice] span[class=subPrice] span[class=price]', 1)->innertext;
                }
                $description = $g->find('div[class=data] div[class=fastTrack]', 0)->innertext;

                if (empty($description)) {
                    $description = $g->find('div[class=data] div[class=lowStock]', 0)->innertext;
                }
                $description = $this->strip_tags_content(strip_tags(trim($description)));
                if (!empty($g->find('div[class=data] div[class=starsAndPrime]', 0)->innertext)) {
                    if (!empty($g->find('div[class=data] div[class=starsAndPrime] div[class=stars]', 0)->innertext)) {
                        $rating = str_replace(" out of 5 stars", "", $g->find('div[class=data] div[class=starsAndPrime] div[class=stars] div[class=asinReviewsSummary] a', 0)->getAttribute('alt'));
                    }
                    if (!empty($g->find('div[class=data] div[class=starsAndPrime] div[class=reviewsCount]', 0)->innertext)) {
                        $reviews = $g->find('div[class=data] div[class=starsAndPrime] div[class=reviewsCount] a', 0)->innertext;
                    }
                }

                if (!empty($title)) {
                    $response[] = array('title' => strip_tags($title),
                        'link' => $link,
                        'price' => $price,
                        'description' => $description,
                        'rating' => $rating,
                        'reviews' => $reviews,
                        'image' => $image);
                    $counter++;
                }
                //echo "<br> Counter = ".$counter;
                if ($counter >= $howMany)
                    break;
            }
            if ($counter < $howMany) {
                foreach ($html->find('div[id=btfResults] div[class=result product]') as $g) {
                    $rating = "";
                    $reviews = 0;
                    $price = "";
                    $image = $g->find('div[class=image] img', 0)->src;
                    $link = $g->find('div[class=data] div[class=title] a', 0)->href;
                    $title = $g->find('div[class=data] div[class=title] a', 0)->innertext;
                    $price = $g->find('div[class=data] div[class=newPrice] span[class=price]', 0)->innertext;
                    if (empty($price)) {
                        $price = $g->find('div[class=data] div[class=usedNewPrice] span[class=subPrice] span[class=price]', 0)->innertext;
                    }
                    if (empty($price)) {
                        $price = $g->find('div[class=data] div[class=usedNewPrice] span[class=subPrice] span[class=price]', 1)->innertext;
                    }
                    $description = $g->find('div[class=data] div[class=fastTrack]', 0)->innertext;
                    if (empty($description)) {
                        $description = $g->find('div[class=data] div[class=lowStock]', 0)->innertext;
                    }
                    $description = $this->strip_tags_content(strip_tags(trim($description)));

                    if (!empty($g->find('div[class=data] div[class=starsAndPrime]', 0)->innertext)) {
                        if (!empty($g->find('div[class=data] div[class=starsAndPrime] div[class=stars]', 0)->innertext)) {
                            $rating = str_replace(" out of 5 stars", "", $g->find('div[class=data] div[class=starsAndPrime] div[class=stars] div[class=asinReviewsSummary] a', 0)->getAttribute('alt'));
                        }
                        if (!empty($g->find('div[class=data] div[class=starsAndPrime] div[class=reviewsCount]', 0)->innertext)) {
                            $reviews = $g->find('div[class=data] div[class=starsAndPrime] div[class=reviewsCount] a', 0)->innertext;
                        }
                    }

                    if (!empty($title)) {
                        $response[] = array('title' => strip_tags($title),
                            'link' => $link,
                            'price' => $price,
                            'description' => $description,
                            'rating' => $rating,
                            'reviews' => $reviews,
                            'image' => $image);
                        $counter++;
                    }
                    //echo "<br> Counter = ".$counter;
                    if ($counter >= $howMany)
                        break;
                }
            }

            //echo '<pre> REsult of Amazon <br><br>';
            //print_r($response);
            //echo "</pre>";
            //exit;
            //Cleans up the memory 
            $html->clear();
            $response = array_merge(array(), $response);
            $debug_text = "\n ------- Amazon (After Parsing) ------- \n";
            $debug_text .= "\n Result= " . serialize($response);
            $this->saveDebugContent($userrname, $debug_text);
        }
        return $response;
    }

    function clean_file_name($file_name) {
        $file_name = str_replace("%", "-", $file_name);
        $file_name = strtolower($file_name);
        return $file_name;
    }

    function get_cached_source($url) {
        $result = null;
        $base_path = SITE_PATH . 'cached_sources';
        if (!file_exists($base_path)) {
            mkdir($base_path, 0777);
        }
        $file_name = $base_path . "/" . urlencode($url) . ".parr";
        echo $file_name = $this->clean_file_name($file_name);
        if (file_exists($file_name)) {
            $file_time = filemtime($file_name);
            $file_time = ($file_time + (SOURCES_CACHE_EXPIRY_TIME * (60)));
            if ($file_time > time()) {
                echo "<br> File Found<br>";
                $result = unserialize(file_get_contents($file_name));
                return $result;
            } else {
                return null;
            }
        } else {
            echo "<br> File Not Found ! <br>";
        }
        return null;
    }

    function store_cached_source($url, $content) {
        $result = null;
        if (!empty($content)) {
            echo "<br> Storing Source Cache <br>";
            $base_path = SITE_PATH . 'cached_sources';
            if (!file_exists($base_path)) {
                mkdir($base_path, 0777);
            }
            $file_name = $base_path . "/" . urlencode($url) . ".parr";
            $file_name = $this->clean_file_name($file_name);
            file_put_contents($file_name, serialize($content));
        }
    }

    function get_user_proxy() {
        $proxy_list = explode("\n", USER_PROXIES);
        return $proxy_list[array_rand($proxy_list, 1)];
    }

    function get_user_agent() {
        $agent_list = explode("\n", USER_AGENTS);
        return $agent_list[array_rand($agent_list, 1)];
    }

    function strip_tags_content($text, $tags = '', $invert = FALSE) {
        /*
          This function removes all html tags and the contents within them
          unlike strip_tags which only removes the tags themselves.
         */
        //removes <br> often found in google result text, which is not handled below
        $text = str_ireplace('<br>', '', $text);

        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) AND count($tags) > 0) {
            //if invert is false, it will remove all tags except those passed a
            if ($invert == FALSE) {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
                //if invert is true, it will remove only the tags passed to this function
            } else {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            }
            //if no tags were passed to this function, simply remove all the tags
        } elseif ($invert == FALSE) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }

        return $text;
    }

    function getDomain($url) {
        //$token = explode('/url?q=',$url);
        //preg_match('@^(?:http://)?([^/]+)@i',$token[1], $matches);
        preg_match('@^(?:http://)?([^/]+)@i', $url, $matches);
        $host = $matches[1];
        return $host;
    }

    function googleNewsContentFilter($Feeds, $howMany = 5) {
        $response = array();
        $howMany = GOOGLE_NEWS_FEED_COUNT;
        if ($Feeds) {
            foreach ($Feeds as $counter => $item) {
                $hrefToken = explode('url=', $item->get_link());
                $href = $hrefToken[1];
                $title = strip_tags($item->get_title());
                $unique_id = md5($title . rand(1000, 100000));
                $description = $this->get_plain($item->get_description());
                $pubdate = $item->get_item_tags('', 'pubDate');
                $pubdate = $pubdate[0]['data'];
                $pubdate = str_replace('+0000', '', $pubdate);

                $response[$counter]['title'] = $title;
                $response[$counter]['description'] = $description;
                $response[$counter]['published'] = $pubdate;
                $response[$counter]['link'] = $href;

                if ($counter == $howMany - 1)
                    break;
            }
        }
        return $response;
    }

    function yahooNewsContentFilter($Feeds, $howMany = 5) {
        $response = array();
        $howMany = YAHOO_NEWS_FEED_COUNT;
        if ($Feeds) {
            foreach ($Feeds as $counter => $item) {
                $href = $item->get_link();
                $href_arr = explode('/**', $href);
                $href_new = str_replace(']]', '', $href_arr[1]);
                $title = $item->get_title();
                $unique_id = md5($title . rand(1000, 100000));
                $description = $this->get_plain($item->get_description());
                $pubdate = $item->get_item_tags('', 'pubDate');
                $pubdate = $pubdate[0]['data'];
                $pubdate = str_replace('+0000', '', $pubdate);
                $response[$counter]['title'] = $title;
                $response[$counter]['description'] = $this->shortString($description, 250);
                $response[$counter]['published'] = $pubdate;
                $response[$counter]['link'] = urldecode($href_new);

                //$response[$counter] .= '';
                if ($counter == $howMany - 1)
                    break;
            }
        }
        return $response;
    }

    function googleSurchurKeywords($Feeds) {
        $response = array();
        foreach ($Feeds as $counter => $item) {
            $href = $item->get_link();
            $title = strip_tags($item->get_title());

            $response[$counter]['title'] = $title;
            $response[$counter]['link'] = $href;
        }
        return $response;
    }

    function feedContentFilter($Feeds, $howMany = null) {
        $response = array();
        if (!$howMany) {
            $howMany = ALL_OTHER_FEED_COUNT;
        }
        if ($Feeds) {
            foreach ($Feeds as $counter => $item) {
                $href = $item->get_link();
                if (strpos($href, "/**") !== false) {
                    $href_arr = explode('/**', $href);
                    $href_new = str_replace(']]', '', $href_arr[1]);
                    $href = $href_new;
                }
                $title = $item->get_title();
                $description = $this->get_plain($item->get_description());
                $pubdate = $item->get_item_tags('', 'pubDate');
                $pubdate = $pubdate[0]['data'];
                $pubdate = str_replace('+0000', '', $pubdate);
                $tags = $item->get_item_tags('', 'category');
                $image_url = $item->get_link(0, 'image');
                if (empty($image_url)) {
                    $image_url = $item->get_item_tags('', 'image');
                    $image_url = $image_url[0]['data'];
                }
                $response[$counter]['title'] = $title;
                $response[$counter]['description'] = $this->shortString($description, 250);
                $response[$counter]['published'] = $pubdate;
                $response[$counter]['link'] = urldecode($href);
                $response[$counter]['tags'] = $this->tagsStrToArry($tags);
                $response[$counter]['image_url'] = $image_url;

                $size1 = $item->get_item_tags('', 'size1');
                $size2 = $item->get_item_tags('', 'size2');
                $size3 = $item->get_item_tags('', 'size3');
                $size4 = $item->get_item_tags('', 'size4');
                $size5 = $item->get_item_tags('', 'size5');
                $size6 = $item->get_item_tags('', 'size6');
                $size7 = $item->get_item_tags('', 'size7');
                $size8 = $item->get_item_tags('', 'size8');
                $size9 = $item->get_item_tags('', 'size9');
                $size10 = $item->get_item_tags('', 'size10');

                $response[$counter]['size1'] = $size1[0]['data'];
                $response[$counter]['size2'] = $size2[0]['data'];
                $response[$counter]['size3'] = $size3[0]['data'];
                $response[$counter]['size4'] = $size4[0]['data'];
                $response[$counter]['size5'] = $size5[0]['data'];
                $response[$counter]['size6'] = $size6[0]['data'];
                $response[$counter]['size7'] = $size7[0]['data'];
                $response[$counter]['size8'] = $size8[0]['data'];
                $response[$counter]['size9'] = $size9[0]['data'];
                $response[$counter]['size10'] = $size10[0]['data'];
                $response[$counter]['feed_index'] = $counter;

                //$response[$counter] .= '';
                if ($counter == $howMany - 1)
                    break;
            }
        }
        return $response;
    }

    public static function get_custom_data(&$feeds, $number, $tag, $deep_tag = null) {
        $tag_data = false;
        if (isset($feeds[$number]->data['child'][''][$tag])) {
            $tag_data = $feeds[$number]->get_item_tags('', $tag);
            if (empty($deep_tag)) {
                $tag_data = $tag_data[0]['data'];
            } else {
                $tag_data = $tag_data[0]['child'][''][$deep_tag][0]['data'];
            }
        }
        return $tag_data;
    }

    public static function destructFeedArray(&$feeds) {
        foreach ($feeds as $feed) {
            $feed->__destruct();
        }
        unset($feeds);
    }

    function twitterFeedContentFilter($Feeds, $howMany = 5) {
        $response = array();
        if ($Feeds) {
            foreach ($Feeds as $counter => $item) {
                $href = $item->get_link();
                if (strpos($href, "/**") !== false) {
                    $href_arr = explode('/**', $href);
                    $href_new = str_replace(']]', '', $href_arr[1]);
                    $href = $href_new;
                }
                $title = $item->get_title();
                $description = $this->get_plain($item->get_description());
                $pubdate = $item->get_item_tags('', 'pubDate');
                $pubdate = $pubdate[0]['data'];
                $pubdate = str_replace('+0000', '', $pubdate);
                $tags = $item->get_item_tags('', 'category');
                $idTokens = explode(':', $item->get_id());
                if (isset($idTokens[2]) && $idTokens[2] > 0) {
                    /* $response[$counter]['title']		= $title;
                      $response[$counter]['description']	= $this->shortString($description, 250);
                      $response[$counter]['published']	= $pubdate;
                      $response[$counter]['link']			= urldecode($href);
                      $response[$counter]['id']			=  $idTokens[2]; */
                    return $idTokens[2];
                }

                //$response[$counter] .= '';
                if ($counter == $howMany - 1)
                    break;
            }
        }
        return null;
    }

    /* function updateStoreFollowingList($content,$screenName,$key,$max=10){
      $followingFile = 'following/'.$screenName.'-follow.txt';
      $followingFileContent = '';
      if(file_exists($followingFile)){
      $followingFileContent = file_get_contents($followingFile);
      }
      $count = 1;
      foreach ($content as $res) {
      if($count>$max){
      break;
      }
      $this->followUser($screenName,$res[$key]);
      $strPosition = strpos($followingFileContent,$res[$key].':');
      if($strPosition === false && $res[$key]!='' ){
      $followingFileContent .= $res[$key].':1:'.date('Ymd').';';
      }
      $count++;
      }
      file_put_contents($followingFile, $followingFileContent);
      } */

    function updateStoreList($content, $screenName, $key, $max) {
        $followListFile = SITE_PATH . 'followlist/' . $screenName . '-list.txt';
        $followListFileContent = '';
        if (file_exists($followListFile)) {
            $followListFileContent = file_get_contents($followListFile);
        }
        foreach ($content as $res) {
            $strPosition = strpos($followListFileContent, $res[$key] . ';');
            if ($strPosition === false) {
                $followListFileContent .= $res[$key] . ';';
            }
        }
        file_put_contents($followListFile, $followListFileContent);
    }

    function mySQLSafe($value, $quote = "'") {

        // strip quotes if already in
        $value = str_replace(array("\'", "'"), "&#39;", $value);

        // Stripslashes 
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }

        //$value = htmlentities($value); 
        // Quote value
        if (version_compare(phpversion(), "4.3.0") == "-1") {
            $value = mysql_escape_string($value);
        } else {
            $value = mysql_real_escape_string($value);
        }
        $value = $quote . $value . $quote;

        return $value;
    }

//$this->get_curl_results(sfConfig::get('app_link_api_url'), $postData);


    function userExists($screenName, $completeList, $username = '') {
        foreach ($completeList as $list) {
            if (strtolower($list) == strtolower($screenName)) {
                $debug_text = "\n ------ User Exist -------\n";
                $debug_text .= "\n User = " . $screenName;
                $debug_text .= "\n Status = Found ";
                $this->saveDebugContent($username, $debug_text);
                return true;
            }
        }

        return false;
    }

    /* function user_exists_file($user_data,$username  = '',$max_records = 0){
      $username = strtolower($username);
      $following_file = SITE_PATH.'following/'.$username.'-follow.txt';
      $return_array = $user_data;
      print_r($user_data);
      exit;
      if(file_exists($following_file)){
      $file_content_array = explode(";",file_get_contents($following_file));
      foreach($user_data as $key=>$user){
      foreach($file_content_array as $arr){
      $tokens = explode(":",$arr);
      if(strtolower($tokens[0]) == $user['screen_name']){
      unset($return_array[$key]);
      }
      }
      }
      }
      if($max_reocrds && count($return_array)>$max_records){
      $return_array = array_chunk($return_array,$max_records);
      print_r($return_array);
      $return_array = $return_array[0];
      }
      return $return_array;
      } */

    function user_exists_file($screen_name, $username = '', $user_data) {
        if (count($user_data) > 0) {
            foreach ($user_data as $key => $user) {
                $tokens = explode(":", $user);
                if (strtolower($tokens[0]) == $screen_name) {
                    return true;
                }
            }
        }
        return false;
    }

    function checkDefaultImage($profile_image, $username, $friend_name) {
        $debug_text = "\n ------ Check Default Image -------\n";
        $debug_text .= "\n User = " . $friend_name;
        $debug_text .= "\n Profile Image = " . $profile_image;
        if (strpos(strtolower($profile_image), strtolower('/default_profile_images/')) === false) {
            $debug_text .= "\n Result = Found";
            return true;
        }
        echo $debug_text .= "\n Result = User with Default Image";
        $this->saveDebugContent($username, $debug_text);
        return false;
    }

    function killProcess($currentProcess) {
        $command = "/bin/ps aux | /bin/grep  '" . $currentProcess . "'";
        $commnadResult = shell_exec($command);
        $commandArray = explode("\n", $commnadResult);
        foreach ($commandArray as $arr) {
            $text = trim(str_replace('apache', '', $arr));
            $str = 'php ' . $currentProcess;
            if (strpos($text, $str) !== false) {
                $tokens = explode(' ', $text);
                $pid = $tokens[0];
                $commnadResult = trim(str_replace('apache', '', $commnadResult));
                $killCommand = "/bin/kill -9 " . $pid;
                $killResult = shell_exec($killCommand);
            }
        }
    }

    public function get_image($str) {
        $str = strip_tags($str, '<img>');
        $parts = explode('"', $str);
        if (isset($parts[1]))
            return $parts[1];
        else
            return false;
    }

    public function make_smaller($str) {
        return str_replace('_m.jpg', '_s.jpg', $str);
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

    function googleImageResults($keyword, $url, $proxy, $usename) {
        $start_time = microtime(true);
        $url = $url . urlencode($keyword);

        $cookie_name = "google_image_" . strtolower($usename) . ".txt";
        $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12";

        $header_array[] = 'Host: images.google.com';
        $header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12';
        //$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        //$header_array[] = 'Accept-Language: en-us,en;q=0.5';
        //$header_array[] = 'Accept-Encoding: gzip,deflate';
        //$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
        $header_array[] = 'Connection: keep-alive';

        $pageSource = $this->get_curl_results_source($url, $cookie_name, '', $header_array, $proxy, $user_agent);
        if (empty($pageSource)) {
            return null;
        }
        //$pageSource = $this->get_curl_results($url,null,false,'',null,0,$proxy);

        if (!preg_match_all('/imgres(.*?)cite>/i', $pageSource, $matches, PREG_SET_ORDER)) {
            return array();
        }
        $results = array();
        $images_array = array('pageSource' => $pageSource, 'matches' => $matches);
        foreach ($matches as $match) {
            // to get image url			
            $urlPattern = '/imgurl=(.*?)&amp;imgrefurl=(.*?)&amp;usg=/is';
            preg_match_all($urlPattern, $match[0], $urlMatches);
            // to get height and width
            $dimensionsPattern = '/h=([0-9]+)&amp;w=([0-9]+)/is';
            preg_match_all($dimensionsPattern, $match[0], $dimensionsMatches);
            //to get title of image			
            $titlePattern = '/<\/a><br>(.*?)<br>/is';
            preg_match_all($titlePattern, $match[0], $titleMatches);
            // to get thumbnail url
            $thumbPattern = '/src="(.*?)"/is';
            preg_match_all($thumbPattern, $match[0], $thumbMatches);

            $imgUrl = urldecode($urlMatches[2][0]);
            $completeImageUrl = urldecode($urlMatches[1][0]);
            $imgHeight = $dimensionsMatches[1][0];
            $imgWidth = $dimensionsMatches[2][0];
            $imgText = $titleMatches[1][0];
            $thumbUrl = $thumbMatches[1][0];
            if (!empty($completeImageUrl)) {
                $images_array['images'][] = array('title' => $imgText, 'thumbUrl' => $thumbUrl, 'imgUrl' => $imgUrl, 'completeImageUrl' => $completeImageUrl, 'width' => $imgWidth, 'height' => $imgHeight);
                break;
            }
        }

        $debug_text = "\n ------ Download Google Images -------\n Process Time =  " . ((float) (microtime(true) - $start_time));
        $debug_text .= "\n Page Source = " . $pageSource;
        $debug_text .= "\n Keyword = " . $keyword;
        $debug_text .= "\n Result = " . serialize($images_array['images']);
        $this->saveDebugContent($usename, $debug_text);

        return $this->donwloadImages($images_array['images']);
    }

    function flickerImageResults($keyword, $url, $proxy, $username, $is_background = false) {
        $start_time = microtime(true);
        $url = $url . urlencode($keyword);
        if ($is_background) {
            $url .= "&l=cc";
        }
        $simpleFeed = new SimplePie(null, null, null, $proxy);
        $simpleFeed->set_timeout(10);
        $simpleFeed->enable_cache(FALSE);
        $simpleFeed->set_max_checked_feeds(10);
        $simpleFeed->set_item_limit(30);
        $simpleFeed->set_stupidly_fast(TRUE);
        $simpleFeed->set_feed_url($url);
        $simpleFeed->handle_content_type();
        $simpleFeed->set_useragent('Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12');
        $success = $simpleFeed->init();
        if ($success) {
            $items = $simpleFeed->get_items();
            if (count($items) > 0) {
                $Feeds = $items;
            } else {
                $Feeds = null;
            }
        } else {
            $Feeds = null;
        }

        foreach ($Feeds as $counter => $item) {
            $href = $item->get_link();
            $title = $item->get_title();
            if (!$this->get_image($item->get_content()))
                continue;
            $image_src = $this->make_smaller($this->get_image($item->get_content()));
            $large_img = $this->get_image($item->get_content());
            $raw = $this->get_plain($item->get_description());
            $author = $item->get_author();
            $author_name = $author->name;
            $str = $author_name . ' posted a photo:';
            $raw = $this->get_plain($item->get_description());
            $description = str_replace($str, '', $raw);
            $tags_arr = $item->get_categories();
            $tags = '';
            foreach ($tags_arr as $tag) {
                $tags .= $tag->term . ' ';
            }
            if (!empty($large_img)) {
                $response[$counter]['title'] = $title;
                $response[$counter]['keywords'] = $tags;
                $response[$counter]['description'] = $description;
                $response[$counter]['author'] = $author_name;
                $response[$counter]['link'] = $href;
                $response[$counter]['img'] = $image_src;
                $response[$counter]['completeImageUrl'] = $large_img;
                $response[$counter]['picture'] = true;
                break;
            }
        }
        $debug_text = "\n ------ Download Flicker Images -------\n Process Time =  " . ((float) (microtime(true) - $start_time));
        $debug_text .= "\n Keyword = " . $keyword;
        $debug_text .= "\n Result = " . serialize($response);
        $this->saveDebugContent($username, $debug_text);
        return $this->donwloadImages($response);
    }

    function donwloadImages($images_array = array()) {
        $picture_path = SITE_PATH . PICTURES_PATH;
        if (!file_exists($picture_path)) {
            mkdir($picture_path, 0777);
        }
        $download_dir = SITE_PATH . PICTURES_PATH . DOWNLOAD_FOLDER;
        if (!file_exists($download_dir)) {
            mkdir($download_dir, 0777);
        }
        echo "\n\n";
        echo "download_dir = " . $download_dir;
        echo "\n\n";
        $imageName = '';
        foreach ($images_array as $image) {
            $imageName = basename($image['completeImageUrl']);
            $imageName = urldecode($imageName);
            $imageName = str_replace("'", "", $imageName);
            $imageName = str_replace('"', '', $imageName);
            $imageName = str_replace(' ', '_', $imageName);
            $imageName = time() . "_" . strtolower($imageName);
            $info = @getimagesize($image['completeImageUrl']);
            if (trim($imageName) != '') {
                if (copy($image['completeImageUrl'], $download_dir . $imageName)) {
                    break;
                }
            }
        }
        return $imageName;
    }

    function deleteDownlodedImage($imageName) {
        if (strpos($imageName, DOWNLOAD_FOLDER) !== false) {
            $file = SITE_PATH . PICTURES_PATH . $imageName;
            if (file_exists($file)) {
                unlink(PICTURES_PATH . $imageName);
            }
        }
    }

    function make_url($str) {
        $str = trim($str);
        $str = str_replace(" ", "+", $str);
        return $str;
    }

    function req_multiurls($arr_urls = array(), $proxy = null, $agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0') {

        // for storing cUrl handlers
        $chs = array();

        // for storing the reponses strings
        $contents = array();

        //return keys array
        $return_keys = array();

        // loop through an array of URLs to initiate
        // one cUrl handler for each URL (request)
        foreach ($arr_urls as $key => $url) {

            $return_keys[$url] = $key;

            $ch = curl_init($url);

            #add options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, REQUEST_TIMEOUT);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, REQUEST_TIMEOUT);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $agent);

            //curl_setopt($ch, CURLOPT_PROXYUSERPWD, "harristes109:6657wBSm");

            if (!is_null($proxy)) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
            }

            $chs[] = $ch;
        }

        // initiate a multi handler
        $mh = curl_multi_init();

        // add all the single handler to a multi handler
        foreach ($chs as $key => $ch) {
            curl_multi_add_handle($mh, $ch);
        }

        // execute the multi cUrl handler
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM || $active);

        // retrieve the reponse from each single handler
        foreach ($chs as $key => $ch) {
            #which url was used
            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

            if (curl_errno($ch) == CURLE_OK) {
                $contents[$return_keys[$url]] = curl_multi_getcontent($ch);
            } else {
                $contents[$return_keys[$url]] = "Err>>> " . curl_error($ch);
            }
        }
        curl_multi_close($mh);
        unset($mh);
        return $contents;
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

    /*     * * For Permutation Algo  ** */

    function resolveTemplate_2($final_output, $output) {
        //global $final_output, $output;
        $pop_val = array_pop($output);

        $str = $this->find_inner_unique($pop_val);
        $tokens = $this->get_probables_unique($str);
        //echo "<br><br>===========tokens=============<br><br>";
        //print_r($tokens);
        if (!empty($tokens) && !is_null($tokens)) {
            foreach ($tokens as $token) {
                //echo "<br><br> ============sub_str========== <br><br>";
                $sub_str = str_replace($str, $token, $pop_val);

                if (strpos($sub_str, '{*') !== false) {
                    array_push($output, $sub_str);
                    //echo "<br>${sub_str}<br>";
                    //array_push($final_output,$sub_str);
                    $final_output = $this->resolveTemplate_2($final_output, $output);
//					break;
                } else {
                    array_push($final_output, $sub_str);
                    //break;
                }
            }
        }
//		echo '<pre>';
//		echo "<br><br>===========final_output=============<br><br>";
//		echo $output;
        //print_r($final_output);
        //exit;
        return $final_output;
    }

    function find_inner_unique($template) {

        $forinnerChoose = false;
        $startPos = array();
        $endPost = array();

        $i = 0;
        $n = 0;
        while (strpos($template, '{*', $n) !== false) {
            $startPos[$i] = $n = strpos($template, '{*', $n);
            $n++;
            $i++;
        }

        $i = 0;
        $n = 0;
        while (strpos($template, '*}', $n) !== false) {
            $endPost[$i] = $n = strpos($template, '*}', $n);
            $n++;
            $i++;
        }

        $innerPostion = 0;
        if (count($endPost) > 0) {
            for ($c = count($endPost) - 1; $c >= 0; $c--) {
                if ($startPos[$c] < $endPost[0]) {
                    $innerPostion = $startPos[$c];
                    break;
                }
            }
            $limit = ($endPost[0]) - $innerPostion;
            $forinnerChoose = substr($template, $innerPostion, $limit + 2);
        }
        return $forinnerChoose;
    }

    function get_probables_unique($template) {
        $match_array = array();
        $tokens = null;
        $pattern = '/\{\*(.*?)\*\}/si';
        preg_match_all($pattern, $template, $match);

        if (isset($match[1][0]) && !empty($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $tokens = explode('||', $val);
            }
        }
        return $tokens;
    }

    function make_template_unqiue($output) {
        $template_count = 0;
        if (strpos($output[0], '{*') !== false && strpos($output[0], '*}') !== false) {

            $template_count = $this->check_total_templates($output[0]);
            echo "<br>template_count = $template_count<br>";
        } else {
            return $output;
        }

        if ($template_count > MAX_TEMPLATE_COMBINATIONS) {
            /* $slices = array_splice($result,MAX_TEMPLATE);
              unset($result);
              $result = array();
              $result = $slices[0]; */
            unset($result);
            $result = array();
            $madapi = new madapi();
            for ($i = 1; $i <= MAX_TEMPLATE_COMBINATIONS; $i++) {
                //$result[] = $obj_unique->buildContent($unique_content);
                //echo "<br><br>================================== buildContent ===================================<br><br>";
                //echo $unique_content;
                $madapi->fromString($output[0]);
                $result[] = $madapi->produce();
                //echo "<br><br>================================== buildContent ===================================<br><br>";				
                //print_r($result);
                //exit;
            }
        } else {
            $final_output = array();
            return $this->resolveTemplate_2($final_output, $output);
        }

        echo "<br>Total = " . count($result) . "<br>";
        //echo '<pre>';
        //print_r($result);
        return $result;
    }

    function check_total_templates($str) {
        $str_count = substr_count($str, '{*');
        $tokens = $this->get_probables_single($str);
        unset($str);
        return pow(count($tokens), $str_count);
    }

    function get_probables_single($template) {
        //echo "<br><br> ============getProbables========== <br><br>";
        $match_array = array();
        $tokens = null;
        $pattern = '/\{\*(.*?)\*\}/si';
        preg_match_all($pattern, $template, $match);
        //echo "<br><br> ===========match============= <br><br>";
        //print_r($match);
        if (isset($match[1][0]) && !empty($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                //if(strpos($val,'$choose{') === false && strpos($val,'endchoose}$') === false && strpos($val,'endchoose}$') !== false)
                if (strpos($val, '||') !== false) {
                    $tokens = explode('||', $val);
                    //echo "<br><br> ===========tokens============= <br><br>";
                    //print_r($tokens);
                    return $tokens;
                }
            }
        }
    }

    function getProbables($template) {
        $match_array = array();
        $pattern = '/\$choose\{(.*?)endchoose\}\$/si';
        preg_match_all($pattern, $template, $match);

        if (isset($match[1][0])) {
            foreach ($match[1] as $key => $val) {
                $tokens = explode('|??|', $val);
            }
        }
        return $tokens;
    }

    function findInner($template) {
        $forinnerChoose = false;
        $startPos = array();
        $endPost = array();

        $i = 0;
        $n = 0;
        while (strpos($template, '$choose{', $n) !== false) {
            $startPos[$i] = $n = strpos($template, '$choose{', $n);
            $n++;
            $i++;
        }

        $i = 0;
        $n = 0;
        while (strpos($template, 'endchoose}$', $n) !== false) {
            $endPost[$i] = $n = strpos($template, 'endchoose}$', $n);
            $n++;
            $i++;
        }

        $innerPostion = 0;

        if (count($endPost) > 0) {

            for ($c = count($endPost) - 1; $c >= 0; $c--) {

                if ($startPos[$c] < $endPost[0]) {
                    $innerPostion = $startPos[$c];
                    break;
                }
            }

            $limit = ($endPost[0]) - $innerPostion;

            $forinnerChoose = substr($template, $innerPostion, $limit + 11);
        }
        return $forinnerChoose;
    }

    function resolveTemplate($final_output, $output) {
        //global $final_output, $output;
        $pop_val = array_pop($output);
        $str = $this->findInner($pop_val);
        $tokens = $this->getProbables($str);
        foreach ($tokens as $token) {

            $sub_str = str_replace($str, $token, $pop_val);

            if (strpos($sub_str, '$choose{') !== false) {
                array_push($output, $sub_str);
                $final_output = $this->resolveTemplate($final_output, $output);
            }else
                array_push($final_output, $sub_str);
        }

        return $final_output;
    }

    function make_template($final_output, $output) {

        return array_unique($this->resolveTemplate($final_output, $output));
    }

    function arrayUnique($array, $preserveKeys = false) {
        // Unique Array for return
        $arrayRewrite = array();
        // Array with the md5 hashes
        $arrayHashes = array();
        foreach ($array as $key => $item) {
            // Serialize the current element and create a md5 hash
            $hash = md5(serialize($item));
            // If the md5 didn't come up yet, add the element to
            // to arrayRewrite, otherwise drop it
            if (!isset($arrayHashes[$hash])) {
                // Save the current element hash
                $arrayHashes[$hash] = $hash;
                // Add element to the unique Array
                if ($preserveKeys) {
                    $arrayRewrite[$key] = $item;
                } else {
                    $arrayRewrite[] = $item;
                }
            }
        }
        return $arrayRewrite;
    }

}

?>