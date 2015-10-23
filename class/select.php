<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			select.php
//		Description:
//			Compile HTML select elements from arrays.
//		Actions:
//			1) compile an HTML select element from an array
//		Date:
//			Added on March 23rd 2006 for ternstyle (tm) v1.0.0
//		Version:
//			5.0.1
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

if(!class_exists('select')) {
//
class select {

	var $a = array();
	var $o = '';
	var $s = '';

	function create($a=array()) {
		$this->a = array_merge(array(
			'type'			=>	'select',
			'data'			=>	array(),
			'key'			=>	'',
			'value'			=>	'',
			'start'			=>	0,
			'finish'		=>	100,
			'id'			=>	'',
			'name'			=>	'',
			'title'			=>	'',
			'class'			=>	'',
			'tabindex'		=>	'',
			'select_value'	=>	'',
			'selected'		=>	array(),
			'onchange'		=>	'',
			'multiple'		=>	false,
			'zeros'			=>	false
		),$a);
		
		$this->compile_options();
		$this->compile_select();
		$this->reset();
		
		return $this->s;
		
	}
	function compile_options() {
		call_user_func_array(array(&$this,$this->a['type']),array());
	}
	function select() {
		foreach((array)$this->a['data'] as $k => $v) {
			$s = in_array($v,$this->a['selected'],true) ? ' selected ' : '';
			$this->add_option($v,$v,$s);
		}
	}
	function paired() {
		foreach((array)$this->a['data'] as $k => $v) {
			$s = in_array($v,$this->a['selected'],true) ? ' selected ' : '';
			$k = empty($k) ? $v : $k;
			$this->add_option($k,$v,$s);
		}
	}
	function multi() {
		foreach((array)$this->a['data'] as $k => $v) {
			$s = in_array($v[$this->a['key']],$this->a['selected'],true) ? ' selected ' : '';
			$this->add_option($v[$this->a['key']],$v[$this->a['value']],$s);
		}
	}
	function assoc() {
		foreach((array)$this->a['data'] as $k => $v) {
			$s = in_array($v,$this->a['selected'],true) ? ' selected ' : '';
			$this->add_option($k,$v,$s);
		}
	}
	function tiered() {
		foreach((array)$this->a['data'] as $k => $v) {
			$this->o .= '<optgroup label="'.$k.'">';
			for($i=0;$i<count($v);$i++) {
				$s = in_array($v[$i][$this->a['value']],$this->a['selected'],true) ? ' selected ' : '';
				$this->add_option($v[$i][$this->a['key']],$v[$i][$this->a['value']],$s);
			}
			$o .= '</optgroup>';
		}
	}
	function numbers() {
		if($this->a['start'] < $this->a['finish']) {
			for($i=$this->a['start'];$i<=$this->a['finish'];$i++) {
				$s = in_array($i,$this->a['selected'],true) ? ' selected ' : '';
				if($this->a['zeros'] and $i < 10) {
					$i = '0'.$i;
				}
				$this->add_option($i,$i,$s);
			}
		}
		else {
			for($i=$this->a['finish'];$i<=$this->a['start'];$i++) {
				$b = $b-1;
				$s = in_array($i,$this->a['selected'],true) ? ' selected ' : '';
				if($this->a['zeros'] and $b < 10) {
					$b = '0'.$b;
				}
				$this->add_option($b,$b,$s);
			}
		}
	}
	function add_option($k,$v,$s) {
		$this->o .= '<option value="'.$v.'"'.$s.'>'.$k.'</option>';
	}
	function compile_select() {
		$this->s = '<select';
		$this->s .= empty($this->a['javascript']) ? '' : ' onChange="'.$this->a['javascript'].'"';
		$this->s .= $this->a['multiple'] ? ' multiple' : '';
		$this->s .= $this->a['tabindex'] !== '' ? ' tabindex="'.$this->a['tabindex'].'"' : '';
		$this->s .= ' name="'.$this->a['name'].'" id="'.$this->a['id'].'" class="'.$this->a['class'].'" title="'.$this->a['title'].'">';
		$this->s .= $this->a['select_value'] ? '<option value="">'.$this->a['select_value'].'</option>' : '';
		$this->s .= $this->o.'</select>';
	}
	function reset() {
		$this->a = array();
		$this->o = '';
	}

}
$ternSel = new select;
//
}

/****************************************Terminate Script******************************************/
?>