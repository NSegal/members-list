<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			members.php
//		Description:
//			This class controls the compilation of the members list and its pagination.
//		Actions:
//			1) compile members list
//			2) compile members list pagination
//			3) compile members list search options
//		Date:
//			Added January 29th, 2009
//		Version:
//			1.0
//		Copyright:
//			Copyright (c) 2009 Matthew Praetzel.
//		License:
//			This software is licensed under the terms of the GNU Lesser General Public License v3
//			as published by the Free Software Foundation. You should have received a copy of of
//			the GNU Lesser General Public License along with this software. In the event that you
//			have not, please visit: http://www.gnu.org/licenses/gpl-3.0.txt
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

//                                *******************************                                 //
//________________________________** MEMBERS LIST              **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
if(!class_exists('tern_members')) {
//
class tern_members {
	
	function tern_members() {
		global $getFIX,$getWP,$tern_wp_members_defaults,$post;
		$this->wp = $getWP;
		$o = $this->wp->getOption('tern_wp_members',$tern_wp_members_defaults);
		if(!empty($o)) {
			$this->num = $o['limit'];
			$f = explode(',',$o['meta']);
			$f = $getFIX->removeEmptyValues($f);
			$a = array();
			foreach($f as $k => $v) {
				$a[$v] = $v;
			}
			$this->meta_fields = $a;
		}
		$this->o = $o;
		$u = get_permalink();
		$this->url = strpos($u,'?') !== false ? $u : $u.'?';
	}
	function members($a,$e=true) {
		global $tern_wp_members_defaults;
		$this->scope();
		$this->list = $a['list'];
		$this->query();
		$o = $this->wp->getOption('tern_wp_members',$tern_wp_members_defaults);

		$r = '<div id="tern_members">';

		if($a['search'] !== false and $a['search'] !== 'false') {
			$r .= $this->search();
		}
		if($a['radius'] !== false and $a['radius'] !== 'false') {
			$r .= $this->radius();
		}
		if($a['alpha'] !== false and $a['alpha'] !== 'false') {
			$r .= $this->alpha();
		}
		$r .= $this->viewing($a);
		if($a['sort'] !== false and $a['sort'] !== 'false') {
			$r .= $this->sortby();
		}
		
		$r .= '<ul class="tern_wp_members_list">';
		foreach($this->r as $u) {
			//get user info
			$u = new WP_User($u);
			//compile name to be displayed
			$n = $u->first_name . ' ' . $u->last_name;
			$n = empty($u->first_name) ? $u->display_name : $n;
			if(!empty($n)) {
				$r .= $this->markup($u);
			}
		}
		$r .= '</ul>';
		if($a['pagination2'] !== false and $a['pagination2'] !== 'false') {
			$r .= $this->pagination();
		}
		$r .= '</div>';
		if($e) { echo $r; }
		return $r;
	}
	function scope() {
		$this->p = get_query_var('page');
		$this->p = empty($this->p) ? 1 : $this->p;
		$this->n = ceil($this->total/$this->num);
		$this->s = intval($this->p-1);
		if(empty($this->s)) {
			$this->s = 0;
		}
		elseif($this->n > 0 and $this->s >= $this->n) {
			$this->s = ($this->n-1);
		}
		$this->e = $this->total > (($this->s*$this->num)+$this->num) ? (($this->s*$this->num)+$this->num) : $this->total;
	}
	function geo_code() {
		global $getMap;
		if(!empty($_GET['byradius']) and !empty($_GET['radius'])) {
			$r = $getMap->geoLocate(array('zip'=>$_GET['byradius']));
			$lat = $this->lat = $r['lat'];
			$lng = $this->lng = $r['lng'];
		}
	}
	function select() {
		global $wpdb,$tern_wp_user_fields,$getMap;
		
		$q = "select distinct a.ID";
		$q .= " from $wpdb->users as a ".$this->q;
		$this->q = $q;
		$this->tq = "select COUNT(distinct a.ID) from $wpdb->users as a ".$this->tq;
	}
	function join() {
		global $wpdb,$tern_wp_user_fields;
		
		//sort
		if(!empty($this->sort) and !in_array($this->sort,$tern_wp_user_fields)) {
			$this->q .= " ,$wpdb->usermeta as b ";
		}
		
		//by
		if(!empty($this->by) and !in_array($this->by,$tern_wp_user_fields) and !in_array($this->sort,$tern_wp_user_fields) and $this->type != 'radius') {
			$this->q .= " ,$wpdb->usermeta as c ";
		}
		
		//alpha
		elseif($this->type == 'alpha' and !in_array($this->sort,$tern_wp_user_fields)) {
			$this->q .= " ,$wpdb->usermeta as c ";
		}
		
		//query
		elseif(!empty($this->query) and !in_array($this->by,$tern_wp_user_fields)) {
			$this->q .= " ,$wpdb->usermeta as c ";
		}
		
		//list
		if(!empty($this->list)) {
			$this->q .= " ,$wpdb->usermeta as d ";
		}
		
		//radius
		if(!empty($_GET['byradius']) and !empty($_GET['radius'])) {
			$this->q .= " ,$wpdb->usermeta as g ";
			$this->q .= " ,$wpdb->usermeta as h ";
		}
		
	}
	function where() {
		global $wpdb,$tern_wp_members_defaults,$tern_wp_user_fields,$tern_wp_meta_fields,$tern_wp_members_fields;
		$o = $this->wp->getOption('tern_wp_members',$tern_wp_members_defaults);
		
		//start where
		$this->q .= ' where 1=1 ';
		
		//sort
		if(!empty($this->sort) and !in_array($this->sort,$tern_wp_user_fields)) {
			$this->q .= " and b.user_id = a.ID ";
		}
		
		//by
		if(!empty($this->by) and !in_array($this->by,$tern_wp_user_fields) and !in_array($this->sort,$tern_wp_user_fields)) {
			$this->q .= " and c.user_id = a.ID ";
		}
		
		//alpha
		elseif($this->type == 'alpha' and !in_array($this->sort,$tern_wp_user_fields)) {
			$this->q .= " and c.user_id = a.ID ";
		}
		
		//query
		elseif(!empty($this->query) and $this->query != 'search...' and !in_array($this->by,$tern_wp_user_fields)) {
			$this->q .= " and c.user_id = a.ID ";
		}
		
		//list
		if(!empty($this->list)) {
			$this->q .= " and d.user_id = a.ID ";
		}
		
		//radius
		if(!empty($_GET['byradius']) and !empty($_GET['radius'])) {
			$this->q .= " and g.user_id = a.ID ";
			$this->q .= " and h.user_id = a.ID ";
			$this->q .= " and g.meta_key='_lat' and h.meta_key='_lng' ";
		}
		
		//hide members
		$this->q .= !empty($o['hidden']) ? " and a.ID NOT IN (".implode(',',$o['hidden']).")" : '';
		
		//sort
		if(!empty($this->sort) and !in_array($this->sort,$tern_wp_user_fields)) {
			$this->q .= " and b.meta_key = '$this->sort' ";
		}
		
		//by
		if(!empty($this->by) and in_array($this->by,$tern_wp_user_fields)) {
			$this->q .= " and instr(a.$this->by,'$this->query') != 0 ";
		}
		elseif(!empty($this->by)) {
			$this->q .= " and c.meta_key = '$this->by' and instr(c.meta_value,'$this->query') != 0 ";
		}
		elseif(!empty($this->query) and $this->query != 'search...' and $this->type != 'alpha') {
			foreach($this->o['searches'] as $v) {
				if(!in_array($v,$tern_wp_user_fields)) {
					$w .= empty($w) ? " c.meta_key = '$v'" : " or c.meta_key = '$v'";
				}
				else {
					$x .= empty($x) ? "a.$v" : ",a.$v";
				}
			}
			$this->q .= empty($x) ? ' and ' : 'and (';
			$this->q .= "(($w) and instr(c.meta_value,'$this->query') != 0) ";
			$this->q .= empty($x) ? '' : " or instr(concat_ws(' ',$x),'$this->query') != 0) ";
		}
		
		//alpha
		if($this->type == 'alpha') {
			$this->q .= " and c.meta_key = 'last_name' and SUBSTRING(LOWER(c.meta_value),1,1) = '$this->query' ";
		}
		
		//list
		if(!empty($this->list)) {
			$this->q .= " and d.meta_key='_tern_wp_member_list' and d.meta_value='$this->list' ";
		}
				
		$this->tq .= $this->q;
		
	}
	function order() {
		global $tern_wp_user_fields;
		if(!empty($_GET['byradius']) and !empty($_GET['radius'])) {
			$d = 1.609344*$_GET['radius'];
			$r = " and 6371 * 2 * ASIN( SQRT( POWER( SIN( RADIANS( $this->lat - g.meta_value ) / 2 ), 2 ) + COS( RADIANS( $this->lat ) ) * COS( RADIANS( g.meta_value ) ) * POWER( SIN( RADIANS( $this->lng - h.meta_value ) / 2 ), 2 ) ) ) < $d";
			$this->q .= $r;
			$this->tq .= $r;
		}
		if(!empty($this->sort) and in_array($this->sort,$tern_wp_user_fields)) {
			$this->q .= " order by $this->sort $this->order";
		}
		elseif(!empty($this->sort)) {
			$this->q .= " order by b.meta_value $this->order";
		}
	}
	function limit() {
		$this->q .= " limit $this->start,$this->end ";
	}
	function query($g=false) {
		global $wpdb,$tern_wp_user_fields,$tern_wp_members_fields,$tern_wp_meta_fields;

		foreach($_GET as $k => $v) {
			$this->$k = $$k = $this->sanitize($v);
		}
		$this->sort = $sort = $_GET['sort'] ? $_GET['sort'] : $this->o['sort'];
		$this->order = $order = $_GET['order'] ? $_GET['order'] : $this->o['order'];
		$this->start = $s = strval($this->s*$this->num);
		$this->end = $e = strval($this->num);
		
		$this->geo_code();
		$this->join();
		$this->where();
		$this->order();
		$this->limit();
		$this->select();
		
		$this->r = $wpdb->get_col($this->q);
		$this->total = intval($wpdb->get_var($this->tq));
		return $this->r;
	}
	function pagination($z=false) {
		global $tern_wp_members_defaults;
		$o = $this->wp->getOption('tern_wp_members',$tern_wp_members_defaults);
		$q = $_GET['query'];
		$b = $_GET['by'];
		$t = $_GET['type'];
		$this->scope();
		if($this->n > 1) {
			$s = $this->p-2;
			$e = ($s+4)>$this->n ? $this->n : $s+4;
			if($s <= 0) {
				$s = 1;
				$e = ($s+4)>$this->n ? $this->n : $s+4;
			}
			elseif(($this->p+2) > $this->n) {
				$e = $this->n;
				$s = ($e-4)<=0 ? 1 : $e-4;
			}
			$sort = empty($_GET['sort']) ? $o['sort'] : $_GET['sort'];
			$order = empty($_GET['order']) ? $o['order'] : $_GET['order'];
			for($i=$s;$i<=$e;$i++) {
				$h = $this->get_query($i);
				$c = intval($this->s+1) == $i ? ' class="tern_members_pagination_current tern_pagination_current"' : '';
				$r .= '<li'.$c.'><a href="' . $h . '">' . $i . '</a></li>';
			}
			if($this->s > 0) {
				$r = '<li><a href="'.$this->get_query(intval($this->s)).'">Previous</a></li>'.$r;
			}
			if($this->total > (($this->s*$this->num)+$this->num)) {
				$r .= '<li><a href="'.$this->get_query(intval($this->s+2)).'">Next</a></li>';
				$r .= '<li><a href="'.$this->get_query($this->n).'">Last</a></li>';
			}
			$r = $this->s > 0 ? '<li><a href="'.$this->get_query(1).'">First</a></li>'.$r : $r;
			$r = '<ul class="tern_pagination">' . $r . '</ul>';
		}
		if($z) { echo $r; }
		return $r;
	}
	function get_query($p) {
		return $this->url.'page='.$p.'&query='.$_GET['query'].'&by='.$_GET['by'].'&type='.$_GET['type'].'&sort='.$this->sort.'&order='.$this->order.'&byradius='.$_GET['byradius'].'&radius='.$_GET['radius'];
	}
	function search($e=false) {
		global $ternSel,$tern_wp_members_fields,$tern_wp_meta_fields;
		
		$a = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

		$v = empty($_REQUEST['query']) ? 'search...' : $_REQUEST['query'];

		$r = '<div class="tern_members_search">
		<form method="get" action="'.$this->url.'">
			<h2>Search Our '.ucwords($this->o['noun']).':</h2>
			<input type="text" id="query" name="query" class="blur" value="'.$v.'" />
			by '.$ternSel->create(array(
						'type'			=>	'paired',
						'data'			=>	$this->o['searches'],
						'id'			=>	'by',
						'name'			=>	'by',
						'select_value'	=>	'All Fields',
						'selected'		=>	array($_REQUEST['by'])
					)).'<input type="hidden" name="p" value="'.$_REQUEST['p'].'" />
			<input type="hidden" name="page_id" value="'.$_REQUEST['page_id'].'" />
			<input type="submit" value="Submit" />
		</form></div>';
		if($e) { echo $r; }
		return $r;
	}
	function radius($e=false) {
		global $ternSel,$tern_wp_members_fields,$tern_wp_meta_fields;
		$v = empty($_REQUEST['byradius']) ? 'search...' : $_REQUEST['byradius'];
		$r = '<div class="tern_members_search"><form method="get" action="'.$this->url.'">
			<label>and search by zipcode:</label>
			<input type="text" id="byradius" name="byradius" class="blur" value="'.$v.'" />
			'.$ternSel->create(array(
						'type'			=>	'select',
						'data'			=>	array(5,10,25,50,100,250,500),
						'name'			=>	'radius',
						'select_value'	=>	'Radius',
						'selected'		=>	array((int)$_REQUEST['radius'])
					)).'<input type="hidden" name="p" value="'.$_REQUEST['p'].'" />
			<input type="submit" value="Submit" />
		</form></div>';		
		if($e) { echo $r; }
		return $r;
	}
	function alpha($e=false) {
		$a = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$r = '<div class="tern_members_alpha">Search alphabetically <span>(by last name)</span>:<br /><ul>';
		foreach($a as $v) {
			unset($c);
			if($v == $_GET['query']) {
				$c = 'class="tern_members_selected"';
			}
			$r .= '<li><a '.$c.' href="'.$this->url.'&page=1&query='.$v.'&type=alpha&sort=last_name">'.strtoupper($v).'</a></li>';
		}
		$r .= '</ul></div>';
		if($e) { echo $r; }
		return $r;
	}
	function sortby($e=false) {
		
		foreach((array)$this->o['sorts'] as $k => $v) {
			unset($c,$o);
			
			if($this->sort == $v) {
				if($this->order == 'asc') {
					$c =  ' class="tern_members_sorted_u" ';
					$o = 'desc';
				}
				else {
					$c = ' class="tern_members_sorted_d" ';
					$o = 'asc';
				}
			}
			
			$r .= '<li'.$c.'><a href="'.$this->url.'&query='.urldecode($_GET['query']).'&by='.$_GET['by'].'&type='.$_GET['type'].'&sort='.$v.'&order='.$o.'&byradius='.$_GET['byradius'].'&radius='.$_GET['radius'].'">'.$k.'</a></li>';
		}
		$r = '<div class="tern_members_sort"><label>Sort by:</label><ul>'.$r.'</ul></div>';
		if($e) { echo $r; }
		return $r;
	}
	function viewing($a,$e=false) {
		global $tern_wp_members_defaults;
		$o = $this->wp->getOption('tern_wp_members',$tern_wp_members_defaults);
		$this->scope();
		$v = $this->total > 0 ? (($this->s*$this->num)+1) : '0';
		$m = '.';
		if($t == 'alpha') {
			$m = ' whose last names begin with the letter "'.strtoupper($q).'".';
		}
		$r = '<div class="tern_members_view">Now viewing <b>' . $v . '</b> through <b>' . $this->e . '</b> of <b>'.$this->total.'</b> '.$o['noun'].' found'.$m;
		if($a['pagination'] !== false and $a['pagination'] !== 'false') {
			$r .= $this->pagination();
		}
		$r .= '</div>';
		if($e) { echo $r; }
		return $r;
	}
	function markup($u) {
		global $tern_wp_members_defaults,$getMap;
		$o = $this->wp->getOption('tern_wp_members',$tern_wp_members_defaults);
		$s = '<li>'."\n    ";
		if($o['gravatars']) {
			$s .= '<a class="tern_wp_member_gravatar" href="'.get_author_posts_url($u->ID).'">'."\n        ".get_avatar($u->ID,60)."\n    ".'</a>'."\n    ";
		}
		$s .= '<div class="tern_wp_member_info">';
		foreach($o['fields'] as $k => $v) {
			if($v['name'] == 'user_email' and $o['hide_email'] and !is_user_logged_in()) {
				continue;
			}
			elseif($v['name'] == 'user_email') {
				$s .= "\n        <a href='mailto:".$u->$v['name']."'>".str_replace('%value%',$u->$v['name'],$v['markup']).'</a>';
				continue;
			}
			//if($v['name'] == 'distance' and !empty($_GET['byradius'])) {
			//	$r = $getMap->geoLocate(array('zip'=>$_GET['byradius']));
			//	$lat = $r['lat'];
			//	$lng = $r['lng'];
			//	$distance = (6371 * 2 * asin( sqrt( pow( sin( deg2rad( $lat - $u->_lat ) / 2 ), 2 ) + cos( deg2rad( $lat ) ) * cos( deg2rad( $u->_lat ) ) * pow( sin( deg2rad( $lng - $u->_lng ) / 2 ), 2 ) ) ))/1.609344;
			//	$s .= "\n        ".str_replace('%author_url%',get_author_posts_url($u->ID),str_replace('%value%',round($distance).' miles',$v['markup']));
			//}
			if(!empty($u->$v['name'])) {
				$s .= "\n        ".str_replace('%author_url%',get_the_author_meta('profilelink',$u->ID),str_replace('%value%',$u->$v['name'],$v['markup']));
			}
		}
		return $s."\n    ".'</div>'."\n".'</li>';
	}
	function sanitize($s) {
		// to be used in future updates
		return mysql_escape_string($s);
	}
	
}
//
}
	
/****************************************Terminate Script******************************************/
?>
