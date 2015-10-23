<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			google_maps.php
//		Description:
//			Functions for dealing with google maps
//		Actions:
//			1) Get coordinates for an address
//		Date:
//			Added on June 18th 2010
//		Version:
//			1.1
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

if(!class_exists('gMaps')) {
//
class gMaps {

	function gMaps() {
		$this->h = new WP_Http();
	}

	function geoLocate($a,$k=false) {
		$this->a = array_merge(array(
			'line1'		=>	'',
			'line2'		=>	'',
			'city'		=>	'',
			'state'		=>	'',
			'zip'		=>	'',
			'country'	=>	''
		),$a);
		
		$a = $this->format_address();
		if(!$k) {
			$x = new ternXML;
			$r = $this->h->get('http://maps.google.com/maps/api/geocode/xml?sensor=false&language=en&address='.$a);
			$r = $x->parse($r['body'],1,false);
			return $r['GeocodeResponse']['result']['geometry']['location'];
		}
		else {
			$x = new ternXML;
			$r = $this->h->get('http://maps.google.com/maps/geo?output=xml&key='.$k.'&q='.$a);
			$r = $x->parse($r['body'],1,false);
			$c = explode(',',$r['kml']['value']['Response']['Placemark']['value']['Point']['coordinates']);
			array_pop($c);
			return array('lat'=>$c[0],'lng'=>$c[1]);
		}
	}	
	function format_address() {
		$this->sanitize_address();
		return urlencode(implode(', ',array_filter($this->a,strlen)));
	}
	function sanitize_address() {
		foreach($this->a as $k => $v) {
			$this->a[$k] = preg_replace("/[^a-zA-Z0-9]+/",'+',trim($v));
		}
		$this->a = array_filter($this->a,strlen);
	}

}
$getMap = new gMaps;
//
}

/****************************************Terminate Script******************************************/
?>