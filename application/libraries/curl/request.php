<?php
namespace curl;

class request
{
	private $url;

	private $_curl_options;

	private $_request_method;	
	
	private $_post_vars;
	private $_get_Vars;
	
	private $_headers;
	
	private $supported_protocols;
	
	private $_cookies;
	
	function __construct($url = '', $options = array())
	{
		$this->url = $url;
		
		$this->timeout = 3;
		$this->_curl_options = array();
		$this->_cookies = array();
		$this->_headers = array();
		
		$this->_request_method = 'GET';
		$this->supported_protocols = array('http', 'https');
		
		foreach($options as $name => $value)
			$this->{'_'.$name} = $value;
	}
	
	function set_request_method($name)
	{
		$name = strtoupper($name);
		
		switch($name)
		{
			case 'PUT':
			case 'DELETE':		
			case 'GET':
			case 'POST':
				break;
			
			default:
				throw new exception('unkown http request method');
				break;			
		}
		$this->_request_method = $name;
	}
	
	function set_curl_options($options)
	{
		$this->_curl_options = $options;
	}
	
	function set_curl_option($id, $value)
	{
		$this->_curl_options[$id] = $value;
	}
	
	function has_curl_option($id)
	{
		return isset($this->_curl_options[$id]);
	}
	
	function default_curl_option($id, $default_value)
	{
		if(!$this->has_curl_option($id))
			$this->set_curl_option($id, $default_value);
	}
	
	function set_post_vars($vars)
	{
		$this->_post_vars = $vars;
	}
	
	function set_post_var($name, $value)
	{
		$this->_post_vars[$name] = $value;
	}
	
	function set_cookies($cookies)
	{
		$this->_cookies = $cookies;
	}
	
	function set_cookie($name, $cookie)
	{
		$this->_cookies[$name] = $cookie;
	}
	
	function set_headers($headers)
	{
		$this->_headers = $headers;
	}
	
	function set_header($name, $header)
	{
		$this->_headers[$name] = $header;
	}
	
	public function http_login($username = '', $password = '', $type = 'any')
	{
		$this->set_curl_option(CURLOPT_HTTPAUTH, constant('CURLAUTH_' . strtoupper($type)));
		$this->set_curl_option(CURLOPT_USERPWD, $username . ':' . $password);
		
		return $this;
	}
	
	public function set_proxy($url = '', $port = 80)
	{
		$this->set_curl_option(CURLOPT_HTTPPROXYTUNNEL, TRUE);
		$this->set_curl_option(CURLOPT_PROXY, $url . ':' . $port);
		
		return $this;
	}
	
	public function proxy_login($username = '', $password = '')
	{
		$this->set_curl_option(CURLOPT_PROXYUSERPWD, $username . ':' . $password);
	
		return $this;
	}
	
	public function ssl($verify_peer = true, $verify_host = 2, $path_to_cert = null)
	{
		if ($verify_peer)
		{
			$this->set_curl_option(CURLOPT_SSL_VERIFYPEER, true);
			$this->set_curl_option(CURLOPT_SSL_VERIFYHOST, $verify_host);
			
			if($path_to_cert !== null)
				$this->set_curl_option(CURLOPT_CAINFO, $path_to_cert);
		}
		else
		{
			$this->set_curl_option(CURLOPT_SSL_VERIFYPEER, false);
		}
		return $this;
	}
	
	function execute()
	{
		$protocol = $this->_guess_protocol($this->url);
		
		switch ($protocol)
		{
			case 'https':
				$this->ssl(); #does this work?
			case 'http':
				$this->_curl_method_update();
				
				if(!empty($this->_cookies))
				{
					$cookies = http_build_query($this->_cookies, NULL, '&');
					$this->set_curl_option(CURLOPT_COOKIE, $cookies);
				}
			
				switch($this->_request_method)
				{
					case 'PUT':
						$this->set_curl_option(CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT'));
					case 'POST':
						$this->set_curl_option(CURLOPT_POST, TRUE);
					case 'DELETE':
						if($this->_post_vars !== array())
						{
							$built_params = http_build_query($this->_post_vars, NULL, '&');

							$this->set_header('Content-Length', strlen($built_params));
							$this->set_curl_option(CURLOPT_POSTFIELDS, $built_params);
						}
					case 'GET':
						
					case '':
						break;
				}
				
								
				if(!empty($this->_headers))
				{
					$this->set_curl_option(CURLOPT_HTTPHEADER, $this->_parse_headers($this->_headers));
				}
				
				break;
			
			default:
				throw new \exception ('Protocol not supported');
				break;
		}
		
		$curl_instance = curl_init($this->url);

		#set default options
		$this->default_curl_option(CURLOPT_TIMEOUT, 30);
		$this->default_curl_option(CURLOPT_RETURNTRANSFER, true);
		$this->default_curl_option(CURLOPT_FAILONERROR, true);

		#only set follow location if not running in secure mode
		if (!ini_get('safe_mode') && !ini_get('open_basedir'))
			$this->default_curl_option(CURLOPT_FOLLOWLOCATION, true);

		#load options
		curl_setopt_array($curl_instance, $this->_curl_options);

		$response = curl_exec($curl_instance);
		$info = curl_getinfo($curl_instance);
		
		#request failed
		if ($response === FALSE)
		{
			$errno = curl_errno($curl_instance);
			$error = curl_error($curl_instance);
			
			curl_close($curl_instance);
			
			$this->_fail_info = $info;
			throw new \exception($error, $errno);
		}

		#request successful
		else
		{
			curl_close($curl_instance);
			return new response($response, $info);
		}
	}
	
	private function _guess_protocol($url)
	{
		return parse_url ($url , PHP_URL_SCHEME);
	}
		
	private function validate_protocol($protocol = null)
	{
		return in_array($protocol, $this->supported_protocols);
	}
	
	private function _curl_method_update()
	{
		$this->set_curl_option(CURLOPT_CUSTOMREQUEST, strtoupper($this->_request_method));
	}
	
	private function _parse_headers()
	{
		$headers = array();
		foreach($this->_headers as $name => $header)
			$headers[] = $this->_parse_header($name, $header);
		
		return $headers;
	}
	
	private function _parse_header($name, $header)
	{
		if(trim($header) == '')
			return $name;
		else
			return $name.': '.$header;
	}

}

