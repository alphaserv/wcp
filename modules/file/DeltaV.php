<?php

require_once("./includes/DWDB.php");
require_once("./includes/DWDBProject.php");
require_once("./includes/Propfind.php");
require_once("./includes/Report.php");
require_once("./includes/DWProject.php");

Class DeltaV {
	private $_dbconnection;
	private $_args;
	
	function DeltaV($dbtype, $dbhost, $dbuser, $dbpass, $dbname)
	{
		$str = $_GET["args"];
		$arr = explode ("!", $str);
		for ( $i=0; $i < count($arr) ; $i++ ) {
			$elem = explode("=", $arr[$i]);
			$this->_args[$elem[0]] = $elem[1];
		}
		$this->_dbconnection = new DWDB($dbtype, $dbhost, $dbuser, $dbpass, $dbname);
	}
	
	function debug($reqbody)
	{
		$Handle = fopen("log.txt", 'a');
		$headers = getallheaders();
		$header = $_SERVER['REQUEST_METHOD'] . " " .$_SERVER['REQUEST_URI'] . " HTTP/1.1\r\n";
		foreach ($headers as $name => $content) {
			$header .= "$name: $content\r\n";
		} 
		fwrite($Handle, $header);
		fwrite($Handle, "\r\n");
		fwrite($Handle, $reqbody);
		fwrite($Handle, "\r\n\r\n\r\n\r\n");
		fclose($Handle); 
	}
	
	function execute()
	{
		$reqbody = @file_get_contents('php://input');
		if ($_SERVER["REQUEST_METHOD"] == "PROPFIND") {
			$body = $this->_propfind($reqbody);
		}
		if ($_SERVER["REQUEST_METHOD"] == "REPORT") {
			$body = $this->_report($reqbody);
		}
		if (empty($body)) {
			header("HTTP/1.1 501 Not Implemented");
		}
		$this->debug($reqbody);
		return ($body);
	}

	private function _propfind($reqbody)
	{
		$array = array (
						"" => array("Propfind", 'emptyType'),
						"default" => array("Propfind", 'defaultType'),
						"bln" => array("Propfind", 'blnType'),
						"bc" => array("Propfind", 'bcType'),
						);
		$reqinfo = Propfind::parseReq($reqbody);
		foreach ($array as $subkey => $subvalue) {
			if ($subkey == $this->_args["type"]) {
				return call_user_func($subvalue, $reqinfo);
			}
		}
	}
	
	private function _report($reqbody)
	{
		$array = array (
						"default" => array("Report", 'defaultType'),
						);

		$reqinfo = Report::parseReq($reqbody);

		foreach ($array as $subkey => $subvalue) {
			if ($subkey == $this->_args["type"]) {
				return call_user_func($subvalue, $reqinfo);
			}
		}
	}
}