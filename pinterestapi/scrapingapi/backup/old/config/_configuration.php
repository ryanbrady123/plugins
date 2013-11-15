<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
//error_reporting(5);

$SITE_PATH = "";
$SITE_URL = '';
$MAX_RECORDS = 100;

	// for Development	
	define( 'DB_HOST',"localhost");	
	define( 'DB_USER',"twitter_test");	
	define( 'DB_PASSWORD',"twitter_p4ss");
	define( 'DB_NAME',"twitter-test");
	define( 'SITE_URL',"http://test-twitter-manager.fs-manager.duribl.com/");
	define( 'SITE_PATH',"/var/www/html/virtual/fs-manager/test-twitter-manager.fs-manager.duribl.com/");
	
	// for Production
	/*define( 'DB_HOST',"localhost");	
	define( 'DB_USER',"twitter_user");	
	define( 'DB_PASSWORD',"twitter_p4ss");
	define( 'DB_NAME',"twitter");
	define( 'SITE_URL',"http://twitter-manager.fs-manager.duribl.com/");
	define( 'SITE_PATH',"/var/www/html/virtual/fs-manager/twitter-manager.fs-manager.duribl.com/");*/
	
	
	/** Decapture **/
	
	define( 'DE_HOST',		"api.decaptcher.com"	);	// YOUR HOST
	define( 'DE_PORT',		15041		);	// YOUR PORT
	define( 'DE_USERNAME',	"grizzlyatoms"	);	// YOUR LOGIN
	define( 'DE_PASSWORD',	"V14bleC4sh");	// YOUR PASSWORD
	define( 'DEBUG',1);	// For Debugging 1 = debugging on, 0 = debuggin off
	/** username and password for email API to confirm email from Twitter */
	define( 'CONFIRM_EMAIL_API_USER','api');
	define( 'CONFIRM_EMAIL_API_PASS','pass007849');
	
	/** Error Messages  */
	define( 'PROXY_ERROR',-2);
	define( 'CAPCHA_LOGIN_ERROR',-3);
	define( 'CAPCHA_IMAGE_ERROR',-4);
	define( 'PROFILE_CREATE_ERROR',-5);
	define( 'LOGIN_FAILED_ERROR',-6);
	define( 'DUBLICATE_TWEET_ERROR',-7);
	define( 'TWEET_ERROR',-8);
	define( 'UNKNOWN_RESPONSE_ERROR',-9);
	//define( 'PROXY_ERROR',-2);
	//define( 'PROXY_ERROR',-2);
	//define( 'PROXY_ERROR',-2);
	
	
	
	
	//require("../classes/Database.inc.php");
	//include("../classes/Database.inc.php");
	include(SITE_PATH."classes/Database.inc.php");
	$db = new Database(DB_HOST,DB_NAME,DB_USER,DB_PASSWORD);
	$db2 = new Database(DB_HOST,DB_NAME,DB_USER,DB_PASSWORD);	

	//$rec_perpage = 10;
	
	
	$max_record  = 10;
	$rec_perpage = 10;
?>