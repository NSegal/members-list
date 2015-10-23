<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			wordpress.php
//		Description:
//			This is a generic class for dealing with various Wordpress plugin tasks.
//		Actions:
//			1) get/set/update Wordpress options
//			2) serve posts and post related items
//			3) handle Wordpress errors
//		Date:
//			Created April 21st, 2009 for Wordpress
//		Version:
//			2.0.3
//		Copyright:
//			Copyright (c) 2010 Matthew Praetzel.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA) 
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit: http://www.ternstyle.us/terncal/license.txt
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

//                                *******************************                                 //
//________________________________** WORDPRESS                 **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
global $getWP;
if(!class_exists('ternWP')) {
//
class ternWP {

	var $errors = array();
	var $alerts = array();

//                                *******************************                                 //
//________________________________** OPTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
	function getOption($n,$d='',$v=false) {
		$o = get_option($n);
		if(!isset($o) and !empty($d)) {
			add_option($n,$d);
		}
		elseif(isset($o) and (empty($o) or $v) and !empty($d)) {
			update_option($n,$d);
		}
		elseif(isset($o) and !empty($d)) {
			foreach($d as $k => $v) {
				if(!isset($o[$k])) {
					$o[$k] = $v;
				}
			}
			update_option($n,$o);
		}
		return get_option($n);
	}
	function updateOption($n,$d,$w) {
		global $tern_wp_msg;
		$o = $this->getOption($n,$d);
		if(wp_verify_nonce($_REQUEST['_wpnonce'],$w) and $_REQUEST['action'] == 'update' and current_user_can('administrator')) {
			$f = new parseForm('post','_wp_http_referer,_wpnonce,action,submit,page,page_id');
			foreach($o as $k => $v) {
				if(is_string($v)) {
					$f->a[$k] = preg_match("/^[0-9]+$/",$f->a[$k]) ? (int)$f->a[$k] : $f->a[$k];
				}
				if(!isset($f->a[$k])) {
					$f->a[$k] = $v;
				}
			}
			return $this->getOption($n,$f->a,true);
			$tern_wp_msg = empty($tern_wp_msg) ? 'You have successfully updated your settings.' : $tern_wp_msg;
		}
		else {
			return $this->getOption($n,$d);
		}
	}
//                                *******************************                                 //
//________________________________** POSTS                     **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
	function postByName($n) {
		global $wpdb;
		return $wpdb->get_var("select ID from $wpdb->posts where post_name='$n'");
	}
	function the_content($c,$m=false,$s=0) {
		global $more;
		//
		if(!$m) {
			$m = __('(more...)');
		}
		$o = '';
		$h = false;
		//
		if(preg_match('/<!--more(.*?)?-->/',$c,$r)) {
			$c = explode($r[0],$c,2);
			if(!empty($r[1]) && !empty($m)) {
				$m = strip_tags(wp_kses_no_null(trim($r[1])));
			}
			$h = true;
		}
		else {
			$c = array($c);
		}
		//
		if(($more) && ($s) && ($h)) {
			$teaser = '';
		}
		else {
		 $o .= $c[0];
		}
		$o .= $teaser;
		if(count($c) > 1) {
			if($more) {
				$o .= '<span id="more-' . $id . '"></span>' . $c[1];
			}
			else {
				if(!empty($m)) {
					$o .= apply_filters('the_content_more_link',' <a href="'.get_permalink()."#more-$id\" class=\"more-link\">$m</a>",$m);
				}
				$o = force_balance_tags($o);
			}

		}
		if($preview) {
			$o = preg_replace_callback('/\%u([0-9A-F]{4})/',create_function('$r','return "&#" . base_convert($r[1], 16, 10) . ";";'),$o);
		}
		return $o;
	}
//                                *******************************                                 //
//________________________________** ERRORS                    **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
	function addError($e) {
		$this->errors[] = $e;
	}
	function renderErrors() {
		//global $notice;
		$notice = '';
		foreach($this->errors as $v) {
			$notice .= '<p>'.$v.'</p>';
		}
		return $notice;
	}
	function addAlert($e) {
		$this->alerts[] = $e;
	}
	function renderAlerts() {
		$notice = '';
		foreach($this->alerts as $v) {
			$notice .= '<p>'.$v.'</p>';
		}
		return $notice;
	}

}
$getWP = new ternWP;
//
}
	
/****************************************Terminate Script******************************************/
?>