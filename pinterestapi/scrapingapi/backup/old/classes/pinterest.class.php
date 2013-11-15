<?php

class Pinterest extends Common{
	
    private $pinterest_url;
	private $pinterest_url_ssl;
	

    public function Pinterest() {
        $this->pinterest_url = 'http://pinterest.com/';
		$this->pinterest_url_ssl = 'https://pinterest.com/';
        $html = '';
    }
	
	public function get_request_page($acc) {
		$start_time = microtime(true);
		$url = $this->pinterest_url.'landing/';
		
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		//$header_array[] = 'Referer: https://twitter.com/';
		//$proxy = parent::getProxy();
		parent::setGlobalProxy($acc['proxy']);
		parent::setCookieName($acc['username']);		
		
		$result = parent::get_curl_results($url, null, TRUE,$acc['username'],$acc['proxy'],$header_array);
		$debug_text = "\n ------ Get Request Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		return $result;
	}
	
	public function send_signup_request($acc) {
		$start_time = microtime(true);
		$url = $this->pinterest_url.'landing/?email='.rawurlencode($acc['email']);
		
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Referer: http://pinterest.com/landing/';
		//$proxy = parent::getProxy();
		
		$result = parent::get_curl_results($url, null, TRUE,$acc['username'],$acc['proxy'],$header_array);
		$debug_text = "\n ------ Send Sign Up Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		return $result;
	}
	
	
	public function send_request_twitter_app($acc,$oauth_token,$reffer = "") {
		$start_time = microtime(true);
		
		echo "<br><br>";
		echo $url = 'https://api.twitter.com/oauth/authenticate?oauth_token='.$oauth_token;
		
		echo "<br><br>";
		//https://api.twitter.com/oauth/authenticate?oauth_token=xTx6oKKLBKId3We3mFgymicLImM7eBFefWEIuaDdTbA
		
		$header_array[] = 'Host: api.twitter.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		if(!empty($reffer)){
			$header_array[] = 'Referer: '.$reffer;
		}
		
		
		$result = parent::get_curl_results($url, NULL, FALSE,$acc['username'].'-twitter',$acc['proxy'],$header_array);
		$debug_text = "\n ------ send_request_twitter_app -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		
		//echo "<br> ================  ".$url." ============= <br> ";
		//echo '<textarea cols="100" rows="50" id="active">'.$result.'</textarea>';
		//echo "<br>";
		
		return $result;
	}
	
	public function authenticate_with_twitter($acc,$reffer) {
		$start_time = microtime(true);
		
		$this->get_confirm_request_page($acc,$reffer);
		
		$url = $this->pinterest_url.'twitter/?invited=1';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Referer: '.$reffer;	
		
		$result = parent::get_curl_results($url, null, TRUE,$acc['username'],$acc['proxy'],$header_array);

		$debug_text = "\n ------ authenticate_with_twitter -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		
		//echo "<br> ================  ".$url." ============= <br> ";
		//echo '<textarea cols="100" rows="50" id="active">'.$result.'</textarea>';
		//echo "<br>";
		$oauth_token = '';
		$pattern = '/"http:\/\/twitter.com\/signup\?context=oauth&amp;oauth_token=(.*?)"/';
		preg_match_all($pattern,$result,$match);
		if(isset($match[1][0]) && !empty($match[1][0])){
			$oauth_token = trim($match[1][0]);
		}
		
		echo "<br> oauth_token = ";
		echo $oauth_token;
		echo "<br>";
		
		$authenticity_token = '';
		$match = array();
		$pattern = '/\<input name="authenticity_token" type="hidden" value="(.*?)"/';
		preg_match_all($pattern,$result,$match);
		if(isset($match[1][0]) && !empty($match[1][0])){
			$authenticity_token = trim($match[1][0]);
		}
		
		echo "<br> Authauthenticity_tokenToken = ";
		echo $authenticity_token;
		echo "<br>";
		//$result = $this->send_request_twitter_app($acc,$oauth_token,$reffer);
		//echo '<textarea cols="100" rows="50" id="active">'.$result.'</textarea>';
		$result = $this->auth_twitter_app($acc,$oauth_token,$authenticity_token);
		
		return $result;
	}
	
	public function auth_twitter_app($acc,$oauth_token,$authenticity_token) {
		$start_time = microtime(true);
		
		echo "<br><br>";
		echo $url = 'https://api.twitter.com/oauth/authenticate';
		echo "<br><br>";
		//https://api.twitter.com/oauth/authenticate?oauth_token=xTx6oKKLBKId3We3mFgymicLImM7eBFefWEIuaDdTbA
		
		$header_array[] = 'Host: api.twitter.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Referer: http://api.twitter.com/oauth/authenticate?oauth_token='.$oauth_token;
		$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
		
		
		
		echo $post_data = 'authenticity_token='.$authenticity_token.'&oauth_token='.$oauth_token.'&session%5Busername_or_email%5D='.$acc['username'].'&session%5Bpassword%5D='.$acc['password']."&remember_me=1";
		
		unlink(SITE_PATH."cookies/".strtolower($acc['username']).'-twitter.txt');
		
		$result = parent::get_curl_results($url, $post_data, FALSE,$acc['username'].'-twitter',$acc['proxy'],$header_array);
		
		//echo "<br> ================  ".$url." ============= <br> ";
		//echo '<textarea cols="100" rows="50" id="active">'.$result.'</textarea>';
		
		$debug_text = "\n ------ auth_twitter_app -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Post Data = ".$post_data;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		return $result;
	}
	
	public function get_confirm_request_page($acc, &$url) {
		$start_time = microtime(true);
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		//$header_array[] = 'Referer: https://twitter.com/';	
		
		$result = parent::get_curl_results($url, null, TRUE,$acc['username'],$acc['proxy'],$header_array);
		$debug_text = "\n ------ Get Confirm Request Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
                
                $url = parent::get_last_url();
                
		return $result;
	}
	
	
	public function get_signup_page($acc,$url_token){
		$start_time = microtime(true);
	  	$url = 'http://pinterest.com/twitter/?oauth_token='.$url_token;
	  	
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		//$header_array[] = 'Referer: http://pinterest.com/';
		//$proxy = parent::getProxy();
		$cookie_name = strtolower($acc['username']);
	   
	   //unlink(SITE_PATH."cookies/".strtolower($acc['username']).'.txt');
	  
	   $result = parent::get_curl_results($url, null, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
	   
	   //($url, $postData = null, $create_cookie = false, $username,$proxy = null,$header = null,$agent = '',$reffer = 'http://twitter.com/')
	   
		$debug_text = "\n ------ Get Sign- UP Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		//echo "<br><br> ================  ".$url." ============= <br><br> ";
		//echo '<textarea cols="100" rows="50" id="active">'.$result.'</textarea>';
		
       return $result;
	}
	
	public function create_account($acc,$invite,$csrf_token,$referer){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$url = "http://pinterest.com/register/";
		$img_url = "http://img.tweetimag.es/i/".$acc['username']."_o";
		
		$post_data = "username=".$acc['username']."&email=".rawurlencode($acc['email'])."&password=".$acc['password']."&invite=".$invite."&twitter=1&csrfmiddlewaretoken=".$csrf_token."&user_image=".rawurlencode($img_url);
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'X-CSRFToken: '.$csrf_token;
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Referer: '.$referer;
		
		$result = parent::get_curl_results($url, $post_data, TRUE,$cookie_name,$acc['proxy'],$header_array,'','');
		
		$debug_text = "\n ------ create_account -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Post Data = ".$post_data;
		$debug_text .= "\n Auth Token = ".$csrf_token;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		echo "\n\n Last Url = ".parent::get_last_url()." \n\n";
		
		$redrtect_url = 'http://pinterest.com/verify_captcha/?src=register&return=%2Fwelcome%2F';
		
		if(parent::get_last_url() == $redrtect_url || strpos(strtolower($result),strtolower('Are You a Human')) !== false){
			$last_result = parent::get_curl_results(parent::get_last_url(), null, false,$cookie_name,$acc['proxy'],null,'','');
			
			$debug_text = "\n ------ last_url -------\n Process Time =  ".((float)(microtime(true) - $start_time));
			$debug_text .= "\n URL = ".parent::get_last_url();
			$debug_text .= "\n Result = ".$last_result;
			parent::saveDebugContent($acc['username'],$debug_text);
			unset($debug_text);
			
			$capcha_url = "http://www.google.com/recaptcha/api/challenge?k=6LdYxc8SAAAAAHyLKDUP3jgHt11fSDW_WBwSPPdF&ajax=1&cachestop=0.9569200263535518";			
			$capcha_result = parent::get_curl_results($capcha_url, null, false,$cookie_name,$acc['proxy'],null,'','');
			
			
			
			$pattern = "/challenge : '(.*?)'/is";
			preg_match_all($pattern,$capcha_result,$match);
			
			$debug_text = "\n ------ capcha_url -------\n Process Time =  ".((float)(microtime(true) - $start_time));
			$debug_text .= "\n URL = ".$capcha_url;
			$debug_text .= "\n Match = ".serialize($match);
			$debug_text .= "\n Result = ".$capcha_result;
			parent::saveDebugContent($acc['username'],$debug_text);
			unset($debug_text);
			
			print_r($match);
			if(isset($match[1][0]) && !empty($match[1][0])){
				$image_text = $this->get_decapcha($acc,$match[1][0]);
				$debug_text = "\n ------ capcha_result -------\n ";
				$debug_text .= "\n View State = ".$match[1][0];
				$debug_text .= "\n Image Text = ".$image_text;
				
				if(!empty($image_text)){
					//http://pinterest.com/verify_captcha/?src=register&return=%2Fwelcome%2F
					$post_data = "challenge=".$match[1][0]."&response=".urlencode($image_text);
					
					
					$header_array = array();
					
					$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
					//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
					$header_array[] = 'Accept-Language: en-us,en;q=0.5';
					$header_array[] = 'Host: pinterest.com';
					$header_array[] = 'Connection: keep-alive';
					$header_array[] = 'X-CSRFToken: '.$csrf_token;
					$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
					$header_array[] = 'Referer: '.$redrtect_url;

					
					$capcha_result = parent::get_curl_results($redrtect_url, $post_data, false,$cookie_name,$acc['proxy'],$header_array,'','');
					$debug_text .= "\n URL = ".$redrtect_url;
					$debug_text .= "\n Post Data = ".$post_data;
					$debug_text .= "\n Result = ".$capcha_result;
					
					return json_decode($capcha_result,true);
				}
				
				parent::saveDebugContent($acc['username'],$debug_text);
				unset($debug_text);
			}
			
			//http://www.google.com/recaptcha/api/image?c=03AHJ_Vus90T_KAybsEQVBQ9oUEKT_H-Dt8LkELaCuCXmi5np4J0dOZ7Es-Z7Wfqs8EkYg3OaNXTlJcDwxVf6rOLfbo6DHRb2SXC3AMH5AXjGUJacFBWabzB130ZzQvjAGjaunfPUEDX2oNBVk7nuHFzovSOsAUw-cow
			
			//challenge : '03AHJ_Vus90T_KAybsEQVBQ9oUEKT_H-Dt8LkELaCuCXmi5np4J0dOZ7Es-Z7Wfqs8EkYg3OaNXTlJcDwxVf6rOLfbo6DHRb2SXC3AMH5AXjGUJacFBWabzB130ZzQvjAGjaunfPUEDX2oNBVk7nuHFzovSOsAUw-cow',
			
			
		}
		
		//echo "<br> ================  ".$url." ============= <br> ";
		//echo '<textarea cols="100" rows="50" id="active">'.$result.'</textarea>';
		
		//echo '<textarea cols="50" rows="50">'.$result.'</textarea>';
		
		return $result;
	
	}
	
	public function capcha_request($acc,$csrf_token,$redirect_url){
		$cookie_name = $acc['username'];
		$capcha_url = "http://www.google.com/recaptcha/api/challenge?k=6LdYxc8SAAAAAHyLKDUP3jgHt11fSDW_WBwSPPdF&ajax=1&cachestop=0.9569200263535518";			
		$capcha_result = parent::get_curl_results($capcha_url, null, false,$cookie_name,$acc['proxy'],null,'','');
		
		$pattern = "/challenge : '(.*?)'/is";
		preg_match_all($pattern,$capcha_result,$match);
		
		$debug_text = "\n ------ capcha_url -------\n";
		$debug_text .= "\n URL = ".$capcha_url;
		$debug_text .= "\n Match = ".serialize($match);
		$debug_text .= "\n Result = ".$capcha_result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		print_r($match);
		if(isset($match[1][0]) && !empty($match[1][0])){
			$image_text = $this->get_decapcha($acc,$match[1][0]);
			$debug_text = "\n ------ capcha_result -------\n ";
			$debug_text .= "\n View State = ".$match[1][0];
			$debug_text .= "\n Image Text = ".$image_text;
			parent::saveDebugContent($acc['username'],$debug_text);
			unset($debug_text);
			
			if(!empty($image_text)){
				//http://pinterest.com/verify_captcha/?src=register&return=%2Fwelcome%2F
				$post_data = "challenge=".$match[1][0]."&response=".urlencode($image_text);
				
				
				$header_array = array();
				
				$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
				//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
				$header_array[] = 'Accept-Language: en-us,en;q=0.5';
				$header_array[] = 'Host: pinterest.com';
				$header_array[] = 'Connection: keep-alive';
				$header_array[] = 'X-CSRFToken: '.$csrf_token;
				$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
				$header_array[] = 'Referer: ttp://pinterest.com/';

				
				$capcha_result = parent::get_curl_results($redirect_url, $post_data, false,$cookie_name,$acc['proxy'],$header_array,'','');
				$debug_text = "\n URL = ".$redirect_url;
				$debug_text .= "\n Post Data = ".$post_data;
				$debug_text .= "\n Result = ".$capcha_result;
				parent::saveDebugContent($acc['username'],$debug_text);
				unset($debug_text);
				
				return $capcha_result;
			}
			
			
		}
	}
	
	public function get_invitation_page($acc){
		
		$start_time = microtime(true);
	  	$url = $this->pinterest_url.'invites/';
	  	
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Referer: http://pinterest.com/';
		//$proxy = parent::getProxy();
		$cookie_name = $acc['username'];
	   
	  
	   $result = parent::get_curl_results($url, null, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
	   
	   //($url, $postData = null, $create_cookie = false, $username,$proxy = null,$header = null,$agent = '',$reffer = 'http://twitter.com/')
	   
		$debug_text = "\n ------ Get Invitation Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
       return $result;
		
	}
	
	public function send_invitation($acc,$auth_token,$friend_email){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
	  	$url = $this->pinterest_url.'invite/new/';
	  	
		$post_data = "name=somebody&message=&email=".$friend_email;
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/';
		$header_array[] = 'X-CSRFToken: '.$auth_token;
		$header_array[] = 'X-Requested-With: XMLHttpRequest';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		
		
		$result = parent::get_curl_results($url, $post_data, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
		//echo '<textarea cols="20" rows="20">'.$result.'</textarea>';
		$debug_text = "\n ------ send_invitation -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Post Data = ".$post_data;
		$debug_text .= "\n Auth Token = ".$auth_token;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
       return json_decode($result,true);
	}
	
	
	
	
	public function get_login_page($acc){
		$start_time = microtime(true);
//################## changed by  Furqan                
	  	$url = $this->pinterest_url_ssl.'login/?next=%2F';
//	  	$url = $this->pinterest_url.'login/?next=%2F';
	  	
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Referer: http://pinterest.com/';
		//$proxy = parent::getProxy();
		$cookie_name = $acc['username'];
	   
	  
	   $result = parent::get_curl_results_login($url, null, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
	   
	   //($url, $postData = null, $create_cookie = false, $username,$proxy = null,$header = null,$agent = '',$reffer = 'http://twitter.com/')
	   
		$debug_text = "\n ------ Get Login Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
       return $result;
	}
	
	public function get_pinterest_page($acc){
		$start_time = microtime(true);
	  	$url = $this->pinterest_url;
	  	
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Referer: http://pinterest.com/';
		//$proxy = parent::getProxy();
		$cookie_name = $acc['username'];
	   
	  
	   $result = parent::get_curl_results_login($url, null, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
	   
	   //($url, $postData = null, $create_cookie = false, $username,$proxy = null,$header = null,$agent = '',$reffer = 'http://twitter.com/')
	   
		$debug_text = "\n ------ Get Pinterest Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
       return $result;
	}
	
	public function is_already_loggedin($html) {
                if(strpos($html,'onClick="Logout.logout(); return false;"') !== false || strpos($html,'href="/logout/"') !== false) {
			return true;
		}
		return false;
	}
	
	public function make_login($acc,$auth_token){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$url = $this->pinterest_url_ssl."login/?next=%2Flogin%2F";
		$post_data = "email=".rawurlencode($acc['email'])."&password=".rawurlencode($acc['password'])."&next=%2F&csrfmiddlewaretoken=".$auth_token;
		
                $header_array = array();
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		//$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: https://pinterest.com/login/?next=%2F';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		//$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
		
		$result = parent::get_curl_results_login($url, $post_data, TRUE,$cookie_name,$acc['proxy'],$header_array,'','');
		
		$debug_text = "\n ------ make_login -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Post Data = ".$post_data;
		$debug_text .= "\n Auth Token = ".$auth_token;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
//		echo '<textarea cols="50" rows="50">'.htmlentities($result).'</textarea>';
		
		return $result;
	
	}
	
	public function get_auth_token($html){
		$auth_token = "";
		preg_match("/type='hidden' name='csrfmiddlewaretoken' value='(.*?)'/is", $html, $match);
		
		if(isset($match[1]) && !empty($match[1])){
			$auth_token = $match[1];
		}
		return $auth_token;
	}
	
	
	public function add_pin($acc,$pin,$auth_token){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$url = $this->pinterest_url."pin/create/";
		

		$post_data['board'] = $pin['board_id'];
		$post_data['details'] = substr($pin['description'],0,500);
		if(empty($post_data['details'])){
			$post_data['details'] = " ";
		}
		$post_data['link'] = $pin['url'];
		$post_data['img_url'] = $pin['picture_url'];
		$post_data['tags'] = "";
		$post_data['replies'] = "";
		$post_data['buyable'] = "";
		$post_data['csrfmiddlewaretoken'] = $auth_token;
		
		print_r($post_data);
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Content-Type: multipart/form-data';
		echo "\n\n";
		$result = parent::get_curl_results($url, $post_data, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
		echo "\n\n";
		
		$debug_text = "\n ------ add_pin -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Post Data = ".serialize($post_data);
		$debug_text .= "\n Auth Token = ".$auth_token;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		return json_decode($result,true);
	
	}
	
	public function do_re_pin($acc,$pin,$auth_token){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$url = $this->pinterest_url."pin/".$pin['pin_id']."/repin/";
		$detail = stripslashes($pin['pin_detail']);	
		if(empty($detail)){
			$detail = " ";
		}
		$post_data = "board=".$pin['board_id']."&id=".$pin['pin_id']."&tags=&replies=&details=".rawurlencode($detail)."&buyable=&csrfmiddlewaretoken=".$auth_token;
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/';
		$header_array[] = 'X-CSRFToken: '.$auth_token;
		$header_array[] = 'X-Requested-With: XMLHttpRequest';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		
		
		$result = parent::get_curl_results($url, $post_data, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
		
		$debug_text = "\n ------ do_re_pin -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Post Data = ".$post_data;
		$debug_text .= "\n Auth Token = ".$auth_token;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return json_decode($result,true);
	
	}
	
	public function do_like($acc,$pin,$auth_token){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		echo $url = $this->pinterest_url."pin/".$pin['pin_id']."/like/";
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/';
		$header_array[] = 'X-CSRFToken: '.$auth_token;
		$header_array[] = 'X-Requested-With: XMLHttpRequest';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		
		
		$result = parent::get_curl_results($url, null, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
		
		$debug_text = "\n ------ do_like -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Pin  = ".$pin['pin_id'];
		$debug_text .= "\n Auth Token = ".$auth_token;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return json_decode($result,true);
	
	}
	
	public function do_comment($acc,$pin,$auth_token){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$url = $this->pinterest_url."pin/".$pin['pin_id']."/comment/";
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/';
		$header_array[] = 'X-CSRFToken: '.$auth_token;
		$header_array[] = 'X-Requested-With: XMLHttpRequest';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		
		//$post_data = 'text='.urlencode($pin['template']).'&replies=&home=1&path=%2Fsearch%2F';
		
		$post_data = 'text='.urlencode($pin['template']).'&replies=&path=%2Fpin%2F'.$pin['pin_id'].'%2F';
		
		$result = parent::get_curl_results($url, $post_data, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
		
		$debug_text = "\n ------ do_comment -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Post Data  = ".$post_data;
		$debug_text .= "\n Pin  = ".$pin['pin_id'];
		$debug_text .= "\n Auth Token = ".$auth_token;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return json_decode($result,true);
	
	}
	
	public function get_repin_data($acc,$pin,$auth_token){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$url = $this->pinterest_url."pin/".$pin['pin_id']."/repindata/";
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/';
		$header_array[] = 'X-CSRFToken: '.$auth_token;
		$header_array[] = 'X-Requested-With: XMLHttpRequest';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		
		
		$result = parent::get_curl_results($url, null, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
		
		$debug_text = "\n ------ get_repin_data -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Auth Token = ".$auth_token;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return json_decode($result,true);
		
	}
	
	public function create_board($acc,$pin,$auth_token){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$board_name = str_replace(" ","+",$pin['board_name']);
		$board_cat = $pin['cat_name'];
		$url = "http://pinterest.com/board/create/";
		echo "<br>";
		echo $post_data = "name=".$board_name."&category=".$board_cat."&collaborator=me";
		echo "<br>";
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/'.$cookie_name.'/';
		$header_array[] = 'X-CSRFToken: '.$auth_token;
		$header_array[] = 'X-Requested-With: XMLHttpRequest';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		
		$result = parent::get_curl_results($url, $post_data, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
		
		$debug_text = "\n ------ create_board -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Post Data = ".$post_data;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);

		
		return json_decode($result,true);
	}
	
	public function search_user($acc,$keyword = ""){
		print_r($acc);
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		echo $url = $this->pinterest_url."search/people/?q=".parent::make_url($keyword);
		$url_array [] = $url;

		for($p = 2; $p<=10; $p++){
			$url_array [] = $url."&page=".$p;
		}
		
		$result_array = parent::req_multiurls($url_array,$acc['proxy']);
		
		$debug_text = "\n ------ search_user multi curl reponse -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".serialize($url_array);
		$debug_text .= "\n Result = ".serialize($result_array);
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return $result_array;
	
	}
	
	public function search_user_pin($acc,$keyword = ""){
		print_r($acc);
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		echo $url = $this->pinterest_url."search/?q=".parent::make_url($keyword);
		$url_array [] = $url;
		for($p = 2; $p<=10; $p++){
			$url_array [] = $url."&page=".$p;
		}
		
		$result_array = parent::req_multiurls($url_array,$acc['proxy']);
		
		$debug_text = "\n ------ search_user multi curl reponse -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".serialize($url_array);
		$debug_text .= "\n Result = ".serialize($result_array);
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return $result_array;
	
	}
        
        public function search_user_category($acc,$keyword = ""){
		print_r($acc);
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		echo $url = $this->pinterest_url."all/?category=".parent::make_url($keyword);
		$url_array [] = $url;
		for($p = 2; $p<=10; $p++){
			$url_array [] = $url."&lazy=1&page=".$p;
		}
		
		$result_array = parent::req_multiurls($url_array,$acc['proxy']);
		
		$debug_text = "\n ------ search_user_category multi curl reponse -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".serialize($url_array);
		$debug_text .= "\n Result = ".serialize($result_array);
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return $result_array;
	}
	
	public function search_user_board($acc,$keyword = ""){
		print_r($acc);
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		echo $url = $this->pinterest_url."search/boards/?q=".parent::make_url($keyword);
		$url_array [] = $url;
		for($p = 2; $p<=10; $p++){
			$url_array [] = $url."&page=".$p;
		}
		
		$result_array = parent::req_multiurls($url_array,$acc['proxy']);
		
		$debug_text = "\n ------ search_user multi curl reponse -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".serialize($url_array);
		$debug_text .= "\n Result = ".serialize($result_array);
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return $result_array;
	
	}
	
	public function follow_user($acc,$follow_user,$auth_token,$referer){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		
		$url = $this->pinterest_url.$follow_user."/follow/";
		
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: '.$referer;
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'X-CSRFToken: '.$auth_token;
		$header_array[] = 'X-Requested-With: XMLHttpRequest';

		$result = parent::get_curl_results($url,array(), FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
				
		$debug_text = "\n ------ follow_user -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Follow User = ".$follow_user;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		$follow_result = json_decode($result,true);
		
		return $result;
	
	}
	
	public function un_follow_user($acc,$follow_user,$auth_token){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		
		$url = $this->pinterest_url.$follow_user."/follow/";
		
		$post_data = "unfollow=1";
		
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/'.strtolower($cookie_name).'/following/';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'X-CSRFToken: '.$auth_token;
		$header_array[] = 'X-Requested-With: XMLHttpRequest';
	
		$result = parent::get_curl_results($url,$post_data, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
				
		$debug_text = "\n ------ un_follow_user -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Un Follow User = ".$follow_user;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		//echo "<br>";
		//echo '<textarea cols="20" rows="20">'.$result.'</textarea>'; 
		//echo "<br>";
		
		return $result;
	}
	
	public function get_public_profile_page($acc){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		
		$url = $this->pinterest_url.$acc['username']."/";
		
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
	
		$result = parent::get_curl_results($url,null, FALSE,'',$acc['proxy'],$header_array,'','');
				
		$debug_text = "\n ------ get_public_profile_page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		//echo "<br>";
		//echo '<textarea cols="20" rows="20">'.$result.'</textarea>'; 
		//echo "<br>";
		
		return $result;
	
	}
        
        public function get_all_followings($acc,$page_count){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$url = $this->pinterest_url.$acc['username']."/following/";
		$url_array [] = $url;

		for($p = 2; $p<=$page_count; $p++){
			$url_array [] = $url."?page=".$p;
		}
		$result_array = parent::req_multiurls($url_array,$acc['proxy']);
		
		$debug_text = "\n ------ search_user multi curl reponse -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".serialize($url_array);
		$debug_text .= "\n Result = ".serialize($result_array);
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return $result_array;
        }
	
        
	public function get_all_followers($acc,$page_count){
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$url = $this->pinterest_url.$acc['username']."/followers/";
		$url_array [] = $url;

		for($p = 2; $p<=$page_count; $p++){
			$url_array [] = $url."?page=".$p;
		}
		$result_array = parent::req_multiurls($url_array,$acc['proxy']);
		
		$debug_text = "\n ------ search_user multi curl reponse -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".serialize($url_array);
		$debug_text .= "\n Result = ".serialize($result_array);
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		return $result_array;
	
	}
        
        public function confirm_email($acc,$link) {
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		$url = $link;

                $header_array = array();
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
	
		$result = parent::get_curl_results($url,null, FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
				
		$debug_text = "\n ------ confirmation_page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
//		echo "<br>";
//                echo "<br><br> ================  " . $url . " ============= <br><br> ";
//		echo '<textarea cols="20" rows="20">'.htmlentities($result).'</textarea>'; 
//		echo "<br>";
                
                if (parent::get_last_url() == 'http://pinterest.com/') {
                    return true;
                }
                return false;
	}
        
        public function resend_confirmEmail($acc,$auth_token) {
		$start_time = microtime(true);
		$cookie_name = $acc['username'];
		
		$url = $this->pinterest_url."verify_email/resend/";
		
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: http://pinterest.com/';
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'X-CSRFToken: '.$auth_token;
		$header_array[] = 'X-Requested-With: XMLHttpRequest';

		$result = parent::get_curl_results($url,array(), FALSE,$cookie_name,$acc['proxy'],$header_array,'','');
				
		$debug_text = "\n ------ resend confirmation email -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
                
		$follow_result = json_decode($result,true);
                
                if($follow_result['status'] == 'success'){
                    return true;
                }
		return false;
	}
	
	public function update_profile($acc,$auth_token){
		
		$start_time = microtime(true);
		$url = $this->pinterest_url_ssl.'settings/';
		
		parent::get_curl_results($url,null, FALSE,strtolower($acc['username']),$acc['proxy'],null,'','');
		
		$picture = SITE_PATH.$acc['image_path'];
		
		$post_data = array();
		$post_data['email'] = $acc['email'];
		$post_data['first_name'] = $acc['first_name'];
		$post_data['last_name'] = $acc['last_name'];
		$post_data['username'] = strtolower($acc['username']);
		$post_data['gender'] = $acc['gender'];
		$post_data['about'] = $acc['about'];
		$post_data['location'] = $acc['location'];
		$post_data['website'] = $acc['website'];
		$post_data['csrfmiddlewaretoken'] = $auth_token;
		
		
		if(file_exists($picture) && ($acc['update_image']>-1 && $acc['update_image'] < MAX_FAILURE)){
			$post_data['img'] =  '@'.$picture;
		}
		else{
			$post_data['img'] =  '';
		}
		
		
		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
		$header_array[] = 'Host: pinterest.com';
		$header_array[] = 'Connection: keep-alive';
		$header_array[] = 'Referer: '.$url;
		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		$header_array[] = 'Content-Type: multipart/form-data;';
		//array("Content-type: multipart/form-data")
		
		//print_r($post_data);
		
		$result = parent::get_curl_results($url,$post_data, TRUE,strtolower($acc['username']),$acc['proxy'],array("Content-type: multipart/form-data"),'','');
		
		$debug_text = "\n ------ update_profile -------\n Process Time =  ".((float)(microtime(true) - $start_time));
		$debug_text .= "\n URL = ".$url;
		$debug_text .= "\n Post Data = ".serialize($post_data);
		$debug_text .= "\n Result = ".$result;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
		//get_curl_results($url, $postData = null, $create_cookie = false, $username,$proxy = null,$header = null,$agent = '',$reffer = 'https://pinterest.com/')
		
		//echo "<br>";
		//echo '<textarea cols="50" rows="50">'.$result.'</textarea>'; 
		//echo "<br>";
		
		return $result;
	}
	
	public function get_capcha_image($acc,$viewState) {
		$capchaImageStartTime = microtime(true);
		//$url = "https://api-secure.recaptcha.net/challenge?k=$viewState";
		//$result = parent::get_curl_results($url);
		//preg_match_all("/challenge : '(.*?)',/si",$result,$match);
		//$googleMatch = ($match[1][0]);
		//$this->challengeCode = $googleMatch;
		$googleURL = "http://www.google.com/recaptcha/api/image?c=$viewState";
		//$result = parent::get_curl_results($googleURL);
		
		$debug_text = "\n ------ get_capcha_image -------\n Process Time =  ".((float)(microtime(true) - $capchaImageStartTime));
		$debug_text .= "\n URL = ".$googleURL;
		$debug_text .= "\n View State = ".$viewState;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
		
        return $googleURL;
    }
	public function get_decapcha($acc,$viewstate){
		$capchaImageStartTime = microtime(true);
		$imageUrl = $this->get_capcha_image($acc,$viewstate);
		$this->globalImageURL = $imageUrl;
		$ccp = new ccproto();
		$ccp->init();
		if( $ccp->login( DE_HOST, DE_PORT, DE_USERNAME, DE_PASSWORD ) < 0 ) {
			$error = 'Decapcher login failed.';
		} 
		else{
			$pictureText = '';
			$major_id	= 0;
			$minor_id	= 0;
			for( $i = 0; $i < 3; $i++ ) {
				echo ' <br> Image URL = '.$imageUrl;
				echo "<br>";
				$pict = file_get_contents( $imageUrl );
				//$pict = $imageUrl;
				$text = '';
				print( "sending a picture..." );
		
				$pict_to	= ptoDEFAULT;
				$pict_type	= ptUNSPECIFIED;
				
				$res = $ccp->picture2( $pict, $pict_to, $pict_type, $text, $major_id, $minor_id );
				switch( $res ) {
					// most common return codes
					case ccERR_OK:
						$pictureText = $text;
						$error = "got text for id=".$major_id."/".$minor_id.", type=".$pict_type.", to=".$pict_to.", text='".$text."'";
						break;
					case ccERR_BALANCE:
						$error = "not enough funds to process a picture, balance is depleted";
						break;
					case ccERR_TIMEOUT:
						$error = "picture has been timed out on server (payment not taken)";
						break;
					case ccERR_OVERLOAD:
						$error = "temporarily server-side error \n server's overloaded, wait a little before sending a new picture";
						break;
				
					// local errors
					case ccERR_STATUS:
						$error = "local error. \n either ccproto_init() or ccproto_login() has not been successfully called prior to ccproto_picture() \n need ccproto_init() and ccproto_login() to be called";
						break;
				
					// network errors
					case ccERR_NET_ERROR:
						$error = "network troubles, better to call ccproto_login() again";
						break;
				
					// server-side errors
					case ccERR_TEXT_SIZE:
						$error = "size of the text returned is too big";
						break;
					case ccERR_GENERAL:
						$error = "server-side error, better to call ccproto_login() again";
						break;
					case ccERR_UNKNOWN:
						$error = "unknown error, better to call ccproto_login() again";
						break;
				
					default:
						// any other known errors?
						break;
				}
				
				if(!empty($pictureText)){
					break;
				}
				
				if(!empty($error)){
					$error .= " | Create Account | Capcha does not match \n";
					echo "\n Capcha Error = ".$error." \n ";
				}
				// process a picture and if it is badly recognized 
				// call picture_bad2() to name it as error. 
				// pictures named bad are not charged
		
				//$ccp->picture_bad2( $major_id, $minor_id );
			}//end of for loop
			$ccp->close();
			return  $pictureText;
		}
		
		$debug_text = "\n ------ get_decapcha -------\n Process Time =  ".((float)(microtime(true) - $capchaImageStartTime));
		$debug_text .= "\n Image URL = ".$imageUrl;
		$debug_text .= "\n View State = ".$viewstate;
		$debug_text .= "\n Error = ".$error;
		parent::saveDebugContent($acc['username'],$debug_text);
		unset($debug_text);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////
	
	
//    public function getLoginPage($proxy,$username) {
//      $start_time = microtime(true);
//	  $url = $this->twitterURL.'#!/login';
//	  	
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
//		$header_array[] = 'Connection: keep-alive';
//		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		//$header_array[] = 'Referer: https://twitter.com/';
//       //$proxy = parent::getProxy();
//	   parent::setGlobalProxy($proxy);
//	   parent::setCookieName($username);
//	   
//	   $result = parent::get_curl_results($url, null, TRUE,'',$header_array);
//	   $debug_text = "\n ------ Get Login Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
//	   $debug_text .= "\n Result = ".$result;
//	   parent::saveDebugContent($username,$debug_text);
//       return $result;
//    }
//	 public function setParams($proxy,$username,$backupProxy,$maxProxyTries) {
//	   parent::setGlobalProxy($proxy);
//	   parent::setCookieName($username);
//	   parent::setGlobalProxyArray($backupProxy);
//	   parent::setMaxProxyTry($maxProxyTries);
//    }
//	
//	public function getPublicProfilePage($username,$proxy){
//		 $url = $this->twitterURL.$username.'/';
//		 $result = parent::get_curl_results($url, null, FALSE,'',null,0,$proxy);
//		 return $result;		 
//	}
//	
//	public function getPublicProfilePage_json($username){
//		 $url = $this->twitterURL.'users/show_for_profile.json?screen_name='.$username;
//		 
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		//$cookie_name = "public_profile_".$username.".txt";				 
//		$result = parent::get_curl_results($url, null, FALSE,'',$header_array,0,parent::get_user_proxy());
//		$result = json_decode($result);
//		return $result;		 
//	}
//	
//	public function getSignupPage($proxy,$username) {
//       $start_time = microtime(true);
//	   
//	   $url = $this->twitterURL.'signup';
//	   
//	   	$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		$header_array[] = 'Referer: https://twitter.com/';  
//	   parent::setGlobalProxy($proxy);
//	   parent::setCookieName($username);
//	   $result = parent::get_curl_results($url,null,TRUE,'',$header_array);
//	   $debug_text = "\n ------ Get Signup Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
//	   $debug_text .= "\n Result = ".$result;
//	   parent::saveDebugContent($username,$debug_text);
//       return $result;
//    }
//	
//	public function confirmEmail($param,$proxy,$username) {
//		$start_time = microtime(true);
//       	$url = 'http://twitter.com/account/confirm_email/'.$param;
//	   	//$url = $this->twitterURL.'#!/login?redirect_after_login=%2Faccount%2Fconfirm_email%2F';
//       	//$proxy = parent::getProxy();	  
//	   	parent::setGlobalProxy($proxy);
//	   	parent::setCookieName($username);
//	   	//parent::setGlobalProxyArray($backupProxy);
//	   	//parent::setMaxProxyTry($maxProxyTries);
//	   	$result = parent::get_curl_results($url, null, FALSE);
//	   
//	   	$debug_text = "\n ------ Confirm Email -------\n Process Time =  ".((float)(microtime(true) - $start_time));
//		$debug_text .= "\n Result = ".$result;		
//		parent::saveDebugContent($username,$debug_text);
//	   
//       	return $result;
//    }
//
//    public function autheticate($userName, $password,$proxy = '',$cookie_name = '') {
//       	if($cookie_name){
//			parent::setCookieName(strtolower($cookie_name));
//		}
//	    $authStartTime = microtime(true);
//		$result = '';
//		// old code
//		/*if(!empty($authenticity_token)){			
//			$url = $this->twitterURL.'sessions';
//			echo $postData = 'authenticity_token=' . $authenticity_token . '&authenticity_token=' . $authenticity_token . '&return_to_ssl=&redirect_after_login=&session%5Busername_or_email%5D=' . urlencode($userName) . '&session%5Bpassword%5D=' . urlencode($password) . '&commit=Sign+In';
//			$result = parent::get_curl_results($url, $postData);
//		}*/
//		
//		// new code
//		
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
//		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//			//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			//$header_array[] = 'X-PHX: true';
//		$header_array[] = 'Referer: https://twitter.com/';
//		
//		
//		$url = $this->twitterURL.'sessions?phx=1';
//		$postData = 'session%5Busername_or_email%5D='.urlencode($userName).'&session%5Bpassword%5D='.urlencode($password).'&scribe_log=%5B%5D&redirect_after_login=&remember_me=1';
//		parent::setGlobalProxy($proxy);
//	   	parent::setCookieName($userName);
//		//$postData = 'session%5Busername_or_email%5D='.urlencode($userName).'&session%5Bpassword%5D='.urlencode($password).'&remember_me=1&return_to_ssl=true';
//		$result = parent::get_curl_results($url, $postData,true,'',$header_array,0,$proxy);	
//		//echo '<textarea cols="100" rows="100">'.$result.'</textarea>'; 
//		$debug_text = "\n ------ Authenticate -------\n Process Time =  ".((float)(microtime(true) - $authStartTime));
//	    $debug_text .= "\n Post Data = ".$postData;
//		$debug_text .= "\n Result = ".$result;
//		parent::saveDebugContent($userName,$debug_text);
//		
//		echo '<textarea cols="100" rows="200">'.$result.'</textarea>';
//		
//        return $result;
//    }
//	public function autheticateToConfirmAccount($redirect_after_login,$userName, $password,$twitterLoginResult) {
//		$authStartTime = microtime(true);
//		$redirect_after_login = '/account/confirm_email/'.$redirect_after_login;
//		$authenticity_token = '';
//		$authenticity_token = $this->getGolbalAuthToken();
//		//$authenticity_token = $this->getAuthencityToken($twitterLoginResult);
//		preg_match('/input name="authenticity_token" value="(.*?)" type="hidden"/', $twitterLoginResult, $match);
//		$result = '';
//		if(!empty($authenticity_token)){			
//			$url = $this->twitterURL.'sessions?phx=1';
//			//$postData = 'authenticity_token='.$authenticity_token.'&authenticity_token='.$authenticity_token.'&return_to_ssl=&redirect_after_login='.urlencode($redirect_after_login).'&session%5Busername_or_email%5D='.urlencode($userName).'&session%5Bpassword%5D='.urlencode($password).'&commit=Sign+In';
//			$postData = 'session%5Busername_or_email%5D='.$userName.'&session%5Bpassword%5D='.$password.'&redirect_after_login='.urlencode($redirect_after_login);
//			$result = parent::get_curl_results($url, $postData);
//			
//		}
//		parent::saveDebugContent("Authenticate to Confirm Account : ".$userName." | Time :  ".((float)(microtime(true) - $authStartTime)));
//        return $result;
//    }
//	public function authenticateWithCapcha($userName, $password,$proxy,$viewstate = '',$cookie_name = '') {
//		$postData = '';
//		$result = '';
//		$pictureText = '';
//		if($cookie_name){
//			parent::setCookieName(strtolower($cookie_name));
//		}
//		$start_time = microtime(true);
//		$view_state = $this->getViewState();
//		if($view_state != ''){
//			echo "\n";
//			echo "Picture Text = ".$pictureText = $this->decapcha($view_state);
//			echo "\n";
//			$imageUrl = $this->globalImageURL;
//			//$imageUrl = $this->getCapchaImage($viewstate);
//			$challengeField = explode('=',$imageUrl);
//			$authenticity_token = '';
//			
//			
//			$url = $this->twitterURL.'sessions?phx=1';
//			$postData = 'session%5Busername_or_email%5D='.urlencode($userName).'&session%5Bpassword%5D='.urlencode($password).'&recaptcha_challenge_field='.$view_state.'&recaptcha_response_field='.urlencode($pictureText).'&remember_me=1&return_to_ssl=true';
//			$result = parent::get_curl_results($url, $postData,FALSE,'',null,0,$proxy);
//		}
//		
//		// old code
//		//$authenticity_token = $this->getAuthencityToken($twitterLoginResult);
//		//$this->globalAuthencityToken = $authenticity_token;
//		//preg_match('/input name="authenticity_token" value="(.*?)" type="hidden"/', $twitterLoginResult, $match);		
//		//if(!empty($authenticity_token)){			
//			//$url = $this->twitterURL.'sessions';
//			//echo $postData = 'authenticity_token='.$authenticity_token.'&authenticity_token='.$authenticity_token.'&return_to_ssl=&redirect_after_login=&session%5Busername_or_email%5D='.urlencode($userName).'&session%5Bpassword%5D='.urlencode($password).'&recaptcha_challenge_field='.$challengeField[1].'&recaptcha_response_field='.urlencode($pictureText).'&commit=Sign+In';			
//			//$header_array[] = 'Host: twitter.com';
//			//$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//			//$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//			//$header_array[] = 'Referer: https://twitter.com/sessions';
//			//$result = parent::get_curl_results($url, $postData,false,'https://twitter.com/sessions',$header_array,1);
//		//}
//		
//		$debug_text = "\n ------ Auth With Capcha -------\n Process Time =  ".((float)(microtime(true) - $start_time));
//	    $debug_text .= "\n Decapcha Image Text = ".$pictureText;
//		$debug_text .= "\n Post Data = ".$postData;
//		$debug_text .= "\n Result = ".$result;		
//		parent::saveDebugContent($userName,$debug_text);
//		
//		parent::saveCapchaCalls(" Username  = $userName, Password = $password, Proxy = $proxy, View State = $view_state, Picture Text = $pictureText \n\n");
//		
//        return $result;
//    }
//	public function getCapchaImage($viewState) {
//		$capchaImageStartTime = microtime(true);
//		//$url = "https://api-secure.recaptcha.net/challenge?k=$viewState";
//		//$result = parent::get_curl_results($url);
//		//preg_match_all("/challenge : '(.*?)',/si",$result,$match);
//		//$googleMatch = ($match[1][0]);
//		//$this->challengeCode = $googleMatch;
//		$googleURL = "https://www.google.com/recaptcha/api/image?c=$viewState";
//		//$result = parent::get_curl_results($googleURL);
//		parent::saveDebugContent("google_capcha","Get Capcha Image: ".$viewState." | Time :  ".((float)(microtime(true) - $capchaImageStartTime)));
//        return $googleURL;
//    }
//	
//	public function getViewState(){
//		$url = "https://www.google.com/recaptcha/api/challenge?k=6LfbTAAAAAAAAE0hk8Vnfd1THHnn9lJuow6fgulO&ajax=1&cachestop=0.5370695004979907&lang=en";
//		$result = parent::get_curl_results($url);
//		preg_match("/challenge : '(.*?)'/", $result, $match);
//		if(isset($match[1])){
//			return $match[1];
//		}
//		return '';
//	}
//	
//	public function getAuthencityToken($twitterResult){
//		preg_match('/input name="authenticity_token" value="(.*?)" type="hidden"/', $twitterResult, $match);
//		
//		$authenticity_token = '';
//		if(isset($match[1])){
//			$authenticity_token = $match[1];
//		}
//		return $authenticity_token;
//	}
//	
//	public function getLoggedAuthencityToken($twitterResult){
//		preg_match("/input name='authenticity_token' value='(.*?)' type='hidden'/", $twitterResult, $match);		
//		$authenticity_token = '';
//		if(isset($match[1])){
//			$authenticity_token = $match[1];
//		}
//		else{
//			preg_match("/input type='hidden' value='(.*?)' name='authenticity_token'/", $twitterResult, $match);
//			if(isset($match[1])){
//				$authenticity_token = $match[1];
//			}
//		}
//		return $authenticity_token;
//	}
//	
//	public function createAccount($authenticity_token = '', $recaptcha_challenge_field = '',$recaptcha_response_field = '',$email,$name = '',$screen_name,$user_password ){
//		$start_time = microtime(true);
//		$email = trim(strtolower($email));
//		if(!empty($name)){
//			$name = trim($name);
//		}else{
//			$name = trim(strtolower($screen_name));
//		}
//		$screen_name = trim($screen_name);
//		$user_password = trim($user_password);		
//				
//		$postData = 'authenticity_token='.$authenticity_token.'&user%5Bname%5D='.$name.'&user%5Bemail%5D='.urlencode($email).'&user%5Buser_password%5D='.$user_password.'&user%5Bscreen_name%5D='.$screen_name.'&user%5Bremember_me_on_signup%5D=1&user%5Bremember_me_on_signup%5D=&context=&user%5Bdiscoverable_by_email%5D=1&user%5Bsend_email_newsletter%5D=1';
//		
//// new design params		//authenticity_token=&user%5Bname%5D=BestfateTurn&user%5Bemail%5D=sureshiftwings%40znajsci.com&user%5Buser_password%5D=m8cjLl5j&user%5Bscreen_name%5D=BestfateTurn&context=&user%5Bdiscoverable_by_email%5D=1&user%5Bsend_email_newsletter%5D=1
//		
//		$header_array[] = 'Host twitter.com';
//		$header_array[] = 'User-Agent Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//		$header_array[] = 'Accept text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		$header_array[] = 'Content-Type application/x-www-form-urlencoded';
//		$header_array[] = 'Referer https://twitter.com/signup';
//		
//		$url = $this->twitterURL.'account/create';
//		$result = parent::get_curl_results($url,$postData);
//		$debug_text = "\n ------ Create Account Request Page -------\n Process Time =  ".((float)(microtime(true) - $start_time));
//		$debug_text .= "\n Post Data = ".$postData;
//	    $debug_text .= "\n Result = ".$result;
//	    parent::saveDebugContent($screen_name,$debug_text);
//		return $result;
//	}
//	
//	public function resendConfirmEmail($authenticity_token,$username = ''){
//		$start_time =  microtime(true);
//		$url = $this->twitterURL.'account/resend_confirmation_email';
//		//$postData = 'authenticity_token='.$authenticity_token;
//		$postData = null;
//		
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//		$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//		$header_array[] = 'Referer: https://twitter.com/settings/account';
//		
//		
//		$result = parent::get_curl_results($url,null,false,null,$header_array);
//		
//		$debug_text = "\n ------ Resend Confirm Email -------\n Process Time =  ".((float)(microtime(true) - $start_time));
//		//$debug_text .= "\n Post Data = ".$postData;
//	    $debug_text .= "\n Result = ".serialize($result);
//	    parent::saveDebugContent($username,$debug_text);
//		
//		return json_decode($result);
//	}
//	
//	
//
//    public function getFollowing($userId, $max = 10,$fileContent = '',$username = '') {
//       $getFolloweingStartTime = microtime(true);
//	    $finalArray = array();
//        $nextCusrsor = -1;
//        $count = 1;
//        while ($nextCusrsor) {
//			$start_time = microtime(true);
//			$url = $this->twitterAPIURL.'statuses/friends.json?cursor=' . $nextCusrsor . '&user_id=' . $userId;
//			
//			$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
//			$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//			//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding: gzip,deflate';
//			//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			//$header_array[] = 'Keep-Alive: 115';
//			//$header_array[] = 'Connection: keep-alive';
//			//$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//			//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			//$header_array[] = 'X-PHX: true';
//			//$header_array[] = 'Referer: http://api.twitter.com/receiver.html';
//			//$header_array[] = 'Pragma: no-cache';
//			//$header_array[] = 'Cache-Control: no-cache';
//			
//            $result = json_decode(parent::get_curl_results($url,null,false,null,$header_array));
//			
//			$debug_text = "\n ------ Get All Following -------\n Process Time =  ".((float)(microtime(true) - $start_time));
//			$debug_text .= "\n URL = ".$url;
//			$debug_text .= "\n Result = ".serialize($result);
//			parent::saveDebugContent($username,$debug_text);
//			
//						
//			if(isset($result->users)){
//				foreach ($result->users as $res) {
//					$resultArray = array();
//					if(!empty($fileContent)){
//						$strPosition = strpos(strtolower($fileContent),strtolower($res->screen_name.':'));
//						if($strPosition === false && $res->screen_name!='' && intval($res->default_profile_image) !=1){
//							$resultArray['id'] = $res->id;
//							$resultArray['screen_name'] = $res->screen_name;
//							$resultArray['following'] = $res->following;
//							$resultArray['followed_by'] = $res->follow_request_sent;
//							$resultArray['profile_image_url'] = $res->profile_image_url;
//							$resultArray['default_profile_image'] = $res->default_profile_image;					
//							$finalArray[] = $resultArray;
//							$count++;						
//						}
//					}else{
//						if(intval($res->default_profile_image) !=1){
//							$resultArray['id'] = $res->id;
//							$resultArray['screen_name'] = $res->screen_name;
//							$resultArray['following'] = $res->following;
//							$resultArray['followed_by'] = $res->follow_request_sent;
//							$resultArray['profile_image_url'] = $res->profile_image_url;
//							$resultArray['default_profile_image'] = $res->default_profile_image;
//							
//							$finalArray[] = $resultArray;
//							$count++;
//						}
//						
//					}
//					
//					if ($count > $max) {
//						$result->next_cursor_str = 0;
//						break;
//					}
//					
//					/*if(!empty($fileContent)){
//						$strPosition = strpos($fileContent,$res->screen_name.':');
//						if($strPosition === false && $res->screen_name!='' ){
//							$count++;
//						}
//					}else{
//						$count++;
//					}*/
//				}
//				
//				$nextCusrsor = 0;
//				
//				if ($result->next_cursor_str) {
//					$nextCusrsor = $result->next_cursor_str;
//				}
//			}
//        }
//		
//		$debug_text = "\n ------ Filtered Following Result -------\n";
//		$debug_text .= "\n Result = ".serialize($finalArray);
//		parent::saveDebugContent($username,$debug_text);
//        return $finalArray;
//    }
//	
//	public function getFollowers($userId, $max = 10,$fileContent = '',$username = '') {
//		$getFollowersStartTime = microtime(true);
//        $finalArray = array();
//        $nextCusrsor = -1;
//        $count = 1;
//        while ($nextCusrsor) {
//           	$url = $this->twitterAPIURL.'statuses/followers.json?cursor=' . $nextCusrsor . '&user_id=' . $userId;
//			
//			$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
//			$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//			//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding: gzip,deflate';
//			//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			//$header_array[] = 'Keep-Alive: 115';
//			//$header_array[] = 'Connection: keep-alive';
//			//$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//			//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			//$header_array[] = 'X-PHX: true';
//			//$header_array[] = 'Referer: http://api.twitter.com/receiver.html';
//			//$header_array[] = 'Pragma: no-cache';
//			//$header_array[] = 'Cache-Control: no-cache';
//            $result = json_decode(parent::get_curl_results($url,null,false,null,$header_array));			
//			$debug_text = "\n ------ Get User Followers REsult -------\n";
//			$debug_text .= "\n Result = ".serialize($result);
//			parent::saveDebugContent($username,$debug_text);
//			if(isset($result->users)){
//				foreach ($result->users as $res) {
//					/*$resultArray['description'] = $res->description;
//					$resultArray['id_str_status'] = $res->id_str;
//					if(isset($res->status->text)){
//						$resultArray['text_status'] = $res->status->text;
//					}
//					$resultArray['location'] = $res->location;
//					$resultArray['profile_image_url'] = $res->profile_image_url;*/
//					$resultArray = array();
//					if(!empty($fileContent)){
//						$strPosition = strpos(strtolower($fileContent),strtolower($res->screen_name.':'));
//						if($strPosition === false && $res->screen_name!='' && intval($res->default_profile_image) !=1){
//							$resultArray['id'] = $res->id;
//							$resultArray['screen_name'] = $res->screen_name;
//							$resultArray['profile_image_url'] = $res->profile_image_url;
//							$resultArray['default_profile_image'] = $res->default_profile_image;
//							$finalArray[] = $resultArray;
//							$count++;
//						}
//					}else{
//						if(intval($res->default_profile_image) !=1){
//							$resultArray['id'] = $res->id;
//							$resultArray['screen_name'] = $res->screen_name;
//							$resultArray['profile_image_url'] = $res->profile_image_url;
//							$resultArray['default_profile_image'] = $res->default_profile_image;
//							$finalArray[] = $resultArray;
//							$count++;
//						}
//					}
//					
//					
//					if ($count > $max) {
//						$result->next_cursor_str = 0;
//						break;
//					}
//					/*if(!empty($fileContent)){
//						$strPosition = strpos($fileContent,$res->screen_name.':');
//						if($strPosition === false && $res->screen_name!='' ){
//							$count++;
//						}
//					}else{
//						$count++;
//					}*/
//					
//				}
//				$nextCusrsor = 0;
//				if (isset($result->next_cursor_str) && $result->next_cursor_str>0) {
//					$nextCusrsor = $result->next_cursor_str;
//				}
//			}else{
//				break;
//			}
//        }
//		$debug_text = "\n ------ Get User Followers Filtered Result -------\n";
//		$debug_text .= "\n Result = ".serialize($finalArray);
//		parent::saveDebugContent($username,$debug_text);
//        return $finalArray;
//    }
//	
//	
//	
//	
//	public function getAllFollowers($userId,$username = '') {
//        $finalArray = array();
//        $nextCusrsor = -1;
//        $count = 1;
//        while ($nextCusrsor) {
//           $url = $this->twitterAPIURL.'statuses/followers.json?cursor=' . $nextCusrsor . '&user_id=' . $userId;
//			
//			$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
//			$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//			//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding: gzip,deflate';
//			//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			//$header_array[] = 'Keep-Alive: 115';
//			//$header_array[] = 'Connection: keep-alive';
//			//$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//			//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			//$header_array[] = 'X-PHX: true';
//			//$header_array[] = 'Referer: http://api.twitter.com/receiver.html';
//			//$header_array[] = 'Pragma: no-cache';
//			//$header_array[] = 'Cache-Control: no-cache';
//			$rawResult = parent::get_curl_results($url,null,false,null,$header_array);           
//			$result = json_decode($rawResult);
//			$debug_text = "\n ------ Get All Followers REsult -------\n";
//			$debug_text .= "\n Result = ".serialize($result);
//			parent::saveDebugContent($username,$debug_text);
//			if(isset($result->users)){
//				foreach ($result->users as $res) {
//					$resultArray = array();
//					$resultArray['id'] = $res->id;
//					$resultArray['screen_name'] = $res->screen_name;
//					$resultArray['profile_image_url'] = $res->profile_image_url;
//					$resultArray['default_profile_image'] = $res->default_profile_image;
//					$finalArray[] = $resultArray;
//				}
//				if (isset($result->next_cursor_str) && $result->next_cursor_str>0) {
//					$nextCusrsor = $result->next_cursor_str;
//				} else {
//					$nextCusrsor = 0;
//				}
//			}else{
//				break;
//			}
//        }	
//		
//		$debug_text = "\n ------ Get All Followers Filtered Result -------\n";
//		$debug_text .= "\n Result = ".serialize($finalArray);
//		parent::saveDebugContent($username,$debug_text);
//		
//		unset($result);
//		
//        return $finalArray;
//    }
//	
//	 public function getAllFollowing($userId,$username = '') {
//        $finalArray = array();
//        $nextCusrsor = -1;
//        $count = 1;
//        while ($nextCusrsor) {
//			$url = $this->twitterAPIURL.'statuses/friends.json?cursor=' . $nextCusrsor . '&user_id=' . $userId;
//			
//			$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
//			$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//			//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding: gzip,deflate';
//			//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			//$header_array[] = 'Keep-Alive: 115';
//			//$header_array[] = 'Connection: keep-alive';
//			//$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//			//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			//$header_array[] = 'X-PHX: true';
//			//$header_array[] = 'Referer: https://api.twitter.com/p_receiver.html';
//			//$header_array[] = 'Pragma: no-cache';
//			//$header_array[] = 'Cache-Control: no-cache';		
//				
//			
//            $result = json_decode(parent::get_curl_results($url,null,false,null,$header_array));
//			
//			$debug_text = "\n ------ Get All Following REsult -------\n";
//			$debug_text .= "\n Result = ".serialize($result);
//			parent::saveDebugContent($username,$debug_text);
//			
//			if(isset($result->users)){
//				foreach ($result->users as $res) {
//					
//				   /* $resultArray['description'] = $res->description;
//					$resultArray['id_str_status'] = $res->status->id_str;
//					$resultArray['text_status'] = $res->status->text;
//					$resultArray['location'] = $res->location;
//					$resultArray['profile_image_url'] = $res->profile_image_url;*/
//					
//					$resultArray = array();
//					
//					$resultArray['id'] = $res->id;
//					$resultArray['screen_name'] = $res->screen_name;
//					$resultArray['profile_image_url'] = $res->profile_image_url;
//					$resultArray['default_profile_image'] = $res->default_profile_image;
//					//$resultArray['profile_image_url'] = $res->profile_image_url;					
//					$finalArray[] = $resultArray;
//				}
//								
//				if ($result->next_cursor_str) {
//					$nextCusrsor = $result->next_cursor_str;
//				} else {
//					$nextCusrsor = 0;
//				}
//			}else{
//				break;
//			}
//        }
//		
//		$debug_text = "\n ------ Get All Following Filtered Result -------\n";
//		$debug_text .= "\n Result = ".serialize($finalArray);
//		parent::saveDebugContent($username,$debug_text);
//		
//        return $finalArray;
//    }
//
//    public function search($keyword,$maxRecords,$username = '',$check_file = false) {
//		$start_time = microtime(true);
//		//$url = $this->twitterSearchURL.'search.json?q=' . $keyword . '&rpp=' . $maxRecords;
//		//$maxRecords = 100;
//		$keyword = str_replace(" ","+",stripslashes($keyword));
//		//$url = "http://twitter.com/phoenix_search.phoenix?q=".$keyword."&include_entities=1&include_available_features=1&contributor_details=true&mode=relevanc&rpp=".$maxRecords;
//		$url = "http://twitter.com/phoenix_search.phoenix?q=".$keyword."&include_entities=1&include_available_features=1&contributor_details=true&rpp=200";
//		//$url = 'http://search.twitter.com/search.json?q=' . $keyword . '&rpp=' . $maxRecords.'&show_user=true';
//		
//		if(DEBUG_MODE){
//			echo "<pre>";
//			
//		}
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 AskTbUT2V5/3.9.1.14019 Firefox/3.6.15';
//		$header_array[] = 'Accept: application/json, text/javascript, */*';
//		//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//		//$header_array[] = 'Accept-Encoding: gzip,deflate';
//		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//		//$header_array[] = 'Keep-Alive: 115';
//		//$header_array[] = 'Connection: keep-alive';
//		$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//		//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//		//$header_array[] = 'X-PHX: true';
//		$header_array[] = 'Referer: http://twitter.com/';
//		//$header_array[] = 'Pragma: no-cache';
//		//$header_array[] = 'Cache-Control: no-cache';
//		$result = json_decode(parent::get_curl_results($url,null,false,null,$header_array));	
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n".$url."\n";
//			//print_r($result);
//		}
//        //$result = json_decode(parent::get_curl_results($url));
//		$finalArray = array();
//		$debug_text = "\n ------ Search REsult -------\n";
//		$debug_text .= "\n URL = ".$url;
//		$debug_text .= "\n Keyword = ".$keyword;
//		$debug_text .= "\n Result = ".serialize($result);
//		parent::saveDebugContent($username,$debug_text);
//		$file_content = array();
//		if($check_file){
//			$file_name = SITE_PATH.'following/'.strtolower($username).'-follow.txt';
//			if(file_exists($file_name)){
//				$file_content = explode(";",file_get_contents($file_name));
//			}
//		}
//		$count = 0;
//		if(isset($result->statuses)){
//			foreach ($result->statuses as $res) {
//                if(intval($res->user->default_profile_image) !=1 && !parent::checkDefaultImage($res->user->profile_image_url,$username,$res->user->screen_name)){
//					if($check_file){
//						if(!parent::user_exists_file(strtolower($res->user->screen_name),$username,$file_content)){
//							$resultArray['id'] = $res->user->id;
//							$resultArray['screen_name'] = strtolower($res->user->screen_name);
//							$resultArray['profile_image_url'] = $res->user->profile_image_url;
//							$resultArray['default_profile_image'] = $res->user->default_profile_image;
//							$finalArray[$res->user->id] = $resultArray;
//							$count++;
//						}
//
//					}
//					else{
//						$resultArray['id'] = $res->user->id;
//						$resultArray['screen_name'] = strtolower($res->user->screen_name);
//						$resultArray['profile_image_url'] = $res->user->profile_image_url;
//						$resultArray['default_profile_image'] = $res->user->default_profile_image;
//						$finalArray[$res->user->id] = $resultArray;
//						$count++;
//					}
//				}
//				if($count>$maxRecords){
//					break;
//				}
//            }
//		}
//		$debug_text = "\n ------ Filtered Search Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//		$debug_text .= "\n Result = ".serialize($finalArray);
//		parent::saveDebugContent($username,$debug_text);
//	    return $finalArray;
//    }
//	
//	public function get_public_profile($keyword,$maxRecords,$username = '',$check_file = false) {
//		$start_time = microtime(true);
//		//$url = $this->twitterSearchURL.'search.json?q=' . $keyword . '&rpp=' . $maxRecords;
//		//$maxRecords = 100;
//		$keyword = str_replace(" ","+",stripslashes($keyword));
//		//$url = "http://twitter.com/phoenix_search.phoenix?q=".$keyword."&include_entities=1&include_available_features=1&contributor_details=true&mode=relevanc&rpp=".$maxRecords;
//		echo $url = "https://twitter.com/phoenix_search.phoenix?q=".$keyword."&include_entities=1&include_available_features=1&contributor_details=true&rpp=200";
//		//$url = 'http://search.twitter.com/search.json?q=' . $keyword . '&rpp=' . $maxRecords.'&show_user=true';
//		
//		if(DEBUG_MODE){
//			echo "<pre>";
//			
//		}
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: 	Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';
//		$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//		//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//		//$header_array[] = 'Accept-Encoding: gzip,deflate';
//		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//		//$header_array[] = 'Keep-Alive: 115';
//		//$header_array[] = 'Connection: keep-alive';
//		//$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//		//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//		//$header_array[] = 'X-PHX: true';
//		//$header_array[] = 'Referer: https://twitter.com/';
//		//$header_array[] = 'Pragma: no-cache';
//		//$header_array[] = 'Cache-Control: no-cache';
//		//parent::setCookieName($keyword."_search_cookie");
//		$temp_reponse = $this->get_curl_results($url,null,false,null,$header_array,0,parent::get_user_proxy());
//		$result = json_decode($temp_reponse);
//		echo "\n".$url."\n";
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n".$url."\n";
//			//print_r($result);
//		}
//        //$result = json_decode(parent::get_curl_results($url));
//		$finalArray = array();
//		$debug_text = "\n ------ Search REsult -------\n";
//		$debug_text .= "\n URL = ".$url;
//		$debug_text .= "\n Keyword = ".$keyword;
//		$debug_text .= "\n Result = ".serialize($result);
//		parent::saveDebugContent($username,$debug_text);
//		$file_content = array();
//		if($check_file){
//			$file_name = SITE_PATH.'following/'.strtolower($username).'-follow.txt';
//			if(file_exists($file_name)){
//				$file_content = explode(";",file_get_contents($file_name));
//			}
//		}
//		
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n User count by Search = ".count($result->statuses)."\n";
//			//print_r($result);
//		}
//		
//		$count = 0;
//		if(isset($result->statuses)){
//			echo "\n\n Some REcords\n\n";
//			echo "\n\n Total REcords = ".count($result->statuses)."\n\n";
//			foreach ($result->statuses as $res) {
//                if(intval($res->user->default_profile_image) !=1 && !parent::checkDefaultImage($res->user->profile_image_url,$username,$res->user->screen_name)){
//					if($check_file){
//						if(!parent::user_exists_file(strtolower($res->user->screen_name),$username,$file_content)){
//							$resultArray['id'] = $res->user->id;
//							$resultArray['screen_name'] = strtolower($res->user->screen_name);
//							$resultArray['profile_image_url'] = $res->user->profile_image_url;
//							$resultArray['default_profile_image'] = $res->user->default_profile_image;
//							$finalArray[$res->user->id] = $resultArray;
//							$count++;
//						}
//
//					}
//					else{
//						$resultArray['id'] = $res->user->id;
//						$resultArray['screen_name'] = strtolower($res->user->screen_name);
//						$resultArray['profile_image_url'] = $res->user->profile_image_url;
//						$resultArray['default_profile_image'] = $res->user->default_profile_image;
//						$finalArray[$res->user->id] = $resultArray;
//						$count++;
//					}
//				}
//				if($count>$maxRecords){
//					break;
//				}
//            }
//		}
//		$debug_text = "\n ------ Filtered Search Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//		$debug_text .= "\n Result = ".serialize($finalArray);
//		parent::saveDebugContent($username,$debug_text);
//		
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n User count by Search After filtering = ".count($finalArray)."\n";
//			//print_r($result);
//		}
//		
//		echo "\n\n Filtered REcords = ".count($finalArray)."\n\n";
//		
//	    return $finalArray;
//    }
//	
//	public function search_poster($keyword,$maxRecords,$username = '',$check_file = false,$query = false) {
//		$start_time = microtime(true);
//		//$url = $this->twitterSearchURL.'search.json?q=' . $keyword . '&rpp=' . $maxRecords;
//		//$maxRecords = 100;
//		$keyword = str_replace(" ","+",stripslashes($keyword));
//		//$url = "http://twitter.com/phoenix_search.phoenix?q=".$keyword."&include_entities=1&include_available_features=1&contributor_details=true&mode=relevanc&rpp=".$maxRecords;
//		$url = "https://twitter.com/phoenix_search.phoenix?q=".$keyword."&include_entities=1&include_available_features=1&contributor_details=true&rpp=200";
//		//$url = 'http://search.twitter.com/search.json?q=' . $keyword . '&rpp=' . $maxRecords.'&show_user=true';
//		
//		if(DEBUG_MODE){
//			echo "<pre>";
//			
//		}
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: 	Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1';
//		$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//		//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//		//$header_array[] = 'Accept-Encoding: gzip,deflate';
//		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//		//$header_array[] = 'Keep-Alive: 115';
//		//$header_array[] = 'Connection: keep-alive';
//		//$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//		//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//		//$header_array[] = 'X-PHX: true';
//		//$header_array[] = 'Referer: https://twitter.com/';
//		//$header_array[] = 'Pragma: no-cache';
//		//$header_array[] = 'Cache-Control: no-cache';
//		//parent::setCookieName($keyword."_search_cookie");
//		$temp_reponse = $this->get_curl_results($url,null,false,null,$header_array,0,parent::get_user_proxy());
//		$result = json_decode($temp_reponse);
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n".$url."\n";
//			//print_r($result);
//		}
//        //$result = json_decode(parent::get_curl_results($url));
//		$finalArray = array();
//		$debug_text = "\n ------ Search REsult -------\n";
//		$debug_text .= "\n URL = ".$url;
//		$debug_text .= "\n Keyword = ".$keyword;
//		$debug_text .= "\n Result = ".serialize($result);
//		parent::saveDebugContent($username,$debug_text);
//		$file_content = array();
//		if($check_file){
//			$file_name = SITE_PATH.'following/'.strtolower($username).'-follow.txt';
//			if(file_exists($file_name)){
//				$file_content = explode(";",file_get_contents($file_name));
//			}
//		}
//		
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n User count by Search = ".count($result->statuses)."\n";
//			//print_r($result);
//		}
//		
//		$count = 0;
//		if(isset($result->statuses)){
//			echo "\n\n Some REcords\n\n";
//			echo "\n\n Total REcords = ".count($result->statuses)."\n\n";
//			foreach ($result->statuses as $res) {
//                if(intval($res->user->default_profile_image) !=1 && !parent::checkDefaultImage($res->user->profile_image_url,$username,$res->user->screen_name)){
//					if($check_file){
//						if(!parent::user_exists_file(strtolower($res->user->screen_name),$username,$file_content)){
//							$resultArray['id'] = $res->user->id;
//							$resultArray['screen_name'] = strtolower($res->user->screen_name);
//							$resultArray['profile_image_url'] = $res->user->profile_image_url;
//							$resultArray['default_profile_image'] = $res->user->default_profile_image;
//							$finalArray[$res->user->id] = $resultArray;
//							$count++;
//						}
//
//					}
//					elseif($query){
//						$str_lower_username = strtolower($username);
//						$first_ch = strtolower(substr($str_lower_username, 0, 1));
//						$second_ch = strtolower(substr($str_lower_username, 1, 1));
//						$range = parent::get_range($second_ch);
//						$range_table = $first_ch.$range;
//						if(parent::check_user_not_exists($range_table,$str_lower_username,$res->user->screen_name)){
//							$resultArray['id'] = $res->user->id;
//							$resultArray['screen_name'] = strtolower($res->user->screen_name);
//							$resultArray['profile_image_url'] = $res->user->profile_image_url;
//							$resultArray['default_profile_image'] = $res->user->default_profile_image;
//							$finalArray[$res->user->id] = $resultArray;
//							$count++;
//						}
//					}
//					else{
//						$resultArray['id'] = $res->user->id;
//						$resultArray['screen_name'] = strtolower($res->user->screen_name);
//						$resultArray['profile_image_url'] = $res->user->profile_image_url;
//						$resultArray['default_profile_image'] = $res->user->default_profile_image;
//						$finalArray[$res->user->id] = $resultArray;
//						$count++;
//					}
//				}
//				if($count>$maxRecords){
//					break;
//				}
//            }
//		}
//		else{
//			echo "\n\n No record in Keyword Search\n\n";
//			echo "\n\nRespoonse = ".$temp_reponse."\n\n";
//		}
//		$debug_text = "\n ------ Filtered Search Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//		$debug_text .= "\n Result = ".serialize($finalArray);
//		parent::saveDebugContent($username,$debug_text);
//		
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n User count by Search After filtering = ".count($finalArray)."\n";
//			//print_r($result);
//		}
//		
//		echo "\n\n Filtered REcords = ".count($finalArray)."\n\n";
//		
//	    return $finalArray;
//    }
//	
//	public function searchByLocation($keyword = '',$geocode,$units,$rpp,$username = '',$check_file = false) {	 
//		// echo '<br>Decoded = '.urldecode('http://search.twitter.com/search.atom?geocode=40.757929%2C-73.985506%2C25km');
//		$start_time = microtime(true);
//		$geocode = str_replace(" ","+",$geocode);
//		$keyword = str_replace(" ","+",stripslashes($keyword));
//		$total_records = 500;
//		//$url = $this->twitterSearchURL.'search.json?q='.urlencode($keyword).'&geocode='.$geocode.$units.'km&rpp='.$rpp;
//		$url = "https://twitter.com/phoenix_search.phoenix?q=".$keyword."+near%3A%22".$geocode."%22+within%3A".$units."km+include%3Aretweets&include_entities=1&include_available_features=1&contributor_details=true&rpp=".$total_records;
//		
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 AskTbUT2V5/3.9.1.14019 Firefox/3.6.15';
//		$header_array[] = 'Accept: application/json, text/javascript, */*';
//		//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//		//$header_array[] = 'Accept-Encoding: gzip,deflate';
//		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//		//$header_array[] = 'Keep-Alive: 115';
//		//$header_array[] = 'Connection: keep-alive';
//		$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//		//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//		//$header_array[] = 'X-PHX: true';
//		$header_array[] = 'Referer: http://twitter.com/';
//		//$header_array[] = 'Pragma: no-cache';
//		//$header_array[] = 'Cache-Control: no-cache';
//		$result = json_decode(parent::get_curl_results($url,null,false,null,$header_array));
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n".$url."\n";
//			print_r($result);
//		}
//		$finalArray = array();
//		$debug_text = "\n ------ Search By Location REsult -------\n";
//		$debug_text .= "\n Keyword = ".$keyword;
//		$debug_text .= "\n GeoCode = ".$geocode;
//		$debug_text .= "\n Units = ".$units;
//		$debug_text .= "\n RPP (Result Per Page) = ".$rpp;
//		$debug_text .= "\n URL = ".$url;
//		$debug_text .= "\n Result = ".serialize($result);
//		parent::saveDebugContent($username,$debug_text);
//		
//		$file_content = array();
//		if($check_file){
//			$file_name = SITE_PATH.'following/'.strtolower($username).'-follow.txt';
//			if(file_exists($file_name)){
//				$file_content = explode(";",file_get_contents($file_name));
//			}
//		}
//		$count = 0;		
//		if(isset($result->statuses)){
//			foreach ($result->statuses as $res){
//				if(intval($res->user->default_profile_image) !=1 && !parent::checkDefaultImage($res->user->profile_image_url,$username,$res->user->screen_name)){
//					if($check_file){
//						if(!parent::user_exists_file(strtolower($res->user->screen_name),$username,$file_content)){
//							$resultArray['id'] = $res->user->id;
//							$resultArray['screen_name'] = $res->user->screen_name;
//							$resultArray['profile_image_url'] = $res->user->profile_image_url;
//							$resultArray['default_profile_image'] = $res->user->default_profile_image;
//							$finalArray[$res->user->id] = $resultArray;
//							$count++;
//						}
//					}
//					else{
//							$resultArray['id'] = $res->user->id;
//							$resultArray['screen_name'] = $res->user->screen_name;
//							$resultArray['profile_image_url'] = $res->user->profile_image_url;
//							$resultArray['default_profile_image'] = $res->user->default_profile_image;
//							$finalArray[$res->user->id] = $resultArray;
//							$count++;
//					}
//					
//					if($count>$rpp){
//						break;
//					}
//				}
//			}
//		}
//		$debug_text = "\n ------ Search By Location Filtered Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//		$debug_text .= "\n Result = ".serialize($finalArray);
//		parent::saveDebugContent($username,$debug_text);
//		return $finalArray;
//    }
//	
//	
//	function get_curl_results($url, $postData = null, $createCookie = false, $reffer = 'http://twitter.com/', $header = array(),$debug=0,$currentProxy = '',$httpUsername = '',$httpPassword = '') {
//
//        //$agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12';
//		
//		//$agent = $this->getAgent();		
//       	$cookie_file_path = SITE_PATH."cookies/".strtolower(parent::getCookieName()).'.txt';       
//		if ($createCookie && !file_exists($cookie_file_path)) {
//           /*  if(file_exists($cookie_file_path)){
//					unlink($cookie_file_path);
//				}*/
//			//$cookie_file_path = "cookie.txt";
//            $fp = fopen($cookie_file_path, "wb");
//            fclose($fp);
//        }
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//		if (!is_null($header) || count($header)) {
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//        }
//		if(is_null($header)){
//			$agent = parent::get_user_agent();
//        	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
//		}
//		
//			// The following ensures SSL always works. A little detail:
//			// SSL does two things at once:
//			//  1. it encrypts communication
//			//  2. it ensures the target party is who it claims to be.
//			// In short, if the following code is allowed, CURL won't check if the 
//			// certificate is known and valid, however, it still encrypts communication.
//			curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
//			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
//
//
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10);
//		curl_setopt($ch, CURLOPT_TIMEOUT,10);
//        
//		$proxy = '';
//		//echo $currentProxy;
//		if(empty($currentProxy)){
//			$proxy = parent::get_user_proxy();
//			/*if(empty($proxy)){
//				$proxy = $this->getSingleProxy();
//				$this->setGlobalProxy($proxy);
//				$proxy = $this->getProxy();
//			}*/
//		}
//		else{
//			$proxy = $currentProxy;
//		}
//        if (!is_null($proxy) && $proxy !='') {
//			//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
//            curl_setopt($ch, CURLOPT_PROXY, $proxy);
//        }
//        if (!is_null($postData)) {
//			curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
//        }
//		if(!empty($httpUsername) && !empty($httpPassword)){
//				echo $httpUsername . ':' . $httpPassword;
//				curl_setopt($ch, CURLOPT_USERPWD, $httpUsername . ':' . $httpPassword);
//    			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);		
//		}
//        curl_setopt($ch, CURLOPT_REFERER, $reffer);
//        if (!empty($cookie_file_path)) {
//            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
//            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
//        }
//		
//		// Add Delay
//		//$delay = 0;
//		//$delayArray = array($this->lowDelay,$this->highDelay);		
//		//$delay = $delayArray[array_rand($delayArray,1)];		
//		//sleep($delay);
//		
//				
//        $result = curl_exec($ch);
//		if($result == false){			
//			$curlResult = parent::checkCurlResult(curl_error($ch),$proxy);			
//			/*if(!$curlResult){
//				if($this->globalCount < MAX_PROXY_FAILURE){
//					//for($i=0;$i<$this->getMaxProxyTry();$i++){
//						$this->setGlobalProxy($this->getSingleProxy());			
//						$curlResult = $this->get_curl_results($url,$postData,false,$reffer,$header,$debug);
//						if($curlResult){
//							 curl_close($ch);
//							 return $curlResult;						
//						}
//						$this->globalCount++;
//					//}
//				}
//			}*/			
//		}
//		
//		$info_array = curl_getinfo($ch);
//		$this->last_url = $info_array['url'];
//        curl_close($ch);
//       
//        return $result;
//    }
//	
//	public function searchByLocation_poster($keyword = '',$geocode,$units,$rpp,$username = '',$check_file = false,$query = false) {	 
//		// echo '<br>Decoded = '.urldecode('http://search.twitter.com/search.atom?geocode=40.757929%2C-73.985506%2C25km');
//		$start_time = microtime(true);
//		$geocode = str_replace(" ","+",$geocode);
//		$keyword = str_replace(" ","+",stripslashes($keyword));
//		$total_records = 500;
//		//$url = $this->twitterSearchURL.'search.json?q='.urlencode($keyword).'&geocode='.$geocode.$units.'km&rpp='.$rpp;
//		$url = "https://twitter.com/phoenix_search.phoenix?q=".$keyword."+near%3A%22".$geocode."%22+within%3A".$units."km+include%3Aretweets&include_entities=1&include_available_features=1&contributor_details=true&rpp=".$total_records;
//		
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: '.parent::get_user_agent();
//		$header_array[] = 'Accept: application/json, text/javascript, */*';
//		//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//		//$header_array[] = 'Accept-Encoding: gzip,deflate';
//		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//		//$header_array[] = 'Keep-Alive: 115';
//		//$header_array[] = 'Connection: keep-alive';
//		$header_array[] = 'Content-Type: application/x-www-form-urlencoded';
//		//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//		//$header_array[] = 'X-PHX: true';
//		$header_array[] = 'Referer: http://twitter.com/';
//		//$header_array[] = 'Pragma: no-cache';
//		//$header_array[] = 'Cache-Control: no-cache';
//		$result = json_decode(parent::get_curl_results($url,null,false,null,$header_array,0,parent::get_user_proxy()));
//		
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n".$url."\n";
//			//print_r($result);
//		}
//		
//		$finalArray = array();
//		$debug_text = "\n ------ Search By Location REsult -------\n";
//		$debug_text .= "\n Keyword = ".$keyword;
//		$debug_text .= "\n GeoCode = ".$geocode;
//		$debug_text .= "\n Units = ".$units;
//		$debug_text .= "\n RPP (Result Per Page) = ".$rpp;
//		$debug_text .= "\n URL = ".$url;
//		$debug_text .= "\n Result = ".serialize($result);
//		parent::saveDebugContent($username,$debug_text);
//		
//		$file_content = array();
//		if($check_file){
//			$file_name = SITE_PATH.'following/'.strtolower($username).'-follow.txt';
//			if(file_exists($file_name)){
//				$file_content = explode(";",file_get_contents($file_name));
//			}
//		}
//		$count = 0;
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n User count by Search_Location = ".count($result->statuses)."\n";
//			//print_r($result);
//		}
//		if(isset($result->statuses)){
//			foreach ($result->statuses as $res){
//				if(intval($res->user->default_profile_image) !=1 && !parent::checkDefaultImage($res->user->profile_image_url,$username,$res->user->screen_name)){
//					if($check_file){
//						if(!parent::user_exists_file(strtolower($res->user->screen_name),$username,$file_content)){
//							$resultArray['id'] = $res->user->id;
//							$resultArray['screen_name'] = $res->user->screen_name;
//							$resultArray['profile_image_url'] = $res->user->profile_image_url;
//							$resultArray['default_profile_image'] = $res->user->default_profile_image;
//							$finalArray[$res->user->id] = $resultArray;
//							$count++;
//						}
//					}
//					elseif($query){
//						$str_lower_username = strtolower($username);
//						$first_ch = strtolower(substr($str_lower_username, 0, 1));
//						$second_ch = strtolower(substr($str_lower_username, 1, 1));
//						$range = parent::get_range($second_ch);
//						$range_table = $first_ch.$range;
//						if(parent::check_user_not_exists($range_table,$str_lower_username,$res->user->screen_name)){
//							$resultArray['id'] = $res->user->id;
//							$resultArray['screen_name'] = strtolower($res->user->screen_name);
//							$resultArray['profile_image_url'] = $res->user->profile_image_url;
//							$resultArray['default_profile_image'] = $res->user->default_profile_image;
//							$finalArray[$res->user->id] = $resultArray;
//							$count++;
//						}
//					}
//					else{
//							$resultArray['id'] = $res->user->id;
//							$resultArray['screen_name'] = $res->user->screen_name;
//							$resultArray['profile_image_url'] = $res->user->profile_image_url;
//							$resultArray['default_profile_image'] = $res->user->default_profile_image;
//							$finalArray[$res->user->id] = $resultArray;
//							$count++;
//					}
//					
//					if($count>$rpp){
//						break;
//					}
//				}
//			}
//		}
//		$debug_text = "\n ------ Search By Location Filtered Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//		$debug_text .= "\n Result = ".serialize($finalArray);
//		parent::saveDebugContent($username,$debug_text);
//		if(DEBUG_MODE){
//			echo "<pre>";
//			echo "\n User count by Search_Location after Filtering = ".count($finalArray)."\n";
//		}
//		return $finalArray;
//    }
//	
//	public function followUser($myUserName,$screen_name,$screen_id){
//		$followUserStartTime = microtime(true);
//		$followedBy = parent::userExists($screen_name,$this->getGolbalFollowingList(),$myUserName);
//		/*$friendResult = $this->isFriendExists($myUserName,$screen_name);
//		$followedBy = 1;
//		if(isset($friendResult->relationship)){
//			$followedBy = $friendResult->relationship->target->followed_by;
//		}else{
//			return 'no-found';
//		}*/
//		//parent::saveDebugContent("Follow User  = ".$followedBy."\n\n\n");
//		
//		if(!$followedBy)
//		{
//			$start_time = microtime(true);
//			//$userInfo = $this->seacrhUser($screen_name);
//			$userId = $screen_id;			
//			$postAuthToken = $this->getPostAuthToken($myUserName);
//			
//			echo $url = $this->twitterAPIURL.'friendships/create.json';
//			echo "<br>";
//			echo $postData = 'user_id='.$userId.'&post_authenticity_token='.$postAuthToken;
//			//echo $postData = 'user_id='.$userId.'&post_authenticity_token='.$postAuthToken.'&follow=true';
//			echo "<br>";
//			
//			$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//			$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//			$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding: gzip,deflate';
//			//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			$header_array[] = 'Keep-Alive: 115';
//			$header_array[] = 'Connection: keep-alive';
//			//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//			$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			$header_array[] = 'X-PHX: true';
//			$header_array[] = 'Referer: https://api.twitter.com/p_receiver.html';
//			//$header_array[] = 'Content-Length: 82';
//			$header_array[] = 'Pragma: no-cache';
//			$header_array[] = 'Cache-Control: no-cache';
//				
//			$tempResult = parent::get_curl_results($url,$postData,false,'http://api.twitter.com/p_receiver.html',$header_array);
//			$result = json_decode($tempResult);
//					 
//			$debug_text = "\n ------ Follow User Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//			$debug_text .= "\n Result = ".serialize($result);
//			parent::saveDebugContent($myUserName,$debug_text);
//			
//			return $result;
//		}else{
//			parent::saveDebugContent($myUserName,"User : ".$myUserName." | User to be Followed : ".$screen_name." | Do not Follow a User If already followed | Total Time :  ".((float)(microtime(true) - $followUserStartTime)));
//			return NULL;
//		}
//	}
//	
//	public function followUserBulk($myUserName,$screen_name,$screen_id){
//	
//			$start_time = microtime(true);
//			//$userInfo = $this->seacrhUser($screen_name);
//			$userId = $screen_id;			
//			$postAuthToken = $this->getPostAuthToken($myUserName);
//			
//			$url = $this->twitterAPIURL.'friendships/create.json';
//			echo "<br>";
//			echo $postData = 'user_id='.$userId.'&post_authenticity_token='.$postAuthToken;
//			echo "<br>";
//			//echo $postData = 'user_id='.$userId.'&post_authenticity_token='.$postAuthToken.'&follow=true';
//			
//			//$header_array[] = 'POST /'.$this->apiVersion.'/friendships/create.json HTTP/1.1';
//			$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//			$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//			$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding: gzip,deflate';
//			//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			$header_array[] = 'Keep-Alive: 115';
//			$header_array[] = 'Connection: keep-alive';
//			//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//			$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			$header_array[] = 'X-PHX: true';
//			$header_array[] = 'Referer: https://api.twitter.com/p_receiver.html';
//			//$header_array[] = 'Content-Length: 82';
//			$header_array[] = 'Pragma: no-cache';
//			$header_array[] = 'Cache-Control: no-cache';
//			echo "<br> Simple Follow REsult<br>";
//			echo $tempResult = parent::get_curl_results($url,$postData,false,null,$header_array);
//			$result = json_decode($tempResult);
//			echo "<br><br>";
//			if(DEBUG_MODE){
//				print_r($result);
//			}
//					 
//			$debug_text = "\n ------ Follow User Bulk Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//			$debug_text .= "\n Result = ".serialize($result);
//			parent::saveDebugContent($myUserName,$debug_text);
//			
//			return $result;
//	}
//	
//	
//	
//	public function doTweet($myUserName,$text){
//			$start_time = microtime(true);
//		
//			$postAuthToken = $this->getPostAuthToken($myUserName);
//			//parent::get_curl_results($this->twitterURL);
//			//parent::get_curl_results('https://api.twitter.com/receiver.html');
//			$url = $this->twitterAPIURL.'statuses/update.json';
//			echo '<br>';
//			echo $postData = 'include_entities=true&status='.urlencode(stripslashes($text)).'&post_authenticity_token='.$postAuthToken;
//			echo '<br>';
//			$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
//			$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//			$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding:	gzip,deflate';
//			$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			//$header_array[] = 'Keep-Alive: 115';
//			$header_array[] = 'Connection: keep-alive';
//			//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//			$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			$header_array[] = 'X-PHX: true';
//			$header_array[] = 'Referer: https://api.twitter.com/receiver.html';
//
//			$tempResult = parent::get_curl_results($url,$postData,false,'https://api.twitter.com/receiver.html',$header_array);
//			$result = json_decode($tempResult);
//						
//			$debug_text = "\n ------ Tweet Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//			$debug_text .= "\n Post Data = ".$postData;
//			$debug_text .= "\n Result = ".serialize($result);
//			parent::saveDebugContent($myUserName,$debug_text);
//			
//			return $result;
//		
//	}
//
//	public function doReTweet($myUserName,$tweetid){
//			$start_time = microtime(true);
//			parent::get_curl_results($this->twitterURL);
//			$postAuthToken = $this->getPostAuthToken($myUserName);
//			
//			$url = $this->twitterAPIURL.'statuses/retweet/'.$tweetid.'.json';
//			$postData = 'post_authenticity_token='.$postAuthToken;
//			
//			$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//			$header_array[] = 'Accept: application/json, text/javascript, */*';
//			$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding:	gzip,deflate';
//			$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			$header_array[] = 'Keep-Alive: 115';
//			$header_array[] = 'Connection: keep-alive';
//			$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//			$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			$header_array[] = 'X-PHX: true';
//			$header_array[] = 'Referer: https://api.twitter.com/p_receiver.html';
//			
//			$tempResult = parent::get_curl_results($url,$postData,false,'https://api.twitter.com/receiver.html',$header_array);
//			$result = json_decode($tempResult);
//					
//			$debug_text = "\n ------ Re-Pin Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//			$debug_text .= "\n Post Data = ".$postData;
//			$debug_text .= "\n Result = ".serialize($result);
//			parent::saveDebugContent($myUserName,$debug_text);
//						
//			return $result;
//		
//	}
//		
//	public function unfollowUser($myUserName,$screen_name,$allFollowingsArray,$userId = ''){
//		$start_time = microtime(true);
//		//$followedBy = parent::userExists($screen_name,$allFollowingsArray,$myUserName);
//		
//		
//		/*$friendResult = $this->isFriendExists($myUserName,$screen_name);
//		if(isset($friendResult->relationship)){
//			$followedBy = $friendResult->relationship->target->followed_by;
//		}*/
//		//if($followedBy)
//		{	
//			if(empty($userId)){
//				$userInfo = $this->seacrhUser($screen_name,$myUserName);
//				if(isset($userInfo->error)){
//					return $userInfo;
//				}
//				$userId = $userInfo->id;
//				
//			}
//			if(!empty($userId)){
//				//$userName = $userInfo['screen_name'];
//				$postAuthToken = $this->getPostAuthToken($myUserName);
//				
//				$url = $this->twitterAPIURL.'friendships/destroy.json';
//				echo "<br><br>";
//				echo $postData = 'user_id='.$userId.'&post_authenticity_token='.$postAuthToken;
//				echo "<br><br>";
//				
//				//$header_array[] = 'POST /'.$this->apiVersion.'/friendships/create.json HTTP/1.1';
//				$header_array[] = 'Host: api.twitter.com';
//				$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
//				$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//				$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//				//$header_array[] = 'Accept-Encoding: gzip,deflate';
//				//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//				$header_array[] = 'Keep-Alive: 115';
//				$header_array[] = 'Connection: keep-alive';
//				//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//				$header_array[] = 'X-Requested-With: XMLHttpRequest';
//				$header_array[] = 'X-PHX: true';
//				$header_array[] = 'Referer: https://api.twitter.com/receiver.html';
//				//$header_array[] = 'Content-Length: 82';
//				$header_array[] = 'Pragma: no-cache';
//				$header_array[] = 'Cache-Control: no-cache';
//					
//					
//				$result = json_decode(parent::get_curl_results($url,$postData,false,'https://api.twitter.com/receiver.html',$header_array));
//				if(DEBUG_MODE){
//					echo '<pre><br> UnFollow Result <br>';
//					print_r($result);
//				}
//				 
//				$debug_text = "\n ------ Un-Follow User Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//				$debug_text .= "\nUn-Follow User = ".$screen_name;
//				$debug_text .= "\n Result = ".serialize($result);
//				
//				parent::saveDebugContent($myUserName,$debug_text);
//				 
//				return $result;
//			}
//		}/*else{
//			return NULL;
//		}*/
//	}
//	
//	public function unfollowNonMutualUser($myUserName,$screen_name,$followingList,$followersList){
//		
//		$flagFollowing = parent::userExists($screen_name,$followingList,$myUserName);
//		$flagFollower = parent::userExists($screen_name,$followersList,$myUserName);
//		$start_time = microtime(true);
//		//$friendResult = $this->isFriendExists($myUserName,$screen_name);
//		/*if(isset($friendResult->relationship)){
//			$targetFollowing = $friendResult->relationship->target->following;
//			$sourceFollowing = $friendResult->relationship->source->following;
//		}*/
//	
//		if(!$flagFollower || !$flagFollowing){
//			$userInfo = $this->seacrhUser($screen_name,$myUserName);
//			$userId = $userInfo->id;
//			$postAuthToken = $this->getPostAuthToken($myUserName);
//			
//			$url = $this->twitterAPIURL.'friendships/destroy.json';
//			echo "<br><br>";
//			echo $postData = 'user_id='.$userId.'&post_authenticity_token='.$postAuthToken;
//			echo "<br><br>";
//			//$header_array[] = 'POST /'.$this->apiVersion.'/friendships/create.json HTTP/1.1';
//			$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//			$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//			$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding: gzip,deflate';
//			//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			$header_array[] = 'Keep-Alive: 115';
//			$header_array[] = 'Connection: keep-alive';
//			//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//			$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			$header_array[] = 'X-PHX: true';
//			$header_array[] = 'Referer: https://api.twitter.com/p_receiver.html';
//			//$header_array[] = 'Content-Length: 82';
//			$header_array[] = 'Pragma: no-cache';
//			$header_array[] = 'Cache-Control: no-cache';				
//			
//			$result = json_decode(parent::get_curl_results($url,$postData,false,'http://api.twitter.com/p_receiver.html',$header_array));
//			
//			$debug_text = "\n ------ Un-Follow Non-Mutual User Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//			$debug_text .= "\nUn-Follow User = ".$screen_name;
//			$debug_text .= "\n Result = ".serialize($result);
//			parent::saveDebugContent($myUserName,$debug_text);
//			
//			if(DEBUG_MODE){
//				echo "<pre> Unfollow Result <br>";
//				print_r($result);
//			}
//			return $result;
//	}else{
//		return NULL;
//		}
//	}
//	
//	public function isFriendExists($myScreenName,$friendScreenName){
//		$chekFriendStartTime = microtime(true);
//		$url = $this->twitterAPIURL.'friendships/show.json?source_screen_name='.$myScreenName.'&target_screen_name='.$friendScreenName;
//		$result = json_decode(parent::get_curl_results($url));
//		parent::saveDebugContent("User : ".$myScreenName." | Check User : ".$friendScreenName." | Time :  ".((float)(microtime(true) - $chekFriendStartTime)));
//		return $result;
//	}
//	
//	
//	public function getPostAuthToken($screen_name){
//		$tokenStartTime = microtime(true);
//		$postAuthToken = $this->getGolbalAuthToken();
//		if(empty($postAuthToken)){
//			$cookieFile = file_get_contents(SITE_PATH.'cookies/'.$screen_name.'.txt');
//			preg_match_all("/auth_token	(.*?)\\n/", $cookieFile, $match);
//			if(isset($match[1][0])){
//				$postAuthToken = $match[1][0];
//			}	
//		}		
//		
//		//$url = $this->twitterURL.'#!/'.$screen_name;		
//		//$header_array[] = 'Host: twitter.com';
//		//$header_array[] = 'User-Agent: 	Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//		//$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//		//$header_array[] = 'Accept-Encoding: gzip,deflate';
//		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//		//$header_array[] = 'Keep-Alive: 115';
//		//$header_array[] = 'Connection: keep-alive';
//		//$header_array[] = 'Cache-Control: max-age=0';
//		
//		//$html = parent::get_curl_results($url,null,false,'',$header_array);		 
//		//preg_match("/postAuthenticityToken: '(.*?)'/si", $html, $match);
//		//preg_match("/\<input type='hidden' value='(.*?)' name='authenticity_token'\/\>/si", $html, $match);
//        //preg_match("/postAuthenticityToken: '(.*?)'/si", $html, $match);
//       /* if (isset($match[1])){
//            $postAuthToken = $match[1];
//        }
//		else{
//			$postAuthToken = $this->getLoggedAuthencityToken($html);
//		}*/
//        return $postAuthToken;
//	}
//    public function seacrhUser($screen_name,$username) {
//		$result = null;
//		$start_time = microtime(true);
//		$url = $this->twitterAPIURL.'users/show.json?screen_name='.$screen_name."&include_entities=true";
//		
//		$header_array[] = 'Host: api.twitter.com';
//			$header_array[] = 'User-Agent: '.parent::get_user_agent();
//			$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//			$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//			//$header_array[] = 'Accept-Encoding: gzip,deflate';
//			//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//			$header_array[] = 'Keep-Alive: 115';
//			$header_array[] = 'Connection: keep-alive';
//			//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//			$header_array[] = 'X-Requested-With: XMLHttpRequest';
//			$header_array[] = 'X-PHX: true';
//			$header_array[] = 'Referer: https://api.twitter.com/p_receiver.html';
//			//$header_array[] = 'Content-Length: 82';
//			$header_array[] = 'Pragma: no-cache';
//			$header_array[] = 'Cache-Control: no-cache';
//		echo "\n ------ Search User Result -------";
//		$twitter_response = parent::get_curl_results($url,null,false,'http://api.twitter.com/p_receiver.html',$header_array,parent::get_user_proxy());
//		$result = json_decode($twitter_response);		
//        //$result = json_decode(parent::get_curl_results($url));
//		$debug_text = "\n ------ Search User Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//		$debug_text .= "\n User = ".$screen_name;
//		$debug_text .= "\n Result = ".serialize($result);
//		parent::saveDebugContent($username,$debug_text);
//        return $result;
//    }
//
//    public function getScreenName($html ,$username = '') {
//        $screenName = '';
//		if($username && $username != ''){
//			
//			$html_lower = strtolower($html);
//			
//		$pattern = '<b class="fullname">'.$username.'</b>';
//		$pos = strpos($html_lower,strtolower($pattern));
//		$pattern2 = $username.'">Profile</a>';
//		$pos2 = strpos($html_lower,strtolower($pattern2));
//		
//		$pattern3 = 'data-screen-name="'.$username.'">';
//		$pos3 = strpos($html_lower,strtolower($pattern3));
//		
//			//$pattern = '<a href="/'.$username.'" data-i18n-label="Profile" class="i18n-deferred">';
//			//$pos = strpos(strtolower($html),strtolower($pattern));
//			if($pos !== false || $pos2 !== false || $pos3 !== false){
//				return strtolower($username);
//			}
//		}
//       /* preg_match('/\<span id="screen-name"\>(.*?)\<\/span\>/si', $html, $match);
//		
//        if (isset($match[1])){
//            $screenName = $match[1];
//        }*/
//		/*if(!empty($screenName)){
//			echo '<br> Auth Token = '.$this->globalAuthencityToken;
//			echo '<br>';
//			if(parent::is_suspeneded($screenName,$suspended)){
//				return null;
//			}
//		}*/
//        return strtolower($screenName);
//    }
//	
//
//    public function getUserId($html) {
//        $userId = '';
//        preg_match('/twitter.com\/favorites\/([0-9]+).rss/si', $html, $match);
//        if (isset($match[1])) {
//            $userId = $match[1];
//        }
//        return $userId;
//    }
//	function getAccountDetail($username = ''){
//		$username = strtolower($username);
//		$detail_dir = "users_detail";
//		$base_path = SITE_PATH.$detail_dir.'/';
//		$file_name = strtolower($username).'.txt';
//		if(file_exists($base_path.$file_name)){
//			return parent::getUserDeatil($username);
//		}
//		
//		$start_time = microtime(true);
//		$rand = (rand(1,99))/100;
//		$rand = number_format($rand,17);
//		$url = 'https://twitter.com/account/bootstrap_data?r='.$rand;
//		$result_raw = parent::get_curl_results($url);
//		parent::saveUserDeatil($username,$result_raw);
//		$result = json_decode($result_raw,true);
//		 
//		
//		
//		$debug_text = "\n ------ Get Account Detail -------\n Process Time =  ".((float)(microtime(true) - $start_time));
//	    $debug_text .= "\n Result = ".serialize($result);
//		parent::saveDebugContent($username,$debug_text);
//		return $result;
//		
//	}
//	function updateEmail($email, $password,$screen_name){
//		$email = trim(strtolower($email));
//		$start_time = microtime(true);
//		$accountPage = $this->getAccountPage();	
//		$authenticityToken = $this->getGolbalAuthToken();
//		//$authenticityToken = $this->getAuthencityToken($accountPage);
//		$url = $this->twitterURL.'settings/accounts/update';
//		//$postData = "_method=put&authenticity_token=".$authenticityToken."&user%5Bemail%5D=".urlencode($email)."&auth_password=".urlencode($password)."&commit=Save+changes";
//		$postData = "_method=put&authenticity_token=".$authenticityToken."&user%5Bscreen_name%5D=".urlencode($screen_name)."&user%5Bemail%5D=".urlencode($email)."&user%5Bdiscoverable_by_email%5D=1&user%5Bdiscoverable_by_email%5D=0&user%5Blang%5D=en&user%5Btime_zone%5D=Central+Time+%28US+%26+Canada%29&user%5Bgeo_enabled%5D=0&user%5Bshow_all_inline_media%5D=0&user%5Bprotected%5D=0&user%5Bssl_only%5D=0&auth_password=".urlencode($password)."&commit=Save+changes";
//		
//		$result = parent::get_curl_results($url,$postData);
//			
//		$debug_text = "\n ------- Update Email ------- \n Total Time = ".((float)(microtime(true) - $start_time));
//		$debug_text .= "\n URL = ".$url;
//		$debug_text .= "\n Post Data = ".$postData;
//		$debug_text .= "\n Result = ".$result;
//		parent::saveDebugContent($screen_name,$debug_text);
//		
//        return $result;
//	}
//	function updateProfile($picture='', $location, $web,$bio,$name = '',$username = ''){
//		$start_time = microtime(true);
//		$profilePage = $this->getProfilePage();	
//		$authenticityToken = $this->getGolbalAuthToken();
//		//$authenticityToken = $this->getAuthencityToken($profilePage);
//		$url = $this->twitterURL.'settings/profile';
//		
//		$postData['_method'] = 'put' ;
//		$postData['authenticity_token'] = $authenticityToken ;
//		//$postData['profile_image[uploaded_data]'] =  '';
//		if(file_exists($picture)){
//			$postData['profile_image[uploaded_data]'] =  '@'.$picture;
//		}
//		else{
//			$postData['profile_image[uploaded_data]'] =  '';
//		}
//	
//	
//		if(trim($name) == ''){
//			$postData['user[name]'] = $username;
//		}else{
//			$postData['user[name]'] = $name;
//		}
//		
//		$postData['user[location]'] = $location;
//		$postData['user[url]'] = $web;
//		$postData['user[description]'] = $bio;
//		$postData['commit'] = 'Save' ;
//		echo '<pre>';
//		print_r($postData);
//		$result = parent::get_curl_results($url,$postData,false,'http://twitter.com/settings/profile',array( 'Expect:' ));
//		
//		$debug_text = "\n ------- Update Profile ------- \n Total Time = ".((float)(microtime(true) - $start_time));
//		$debug_text .= "\n URL = ".$url;
//		$debug_text .= "\n Post Data = ".serialize($postData);
//		$debug_text .= "\n Result = ".$result;
//		parent::saveDebugContent($username,$debug_text);
//        
//		return $result;
//	}
//	
//	function updateDesign($picture='',$profile_use_background_image=true,$username){
//		$start_time = microtime(true);
//		$designPage = $this->getDesignPage();	
//		$authenticityToken = $this->getGolbalAuthToken();
//		//$authenticityToken = $this->getAuthencityToken($designPage);
//		
//		$url = $this->twitterURL.'settings/design/update';
//		
//		$header_array = array();		
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		$header_array[] = 'Referer: http://twitter.com/settings/design?tab=backgrounds';
//		
//		
//		$postData = array();
//		
//		$postData['authenticity_token'] = $authenticityToken;
//		$postData['user[profile_default]'] = "false";
//		$postData['tab'] = "backgrounds";
//		$postData['profile_theme'] = "1";	
//		$postData['user[uploaded_data]'] = '@'.$picture;
//		$postData['user[profile_use_background_image]'] = $profile_use_background_image;
//		//$postData['user[profile_background_image_url]'] = "http://a0.twimg.com/profile_background_images/200895598/asi5.jpg";
//		$postData['user[profile_background_tile]'] = "1";
//		$postData['user[profile_background_color]'] = "#C0DEED";
//		$postData['user[profile_text_color]'] = "#333333";
//		$postData['user[profile_link_color]'] = "#0084B4";
//		$postData['user[profile_sidebar_fill_color]'] = "#DDEEF6";
//		$postData['user[profile_sidebar_border_color]'] = "#C0DEED";
//		$postData['commit'] = "Save Changes";
//		
//		
//		
//		$result = parent::get_curl_results($url,$postData,false,'http://twitter.com/settings/design?tab=backgrounds',array( 'Expect:' ));
//		
//		$debug_text = "\n ------- Update Design ------- \n Total Time = ".((float)(microtime(true) - $start_time));
//		$debug_text .= "\n URL = ".$url;
//		$debug_text .= "\n Post Data = ".serialize($postData);
//		$debug_text .= "\n Result = ".$result;
//		parent::saveDebugContent($username,$debug_text);
//        
//		return $result;
//	}
//	
//	function updateNotifications($notif_code,$username){
//		$start_time = microtime(true);
//		$authenticityToken = $this->getPostAuthToken($username);		
//		if(empty($authenticityToken) || is_null($authenticityToken)){
//			$designPage = $this->getNotificationPage();			
//			$authenticityToken = $this->getAuthencityToken($designPage);
//		}
//		$url = $this->twitterURL.'settings/notifications/update';
//		
//		$header_array = array();		
//		//$header_array[] = 'Host: twitter.com';
//		//$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//		//$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		//$header_array[] = 'Referer: http://twitter.com/settings/notifications';
//		
//		
//		$header_array[] = 'Host: api.twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//		//$header_array[] = 'Accept-Encoding: gzip,deflate';
//		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//		//$header_array[] = 'Keep-Alive: 115';
//		$header_array[] = 'Connection: keep-alive';
//		//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//		//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//		//$header_array[] = 'X-PHX: true';
//		$header_array[] = 'Referer: http://twitter.com/settings/notifications';
//		//$header_array[] = 'Content-Length: 82';
//		//$header_array[] = 'Pragma: no-cache';
//		//$header_array[] = 'Cache-Control: no-cache';
//		
//		
//		$postData = "";	
//		
//	
//	if($notif_code == 150){
//		$postData = 'authenticity_token='.$authenticityToken.'&user%5Bsend_new_direct_text_email%5D=0&user%5Bsend_mention_email%5D=0&user%5Bsend_new_friend_email%5D=0&user%5Bsend_favorited_email%5D=0&user%5Bsend_retweeted_email%5D=0&user%5Bsend_email_newsletter%5D=0&user%5Bsend_account_updates_email%5D=0&commit=Save';
//	
//	}
//	elseif($notif_code == 151){
//		
//		/*$postData['user[send_new_direct_text_email]'] = "1";
//		$postData['user[send_new_direct_text_email]'] = "0";
//		$postData['user[send_mention_email]'] = "2";
//		$postData['user[send_mention_email]'] = "0";
//		$postData['user[send_new_friend_email]'] = "0";
//		$postData['user[send_favorited_email]'] = "0";
//		$postData['user[send_retweeted_email]'] = "0";
//		$postData['user[send_email_newsletter]'] = "0";
//		$postData['user[send_account_updates_email]'] = "0";*/
//		
//		$postData = 'authenticity_token='.$authenticityToken.'&user%5Bsend_new_direct_text_email%5D=1&user%5Bsend_new_direct_text_email%5D=0&user%5Bsend_mention_email%5D=2&user%5Bsend_mention_email%5D=0&user%5Bsend_new_friend_email%5D=0&user%5Bsend_favorited_email%5D=0&user%5Bsend_retweeted_email%5D=0&user%5Bsend_email_newsletter%5D=0&user%5Bsend_account_updates_email%5D=0&commit=Save';
//		
//	}
//	elseif($notif_code == 152){
//		/*$postData['user[send_new_direct_text_email]'] = "1";
//		$postData['user[send_new_direct_text_email]'] = "0";
//		$postData['user[send_mention_email]'] = "2";
//		$postData['user[send_mention_email]'] = "0";
//		$postData['user[send_new_friend_email]'] = "1";
//		$postData['user[send_new_friend_email]'] = "0";
//		$postData['user[send_favorited_email]'] = "2";
//		$postData['user[send_favorited_email]'] = "0";
//		$postData['user[send_retweeted_email]'] = "2";
//		$postData['user[send_retweeted_email]'] = "0";
//		$postData['user[send_email_newsletter]'] = "1";
//		$postData['user[send_email_newsletter]'] = "0";
//		$postData['user[send_account_updates_email]'] = "1";
//		$postData['user[send_account_updates_email]'] = "0";*/
//		
//		$postData = 'authenticity_token='.$authenticityToken.'&user%5Bsend_new_direct_text_email%5D=1&user%5Bsend_new_direct_text_email%5D=0&user%5Bsend_mention_email%5D=2&user%5Bsend_mention_email%5D=0&user%5Bsend_new_friend_email%5D=1&user%5Bsend_new_friend_email%5D=0&user%5Bsend_favorited_email%5D=2&user%5Bsend_favorited_email%5D=0&user%5Bsend_retweeted_email%5D=2&user%5Bsend_retweeted_email%5D=0&user%5Bsend_email_newsletter%5D=1&user%5Bsend_email_newsletter%5D=0&user%5Bsend_account_updates_email%5D=1&user%5Bsend_account_updates_email%5D=0&commit=Save';
//	}
//	
//		if(!empty($postData)){
//				
//				$result = parent::get_curl_results($url,$postData,false,'http://twitter.com/settings/notifications',$header_array);
//				
//				$debug_text = "\n ------- Update Design ------- \n Total Time = ".((float)(microtime(true) - $start_time));
//				$debug_text .= "\n URL = ".$url;
//				$debug_text .= "\n Post Data = ".serialize($postData);
//				$debug_text .= "\n Result = ".$result;
//				parent::saveDebugContent($username,$debug_text);
//				
//				return $result;
//		}
//		else{
//			return null;
//		}
//	}
//	
//	function getAccountPage(){
//		$url = $this->twitterURL.'settings/account';
//        $result = parent::get_curl_results($url);
//        return $result;
//	}
//	function getProfilePage(){
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
//		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//		//$header_array[] = 'Accept-Encoding: gzip,deflate';
//		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//		//$header_array[] = 'Keep-Alive: 115';
//		//$header_array[] = 'Connection: keep-alive';
//		//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//		//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//		///$header_array[] = 'X-PHX: true';
//		$header_array[] = 'Referer: https://twitter.com/';
//		//$header_array[] = 'Content-Length: 82';
//		//$header_array[] = 'Pragma: no-cache';
//		//$header_array[] = 'Cache-Control: no-cache';
//		
//		$url = $this->twitterURL.'settings/profile';
//        $result = parent::get_curl_results($url,null,false,'',$header_array);
//        return $result;
//	}
//	function getDesignPage(){
//		
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
//		$header_array[] = 'Accept: ext/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//		//$header_array[] = 'Accept-Encoding: gzip,deflate';
//		//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//		//$header_array[] = 'Keep-Alive: 115';
//		//$header_array[] = 'Connection: keep-alive';
//		//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//		//$header_array[] = 'X-Requested-With: XMLHttpRequest';
//		///$header_array[] = 'X-PHX: true';
//		$header_array[] = 'Referer: https://twitter.com/settings/profile';
//		//$header_array[] = 'Content-Length: 82';
//		//$header_array[] = 'Pragma: no-cache';
//		//$header_array[] = 'Cache-Control: no-cache';
//		
//		$url = $this->twitterURL.'settings/design';
//        $result = parent::get_curl_results($url,null,false,'',$header_array);
//        return $result;
//	}
//	
//	function getNotificationPage(){
//		$url = $this->twitterURL.'settings/notifications';
//		
//		$header_array[] = 'Host: twitter.com';
//		$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//		$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//		$header_array[] = 'Referer: http://twitter.com/settings/notifications';
//		
//        $result = parent::get_curl_results($url,null,false,'http://twitter.com/settings/notifications',$header_array);
//        return $result;
//	}
//
//
//	
//public function getAllLists($user_id){
//	$url = $this->twitterAPIURL.$user_id.'/lists.json?cursor=-1';
//	$header_array[] = 'Host: api.twitter.com';
//	$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//	$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//	$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//	//$header_array[] = 'Accept-Encoding: gzip,deflate';
//	//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//	$header_array[] = 'Keep-Alive: 115';
//	$header_array[] = 'Connection: keep-alive';
//	//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//	$header_array[] = 'X-Requested-With: XMLHttpRequest';
//	$header_array[] = 'X-PHX: true';
//	$header_array[] = 'Referer: https://api.twitter.com/p_receiver.html';
//	//$header_array[] = 'Pragma: no-cache';
//	//$header_array[] = 'Cache-Control: no-cache';				
//	$tempResult = parent::get_curl_results($url,null,false,'',$header_array);
//	$result = json_decode($tempResult);
//}
//
//public function createList($user_id,$list_name,$username){
//	$start_time = microtime(true);
//	$url = $this->twitterAPIURL.$user_id.'/lists.json';
//	$postAuthToken = $this->getPostAuthToken($list_name);
//	
//	$postData = 'name='.strtolower($list_name).'&description=&mode=public&post_authenticity_token='.$postAuthToken;
//	
//	$header_array[] = 'Host: api.twitter.com';
//	$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
//	$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
//	$header_array[] = 'Accept-Language: en-us,en;q=0.5';
//	//$header_array[] = 'Accept-Encoding: gzip,deflate';
//	//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
//	$header_array[] = 'Keep-Alive: 115';
//	$header_array[] = 'Connection: keep-alive';
//	//$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
//	$header_array[] = 'X-Requested-With: XMLHttpRequest';
//	$header_array[] = 'X-PHX: true';
//	$header_array[] = 'Referer: https://api.twitter.com/p_receiver.html';
//	//$header_array[] = 'Pragma: no-cache';
//	//$header_array[] = 'Cache-Control: no-cache';				
//	$tempResult = parent::get_curl_results($url,$postData,false,'',$header_array);
//	$result = json_decode($tempResult);
//	$debug_text = "\n ------ Create List Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
//	$debug_text .= "\n List Name = ".$list_name;
//	$debug_text .= "\n Result = ".serialize($result);
//	parent::saveDebugContent($username,$debug_text);
//	
//	echo '<pre><br>List Result <br>';
//	print_r($result);
//	
//	return $result;
//}
//
////public function addUserInList($list_id,$userId,$screen_name,$username){
////			$start_time = microtime(true);
////			$userInfo = $this->seacrhUser($screen_name,$username);
////			//$screen_id = $userInfo->id;			
////			$postAuthToken = $this->getPostAuthToken($screen_name);
////			$url = $this->twitterAPIURL.$userId.'/'.strtolower($list_id).'/members.json';
////			$postData = 'id='.$screen_name.'&post_authenticity_token='.$postAuthToken;
////			$header_array[] = 'Host: api.twitter.com';
////			$header_array[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 AskTbUT2V5/3.9.1.14019 Firefox/3.6.13';
////			$header_array[] = 'Accept: application/json, text/javascript, */*; q=0.01';
////			$header_array[] = 'Accept-Language: en-us,en;q=0.5';
////			//$header_array[] = 'Accept-Encoding: gzip,deflate';
////			//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
////			$header_array[] = 'Keep-Alive: 115';
////			$header_array[] = 'Connection: keep-alive';
////			$header_array[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
////			$header_array[] = 'X-Requested-With: XMLHttpRequest';
////			$header_array[] = 'X-PHX: true';
////			$header_array[] = 'Referer: https://api.twitter.com/p_receiver.html';
////			$header_array[] = 'Pragma: no-cache';
////			$header_array[] = 'Cache-Control: no-cache';				
////			$tempResult = parent::get_curl_results($url,$postData,false,'',$header_array);
////			$result = json_decode($tempResult);
////			
////			$debug_text = "\n ------Add User In List Result ------- Total Time :  ".((float)(microtime(true) - $start_time))." \n";
////			$debug_text .= "\n List ID = ".$list_id;
////			$debug_text .= "\n User Added = ".$screen_name;
////			$debug_text .= "\n Result = ".serialize($result);
////			parent::saveDebugContent($username,$debug_text);
////						
////			return $result;		
////	}
//
//
//public function updateStoreLists($content,$username,$list_name,$user_id,$max=10){
//	$file = SITE_PATH.'listslog/';
//	$allFollowStartTime = microtime(true);
//	$logText = '';
//	//$screenName = trim(strtolower($screenName));
//	$followingFileContent = '';
//	$count = 1;
//	
//	 $listExist = parent::listExist($username,$list_name);
//	 $list_id = 0;
//	 if(is_null($listExist)){
//		$create_result = $this->createList($user_id,$list_name,$username);
//		if(isset($create_result->id)){
//			$list_id = $create_result->id;
//			parent::addList($username,$list_name);
//		}
//		else{
//			$logText .= date("Y-m-d, H:i:s").' List Name = '.$list_name." List creation failed \n\n";
//		}
//	 }
//	 else{
//	 	$logText .= date("Y-m-d, H:i:s").' List Name = '.$list_name." List Already exist \n\n";
//	 }
//	
//	foreach ($content as $res) {
//		if($count>$max){
//			break;
//		 }
//	
//
//		 $userExist = parent::userExistInList($username,$list_name,$res['screen_name']);
//		 $list_exist = parent::listExist($username,$list_name);
//		 if(is_null($userExist)){
//			 if(!is_null($list_exist)){
//				 $addResult = $this->addUserInList($list_name,$user_id,$res['screen_name'],$username);
//				 if(isset($addResult->id)){
//					 parent::addUserInList($username,$list_name,$res['screen_name']);
//					 $count++;
//				 }
//				 else{
//				 	$logText .= date("Y-m-d, H:i:s").' List Name = '.$list_name.", User = ".$res['screen_name'].", User addition in list failed \n\n";
//				 }
//			 }
//			 else{
//			 	$logText .= date("Y-m-d, H:i:s").' List Name = '.$list_name.", User = ".$res['screen_name'].", List do not exist \n\n";
//			 }
//		 }
//		 else{
//	 		$logText .= date("Y-m-d, H:i:s").' List Name = '.$list_name.", User = ".$res['screen_name'].", User Already exist in list \n\n";
//		 }
//	}
//	
//	parent::saveLog($file,$username,$logText);
//	
//}
//
//	
//public function updateStoreFollowingList($content,$screenName,$key,$max=10,$val = '1'){
//	
//	$allFollowStartTime = microtime(true);
//	$errorText = '';
//	$debug_text = '';
//	//$screenName = trim(strtolower($screenName));
//	$followingFile = SITE_PATH.'following/'.$screenName.'-follow.txt';
//	$followingFileContent = '';
//	$count = 1;
//	foreach ($content as $res) {
//		$start_time = microtime(true);
//		if($count>$max){
//			break;
//		 }
//		
//		 if(intval($res['default_profile_image']) == 1){
//			$errorText .= $res['screen_name']." Image = ".$res['profile_image_url']." | User has defult iamge \n";// follow request failed			
//			echo "<br> default_profile_image => ".$res['screen_name'];
//			
//			continue;
//		 }
//		 elseif(parent::checkDefaultImage($res['profile_image_url'],$screenName,$res['screen_name'])){
//		 	$errorText .= $res['screen_name']." Image = ".$res['profile_image_url']." | User has defult iamge \n";// follow request failed
//			echo "<br> profile_image_url => ".$res['screen_name'];
//			continue;
//		 }
//		$followResult = $this->followUser($screenName,$res['screen_name'],$res['id']);
//		echo "<br>Follow Result <br>";
//		print_r($followResult);
//		
//		$debug_text .= "\n ------ Follow Result -------\n Process Time =  ".((float)(microtime(true) - $start_time));
//		$debug_text .= "\n User = ".$res['screen_name'];
//		$debug_text .= "\n User Id = ".$res['id'];
//	    $debug_text .= "\n Result = ".serialize($followResult);
//		
//		if(is_null($followResult)){
//			$errorText .= $res['screen_name']."|204, Already Followed \n";// already followed
//		}
//		elseif(!isset($followResult->id)){
//			$errorText .= $res['screen_name']."|205, Follow REquest Failed \n";// follow request failed
//		}elseif($followResult == 'no-found'){
//			$errorText .= $res['screen_name']."|207, Follow user not found \n";// follow request failed
//		}
//		
//		if(isset($followResult->id) && intval($followResult->id)>0){		
//			$tempArray = array('id'=>$followResult->id,'screen_name'=>$followResult->screen_name);
//			$this->appendFollowersList($tempArray);				
//			if(!empty($followingFileContent)){
//				$strPosition = strpos(strtolower($followingFileContent),strtolower($followResult->screen_name.':'));
//				  if($strPosition === false && $followResult->screen_name!='' ){
//					$followingFileContent .= strtolower($followResult->screen_name.':'.$val.':'.date('Ymd').';');
//					$count++;
//				 }
//			}
//			else{
//				$followingFileContent .= strtolower($followResult->screen_name.':'.$val.':'.date('Ymd').';');
//				$count++;
//			}
//		}
//	}
//	if($followingFileContent) {
//		file_put_contents($followingFile, $followingFileContent,FILE_APPEND);
//		parent::saveLog(SITE_PATH.'followlog/',$screenName," \n USer Followed = ".$followingFileContent." \n");	
//	}
//		
//	parent::saveDebugContent($screenName,$debug_text);
//	parent::saveLog(SITE_PATH.'followlog/',$screenName,$errorText);	
//	parent::saveDebugContent($screenName," Total Time for Following User :  ".((float)(microtime(true) - $allFollowStartTime)));
//	return $errorText;
//	
//}
//
//
//public function updateStoreFollowingList_bulk($content){
//	if(!empty($content['user_username'])){
//		$file_name = SITE_PATH.'following/'.$content['user_username'].'-follow.txt';
//		$file_content = array();
//		$followingFileContent = "";
//		if(file_exists($file_name)){
//			$followingFileContent = file_get_contents($file_name);
//			$file_content = explode(";",$followingFileContent);
//		}
//		if(!parent::user_exists_file($content['screen_name'],$content['user_username'],$file_content)){
//			$followResult = $this->followUserBulk($content['user_username'],$content['screen_name'],$content['id']);		
//			if(isset($followResult->id) && intval($followResult->id)>0){
//				$strPosition = strpos(strtolower($followingFileContent),strtolower($followResult->screen_name.':'));
//				if($strPosition === false && $followResult->screen_name!='' ){
//					$store_text = strtolower($followResult->screen_name.':1:'.date('Ymd').';');
//					echo "\n Followed = ".$store_text." -- User =  ".$content['user_username'] ." \n ";
//					file_put_contents($file_name, $store_text,FILE_APPEND);
//				 }
//			}
//		}
//		else{
//			echo "\n User Already Followed =  ".$content['screen_name']." \n";			
//		}
//	}
//	
//}
//
//public function updateUnfollowList($content,$screenName){
//	$screenName = trim(strtolower($screenName));
//	$file = SITE_PATH.'following/'.$screenName.'-follow.txt';
//	$followingFileContent = '';
//	if(file_exists($file)){
//		$followingFileContent = file_get_contents($file);
//	}
//	
//	foreach ($content as $res) {
//		$strPosition = strpos(strtolower($followingFileContent),strtolower($res['search_text']));
//		  if($strPosition !== false && $res['search_text']!='' ){		  	
//			$followingFileContent = str_replace($res['search_text'],$res['replace_text'],$followingFileContent);
//		 }
//	}
//	if($followingFileContent) {
//		file_put_contents($file, $followingFileContent);
//	}
//	
//}
//
//public function decapcha($viewstate){
//	$imageUrl = $this->getCapchaImage($viewstate);
//	$this->globalImageURL = $imageUrl;
//	$ccp = new ccproto();
//	$ccp->init();
//	if( $ccp->login( DE_HOST, DE_PORT, DE_USERNAME, DE_PASSWORD ) < 0 ) {
//		$error = 'Decapcher login failed.';
//	} 
//	else{
//		$pictureText = '';
//		$major_id	= 0;
//		$minor_id	= 0;
//		for( $i = 0; $i < 3; $i++ ) {
//			echo ' <br> Image URL = '.$imageUrl;
//			echo "<br>";
//			$pict = file_get_contents( $imageUrl );
//			//$pict = $imageUrl;
//			$text = '';
//			print( "sending a picture..." );
//	
//			$pict_to	= ptoDEFAULT;
//			$pict_type	= ptUNSPECIFIED;
//			
//			$res = $ccp->picture2( $pict, $pict_to, $pict_type, $text, $major_id, $minor_id );
//			switch( $res ) {
//				// most common return codes
//				case ccERR_OK:
//					$pictureText = $text;
//					$error = "got text for id=".$major_id."/".$minor_id.", type=".$pict_type.", to=".$pict_to.", text='".$text."'";
//					break;
//				case ccERR_BALANCE:
//					$error = "not enough funds to process a picture, balance is depleted";
//					break;
//				case ccERR_TIMEOUT:
//					$error = "picture has been timed out on server (payment not taken)";
//					break;
//				case ccERR_OVERLOAD:
//					$error = "temporarily server-side error \n server's overloaded, wait a little before sending a new picture";
//					break;
//			
//				// local errors
//				case ccERR_STATUS:
//					$error = "local error. \n either ccproto_init() or ccproto_login() has not been successfully called prior to ccproto_picture() \n need ccproto_init() and ccproto_login() to be called";
//					break;
//			
//				// network errors
//				case ccERR_NET_ERROR:
//					$error = "network troubles, better to call ccproto_login() again";
//					break;
//			
//				// server-side errors
//				case ccERR_TEXT_SIZE:
//					$error = "size of the text returned is too big";
//					break;
//				case ccERR_GENERAL:
//					$error = "server-side error, better to call ccproto_login() again";
//					break;
//				case ccERR_UNKNOWN:
//					$error = "unknown error, better to call ccproto_login() again";
//					break;
//			
//				default:
//					// any other known errors?
//					break;
//			}
//			
//			if(!empty($pictureText)){
//				break;
//			}
//			
//			if(!empty($error)){
//				$error .= " | Create Account | Capcha does not match \n";
//				echo '<br> Capcha Error = '.$error."<br>";
//			}
//			// process a picture and if it is badly recognized 
//			// call picture_bad2() to name it as error. 
//			// pictures named bad are not charged
//	
//			//$ccp->picture_bad2( $major_id, $minor_id );
//		}//end of for loop
//		$ccp->close();
//		return  $pictureText;
//	}
//}
//
//public function getSats($html){
//	$stats = array();
//	$stats['suspended'] = 0;
//	if($this->checkSuspeneded($html)){
//		$stats['suspended'] = 1;
//	}else{
//		$stats['following_count'] = trim($this->getFollowingCount($html));
//		$stats['follow_count'] = trim($this->getFollowCount($html));
//		$stats['list_count'] = trim($this->getListedCount($html));
//		$stats['tweets_count'] = trim($this->getTweetsCount($html));
//	}
//	
//	return $stats;
//}
//
//public function checkSuspeneded($html){
//	$pos = strpos(strtolower($html),strtolower('twitter.com/account/suspended'));
//	if($pos !== false){
//		return true;
//	}	
//	return false;
//}
//
//public function getFollowingCount($html) {
//       	
//		$html_parse = str_get_html($html);		
//		$count = str_replace('Following','',$html_parse->find('ul[class=user-stats clearfix] a[class=user-stats-count user-stats-following]',0)->plaintext);
//		$html_parse->clear();
//        $count = parent::removeCommaFromCount($count);
//	   /* $count = 0;
//         preg_match('/\<span id="following_count" class="stats_count numeric"\>(.*?)\<\/span\>/si', $html, $match);
//        if (isset($match[1])) {
//            $count = parent::removeCommaFromCount($match[1]);
//        }*/
//		
//        return $count;
//}
//	public function getFollowCount($html) {
//		$html_parse = str_get_html($html);		
//		$count = str_replace('Followers','',$html_parse->find('ul[class=user-stats clearfix] a[class=user-stats-count user-stats-followers]',0)->plaintext);
//		$html_parse->clear();
//        $count = parent::removeCommaFromCount($count); 
//      /*  $count = 0;
//        preg_match('/\<span id="follower_count" class="stats_count numeric"\>(.*?)\<\/span\>/si', $html, $match);
//		if (isset($match[1])) {
//           $count = parent::removeCommaFromCount($match[1]);
//        }*/
//        return $count;
//    }
//	public function getListedCount($html) {
//		$html_parse = str_get_html($html);
//		
//		$count = str_replace('Listed','',$html_parse->find('ul[class=user-stats clearfix] a[class=user-stats-count user-stats-listed]',0)->plaintext);
//		$html_parse->clear();
//        $count = parent::removeCommaFromCount($count);       
//	   
//      /*  $count = 0;
//        preg_match('/\<span id="lists_count" class="stats_count numeric"\>(.*?)\<\/span\>/si', $html, $match);
//		if (isset($match[1])) {
//           $count = parent::removeCommaFromCount($match[1]);
//        }*/
//        return $count;
//    }
//	public function getTweetsCount($html) {
//		$html_parse = str_get_html($html);
//		$count = str_replace('Tweets','',$html_parse->find('ul[class=user-stats clearfix] a[class=user-stats-count user-stats-tweets]',0)->plaintext);
//		$html_parse->clear();
//        $count = parent::removeCommaFromCount($count);       
//	   
//       /* $count = 0;
//        preg_match('/\<span id="update_count" class="stat_count"\>(.*?)\<\/span\>/si', $html, $match);
//		if (isset($match[1])) {
//            $count = parent::removeCommaFromCount($match[1]);
//        }*/
//        return $count;
//    }
//	
//	
//
//
//
//	public function logout($screen_name){
//		$postAuthToken = $this->getPostAuthToken($screen_name);
//		$url = $this->twitterURL.'logout';
//		$postData = 'authenticity_token='.$postAuthToken;
//		$result = parent::get_curl_results($url,$postData);
//	}
//
//   
//
}
//
        ?>