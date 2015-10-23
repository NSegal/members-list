<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			tern_wp_members.php
//		Description:
//			This file initializes the Wordpress Plugin - Members List Plugin
//		Actions:
//			1) list members
//			2) search through members
//			3) perform administrative actions
//		Date:
//			Added on January 29th 2009
//		Copyright:
//			Copyright (c) 2010 Matthew Praetzel.
//		License:
//			This software is licensed under the terms of the GNU Lesser General Public License v3
//			as published by the Free Software Foundation. You should have received a copy of of
//			the GNU Lesser General Public License along with this software. In the event that you
//			have not, please visit: http://www.gnu.org/licenses/gpl-3.0.txt
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('admin_menu','tern_wp_members_menu');
//scripts & stylesheets
add_action('init','tern_wp_members_styles');
add_action('init','tern_wp_members_js');
add_action('wp_print_scripts','tern_wp_members_js_root');
//hide new members
add_action('user_register','tern_wp_members_hide');
//short code
add_shortcode('members-list','tern_wp_members_shortcode');
//errors
add_action('init','WP_members_list_errors');
//                                *******************************                                 //
//________________________________** MENUS                     **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_members_menu() {
	if(function_exists('add_menu_page')) {
		add_menu_page('Members List','Members List',10,__FILE__,'WP_members_list_settings');
		add_submenu_page(__FILE__,'Members List','Settings',10,__FILE__,'WP_members_list_settings');
		add_submenu_page(__FILE__,'Change Sort Fields','Change Sort Fields',10,'members-list-configure-sort','WP_members_list_sort_fields');
		add_submenu_page(__FILE__,'Change Search Fields','Change Search Fields',10,'members-list-configure-search','WP_members_list_search_fields');
		add_submenu_page(__FILE__,'Configure Mark-Up','Configure Mark-Up',10,'members-list-configure-mark-up','WP_members_list_markup');
		add_submenu_page(__FILE__,'Edit Members','Edit Members',10,'members-list-edit-members-list','WP_members_list_list');
		add_submenu_page(__FILE__,'Create a List','Create a List',10,'members-list-create-list','WP_members_list_create');
	}
}
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_members_styles() {
	if(!is_admin()) {
		wp_enqueue_style('tern_wp_members_css',get_bloginfo('wpurl').'/wp-content/plugins/members-list/css/members-list.css');
	}
}
function tern_wp_members_js() {
	if(!is_admin()) {
		wp_enqueue_script('members-list',get_bloginfo('wpurl').'/wp-content/plugins/members-list/js/scripts.js',array('jquery'));
	}
}
function tern_wp_members_js_root() {
	echo '<script type="text/javascript">var tern_wp_root = "'.get_bloginfo('home').'";</script>'."\n";
}
function tern_wp_members_hide($i) {
	global $getWP,$tern_wp_members_defaults;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	if($o['hide'] and !in_array($i,$o['hidden'])) {
		$o['hidden'][] = $i;
		$o = $getWP->getOption('tern_wp_members',$o,true);
	}
}
//                                *******************************                                 //
//________________________________** SHORTCODE                 **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function tern_wp_members_shortcode($a) {
	$m = new tern_members;
	return $m->members($a,false);
}
//                                *******************************                                 //
//________________________________** ERRORS                    **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_errors() {
	global $getWP,$tern_wp_members_defaults;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);

	//if(!$o['url'] or empty($o['url'])) {
	//	$getWP->addError('Please remember to select a page in your Members List settings. Otherwise, many of the Members List plugin\'s featured will not work.');
	//}
	
	$getWP->renderErrors();
}
//                                *******************************                                 //
//________________________________** FUNCTIONS                 **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_is_in_list($u,$l) {
	$m = get_user_meta($u,'_tern_wp_member_list');
	$m = is_array($m) ? $m : array($m);
	
	foreach($m as $v) {
		if($v == $l) {
			return true;
		}
	}

	return false;
}
function WP_members_list_get_users_by_role($r) {
	global $wpdb;
	
	foreach($r as $v) {
		$x .= empty($x) ? " $wpdb->usermeta.meta_value LIKE '%$v%' " : " or $wpdb->usermeta.meta_value LIKE %'$v'% ";
	}
	return $wpdb->get_results("select ID from $wpdb->users inner join $wpdb->usermeta on($wpdb->users.ID = $wpdb->usermeta.user_id) where $wpdb->usermeta.meta_key='$wpdb->prefix"."capabilities' and ($x)");  
}

/****************************************Terminate Script******************************************/
?>