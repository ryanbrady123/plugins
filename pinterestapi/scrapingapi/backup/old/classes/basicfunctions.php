<?php 
//	require_once(dirname(__FILE__)."/../config/configuration.php");
	require_once(SITE_PATH."classes/common.class.php");
//	require_once(SITE_PATH."classes/accounts.class.php");
//	require_once(SITE_PATH."classes/options.class.php");
//	require_once(SITE_PATH."classes/emailapi.class.php");
	require_once(SITE_PATH."classes/pinterest.class.php");
//	require_once(SITE_PATH."classes/mass_scheduler.class.php");
//	require_once(SITE_PATH."classes/super_mass_scheduler.class.php");
//	require_once(SITE_PATH."classes/bitly.class.php");
//	require_once(SITE_PATH."classes/emailapi.class.php");
//	require_once(SITE_PATH."classes/simplepie.class.php");
//	require_once(SITE_PATH."classes/unique.class.php");
	require_once(SITE_PATH."classes/simple_html_dom.php");
//	require_once(SITE_PATH."classes/mass_follow.class.php");
//	require_once(SITE_PATH."classes/mass_unfollow.class.php");
//	require_once(SITE_PATH."classes/twitter.class.php");
//	require_once(SITE_PATH."classes/repin_mass_scheduler.class.php");
//	require_once(SITE_PATH."classes/super_repin_mass_scheduler.class.php");
//	require_once(SITE_PATH."classes/like_mass_scheduler.class.php");
//	require_once(SITE_PATH."classes/comment_mass_scheduler.class.php");
//	require_once(SITE_PATH."classes/decapcha/ccproto_client.php");
//	require_once(SITE_PATH."classes/madapi.class.php");
//	require_once(SITE_PATH."classes/direct_pins.class.php");
//	require_once(SITE_PATH."classes/direct_repins.class.php");
	
	/*
	require_once(SITE_PATH."classes/scheduletweets.class.php");
	require_once(SITE_PATH."classes/tweets.class.php");	
	
	require_once(SITE_PATH."classes/unfollow.class.php");
	require_once(SITE_PATH."classes/follow.class.php");
	require_once(SITE_PATH."classes/lists.class.php");*/
	
	class BasicFunctions{
		
		public $common, $bitly, $scheduleTweets, $tweets, $accounts, $twitter, $options,$lists,$db2,$simple_pie,$simple_html_dom;
		private $user_details;
		private $authnticity_token;
		private $proxy,$max_failure;
		
		
		function __construct() {
//			global $db2;
			$common = new Common();
			//$bitly = new Bitly();
			//$scheduleTweets = new Scheduletweets();
//			$options = new Options();
//			$db2->connect();
//			$this->db2 =& $db2;
			$this->proxy = '';
		}
		
		function __destruct() {
//		  	$this->db2->close();
		}
		
		function update_code_field_error($acc,$field_name,$current_code,$error_code){		
			$accounts = new Accounts();
			$sql = '';
			if($acc[$field_name] >= ($current_code+MAX_FAILURE)){
				$sql = $accounts->update_field_status($acc['username'],$field_name,$error_code);
			}else{
				$sql = $accounts->update_field_status($acc['username'],$field_name,$current_code+1);
			}
			$this->db2->execute($sql);
		}
		
		function authenticate($acc){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			$auth_token = "";
			$html = $objPin->get_pinterest_page($acc);
			$cookie_name =  strtolower($acc['username']);
			
			if(!empty($html) || !is_null($html)){
				$result = '';					
				$is_logged_in = $objPin->is_already_loggedin($html);
				if($is_logged_in){
					$auth_token = $objPin->get_auth_token($html);
					echo 'auth_token = '.$auth_token;
					$this->db2->execute("UPDATE accounts SET login_active = 0 WHERE username  = '".$acc['username']."'");
					return $auth_token;
				}
				else{
					unlink(SITE_PATH."cookies/".$cookie_name.".txt");
					$html = $objPin->get_login_page($acc);
					$auth_token = $objPin->get_auth_token($html);
					$result = $objPin->make_login($acc,$auth_token);					
					if(empty($result)){
						$this->update_erros_codes($acc,'login_active',EMPTY_RESPONSE);
					}
					else{
						if(strpos($result,"Are You a Human?") !== false){
							$this->common =  new Common();
							
							/*$redrtect_url = 'http://pinterest.com/login/?next=%2F';
							$last_result = $this->common->get_curl_results($redrtect_url, null, false,$cookie_name,$acc['proxy'],null,'','');
							
							$debug_text = "\n ------ last_url -------\n ";
							$debug_text .= "\n URL = ".$this->common->get_last_url();
							$debug_text .= "\n Result = ".$last_result;
							$this->common->saveDebugContent($acc['username'],$debug_text);
							unset($debug_text);*/
							
							$capcha_url = "http://www.google.com/recaptcha/api/challenge?k=6LdYxc8SAAAAAHyLKDUP3jgHt11fSDW_WBwSPPdF&ajax=1&cachestop=0.9569200263535518";			
							$capcha_result = $this->common->get_curl_results($capcha_url, null, false,$cookie_name,$acc['proxy'],null,'','');
							
							
							
							$pattern = "/challenge : '(.*?)'/is";
							preg_match_all($pattern,$capcha_result,$match);
							
							$debug_text = "\n ------ capcha_url -------\n ";
							$debug_text .= "\n URL = ".$capcha_url;
							$debug_text .= "\n Match = ".serialize($match);
							$debug_text .= "\n Result = ".$capcha_result;
							$this->common->saveDebugContent($acc['username'],$debug_text);
							unset($debug_text);
							
							print_r($match);
							if(isset($match[1][0]) && !empty($match[1][0])){
								
								$image_text = $objPin->get_decapcha($acc,$match[1][0]);
								echo "\n\n Image Text = ";
								echo $image_text;
								echo "\n\n";
								$debug_text = "\n ------ capcha_result -------\n ";
								$debug_text .= "\n View State = ".$match[1][0];
								$debug_text .= "\n Image Text = ".$image_text;
								
								if(!empty($image_text)){
									//http://pinterest.com/verify_captcha/?src=register&return=%2Fwelcome%2F
									$post_data = "challenge=".$match[1][0]."&response=".urlencode($image_text);
									
									$redrtect_url = "http://pinterest.com/verify_captcha/?return=%2F%3F";
									
									$header_array = array();									
									$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
									//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
									$header_array[] = 'Accept-Language: en-us,en;q=0.5';
									$header_array[] = 'Host: pinterest.com';
									$header_array[] = 'Connection: keep-alive';
									$header_array[] = 'X-CSRFToken: '.$auth_token;
									$header_array[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
									$header_array[] = 'Referer: '.$redrtect_url;
									
									
									$result = $this->common->get_curl_results($redrtect_url, $post_data, TRUE,$cookie_name,$acc['proxy'],$header_array,'','');
									$debug_text .= "\n URL = ".$redrtect_url;
									$debug_text .= "\n Auth Token  = ".$auth_token;
									$debug_text .= "\n Post Data = ".$post_data;
									$debug_text .= "\n Result = ".$result;
								}
								else{
									$this->db2->execute("UPDATE accounts SET login_active = ".CAPCHA_IMAGE_ERROR." WHERE username  = '".$acc['username']."'");
								}
								
								$this->common->saveDebugContent($acc['username'],$debug_text);
								unset($debug_text);
							}
							else{
								$this->db2->execute("UPDATE accounts SET login_active = ".CAPCHA_IMAGE_ERROR." WHERE username  = '".$acc['username']."'");
							}
						
						}
						if($objPin->is_already_loggedin($result)){
							echo 'auth_token = '.$auth_token;
							$this->db2->execute("UPDATE accounts SET login_active = 0 WHERE username  = '".$acc['username']."'");
							return $auth_token;
						}
					}
				}
			}
			else{
				$this->update_erros_codes($acc,'login_active',EMPTY_RESPONSE);
				return false;
			}
			return false;
		}
		
		function send_invitation($acc,$auth_token,$request_accounts){
			$objPin = new Pinterest();
			foreach($request_accounts as $rer_acc){
				$response = $objPin->send_invitation($acc,$auth_token,$rer_acc['email']);
				echo "\n Response = ";
				print_r($response);
				echo "\n";
				if($response['status'] == "success"){
					echo "\n Invitantion Sent!";
					echo "\n From User = ".$acc['username'];
					echo "\n To User = ".$rer_acc['username'];
					echo "\n To Email = ".$rer_acc['email'];
					
					echo "\n";
					$time = date('Y-m-d H:i:s',time()+(CHECK_EMAIL_TIME *60 ));
					echo $sql = "UPDATE accounts SET created = 100, send_request = ".SUCCESS_CODE." , last_run = '".$time."',requested_by = '".$acc['username']."' WHERE username = '".$rer_acc['username']."'";
					echo "\n";
					$this->db2->execute($sql);
				}
				else{
					echo "\n Invitantion Failed!";
					echo "\n From User = ".$acc['username'];
					echo "\n To User = ".$rer_acc['username'];
					echo "\n To Email = ".$rer_acc['email'];
					echo "\n";
					//echo $sql = "UPDATE accounts SET created = 100, last_run = NOW() WHERE username = '".$rer_acc['username']."'";
					echo "\n";
					//$error_code = PROXY_ERROR;
					//$this->update_code_field_error($rer_acc,'created',$rer_acc['created'],$error_code);
					//$this->db2->execute($sql);
				}
			}
		}
		
		
		function post_pin($acc, $pin, $auth_token,$add_board = false,$is_repin = false){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			
			if($add_board){
				// current boards
				$boards_and_cat = explode("||",trim($acc['board']));
				$board_all = explode(",",$boards_and_cat[0]);
				$cat_name = $boards_and_cat[1];
				$single_board = trim($board_all[array_rand($board_all,1)]);
				// user boards
				$board_to_create = null;
					
				$this->db2->query("SELECT board_name FROM accounts where username  = '".$acc['username']."'");
				$user_boards_db = $this->db2->fetch_all_assoc();
				
				$user_boards = trim($user_boards_db[0]['board_name']);
				$temp_boards = explode("|??|",$user_boards);
				$user_boards = $temp_boards;
				
				foreach($user_boards as $u_b){
					$u_b = trim($u_b);
					if(!empty($u_b)){
						$user_board_detail = explode("||",$u_b);
						//echo "\n " .strtolower(trim($user_board_detail[0])) ."   ==   ". strtolower($single_board) ." \n";
						if(strtolower(trim($user_board_detail[0])) == strtolower($single_board)){
							echo "<br>Board Found In Accounts Table!";
							echo "<br>User = ".$acc['username'];
							echo "<br>Board Name = ".$single_board;
							echo "<br>Board Id = ".$user_board_detail[2];
							echo "<br>";
							$board_to_create['board_name'] = $single_board;
							$board_to_create['board_id'] = $user_board_detail[2];
							break;
						}
					}
				}
				
				
				if(empty($board_to_create) && count($board_to_create) == 0){
					$board_to_create['board_name'] = $single_board;
					$board_to_create['cat_name'] = $cat_name;
					$board_res = $objPin->create_board($acc, $board_to_create, $auth_token);
					if($board_res['status'] == "success"){
						echo "<br>New Board Created!";
						echo "<br>Board Found In Accounts Table!";
						echo "<br>User = ".$acc['username'];
						echo "<br>Board Name = ".$board_to_create['board_name'];
						echo "<br>Board Id = ".$board_res['id'];
						echo "<br>";
						$board_to_create['board_id'] = $board_res['id'];
						$edit_board_str = "";
						if(empty($user_boards_db)){
							$edit_board_str = $single_board."||".$cat_name."||".$board_res['id'];
						}
						else{
							$edit_board_str = trim($user_boards_db[0]['board_name'])."|??|".$single_board."||".$cat_name."||".$board_res['id'];
						}
						echo "<br>";
						echo $add_board_sql = "UPDATE accounts SET board_name = '".$edit_board_str."' WHERE username = '".$acc['username']."'";
						echo "<br>";
						$this->db2->execute($add_board_sql);
					}
					else{
						echo "\n Create Board Failed !\n";
						echo "\n Board = ".$board_to_create['board_name']."\n";
						echo "\n User = ".$acc['username']."\n";
						echo "\n Message = ".$board_res['message']."\n";
					}
				}
				
				if(isset($board_to_create['board_id']) && !empty($board_to_create['board_id'])){
					$pin['board_id'] = $board_to_create['board_id'];
					if($is_repin){
						$pin_data = $objPin->get_repin_data($acc,$pin, $auth_token);
						$pin['pin_detail'] = $pin_data['details'];
						$response = $objPin->do_re_pin($acc, $pin, $auth_token);
						print_r($response);
						if($response['status'] == "success"){
							echo "\n Re-Pin Posted !\n";
							echo "\n Board = ".$board_to_create['board_name']."\n";
							echo "\n User =  ".$acc['username']."\n";
							echo "\n URL =  http://pinterest.com".$response['repin_url']."\n";
							//echo "\n URL =  http://pinterest.com/overmixercrisp".$response['url']."\n";
							return $response;
						}
						else{
							echo "\n Re-Pin Failed! \n";
							echo "\n Board = ".$board_to_create['board_name']."\n";
							echo "\n User =  ".$acc['username']."\n";
							echo "\n Error =  ".$response['message']."\n";
							return false;
						}
					}
					else{
						$response = $objPin->add_pin($acc, $pin, $auth_token);
						echo "\n\n Pin REsponse \n\n";
						print_r($response);
						if($response['status'] == "success"){
							echo "\n Pin Posted !\n";
							echo "\n Board = ".$board_to_create['board_name']."\n";
							echo "\n User =  ".$acc['username']."\n";
							echo "\n URL =  http://pinterest.com".$response['url']."\n";
							return $response;
						}
						else{
							if($response['captcha']){
								
								$json_response = json_decode($objPin->capcha_request($acc,$auth_token,"http://pinterest.com/verify_captcha/"),true);
								echo "\n\n capcha_request REsponse \n\n";
								print_r($json_response);
								if($json_response['status'] == "success" ){
								
									$response = $objPin->add_pin($acc, $pin, $auth_token);
									echo "\n\n Pin REsponse \n\n";
									print_r($response);
									if($response['status'] == "success"){
										echo "\n Pin Posted !\n";
										echo "\n Board = ".$board_to_create['board_name']."\n";
										echo "\n User =  ".$acc['username']."\n";
										echo "\n URL =  http://pinterest.com".$response['url']."\n";
										return $response;
									}
									else{
										echo "\n Failed! \n";
										echo "\n Board = ".$board_to_create['board_name']."\n";
										echo "\n User =  ".$acc['username']."\n";
										echo "\n Error =  ".$response['message']."\n";
										return false;
									}
								}
								
							}
							else{
								echo "\n Failed! \n";
								echo "\n Board = ".$board_to_create['board_name']."\n";
								echo "\n User =  ".$acc['username']."\n";
								echo "\n Error =  ".$response['message']."\n";
								return false;
							}
						}
					}
				}
			}
			else{
				$html = $objPin->add_pin($acc, $pin, $auth_token);
				if($html['status'] == "success"){
					echo "\n Pin Posted !\n";
					echo "\n Board = ".$board_to_create['board_name']."\n";
					echo "\n User =  ".$acc['username']."\n";
					return $html;
				}
				else{
					echo "\n Failed! \n";
					echo "\n Board = ".$board_to_create['board_name']."\n";
					echo "\n User =  ".$acc['username']."\n";
					return false;
				}
			}
			return false;
						
		}
		
		function post_repin($acc, $pin, $auth_token,$add_board = false){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			
			if($add_board){
				// current boards
				$boards_and_cat = explode("||",trim($acc['board']));
				//echo "<br> boards_and_cat = <br>";
				//print_r($boards_and_cat);
				$board_all = explode(",",$boards_and_cat[0]);
				//echo "<br> board_all = <br>";
				//print_r($board_all);
				$cat_name = $boards_and_cat[1];
				$single_board = trim($board_all[array_rand($board_all,1)]);
				//echo "<br> single_board = ".$single_board."<br>";
				//echo "<br> cat_name = ".$cat_name."<br>";
				// user boards
				$board_to_create = null;
					
				$this->db2->query("SELECT board_name FROM accounts where username  = '".$acc['username']."'");
				$user_boards_db = $this->db2->fetch_all_assoc();
				
				$user_boards = trim($user_boards_db[0]['board_name']);
				$temp_boards = explode("|??|",$user_boards);
				$user_boards = $temp_boards;
				
				//echo "<br> user_boards = <br>";
				//print_r($user_boards);
				
				foreach($user_boards as $u_b){
					$u_b = trim($u_b);
					if(!empty($u_b)){
						$user_board_detail = explode("||",$u_b);
						//echo "\n " .strtolower(trim($user_board_detail[0])) ."   ==   ". strtolower($single_board) ." \n";
						if(strtolower(trim($user_board_detail[0])) == strtolower($single_board)){
							echo "<br>Board Found In Accounts Table!";
							echo "<br>User = ".$acc['username'];
							echo "<br>Board Name = ".$single_board;
							echo "<br>Board Id = ".$user_board_detail[2];
							echo "<br>";
							$board_to_create['board_name'] = $single_board;
							$board_to_create['board_id'] = $user_board_detail[2];
							break;
						}
					}
				}
				
				
				if(empty($board_to_create) && count($board_to_create) == 0){
					$board_to_create['board_name'] = $single_board;
					$board_to_create['cat_name'] = $cat_name;
					
					//echo "<br> board_to_create = <br>";
					//print_r($board_to_create);
					
					$board_res = $objPin->create_board($acc, $board_to_create, $auth_token);
					if($board_res['status'] == "success"){
						echo "<br>New Board Created!";
						echo "<br>Board Found In Accounts Table!";
						echo "<br>User = ".$acc['username'];
						echo "<br>Board Name = ".$board_to_create['board_name'];
						echo "<br>Board Id = ".$board_res['id'];
						echo "<br>";
						$board_to_create['board_id'] = $board_res['id'];
						$edit_board_str = "";
						if(empty($user_boards_db)){
							$edit_board_str = $single_board."||".$cat_name."||".$board_res['id'];
						}
						else{
							$edit_board_str = trim($user_boards_db[0]['board_name'])."|??|".$single_board."||".$cat_name."||".$board_res['id'];
						}
						echo "<br>";
						echo $add_board_sql = "UPDATE accounts SET board_name = '".$edit_board_str."' WHERE username = '".$acc['username']."'";
						echo "<br>";
						$this->db2->execute($add_board_sql);
					}
					else{
						echo "\n Create Board Failed !\n";
						echo "\n Board = ".$board_to_create['board_name']."\n";
						echo "\n User = ".$acc['username']."\n";
						echo "\n Message = ".$board_res['message']."\n";
					}
				}
				
				if(isset($board_to_create['board_id']) && !empty($board_to_create['board_id'])){
					$pin['board_id'] = $board_to_create['board_id'];
					//$pin_data = $objPin->get_repin_data($acc,$pin, $auth_token);
					$response = $objPin->do_re_pin($acc, $pin, $auth_token);
					if($response['status'] == "success"){
						echo "\n Re-Pin Posted !\n";
						echo "\n Board = ".$board_to_create['board_name']."\n";
						echo "\n User =  ".$acc['username']."\n";
						echo "\n URL =  http://pinterest.com".$response['repin_url']."\n";
						//echo "\n URL =  http://pinterest.com/overmixercrisp".$response['url']."\n";
						return $response;
					}
					else{
						echo "\n Re-Pin Failed! \n";
						echo "\n Board = ".$board_to_create['board_name']."\n";
						echo "\n User =  ".$acc['username']."\n";
						echo "\n Error =  ".$response['message']."\n";
						return false;
					}
				}
			}
			return false;
						
		}
		
		function post_like($acc, $pin, $auth_token){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			//$pin_data = $objPin->get_repin_data($acc,$pin, $auth_token);
			$response = $objPin->do_like($acc, $pin, $auth_token);
			if($response['status'] == "success"){
				echo "\nLike Posted !\n";
				echo "\n User =  ".$acc['username']."\n";
				echo "\n Pin =  ".$pin['pin_id']."\n";
				echo "\n URL =  http://pinterest.com/pin/".$pin['pin_id']."\n";
				//echo "\n URL =  http://pinterest.com/overmixercrisp".$response['url']."\n";
				return $response;
			}
			else{
				echo "\n Like Failed! \n";
				echo "\n User =  ".$acc['username']."\n";
				echo "\n Pin =  ".$pin['pin_id']."\n";
				echo "\n Error =  ".$response['message']."\n";
				return false;
			}
			return false;
						
		}
		
		function post_comment($acc, $pin, $auth_token){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			//$pin_data = $objPin->get_repin_data($acc,$pin, $auth_token);
			$response = $objPin->do_comment($acc, $pin, $auth_token);
			if($response['status'] == "success"){
				echo "\n Comment Posted !\n";
				echo "\n User =  ".$acc['username']."\n";
				echo "\n Pin =  ".$pin['pin_id']."\n";
				echo "\n Comment =  ".$pin['template']."\n";
				echo "\n URL =  http://pinterest.com/pin/".$pin['pin_id']."\n";
				//echo "\n URL =  http://pinterest.com/overmixercrisp".$response['url']."\n";
				return $response;
			}
			else{
				echo "\n Comment Failed! \n";
				echo "\n User =  ".$acc['username']."\n";
				echo "\n Pin =  ".$pin['pin_id']."\n";
				echo "\n Error =  ".$response['message']."\n";
				return false;
			}
			return false;
						
		}
		
		function follow_multiple($acc, $follow, $auth_token){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			$max_users = $follow['count'];
			$html_array = $objPin->search_user($acc, $follow);
			$reached_max = false;
			$all_users = array();
			foreach($html_array as $html){
				$users = $this->parse_users($acc,$html);
				if(count($all_users) < $max_users){
					foreach($users as $u){
						$all_users[] = $u;
						if(count($all_users) >= $max_users){
							$reached_max = true;
							break;
						}
					}
				}
				if($reached_max){
					break;
				}
			}
			unset($html_array);
			$success_users = array();
			if(count($all_users)>0){
				$url = "http://pinterest.com/search/people/?q=".$this->common->make_url($follow['params']);
				$this->common =  new Common();
				foreach($all_users as $user){
					$follow_result = json_decode($objPin->follow_user($acc,$user,$auth_token,$url),true);
					print_r($follow_result);
					if($follow_result['status'] == "success"){
						$success_users[] = $user;
					}
				}
			}
			return $success_users;
						
		}
		
		function follow_single($acc, $follow_user, $auth_token){
			$objPin = new Pinterest();
			$this->common =  new Common();
			$url = "http://pinterest.com/search/people/?q=".$this->common->make_url($acc['param']);
			
			$follow_result = json_decode($objPin->follow_user($acc,$follow_user,$auth_token,$url),true);
//			echo "\n";
//			print_r($follow_result);
			if($follow_result['status'] == "success"){
//				echo "\n Follow Success! ";
//				echo "\n User = ".$acc['username'];
//				echo "\n User to follow  = ".$follow_user;
				return true;
			}
			else{
				
				if($follow_result['captcha']){
					echo 'Captcha Needed for account '.$acc['username'];
					/*$json_response = json_decode($objPin->capcha_request($acc,$auth_token,"http://pinterest.com/verify_captcha/"),true);
					echo "\n\n capcha_request REsponse \n\n";
					print_r($json_response);			
					echo "\n\n capcha_request \n\n";
					
					$debug_text = "\n ------ follow_user capcha_request esponmse-------\n";
					$debug_text .= "\n Result = ".serialize($json_response);
					parent::saveDebugContent($acc['username'],$debug_text);
					unset($debug_text);
					
					if($json_response['status'] == "success" ){
						$follow_result = json_decode($objPin->follow_user($acc,$follow_user,$auth_token,$url),true);
						echo "\n\n Follow REsponse \n\n";
						print_r($response);
						if($follow_result['status'] == "success"){
							echo "\n Follow Success! ";
							echo "\n User = ".$acc['username'];
							echo "\n User to follow  = ".$follow_user;
							return true;
						}
						else{
							echo "\n Follow Failed! ";
							echo "\n User = ".$acc['username'];
							echo "\n User to follow  = ".$follow_user;
							echo "\n Message  = ".$follow_result['message'];
							return false;
						}
						
					}*/			
				}
				
				echo "\n Follow Failed! ";
				echo "\n User = ".$acc['username'];
				echo "\n User to follow  = ".$follow_user;
				echo "\n Message  = ".$follow_result['message'];
				return false;
			}
			return false;
		}
		
		function un_follow_single($acc, $follow_user, $auth_token){
			$objPin = new Pinterest();
			$this->common =  new Common();
			
			$follow_result = json_decode($objPin->un_follow_user($acc,$follow_user,$auth_token),true);
			
			if($follow_result['status'] == "success"){
				echo "\n Un-Follow Success! ";
				echo "\n User = ".$acc['username'];
				echo "\n User to follow  = ".$follow_user;
				return true;
			}
			else{
				echo "\n Un-Follow Failed! ";
				echo "\n User = ".$acc['username'];
				echo "\n User to un-follow  = ".$follow_user;
				echo "\n Message  = ".$follow_result['message'];
				return false;
			}
			return false;
		}
		
		function search_users_pins($acc,$follow,$keyword){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			$max_users = $follow['count'];
			$html_array = $objPin->search_user_pin($acc, $keyword);
			$reached_max = false;
			$all_users = array();
			foreach($html_array as $html){
				$users = $this->parse_users_pin($acc,$html);
                                $all_users = array_merge($all_users, $users);
                                $all_users = array_unique($all_users);
				if(count($all_users) >= $max_users){
					break;
				}
			}
                        $all_users = array_slice($all_users, 0, $max_users);
			unset($html_array);
			return $all_users;
		}
                
                function search_users_category($acc,$follow,$keyword){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			$max_users = $follow['count'];
			$html_array = $objPin->search_user_category($acc, $keyword);
			$reached_max = false;
			$all_users = array();
			foreach($html_array as $html){
				$users = $this->parse_users_pin($acc,$html);
                                $all_users = array_merge($all_users, $users);
                                $all_users = array_unique($all_users);
				if(count($all_users) >= $max_users){
					break;
				}
			}
                        $all_users = array_slice($all_users, 0, $max_users);
			unset($html_array);
			return $all_users;
		}
                
                function search_users_followers_deep($acc, $users, $deep_count) {
			$all_users = $users;
                        foreach ($users as $user) {
                            $cnt = array('username' => $user, 'proxy' => $acc['proxy']);
                            $page_count = ceil($deep_count/50);
                            $res = $this->get_all_folower($cnt, $page_count);
                            foreach($res as $key => $usr) {
                                if(!$this->check_user_not_exists($acc['username'],$usr)) {
                                    unset($res[$key]);
                                }
                            }
                            if(count($res) > $deep_count) {
                                $res = array_slice($res, 0, $deep_count);
                            }
                            $all_users = array_merge($all_users, $res);
                        }
                        $all_users = array_unique($all_users);
                        return $all_users;
		}
                
                function search_users_followings_deep($acc, $users, $deep_count) {
			$all_users = $users;
                        foreach ($users as $user) {
                            $cnt = array('username' => $user, 'proxy' => $acc['proxy']);
                            $page_count = ceil($deep_count/50);
                            $res = $this->get_all_folowing($cnt, $page_count);
                            foreach($res as $key => $usr) {
                                if(!$this->check_user_not_exists($acc['username'],$usr)) {
                                    unset($res[$key]);
                                }
                            }
                            if(count($res) > $deep_count) {
                                $res = array_slice($res, 0, $deep_count);
                            }
                            $all_users = array_merge($all_users, $res);
                        }
                        $all_users = array_unique($all_users);
                        return $all_users;
		}
		
		function search_users_board($acc,$follow,$keyword){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			$max_users = $follow['count'];
			$html_array = $objPin->search_user_board($acc, $keyword);
			$reached_max = false;
			$all_users = array();
			foreach($html_array as $html){
				$users = $this->parse_users_board($acc,$html);
				if(count($all_users) < $max_users){
					foreach($users as $u){
						$all_users[] = $u;
						if(count($all_users) >= $max_users){
							$reached_max = true;
							break;
						}
					}
				}
				if($reached_max){
					break;
				}
			}
			unset($html_array);
			return $all_users;
						
		}
		
		function search_users($acc,$follow,$keyword){
			$objPin = new Pinterest();
			$loggedScreenName = "";
			$max_users = $follow['count'];
			$html_array = $objPin->search_user($acc, $keyword);
			$reached_max = false;
			$all_users = array();
			foreach($html_array as $html){
				$users = $this->parse_users($acc,$html);
				if(count($all_users) < $max_users){
					foreach($users as $u){
						$all_users[] = $u;
						if(count($all_users) >= $max_users){
							$reached_max = true;
							break;
						}
					}
				}
				if($reached_max){
					break;
				}
			}
			unset($html_array);
			return $all_users;
		}
		
		function create_all_boards($acc, $auth_token){
			$objPin = new Pinterest();
			$board_detail = $this->separate_accounts_boards($acc['board_name']);
			if(!empty($board_detail)){
				$final_array = array();
				foreach($board_detail as $board){
					$board_info = $objPin->create_board($acc,$board,$auth_token);
					if($board_info['status'] == "success"){
						$final_array[] = $board['board_name']."||".$board['cat_name']."||".$board_info['id'];
					}
				}
			}
			if(!empty($final_array)){
				$all_boards_info = implode("|??|",$final_array);
				$sql = "UPDATE accounts SET pin_active = ".SUCCESS_CODE." ,board_active = ".SUCCESS_CODE.", board_name = '".$all_boards_info."' WHERE username = '".$acc['username']."'";
				$this->db2->execute($sql);
			}
			
			return $final_array;
		}
		
		function parse_users($acc,$html){
			$pattern = '/\<a class="ImgLink" href="(.*?)"\>\<img.*?src="(.*?)" \/\>/si';
			//<a class="ImgLink" href="/xeec83/"><img alt="Xee Chue" src="http://media-cdn.pinterest.com/avatars/xeec83_1330017852_o.jpg" /></a>
			preg_match_all($pattern,$html,$match);			
			$result = array();
			if(isset($match[1]) && count($match[1])>0){
				$this->common =  new Common();
				foreach($match[1] as $key => $m){
					$friend_name = strtolower(str_replace("/","",$m));
					$is_not_default = $this->common->checkDefaultImage($match[2][$key],$acc['username'],$friend_name);
					$db_user_not_exist = $this->check_user_not_exists($acc['username'],$friend_name);
					if($is_not_default && $db_user_not_exist){						
						$result[] = $friend_name;
					}
				}
			}
			return $result;
		}
		
		function parse_users_pin($acc,$html_result){
			$html = new simple_html_dom();
			$html = null;	
			$html = str_get_html($html_result);
			$result = array();
			$this->common =  new Common();
			foreach($html->find('div[id=ColumnContainer] div[class=pin]') as $g){
				$friend_name = trim($g->find('div[class=convo attribution clearfix] a', 0)->href);
				$friend_image = trim($g->find('div[class=convo attribution clearfix] img', 0)->src);
				$friend_name = str_replace("/","",$friend_name);
				if(!empty($friend_name)){
					$is_not_default = $this->common->checkDefaultImage($friend_image,$acc['username'],$friend_name);
					$db_user_not_exist = $this->check_user_not_exists($acc['username'],$friend_name);
					if($is_not_default && $db_user_not_exist){						
						$result[] = $friend_name;
					}
				}
				
			}
			$html->clear();
			return $result;
		}
		
		function parse_single_user_pin($html_result,$max){
			$html = new simple_html_dom();
			$html = null;	
			$html = str_get_html($html_result);
			$result = array();
			$this->common =  new Common();
			$count = 1;
			foreach($html->find('div[id=ColumnContainer] div[class=pin]') as $g){
				$pin_id = trim($g->find('a[class=PinImage ImgLink]', 0)->href);
				$pin_id = str_replace(array("/","pin"),"",$pin_id);				
				if(!empty($pin_id)){		
					$desc = addslashes(trim($g->find('p[class=description]', 0)->innertext));
					if(empty($desc)){
						$desc = "";
					}
					$pin_detail = array('pin_id' => $pin_id, 'pin_detail' => $desc) ;
					$result[]= $pin_detail;
					if($count>=$max){
						break;
					}
					$count ++;
				}
			}
			$html->clear();
			return $result;
		}
		
		function parse_users_board($acc,$html_result){
			$html = new simple_html_dom();
			$html = null;	
			$html = str_get_html($html_result);
			$result = array();
			$this->common =  new Common();
			foreach($html->find('div[id=ColumnContainer] li div[class=pin pinBoard]') as $g){
				$friend_name = trim($g->find('h4[class=user] a', 0)->href);
				$friend_image = trim($g->find('h4[class=user] img', 0)->src);
				$friend_name = str_replace("/","",$friend_name);
				if(!empty($friend_name)){
					$is_not_default = $this->common->checkDefaultImage($friend_image,$acc['username'],$friend_name);
					$db_user_not_exist = $this->check_user_not_exists($acc['username'],$friend_name);
					if($is_not_default && $db_user_not_exist){						
						$result[] = $friend_name;
					}
				}
				
			}
			$html->clear();
			return $result;
		}
		
		function check_user_not_exists($username,$follow_name){
			
			$this->common =  new Common();
			$table_info = $this->common->get_follow_table_name($username);

			$sql = "SELECT id,follow_name,status,date FROM follow_table_".$table_info['table_name']." WHERE username = '".$username."' AND follow_name = '".$follow_name."' LIMIT 1";
			$this->db2->query($sql);
			$user_exists_check = $this->db2->fetch_all_assoc();
			if(count($user_exists_check) == 0){
				echo "\n User Not in DB = ".$follow_name."    Username  = ".$username."\n";
				return true;
			}
			echo "\n User Already in DB = ".$follow_name."    Username  = ".$username."\n";
			return false;
		}
		
		function separate_accounts_boards($string){
			$array = explode("|??|",$string);
			$return_array = array();
			foreach($array as $arr){
				$temp = explode("||",$arr);
				$return_array[] = array('board_name' => $temp[0],'cat_name' => $temp[1]);
			}
			return $return_array;
		}
		
		
		function get_counts($acc){
			$objPin = new Pinterest();
			$html_result = $objPin->get_public_profile_page($acc);
			$result = array();
			if(!empty($html_result)){
				$html = new simple_html_dom();
				$html = null;	
				$html = str_get_html($html_result);
				
				$result['followers_count'] = intval(trim(str_replace("followers","",strip_tags($html->find('div[id=ContextBar] ul[class=follow] li a', 0)->innertext))));
				$result['following_count'] = intval(trim(str_replace("following","",strip_tags($html->find('div[id=ContextBar] ul[class=follow] li a', 1)->innertext))));
				$result['total_pages'] = ceil($result['followers_count']/50);
				$html->clear();
			}
			
			return $result;
		}
		
                function get_all_folowing($acc,$page_count){
			$objPin = new Pinterest();
			$html_array = $objPin->get_all_followings($acc,$page_count);
                        $all_users = array();
			foreach($html_array as $html){
				$users = $this->parse_follow_page($html);
				foreach($users as $u){
					$all_users[] = $u;
				}
			}
			unset($html_array);
			
			return $all_users;
		}
                
		function get_all_folower($acc,$page_count){
			$objPin = new Pinterest();
			$html_array = $objPin->get_all_followers($acc,$page_count);
			$all_users = array();
                        foreach($html_array as $html){
                            	$users = $this->parse_follow_page($html);
                                foreach($users as $u){
					$all_users[] = $u;
				}
			}
			unset($html_array);
			return $all_users;
		}
		
		function parse_follow_page($page_html){
			$followers_list = array();
                        
			$html = new simple_html_dom();
                        $html = null;	
			$html = str_get_html($page_html);
			
			foreach($html->find('div[id=PeopleList] div[class=person]') as $g){
				$user_name = trim($g->find('div[class=PersonInfo] a', 0)->href);
				$followers_list[] = str_replace("/","",$user_name);
			}
			$html->clear();
			unset($page_html);
			return $followers_list;
		}
		
		
		function authenticate_twitter($acc){
			$loggedScreenName = "";
			$cookie_name = strtolower($acc['username']);
		
			$result = '';	
			$loggedScreenName = "";
			$twitter = new Twitter();
			$html = $twitter->getLoginPage($acc);
			if(empty($html)){
				$db->execute($objAccounts->update_field_status($acc['username'],'login_active',PROXY_ERROR));
				continue;
			}
			else{
				$loggedScreenName = trim($twitter->getScreenName($html,$acc['username']));
				if(empty($loggedScreenName)){
					$result = $twitter->autheticate($acc);
					echo '<br><br><textarea cols="100" rows="100">'.$result.'</textarea><br><br>';
					//$result = $twitter->getProfilePage();
					//echo '<br><br><textarea cols="100" rows="100">'.$result.'</textarea><br><br>';
					$loggedScreenName = trim($twitter->getScreenName($result,$acc['username']));
				}
				else{
					return strtolower(trim($loggedScreenName));
				}
					
				if(empty($loggedScreenName) && (strtolower($twitter->last_url) == strtolower('https://twitter.com/#!/login/captcha'))){
					echo '<br> Capcha URL = '.$twitter->last_url.'<br>';				
					$result = $twitter->authenticateWithCapcha($acc['username'], $acc['password'],$acc['proxy'],'',$cookie_name);
					$loggedScreenName = trim($twitter->getScreenName($result,$acc['username']));
				}
			}

			/**  old code   **/ 	
			/*// if capcha is there 
			//preg_match_all('/src="https:\/\/api-secure\.recaptcha\.net\/challenge\?k=(.*?)"/si',$result,$match);
			preg_match_all('/src="https:\/\/www\.google\.com\/recaptcha\/api\/image\?c=(.*?)"/si',$result,$match);
			//http://www.google.com/recaptcha/api/image?c=03AHJ_VusU4BJGl180JKE9GEctMQWVFFF6ybssJDqLqqTIRANhHYjetmvc9-pxqU0VEUZMi7TJaMnOMvbvTzQ4WV_YKzPbgQU5BFDzX4FoIYqHHxrfmUZCna3-OOeeJ7NRFGWL42Q9vuCozw6Efu8_IIhYDFYpu1hQew
			echo '<pre> Capctah = <br>';
			print_r($match);
			if(isset($match[1][0]) && !empty($match[1][0])){
				
				$viewstate = ($match[1][0]);
				$result = $twitter->authenticateWithCapcha($username, $passsword,$this->getProxy(),$viewstate,$cookie_name);
			}
			else{
				
			}*/
			echo "<br> Loged USer = ";
			echo $loggedScreenName;
			echo "<br>";
			return strtolower($loggedScreenName);
		}
		
		
		function update_profile($acc, $auth_token){
			$objPin = new Pinterest();
			$this->common =  new Common();			
			$update_result = $objPin->update_profile($acc,$auth_token);
			if(strpos($update_result,'onClick="Logout.logout(); return false;"') !== false || strpos($update_result,'href="/logout/"') !== false){
				if($acc['update_image'] != 200){
					$sql = "UPDATE accounts SET update_image =  ".SUCCESS_CODE ." WHERE username  = '".$acc['username']."'";
					$this->db2->execute($sql);
					echo "\n User = ".$acc['username']." \n ";
					echo "\n Image Succefully updated \ n";
				}
				if($acc['update_profile'] != 200){
					$sql = "UPDATE accounts SET update_profile =  ".SUCCESS_CODE." WHERE username  = '".$acc['username']."'";
					$this->db2->execute($sql);
					echo "\n User = ".$acc['username']." \n ";
					echo "\n Profile Succefully updated \ n";
				}
				unlink(SITE_PATH.$acc['image_path']);
			}
			elseif(empty($update_result)){
				if($acc['update_profile'] != 200){
					$this->update_erros_codes($acc,'update_profile',EMPTY_RESPONSE);
					echo "\n User = ".$acc['username']." \n ";
					echo "\n Profile updation failed! \ n";
				}
				if($acc['update_image'] != 200){
					$this->update_erros_codes($acc,'update_image',EMPTY_RESPONSE);
					echo "\n User = ".$acc['username']." \n ";
					echo "\n Image updation failed! \ n";
				}
			}
			else{
				if($acc['update_profile'] != 200){
					$this->update_erros_codes($acc,'update_profile',UNKNOWN_RESPONSE_ERROR);
					echo "\n User = ".$acc['username']." \n ";
					echo "\n Profile updation failed! \ n";
				}
				if($acc['update_image'] != 200){
					$this->update_erros_codes($acc,'update_image',UNKNOWN_RESPONSE_ERROR);
					echo "\n User = ".$acc['username']." \n ";
					echo "\n Image updation failed! \ n";
				}
			}
		}
		
		
		function convert_to_pins($post,$feeds){
			$result = array();
			$count = 1;
			foreach($feeds as $f){
				$pin_detail = array('pin_id' => $f['title'], 'pin_detail' => $f['description']) ;
				$result[]= $pin_detail;
				if($count>=$post['count']){
					break;
				}
				$count++;
			}
			return $result;
		}
		
		
		function update_stats($html,$acc){
			$stats_array = $this->get_stats($html,$acc);
			$stats_sql = "UPDATE accounts SET follower = ".$stats_array['followers']." , following = ".$stats_array['following']." , boards = ".$stats_array['boards']." , pins = ".$stats_array['pins'].", likes = ".$stats_array['likes']." , stats_date = NOW() WHERE username = '".$acc['username']."'";
			$this->db2->execute($stats_sql);
		}
		
		function get_stats($html_result,$acc){
			$html = new simple_html_dom();
			$html = null;	
			$html = str_get_html($html_result);
			$result = array();
			
			$result['boards'] = intval(trim(strip_tags($html->find('div[id=ContextBar] div[class=FixedContainer] ul[class=links] li',0)->find('a strong',0)->innertext)));
			$result['pins'] = intval(trim(strip_tags($html->find('div[id=ContextBar] div[class=FixedContainer] ul[class=links] li',1)->find('a strong',0)->innertext)));
			$result['likes'] = intval(trim(strip_tags($html->find('div[id=ContextBar] div[class=FixedContainer] ul[class=links] li',2)->find('a strong',0)->innertext)));
			$result['followers'] = intval(trim(strip_tags($html->find('div[id=ContextBar] div[class=FixedContainer] ul[class=follow] li',0)->find('a strong',0)->innertext)));
			$result['following'] = intval(trim(strip_tags($html->find('div[id=ContextBar] div[class=FixedContainer] ul[class=follow] li',1)->find('a strong',0)->innertext)));
			
			$html->clear();
			unset($html);
			
			return $result;
		}
		
		
		
		
		/////////////////////////////////////////////////////////////////////////////////
		/////////////////////////////////////////////////////////////////////////////////
		/////////////////////////////////////////////////////////////////////////////////
		/////////////////////////////////////////////////////////////////////////////////
		
		
		
		
		
			
		function updateLoginStatus($username,$login_active,$error_code){		
			$accounts = new Accounts();
			$sql = '';
			$accounts->setUsername($username);
			if($login_active+1 >= (150+$this->getMaxFailure())){
				$sql = $accounts->updateLoginActive($error_code);
			}else{
				$sql = $accounts->updateLoginActive($login_active+1);
			}
			$this->db2->execute($sql);
		}
		
		function update_erros_codes($acc,$field_name,$error_code, $error_index = 0){
			$obj_accounts = new Accounts();
			if($acc[$field_name]+1 >= ($error_index + MAX_FAILURE)) {
				$sql = $obj_accounts->update_field_status($acc['username'],$field_name,$error_code);
			}else{
				$sql = $obj_accounts->update_field_status($acc['username'],$field_name,$acc[$field_name]+1);
			}
			$this->db2->execute($sql);
		}
		
		
		
		function updateTweetStatus($task_id, $status = 0){
			$tweets = new Tweets();
			$tweets->setId($task_id);
			$tweets->setStatus($status);
			$this->db2->execute($tweets->updateStatusByIds());
		}
		
		
		
		
		function updateTweetErrorCode($id,$date,$error_code){
			$tweets = new Tweets();
			$tweets->setId($id);
			$tweets->setPublishDate($date);
			$tweets->setErrorCode($error_code);//success
			$this->db2->execute($tweets->updateErrorCode());
		}
		
		function insertTweet($username,$content,$publish_date,$error_code,$schedule_id){
			$tweets = new Tweets();
			$tweets->setUsername($username);
			$tweets->setContent(addslashes($content));
			$tweets->setPublishDate($publish_date);
			$tweets->setErrorCode($error_code);
			$tweets->setScheduleId($schedule_id);
			//echo '<br>'.$tweets->insertTweetWithErrorCode();
			//echo '<br>';
			$this->db2->execute($tweets->insertTweetWithErrorCode());
			//$sql ="INSERT INTO ".$this->tableName."(username,content,publish_date,error_code,schedule_id) VALUES('".$this->getUsername()."','".$this->getContent()."','".$this->getPublishDate()."',".$this->getErrorCode().",".$this->getScheduleId().")";
		}
		function updateConsecFailure($id,$error_code){
			$scheduleTweets = new Scheduletweets();
			$scheduleTweets->setId($id);
			$this->db2->execute($scheduleTweets->updateConsecFailure($error_code));
		}
		
		function get_feeds($url,$proxy,$userrname,$type,$id,$file){
			$content = null;
			$this->common =  new Common();
			$simple_pie = new SimplePie();
			$cookie_name = $type."_".strtolower($userrname);
			$header_array = null;
			$user_agent = $this->common->get_user_agent();
			$proxy = $this->common->get_user_proxy();
			if($type == 'googleblogsearch' || $type == 'googleblogsearchcount'){
				$header_array[] = 'Host: www.google.com';
				$header_array[] = 'User-Agent: '.$user_agent;
				$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
				//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
				//$header_array[] = 'Accept-Encoding: gzip,deflate';
				//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
				$header_array[] = 'Connection: keep-alive';
				
				$content = $this->get_curl_results_source($url,$cookie_name,'http://www.google.com/',null,$proxy,$user_agent);
			}
			elseif($type == 'googlenews'  || $type == 'googlenewscount'){
				$header_array[] = 'Host: news.google.com';
				$header_array[] = 'User-Agent: '.$user_agent;
				//$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
				//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
				//$header_array[] = 'Accept-Encoding: gzip,deflate';
				//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
				$header_array[] = 'Connection: keep-alive';
				
				$content = $this->get_curl_results_source($url,$cookie_name,'http://www.google.com/',null,$proxy,$user_agent);
				
			}
			elseif($type == 'yahoonews'  || $type == 'yahoonewscount'){
				$header_array[] = 'Host: news.search.yahoo.com';
				$header_array[] = 'User-Agent: '.$user_agent;
				//$header_array[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
				//$header_array[] = 'Accept-Language: en-us,en;q=0.5';
				//$header_array[] = 'Accept-Encoding: gzip,deflate';
				//$header_array[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
				$header_array[] = 'Connection: keep-alive';
				
				$content = $this->get_curl_results_source($url,$cookie_name,'http://www.yahoo.com/',null,$proxy,$user_agent);
			}
			else{
				$content = $this->get_curl_results_source($url,$cookie_name,'',null,$proxy,$user_agent);
				//$content = $common->get_curl_results($url,null,false,null,null,null,$proxy);
			}
			
			$debug_text = "\n ------- ".$file." (XML) ------- \n";
			$debug_text .= "\n ID = ".$id;
			$debug_text .= "\n Type= ".$type;
			$debug_text .= "\n URL = ".$url;
			$debug_text .= "\n Result = ".$content;
			$this->common->saveDebugContent($userrname,$debug_text);
			
			if(trim($content) ==""){
					return "";
			}else{
				if($type == 'googleblogsearch' || $type == 'googleblogsearchcount'){
					$items = $content;
				}
				else{
					$simple_pie->set_raw_data($content);
					$simple_pie->init();
					$simple_pie->handle_content_type();
					$items = $simple_pie->get_items();
					
					$debug_text = "\n ------- ".$file." (After Parsing) ------- \n";
					$debug_text .= "\n ID = ".$id;
					$debug_text .= "\n Type= ".$type;
					$debug_text .= "\n URL = ".$url;
					$debug_text .= "\n Result= ".serialize($items);
					$this->common->saveDebugContent($userrname,$debug_text);
				}
				if(count($items)){
					return $items;
				}
				else{
					return null;
				}
			}
		}
		
		
		function get_curl_results_source($url,$cookie_name,$reffer,$header = array(),$currentProxy,$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12") {
		echo $url;
        //$agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12';
		//$agent = $this->getAgent();		
       	$cookie_file_path = SITE_PATH."cookies/".$cookie_name.'.txt';       
		if (!file_exists($cookie_name))
		{
            $fp = fopen($cookie_file_path, "wb");
            fclose($fp);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
		if (!is_null($header) || count($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
		if(is_null($header)){
        	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		}
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,REQUEST_TIMEOUT);
		curl_setopt($ch, CURLOPT_TIMEOUT,REQUEST_TIMEOUT);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        
		$proxy = $currentProxy;
        if (!is_null($proxy) && $proxy !='') {
			//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($ch, CURLOPT_REFERER, $reffer);
       /* if (!empty($cookie_file_path)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
        }*/
		
		
        $result = curl_exec($ch);
		if($result == false){			
			echo curl_error($ch);
			//$curlResult = $this->common->checkCurlResult(curl_error($ch),$proxy);
		}
		
		$info_array = curl_getinfo($ch);
        curl_close($ch);
       
        return $result;
    }
		
		
		
		function is_suspended($accounts_details,$username = ""){
			$accounts = new Accounts();
			$suspended = 0;
			if(array_key_exists('suspended',$accounts_details['requestCacheSeedData'][0]['json']['states'])){
			//if(isset($accounts_details['requestCacheSeedData'][0]['json']['states']['suspended'])){		
				$suspended = intval($accounts_details['requestCacheSeedData'][0]['json']['states']['suspended']);
			}
			/*else{
				$suspended = 1;
			}*/		
		
			if($suspended){
				$sql = $accounts->updateSuspended(-1,$username);// account is suspended
				$this->db2->execute($sql);
				return true;
			}
			else{
				return false;
			}
		}
		
	}
		

?>
