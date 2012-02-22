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
}
