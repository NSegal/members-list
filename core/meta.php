<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			meta.php
//		Description:
//			This file compiles and processes the plugin's user meta options.
//		Actions:
//			1) compile options
//			2) process and save options
//		Date:
//			Added on April 30th 2011
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
if($pagenow !== 'profile.php' and $pagenow !== 'user-edit.php') {
	return;
}

$states = array('Alabama'=>'AL','Alaska'=>'AK','Arizona'=>'AZ','Arkansas'=>'AR','California'=>'CA','Colorado'=>'CO','Connecticut'=>'CT','Delaware'=>'DE','Florida'=>'FL','Georgia'=>'GA','Hawaii'=>'HI','Idaho'=>'ID','Illinois'=>'IL','Indiana'=>'IN','Iowa'=>'IA','Kansas'=>'KS','Kentucky'=>'KY','Louisiana'=>'LA','Maine'=>'ME','Maryland'=>'MD','Massachusetts'=>'MA','Michigan'=>'MI','Minnesota'=>'MN','Mississippi'=>'MS','Missouri'=>'MO','Montana'=>'MT','Nebraska'=>'NE','Nevada'=>'NV','New Hampshire'=>'NH','New Jersey'=>'NJ','New Mexico'=>'NM','New York'=>'NY','North Carolina'=>'NC','North Dakota'=>'ND','Ohio'=>'OH','Oklahoma'=>'OK','Oregon'=>'OR','Pennsylvania'=>'PA','Rhode Island'=>'RI','South Carolina'=>'SC','South Dakota'=>'SD','Tennessee'=>'TN','Texas'=>'TX','Utah'=>'UT','Vermont'=>'VT','Virginia'=>'VA','Washington'=>'WA','West Virginia'=>'WV','Wisconsin'=>'WI','Wyoming'=>'WY','Alberta '=>'AB','British Columbia '=>'BC','Manitoba '=>'MB','New Brunswick '=>'NB','Newfoundland and Labrador '=>'NL','Northwest Territories '=>'NT','Nova Scotia '=>'NS','Nunavut '=>'NU','Ontario '=>'ON','Prince Edward Island '=>'PE','Quebec '=>'QC','Saskatchewan '=>'SK','Yukon '=>'YT');

$addy = array('line1','line2','city','state','zip');
//                                *******************************                                 //
//________________________________** ADD EVENTS                **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
add_action('profile_update','WP_members_list_meta_actions');
add_action('init','WP_members_list_meta_styles');
add_action('init','WP_members_list_meta_scripts');
add_action('edit_user_profile','WP_members_list_meta');
add_action('show_user_profile','WP_members_list_meta');
//                                *******************************                                 //
//________________________________** SCRIPTS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_meta_styles() {
	
}
function WP_members_list_meta_scripts() {
	
}
//                                *******************************                                 //
//________________________________** ACTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_meta_actions($i) {
	global $getWP,$tern_wp_members_defaults,$current_user,$wpdb,$profileuser,$current_user,$getMap;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	get_currentuserinfo();

	if(!current_user_can('edit_users') and (($o['allow_display'] and $current_user->ID != $i) or !$o['allow_display'])) {
		return;
	}

	global $getWP,$tern_wp_members_defaults,$current_user,$wpdb,$profileuser;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	
	delete_user_meta($i,'_tern_wp_member_list');
	foreach((array)$_REQUEST['lists'] as $v) {
		add_user_meta($i,'_tern_wp_member_list',$v);
	}
	
	$a = array('line1','line2','city','state','zip');
	foreach($a as $v) {
		delete_user_meta($i,'_'.$v);
		add_user_meta($i,'_'.$v,$_POST[$v]);
		$address[$v] = $_POST[$v];
	}
	//delete_user_meta($i,'_address');
	//add_user_meta($i,'_address',$address);
	
	
	$l = $getMap->geoLocate($address);
	delete_user_meta($i,'_lat');
	delete_user_meta($i,'_lng');
	add_user_meta($i,'_lat',$l['lat']);
	add_user_meta($i,'_lng',$l['lng']);
}
//                                *******************************                                 //
//________________________________** SETTINGS                  **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
function WP_members_list_meta($i) {

	global $getWP,$tern_wp_members_defaults,$profileuser,$current_user,$ternSel,$states,$addy;
	$o = $getWP->getOption('tern_wp_members',$tern_wp_members_defaults);
	get_currentuserinfo();

	if(!current_user_can('edit_users') and (($o['allow_display'] and $current_user->ID != $i->ID) or !$o['allow_display'])) {
		return;
	}
?>
	<h3>Members Lists</h3>
	<table class="form-table">
	<tr>
		<th><label for="lists">Select the lists you'd like this user displayed in:</label></th>
		<td>
			<ul>
			<?php foreach($o['lists'] as $v) { ?>
			<li><input type="checkbox" name="lists[]" value="<?php echo $v; ?>" <?php if(WP_members_list_is_in_list($i->ID,$v)) {?>checked<?php } ?> /> <?php echo $v; ?></li>
			<?php } ?>
			</ul>
		</td>
	</tr>
	</table>
	<h3>Address</h3>
	<?php
		foreach($addy as $v) {
			$address[$v] = get_user_meta($i->ID,'_'.$v,true);
		}
		//$address = get_user_meta($i->ID,'_address',true);
	?>
	<table class="form-table">
	<tr>
		<th><label for="line1">Address Line 1:</label></th>
		<td>
			<input type="text" name="line1" value="<?php echo $address['line1']; ?>" class="regular-text" />
		</td>
	</tr>
	<tr>
		<th><label for="line2">Address Line 2:</label></th>
		<td>
			<input type="text" name="line2" value="<?php echo $address['line2']; ?>" class="regular-text" />
		</td>
	</tr>
	<tr>
		<th><label for="city">City:</label></th>
		<td>
			<input type="text" name="city" value="<?php echo $address['city']; ?>" class="regular-text" />
		</td>
	</tr>
	<tr>
		<th><label for="state">State:</label></th>
		<td>
			<?php echo $ternSel->create(array(
				'type'			=>	'paired',
				'data'			=>	$states,
				'name'			=>	'state',
				'select_value'	=>	'select',
				'selected'		=>	array($address['state'])
			)); ?>
		</td>
	</tr>
	<tr>
		<th><label for="zip">Zipcode:</label></th>
		<td>
			<input type="text" name="zip" value="<?php echo $address['zip']; ?>" class="regular-text" />
		</td>
	</tr>
	<tr>
		<th><label for="lat">Latitude:</label></th>
		<td>
			<input type="text" name="lat" value="<?php echo get_user_meta($i->ID,'_lat',true); ?>" class="regular-text" />
		</td>
	</tr>
	<tr>
		<th><label for="lng">Longitude:</label></th>
		<td>
			<input type="text" name="lng" value="<?php echo get_user_meta($i->ID,'_lng',true); ?>" class="regular-text" />
		</td>
	</tr>
	</table>
<?php
	
}

/****************************************Terminate Script******************************************/
?>