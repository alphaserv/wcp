<?php

class File extends MX_Controller
{
	private $input = '';
	private $_args = array();
	
	function __construct()
	{
		$this->input = file_get_contents('php://input');
		
		if(isset($_GET['args']))
		{
			$str = $_GET['args'];
			$arr = explode ("!", $str);
			for ( $i=0; $i < count($arr) ; $i++ )
			{
				$elem = explode("=", $arr[$i]);
				$this->_args[$elem[0]] = $elem[1];
			}
		}
		
		$Handle = fopen(dirname(__FILE__)."/log.txt", 'a');
		$headers = getallheaders();
		$header = $_SERVER['REQUEST_METHOD'] . " " .$_SERVER['REQUEST_URI'] . " HTTP/1.1\r\n";
		foreach ($headers as $name => $content) {
			$header .= "$name: $content\r\n";
		} 
		fwrite($Handle, $header);
		fwrite($Handle, "\r\n");
		fwrite($Handle, $this->input);
		fwrite($Handle, "\nGET: ".print_r($_GET, true));
		fwrite($Handle, "\r\n\r\n\r\n\r\n");
		fclose($Handle); 
	}
	
	function raw()
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				die('ehm, this is ment for svn clients ;)');
				break;
			
			case 'PROPFIND':
				$this->_propfind();
				break;
			
			case 'REPORT':
				break;
			
			default:
				header("HTTP/1.1 501 Not Implemented");
				break;		
		}
	}
	
	function _propfind()
	{
		$this->input;
	}
}
