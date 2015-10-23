<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
////	File:
////		curl.php
////	Actions:
////		1) 
////	Account:
////		Added on July 17th 2010
////	Version:
////		0.1b
////
////	Written by Matthew Praetzel. Copyright (c) 2010 Matthew Praetzel.
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

if(!class_exists('tern_curl')) {

class tern_curl {

	var $options = array(
		
		//authentication
		//'HTTPAUTH'			=>	CURLAUTH_ANY,
		//'NETRC'				=>	false,
		//'UNRESTRICTED_AUTH'	=>	false,
		//'USERPWD'				=>	'',
	
		//buffer
		//'BUFFERSIZE'			=>	128,
		
		//cache
		//'DNS_USE_GLOBAL_CACHE'=>	true,
		//'FORBID_REUSE'		=>	false,
		//'FRESH_CONNECT'		=>	false,
		//'TIMECONDITION'		=>	CURL_TIMECOND_IFMODSINCE,
		//'TIMEVALUE'			=>	(int),
		
		//callbacks
		//'HEADERFUNCTION'		=>	(func),
		//'PASSWDFUNCTION'		=>	(func),
		//'PROGRESSFUNCTION'	=>	(func),
		//'READFUNCTION'		=>	(func),
		//'WRITEFUNCTION'		=>	(func),
		
		//connection
		'MAXCONNECTS'			=>	3,
		//'CLOSEPOLICY'			=>	CURLCLOSEPOLICY_OLDEST,
		//'INTERFACE'			=>	'',
		//'PROTOCOLS'			=>	CURLPROTO_ALL,
		
		//debugging
		//'NOPROGRESS'			=>	true,
		
		//FTP
		//'FTP_USE_EPRT'		=>	false,
		//'FTP_USE_EPSV'		=>	false,
		//'FTPAPPEND'			=>	false,
		//'FTPLISTONLY'			=>	false,
		//'FTPPORT'				=>	'',
		//'FTPSSLAUTH'			=>	CURLFTPAUTH_DEFAULT,
		//'INFILESIZE'			=>	128,
		//'KRB4LEVEL'			=>	NULL,
		//'POSTQUOTE'			=>	array(),
		//'QUOTE'				=>	array(),
		//'TRANSFERTEXT'		=>	true,
		
		//processing
		//'NOSIGNAL'			=>	true,
		
		//porting
		//'PORT'				=>	(int),
		
		//proxies
		//'HTTPPROXYTUNNEL'		=>	false,
		//'PROXY'				=>	'',
		//'PROXYAUTH'			=>	CURLAUTH_BASIC,
		//'PROXYPORT'			=>	(int),
		//'PROXYTYPE'			=>	CURLPROXY_HTTP,
		//'PROXYUSERPWD'		=>	'',
		
		//redirects
		//'AUTOREFERER'			=>	true,
		//'FOLLOWLOCATION'		=>	false,
		'MAXREDIRS'				=>	3,
		//'REDIR_PROTOCOLS'		=>	CURLPROTO_ALL,
		
		//return
		//'BINARYTRANSFER'		=>	true,
		//'CRLF'				=>	false,
		//'FAILONERROR'			=>	false,
		//'FILE'				=>	STDOUT,
		//'FILETIME'			=>	false,
		'HEADER'				=>	true,
		//'HEADER_OUT'			=>	false,
		//'MUTE'				=>	false,
		//'NOBODY'				=>	false,
		'RETURNTRANSFER'		=>	false,
		//'STDERR'				=>	(resource),
		//'VERBOSE'				=>	false,
		//'WRITEHEADER'			=>	(resource),
		
		//request
		//'CUSTOMREQUEST'		=>	'',
		//'ENCODING'			=>	'',
		//'HTTPGET'				=>	(bool),
		//'HTTPHEADER'			=>	array(),
		//'INFILE'				=>	(resource),
		//'POST'				=>	(bool),
		//'POSTFIELDS'			=>	'',
		//'PUT'					=>	(bool),
		//'RANGE'				=>	'',
		//'REFERER'				=>	'',
		//'UPLOAD'				=>	(bool),
		'USERAGENT'				=>	'Ternstyle/3.0',
		
		//sessions
		//'COOKIE'				=>	'',
		//'COOKIEFILE'			=>	'',
		//'COOKIEJAR'			=>	'',
		//'COOKIESESSION'		=>	false,
		
		//ssl
		//'CAINFO'				=>	'',
		//'CAPATH'				=>	'',
		//'EGDSOCKET'			=>	'',
		//'RANDOM_FILE'			=>	'',
		//'SSL_CIPHER_LIST'		=>	'',
		//'SSL_VERIFYHOST'		=>	true,
		//'SSL_VERIFYPEER'		=>	2,
		//'SSLCERT'				=>	'',
		//'SSLCERTPASSWD'		=>	'',
		//'SSLCERTTYPE'			=>	'',
		//'SSLENGINE'			=>	'',
		//'SSLENGINE_DEFAULT'	=>	'',
		//'SSLKEY'				=>	'',
		//'SSLKEYPASSWD'		=>	'',
		//'SSLKEYTYPE'			=>	'PEM',
		//'SSLVERSION'			=>	2,
		
		//timeout
		'CONNECTTIMEOUT'		=>	5,
		//'CONNECTTIMEOUT_MS'	=>	5000,
		//'DNS_CACHE_TIMEOUT'	=>	120,
		'TIMEOUT'				=>	5,
		//'TIMEOUT_MS'			=>	5000,
		
		//transfer
		//'LOW_SPEED_LIMIT'		=>	(int),
		//'LOW_SPEED_TIME'		=>	(int),
		//'RESUME_FROM'			=>	(int),
		
		//versioning
		'HTTP_VERSION'			=>	CURL_HTTP_VERSION_1_0
	);
	
	var $statii = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Reserved',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		510 => 'Not Extended'
	);
	
	function __contruct() {
		if(!function_exists('curl_init')) {
			return false;
		}
	}
	function get($a) {
		$a = array_merge($a,array('type'=>'GET'));
		return $this->request($a);
	}
	function post($a) {
		$a = array_merge($a,array('type'=>'POST'));
		return $this->request($a);
	}
	function request($a) {
		
		$a['options'] = $a['options'] ? array_merge($this->options,$a['options']) : $this->options;
		$this->a = array_merge(array(
			'url'			=>	'',
			'type'			=>	'GET',
			'data'			=>	array(),
			'headers'		=>	array(),
			'options'		=>	array()
		),$a);
		
		$this->c = curl_init();
		
		$this->set_headers();
		$this->set_cookies();
		$this->set_opts();
		
		$this->o = new tern_curl_response;
		$this->response = curl_exec($this->c);
		
		$this->status();
		$this->parse();
		
		return $this->o;
	}
	function set_headers() {
		$h = array();
		foreach((array)$this->a['headers'] as $k => $v) {
			$h[] = $k.': '.$v;
		}
		if(!empty($h)) {
			curl_setopt($this->c,CURLOPT_HEADERS,$h);
		}
	}
	function set_cookies() {
		if(!empty($this->a['cookies'])) {
			curl_setopt($this->c,CURLOPT_COOKIE,$this->compile_cookies());
		}
	}
	function set_opts() {
	
		curl_setopt($this->c,CURLOPT_URL,$this->a['url']);
		curl_setopt($this->c,CURLOPT_REFERER,$this->a['url']);
		
		foreach($this->a['options'] as $k => $v) {
			eval('curl_setopt($this->c,CURLOPT_'.$k.',$v);');
		}
		
		switch ($this->a['type']) {
		
			case 'GET' : 
				curl_setopt($this->c,CURLOPT_HTTPGET,true);
				break;
				
			case 'POST' :
				curl_setopt($this->c,CURLOPT_POST,true);
				if(!empty($this->a['data'])) {
					curl_setopt($this->c,CURLOPT_POSTFIELDS,$this->a['data']);
				}
				break;
				
			case 'PUT' :
				curl_setopt($this->c,CURLOPT_PUT,true);
				break;
				
			case 'HEAD' :
				curl_setopt($this->c,CURLOPT_CUSTOMREQUEST,'HEAD');
				break;
				
			default :
				curl_setopt($this->c,CURLOPT_CUSTOMREQUEST,$this->a['type']);
				break;
		
		}
		
	}
	function compile_cookies() {
		$s = '';
		foreach((array)$this->a['cookies'] as $k => $v) {
			$s .= empty($s) ? $k.'='.$v : '; '.$k.'='.$v;
		}
		return $s;
	}
	
	
	function status() {
		$c = curl_getinfo($this->c,CURLINFO_HTTP_CODE);
		$this->o->set_code($c);
		$this->o->set_status($this->statii[$c]);
	}
	function parse() {
		$this->hsize = curl_getinfo($this->c,CURLINFO_HEADER_SIZE);
		$this->parse_head();
		$this->parse_body();
	}
	function parse_head() {
		
		$h = explode("\n",preg_replace("/\n[ \t]/",' ',str_replace("\r\n","\n",trim(substr($this->response,0,$this->hsize)))));

		foreach($h as $v) {
			if(empty($v)) {
				continue;
			}
			if(strpos($v,':') !== false) {
				$v = explode(':',$v,2);
				if(strtolower($v[0]) == 'set-cookie') {
					$this->parse_cookie($v[1]);
				}
				else {
					$this->o->set_header($v[0],trim($v[1]));
				}
			}
		}
		
	}
	function parse_cookie($c) {
		$c = explode(';',$c);
		$b = 0;
		$a = array();
		foreach($c as $v) {
			$v = explode('=',$v);
			$w = trim(urldecode($v[1]));
			$m = trim($v[0]);
			if(!$b) {
				$n = $m;
				$a['value'] = $w;
			}
			else {
				$a[$m] = $w;
			}
			$b++;
		}
		$this->o->set_cookie($n,$a);
		/*
		if($this->a['set_cookies']) {
			$e = $a['expires'] ? $a['expires'] : 0;
			$p = $a['path'] ? $a['path'] : '/';
			$d = $a['domain'] ? $a['domain'] : '';
			$s = $a['secure'] ? true : false;
			setcookie($n,$a['value'],$e,$p,'staging.ternstyle.us',$s);
		}
		*/
	}
	function parse_body() {
		$this->o->set_body(substr($this->response,$this->hsize));
	}
	

}

class tern_curl_response {

	var $code		= 0;
	var $status		= '';
	var $headers	= array();
	var $cookies	= array();
	var $body		= '';
	
	function set_code($c) {
		$this->code = $c;
	}
	function set_status($s) {
		$this->status = $s;
	}
	function set_header($k,$v) {
		$this->headers[$k] = $v;
	}
	function set_body($b) {
		$this->body = $b;
	}
	function set_cookie($k, $v) {
		$this->cookies[$k] = $v;
	}
	
	function get_cookie($n) {
		return $this->cookies[$n]['value'];
	}
	function get_cookies() {
		$a = array();
		foreach($this->cookies as $k => $v) {
			$a[$k] = $v['value'];
		}
		return $a;
	}

}

}
	
/****************************************Terminate Script******************************************/
?>