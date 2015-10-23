<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			settings.php
//		Description:
//			This file compiles and processes the plugin's various settings pages.
//		Actions:
//			1) compile overall plugin settings form
//			2) process and save plugin settings
//		Date:
//			Added on September 15th 2010
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
//________________________________** INITIALIZE                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
if(!isset($_GET['page']) or $_GET['page'] !== 'members-list/core/members-list.php') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_members_list_settings_actions');
add_action('init','WP_members_list_settings_styles');
add_action('init','WP_members_list_settings_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_settings_styles() {
	
	
}
function WP_members_list_settings_scripts() {
	
	
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_settings_actions() {
	global $getWP,$tern_wp_members_defaults,$current_user;
	get_currentuserinfo();
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	
	if($_REQUEST['action'] == 'update') {
		$_POST['meta'] = empty($_POST['meta']) ? '' : $_POST['meta'];
		$getWP->updateOption('tern_wp_members',$tern_wp_members_defaults,'tern_wp_members_nonce');
	}
	
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_settings() {
	global $getWP,$ternSel,$tern_wp_msg,$tern_wp_members_defaults,$tern_wp_members_fields,$tern_wp_meta_fields,$wpdb,$notice;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Members Settings</h2>
	<?php if(!empty($notice)) { ?><div id="notice" class="error"><p><?php echo $notice ?></p></div><?php } ?>
	<?php
		if(!empty($tern_wp_msg)) {
			echo '<div id="message" class="updated fade"><p>'.$tern_wp_msg.'</p></div>';
		}
	?>
	<form method="post" action="">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="noun">Use a word other than "Members" on the front-end</label></th>
				<td>
					<input type="text" name="noun" class="regular-text" value="<?php echo $o['noun']; ?>" />
					<span class="setting-description">i.e. "Clients" or "Users"</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="limit">Number of viewable members at one time</label></th>
				<td>
					<?php
						$a = array('5','10','15','20','25','50','100','200');
						echo $ternSel->create(array(
							'type'			=>	'select',
							'data'			=>	$a,
							'id'			=>	'limit',
							'name'			=>	'limit',
							'selected'		=>	array($o['limit'])
						));
					?>
				</td>
			</tr>
		</table>
		<h3>Hiding Members</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="hide_email">Hide email addresses from anyone who is not logged in:</label></th>
				<td>
					<input type="radio" name="hide_email" value="1" <?php if($o['hide_email']) { echo 'checked'; } ?> /> yes
					<input type="radio" name="hide_email" value="0" <?php if(!$o['hide_email']) { echo 'checked'; } ?> /> no
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="hide">Automatically hide new members</label></th>
				<td>
					<input type="radio" name="hide" value="1" <?php if($o['hide']) { echo 'checked'; } ?> /> yes
					<input type="radio" name="hide" value="0" <?php if(!$o['hide']) { echo 'checked'; } ?> /> no
				</td>
			</tr>
		</table>
		<h3>Sorting Members</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="sort">Sort the members list originally by</label></th>
				<td>
					<?php
						$a = array();
						foreach($tern_wp_members_fields as $k => $v) {
							$a['Standard Fields'][] = array($k,$v);
						}
						foreach($tern_wp_meta_fields as $k => $v) {
							$a['Standard Meta Fields'][] = array($k,$v);
						}
						$r = $wpdb->get_col("select distinct meta_key from $wpdb->usermeta");
						foreach($r as $v) {
							if(in_array($v,$tern_wp_members_fields) or in_array($v,$tern_wp_meta_fields)) {
								continue;
							}
							$a['Available Meta Fields'][] = array($v,$v);
						}
						echo $ternSel->create(array(
							'type'			=>	'tiered',
							'data'			=>	$a,
							'key'			=>	0,
							'value'			=>	1,
							'id'			=>	'sort',
							'name'			=>	'sort',
							'selected'		=>	array($o['sort'])
						));
					?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="order">Sort members list originally in this order</label></th>
				<td>
					<input type="radio" name="order" value="asc" <?php if($o['order'] == 'asc') { echo 'checked'; } ?> /> Ascending
					<input type="radio" name="order" value="desc" <?php if($o['order'] == 'desc') { echo 'checked'; } ?> /> Descending
				</td>
			</tr>
		</table>
		<h3>Display Options</h3>
		<table class="form-table">
		<!--
			<tr valign="top">
				<th scope="row"><label for="meta">Meta fields to search by</label></th>
				<td>
					<textarea name="meta" style="width:100%;"><?php echo $o['meta']; ?></textarea><br />
					<span class="setting-description">e.g. occupation,employer,department,city,state,zip,country</span>
				</td>
			</tr>
		-->
			<tr valign="top">
				<th scope="row"><label for="allow_display">Allow users to choose which lists they wish to be a part of?</label></th>
				<td>
					<input type="radio" name="allow_display" value="1" <?php if($o['allow_display']) { echo 'checked'; } ?> /> yes
					<input type="radio" name="allow_display" value="0" <?php if(!$o['allow_display']) { echo 'checked'; } ?> /> no
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="gravatars">Use gravatars?</label></th>
				<td>
					<input type="radio" name="gravatars" value="1" <?php if($o['gravatars']) { echo 'checked'; } ?> /> yes
					<input type="radio" name="gravatars" value="0" <?php if(!$o['gravatars']) { echo 'checked'; } ?> /> no
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Changes" /></p>
		<input type="hidden" id="page" name="page" value="members-list/core/members-list.php" />
		<input type="hidden" name="action" value="update" />
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_members_nonce'); ?>" />
		<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
	</form>
</div>
<?php
}

/****************************************Terminate Script******************************************/
?>