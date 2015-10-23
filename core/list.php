<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			list.php
//		Description:
//			This file compiles and processes the plugin's list of members page.
//		Actions:
//			1) compile plugin list of members
//			2) process and save plugin list
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
if(!isset($_GET['page']) or $_GET['page'] !== 'members-list-edit-members-list') {
	return;
}
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('init','WP_members_list_list_actions');
add_action('init','WP_members_list_list_styles');
add_action('init','WP_members_list_list_scripts');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_list_styles() {
	
	
}
function WP_members_list_list_scripts() {
	
	
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_list_actions() {
	global $getWP,$tern_wp_members_defaults,$current_user;
	get_currentuserinfo();
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	
	if($_REQUEST['page'] == 'members-list-edit-members-list') {
		$a = empty($_REQUEST['action']) ? $_REQUEST['action2'] : $_REQUEST['action'];
		if(wp_verify_nonce($_REQUEST['_wpnonce'],'tern_wp_members_nonce') and !empty($a)) {
			$r = array();
			$o['hidden'] = is_array($o['hidden']) ? $o['hidden'] : array();
			foreach($_REQUEST['users'] as $v) {
				if($a == 'show' and in_array($v,$o['hidden'])) {
					array_splice($o['hidden'],array_search($v,$o['hidden']),1);
				}
				elseif($a == 'hide' and !in_array($v,$o['hidden'])) {
					$o['hidden'][] = $v;
				}
			}
			$o = $getWP->getOption('tern_wp_members',$o,true);
			$tern_wp_msg = empty($tern_wp_msg) ? 'You have successfully updated your settings.' : $tern_wp_msg;
		}
	}
	
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_list() {
global $wp_roles,$getWP,$tern_wp_msg,$tern_wp_members_defaults,$current_user,$notice;
	get_currentuserinfo();
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	$wps = new WP_User_Search($_GET['query'],$_GET['userspage'],$_GET['role']);
	
	$paging_text = paginate_links(array(
		'total' => ceil($wps->total_users_for_query/$wps->users_per_page),
		'current' => $wps->page,
		'base' => 'admin.php?page=members-list-edit-members-list&%_%',
		'format' => 'userspage=%#%',
		'add_args' => $args
	));
	if($paging_text) {
		$paging_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
			number_format_i18n( ( $wps->page - 1 ) * $wps->users_per_page + 1 ),
			number_format_i18n( min( $wps->page * $wps->users_per_page, $wps->total_users_for_query ) ),
			number_format_i18n( $wps->total_users_for_query ),
			$paging_text
		);
	}
?>
	<div class="wrap">
		<div id="icon-users" class="icon32"><br /></div>
		<h2>Members List</h2>
		<?php if(!empty($notice)) { ?><div id="notice" class="error"><p><?php echo $notice ?></p></div><?php } ?>
		<p>Here you are able to select which of your members you'd like to show or hide in your members list. By default all members are showm.</p>
		<?php
			if(!empty($tern_wp_msg)) {
				echo '<div id="message" class="updated fade"><p>'.$tern_wp_msg.'</p></div>';
			}
		?>
		<div class="filter">
			<form id="list-filter" action="" method="get">
				<ul class="subsubsub">
					<?php
						$l = array();
						$a = array();
						$u = get_users_of_blog();
						$t = count($u);
						foreach((array) $u as $c) {
							$d = unserialize($c->meta_value);
							foreach((array) $d as $e => $v) {
								if ( !isset($a[$e]) )
									$a[$e] = 0;
								$a[$e]++;
							}
						}
						unset($u);
						$current_role = false;
						$class = empty($role) ? ' class="current"' : '';
						$l[] = "<li><a href='admin.php?page=members-list-edit-members-list'$class>".sprintf(__ngettext('All<span class="count">(%s)</span>','All <span class="count">(%s)</span>',$t),number_format_i18n($t)).'</a>';
						foreach($wp_roles->get_names() as $s => $name) {
							if (!isset($a[$s]))
								continue;
							$class = '';
							if ($s == $role) {
								$current_role = $role;
								$class = ' class="current"';
							}
							$name = translate_with_context($name);
							$name = sprintf( _c('%1$s <span class="count">(%2$s)</span>|user role with count'),$name,$a[$s]);
							$l[] = "<li><a href='admin.php?page=members-list-edit-members-list&role=$s'$class>$name</a>";
						}
						echo implode( " |</li>\n", $l) . '</li>';
						unset($l);
					?>
				</ul>
			</form>
		</div>
		<form class="search-form" action="" method="get">
			<p class="search-box">
				<label class="hidden" for="user-search-input">Search Users:</label>
				<input type="text" class="search-input" id="user-search-input" name="query" value="" />
				<input type="hidden" id="page" name="page" value="<?php echo $_REQUEST['page']; ?>" />
				<input type="submit" value="Search Users" class="button" />
			</p>
		</form>
		<form id="posts-filter" action="" method="get">
			<div class="tablenav">
				<?php if($wps->results_are_paged()) { ?>
					<div class="tablenav-pages"><?php echo $paging_text; ?></div>
				<?php } ?>
				<div class="alignleft actions">
					<select name="action">
						<option value="" selected="selected">Bulk Actions</option>
						<option value="show">Show</option>
						<option value="hide">Hide</option>
					</select>
					<input type="submit" value="Apply" name="doaction" id="doaction" class="button-secondary action" />
				</div>
				<br class="clear" />
			</div>
			<table class="widefat fixed" cellspacing="0">
				<thead>
				<tr class="thead">
					<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
					<th scope="col" id="username" class="manage-column column-username" style="">Username</th>
					<th scope="col" id="name" class="manage-column column-name" style="">Name</th>
					<th scope="col" id="email" class="manage-column column-email" style="">E-mail</th>
					<th scope="col" id="role" class="manage-column column-role" style="">Role</th>
					<th scope="col" id="displayed" class="manage-column column-displayed" style="">Displayed</th>
				</tr>
				</thead>
				<tfoot>
				<tr class="thead">
					<th scope="col"  class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
					<th scope="col"  class="manage-column column-username" style="">Username</th>
					<th scope="col"  class="manage-column column-name" style="">Name</th>
					<th scope="col"  class="manage-column column-email" style="">E-mail</th>
					<th scope="col"  class="manage-column column-role" style="">Role</th>
					<th scope="col" id="displayed" class="manage-column column-displayed" style="">Displayed</th>
				</tr>
				</tfoot>
				<tbody id="users" class="list:user user-list">
<?php
	//
	$c = 0;
	//foreach($m as $u) {
	foreach($wps->get_results() as $u) {
		$u = new WP_User($u);
		$r = $u->roles;
		$r = array_shift($r);
		if(!empty($_REQUEST['role']) and $_REQUEST['role'] != $r) {
			continue;
		}
		$d = is_float($c/2) ? '' : ' class="alternate"';
		$nu = $current_user;
		$e = $u->ID == $nu->ID ? 'profile.php' : 'user-edit.php?user_id='.$u->ID.'&#038;wp_http_referer='.wp_get_referer();
?>
		<tr id='user-<?php echo $u->ID;?>'<?php echo $d;?>>
			<th scope='row' class='check-column'><input type='checkbox' name='users[]' id='user_<?php echo $u->ID;?>' class='administrator' value='<?php echo $u->ID;?>' /></th>
			<td class="username column-username">
				<?php echo get_avatar($u->ID,32);?>
				<strong>
					<a href="<?php echo $e;?>"><?php echo $u->user_nicename;?></a>
				</strong><br />
				<div class="row-actions">
					<span class='edit'><a href="admin.php?page=members-list-edit-members-list&users%5B%5D=<?php echo $u->ID;?>&action=show&_wpnonce=<?php echo wp_create_nonce('tern_wp_members_nonce');?>">Show</a> | </span>
					<span class='edit'><a href="admin.php?page=members-list-edit-members-list&users%5B%5D=<?php echo $u->ID;?>&action=hide&_wpnonce=<?php echo wp_create_nonce('tern_wp_members_nonce');?>">Hide</a></span>
				</div>
			</td>
			<td class="name column-name"><?php echo $u->first_name.' '.$u->last_name;?></td>
			<td class="email column-email"><a href='mailto:<?php echo $u->user_email;?>' title='e-mail: <?php echo $u->user_email;?>'><?php echo $u->user_email;?></a></td>
			<td class="role column-role"><?php echo $r;?></td>
			<td class="role column-displayed"><?php if(!empty($o['hidden']) and in_array($u->ID,$o['hidden'])) { echo 'no'; } else { echo 'yes'; } ?></td>
		</tr>
<?php
		$c++;
	}
?>
				</tbody>
			</table>
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="action2">
						<option value="" selected="selected">Bulk Actions</option>
						<option value="show">Show</option>
						<option value="hide">Hide</option>
					</select>
					<input type="hidden" id="page" name="page" value="members-list-edit-members-list" />
					<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('tern_wp_members_nonce');?>" />
					<input type="submit" value="Apply" name="doaction2" id="doaction2" class="button-secondary action" />
				</div>
				<br class="clear" />
			</div>
		</form>
	</div>
<?php
}

/****************************************Terminate Script******************************************/
?>