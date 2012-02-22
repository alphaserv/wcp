<?php

class Curl_client
{
	private $useragent;
	
	function __construct()
	{
		include_once dirname(__file__).'/curl/request.php';
		include_once dirname(__file__).'/curl/response.php';
		
		$this->useragent = 'AsWcp Curl php/'.phpversion();
	}
	
	function set_useragent($name)
	{
		$this->useragent = (string)$name;
	}
	
	public function __call($name, $args)
	{
		#not working
		if($name == 'is_supported' && !$this->is_supported())
			throw new exception('curl is not available');
		elseif(method_exists($this, $name))
        	call_user_func_array(array($this, $name), $args);
        
	}
	
	function is_supported()
	{
		return function_exists('curl_init');
	}

	function new_request($url, $opts = array())
	{
		$request = new curl\request($url, $opts);
		$request->set_curl_option(CURLOPT_USERAGENT, $this->useragent);
		return $request;
	}
	
	function info()
	{
		return curl_version();
	}
}
print_r(php_egg_logo_guid());die;
$client = new Curl_client;
$request = $client->new_request('https://googlemail.com');
$request->execute();
