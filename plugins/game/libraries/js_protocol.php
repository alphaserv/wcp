<?php
class Js_protocol_message
{
	public $name = '';
	public $arguments = array();
	public $arg_hash = '';
	
	public function gen_hash()
	{
		$tot = '';
		foreach($this->arguments as $argument)
			if(is_int($argument) or is_string($argument))
				$tot .= $argument;
		
		$this->arg_hash = md5($tot);
		return $this;
	}
}


class Js_protocol
{
	private $messages = array();
	function push_message($message)
	{
		$this->messages[] = $message;
	}
	
	function simple_push_message($name, $arguments)
	{
		$msg = new Js_protocol_message();
		$msg->name = $name;
		$msg->arguments = $arguments;
		$msg->gen_hash();
		$this->push_message($msg);
	}
	
	function send()
	{
		echo json_encode($this->messages);
	}

}
