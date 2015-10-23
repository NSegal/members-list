<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			conf.php
//		Description:
//			This file configures the Wordpress Plugin - Members List
//		Actions:
//			1) initialize pertinent variables
//			2) load classes and functions
//		Date:
//			Added on June 12th 2010
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
//________________________________** INITIALIZE VARIABLES      **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
$tern_wp_members_defaults = array(
	'limit'		=>	10,
	'sort'		=>	'last_name',
	'sorts'		=>	array('Last Name'=>'last_name','First Name'=>'first_name','Registration Date'=>'user_registered','Email'=>'user_email'),
	'order'		=>	'asc',
	'meta'		=>	'',
	'url'		=>	false,
	'gravatars'	=>	1,
	'hide_email'	=>	0,
	'hide'		=>	0,
	'hidden'	=>	array(0),
	'noun'		=>	'members',
	'searches'	=>	array('Last Name'=>'last_name','First Name'=>'first_name','Description'=>'description','User Name'=>'user_nicename','Email'=>'user_email','Display Name'=>'display_name','URL'=>'user_url'),
	'fields'	=>	array(
		'User Name'		=>	array(
			'name'		=>	'user_nicename',
			'markup'	=>	'<div class="tern_wp_members_user_nicename"><h3><a href="%author_url%">%value%</a></h3></div>'
		),
		'Email Address'	=>	array(
			'name'		=>	'user_email',
			'markup'	=>	'<div class="tern_wp_members_user_email"><a href="mailto:%value%">%value%</a></div>'
		),
		'URL'			=>	array(
			'name'		=>	'user_url',
			'markup'	=>	'<div class="tern_wp_members_user_url"><a href="%value%">%value%</a></div>'
		)
	),
	'lists'		=>	array(),
	'allow_display'	=>	0
);
$tern_wp_meta_fields = array(
	'Last Name'		=>	'last_name',
	'First Name'	=>	'first_name',
	'Description'	=>	'description'
);
$tern_wp_members_fields = array(
	'User Name'		=>	'user_nicename',
	'Email Address'	=>	'user_email',
	'Display Name'	=>	'display_name',
	'URL'			=>	'user_url',
	'Registration Date'	=>	'user_registered'
);
$tern_wp_user_fields = array('ID','user_login','user_pass','user_nicename','user_email','user_url','user_registered','user_activation_key','user_status','display_name');
//                                *******************************                                 //
//________________________________** FILE CLASS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
require_once(dirname(__FILE__).'/class/file.php');
$getFILE = new fileClass;
//                                *******************************                                 //
//________________________________** LOAD CLASSES              **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
$l = $getFILE->directoryList(array(
	'dir'	=>	dirname(__FILE__).'/class/',
	'rec'	=>	true,
	'flat'	=>	true,
	'depth'	=>	1
));
if(is_array($l)) {
	foreach($l as $k => $v) {
		require_once($v);
	}
}
//                                *******************************                                 //
//________________________________** INITIALIZE INCLUDES       **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
$l = $getFILE->directoryList(array(
	'dir'	=>	dirname(__FILE__).'/core/',
	'rec'	=>	true,
	'flat'	=>	true,
	'depth'	=>	1
));
if(is_array($l)) {
	foreach($l as $k => $v) {
		require_once($v);
	}
}

/****************************************Terminate Script******************************************/
?>