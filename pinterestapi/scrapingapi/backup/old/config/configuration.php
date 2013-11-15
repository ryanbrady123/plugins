<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
//error_reporting(5);

$SITE_PATH = "";
$SITE_URL = '';
$MAX_RECORDS = 100;
	
	define( 'DB_HOST',"localhost");	
	define( 'DB_USER',"twitterp_follow");	
	define( 'DB_PASSWORD',"pl@123");
	define( 'DB_NAME',"twitterp_pin");
	define( 'SITE_URL',"http://twitter.purelogics.info/pinterest/");
	define( 'SITE_PATH', realpath(dirname(__FILE__).'/../').'/');
	
	
	/** Decapture **/
	
	//define( 'DE_HOST',		"api.decaptcher.com"	);	// YOUR HOST
	//define( 'DE_HOST',		"api.de-captcher.com"	);	// YOUR HOST
	//define( 'DE_PORT',		15041		);	// YOUR PORT
	//define( 'DE_USERNAME',	"grizzlyatoms"	);	// YOUR LOGIN
	//define( 'DE_PASSWORD',	"V14bleC4sh");	// YOUR PASSWORD
	/** for bit.ly **/
	define( 'BITLY_API_USERNAME',	"usmanbitly"	);	// YOUR LOGIN
	define( 'BITLY_API_PASSWORD',	"R_c97939f045bebe9e421283b1e7dbab2f");	// YOUR PASSWORD
	
	define( 'SERVER_ID',1);	// for server to run crons on different server we have to provide server id
	
	/** Error Messages  */
	define( 'PROXY_ERROR',-2);
	define( 'CAPCHA_LOGIN_ERROR',-3);
	define( 'CAPCHA_IMAGE_ERROR',-4);
	define( 'PROFILE_CREATE_ERROR',-5);
	define( 'LOGIN_FAILED_ERROR',-6);
	define( 'DUBLICATE_TWEET_ERROR',-7);
	define( 'POST_ERROR',-8);
	define( 'UNKNOWN_RESPONSE_ERROR',-9);
	define( 'TWEET_LENGTH_EXCEEDED',-10);
	define( 'USERNAME_ALREADY_EXISTS',-11);
	define( 'EMAIL_ALREADY_EXISTS',-12);
	define( 'API_EMPTY_RESPONSE',-13);
	define( 'EMPTY_RESPONSE',-14);
	define( 'RETWEET_SHARING_ERROR',-15);
	define( 'PASSWORD_TOO_OBVIOUS',-16);	
	define( 'INVALID_UNICODE',-17);
	define( 'TWEET_ERROR_SUSPENDED',-18);
	define( 'EMPTY_AFTER_PROCESS',-19);
	define( 'TWITTER_OUT_OF_CAPACITY',-20);
	define( 'TWITTER_AUTH_FAILED',-21);
	
	// Success Code
	define( 'SUCCESS_CODE',200);
	
	define( 'TABLE_STRUCTURE',"CREATE TABLE IF NOT EXISTS `thread_%thread_id%` (  `content` TEXT NULL, `publish_date` DATETIME NULL,  `schedule_id` INT(12) NULL  ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM ROW_FORMAT=DEFAULT");
	
	define( 'TABLE_STRUCTURE_REPIN',"CREATE TABLE IF NOT EXISTS `repin_thread_%thread_id%` (  `content` TEXT NULL, `publish_date` DATETIME NULL,  `schedule_id` INT(12) NULL  ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM ROW_FORMAT=DEFAULT");
	
	define( 'TABLE_STRUCTURE_FOLLOW',"CREATE TABLE IF NOT EXISTS `follow_thread_%thread_id%` (  `content` TEXT NULL, `publish_date` DATETIME NULL,  `schedule_id` INT(12) NULL  ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM ROW_FORMAT=DEFAULT");
	
	define( 'TABLE_STRUCTURE_UN_FOLLOW',"CREATE TABLE IF NOT EXISTS `un_follow_thread_%thread_id%` (  `content` TEXT NULL, `publish_date` DATETIME NULL,  `schedule_id` INT(12) NULL  ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM ROW_FORMAT=DEFAULT");
	
	define( 'TABLE_STRUCTURE_USER_FOLLOW',"CREATE TABLE `follow_table_%range%` (  `id` INT(11) NULL AUTO_INCREMENT,  `username` VARCHAR(50) NULL DEFAULT NULL,  `follow_name` VARCHAR(50) NULL DEFAULT NULL,  `status` TINYINT(1) NULL DEFAULT NULL,  `date` DATE NULL DEFAULT NULL, PRIMARY KEY (`id`),  INDEX `username` (`username`),  INDEX `follow_name` (`follow_name`),  INDEX `status` (`status`),  INDEX `date` (`date`) ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM ROW_FORMAT=DEFAULT");
	
	
	
	//define( 'DOWNLOAD_FOLDER','downloads/');
	//define( 'STATS_FOLDER','stats/');
	//define( 'RESET_LOG_FOLDER','reset_log/');
	
	
	include(SITE_PATH."classes/Database.inc.php");
	$db = new Database(DB_HOST,DB_NAME,DB_USER,DB_PASSWORD);
	$db2 = new Database(DB_HOST,DB_NAME,DB_USER,DB_PASSWORD);


?>
