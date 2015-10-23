<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			create.php
//		Description:
//			This file compiles and processes the plugin's configurable lists.
//		Actions:
//			1) compile plugin lists
//			2) process and save plugin lists
//		Date:
//			Added on April 29th 2011
//		Copyright:
//			Copyright (c) 2011 Matthew Praetzel.
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
if(!isset($_GET['page']) or $_GET['page'] !== 'members-list-create-list') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_members_list_create_actions');
add_action('init','WP_members_list_create_styles');
add_action('init','WP_members_list_create_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_create_styles() {
	wp_enqueue_style('thickbox');
	wp_enqueue_style('ml-admin',get_bloginfo('wpurl').'/wp-content/plugins/members-list/css/admin.css');
}
function WP_members_list_create_scripts() {
	wp_enqueue_script('thickbox');
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_create_actions() {
	global $getWP,$tern_wp_members_defaults,$current_user,$wpdb;
	get_currentuserinfo();
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	
	if(!wp_verify_nonce($_REQUEST['_wpnonce'],'tern_wp_members_nonce')) {
		return false;
	}
	
	switch($_REQUEST['action']) {
	
		case 'list' :
			
			$n = $_POST['name'];
			
			if(in_array($n,$o['lists']) and empty($_POST['item'])) {
				$getWP->addError('This list has already been created.');
				return;
			}
			$o['lists'][] = $n;

			$o = $getWP->getOption('tern_wp_members',$o,true);
			
			if($_POST['all']) {
				$a = $wpdb->get_results('select ID from '.$wpdb->users);
				
			}
			elseif($_POST['role']) {
				$a = WP_members_list_get_users_by_role(array($_POST['role']));
			}

			if($a) {
				foreach($a as $v) {
					$m = get_user_meta($v->ID,'_tern_wp_member_list');
					$m = is_array($m) ? $m : array($m);
					$t = false;
					foreach($m as $w) {
						if($w == $n) {
							$t = true;
							break;
						}
					}
					if(!$t) {
						add_user_meta($v->ID,'_tern_wp_member_list',$n,false);
					}
					
				}
			}
			
			break;
			
		case 'delete' :
			
			foreach($o['lists'] as $k => $v) {
				if(!in_array($k,$_REQUEST['lists'])) {
					$b[] = $v;
				}
				else {
					$wpdb->query("delete from $wpdb->usermeta where meta_key='_tern_wp_member_list' and meta_value='$v'");
				}
			}
			$o['lists'] = $b;
			$o = $getWP->getOption('tern_wp_members',$o,true);
			
			
			
			/*
			$a = $wpdb->get_results('select ID from '.$wpdb->users);
			foreach($a as $v) {
				foreach($c as $w) {
					delete_user_meta($v->ID,'_tern_wp_member_list',$w);
				}
			}
			*/
			
			break;
			
		default :
			break;
			
	}
	
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_create() {
	
	global $getWP,$tern_wp_msg,$tern_wp_members_defaults,$notice;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2>Members List - "Created Lists"<a href="#TB_inline?width=400&height=700&inlineId=members_list_add_item" id="add_item" class="thickbox button add-new-h2">Add New</a></h2>
		<?php if(!empty($notice)) { ?><div id="notice" class="error"><p><?php echo $notice ?></p></div><?php } ?>
		<?php
			if(!empty($tern_wp_msg)) {
				echo '<div id="message" class="updated fade"><p>'.$tern_wp_msg.'</p></div>';
			}
		?>
		<?php if(!empty($o['lists'])) { ?>
		<form id="ml_fm" method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=members-list-create-list">
		
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="action">
						<option value="" selected="selected">Bulk Actions</option>
						<option value="delete">Delete</option>
					</select>
					<input type="submit" value="Apply" name="doaction" id="doaction" class="button-secondary action" />
				</div>
				<br class="clear" />
			</div>
		
			<table id="lists" class="widefat fixed" cellspacing="0">
				<thead>
				<tr class="thead">
					<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
					<th scope="col" class="manage-column" style="width:15%;">Name</th>
					<th scope="col" class="manage-column">Short Code</th>
				</tr>
				</thead>
				<tfoot>
				<tr class="thead">
					<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
					<th scope="col" class="manage-column">Name</th>
					<th scope="col" class="manage-column">Short Code</th>
				</tr>
				</tr>
				</tfoot>
				<tbody id="list" class="list:cals cals-list">
					<?php foreach($o['lists'] as $k => $v) { $d = empty($d) ? ' class="alternate"' : ''; ?>
					<tr id="list-<?php echo $k; ?>"<?php echo $d; ?>>
						<th scope="row" class="check-column"><input type="checkbox" name="lists[]" id="list_<?php echo $k; ?>" value="<?php echo $k; ?>" /></th>
						<td>
							<strong><?php echo $v; ?></strong><br />
							<div class="row-actions">
								<span class='edit wpcal_edit'><a href="#TB_inline?width=400&height=700&inlineId=members_list_add_item" class="thickbox">Edit</a></span> | 
								<span class='edit'>
									<a href="admin.php?page=members-list-create-list&lists%5B%5D=<?php echo $k; ?>&action=delete&_wpnonce=<?php echo wp_create_nonce('tern_wp_members_nonce'); ?>">Delete</a> 
								</span>
							</div>
						</td>
						<td><?php echo '[members-list list="'.$v.'" search=true alpha=true pagination=true pagination2=true sort=true]'; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<input type="hidden" name="page" value="members-list-create-list" />
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_members_nonce'); ?>" />
		</form>
		<?php } ?>
	</div>
	<div id="members_list_add_item" class="add_item">
		<form id="members_list_add_item_form" method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=members-list-create-list">
			<fieldset>
				<h2>Add a new list:</h2>
				<label>Name:</label>
				<input type="text" name="name" class="regular-text" />
			</fieldset>
			<fieldset>
				<label>Add all users to this list:</label>
				<input type="checkbox" name="all" value="1" />
			</fieldset>
			<fieldset>
				<label>Add users of these roles to this list:</label>
				<select name="role" id="role">
					<option value="">select</option>
					<?php wp_dropdown_roles(); ?>
				</select>
			</fieldset>
			<input type="submit" value="Add List" class="btn button-secondary action" />
			<input type="hidden" name="item" />
			<input type="hidden" name="action" value="list" />
			<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_members_nonce'); ?>" />
		</form>
	</div>
<?php
	
}

/****************************************Terminate Script******************************************/
?>