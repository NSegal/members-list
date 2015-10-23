<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			markup.php
//		Description:
//			This file compiles and processes the plugin's configure mark-up page.
//		Actions:
//			1) compile plugin mark-up form
//			2) process and save plugin mark-up
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
if((!isset($_REQUEST['page']) or $_REQUEST['page'] !== 'members-list-configure-mark-up') and $GLOBALS['pagenow'] != 'admin-ajax.php') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_members_list_markup_actions');
add_action('wp_ajax_markup','WP_members_list_markup_actions');
add_action('wp_ajax_getmarkup','WP_members_list_markup_actions');
add_action('init','WP_members_list_markup_styles');
add_action('init','WP_members_list_markup_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_markup_styles() {
	wp_enqueue_style('tern_wp_members_css',get_bloginfo('wpurl').'/wp-content/plugins/members-list/css/members-list.css');
}
function WP_members_list_markup_scripts() {
	wp_enqueue_script('TableDnD',get_bloginfo('wpurl').'/wp-content/plugins/members-list/js/jquery.tablednd_0_5.js.php',array('jquery'),'0.5');
	wp_enqueue_script('members-list',get_bloginfo('wpurl').'/wp-content/plugins/members-list/js/admin.js');
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_markup_actions() {
	global $getWP,$tern_wp_members_defaults,$current_user;
	get_currentuserinfo();
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);

	if($_REQUEST['page'] == 'members-list-configure-mark-up') {
		if(wp_verify_nonce($_REQUEST['_wpnonce'],'tern_wp_members_nonce')) {
			switch($_REQUEST['action']) {
				//update all fields
				case 'markup' :
					$o['fields'] = array();
					foreach($_REQUEST['field_titles'] as $k => $v) {
						$v = stripslashes($v);
						$o['fields'][$v] = array(
							'name'		=>	$_REQUEST['field_names'][$k],
							'markup'	=>	stripslashes($_REQUEST['field_markups'][$k])
						);
					}
					$o = $getWP->getOption('tern_wp_members',$o,true);
					echo '<div id="message" class="updated fade"><p>Your order has been successfully saved.</p></div>';
					die();
				//add a field
				case 'add' :
					$f = $_REQUEST['new_field'];
					$o['fields'] = is_array($o['fields']) ? $o['fields'] : array();
					$o['fields'][$f] = array(
						'name'		=>	$f,
						'markup'	=>	'<div class="tern_wp_members_'.$f.'">%value%</div>'
					);
					$o = $getWP->getOption('tern_wp_members',$o,true);
				//delete a field
				case 'remove' :
					$a = array();
					foreach($o['fields'] as $k => $v) {
						if($v['name'] != $_REQUEST['fields'][0]) {
							$a[$k] = $v;
						}
					}
					$o['fields'] = $a;
					$o = $getWP->getOption('tern_wp_members',$o,true);
			}
		}
		//attempted to update all fields without nonce
		elseif($_REQUEST['action'] == 'update' or $_REQUEST['action'] == 'add' or $_REQUEST['action'] == 'delete') {
			echo '<div id="message" class="updated fade"><p>There was an error whil processing your request. Please try again.</p></div>';
			die();
		}
		//get sample mark-up
		if($_REQUEST['action'] == 'getmarkup') {
			$m = new tern_members();
			echo htmlentities($m->markup($current_user));
			die();
		}
	}
	
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_markup() {
	global $wpdb,$getWP,$ternSel,$tern_wp_members_defaults,$tern_wp_msg,$tern_wp_members_fields,$tern_wp_meta_fields,$current_user,$notice;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	get_currentuserinfo();
?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2>Configure Your Members List Mark-Up</h2>
		<?php if(!empty($notice)) { ?><div id="notice" class="error"><p><?php echo $notice ?></p></div><?php } ?>
		<p>
			Below you can configure what fields are shown when viewing your members list. Add fields to be displayed and edit their names, 
			mark-up and order. When editing their mark-up, use the string %value% to place the respective value for each field and use the string 
			%author_url% to add the url (e.g. http://blog.ternstyle.us/?author=1) for each respective author's page.
		</p>
		<div id="tern_wp_message">
		<?php
			if(!empty($tern_wp_msg)) {
				echo '<div id="message" class="updated fade"><p>'.$tern_wp_msg.'</p></div>';
			}
		?>
		</div>
		<form class="field-form" action="" method="get">
			<p class="field-box">
				<label class="hidden" for="new-field-input">Add New Field:</label>
				<?php
					foreach($tern_wp_members_fields as $k => $v) {
						foreach($o['fields'] as $w) {
							if($v == $w['name']) {
								continue 2;
							}
						}
						$a['Standard Fields'][] = array($k,$v);
					}
					foreach($tern_wp_meta_fields as $k => $v) {
						foreach($o['fields'] as $w) {
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
						foreach($o['fields'] as $w) {
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
						'id'			=>	'new_field',
						'name'			=>	'new_field',
						'select_value'	=>	'Add New Field'
					));
				?>
				<input type="hidden" id="page" name="page" value="<?php echo $_REQUEST['page']; ?>" />
				<input type="submit" value="Add New Field" class="button" />
				<input type="hidden" name="action" value="add" />
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_members_nonce'); ?>" />
				<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
			</p>
		</form>
		<form id="tern_wp_members_list_fm" method="post" action="">
			<table id="members_list_fields" class="widefat fixed" cellspacing="0">
				<thead>
				<tr class="thead">
					<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
					<th scope="col" id="field" class="manage-column column-field" style="width:20%;">Database Field</th>
					<th scope="col" id="name" class="manage-column column-name" style="width:20%;">Field Name</th>
					<th scope="col" id="markup" class="manage-column column-markup" style="">Mark-Up</th>
				</tr>
				</thead>
				<tfoot>
				<tr class="thead">
					<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
					<th scope="col" id="field" class="manage-column column-field" style="">Database Field</th>
					<th scope="col" class="manage-column column-name" style="">Field Name</th>
					<th scope="col" class="manage-column column-markup" style="">Mark-Up</th>
				</tr>
				</tfoot>
				<tbody id="fields" class="list:fields field-list">
					<?php
						foreach($o['fields'] as $k => $v) {
							$d = empty($d) ? ' class="alternate"' : '';
					?>
							<tr id='field-<?php echo $v['name']; ?>'<?php echo $d; ?>>
								<th scope='row' class='check-column'><input type='checkbox' name='fields[]' id='field_<?php echo $v['name'];?>' value='<?php echo $v['name'];?>' /></th>
								<td class="field column-field">
									<input type="hidden" name="field_names%5B%5D" value="<?php echo $v['name'];?>" />
									<strong><?php echo $v['name'];?></strong><br />
									<div class="row-actions">
										<span class='edit tern_memebrs_edit'><a>Edit</a> | </span>
										<span class='edit'><a href="admin.php?page=members-list-configure-mark-up&fields%5B%5D=<?php echo $v['name'];?>&action=remove&_wpnonce=<?php echo wp_create_nonce('tern_wp_members_nonce');?>">Remove</a></span>
									</div>
								</td>
								<td class="name column-name">
									<input type="text" name="field_titles%5B%5D" class="tern_members_fields hidden" value="<?php echo $k;?>" /><br class="tern_members_fields hidden" />
									<input type="button" value="Update Field" class="tern_members_fields hidden button" />
									<span class="tern_members_fields field_titles"><?php echo $k;?></span>
								</td>
								<td class="markup column-markup">
									<textarea name="field_markups%5B%5D" class="tern_members_fields hidden" rows="4" cols="10"><?php echo $v['markup'];?></textarea><br class="tern_members_fields hidden" />
									<input type="button" value="Update Field" class="tern_members_fields hidden button" />
									<span class="tern_members_fields field_markups"><?php echo htmlentities($v['markup']); ?></span>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
			<input type="hidden" name="action" value="markup" />
			<input type="hidden" id="page" name="page" value="members-list-configure-mark-up" />
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_members_nonce');?>" />
			<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
		</form>
		<h3>Your Mark-Up will look like this:</h3>
		<?php
			$m = new tern_members();
			echo '<pre id="tern_members_sample_markup">'.htmlentities($m->markup($current_user)).'</pre>';
		?>
	</div>
<?php
}

/****************************************Terminate Script******************************************/
?>