<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
////	File:
////		xml.php
////	Actions:
////		1) generate xml
////		2) parse xml
////	Account:
////		Added on June 3rd 2010
////	Version:
////		2.0
////
////	Written by Matthew Praetzel. Copyright (c) 2010 Matthew Praetzel.
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

if(!class_exists('ternXML')) {
//
class ternXML {
	
	var $init = false;
	var $a = array(
		'root'		=>	'root',
		'data'		=>	array(),
		'default'	=>	'item',
		'cdata'		=>	array()
	);
	var $xml;
	var $parsed = array();
	var $open = array();
	var $index = 0;
	var $root = false;
	
	function compile($a) {
		$this->a = array_merge($this->a,$a);
		$this->init = true;
		
		$this->head();
		$this->body();
		
		return $this->xml;
	}
	function head() {
		$this->xml .= '<?xml version="1.0" encoding="utf-8"?>';
	}
	function body() {
		$this->generate($this->a['data']);
	}
	function generate($a) {
	
		if(is_array($a)) {
			foreach($a as $k => $this->item) {
				
				//set id
				$this->id = uniqid();
				
				//set attributes
				$this->set_attributes();
				//fix offset by array key 'value'
				if(is_array($this->item) and isset($this->item['value'])) {
					$this->item = $this->item['value'];
				}
				
				//add cdata
				if(((!is_array($this->a['cdata']) and $this->a['cdata']) or in_array($k,$this->a['cdata'])) and !is_array($this->item)) {
					$this->item = '<![CDATA['.$this->item.']]>';
				}
				
				//add to array
				$c = count($this->open);
				$this->open[$c] = array();
				$this->open[$c] = array(
					'id'		=>	$this->id,
					'name'		=>	$k,
					//'depth'		=>	$this->parent_is_a_list() ? $this->get_parent_value('depth') : count($this->open)-1,
					'index'		=>	0,
					'is_list'	=>	$this->is_a_list(),
					'count'		=>	count($this->item),
					'item'		=>	$this->item,
					'parent'	=>	$this->open[$c-1]
				);
				
				//add xml
				if($this->is_a_list()) {
					$this->generate($this->item);
				}
				elseif(is_array($this->item)) {
					$this->open_item();
					$this->generate($this->item);
					$this->close_item();
				}
				else {
					$this->open_item();
					$this->item();
					$this->close_item();
				}
				
			}
		}

	}
	function open_item() {
		$this->add_indent(1);
		$this->xml .= '<';
		$this->xml .= $this->parent_is_a_list() ? $this->get_parent_value('name') : $this->get_item_value('name');
		$this->last = $this->open[count($this->open)-1];
		$this->add_attributes();
		$this->xml .= '>';
		$this->increment_index();
	}
	function item() {
		$this->xml .= $this->item;
	}
	function close_item() {
	
		
		
		$this->add_indent(0);
		$this->xml .= '</';
		if($this->parent_is_a_list()) {
			$this->xml .= $this->get_parent_value('name');
		}
		else {
			$this->xml .= $this->get_item_value('name');
		}
		$this->xml .= '>';
		
		if($this->parent_is_a_list() and $this->get_parent_value('index') == $this->get_parent_value('count')) {
			array_pop($this->open);
		}
		//if(!$this->parent_is_a_list()) {
			array_pop($this->open);
		//}

	}
	function add_attributes() {
		if(is_array($this->attr)) {
			foreach((array)$this->attr as $k => $v) {
				$this->xml .= ' '.$k.'="'.$v.'"';
			}
		}
	}
	function set_attributes() {
		if(is_array($this->item['attributes'])) {
			$this->attr = $this->item['attributes'];
			unset($this->item['attributes']);
		}
		else {
			
			$this->attr = false;
		}
	}
	function index_in_parent() {
		if($this->get_parent_value('is_list')) {
			return $this->get_parent_value['index'];
		}
		return 0;
	}
	function increment_index() {
		if($this->get_parent_value('is_list')) {
			$this->open[count($this->open)-2]['index']++;
		}
	}
	function get_parent_value($v) {
		return $this->open[count($this->open)-2][$v];
	}
	function get_item_value($v) {
		return $this->open[count($this->open)-1][$v];
	}
	function get_item() {
		return $this->open[count($this->open)-1][$v];
	}
	function is_a_list() {
		if(is_array($this->item)) {
			$a = is_array($this->item['attributes']) ? $this->item['value'] : $this->item;
			if(count($a) == 0) {
				return false;
			}
			foreach($a as $k => $v) {
				if(!is_numeric($k)) {
					return false;
				}
			}
			return true;
		}
		return false;
	}
	function parent_is_a_list() {
		if($this->get_parent_value('is_list')) {
			return true;
		}
		return false;
	}
	function add_indent($b=0) {
		
		if(!$b and $this->get_parent_value('is_list') and $this->last['parent']['id'] == $this->get_item_value('id')) {
			$this->indent();
			return;
		}
		elseif(!$b and $this->last['id'] == $this->get_item_value('id')) {
			return;
		}
		$this->indent();
		
	}
	function indent() {
		$this->xml .= "\n";
		for($i=0;$i<count($this->open)-1;$i++) {
			if(!$this->open[$i]['is_list']) {
				$this->xml .= "\t";
			}
		}
	}


	function parse($x,$v=true) {

		$this->value = $v;
		$this->open = array();
		$this->parsed = array();
		$this->opened = array();
		
		$this->parser = xml_parser_create();
		xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($this->parser,XML_OPTION_TARGET_ENCODING,'utf-8');
		xml_parser_set_option($this->parser,XML_OPTION_SKIP_WHITE,1);
		xml_set_object($this->parser,$this);
		xml_set_element_handler($this->parser,'parse_open_item','parse_close_item');
		xml_set_character_data_handler($this->parser,'parse_item');
		xml_parse($this->parser,$x,true);
		xml_parser_free($this->parser);
		
		$this->clean_parsed($this->parsed);
		return $this->parsed;
	}
	
	function parse_open_item($p,$n,$a) {
		$this->name = $n;
		
		$this->attr = count($a) > 0 ? $a : false;
		
		$this->is_list = false;
		if($this->crawl_items('exists')) {
			$this->is_list = true;
			$this->crawl_items('fix');
			$this->crawl_items('add');
			$this->open[] =  array('name'=>$this->name,'is_list'=>true);
		}
		else {
			$this->crawl_items('add');
			$this->open[] =  array('name'=>$this->name,'is_list'=>false);
		}
	}
	function parse_item($p,$v) {
		$this->parsed_value = strval(ltrim(rtrim($v,"\t\r\n"),"\t\r\n"));
		if($this->parsed_value === '' or (empty($this->parsed_value) and $this->parsed_value !== 0 and $this->parsed_value !== '0') or preg_match("/^[\s]+$/",$this->parsed_value)) {
			return;
		}
		$this->crawl_items('value');
	}
	function parse_close_item($p,$n) {
		array_pop($this->open);
	}
	
	function crawl_items($n) {
		$this->count[$n] = 0;
		return $this->walk_items($this->parsed,$n);
	}
	function walk_items(&$item,$n) {
		$c = $this->count[$n];
		$name = $this->open[$c]['name'];
		
		if($this->open[$c]['is_list']) {
			$this->count[$n]++;
			return $this->walk_items($item[$name]['value'][count($item[$name]['value'])-1],$n);
		}
		
		//stop walking
		if(empty($this->open[$c])) {
			/*
			if($this->open[$c-1]['is_list']) {
				//return $this->perform_on_item($item['value'][count($item['value'])-1],$n);
			}
			*/
			return $this->perform_on_item($item,$n);
		}
		
		$this->count[$n]++;
		return $this->walk_items($item[$name],$n);
	}
	function perform_on_item(&$item,$n) {
		if($n == 'exists') {
			return $this->item_exists($item);
		}
		if($n == 'fix') {
			return $this->fix_item($item);
		}
		if($n == 'add') {
			return $this->add_item($item);
		}
		if($n == 'value') {
			return $this->add_value($item);
		}
	}
	
	function item_exists(&$item) {
		if(is_array($item) and array_key_exists($this->name,$item)) {
			return true;
		}
		return false;
	}
	function fix_item(&$item) {
		if($this->is_list and ((is_array($item[$this->name]) and !$item[$this->name]['attributes']['list']) or !is_array($item[$this->name]))) {
			$a = array();
			if(is_array($item[$this->name]) and $item[$this->name]['attributes']) {
				$v = $item[$this->name]['value'];
				$a = $item[$this->name]['attributes'];
			}
			else {
				$v = $item[$this->name];
				$a = array();
			}

			$item[$this->name] = array(
				'attributes'	=>	array_merge($a,array(
					'list'	=>	true
				)),
				'value'			=>	array($v)
			);
		}
		return true;
	}
	function add_item(&$item) {
		if($this->attr) {
			$v = array('attributes'=>$this->attr,'value'=>array());
		}
		else {
			$v = array();
		}

		if(is_array($item)) {
			if($this->is_list) {
				$item[$this->name]['value'][count($item[$this->name]['value'])] = $v;
			}
			else {
				$item[$this->name] = $v;
			}
		}
		else {
			$this->parsed[$this->name] = $v;
		}
		return true;
	}
	function add_value(&$item) {
		if($item['value']) {
			$item['value'] = $this->parsed_value;
		}
		else {
			$item = $this->parsed_value;
		}
	}
	function clean_parsed(&$a) {
		if(is_array($a)) {
			foreach($a as $k => $v) {
				if($v['attributes']) {
					$this->clean_attributes($a[$k]);
				}
				if(is_array($v) and count($v) < 1) {
					$this->clean_value($a[$k]);
				}
				$this->clean_parsed($a[$k]);
			}
		}
	}
	function clean_attributes(&$a) {
		if(is_array($a) and $a['attributes']) {
			foreach($a['attributes'] as $k => $v) {
				if($k == 'list') {
					unset($a['attributes'][$k]);
				}
			}
			if(count($a['attributes']) < 1) {
				$this->remove_attributes($a);
			}
		}
	}
	function remove_attributes(&$a) {
		unset($a['attributes']);
		$a = $a['value'];
	}
	function clean_value(&$a) {
		$a = '';
	}

}

}
	
/****************************************Terminate Script******************************************/
?>