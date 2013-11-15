<?php
/*
Plugin Name: HM Top Posts
Description: Top Posts. By Google Analytics.
Version: 0.1
Author: Human Made Limited
Author URI: http://hmn.md
*/

define( 'HMTP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

if ( defined( 'HMTP_DISABLE_GA_TOP_POSTS' ) && HMTP_DISABLE_GA_TOP_POSTS )
	return;

// The google analytics helper class.
if( ! class_exists( 'gapi' ) )
 	require_once( 'gapi.class.php' );

// All the admin settings pages.
require_once( 'hm-top-posts-admin.php' );

// Meta box to allow authors from opting out of top posts.
require_once( 'hm-top-posts-opt-out.php' );

// Get Top Posts from google analytics.
require_once( 'hm-top-posts-ga.php' );

/**
 * If there has been an error - show our message
 */
function hmtp_top_posts_error_messaages()
{

	$error = get_option( 'hmtp_top_posts_error_message' );

    if ( ! current_user_can('administrator' ) || ! $error )
    	return;

 	$message = '<strong>Top Posts by Google Analytics Error: ' . $error . '</strong>';

	?>
	<div id="message" class="error">
		<p><?php echo $message; ?></p>
	</div>
	<?php

	delete_option( 'hmtp_top_posts_error_message' );

}
add_action('admin_notices', 'hmtp_top_posts_error_messaages');