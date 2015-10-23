<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			arrays.php
//		Description:
//			Perform various fixes to arrays.
//		Actions:
//			1) functions for dealing with arrays
//		Date:
//			Added on May 9th 2006 for ternstyle (tm) v1.0.0
//		Version:
//			2.0
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

if(!class_exists('arrayFixes')) {
//
class arrayFixes {

	function sortMulti($a,$c,$t,$o='asc',$p=false) {
		$r = array();
		for($i=0;$i<count($a);$i++) {
			if(empty($r)) {
				$r[] = $a[$i];
			}
			else {
				for($b=0;$b<count($r);$b++) {
					if($t == "str") {
						if(strcmp(strtolower($a[$i][$c]),strtolower($r[$b][$c])) < 0) {
							$n = array($a[$i]);
							array_splice($r,$b,0,$n);
							break;
						}
						elseif(strcmp(strtolower($a[$i][$c]),strtolower($r[$b][$c])) > 0 and $b == (count($r)-1)) {
							array_push($r,$a[$i]);
							break;
						}
					}
					elseif($t == "num") {
						if($a[$i][$c] < $r[$b][$c] or $a[$i][$c] == $r[$b][$c]) {
							$n = array($a[$i]);
							array_splice($r,$b,0,$n);
							break;
						}
						elseif($a[$i][$c] > $r[$b][$c] and $b == (count($r)-1)) {
							array_push($r,$a[$i]);
							break;
						}
					}
				}
			}
		}
		if($o == "desc") {
			$r = is_array($r) ? array_reverse($r) : array();
		}
		return $r;
	}
	function sortMultiClass($a,$c,$t,$o='asc',$p=false) {
		$r = array();
		for($i=0;$i<count($a);$i++) {
			if(empty($r)) {
				$r[] = $a[$i];
			}
			else {
				for($b=0;$b<count($r);$b++) {
					if($t == "str") {
						if(strcmp(strtolower($a[$i]->$c),strtolower($r[$b]->$c)) < 0) {
							$n = array($a[$i]);
							array_splice($r,$b,0,$n);
							break;
						}
						elseif(strcmp(strtolower($a[$i]->$c),strtolower($r[$b]->$c)) > 0 and $b == (count($r)-1)) {
							array_push($r,$a[$i]);
							break;
						}
					}
					elseif($t == "num") {
						if($a[$i]->$c < $r[$b]->$c or $a[$i]->$c == $r[$b]->$c) {
							$n = array($a[$i]);
							array_splice($r,$b,0,$n);
							break;
						}
						elseif($a[$i]->$c > $r[$b]->$c and $b == (count($r)-1)) {
							array_push($r,$a[$i]);
							break;
						}
					}
				}
			}
		}
		if($o == "desc") {
			$r = is_array($r) ? array_reverse($r) : array();
		}
		return $r;
	}
	function removeEmptyValues($a,$p=true) {
		$b = array();
		if(is_array($a)) {
			foreach($a as $k => $v) {
				if(!empty($v)) {
					if($p) {
						$b[$k] = $v;
					}
					else {
						$b[] = $v;
					}
				}
			}
		}
		return $b;
	}

}
$getFIX = new arrayFixes;
//
}

/****************************************Terminate Script******************************************/
?>