<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			search.php
//		Description:
//			This file compiles and processes the plugin's configure search page.
//		Actions:
//			1) compile plugin search fields form
//			2) process and save plugin search fields
//		Date:
//			Added on May 3rd 2011
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
if($_REQUEST['page'] !== 'members-list-configure-search') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_members_list_search_fields_actions');
add_action('wp_ajax_order','WP_members_list_search_fields_actions');
add_action('init','WP_members_list_search_fields_styles');
add_action('init','WP_members_list_search_fields_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_search_fields_styles() {
	wp_enqueue_style('thickbox');
	wp_enqueue_style('ml-admin',get_bloginfo('wpurl').'/wp-content/plugins/members-list/css/admin.css');
}
function WP_members_list_search_fields_scripts() {
	wp_enqueue_script('TableDnD',get_bloginfo('wpurl').'/wp-content/plugins/members-list/js/jquery.tablednd_0_5.js.php',array('jquery'),'0.5');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('members-list',get_bloginfo('wpurl').'/wp-content/plugins/members-list/js/admin.js');
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_search_fields_actions() {
	global $getWP,$tern_wp_members_defaults;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	
	if(!wp_verify_nonce($_REQUEST['_wpnonce'],'tern_wp_members_nonce')) {
		return false;
	}
	
	$_REQUEST['action'] = $_REQUEST['action2'] == 'delete' ? 'delete' : $_REQUEST['action'];
	switch($_REQUEST['action']) {
	
		case 'order' :
			$a = array();
			foreach($_POST['field_names'] as $k => $v) {
				$a[$v] = $_POST['field_values'][$k];
			}
			$o['searches'] = $a;
			if($getWP->getOption('tern_wp_members',$o,true)) {
				die('<div id="message" class="updated fade"><p>Your order has been successfully saved.</p></div>');
			}
			break;
	
		case 'field' :
			
			$n = $_POST['name'];
			
			if(in_array($_POST['field'],$o['searches'])) {
				$getWP->addError('This field has already been added.');
				return;
			}
			$o['searches'][$n] = $_POST['field'];

			$o = $getWP->getOption('tern_wp_members',$o,true);
			break;
			
		case 'delete' :

			$b = array();
			foreach($o['searches'] as $k => $v) {
				if(!in_array($v,$_REQUEST['searches'])) {
					$b[$k] = $v;
				}
			}
			$o['searches'] = $b;
			$o = $getWP->getOption('tern_wp_members',$o,true);
			
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
function WP_members_list_search_fields() {
	global $wpdb,$getWP,$ternSel,$tern_wp_members_defaults,$tern_wp_msg,$tern_wp_members_fields,$tern_wp_meta_fields,$notice;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2>Members List - "Fields by which to Search"<a href="#TB_inline?width=400&height=700&inlineId=members_list_add_item" id="add_item" class="thickbox button add-new-h2">Add New</a></h2>
		<?php if(!empty($notice)) { ?><div id="notice" class="error"><p><?php echo $notice ?></p></div><?php } ?>
		<div id="tern_wp_message">
		<?php
			if(!empty($tern_wp_msg)) {
				echo '<div id="message" class="updated fade"><p>'.$tern_wp_msg.'</p></div>';
			}
		?>
		</div>
		<?php if(!empty($o['searches'])) { ?>
		<form id="tern_wp_members_list_fm" method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=members-list-configure-search">
		
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="action2">
						<option value="" selected="selected">Bulk Actions</option>
						<option value="delete">Delete</option>
					</select>
					<input type="submit" value="Apply" name="doaction" id="doaction" class="button-secondary action" />
				</div>
				<br class="clear" />
			</div>
		
			<table id="members_list_fields" class="widefat fixed" cellspacing="0">
				<thead>
				<tr class="thead">
					<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
					<th scope="col" class="manage-column" style="width:15%;">Name</th>
					<th scope="col" class="manage-column" style="width:15%;">Field</th>
				</tr>
				</thead>
				<tfoot>
				<tr class="thead">
					<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
					<th scope="col" class="manage-column">Name</th>
					<th scope="col" class="manage-column">Field</th>
				</tr>
				</tfoot>
				<tbody id="fields" class="list:sort sort-list">
					<?php foreach($o['searches'] as $k => $v) { $d = empty($d) ? ' class="alternate"' : ''; ?>
					<tr id="list-<?php echo $v; ?>"<?php echo $d; ?>>
						<th scope="row" class="check-column">
							<input type="checkbox" name="searches[]" id="searches_<?php echo $v; ?>" value="<?php echo $v; ?>" />
							<input type="hidden" name="field_names[]" value="<?php echo $k; ?>" />
							<input type="hidden" name="field_values[]" value="<?php echo $v; ?>" />
						</th>
						<td>
							<strong><?php echo $k; ?></strong><br />
							<div class="row-actions">
								<span class='edit'>
									<a href="admin.php?page=members-list-configure-search&searches%5B%5D=<?php echo $v; ?>&action=delete&_wpnonce=<?php echo wp_create_nonce('tern_wp_members_nonce'); ?>">Delete</a> 
								</span>
							</div>
						</td>
						<td>
							<?php echo $v; ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<input type="hidden" name="action" value="order" />
			<input type="hidden" name="page" value="members-list-configure-search" />
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_members_nonce'); ?>" />
		</form>
		<?php } ?>
	</div>
	<div id="members_list_add_item" class="add_item">
		<form id="members_list_add_item_form" method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=members-list-configure-search">
			<fieldset>
				<h2>Add a new field by which to sort:</h2>
				<label for="name">Name:</label>
				<input type="text" name="name" />
				<label for="field">Field:</label>
				<?php
					foreach($tern_wp_members_fields as $k => $v) {
						foreach($o['searches'] as $w) {
							if($v == $w['name']) {
								continue 2;
							}
						}
						$a['Standard Fields'][] = array($k,$v);
					}
					foreach($tern_wp_meta_fields as $k => $v) {
						foreach($o['searches'] as $w) {
							if($v == $w['name']) {
								continue 2;
							}
						}
						$a['Standard Meta Fields'][] = array($k,$v);
					}
					$r = $wpdb->get_col("select distinct meta_key from $wpdb->usermeta");
					foreach($r as $v) {
						if(in_array($v,$tern_wp_members_fields) or in_array($v,$tern_wp_meta_fields)) {
							continue;
						}
						foreach($o['searches'] as $w) {
							if($v == $w['name']) {
								continue 2;
							}
						}
						$a['Available Meta Fields'][] = array($v,$v);
					}
					echo $ternSel->create(array(
						'type'			=>	'tiered',
						'data'			=>	$a,
						'key'			=>	0,
						'value'			=>	1,
						'id'			=>	'field',
						'name'			=>	'field',
						'select_value'	=>	'Add New Field'
					));
				?>
			</fieldset>
			<input type="submit" value="Add Field" class="btn button-secondary action" />
			<input type="hidden" name="item" />
			<input type="hidden" name="action" value="field" />
			<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_members_nonce'); ?>" />
		</form>
	</div>
<?php
}

/****************************************Terminate Script******************************************/
?>