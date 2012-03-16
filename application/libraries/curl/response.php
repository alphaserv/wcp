<?php
namespace curl;

class response
{
	private $response;
	private $info;
	
	function __construct($response, $info)
	{
		$this->response = $response;
		$this->info = $info;
	}
	
	function __tostring()
	{
		return var_export($this, true);
	}
	
	function get_response()
	{
		return $this->response;
	}
}
