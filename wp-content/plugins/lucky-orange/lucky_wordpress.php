<?php

/*
Plugin Name: Lucky Orange | Chat, heatmaps, polls, visitor recordings, live analytics. 
Plugin URI: http://www.luckyorange.com/
Description: See heatmaps, live support chat, visitor polls, and visitor recordings. Look under Dashboard for "Lucky Orange". 
Version: 1.96
Author: LuckyOrange.com
Author URI: http://www.luckyorange.com
*/
/*  Copyright 2010  Brian Gruber  (email : support@luckyorange.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*****************
*  ADD HOOKS
******************/
// add tracking code hook

add_action('wp_head', 'lucky_orange_print_track_code');


// get the url for use of graphics 
//$wbc_wp_url = get_bloginfo('wpurl').'/wp-content/plugins/webby_chat/';

add_action('admin_menu', 'lucky_orange_menus');


function lucky_orange_print_main_page()
{
	
	echo '<iframe src="//luckyorange.com/view.php?wp=1&wp2=1&l=' . urlencode(get_bloginfo('url')) . '" width="100%" height="100%" style="min-height:850px; width:100%" frameborder=0 ></iframe>';
}

function lucky_orange_menus()
{
	
	// add to dashboard
	if (current_user_can('administrator')) {
	 add_menu_page( 'Lucky Orange', 'Lucky Orange', 'administrator', 'lucky_orange_slug', 'lucky_orange_print_main_page', 'https://www.luckyorange.com/graphics/16x16_orange.png',3);
	}
}
add_action('admin_menu', 'lucky_orange_menus');
register_activation_hook( __FILE__, 'lucky_orange_activate' );
register_deactivation_hook( __FILE__, 'lucky_orange_deactivate' );
register_uninstall_hook( __FILE__, 'lucky_orange_uninstall' );

function lucky_redirect_setup(){
if (get_option('Lucky_Needs_Setup')=='Plugin-Slug') {
	// show once
	delete_option('Lucky_Needs_Setup');
   
	// redirect
	header('location: admin.php?page=lucky_orange_slug');
	exit;
	}
}
//add_action('admin_notices', 'lucky_admin_notices');
lucky_redirect_setup();
function lucky_orange_uninstall()
{
	delete_option('Lucky_Needs_Setup');
	
	$type = 'wordpress_uninstall';
	$url = 'luckyorange.com/json.event.php?t=' . $type . '&o1=' . urlencode(site_url());
	track_url_curl($url);
}
function lucky_orange_activate()
{
	// tell lucky that plugin was activated
	 add_option('Lucky_Activate_Plugin','Plugin-Slug');
	 add_option('Lucky_Needs_Setup','Plugin-Slug');
	  
}
function lucky_orange_deactivate()
{
	// tell lucky that plugin was deactivated
	// add_option('Lucky_Deactivate_Plugin','Plugin-Slug');
	delete_option('Lucky_Needs_Setup');
	
	$type = 'wordpress_deactivate';
	$url = 'luckyorange.com/json.event.php?t=' . $type . '&o1=' . urlencode(site_url());
	track_url_curl($url);
}

add_action('admin_init','lucky_orange_load_plugin');

function lucky_orange_load_plugin() {
	
    if(is_admin())
	{
		if (get_option('Lucky_Activate_Plugin')=='Plugin-Slug') {
			delete_option('Lucky_Activate_Plugin');
			/* do stuff once right after activation */
			lucky_orange_track_event('wordpress_activate');
					
		}
		
	}
	
}

function track_url_curl($url)
{
	$ch = @curl_init(); // create cURL handle ( )
	if ( $ch && $url) {
		// curl okay
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		// grab URL and pass it to the browser
		curl_exec($ch);

		// close cURL resource, and free up system resources
		curl_close($ch);
	}	

}
function lucky_orange_track_event($type)
{
	$type = urlencode($type);
	
	
	 echo "<script>(function() {
		var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = false;
		wa.src = 'https://www.luckyorange.com/json.event.php?t=" . $type . "&o1=' + encodeURIComponent('" . site_url() . "');
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
	  })();
	  </script>
	  ";

   
}

function lucky_orange_print_track_code()
{
	// This function will output the BestContactForm tracking code near the bottom of the page.
	// Hooked Action: wp_footer 
	//
	global $current_user;
	get_currentuserinfo(); 

	
	echo "\r\n";
	echo "<script type='text/javascript'>
	";
	if ($current_user->user_login != '')
	{
		echo 'var __wtw_custom_name = "' . $current_user->user_login . '";';
	}
	
	echo "
	
(function() {
    var wa = document.createElement('script'); wa.type = 'text/javascript'; wa.async = true;
    wa.src = 'https://d10lpsik1i8c69.cloudfront.net/w.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(wa, s);
  })();
</script>";	

}
?>